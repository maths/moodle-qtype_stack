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
 * A utility class for checking that a graph is acyclic and connected.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * A utility class for checking that a graph is acyclic and connected.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_acyclic_graph_checker {
    /** @var array node id => array of ids of successor node ids. */
    protected $next;

    /** @var array node id => true of nodes that have been visited. */
    protected $seen = array();

    /**
     * @var array node ids that have been visited on the path from root that is
     * currently being explored in the depth-first search.
     */
    protected $stack = array();

    /** @var gets set to an array node id => node id if a backlink is found. */
    protected $loopfound = null;

    /**
     * Check a graph to ensure that all nodes are reachable from $firstnode,
     * and that there are no cycles.
     * @param array $nextnodes array node id => array of ids of successor node ids.
     * @param mixed $firstnode id of the node to start checking at.
     * @return mixed 'disconnected' if the graph is not connected.
     *         array(node id, node id) if a back-link is found.
     *         false if there are no problems.
     */
    public static function check_graph($nextnodes, $firstnode) {
        $checker = new self($nextnodes);
        return $checker->check($firstnode);
    }

    /**
     * Make a new checker for a particular graph.
     * @param array $nextnodes array node id => array of ids of successor node ids.
     */
    protected function __construct($nextnodes) {
        $this->next = $nextnodes;
    }

    /**
     * Check the graph to ensure that all nodes are reachable from $firstnode,
     * and that there are no cycles.
     * @param mixed $firstnode id of the node to start checking at.
     * @return array first element is a string that summarises the problem, second element has details:
     *         '' - no problme found. Second element null.
     *         'disconnected' - the graph is not connected. Second element is a list of nodes not reachable from the first.
     *         'backlink' - a backlink was found during the depth-first search, showing that there was a cycle.
     */
    protected function check($firstnode) {
        $this->seen = array();
        $this->stack = array();
        $this->loopfound = null;

        $this->depth_first_search($firstnode);
        if ($this->loopfound) {
            return array('backlink', $this->loopfound);
        }

        if (!empty($this->stack)) {
            throw new coding_exception('Stack not empty at the end of the search.');
        }

        if (count($this->seen) != count($this->next)) {
            $missing = array();
            foreach ($this->next as $node => $notused) {
                if (!array_key_exists($node, $this->seen)) {
                    $missing[] = $node;
                }
            }
            return array('disconnected', $missing);
        }

        return array('', null);
    }

    protected function depth_first_search($currentnode) {
        if (!array_key_exists($currentnode, $this->next)) {
            throw new coding_exception('Node ' . $currentnode . ' is not in the graph.');
        }
        $this->seen[$currentnode] = true;

        foreach ($this->stack as $visited) {
            if ($visited == $currentnode) {
                $this->loopfound = array(end($this->stack), $currentnode);
                return;
            }
        }
        array_push($this->stack, $currentnode);

        foreach ($this->next[$currentnode] as $next) {
            $this->depth_first_search($next);
            if ($this->loopfound) {
                return;
            }
        }

        if (array_pop($this->stack) != $currentnode) {
            throw new coding_exception('Something went wrong with the stack.');
        }
        return;
    }
}
