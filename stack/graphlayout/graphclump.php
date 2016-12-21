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
 * Represents a group of nodes that have been laid out relative to each other.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Used by {@link stack_abstract_graph} during the layout algorithm.
 * This represents a group of nodes that have been laid out relative to each other.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_abstract_graph_node_clump {
    /** @var array stack_abstract_graph_node. */
    public $nodes = array();

    /** @var array depth => x-coordinate of the left-most node in the clump at this depth. */
    public $leftedge = array();

    /** @var array depth => y-coordinate of the right-most node in the clump at this depth. */
    public $rightedge = array();

    /**
     * Constructor.
     * @param stack_abstract_graph_node $node the the node that is starting this clump.
     * @param int $x x-co-ordinate to place the node at.
     */
    public function __construct(stack_abstract_graph_node $node, $x = 0) {
        $this->add_node($node, $x, 0);
    }

    /**
     * Add a node to this clump.
     *
     * As part of this operation, we will change the x-coordinate to be an integer.
     * in order to do that, we may move apart nodes lower down the tree to make more room.
     *
     * @param stack_abstract_graph_node $node the the node to add to this clump.
     * @param float $roughx the approximate x-co-ordinate to place the node at.
     * @param int $minspacing the minimum gap between this node and any other at the same depth.
     */
    public function add_node(stack_abstract_graph_node $node, $roughx, $minspacing) {
        // First we adjust the x position to ensure:
        // 1. it is no too close ($minspacing) to another node at the same depth.
        // 2. the position is an integer.
        list($leftneighbour, $rightneighbour) = $this->find_neighbour_positions($node->depth, $roughx);

        if (is_null($leftneighbour) && is_null($rightneighbour)) {
            $x = round($roughx);

        } else if (is_null($rightneighbour)) {
            $x = max(round($roughx), $leftneighbour + $minspacing);

        } else if (is_null($leftneighbour)) {
            $x = min(round($roughx), $rightneighbour - $minspacing);

        } else if ($rightneighbour - $leftneighbour < 2 * $minspacing) {
            // Not enough space.
            $x = ($leftneighbour + $rightneighbour) / 2;

        } else {
            // Plenty of space.
            $x = min(round($roughx), $leftneighbour + $minspacing);
            $x = max($x, $leftneighbour - $minspacing);
        }

        $shift = ceil(abs($x - $roughx));
        if ($shift) {
            if ($x < $roughx) {
                $x += $shift;
            }
            $this->drive_wedge($roughx, $node->depth, $shift);
        }

        $node->x = $x;
        $this->nodes[$node->name] = $node;
        if (array_key_exists($node->depth, $this->leftedge)) {
            $this->leftedge[$node->depth] = min($x, $this->leftedge[$node->depth]);
            $this->rightedge[$node->depth] = max($x, $this->rightedge[$node->depth]);
        } else {
            $this->leftedge[$node->depth] = $x;
            $this->rightedge[$node->depth] = $x;
        }
    }

    /**
     * Fine the next nearest nodes to the left and right at a given level on the tree.
     * @param int $depth the depth to look at.
     * @param float $x the $x position to find the neighbours at.
     * $return array of two float|null. The x-coordinates of the nearest nodes
     *      with node-x < $x and $x <= node-x, if such exist.
     */
    protected function find_neighbour_positions($depth, $x) {
        $leftneighbour = null;
        $rightneighbour = null;
        foreach ($this->nodes as $othernode) {
            if ($othernode->depth != $depth) {
                continue;
            }
            if ($othernode->x <= $x) {
                if (is_null($leftneighbour)) {
                    $leftneighbour = $othernode->x;
                } else {
                    $leftneighbour = max($leftneighbour, $othernode->x);
                }
            } else {
                if (is_null($rightneighbour)) {
                    $rightneighbour = $othernode->x;
                } else {
                    $rightneighbour = min($rightneighbour, $othernode->x);
                }
            }
        }
        return array($leftneighbour, $rightneighbour);
    }

    /**
     * In necessary, move apart nodes to make room for a new node. We do this by
     * moving nodes with x >= $x to the right by $shift, but we only do that if necessary.
     * Also, sometimes nodes with x = $x are only moved by floor($shift/2).
     * @param float $x
     * @param int $startdepth
     * @param int $shift
     */
    protected function drive_wedge($x, $startdepth, $shift) {
        $wedgenecessary = false;
        foreach ($this->nodes as $node) {
            if ($node->x == $x && $node->depth > $startdepth) {
                $wedgenecessary = true;
                break;
            } else if ($node->x > $x && $node->x < $x + $shift) {
                $wedgenecessary = true;
                break;
            }
        }

        if (!$wedgenecessary) {
            return;
        }

        foreach ($this->nodes as $node) {
            if ($node->x == $x && $node->depth > $startdepth) {
                $node->x += floor($shift / 2);
            } else if ($node->x > $x) {
                $node->x += $shift;
            }
        }
    }

    /**
     * @param stack_abstract_graph_node $node
     * @return bool whether this clump contains the given node.
     */
    public function contains(stack_abstract_graph_node $node) {
        return array_key_exists($node->name, $this->nodes);
    }

    /**
     * Move a clump a certain distance in the x-direction.
     * @param float $dx
     */
    public function shift($dx) {
        foreach ($this->nodes as $node) {
            $node->x += $dx;
        }
        foreach ($this->leftedge as $depth => $notused) {
            $this->leftedge[$depth] += $dx;
            $this->rightedge[$depth] += $dx;
        }
    }

    /**
     * Given an other clump, computer how far it should be moved to the right
     * in order to sit to the right of this one, with at least a given gap at
     * every depth.
     * @param stack_abstract_graph_node_clump $otherclump
     * @param float $gap
     */
    public function comput_offset(stack_abstract_graph_node_clump $otherclump, $gap) {
        $requiredshifts = array();
        foreach ($this->rightedge as $depth => $right) {
            if (array_key_exists($depth, $otherclump->leftedge)) {
                $requiredshifts[] = $right + $gap - $otherclump->leftedge[$depth];
            }
        }
        if ($requiredshifts) {
            return max($requiredshifts);
        } else {
            return 0.0;
        }
    }

    /**
     * Add all the nodes from the other clump to this clump.
     * @param stack_abstract_graph_node_clump $otherclump
     */
    public function merge_in(stack_abstract_graph_node_clump $otherclump) {
        foreach ($otherclump->rightedge as $depth => $right) {
            if (array_key_exists($depth, $this->rightedge)) {
                $this->rightedge[$depth] = max($this->rightedge[$depth], $otherclump->rightedge[$depth]);
                $this->leftedge[$depth] = min($this->leftedge[$depth], $otherclump->leftedge[$depth]);
            } else {
                $this->rightedge[$depth] = $otherclump->rightedge[$depth];
                $this->leftedge[$depth] = $otherclump->leftedge[$depth];
            }
        }
        $this->nodes += $otherclump->nodes;
    }
}
