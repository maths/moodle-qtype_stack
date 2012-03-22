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
require_once(dirname(__FILE__) . '/stack/questiontest.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
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
$urlparams = array('questionid' => $question->id);
if (!is_null($seed)) {
    $urlparams['seed'] = $seed;
}
$PAGE->set_url('/question/type/stack/questiontestrun.php', $urlparams);
$PAGE->set_context($context);
$title = get_string('testingquestion', 'qtype_stack', format_string($question->name));
$PAGE->set_title($title);
// TODO fix page layout and navigation.

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
$testscases = question_bank::get_qtype('stack')->load_question_tests($questiondata);

// Exectue the tests.
$testresults = array();
$allpassed = true;
foreach ($testscases as $key => $testcase) {
    $testresults[$key] = $testcase->test_question($quba, $question, $seed);
    if (!$testresults[$key]->passed()) {
        $allpassed = false;
    }
}

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
foreach ($question->get_all_question_vars() as $key => $value) {
    echo  html_writer::tag('p', s($key) . ' = ' . s($value));
}

// Display the controls to add another question test.
echo $OUTPUT->heading(get_string('questiontests', 'qtype_stack'), 2);

// Display the test results.
$addlabel = stack_string('addanothertestcase', 'qtype_stack');
if (empty($testresults)) {
    echo html_writer::tag('p', get_string('notestcasesyet', 'qtype_stack'));
    $addlabel = stack_string('addatestcase', 'qtype_stack');
} else if ($allpassed) {
    echo html_writer::tag('p', stack_string('stackInstall_testsuite_pass'), array('class' => 'overallresult pass'));
} else {
    echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'), array('class' => 'overallresult fail'));
}

if ($canedit) {
    echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
            array('questionid' => $question->id)), $addlabel, 'get');
}

foreach ($testresults as $key => $result) {
    if ($result->passed()) {
        $outcome = html_writer::tag('span', get_string('testsuitepass', 'qtype_stack'), array('class' => 'pass'));
    } else {
        $outcome = html_writer::tag('span', get_string('testsuitefail', 'qtype_stack'), array('class' => 'fail'));
    }
    echo $OUTPUT->heading(get_string('testcasexresult', 'qtype_stack',
            array('no' => $key, 'result' => $outcome)), 3);

    // Display the information about the inputs.
    $inputstable = new html_table();
    $inputstable->head = array(
        get_string('inputname', 'qtype_stack'),
        get_string('inputentered', 'qtype_stack'),
        get_string('inputdisplayed', 'qtype_stack'),
        get_string('inputstatus', 'qtype_stack'),
    );
    $inputstable->attributes['class'] = 'generaltable stacktestsuite';

    foreach ($result->get_input_states() as $inputname => $inputstate) {
        $inputstable->data[] = array(
            s($inputname),
            s($inputstate->input),
            $inputstate->display,
            get_string('inputstatusname' . $inputstate->status, 'qtype_stack'),
        );
    }

    echo html_writer::table($inputstable);

    // Display the information about the PRTs.
    $prtstable = new html_table();
    $prtstable->head = array(
        get_string('prtname', 'qtype_stack'),
        get_string('score', 'qtype_stack'),
        get_string('expectedscore', 'qtype_stack'),
        get_string('penalty', 'qtype_stack'),
        get_string('expectedpenalty', 'qtype_stack'),
        get_string('answernote', 'qtype_stack'),
        get_string('expectedanswernote', 'qtype_stack'),
        get_string('feedback', 'question'),
        get_string('testsuitecolpassed', 'qtype_stack'),
    );
    $prtstable->attributes['class'] = 'generaltable stacktestsuite';

    foreach ($result->get_prt_states() as $prtname => $state) {
        if ($state->testoutcome) {
            $prtstable->rowclasses[] = 'pass';
            $passedcol = get_string('testsuitepass', 'qtype_stack');
        } else {
            $prtstable->rowclasses[] = 'fail';
            $passedcol = get_string('testsuitefail', 'qtype_stack');
        }

        $prtstable->data[] = array(
            $prtname,
            $state->score,
            $state->expectedscore,
            $state->penalty,
            $state->expectedpenalty,
            s($state->answernote),
            s($state->expectedanswernote),
            $state->feedback,
            $passedcol,
        );
    }

    echo html_writer::table($prtstable);

    if ($canedit) {
        echo html_writer::start_tag('div', array('class' => 'testcasebuttons'));
        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
                array('questionid' => $question->id, 'testcase' => $key)),
                stack_string('editthistestcase', 'qtype_stack'), 'get');

        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestdelete.php',
                array('questionid' => $question->id, 'testcase' => $key)),
                stack_string('deletethistestcase', 'qtype_stack'), 'get');
        echo html_writer::end_tag('div');
    }
}

// Finish output.
echo $OUTPUT->footer();
