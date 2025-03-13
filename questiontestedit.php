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
 * This script lets the user create or edit question tests for a question.
 *
 * @package    qtype_stack
 * @copyright  2012 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/questiontestform.php');
require_once(__DIR__ . '/stack/questiontest.php');
require_once(__DIR__ . '/stack/potentialresponsetreestate.class.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$testcase = optional_param('testcase', null, PARAM_INT);
$confirmthistestcase = optional_param('confirmthistestcase', null, PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', ['id' => $questionid], '*', MUST_EXIST);
$question = question_bank::load_question($questionid);
// We hard-wire decimals to be a full stop when testing questions.
$question->options->set_option('decimals', '.');
if ($testcase || $confirmthistestcase) {
    $qtest = question_bank::get_qtype('stack')->load_question_test($questionid, $testcase);
}

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'edit');

// Work out whether we are adding or editing.
if (!is_null($testcase)) {
    $title = stack_string('editingtestcase',
            ['no' => $testcase, 'question' => format_string($question->name)]);
    $submitlabel = get_string('savechanges');
} else {
    $title = stack_string('addingatestcase', format_string($question->name));
    $submitlabel = stack_string('createtestcase');
}

// Create the question usage we will use.
$quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
$quba->set_preferred_behaviour('adaptive');
if (!is_null($seed)) {
    // This is a bit of a hack to force the question to use a particular seed,
    // even if it is not one of the deployed seeds.
    $question->seed = $seed;
}

$slot = $quba->add_question($question, $question->defaultmark);
$quba->start_question($slot);

// Initialise $PAGE.
$backurl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
if (!is_null($testcase)) {
    $urlparams['testcase'] = $testcase;
}

$PAGE->set_url('/question/type/stack/questiontestedit.php', $urlparams);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

require_login();

// Create the editing form.
$mform = new qtype_stack_question_test_form($PAGE->url,
        ['submitlabel' => $submitlabel, 'question' => $question]);

// Send current data to the form.
if ($testcase) {
    $currentdata = new stdClass();
    $currentdata->description = $qtest->description;

    foreach ($qtest->inputs as $name => $value) {
        $currentdata->{$name} = $value;
    }

    foreach ($qtest->expectedresults as $prtname => $expected) {
        if (is_null($expected->score)) {
            $currentdata->{$prtname . 'score'}      = '';
        } else {
            $currentdata->{$prtname . 'score'}      = $expected->score + 0;  // Fix excessive DPs.
        }
        if (is_null($expected->penalty)) {
            $currentdata->{$prtname . 'penalty'}      = '';
        } else {
            $currentdata->{$prtname . 'penalty'}      = $expected->penalty + 0;  // Fix excessive DPs.
        }
        $currentdata->{$prtname . 'answernote'} = $expected->answernotes[0];
    }

    $mform->set_data($currentdata);
}

// Process the form.
if ($mform->is_cancelled()) {
    unset($urlparams['testcase']);
    redirect($backurl);
} else if ($confirmthistestcase) {
    $inputs = [];
    foreach ($qtest->inputs as $name => $value) {
        $inputs[$name] = $value;
    }
    $qtest = new stack_question_test($qtest->description, $inputs);
    $response = stack_question_test::compute_response($question, $inputs);

    foreach ($question->prts as $prtname => $prt) {
        $result = $question->get_prt_result($prtname, $response, false);
        // For testing purposes we just take the last note.
        $answernotes = $result->get_answernotes();
        $answernote = [end($answernotes)];
        $qtest->add_expected_result($prtname, new stack_potentialresponse_tree_state(
            1, true, $result->get_score(), $result->get_penalty(), '', $answernote));
    }
    question_bank::get_qtype('stack')->save_question_test($questionid, $qtest, $testcase);
    redirect($backurl);
} else if ($data = $mform->get_data()) {
    // Process form submission.
    $inputs = [];
    foreach ($question->inputs as $inputname => $notused) {
        $inputs[$inputname] = $data->$inputname;
    }
    $qtest = new stack_question_test($data->description, $inputs);

    foreach ($question->prts as $prtname => $notused) {
        $qtest->add_expected_result($prtname, new stack_potentialresponse_tree_state(
                1, true, $data->{$prtname . 'score'}, $data->{$prtname . 'penalty'},
                '', [$data->{$prtname . 'answernote'}]));
    }
    question_bank::get_qtype('stack')->save_question_test($questionid, $qtest, $testcase);
    redirect($backurl);
}

// Prepare the display options.
$options = question_display_options();

// Display the page.
echo $OUTPUT->header();

// Show the question read-only.
echo $quba->render_question($slot, $options);

// Display the question variables.
echo $OUTPUT->heading(stack_string('questionvariables'), 3);
echo html_writer::start_tag('div', ['class' => 'questionvariables']);
echo html_writer::tag('pre', $question->questionvariables);
echo html_writer::end_tag('div');

echo $OUTPUT->heading(stack_string('questionvariablevalues'), 3);
echo html_writer::start_tag('div', ['class' => 'questionvariables']);
echo html_writer::tag('pre', $question->get_question_session_keyval_representation());
echo html_writer::end_tag('div');

// Display the question text.
// We need this as well as the rendered view above so that teachers can see the names of variables used.
// This helps when writing question tests using those variables to reflect randomization.
echo $OUTPUT->heading(stack_string('questiontext'), 3);
echo html_writer::tag('pre', $question->questiontext, ['class' => 'questiontext']);

echo html_writer::tag('p', stack_string('testinputsimpwarning'));

// Show the form.
$mform->display();
echo $OUTPUT->footer();
