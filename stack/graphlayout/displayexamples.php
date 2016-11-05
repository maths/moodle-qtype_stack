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
 * Script that displays some example graphs.
 *
 * This was used during development to document hard cases that were laid out
 * badly. It has been kept becuase it will be useful for future developments.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once(__DIR__ . '/graph.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/prt.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('PRT rendering test');

$broken = new stack_abstract_graph();
$broken->add_node(1, 2, 3, '=1', '=0', 'http://google.com');
$broken->add_node(2, null, 1, '+0.1', '-0.1');
$broken->add_node(3, 7, null, '+0.1', '-0.1');
$broken->add_node(4, 7, 8, '+0.1', '-0.1');
$broken->add_node(7, null, null, '+0.1', '-0.1');
$broken->add_node(8, null, null, '+0.1', '-0.1');

$broken2 = new stack_abstract_graph();
$broken2->add_node(1, 2, 3, '=1', '=0');
$broken2->add_node(2, 4, 5, '+0.1', '-0.1');
$broken2->add_node(3, 6, 7, '+0.1', '-0.1');
$broken2->add_node(4, null, null, '+0.1', '-0.1');
$broken2->add_node(5, null, null, '+0.1', '-0.1');
$broken2->add_node(6, null, null, '+0.1', '-0.1');
$broken2->add_node(7, null, null, '+0.1', '-0.1');
$broken2->add_node(8, 9, 10, '=1', '=0');
$broken2->add_node(9, null, null, '+0.1', '-0.1');
$broken2->add_node(10, null, null, '+0.1', '-0.1');

$tree = new stack_abstract_graph();
$tree->add_node(7, null, null, '+0.1', '-0.1');
$tree->add_node(6, null, null, '+0.1', '-0.1');
$tree->add_node(5, null, null, '+0.1', '-0.1');
$tree->add_node(4, null, null, '+0.1', '-0.1');
$tree->add_node(3, 6, 7, '+0.1', '-0.1');
$tree->add_node(2, 4, 5, '+0.1', '-0.1');
$tree->add_node(1, 2, 3, '=1', '=0');

$tree2 = new stack_abstract_graph();
$tree2->add_node(1, 2, 5, '=1', '=0');
$tree2->add_node(2, null, 3, '+0.1', '-0.1');
$tree2->add_node(3, null, 4, '+0.1', '-0.1');
$tree2->add_node(4, null, null, '+0.1', '-0.1');
$tree2->add_node(5, 6, null, '+0.1', '-0.1');
$tree2->add_node(6, 7, null, '+0.1', '-0.1');
$tree2->add_node(7, null, null, '+0.1', '-0.1');

$tree3 = new stack_abstract_graph();
$tree3->add_node(1, 2, 5, '=1', '=0');
$tree3->add_node(2, null, 3, '+0.1', '-0.1');
$tree3->add_node(3, null, 4, '+0.1', '-0.1');
$tree3->add_node(4, null, null, '+0.1', '-0.1');
$tree3->add_node(5, null, 6, '+0.1', '-0.1');
$tree3->add_node(6, null, 7, '+0.1', '-0.1');
$tree3->add_node(7, null, null, '+0.1', '-0.1');

$graph = new stack_abstract_graph();
$graph->add_node(1, 2, 3, '=1', '=0');
$graph->add_node(2, 4, 3, '+0.1', '-0.1');
$graph->add_node(3, 5, 6, '+0.1', '-0.1');
$graph->add_node(4, 7, 8, '+0.1', '-0.1');
$graph->add_node(5, 8, 7, '+0.1', '-0.1');
$graph->add_node(6, 5, null, '+0.1', '-0.1');
$graph->add_node(7, null, null, '+0.1', '-0.1');
$graph->add_node(8, null, null, '+0.1', '-0.1');

$graph2 = new stack_abstract_graph();
$graph2->add_node(1, 2, 2, '=1', '=0');
$graph2->add_node(2, 3, 3, '+0.1', '-0.1');
$graph2->add_node(3, null, null, '+0.1', '-0.1');

$graph3 = new stack_abstract_graph();
$graph3->add_node(1, 2, 3, '=1', '=0');
$graph3->add_node(2, 4, 4, '+0.1', '-0.1');
$graph3->add_node(3, 4, 4, '+0.1', '-0.1');
$graph3->add_node(4, null, null, '+0.1', '-0.1');

$graph4 = new stack_abstract_graph();
$graph4->add_node(1, 3, 2, '=1', '=0');
$graph4->add_node(2, 4, 4, '+0.1', '-0.1');
$graph4->add_node(3, 4, 4, '+0.1', '-0.1');
$graph4->add_node(4, null, null, '+0.1', '-0.1');

$graph5 = new stack_abstract_graph();
$graph5->add_node(1, 7, 2, '=1', '=0');
$graph5->add_node(2, 7, 3, '+0.1', '-0.1');
$graph5->add_node(3, 7, 4, '+0.1', '-0.1');
$graph5->add_node(4, 7, 5, '+0.1', '-0.1');
$graph5->add_node(5, 7, 6, '+0.1', '-0.1');
$graph5->add_node(6, 7, 7, '+0.1', '-0.1');
$graph5->add_node(7, null, null, '+0.1', '-0.1');

$graph6 = new stack_abstract_graph();
$graph6->add_node(7, 10, 8, '=1', '=0');
$graph6->add_node(8, null, 9, '+0.1', '-0.1');
$graph6->add_node(9, 10, 10, '+0.1', '-0.1');
$graph6->add_node(10, null, null, '+0.1', '-0.1');

$examples = array(
    $broken,
    $broken2,
    $tree,
    $tree2,
    $tree3,
    $graph,
    $graph2,
    $graph3,
    $graph4,
    $graph5,
    $graph6,
);
foreach ($examples as $example) {
    $example->layout();
}


echo $OUTPUT->header();
echo $OUTPUT->heading('Example graphs');
$i = 0;
foreach ($examples as $example) {
    echo stack_abstract_graph_svg_renderer::render($example, 'example' . $i++);
}
echo $OUTPUT->footer();
