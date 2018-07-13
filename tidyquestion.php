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
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script lets rename parts of the question, which is not possible using
 * the standard editing form.
 *
 * @copyright  2013 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/tidyquestionform.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $notused, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/tidyquestion.php', $urlparams);
$title = stack_string('tidyquestionx', format_string($question->name));
$PAGE->set_title($title);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('admin');

require_login();

// The URL back to the preview page.
$returnurl = question_preview_url($questionid, null, null, null, null, $context);

// Create the question usage we will use.
$quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
$quba->set_preferred_behaviour('adaptive');
$slot = $quba->add_question($question, $question->defaultmark);
$quba->start_question($slot);

// Now we are going to display the question with each input box containing the
// name of that input, and each feedback area displaying something like
// "Feedback from PRT {name}". To do this, first we submit the right answer to
// get the question into a state where all feedback will be displayed.
$response = $question->get_correct_response();
$response['-submit'] = 1;
$quba->process_action($slot, $response);

// Now we want to be able to display the question with the wrong input values
// and PRT feedback. We do that by polluting the question's input state and
// PRT result caches with the data we want to display, then the renderer will
// display that.
$question->setup_fake_feedback_and_input_validation();

// Prepare the display options.
$options = new question_display_options();
$options->readonly = true;
$options->flags = question_display_options::HIDDEN;
$options->suppressruntestslink = true;

// Create the form for renaming bits of the question.
$form = new qtype_stack_tidy_question_form($PAGE->url, $question);

if ($form->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $form->get_data()) {
    $qtype = question_bank::get_qtype('stack');
    $transaction = $DB->start_delegated_transaction();

    // Rename the inputs.
    $inputrenames = array();
    foreach ($question->inputs as $inputname => $notused) {
        $inputrenames[$inputname] = $data->{'inputname_' . $inputname};
    }
    foreach (stack_utils::decompose_rename_operation($inputrenames) as $from => $to) {
        $qtype->rename_input($question->id, $from, $to);
    }

    // Rename the PRT nodes.
    foreach ($question->prts as $prtname => $prt) {
        $noderenames = array();
        foreach ($prt->get_nodes_summary() as $nodekey => $notused) {
            $noderenames[$nodekey] = $data->{'nodename_' . $prtname . '_' . $nodekey} - 1;
        }
        foreach (stack_utils::decompose_rename_operation($noderenames) as $from => $to) {
            $qtype->rename_prt_node($question->id, $prtname, $from, $to);
        }
    }

    // Rename the PRTs. Much easier to do this after the nodes.
    $prtrenames = array();
    foreach ($question->prts as $prtname => $notused) {
        $prtrenames[$prtname] = $data->{'prtname_' . $prtname};
    }
    foreach (stack_utils::decompose_rename_operation($prtrenames) as $from => $to) {
        $qtype->rename_prt($question->id, $from, $to);
    }

    // Done.
    $transaction->allow_commit();
    redirect($returnurl);
}

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Display the question.
echo $OUTPUT->heading(stack_string('questionpreview'), 3);
echo $quba->render_question($slot, $options);

// Display the form to rename bits of the question.
$form->display();

// Finish output.
echo $OUTPUT->footer();
