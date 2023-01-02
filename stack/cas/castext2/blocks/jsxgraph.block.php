<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');

class stack_cas_castext2_jsxgraph extends stack_cas_castext2_block {

    private static $countgraphs = 1;

    public function compile($format, $options):  ? MP_Node {
        $r = new MP_List([new MP_String('jsxgraph')]);

        // We need to transfer the parameters forward.
        $r->items[] = new MP_String(json_encode($this->params));

        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $options);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        return $r;
    }

    public function is_flat() : bool {
        // Even when the content were flat we need to evaluate this during postprocessing.
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor): string {
        global $PAGE;

        if (count($params) < 3) {
            // Nothing at all.
            return '';
        }

        $parameters = json_decode($params[1], true);
        $content    = '';
        for ($i = 2; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $content .= $processor->process($params[$i][0], $params[$i]);
            } else {
                $content .= $params[$i];
            }
        }

        $divid  = 'stack-jsxgraph-' . self::$countgraphs;
        $width  = '500px';
        $height = '400px';
        $aspectratio = false;
        if (array_key_exists('width', $parameters)) {
            $width = $parameters['width'];
        }
        if (array_key_exists('height', $parameters)) {
            $height = $parameters['height'];
        }

        $style = "width:$width;height:$height;";

        if (array_key_exists('aspect-ratio', $parameters)) {
            $aspectratio = $parameters['aspect-ratio'];
            // Unset the undefined dimension, if both are defined then we have a problem.
            if (array_key_exists('height', $parameters)) {
                $style = "height:$height;aspect-ratio:$aspectratio;";
            } else if (array_key_exists('width', $parameters)) {
                $style = "width:$width;aspect-ratio:$aspectratio;";
            }
        }

        $code = $content;

        // Input ref prefixes.
        // We could simply expose the prefix Moodle uses and let the author work
        // with that but suppose there exists another VLE which does not use
        // the same prefix for all inputs in the question, by keepping the id
        // mapping here the materials need not care about that and only this
        // needs to be fixed to match the environment.
        // And in any case in Moodle at the time we render this we do not know.
        // The prefix.
        // NOTE! We should validate that we never have input-refs-outside
        // question-text, but for now lets use the old seek code to work with
        // them out if that happens. We now can directly access the identtifier.
        foreach ($parameters as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $inputname  = substr($key, 10);
                if (property_exists($processor, 'qa') && $processor->qa !== null) {
                    // For contents useing the new renderer we have access to
                    // the identifier at this phase.
                    $namecode = "var $value='" . $processor->qa->get_qt_field_name($inputname) . "';\n";
                    $code = "$namecode\n$code";
                } else {
                    $seekcode = "var $value=stack_jxg.find_input_id(divid,'$inputname');";
                    $code = "$seekcode\n$code";
                }
            }
        }

        // Prefix the code with the id of the div.
        $code = "var divid = '$divid';\nvar BOARDID = divid;\n$code";

        // We restrict the actions of the block code a bit by stopping it from
        // rewriting some things in the surrounding scopes.
        // Also catch errors inside the code and try to provide console logging
        // of them for the author.
        // We could calculate the actual offset but I'll leave that for
        // someone else. 1+2*n probably, or we could just write all the preamble
        // on the same line and make the offset always be the same?
        $code = '"use strict";try{if(document.getElementById("' . $divid .
            '")){' . $code . '}} '
            . 'catch(err) {console.log("STACK JSXGraph error in \"' . $divid
            . '\", (note a slight varying offset in the error position due to possible input references):");'
            . 'console.log(err);}';

        $attributes = ['class' => 'jxgbox', 'style' => $style, 'id' => $divid];

        $PAGE->requires->js_amd_inline(
            'require(["qtype_stack/jsxgraph","qtype_stack/jsxgraphcore-lazy","core/yui"], '
            . 'function(stack_jxg, JXG, Y){Y.use("mathjax",function(){' . $code
            . '});});');

        self::$countgraphs = self::$countgraphs + 1;

        return html_writer::tag('div', '', $attributes);
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid  = true;
        $width  = '500px';
        $height = '400px';
        if (array_key_exists('width', $this->params)) {
            $width = $this->params['width'];
        }
        if (array_key_exists('height', $this->params)) {
            $height = $this->params['height'];
        }

        // NOTE! List ordered by length. For the trimming logic.
        $validunits = ['vmin', 'vmax', 'rem', 'em', 'ex', 'px', 'cm', 'mm',
            'in', 'pt', 'pc', 'ch', 'vh', 'vw', '%'];

        $widthend   = false;
        $heightend  = false;
        $widthtrim  = $width;
        $heighttrim = $height;

        foreach ($validunits as $suffix) {
            if (!$widthend && strlen($width) > strlen($suffix) &&
                substr($width, -strlen($suffix)) === $suffix) {
                $widthend  = true;
                $widthtrim = substr($width, 0, -strlen($suffix));
            }
            if (!$heightend && strlen($height) > strlen($suffix) &&
                substr($height, -strlen($suffix)) === $suffix) {
                $heightend  = true;
                $heighttrim = substr($height, 0, -strlen($suffix));
            }
            if ($widthend && $heightend) {
                break;
            }
        }
        $err = [];

        if (!$widthend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_width');
        }
        if (!$heightend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_height_num');
        }

        if (array_key_exists('width', $this->params) &&
            array_key_exists('height', $this->params) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_overdefined_dimension');
        }
        if (!(array_key_exists('width', $this->params) ||
            array_key_exists('height', $this->params)) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_underdefined_dimension');
        }

        $valids = null;
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $varname = substr($key, 10);
                if (isset($options['inputs']) && !isset($options['inputs'][$varname])) {
                    $err[] = stack_string('stackBlock_jsxgraph_input_missing',
                        ['var' => $varname]);
                }
            } else if ($key !== 'width' && $key !== 'height' && $key !== 'aspect-ratio') {
                $err[] = "Unknown parameter '$key' for jsxgraph-block.";
                $valid    = false;
                if ($valids === null) {
                    $valids = ['width', 'height', 'aspect-ratio'];
                    // The variable $inputdefinitions is not defined!
                    if ($inputdefinitions !== null) {
                        $tmp    = $root->get_parameter('ioblocks');
                        $inputs = [];
                        foreach ($inputdefinitions->get_names() as $key) {
                            $inputs[] = "input-ref-$key";
                        }
                        $valids = array_merge($valids, $inputs);
                    }
                    $err[] = stack_string('stackBlock_jsxgraph_param', [
                        'param' => implode(', ', $valids)]);
                }
            }
        }

        // Wrap the old string errors with the context details.
        foreach ($err as $er) {
            $errors[] = new $options['errclass']($er, $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
        }

        return $valid;
    }
}
