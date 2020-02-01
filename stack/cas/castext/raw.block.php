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

require_once("block.interface.php");

class stack_cas_castext_raw extends stack_cas_castext_block {

    private $string;

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = array()) {
        $raw = trim($this->get_node()->get_content());
        $cs = stack_ast_container_conditional_value::make_from_teacher_source($raw, '', new stack_cas_security());
        $cs->set_conditions($conditionstack);
        $cs->set_keyless(true);
        $this->string = $cs;

        $tobeevaluatedcassession->add_statement($cs);
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Adds nothing to the evaluation context as we have nothing inside.
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {

        $errors = $this->string->get_errors();
        if ('' !== $errors && null != $errors) {
            $this->get_node()->convert_to_text($this->get_node()->get_content());
            return false;
        }

        $thenewone = $this->string->get_value();
        $this->get_node()->convert_to_text($thenewone);

        return false;
    }

    public function validate_extract_attributes() {
        $condition = trim($this->get_node()->get_content());
        $r = array(stack_ast_container::make_from_teacher_source($condition, '', new stack_cas_security(), array()));
        return $r;
    }
}