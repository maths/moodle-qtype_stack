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
 * Class to represtent a vertex in an abstract representation of a PRT.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Represents a node in a {@link stack_abstract_graph}.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_abstract_graph_node {
    /** @var string indentifier for this node. */
    public $name;

    /** @var string identifier of the left child. */
    public $left;

    /** @var string identifier of the right child. */
    public $right;

    /** @var string label on the left edge. */
    public $leftlabel = '';

    /** @var string label on the left edge. */
    public $rightlabel = '';

    /** @param string $url if set, this node should be a link to that URL. */
    public $url = '';

    /** @var int depth of this node in the display. */
    public $depth = null;

    /**
     * @var int x-coordinate to display this node at. (Actually, may be a float
     * at during the layout algorithm, but becomes an int in the end.)
     */
    public $x = 0;

    /**
     * @var array of float. Used during the layout algorithm. See
     * {@link stack_abstract_graph::compute_heuristic_xs()}.
     */
    public $heuristicxs = null;

    /**
     * Constructor.
     * @param string $name name of this node.
     * @param string $left name of the left child.
     * @param string $right name of the right child.
     * @param string $leftlabel lable to display on the edge to the left child.
     * @param string $rightlabel lable to display on the edge to the right child.
     * @param string $url if set, this node should be a link to that URL.
     */
    public function __construct($name, $left, $right, $leftlabel = '', $rightlabel = '', $url = '') {
        $this->name  = $name;
        $this->left  = $left;
        $this->right = $right;
        $this->leftlabel  = $leftlabel;
        $this->rightlabel = $rightlabel;
        $this->url = $url;
    }

    public function __toString() {
        return '[' . $this->name . ' (' . $this->x . ', ' . $this->depth . '): -> ' . $this->left . ', -> ' . $this->right . ']';
    }
}
