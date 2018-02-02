<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

/**
 * JSXGraph block essentially repeats the functionality of the JSXGraph filter
 * in Moodle but limits the use to authors thus negating the primary security
 * issue. More importantly it also provides STACK specific extensions in
 * the form of references to question inputs.
 *
 * While this filter is simple and repeat existing logic it does have a purpose.
 *
 * @copyright  2018 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../casstring.class.php');
require_once("block.interface.php");
require_once(__DIR__ . '/../../../../../../lib/pagelib.php');

class stack_cas_castext_jsxgraph extends stack_cas_castext_block {

    private static $countgraphs = 1;

    public function extract_attributes(&$tobeevaluatedcassession, $conditionstack = null) {
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

        // Prefix the code with the id of the div.
        $code = "var divid = '$divid';\n$code";

        // Input ref prefixes.
        // We could simply expose the prefix Moodle uses and let the author work
        // with that but suppose there exists another VLE which does not use
        // the same prefix for all inputs in the question, by keepping the id
        // mapping here the materials need not care about that and only this
        // needs to be fixed to match the environment.
        // And in any case in Moodle at the time we render this we do not know
        // The prefix.....
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            if (substr($key, 0, 10) === "input-ref-") {
                $hasinputrefs = true;
                $varname = substr($key, 10);
                $seekcode = self::geninputseek($varname, $divid, $value);
                $code = "$seekcode\n$code";
            }
        }

        $width  = $this->get_node()->get_parameter('width', '500px');
        $height = $this->get_node()->get_parameter('height', '400px');

        $style  = "width:$width;height:$height;";

        $this->get_node()->convert_to_text("<div id='$divid' class='jxgbox' style='$style'></div>");

        // This may prove to be problematic if the version used in any active
        // official JSXGraph filter differs greatly. This may need to check if
        // such a filter exists and coerce it to serve a version instead to
        // avoid dula loading.
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/question/type/stack/thirdparty/jsxgraph/jsxgraphcore.js'), true);

        // Only activate the code if the div actually ended on the page.
        $code = "if (document.getElementById('" . $divid . "') != null) {" . $code . "};";
        $PAGE->requires->js_init_call($code);

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
            $errors[] = "The width of a JSXGraph must use a known CSS-length unit.";
        }
        if (!$heightend) {
            $valid = false;
            $errors[] = "The height of a JSXGraph must use a known CSS-length unit.";
        }

        if (!preg_match('/[0-9]*\.?[0-9]+/', $widthtrim)) {
            $valid = false;
            $errors[] = "The numeric portion of the width of a JSXGraph must be a raw number and must not contain any extra chars.";
        }
        if (!preg_match('/[0-9]*\.?[0-9]+/', $heighttrim)) {
            $valid = false;
            $errors[] = "The numeric portion of the height of a JSXGraph must be a raw number and must not contain any extra chars.";
        }

        // TODO: Check that references have targets...


        // Finally check parent for other issues, should be none.
        if ($valid) {
            $valid = parent::validate($errors);
        }

        return $valid;
    }

    private function geninputseek($name, $targetdiv, $targetvar) {
      $R = "var tmp = document.getElementById('$targetdiv');";
      $R .= "while ((tmp = tmp.parentElement) && !(tmp.classList.contains('formulation') && tmp.parentElement.classList.contains('content')));";
      $R .= "tmp = tmp.querySelector('input[id$=\"_$name\"]');";
      $R .= "\nvar $targetvar = tmp.id;";
      return $R;
    }
}
