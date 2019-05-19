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

// Raw blocks correspond to {#content#} -syntax and ouput the content evaluated in CAS in CAS-syntax.
// The node referenced by this block is expected to be an instance of DOMText
//
// @copyright  2013 Aalto University
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../casstring.class.php');
require_once("block.interface.php");

class stack_cas_castext_raw extends stack_cas_castext_block {

    // Remembers the number for this instance.
    private $number;

    public function extract_attributes(&$tobeevaluatedcassession, $conditionstack = array()) {

        $sessionkeys = $tobeevaluatedcassession->get_all_keys();
        $i = 0;
        do { // Make sure names are not already in use.
            $key = 'caschat'.$i;
            $i++;
        } while (in_array($key, $sessionkeys));
        $this->number = $i - 1;

        // The new ast_container does not modify the casstring, so we create the key here
        // to avoid using "set_key" methods on the ast.
        $raw = $key . ':' . trim($this->get_node()->get_content());
        $cs = stack_ast_container::make_from_teacher_source($raw, '', new stack_cas_security(), $conditionstack);

        $tobeevaluatedcassession->add_vars(array($cs));
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Adds nothing to the evaluation context as we have nothing inside.
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        $thenewone = $evaluatedcassession->get_value_key("caschat".$this->number);
        $this->get_node()->convert_to_text($thenewone);

        return false;
    }

    public function validate_extract_attributes() {
        $r = array(new stack_cas_casstring(trim($this->get_node()->get_content())));
        return $r;
    }
}