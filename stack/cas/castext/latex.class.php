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
     * content separated by the parser
     */
    private $content;

    /**
     * counts blocks so that we can generate unique variable-names for the CAS,
     */
    private static $count = 1;

    /**
     * remembers the count for this instance
     */
    private $thiscount;

    public function set_content($content) {
        $this->content = $content;
    }

    public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL) {
        self::$count++;

        $cs = NULL;
        if ($conditionstack === NULL || count($conditionstack) === 0) {
            $cs = new stack_cas_casstring($this->content);
        } else {
            $cs = new stack_cas_conditionalcasstring($this->content,$conditionstack);
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
	// String that we were
        $original = "{@".$this->content."@}";

        // Now as evaluated
        $thenewone = $evaluatedcassession->get_display_key("latexCASchat".$this->thiscount);

        // If this is a plot we need to do some trickery
        if (strpos($thenewone,"<html>")!==FALSE) {
            $doc = new DOMDocument();
            $doc->loadXML($thenewone);
            $doc->normalizeDocument();

            // split the DOMText node we have at the begining of $original insert the contents of HTML
            // after that then split the remaining text at end of $original and destroy the node containing $original
            $end = $this->get_node()->splitText(strpos($this->get_node()->wholeText,$original));

            foreach ($doc->documentElement->childNodes as $child) {
                $end->parentNode->insertBefore($end->ownerDocument->importNode($child,true),$end);
            }

            $end->splitText(strlen($original));
            $end->parentNode->removeChild($end);

            return false;
        }

        // replace
        if (is_a($this->get_node(),"DOMAttr")) {
            $this->get_node()->parentNode->setAttribute($this->get_node()->name,str_replace($original,$thenewone,$this->get_node()->value));
        } else {
            $this->get_node()->replaceData(strpos($this->get_node()->wholeText,$original),strlen($original),$thenewone);
        }

        return false;
    }

}
?>
