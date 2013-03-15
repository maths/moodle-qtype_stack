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
require_once(dirname(__FILE__) . '/../conditionalcasstring.class.php');
require_once(dirname(__FILE__) . '/../casstring.class.php');
require_once("block.interface.php");

class stack_cas_castext_latex extends stack_cas_castext_block {

    /**
     * counts blocks so that we can generate unique variable-names for the CAS,
     */
    private static $count = 1;

    /**
     * remembers the count for this instance
     */
    private $thiscount;

    public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL) {
        self::$count++;

        $cs = NULL;
        if ($conditionstack === NULL || count($conditionstack) === 0) {
            $cs = new stack_cas_casstring($this->get_node()->get_content());
        } else {
            $cs = new stack_cas_conditionalcasstring($this->get_node()->get_content(),$conditionstack);
        }

        // TODO: we might want to check that key just in case there is a collision.
        // also the count should be defined at castext instance level so that we can benefit from the cache
        // even when we instantiate the texts in different order
        $cs->set_key("latexCASchat".self::$count,true);
        $this->thiscount = self::$count;

        $tobeevaluatedcassession->add_vars(array($cs));
    }

    public function content_evaluation_context($conditionstack = array()) {
        // adds nothing to the evaluation context as we have nothing inside
        return $conditionstack;
    }

    public function process_content($evaluatedcassession,$conditionstack = NULL) {
        $thenewone = "{".$evaluatedcassession->get_display_key("latexCASchat".$this->thiscount)."}";
        $this->get_node()->convert_to_text($thenewone);

        return false;
    }

}
?>
