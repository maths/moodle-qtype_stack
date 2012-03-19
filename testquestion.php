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
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/stack/utils.class.php');
require_once(dirname(__FILE__) . '/stack/options.class.php');
require_once(dirname(__FILE__) . '/stack/cas/castext.class.php');
require_once(dirname(__FILE__) . '/stack/cas/casstring.class.php');
require_once(dirname(__FILE__) . '/stack/cas/cassession.class.php');


// Get the parameters from the URL.
$questionid = required_param('id', PARAM_INT);
$seed = optional_param('seed', null, PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);
$context = $question->get_context();

// Check permissions.
require_login();
question_require_capability_on($questiondata, 'view');
$canedit = question_has_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$urlparams = array('id' => $question->id);
if (!is_null($seed)) {
    $urlparams['seed'] = $seed;
}
$PAGE->set_url('/question/type/stack/testquestion.php', $urlparams);
$PAGE->set_context($context);
$title = get_string('testingquestion', 'qtype_stack', format_string($question->name));
$PAGE->set_title($title);

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

// Load the list of test cases.
$testscases = array(null);
// TODO

// Exectue the tests.
// TODO

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Display the list of deployed variants, with UI to edit the list.
// TODO

// Display the question.
echo $quba->render_question($slot, $options);

// Display the question note
echo $OUTPUT->heading(get_string('questionnote', 'qtype_stack'), 3);
echo html_writer::tag('p', $question->get_question_summary());

// Display the question variables.
echo $OUTPUT->heading(get_string('questionvariables', 'qtype_stack'), 3);

// Display the controls to add another question test.
echo $OUTPUT->heading(get_string('questiontests', 'qtype_stack'), 3);
// TODO

// Display the test results.
foreach ($testscases as $key => $results) {
    echo $OUTPUT->heading(get_string('testcasex', 'qtype_stack', $key + 1), 3);
    // TODO
}

// Finish output.
echo $OUTPUT->footer();
