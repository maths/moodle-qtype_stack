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
 * Unit tests for stack_utils.
 *
 * @copyright  2012 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../acyclicchecker.class.php');


/**
 * Unit tests for stack_utils.
 *
 * @copyright  2012 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_acyclic_graph_checker_test extends basic_testcase {

    public function test_one_node() {
        $this->assertEquals(array('', null),
                stack_acyclic_graph_checker::check_graph(array(
                        1 => array()), 1));
    }

    public function test_two_nodes_one_edge() {
        $this->assertEquals(array('', null),
                stack_acyclic_graph_checker::check_graph(array(
                        1 => array(2), 2 => array()), 1));
    }

    public function test_two_nodes_two_edges() {
        $this->assertEquals(array('', null),
                stack_acyclic_graph_checker::check_graph(array(
                        1 => array(2, 2), 2 => array()), 1));
    }

    public function test_disconnected() {
        $this->assertEquals(array('disconnected', array(2)),
                stack_acyclic_graph_checker::check_graph(array(
                        1 => array(), 2 => array()), 1));
    }

    public function test_two_node_cycle() {
        $this->assertEquals(array('backlink', array(2, 1)),
                stack_acyclic_graph_checker::check_graph(array(
                        1 => array(2), 2 => array(1)), 1));
    }

    public function test_spurious_edge() {
        $this->setExpectedException('coding_exception');
        stack_acyclic_graph_checker::check_graph(array(1 => array(42)), 1);
    }

    public function test_spurious_start_point() {
        $this->setExpectedException('coding_exception');
        stack_acyclic_graph_checker::check_graph(array(1 => array()), 42);
    }

    public function test_complex_ok() {
        $this->assertEquals(array('', null),
                stack_acyclic_graph_checker::check_graph(array(
                    0 => array(1, 2),
                    1 => array(3),
                    2 => array(4, 5),
                    3 => array(6, 4),
                    4 => array(7, 9, 8),
                    5 => array(7, 9),
                    6 => array(2, 9),
                    7 => array(10),
                    8 => array(),
                    9 => array(10),
                    10 => array(),
                ), 0));
    }

    public function test_complex_loop() {
        $this->assertEquals(array('backlink', array(5, 7)),
                stack_acyclic_graph_checker::check_graph(array(
                    0 => array(1, 2),
                    1 => array(3),
                    2 => array(4, 5),
                    3 => array(6, 4),
                    4 => array(7, 9, 8),
                    5 => array(7, 9),
                    6 => array(2, 9),
                    7 => array(10),
                    8 => array(),
                    9 => array(10),
                    10 => array(5),
                ), 0));
    }

    public function test_another_loop_case() {
        $this->assertEquals(array('backlink', array(1, 0)),
                stack_acyclic_graph_checker::check_graph(array(0 => array(1, 1), 1 => array(0)), 0));
    }
}
