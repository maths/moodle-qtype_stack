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

// The foreach block does just what one would expect. One gives them a list or
// a set and and they will repeat their contents and add a define block
// before each repetition. For example:
//
// [[ foreach I='[1,2,3]' K='{4,5,6}' ]]({#I#},{#K#}) [[/ foreach ]]
//
// will generate:
//
// [[ define I='1' K='4' /]]({#I#},{#K#}) [[ define I='2' K='5' /]]({#I#},{#K#}) [[ define I='3' K='6' /]]({#I#},{#K#})
//
// and that will evaluate to:
//
// (1,4) (2,5) (3,6)
//
// @copyright  2013 Aalto University
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once("block.interface.php");
require_once(__DIR__ . '/../ast.container.conditional.class.php');


class stack_cas_castext_foreach extends stack_cas_castext_block {

    // Remembers the strings.
    private $strings = array();

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = null) {
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            $cs = stack_ast_container_conditional_value::make_from_teacher_source($value, '', new stack_cas_security());
            $cs->set_conditions($conditionstack);
            $this->strings[$key] = $cs;
            $cs->set_keyless(true);

            $tobeevaluatedcassession->add_statement($cs);
        }
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Foreach blocks contents may not be evaluated before the block has been writen open.
        return false;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        // Extract the lists.
        $lists = array();
        $maxlength = -1;
        foreach ($this->strings as $key => $value) {
            $errors = $value->get_errors();
            if ('' !== $errors && null != $errors) {
                return false;
            }

            $lists[$key] = stack_utils::list_to_array($value->get_value(), false);
            if ($maxlength == -1 || $maxlength > count($lists[$key])) {
                $maxlength = count($lists[$key]);
            }
        }

        // What we are repeating.
        $innertext = "";
        $iter = $this->get_node()->firstchild;
        while ($iter !== null) {
            $innertext .= $iter->to_string();
            $iter = $iter->nextsibling;
        }

        $newtext = "";

        // For each iteration...
        for ($i = 0; $i < $maxlength; $i++) {
            $newtext .= "[[ define";
            foreach ($lists as $key => $list) {
                $newtext .= " $key=";
                if (strpos($list[$i], "'") === false) {
                    $newtext .= "'" . $list[$i] . "'";
                } else {
                    $newtext .= '"' . $list[$i] . '"';
                }
            }
            $newtext .= " /]]";
            $newtext .= $innertext;
        }

        $this->get_node()->convert_to_text($newtext);

        // As the block generates code to be evaluated.
        return true;
    }

    public function validate_extract_attributes() {
        $r = array();
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            $cs = stack_ast_container::make_from_teacher_source($key . ':' . $value, '', new stack_cas_security());
            $r[] = $cs;
        }
        return $r;
    }

}
