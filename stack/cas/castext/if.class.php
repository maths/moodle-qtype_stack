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
 * If blocks hide their content if the value of their test-attribute is not "true".
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../conditionalcasstring.class.php');
require_once(dirname(__FILE__) . '/../casstring.class.php');

class stack_cas_castext_if extends stack_cas_castext_block {

    /**
     * remembers the number for this instance
     */
    private $number;

    private $condition;

    public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL) {
        $condition = $this->get_node()->get_parameter("test","false");

        $cs = NULL;
        if ($conditionstack === NULL || count($conditionstack) === 0) {
            $cs = new stack_cas_casstring($condition);
        } else {
            $cs = new stack_cas_conditionalcasstring($condition,$conditionstack);
        }

        $this->condition = $cs;

        $session_keys = $tobeevaluatedcassession->get_all_keys();
        $i = 0;
        do { // ... make sure names are not already in use.
            $key = 'caschat'.$i;
            $i++;
        } while (in_array($key, $session_keys));
        $this->number = $i-1;

        $cs->set_key($key,true);

        $tobeevaluatedcassession->add_vars(array($cs));
    }

    public function content_evaluation_context($conditionstack = array()) {
        $conditionstack[] = $this->condition;
        return $conditionstack;
    }

    public function process_content($evaluatedcassession,$conditionstack = NULL) {
        $evaluated = $evaluatedcassession->get_value_key("caschat".$this->number);

        // If so then move childs up
        if ($evaluated == 'true') {
            $this->get_node()->destroy_node_promote_children();
        } else { // otherwise blank
            $this->get_node()->destroy_node();
        }

        return false;
    }

}
