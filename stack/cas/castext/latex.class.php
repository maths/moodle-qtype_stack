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
 * Latex blocks correspond to {@content@} -syntax and ouput the content evaluated in CAS in LaTeX-form.
 * The node referenced by this block is expected to be an instance of DOMText
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../casstring.class.php');
require_once("block.interface.php");

class stack_cas_castext_latex extends stack_cas_castext_block {

    /**
     * remembers the number for this instance
     */
    private $number;

    public function extract_attributes(&$tobeevaluatedcassession, $conditionstack = null) {
        $cs = null;
        $cs = new stack_cas_casstring(trim($this->get_node()->get_content()), $conditionstack);

        $sessionkeys = $tobeevaluatedcassession->get_all_keys();
        $i = 0;
        do { // ... make sure names are not already in use.
            $key = 'caschat'.$i;
            $i++;
        } while (in_array($key, $sessionkeys));
        $this->number = $i - 1;

        $cs->get_valid($this->security, $this->syntax, $this->insertstars);
        $cs->set_key($key, true);

        $tobeevaluatedcassession->add_vars(array($cs));
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Adds nothing to the evaluation context as we have nothing inside.
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        $evaluated = $evaluatedcassession->get_display_key("caschat".$this->number);
        if (strpos($evaluated, "<html") !== false) {
            $this->get_node()->convert_to_text($evaluated);
        } else {
            if ($this->get_node()->get_mathmode() == true) {
                $this->get_node()->convert_to_text("{".$evaluated."}");
            } else {
                $this->get_node()->convert_to_text("\\({".$evaluated."}\\)");
            }
        }

        return false;
    }

    public function validate_extract_attributes() {
        $r = array(new stack_cas_casstring(trim($this->get_node()->get_content())));
        return $r;
    }
}