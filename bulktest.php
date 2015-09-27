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
 * This script runs all the quesion tests for all deployed versions of all
 * questions in a given context.
 *
 * @copyright  2013 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');


// Get the parameters from the URL.
$contextid = required_param('contextid', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

// Login and check permissions.
$context = context::instance_by_id($contextid);
require_login();
require_capability('qtype/stack:usediagnostictools', $context);
$PAGE->set_url('/question/type/stack/bulktest.php', array('contextid' => $context->id));
$PAGE->set_context($context);
$title = stack_string('bulktesttitle', $context->get_context_name());
$PAGE->set_title($title);

// Load the necessary data.
$categories = question_category_options(array($context));
$categories = reset($categories);
$questiontestsurl = new moodle_url('/question/type/stack/questiontestrun.php');
if ($context->contextlevel == CONTEXT_COURSE) {
    $questiontestsurl->param('courseid', $context->instanceid);
} else if ($context->contextlevel == CONTEXT_MODULE) {
    $questiontestsurl->param('cmid', $context->instanceid);
}
$allpassed = true;

// Display.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

foreach ($categories as $key => $category) {
    list($categoryid) = explode(',', $key);
    echo $OUTPUT->heading($category, 3);

    $questionids = $DB->get_records_menu('question',
            array('category' => $categoryid, 'qtype' => 'stack'), 'name', 'id,name');
    if (!$questionids) {
        continue;
    }

    echo html_writer::tag('p', stack_string('replacedollarscount', count($questionids)));

    foreach ($questionids as $questionid => $name) {
        $tests = question_bank::get_qtype('stack')->load_question_tests($questionid);
        if (!$tests) {
            echo $OUTPUT->heading(html_writer::link(new moodle_url($questiontestsurl,
                        array('questionid' => $questionid)), format_string($name)), 4);
            echo html_writer::tag('p', stack_string('bulktestnotests'));
            continue;
        }

        $question = question_bank::load_question($questionid);
        if (empty($question->deployedseeds)) {
            echo $OUTPUT->heading(html_writer::link(new moodle_url($questiontestsurl,
                        array('questionid' => $questionid)), format_string($name)), 4);
            $allpassed = qtype_stack_test_question($question, $tests) && $allpassed;

        } else {
            echo $OUTPUT->heading(format_string($name), 4);
            foreach ($question->deployedseeds as $seed) {
                echo $OUTPUT->heading(html_writer::link(new moodle_url($questiontestsurl,
                        array('questionid' => $questionid, 'seed' => $seed)), stack_string('seedx', $seed)), 5);
                $allpassed = qtype_stack_test_question($question, $tests, $seed) && $allpassed;
            }
        }
    }
}

echo $OUTPUT->heading(stack_string('overallresult'), 3);
if ($allpassed) {
    echo html_writer::tag('p', stack_string('stackInstall_testsuite_pass'),
            array('class' => 'overallresult pass'));
} else {
    echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'),
            array('class' => 'overallresult fail'));
}
echo html_writer::tag('p', html_writer::link(new moodle_url('/question/type/stack/bulktestindex.php'),
        get_string('back')));

echo $OUTPUT->footer();


/**
 * Run the tests for one variant of one question.
 */
function qtype_stack_test_question($question, $tests, $seed = null) {
    flush(); // Force output to prevent timeouts and to make progress clear.
    set_time_limit(30); // Prevent PHP timeouts.
    gc_collect_cycles(); // Because PHP's default memory management is rubbish.

    // Prepare the question and a usage.
    $question = clone($question);
    $question->seed = (int) $seed;
    $quba = question_engine::make_questions_usage_by_activity('qtype_stack', context_system::instance());
    $quba->set_preferred_behaviour('adaptive');

    // Execute the tests.
    $passes = 0;
    $fails = 0;
    foreach ($tests as $key => $testcase) {
        $testresults[$key] = $testcase->test_question($quba, $question, null);
        if ($testresults[$key]->passed()) {
            $passes += 1;
        } else {
            $fails += 1;
        }
    }

    $flag = '';
    $ok = $fails == 0;
    if ($ok) {
        $class = 'pass';
    } else {
        $class = 'fail';
        $flag = '* ';
    }

    echo html_writer::tag('p', $flag.stack_string('testpassesandfails',
            array('passes' => $passes, 'fails' => $fails)), array('class' => $class));

    flush(); // Force output to prevent timeouts and to make progress clear.

    return $ok;
}
