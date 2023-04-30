<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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
 * Class to represent a vertex in an abstract graph of a PRT.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/graphnode.php');

/**
 * Represents a node in a STACK PRT extending {@link stack_abstract_graph}.
 *
 * @copyright 2023 The University of Edinburgh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_prt_graph_node extends stack_abstract_graph_node {
    /**
     * @var string statement sent to Maxima by this node.
     */
    public $casstatement;

    /**
     * @var boolean Is the feedback from this test igored?
     */
    public $quiet;

    /**
     * @var string Note create by the true branch.
     */
    public $truenote;

    /**
     * @var string Note create by the false branch.
     */
    public $falsenote;

    public function add_prt_text($casstatement, $quiet, $truenote, $falsenote) {
        $this->casstatement = $casstatement;
        $this->quiet        = $quiet;
        $this->truenote     = $truenote;
        $this->falsenote    = $falsenote;
    }
}
