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
 * This script lets the user delete a question test for a question, after confirmation.
 *
 * @copyright  2012 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/questiontestform.php');
require_once(dirname(__FILE__) . '/stack/questiontest.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$seed = optional_param('seed', null, PARAM_INT);
$testcase = required_param('testcase', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);
$context = $question->get_context();
$testcasedata = $DB->get_record('qtype_stack_qtests',
            array('questionid' => $question->id, 'testcase' => $testcase), '*', MUST_EXIST);

// Check permissions.
require_login();
question_require_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$urlparams = array('questionid' => $question->id);
if (!is_null($seed)) {
    $urlparams['seed'] = $seed;
}
$backurl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$urlparams['testcase'] = $testcase;
$PAGE->set_url('/question/type/stack/questiontestdelete.php', $urlparams);
$PAGE->set_context($context);
$title = get_string('deletetestcase', 'qtype_stack',
        array('no' => $testcase, 'question' => format_string($question->name)));
// TODO fix page layout and navigation.

if (data_submitted() && confirm_sesskey()) {
    // User has confirmed. Actually delete the test case.
    $DB->delete_records('qtype_stack_qtest_expected',
            array('questionid' => $questionid, 'testcase' => $testcase));
    $DB->delete_records('qtype_stack_qtest_inputs',
            array('questionid' => $questionid, 'testcase' => $testcase));
    $DB->delete_records('qtype_stack_qtests',
            array('questionid' => $questionid, 'testcase' => $testcase));
    redirect($backurl);
}

// Display the confirmation.
$PAGE->set_title($title);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $OUTPUT->confirm(get_string('deletetestcaseareyousure', 'qtype_stack',
        array('no' => $testcase, 'question' => format_string($question->name))),
        new moodle_url($PAGE->url, array('sesskey' => sesskey())), $backurl);

echo $OUTPUT->footer();
