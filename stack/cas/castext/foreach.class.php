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
 * foreach blocks do just what one would expect. One gives them a list or
 * a set and and they will repeat their contents and add a define block
 * before each repetition. For example:
 *
 *   [[ foreach I='[1,2,3]' K='{4,5,6}' ]]({#I#},{#K#}) [[/ foreach ]]
 *
 * will generate:
 *
 *   [[ define I='1' K='4' /]]({#I#},{#K#}) [[ define I='2' K='5' /]]({#I#},{#K#}) [[ define I='3' K='6' /]]({#I#},{#K#})
 *
 * and that will evaluate to:
 *
 *   (1,4) (2,5) (3,6)
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../casstring.class.php');

class stack_cas_castext_foreach extends stack_cas_castext_block {

    /**
     * remembers the numbers for this instance
     */
    private $numbers = array();

    public function extract_attributes(&$tobeevaluatedcassession, $conditionstack = null) {
        $sessionkeys = $tobeevaluatedcassession->get_all_keys();
        $i = 0;
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            $cs = null;
            $cs = new stack_cas_casstring($value, $conditionstack);
            $caskey = '';
            do { // ... make sure names are not already in use.
                $caskey = 'caschat'.$i;
                $i++;
            } while (in_array($caskey, $sessionkeys));
            $this->numbers[$key] = $i - 1;
            $cs->get_valid($this->security, $this->syntax, $this->insertstars);
            $cs->set_key($caskey, true);
            $tobeevaluatedcassession->add_vars(array($cs));
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
        foreach ($this->numbers as $key => $id) {
            $lists[$key] = stack_utils::list_to_array($evaluatedcassession->get_value_key("caschat".$id), false);
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

        // As the block generates code to be evaluated...
        return true;
    }

    public function validate_extract_attributes() {
        $r = array();
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            $cs = new stack_cas_casstring($key . ':' . $value);
            $r[] = $cs;
        }
        return $r;
    }

}