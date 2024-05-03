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
 * This script is really a bit of fun. It displays all the tolologically different
 * PRTs that are in your question bank, sorted by frequency.
 *
 * Despite being fun, this script is somewhat useful:
 * 1. It is a quick and easy way to check that the graph layout algorithm
 *    is doing a reasonable good job on all the PRTs that people actually use.
 * 2. It is interesting to see how complex are the PRTs that people acutally build.
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
$PAGE->set_title('PRT structures used');

$nodes = $DB->get_recordset('qtype_stack_prt_nodes', [], 'questionid, prtname, nodename',
        'questionid, prtname, nodename, truenextnode, falsenextnode');
$trees = [];
foreach ($nodes as $node) {
    $questions = $DB->get_records('question', ['id' => $node->questionid], '', 'name');
    $qnames = [];
    foreach ($questions as $q) {
        $qnames[] = $q->name;
    }
    $trees[implode(', ', $qnames).' ('.$node->questionid . ') ' . $node->prtname][$node->nodename]
        = [$node->truenextnode, $node->falsenextnode];
}
$nodes->close();
$uniquetrees = [];
$frequency = [];
$qnamesused = [];
foreach ($trees as $qname => $tree) {
    $key = json_encode($tree);
    $uniquetrees[$key] = $tree;
    $qnamesused[$key][] = $qname;
    if (array_key_exists($key, $frequency)) {
        $frequency[$key] += 1;
    } else {
        $frequency[$key] = 1;
    }
}
arsort($frequency);


echo $OUTPUT->header();

$i = 0;
foreach ($frequency as $key => $count) {
    $uniquetree = $uniquetrees[$key];
    $tree = new stack_abstract_graph();
    foreach ($uniquetree as $node => $branches) {
        list($left, $right) = $branches;
        if ($left == -1) {
            $left = null;
        } else {
            $left += 1;
        }
        if ($right == -1) {
            $right = null;
        } else {
            $right += 1;
        }
        $tree->add_node($node + 1, '', $left, $right);
    }
    reset($uniquetree);
    echo $OUTPUT->heading('Tree used ' . $count . ' times');
    if ($count < 10) {
        echo '<p>'.implode('<br .>', $qnamesused[$key]).'</p>';
    }
    try {
        $tree->layout();
        echo stack_abstract_graph_svg_renderer::render($tree, 'real' . $i++);
    } catch (Exception $e) {
        // @codingStandardsIgnoreStart
        print_object($tree);
        foreach ($trees as $name => $tree) {
            if (json_encode($tree) == $key) {
                print_object($name);
            }
        }
        // @codingStandardsIgnoreEnd
    }
}

echo $OUTPUT->footer();
