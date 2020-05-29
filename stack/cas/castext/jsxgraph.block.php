<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

// JSXGraph block essentially repeats the functionality of the JSXGraph filter
// in Moodle but limits the use to authors thus negating the primary security
// issue. More importantly it also provides STACK specific extensions in
// the form of references to question inputs.
//
// While this filter is simple and repeat existing logic it does have a purpose.
//
// @copyright  2018 Aalto University
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once("block.interface.php");
require_once(__DIR__ . '/../../../../../../lib/pagelib.php');

class stack_cas_castext_jsxgraph extends stack_cas_castext_block {

    private static $countgraphs = 1;

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = null) {
        // There are currently no CAS evaluated attributes.
        // Only reasonable such would be dynamic size parameters.
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Nothing changes, we want the contents to be evaluated as they are.
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        // There is nothing to do before the full CASText has been evaluated.
        return false;
    }

    public function clear() {
        global $PAGE, $CFG;
        // Now is the time to replace the block with the div and the code.
        $code = "";
        $iter = $this->get_node()->firstchild;
        while ($iter !== null) {
            $code .= $iter->to_string();
            $iter = $iter->nextsibling;
        }

        $divid  = "stack-jsxgraph-" . self::$countgraphs;

        // Input ref prefixes.
        // We could simply expose the prefix Moodle uses and let the author work
        // with that but suppose there exists another VLE which does not use
        // the same prefix for all inputs in the question, by keepping the id
        // mapping here the materials need not care about that and only this
        // needs to be fixed to match the environment.
        // And in any case in Moodle at the time we render this we do not know.
        // The prefix.
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            if (substr($key, 0, 10) === "input-ref-") {
                $varname = substr($key, 10);
                $seekcode = "var $value=stack_jxg.find_input_id(divid,'$varname');";
                $code = "$seekcode\n$code";
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
        $code = '"use strict";try{if(document.getElementById("' . $divid . '")){' . $code . '}} '
            . 'catch(err) {console.log("STACK JSXGraph error in \"' . $divid
            . '\", (note a slight varying offset in the error position due to possible input references):");'
            . 'console.log(err);}';

        $width  = $this->get_node()->get_parameter('width', '500px');
        $height = $this->get_node()->get_parameter('height', '400px');

        $style  = "width:$width;height:$height;";

        $attributes = array('class' => 'jxgbox', 'style' => $style, 'id' => $divid);

        // Empty tags seem to be an issue.
        $this->get_node()->convert_to_text(html_writer::tag('div', '', $attributes));

        $PAGE->requires->js_amd_inline('require(["qtype_stack/jsxgraph","qtype_stack/jsxgraphcore-lazy","core/yui"], '
            . 'function(stack_jxg, JXG, Y){Y.use("mathjax",function(){'.$code.'});});');

        // Up the graph number to generate unique names.
        self::$countgraphs = self::$countgraphs + 1;
    }

    public function validate_extract_attributes() {
        // There are currently no CAS evaluated attributes.
        return array();
    }

    public function validate(&$errors=array()) {
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid      = true;
        $width      = $this->get_node()->get_parameter('width', '500px');
        $height     = $this->get_node()->get_parameter('height', '400px');

        // NOTE! List ordered by length. For the trimming logic.
        $validunits = array("vmin", "vmax", "rem", "em", "ex", "px", "cm", "mm",
                            "in", "pt", "pc", "ch", "vh", "vw", "%");

        $widthend   = false;
        $heightend  = false;
        $widthtrim  = $width;
        $heighttrim = $height;

        foreach ($validunits as $suffix) {
            if (!$widthend && strlen($width) > strlen($suffix) &&
                    substr($width, -strlen($suffix)) === $suffix) {
                $widthend = true;
                $widthtrim = substr($width, 0, -strlen($suffix));
            }
            if (!$heightend && strlen($height) > strlen($suffix) &&
                    substr($height, -strlen($suffix)) === $suffix) {
                $heightend = true;
                $heighttrim = substr($height, 0, -strlen($suffix));
            }
            if ($widthend && $heightend) {
                break;
            }
        }

        if (!$widthend) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_width');
        }
        if (!$heightend) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_height_num');
        }

        // To check if the input references are ok we need to check the parsers
        // stats about inputs. Those are stored in the root node.
        $root = $this->get_node();
        while ($root->parent !== null) {
            $root = $root->parent;
        }
        $valids = null;
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            if (substr($key, 0, 10) === "input-ref-") {
                $varname = substr($key, 10);
                if (!array_key_exists('input', $root->get_parameter('ioblocks', array()))
                    || !array_key_exists($varname, $root->get_parameter('ioblocks')['input'])) {
                    $errors[] = stack_string('stackBlock_jsxgraph_height_num', array('var' => $varname));
                }
            } else if ($key !== 'width' && $key !== 'height') {
                $errors[] = "Unknown parameter '$key' for jsxgraph-block.";
                if ($valids == null) {
                    $valids = array('width', 'height');
                    if (array_key_exists('input', $root->get_parameter('ioblocks', array()))) {
                        $tmp = $root->get_parameter('ioblocks');
                        $inputs = array();
                        foreach ($tmp['input'] as $key => $value) {
                            $inputs[] = "input-ref-$key";
                        }
                        $valids = array_merge($valids, $inputs);
                    }
                    $errors[] = stack_string('stackBlock_jsxgraph_param', array('param' => implode(', ', $valids)));
                }
            }
        }

        // Finally check parent for other issues, should be none.
        if ($valid) {
            $valid = parent::validate($errors);
        }

        return $valid;
    }
}
