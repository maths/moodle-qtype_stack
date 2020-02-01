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

// Define blocks allow one to (re)define variables in the middle of castext.
// They are meant for writing out for-blocks but no one says that you could not use them.
//
// @copyright  2013 Aalto University
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once("block.interface.php");
require_once(__DIR__ . '/../ast.container.conditional.class.php');

class stack_cas_castext_define extends stack_cas_castext_block {

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = null) {
        $css = array();

        foreach ($this->get_node()->get_parameters() as $key => $value) {
            // In 4.3 the nounification happens in such a way that evaluation of
            // conditions may break, therefore we must force denounification here.

            $raw = "$key:$value";
            $cs = stack_ast_container_conditional_silent::make_from_teacher_source($raw, '', new stack_cas_security());
            $cs->set_conditions($conditionstack);
            $cs->set_keyless(true);
            $cs->set_nounify(0);

            $tobeevaluatedcassession->add_statement($cs);
        }
    }

    public function content_evaluation_context($conditionstack = array()) {
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        return false;
    }

    public function clear() {
        $this->get_node()->destroy_node();
    }

    public function validate_extract_attributes() {
        $r = array();
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            $cs = stack_ast_container::make_from_teacher_source($key . ':' . $value, '', new stack_cas_security(), array());
            $r[] = $cs;
        }
        return $r;
    }

}
