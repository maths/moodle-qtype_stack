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

// Escape block allows one to output character sequences that would otherwise cause CASText to do something.
//
// @copyright  2017 Aalto University
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once("block.interface.php");

class stack_cas_castext_escape extends stack_cas_castext_block {

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = null) {
        // Nothing is done.
    }

    public function content_evaluation_context($conditionstack = array()) {
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        return false;
    }

    public function clear() {
        $value = $this->get_node()->get_parameter("value", "");
        $this->get_node()->convert_to_text($value);
    }

    public function validate_extract_attributes() {
        return array();
    }

    public function validate(&$errors='') {
        $valid = true;
        if (!$this->get_node()->parameter_exists('value')) {
            $valid = false;
            $errors[] = stack_string('stackBlock_escapeNeedsValue');
        } else {
            $valid = parent::validate($errors);
        }

        return $valid;
    }
}
