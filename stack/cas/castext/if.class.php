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
     * counts blocks so that we can generate unique variable-names for the CAS,
     */
    private static $count = 1;

    /**
     * remembers the count for this instance
     */
    private $thiscount;

    private $condition;

    public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL) {
        self::$count++;

        $condition = $this->get_node()->getAttribute("test");

        $cs = NULL;
        if ($conditionstack === NULL || count($conditionstack) === 0) {
            $cs = new stack_cas_casstring($condition);
        } else {
            $cs = new stack_cas_conditionalcasstring($condition,$conditionstack);
        }

        $this->condition = $cs;

        // TODO: we might want to check that key just in case there is a collision.
        // also the count should be defined at castext instance level so that we can benefit from the cache
        // even when we instantiate the texts in different order
        $cs->set_key("ifCASchat".self::$count,true);
        $this->thiscount = self::$count;

        $tobeevaluatedcassession->add_vars(array($cs));
    }

    public function content_evaluation_context($conditionstack = array()) {
        $conditionstack[] = $this->condition;
        return $conditionstack;
    }

    public function process_content($evaluatedcassession,$conditionstack = NULL) {
        $evaluated = $evaluatedcassession->get_value_key("ifCASchat".$this->thiscount);

        // If so then move childs up
        if ($evaluated === 'true') {
            for ($i = 0; $i < $this->get_node()->childNodes->length; $i++) {
                $child = $this->get_node()->childNodes->item($i)->cloneNode(true);
                if ($this->get_node()->previousSibling) {
                    $this->get_node()->parentNode->insertBefore($child,$this->get_node());
                } else {
                    $this->get_node()->parentNode->appendChild($child);
                }
            }
        }

        // and in all cases make this node go away
        $this->get_node()->parentNode->removeChild($this->get_node());

        return false;
    }

}
?>
