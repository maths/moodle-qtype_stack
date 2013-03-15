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
 * Interface/base class for the blocks. For each block found by the castext-processor an instance of
 * this kind of a class will be created, so feel free to store state.
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class stack_cas_castext_block {

    /**
     * Nodes here are like DOM-nodes but not quite. The type is stack_cas_castext_parsetreenode, we use these nodes instead of arrays so that the references are simpler to handle.
     */
    private $node;
    private $session;
    private $seed;
    private $security;
    private $syntax;
    private $insertstars;

    /**
     * Returns the node this block is supposed to act on
     */
    public function &get_node() {
        return $this->node;
    }


    /**
     * The functions here are listed in the order they will be called from the castext-processor.
     */
    public function __construct(&$node,&$session=null, $seed=null, $security='s', $syntax=true, $insertstars=false) {
        $this->node = $node;

        // These are for creating a new castext-parser if need be.
        $this->session = &$session;
        $this->seeed = $seed;
        $this->security = $security;
        $this->syntax = $syntax;
        $this->insertstars = $insertstars;
    }

    /**
     * Extracts parameters to be evaluated from the block and adds them to the cas-session.
     * Called when the parser finds this block.
     * The stack of conditions must be added to the casstrings if present.
     * Meant for extracting CAS-commands that have not been encased in "raw"- or "latex"-blocks. As well
     * as things not present in code.
     */
    abstract public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL);

    /**
     * Returns FALSE if the contents of this block should not be processed by the castext-processor
     * calling this function. Otherwise returns a new condition stack including whatever conditions are
     * needed for safe evaluatin of the contents of this block.
     */
    abstract public function content_evaluation_context($conditionstack = array());

    /**
     * Does custom processing of the content. This will be called after content evaluation if it has
     * been done. Content evaluation should modify this XML-node or outright delete it from the parent.
     *
     * Returns true if the DOM should be searched again fro new blocks to be evaluated and false if this
     * block caused nothing to be hidden from evaluation nor created new things to be evaluated.
     */
    abstract public function process_content($evaluatedcassession,$conditionstack = NULL);

}


?>


