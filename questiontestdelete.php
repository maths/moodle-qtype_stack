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
 * This script lets the user delete a question test for a question, after confirmation.
 *
 * @copyright  2012 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/questiontestform.php');
require_once(__DIR__ . '/stack/questiontest.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$testcase = required_param('testcase', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);
$DB->get_record('qtype_stack_qtests', array('questionid' => $question->id, 'testcase' => $testcase),
        '*', MUST_EXIST); // Just to verify that the record exists.

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$backurl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$urlparams['testcase'] = $testcase;
$PAGE->set_url('/question/type/stack/questiontestdelete.php', $urlparams);
$title = stack_string('deletetestcase',
        array('no' => $testcase, 'question' => format_string($question->name)));

require_login();

if (data_submitted() && confirm_sesskey()) {
    // User has confirmed. Actually delete the test case.
    question_bank::get_qtype('stack')->delete_question_test($questionid, $testcase);
    redirect($backurl);
}

// Display the confirmation.
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();

echo $OUTPUT->confirm(stack_string('deletetestcaseareyousure',
        array('no' => $testcase, 'question' => format_string($question->name))),
        new moodle_url($PAGE->url, array('sesskey' => sesskey())), $backurl);

echo $OUTPUT->footer();
