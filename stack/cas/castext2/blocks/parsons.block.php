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
require_once(__DIR__ . '/../../../../vle_specific.php');

require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///PARSONS_COUNT///');

class stack_cas_castext2_parsons extends stack_cas_castext2_block {

    /* This is not something we want people to edit in general. */
    public static $namedversions = [
        /* TODO: change to proof minimised scripts
         * make this `cdn-latest` if possible, no point in having it
         * pointing to a particular version.
         */
        'cdn' => [
            'css' => 'https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/1.5.0/jsxgraph.min.css',
            'js' => 'https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/1.5.0/jsxgraphcore.min.js'],
        'local' => [
            'css' => 'cors://jsxgraph.min.css',
            'js' => 'cors://sortable.min.js',
        ]
    ];

    public function compile($format, $options):  ? MP_Node {
        $r = new MP_List([new MP_String('iframe')]);

        // Define iframe params --------------------------------------------------
        $xpars = $this->params;

        // These are some of the othe parameters we do not need to push forward.
        if (isset($xpars['version'])) {
            unset($xpars['version']);
        }
        if (isset($xpars['overridecss'])) {
            unset($xpars['overridecss']);
        }
        if (isset($xpars['overridejs'])) {
            unset($xpars['overridejs']);
        }

        // Disable scrolling for this.
        $xpars['scrolling'] = false;
        // Set a title.
        $xpars['title'] = 'STACK Parsons ///PARSONS_COUNT///';

        // Figure out what scripts we serve ---------------------------------------
        $css = self::$namedversions['local']['css'];
        $js = self::$namedversions['local']['js'];
        if (isset($this->params['version']) &&
            isset(self::$namedversions[$this->params['version']])) {
            $css = self::$namedversions[$this->params['version']]['css'];
            $js = self::$namedversions[$this->params['version']]['js'];
        }
        if (isset($this->params['overridecss'])) {
            $css = $this->params['overridecss'];
        }
        if (isset($this->params['overridejs'])) {
            $js = $this->params['overridejs'];
        }

        $r->items[] = new MP_String(json_encode($xpars));

        // Plug in some style and scripts.
        $mathjax = stack_get_mathjax_url();
        /*$r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $mathjax]))
        ]);*/
        /*$r->items[] = new MP_List([
            new MP_String('style'),
            new MP_String(json_encode(['href' => $css]))
        ]);*/
        /*$r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $js]))
        ]);*/

        // We need to define a size for the inner content. ---------------------------------
        $width  = '500px';
        $height = '400px';
        $aspectratio = false;
        if (array_key_exists('width', $xpars)) {
            $width = $xpars['width'];
        }
        if (array_key_exists('height', $xpars)) {
            $height = $xpars['height'];
        }

        $astyle = "width:calc($width - 3px);height:calc($height - 3px);";

        if (array_key_exists('aspect-ratio', $xpars)) {
            $aspectratio = $xpars['aspect-ratio'];
            // Unset the undefined dimension, if both are defined then we have a problem.
            if (array_key_exists('height', $xpars)) {
                $astyle = "height:calc($height - 3px);aspect-ratio:$aspectratio;";
            } else if (array_key_exists('width', $xpars)) {
                $astyle = "width:calc($width - 3px);aspect-ratio:$aspectratio;";
            }
        }

        // Add container divs for the proof lists to be accessed by sortable ------------------
        $r->items[] = new MP_String('<script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>');
        $r->items[] = new MP_String('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" 
			  rel="stylesheet" 
			  integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" 
			  crossorigin="anonymous">
		<style>body{background-color:inherit;}
			#usedList:empty {height:50px;background-color:floralwhite}
			#availableList > li {background-color:lightcoral}
			#availableList:empty {height:50px;background-color:lightpink} 
		</style>');

        $r->items[] = new MP_String('<div class="container" style="width:100%;height:100%:">
            <div class="row">
                <ul class="list-group col" id="usedList"></ul>
                    <ul class="list-group col" id="availableList"></ul>
            </div>
        </div>');

        // JS script ------------------------------------------------------------------------------

        $code = "\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n";
        $code .= "import {Sortable} from '" . stack_cors_link('sortable.min.js') . "';\n";
        $code .= "import {stack_sortable} from '" . stack_cors_link('stacksortable.min.js') . "';\n";

        // Extract the proof steps from the inner content
        $code .= 'var proofSteps = ';

        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;

        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $code .= $c->value;
            }
        }

        // Link up to STACK inputs
        $code .= 'var inputPromise = stack_js.request_access_to_input("' . $this->params['input'] . '", true);' . "\n";
        $code .= 'inputPromise.then((id) => {' . "\n";
        
        // Generate initial state
        $code .= 'var state;' . "\n";
        $code .= 'state = {used: [], available: [...Object.keys(proofSteps)]};' . "\n";

        // Create the sortable objects by filling in the container div
        $code .= 'const sortable = new stack_sortable(state, id, "availableList");' . "\n";
        $code .= 'sortable.generate_available(proofSteps);' . "\n";
        $code .= 'MathJax.typesetPromise();' . "\n";
        $code .= 'var opts = {...sortable.options, ...{onSort: () => {sortable.update_state(sortableUsed, sortableAvailable);}}}' . "\n";
        $code .= 'var sortableUsed = Sortable.create(usedList, opts);' . "\n";
        $code .= 'var sortableAvailable = Sortable.create(availableList, opts);' . "\n";
        $code .= "\n});";
        
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'module'])),
            new MP_String($code)
        ]);

        return $r;
    }

    public function is_flat() : bool {
        // Even when the content were flat we need to evaluate this during postprocessing.
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor): string {
        return 'This is never happening! The logic goes to [[iframe]].';
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        return true;
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        /*$valid  = true;
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

        if (array_key_exists('version', $this->params) && array_key_exists($this->params['version'], self::$namedversions)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_jsxgraph_unknown_named_version');
        }

        $valids = null;
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $varname = substr($key, 10);
                if (isset($options['inputs']) && !isset($options['inputs'][$varname])) {
                    $err[] = stack_string('stackBlock_jsxgraph_input_missing',
                        ['var' => $varname]);
                }
            } else if ($key !== 'width' && $key !== 'height' && $key !== 'aspect-ratio' &&
                    $key !== 'version' && $key !== 'overridejs' && $key !== 'overridecss') {
                $err[] = "Unknown parameter '$key' for jsxgraph-block.";
                $valid    = false;
                if ($valids === null) {
                    $valids = ['width', 'height', 'aspect-ratio', 'version', 'overridecss', 'overridejs'];
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

        return $valid;*/
    }
}
