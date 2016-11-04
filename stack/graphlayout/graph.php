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
 * Abstract representation of a PRT.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/graphnode.php');
require_once(__DIR__ . '/graphclump.php');
require_once(__DIR__ . '/svgrenderer.php');


/**
 * Abstract representation of a graph (e.g. a PRT).
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_abstract_graph {
    /** @var int constant representing a left branch direction. */
    const LEFT = -1;
    /** @var int constant representing a right branch direction. */
    const RIGHT = 1;

    /** @var array node name => stack_abstract_graph_node the nodes of the graph. */
    protected $nodes = array();

    /**
     * @var array array node name => stack_abstract_graph_node once the graph
     * is laid out, this is a list of root nodes.
     */
    protected $roots;

    /**
     * @var array with keys like "node name|-1" or "node name|1". If, we
     * find a cycle in the graph we break it at an arbitrary point, and record
     * that fact here, then carry on. Therefore, in a sense, this is a list of errors.
     */
    protected $brokenloops = array();

    /** @var array depth => array stack_abstract_graph_node. */
    protected $nodesbydepth = array();

    /**
     * @var array of node names that have been visited on the path from root
     * that is currently being explored in the depth-first search.
     */
    protected $stack = array();

    /**
     * Add a node to the graph.
     *
     * @param string $name name of the node to add.
     * @param string $leftchild name of the left child node.
     * @param string $rightchild name of the right child node.
     * @param string $leftlabel lable to display on the edge to the left child.
     * @param string $rightlabel lable to display on the edge to the right child.
     * @param string $url if set, this node should be a link to that URL.
     */
    public function add_node($name, $leftchild, $rightchild, $leftlabel = '', $rightlabel = '', $url = '') {
        $this->nodes[$name] = new stack_abstract_graph_node($name, $leftchild, $rightchild,
                $leftlabel, $rightlabel, $url);
    }

    public function remove_node($nametodelete) {
        foreach ($this->nodes as $name => $node) {
            if ($name == $nametodelete) {
                unset($this->nodes[$name]);
                continue;
            }

            if ($node->left == $nametodelete) {
                $node->left = null;
            }
            if ($node->right == $nametodelete) {
                $node->right = null;
            }
        }
    }

    /**
     * Lay out the graph, based on the left and right links.
     * @param string $firstnode identifier of the root node.
     */
    public function layout() {
        // First we assign a depth to each node, to ensure that ercs always go
        // from one depth to a deeper one.
        $this->stack = array();
        $this->roots = $this->nodes;
        while (true) {
            $firstnode = null;
            foreach ($this->roots as $possibleunexploredroot) {
                if (is_null($possibleunexploredroot->depth)) {
                    $firstnode = $possibleunexploredroot;
                    break;
                }
            }
            if (is_null($firstnode)) {
                break;
            }
            $this->depth_first_search($firstnode, 1);
        }

        // Next, but build arrays listing the nodes at each depth.
        $this->nodesbydepth = array();
        foreach ($this->nodes as $node) {
            $this->nodesbydepth[$node->depth][] = $node;
        }
        krsort($this->nodesbydepth);

        // Work out some rough heuristic x co-ordinates so that we lay out the
        // nodes from left to right.
        reset($this->nodesbydepth);
        $maxdepth = key($this->nodesbydepth);
        foreach ($this->roots as $root) {
            $this->compute_heuristic_xs($root, 1 << $maxdepth, 1 << $maxdepth);
        }
        foreach ($this->nodes as $node) {
            $node->x = array_sum($node->heuristicxs) / count($node->heuristicxs);
            $node->heuristicxs = null;
        }
        foreach ($this->nodesbydepth as $depth => $nodes) {
            uasort($this->nodesbydepth[$depth], array('stack_abstract_graph', 'compare_node_x_coords'));
        }

        // Now, working from the bottom, we stick nodes together to form clumps.
        $this->clumps = array();
        foreach ($this->nodesbydepth as $depth => $nodes) {
            foreach ($nodes as $node) {
                if (is_null($node->left) && is_null($node->right)) {
                    // This is a leaf node, so it starts a new clump.
                    $this->clumps[] = new stack_abstract_graph_node_clump($node);

                } else if (is_null($node->right)) {
                    // Only a left child, so tack it onto that clump.
                    $child = $this->get($node->left);
                    $clump = $this->find_clump_containing_node($child);
                    $clump->add_node($node, $child->x + 1, 2);

                } else if (is_null($node->left)) {
                    // Only a right child, so tack it onto that clump.
                    $child = $this->get($node->right);
                    $clump = $this->find_clump_containing_node($child);
                    $clump->add_node($node, $child->x - 1, 2);

                } else {
                    // Both children, either both in the same clump, or we need to merge clumps.
                    $leftchild = $this->get($node->left);
                    $leftclump = $this->find_clump_containing_node($leftchild);
                    $rightchild = $this->get($node->right);
                    $rightclump = $this->find_clump_containing_node($rightchild);
                    if ($rightclump != $leftclump) {
                        $offset = $leftclump->comput_offset($rightclump, 2);
                        $rightclump->shift($offset);
                        $leftclump->merge_in($rightclump);
                        $this->remove_clump($rightclump);
                    }

                    // @codingStandardsIgnoreStart
                    // Weighted mean based on the length of the two branches.
                    $xpos = (($leftchild->x + 1) * ($rightchild->depth - $node->depth) +
                                ($rightchild->x - 1) * ($leftchild->depth - $node->depth)) /
                                ($rightchild->depth - $node->depth + $leftchild->depth - $node->depth);
                    // @codingStandardsIgnoreEnd

                    $leftclump->add_node($node, $xpos, 2);
                }
            }
        }

        // If there are still multiple clumps left, prevent them overlapping.
        $previousclump = null;
        foreach ($this->clumps as $clump) {
            if ($previousclump) {
                $offset = $previousclump->comput_offset($clump, 2);
                $clump->shift($offset);
            }
            $previousclump = $clump;
        }

        // Now sort each row by level by x-coordinate.
        foreach ($this->nodesbydepth as $depth => $nodes) {
            uasort($this->nodesbydepth[$depth], array('stack_abstract_graph', 'compare_node_x_coords'));
        }
        ksort($this->nodesbydepth);

        // Sort the roots into name order.
        ksort($this->roots);
    }

    /**
     * Used for sorting arrays of nodes by x-coordinate.
     * @param stack_abstract_graph_node $node1
     * @param stack_abstract_graph_node $node2
     */
    protected static function compare_node_x_coords(stack_abstract_graph_node $node1, stack_abstract_graph_node $node2) {
        if ($node1->x < $node2->x) {
            return -1;
        } else if ($node1->x == $node2->x) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * This is the first part of the layout algorithm. We traverse the tree assigning
     * depts to nodes, ensuring all edges go from one depth to a deeper depth, but
     * minimising the depth of each node given that constraint.
     *
     * This is also the place where we detect loops in the graph and break them,
     * and also where we detect broken edges (that go to a non-existent node).
     *
     * @param stack_abstract_graph_node $currentnode
     * @param integer $depth
     */
    protected function depth_first_search(stack_abstract_graph_node $currentnode, $depth) {

        if ($currentnode->depth >= $depth) {
            return; // Aready done, and at least as deep as this path.
        }

        $currentnode->depth = $depth;
        array_push($this->stack, $currentnode->name);

        if ($currentnode->left) {
            if (in_array($currentnode->left, $this->stack)) {
                $this->brokenloops[$currentnode->name . '|' . self::LEFT] = true;
                $currentnode->left = null;
            } else {
                unset($this->roots[$currentnode->left]);
                $this->depth_first_search($this->get($currentnode->left), $depth + 1);
            }
        }
        if ($currentnode->right) {
            if (in_array($currentnode->right, $this->stack)) {
                $this->brokenloops[$currentnode->name . '|' . self::RIGHT] = true;
                $currentnode->right = null;
            } else {
                unset($this->roots[$currentnode->right]);
                $this->depth_first_search($this->get($currentnode->right), $depth + 1);
            }
        }

        if (array_pop($this->stack) != $currentnode->name) {
            throw new coding_exception('Something went wrong with the stack.');
        }
    }

    /**
     * This is the second stage of the layout algorithm. We compute some very crude
     * x-positions top-down (the positions you would get if you laid out a complete
     * binary tree of a given depth). The reason to do this is that the main
     * layout algorithm works bottom-up. It turns out that a bit of top-down information
     * is useful to break ties in the bottom-up stage.
     * @param stack_abstract_graph_node $node the current node.
     * @param int $x heuristic x-position to give that node.
     * @param float $dx the gap that should be left between the two child nodes.
     */
    protected function compute_heuristic_xs(stack_abstract_graph_node $node, $x, $dx) {
        $node->heuristicxs[] = $x;
        $dx /= 2;
        if ($node->left) {
            $this->compute_heuristic_xs($this->get($node->left), $x - $dx, $dx);
        }
        if ($node->right) {
            $this->compute_heuristic_xs($this->get($node->right), $x + $dx, $dx);
        }
    }

    /**
     * Return the clump containing a given node.
     * @param stack_abstract_graph_node $node
     * @return stack_abstract_graph_node_clump
     */
    protected function find_clump_containing_node(stack_abstract_graph_node $node) {
        foreach ($this->clumps as $clump) {
            if ($clump->contains($node)) {
                return $clump;
            }
        }
        throw new coding_exception($node->name . ' is not in any clump.');
    }

    /**
     * Remove a clump from the list of clumps.
     * @param stack_abstract_graph_node_clump $clump
     */
    protected function remove_clump(stack_abstract_graph_node_clump $clump) {
        $key = array_search($clump, $this->clumps);
        if (is_null($key)) {
            throw new coding_exception('Unknown clump.');
        }
        unset($this->clumps[$key]);
    }

    /**
     * Get a node by name.
     * @param string $nodename
     * @return stack_abstract_graph_node
     */
    public function get($nodename) {
        if (!array_key_exists($nodename, $this->nodes)) {
            throw new coding_exception('Node ' . $nodename . ' is not in the graph.');
        }
        return $this->nodes[$nodename];
    }

    /**
     * @return array node name => stack_abstract_graph_node the list of all nodes.
     */
    public function get_nodes() {
        return $this->nodes;
    }

    /**
     * @return array node name => stack_abstract_graph_node nodes that are
     * roots in the graph. (That is, no other node links to them.) Only available
     * once the graph has been laid out.
     */
    public function get_roots() {
        return $this->roots;
    }

    /**
     * @return array with keys like "node name|-1" or "node name|1". If, we
     * find a cycle in the graph we break it at an arbitrary point, and record
     * that fact here, then carry on. Therefore, this is a list of errors.
     * Only available once the graph has been laid out.
     */
    public function get_broken_cycles() {
        return $this->brokenloops;
    }

    /**
     * @param stack_abstract_graph_node $node the parent node of the edge.
     * @param int $direction self::LEFT or self::RIGHT.
     * @return book whether this edge was broken to break a cycle.
     */
    public function is_broken_edge(stack_abstract_graph_node $node, $direction) {
        return array_key_exists($node->name . '|' . $direction, $this->brokenloops);
    }

    /**
     * @return int the maximum depth of any node. Root nodes have depth 1.
     */
    public function max_depth() {
        end($this->nodesbydepth);
        return key($this->nodesbydepth);
    }

    /**
     * @return array with two elements, the minimum and maximum x-coordinates of any node.
     */
    public function x_range() {
        $minx = null;
        $maxx = null;
        foreach ($this->nodes as $node) {
            if (is_null($minx)) {
                $minx = $node->x;
                $maxx = $node->x;
            } else {
                $minx = min($minx, $node->x);
                $maxx = max($maxx, $node->x);
            }
        }
        return array($minx, $maxx);
    }

    /**
     * @param stack_abstract_graph_node $parent the parent node.
     * @param stack_abstract_graph_node $child one of its children.
     * @return bool whether there is another node on the direct line from parent to child.
     */
    public function edge_hits_another_node(stack_abstract_graph_node $parent, stack_abstract_graph_node $child) {
        $x = $parent->x;
        $dx = ($child->x - $parent->x) / ($child->depth - $parent->depth);
        for ($depth = $parent->depth + 1; $depth < $child->depth; $depth += 1) {
            $x += $dx;
            foreach ($this->nodesbydepth[$depth] as $node) {
                if (abs($node->x - $x) < 0.1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return new node names for this tree in a tidy order.
     * We generate the names by doing a depth-first traversal according to the
     * following rules:
     *  - if the two children have unequal depth, follow the shallower branch first.
     *  - otherwise true branch before false branch.
     * @return array old node name => new node name.
     */
    public function get_suggested_node_names() {
        $rawresults = $this->suggested_names_worker(array(), reset($this->roots));
        $newnames = array();
        foreach ($rawresults as $newkey => $oldname) {
            $newnames[$oldname] = $newkey + 1;
        }
        return $newnames;
    }

    /**
     * Recursive function used by {@link get_suggested_node_names}.
     * @param array $alreadynamed partial array of new node name - 1 => old node name.
     * @param stack_abstract_graph_node $node the current node being visited by the algorithm.
     * @param array partial array of new node name - 1 => old node name with all the childern of $currentnode named.
     */
    protected function suggested_names_worker(array $alreadynamed,
            stack_abstract_graph_node $node) {
        if (in_array($node->name, $alreadynamed)) {
            return $alreadynamed;
        }

        $alreadynamed[] = $node->name;

        if (is_null($node->left) && is_null($node->right)) {
            return $alreadynamed;

        } else if (is_null($node->right)) {
            return $this->suggested_names_worker($alreadynamed, $this->get($node->left));

        } else if (is_null($node->left)) {
            return $this->suggested_names_worker($alreadynamed, $this->get($node->right));

        } else if ($this->get($node->right)->depth < $this->get($node->left)->depth) {
            $alreadynamed = $this->suggested_names_worker($alreadynamed, $this->get($node->right));
            $alreadynamed = $this->suggested_names_worker($alreadynamed, $this->get($node->left));
            return $alreadynamed;

        } else {
            $alreadynamed = $this->suggested_names_worker($alreadynamed, $this->get($node->left));
            $alreadynamed = $this->suggested_names_worker($alreadynamed, $this->get($node->right));
            return $alreadynamed;
        }
    }

    public function __toString() {
        $string = '';
        foreach ($this->nodesbydepth as $depth => $nodes) {
            $string .= 'Depth ' . $depth . ': ' . implode(' ', $nodes) . "\n";
        }
        return $string;
    }
}
