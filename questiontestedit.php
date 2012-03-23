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
 * This script lets the user test a question using any question tests defined
 * in the database. It also displays some of the internal workins of questions.
 *
 * Users with moodle/question:view capability can use this script to view the
 * results of the tests.
 *
 * Users with moodle/question:edit can edit the test cases and deployed version,
 * as well as just run them.
 *
 * The script takes one parameter id which is a questionid as a parameter.
 * In can optionally also take a random seed.
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
$testcase = optional_param('testcase', null, PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);
$context = $question->get_context();

// Check permissions.
require_login();
question_require_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$urlparams = array('questionid' => $question->id);
if (!is_null($seed)) {
    $urlparams['seed'] = $seed;
}
$backurl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
if (!is_null($testcase)) {
    $urlparams['testcase'] = $testcase;
}
$PAGE->set_url('/question/type/stack/questiontestedit.php', $urlparams);
$PAGE->set_context($context);
$title = get_string('testingquestion', 'qtype_stack', format_string($question->name));
// TODO fix page layout and navigation.

if (!is_null($testcase)) {
    $title = get_string('editingatestcase', 'qtype_stack', format_string($question->name));
    $submitlabel = get_string('savechanges');
} else {
    $title = get_string('addingatestcase', 'qtype_stack', format_string($question->name));
    $submitlabel = get_string('createtestcase', 'qtype_stack');
}

// Create the editing form.
$mform = new qtype_stack_question_test_form($PAGE->url,
        array('submitlabel' => $submitlabel, 'question' => $question));

// Process the form.
if ($mform->is_cancelled()) {
    unset($urlparams['testcase']);
    redirect($backurl);

} else if ($data = $mform->get_data()) {
    // Process form submission.
    // TODO.
    redirect($backurl);
}

// Create the question usage we will use.
$quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
$quba->set_preferred_behaviour('adaptive');
$slot = $quba->add_question($question, $question->defaultmark);
$quba->start_question($slot, $seed);

// Prepare the display options.
$options = new question_display_options();
$options->readonly = true;
$options->flags = question_display_options::HIDDEN;
$options->suppressruntestslink = true;

// Display the page.
$PAGE->set_title($title);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Show the question read-only.
echo $quba->render_question($slot, $options);

//TODO: formatting?
echo "<hr/>";
echo html_writer::tag('h3', get_string('questionvariables', 'qtype_stack'));
echo html_writer::tag('pre', $question->questionvariables);
echo "<hr/>";
echo html_writer::tag('h3', get_string('questiontext', 'qtype_stack'));
echo html_writer::tag('pre', $question->questiontext);
echo "<hr/>";

// Show the form.
$mform->display();
echo $OUTPUT->footer();
