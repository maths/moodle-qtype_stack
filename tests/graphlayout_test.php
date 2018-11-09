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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/graphlayout/graph.php');

// Unit tests for stack_abstract_graph and friends.
//
// @copyright 2013 The Open Unviersity.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_abstract_graph_test extends basic_testcase {

    /**
     * This graph has 4 nodes and should look like:
     * /\
     * \
     */
    public function test_simple_graph() {
        $graph = new stack_abstract_graph();
        $graph->add_node(1, 2, 3, '=1', '=0');
        $graph->add_node(2, null, 4, '+0.1', '-0.1');
        $graph->add_node(3, null, null, '+0.1', '-0.1');
        $graph->add_node(4, null, null, '+0.1', '-0.1');
        $graph->layout();

        $n = $graph->get(1);
        $this->assertEquals(1, $n->depth);
        $this->assertEquals(0, $n->x);

        $n = $graph->get(2);
        $this->assertEquals(2, $n->depth);
        $this->assertEquals(-1, $n->x);

        $n = $graph->get(3);
        $this->assertEquals(2, $n->depth);
        $this->assertEquals(1, $n->x);

        $n = $graph->get(4);
        $this->assertEquals(3, $n->depth);
        $this->assertEquals(0, $n->x);

        $this->assertEmpty($graph->get_broken_cycles());

        $roots = $graph->get_roots();
        $this->assertCount(1, $roots);
        $this->assertArrayHasKey(1, $roots);
    }

    /**
     * This graph has 2 nodes and should look like:
     * ()
     * This is quite a common pattern in STACK questions.
     * Also, here we test layout out a graph where the root node is not first.
     */
    public function test_linear_graph() {
        $graph = new stack_abstract_graph();
        $graph->add_node(2, null, null, '+0.1', '-0.1');
        $graph->add_node(1, 2, 2, '=1', '=0');
        $graph->layout();

        $n = $graph->get(1);
        $this->assertEquals(1, $n->depth);
        $this->assertEquals(0, $n->x);

        $n = $graph->get(2);
        $this->assertEquals(2, $n->depth);
        $this->assertEquals(0, $n->x);

        $this->assertEmpty($graph->get_broken_cycles());

        $roots = $graph->get_roots();
        $this->assertCount(1, $roots);
        $this->assertArrayHasKey(1, $roots);
    }

    /**
     * This graph has 1 node and contains a loop. We verify it is detected.
     */
    public function test_loop_detection() {
        $graph = new stack_abstract_graph();
        $graph->add_node(1, 1, 1, '=1', '=0');
        $graph->layout();

        $n = $graph->get(1);
        $this->assertEquals(1, $n->depth);
        $this->assertEquals(0, $n->x);

        $this->assertEquals(array('1|-1' => true, '1|1' => true),
                $graph->get_broken_cycles());

        $roots = $graph->get_roots();
        $this->assertCount(1, $roots);
        $root = reset($roots);
        $this->assertEquals(1, $root->name);
    }

    /**
     * This graph has 2 distinct nodes. We verify that they are both detected as roots.
     */
    public function test_two_roots() {
        $graph = new stack_abstract_graph();
        $graph->add_node(1, null, null, '=1', '=0');
        $graph->add_node(2, null, null, '=1', '=0');
        $graph->layout();

        $n = $graph->get(1);
        $this->assertEquals(1, $n->depth);
        $this->assertEquals(0, $n->x);

        $n = $graph->get(2);
        $this->assertEquals(1, $n->depth);
        $this->assertEquals(2, $n->x);

        $this->assertEmpty($graph->get_broken_cycles());
        $this->assertSame(array(1, 2), array_keys($graph->get_roots()));
    }

    /**
     * This graph has a link to a non-existent node. We verify that throws an exception.
     *
     * @expectedException coding_exception
     */
    public function test_missing_node() {
        $graph = new stack_abstract_graph();
        $graph->add_node(1, null, 2, '=1', '=0');

        $graph->layout();
    }

    /**
     * This graph has a link to a non-existent node. We verify that throws an exception.
     */
    public function test_get_suggested_node_names() {
        $graph = new stack_abstract_graph();
        $graph->add_node(1, 2, 3);
        $graph->add_node(2, 7, null);
        $graph->add_node(3, 2, 4);
        $graph->add_node(4, 5, 6);
        $graph->add_node(5, null, 9);
        $graph->add_node(6, null, null);
        $graph->add_node(7, null, null);
        $graph->add_node(9, null, null);
        $graph->layout();

        $newnames = $graph->get_suggested_node_names();
        $this->assertEquals(array(1 => 1, 3 => 2, 2 => 3, 7 => 4, 4 => 5, 5 => 6, 9 => 7, 6 => 8), $newnames);
    }
}
