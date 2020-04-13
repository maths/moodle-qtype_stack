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

// If blocks hide their content if the value of their test-attribute is not "true".
//
// @copyright  2013 Aalto University
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once("block.interface.php");
require_once(__DIR__ . '/../ast.container.conditional.class.php');

class stack_cas_castext_if extends stack_cas_castext_block {

    // Remembers the casstring.
    private $string;

    private $condition;

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = null) {
        $condition = $this->get_node()->get_parameter("test", "false");

        $cs = stack_ast_container_conditional_value::make_from_teacher_source($condition, '', new stack_cas_security());
        $cs->set_conditions($conditionstack);

        // Let's provide condition free version for deepper use.
        $this->condition = stack_ast_container::make_from_teacher_source($condition, '', new stack_cas_security());

        $this->string = $cs;
        $cs->set_keyless(true);

        $tobeevaluatedcassession->add_statement($cs);
    }

    public function content_evaluation_context($conditionstack = array()) {
        $conditionstack[] = $this->condition;
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        $errors = $this->string->get_errors();
        if ('' !== $errors && null != $errors) {
            return false;
        }

        $evaluated = $this->string->get_value();

        // If so then move childs up.
        if ($evaluated == 'true') {
            $this->get_node()->destroy_node_promote_children();
        } else { // Otherwise blank.
            $this->get_node()->destroy_node();
        }

        return false;
    }

    public function validate_extract_attributes() {
        $condition = $this->get_node()->get_parameter('test', 'false');
        $r = array(stack_ast_container::make_from_teacher_source($condition, '', new stack_cas_security(), array()));
        return $r;
    }

    public function validate(&$errors='') {
        $valid = true;
        if (!$this->get_node()->parameter_exists('test')) {
            $valid = false;
            $errors[] = stack_string('stackBlock_ifNeedsCondition');
        } else {
            $valid = parent::validate($errors);
        }

        return $valid;
    }

}
