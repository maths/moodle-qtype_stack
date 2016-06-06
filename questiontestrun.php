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
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.

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

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/questiontest.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'view');
$canedit = question_has_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/questiontestrun.php', $urlparams);
$title = stack_string('testingquestion', format_string($question->name));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

// Create some other useful links.
$qbankparams = $urlparams;
unset($qbankparams['questionid']);
unset($qbankparams['seed']);
$qbankparams['qperpage'] = 1000; // Should match MAXIMUM_QUESTIONS_PER_PAGE but that constant is not easily accessible.
$qbankparams['category'] = $questiondata->category . ',' . $question->contextid;
$qbankparams['lastchanged'] = $question->id;
if ($questiondata->hidden) {
    $qbankparams['showhidden'] = 1;
}
$questionbanklink = new moodle_url('/question/edit.php', $qbankparams);
$exportquestionlink = new moodle_url('/question/type/stack/exportone.php', $urlparams);
$exportquestionlink->param('sesskey', sesskey());

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

// Prepare the display options.
$options = new question_display_options();
$options->readonly = true;
$options->flags = question_display_options::HIDDEN;
$options->suppressruntestslink = true;

// Load the list of test cases.
$testscases = question_bank::get_qtype('stack')->load_question_tests($question->id);

// Execute the tests.
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
$renderer = $PAGE->get_renderer('qtype_stack');

$deployfeedback = optional_param('deployfeedback', null, PARAM_TEXT);
if (!is_null($deployfeedback)) {
    echo html_writer::tag('p', $deployfeedback, array('class' => 'overallresult pass'));
}
$deployfeedbackerr = optional_param('deployfeedbackerr', null, PARAM_TEXT);
if (!is_null($deployfeedbackerr)) {
    echo html_writer::tag('p', $deployfeedbackerr, array('class' => 'overallresult fail'));
}

// Display the list of deployed variants, with UI to edit the list.
if ($question->deployedseeds) {
    echo $OUTPUT->heading(stack_string('deployedvariantsn', count($question->deployedseeds)), 3);
} else {
    echo $OUTPUT->heading(stack_string('deployedvariants'), 3);
}

$variantmatched = false;
$variantdeployed = false;
if (!$question->has_random_variants()) {
    echo html_writer::tag('p', stack_string('questiondoesnotuserandomisation') .
            ' ' . $OUTPUT->action_icon(question_preview_url($questionid, null, null, null, null, $context),
            new pix_icon('t/preview', get_string('preview'))));
    $variantmatched = true;
} else if (empty($question->deployedseeds)) {
    echo html_writer::tag('p', stack_string('questionnotdeployedyet').' '.
            $OUTPUT->action_icon(question_preview_url($questionid, null, null, null, null, $context),
                new pix_icon('t/preview', get_string('preview'))));
} else {

    $notestable = new html_table();
    $notestable->head = array(
        stack_string('variant'),
        stack_string('questionnote'),
    );
    $prtstable->attributes['class'] = 'generaltable stacktestsuite';

    foreach ($question->deployedseeds as $key => $deployedseed) {
        if (!is_null($question->seed) && $question->seed == $deployedseed) {
            $choice = html_writer::tag('b', $deployedseed,
                    array('title' => stack_string('currentlyselectedvariant')));;
            $variantmatched = true;
        } else {
            $choice = html_writer::link(new moodle_url($PAGE->url, array('seed' => $deployedseed)),
                    $deployedseed, array('title' => stack_string('testthisvariant')));
        }

        $choice .= ' ' . $OUTPUT->action_icon(question_preview_url($questionid, null, null, null, $key + 1, $context),
                new pix_icon('t/preview', get_string('preview')));

        if ($canedit) {
            $choice .= ' ' . $OUTPUT->action_icon(new moodle_url('/question/type/stack/deploy.php',
                        $urlparams + array('undeploy' => $deployedseed, 'sesskey' => sesskey())),
                    new pix_icon('t/delete', stack_string('undeploy')));
        }

        // Print out question notes of all deployed versions.
        $qn = question_bank::load_question($questionid);
        $qn->seed = (int) $deployedseed;
        $cn = $qn->get_context();
        $qunote = question_engine::make_questions_usage_by_activity('qtype_stack', $cn);
        $qunote->set_preferred_behaviour('adaptive');
        $slotnote = $qunote->add_question($qn, $qn->defaultmark);
        $qunote->start_question($slotnote);

        // Check if the question note has already been deployed.
        if ($qn->get_question_summary() == $question->get_question_summary()) {
            $variantdeployed = true;
        }
        $notestable->data[] = array(
            $choice,
            stack_ouput_castext($qn->get_question_summary()),
        );
    }

    echo html_writer::table($notestable);
}

if (!$variantmatched) {
    if ($canedit) {
        $deploybutton = ' ' . $OUTPUT->single_button(new moodle_url('/question/type/stack/deploy.php',
                $urlparams + array('deploy' => $question->seed)),
                stack_string('deploy'));
        if ($variantdeployed) {
            $deploybutton = stack_string('alreadydeployed');
        }
    } else {
        $deploybutton = '';
    }
    echo html_writer::tag('div', stack_string('showingundeployedvariant',
            html_writer::tag('b', $question->seed)) . $deploybutton,
            array('class' => 'undeployedvariant'));
}

if ($question->has_random_variants()) {
    echo html_writer::start_tag('form', array('method' => 'get', 'class' => 'switchtovariant',
            'action' => new moodle_url('/question/type/stack/questiontestrun.php')));
    echo html_writer::start_tag('p');
    echo html_writer::input_hidden_params($PAGE->url, array('seed'));

    echo html_writer::tag('label', stack_string('switchtovariant'), array('for' => 'seedfield'));
    echo ' ' . html_writer::empty_tag('input', array('type' => 'text', 'size' => 7,
            'id' => 'seedfield', 'name' => 'seed', 'value' => mt_rand()));
    echo ' ' . html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go')));

    echo html_writer::end_tag('p');
    echo html_writer::end_tag('form');

    // Deploy many variants.
    if ($canedit) {
        echo html_writer::start_tag('form', array('method' => 'get', 'class' => 'deploymany',
                'action' => new moodle_url('/question/type/stack/deploy.php', $urlparams)));
        echo html_writer::start_tag('p');
        echo html_writer::input_hidden_params(new moodle_url($PAGE->url, array('sesskey' => sesskey())), array('seed'));

        echo html_writer::tag('label', stack_string('deploymany'));
        echo ' ' . html_writer::empty_tag('input', array('type' => 'text', 'size' => 4,
                'id' => 'deploymanyfield', 'name' => 'deploymany', 'value' => ''));
        echo ' ' . html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go')));
        echo html_writer::tag('label', ' '.stack_string('deploymanynotes'));

        echo html_writer::end_tag('p');
        echo html_writer::end_tag('form');
    }
}

// Display the controls to add another question test.
echo $OUTPUT->heading(stack_string('questiontests'), 2);

// Display the test results.
$addlabel = stack_string('addanothertestcase', 'qtype_stack');
if (empty($testresults)) {
    echo html_writer::tag('p', stack_string('notestcasesyet'));
    $addlabel = stack_string('addatestcase', 'qtype_stack');
} else if ($allpassed) {
    echo html_writer::tag('p', stack_string('stackInstall_testsuite_pass'), array('class' => 'overallresult pass'));
} else {
    echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'), array('class' => 'overallresult fail'));
}

if ($canedit) {
    echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
            $urlparams), $addlabel, 'get');
}

foreach ($testresults as $key => $result) {
    if ($result->passed()) {
        $outcome = html_writer::tag('span', stack_string('testsuitepass'), array('class' => 'pass'));
    } else {
        $outcome = html_writer::tag('span', stack_string('testsuitefail'), array('class' => 'fail'));
    }
    echo $OUTPUT->heading(stack_string('testcasexresult',
            array('no' => $key, 'result' => $outcome)), 3);

    // Display the information about the inputs.
    $inputstable = new html_table();
    $inputstable->head = array(
            stack_string('inputname'),
            stack_string('inputexpression'),
            stack_string('inputentered'),
            stack_string('inputdisplayed'),
            stack_string('inputstatus'),
            stack_string('errors'),
    );
    $inputstable->attributes['class'] = 'generaltable stacktestsuite';

    foreach ($result->get_input_states() as $inputname => $inputstate) {
        $inputval = s($inputstate->input);
        if (false === $inputstate->input) {
            $inputval = '';
        }
        $inputstable->data[] = array(
                s($inputname),
                s($inputstate->rawinput),
                $inputval,
                stack_ouput_castext($inputstate->display),
                stack_string('inputstatusname' . $inputstate->status),
                $inputstate->errors,
        );
    }

    echo html_writer::table($inputstable);

    // Display the information about the PRTs.
    $prtstable = new html_table();
    $prtstable->head = array(
            stack_string('prtname'),
            stack_string('score'),
            stack_string('expectedscore'),
            stack_string('penalty'),
            stack_string('expectedpenalty'),
            stack_string('answernote'),
            stack_string('expectedanswernote'),
            get_string('feedback', 'question'),
            stack_string('testsuitecolpassed'),
    );
    $prtstable->attributes['class'] = 'generaltable stacktestsuite';

    $debuginfo = '';
    foreach ($result->get_prt_states() as $prtname => $state) {
        if ($state->testoutcome) {
            $prtstable->rowclasses[] = 'pass';
            $passedcol = stack_string('testsuitepass');
        } else {
            $prtstable->rowclasses[] = 'fail';
            $passedcol = stack_string('testsuitefail').$state->reason;
        }

        // Sort out excessive decimal places from the DB.
        if (is_null($state->expectedscore) || '' === $state->expectedscore) {
            $expectedscore = '';
        } else {
            $expectedscore = $state->expectedscore + 0;
        }
        if (is_null($state->expectedpenalty) || '' === $state->expectedpenalty) {
            $expectedpenalty = '';
        } else {
            $expectedpenalty = $state->expectedpenalty + 0;
        }

        $prtstable->data[] = array(
                $prtname,
                $state->score,
                $expectedscore,
                $state->penalty,
                $expectedpenalty,
                s($state->answernote),
                s($state->expectedanswernote),
                format_text($state->feedback),
                $passedcol,
        );
        if ($state->debuginfo != '') {
            $debuginfo .= "\n<h2>".$prtname."</h2>\n\n";
            $debuginfo .= $state->debuginfo;
        }
    }

    echo html_writer::table($prtstable);

    echo $debuginfo;

    if ($canedit) {
        echo html_writer::start_tag('div', array('class' => 'testcasebuttons'));
        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
                $urlparams + array('testcase' => $key)),
                stack_string('editthistestcase', 'qtype_stack'), 'get');

        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestdelete.php',
                $urlparams + array('testcase' => $key)),
                stack_string('deletethistestcase', 'qtype_stack'), 'get');
        echo html_writer::end_tag('div');
    }
}

// Display the question.
echo $OUTPUT->heading(stack_string('questionpreview'), 3);

echo html_writer::tag('p', html_writer::link($questionbanklink,
        stack_string('seethisquestioninthequestionbank')));

if ($canedit) {
    echo html_writer::tag('p',
            html_writer::link($exportquestionlink, stack_string('exportthisquestion')) .
            $OUTPUT->help_icon('exportthisquestion', 'qtype_stack'));
}

echo $quba->render_question($slot, $options);

// Display the question note.
echo $OUTPUT->heading(stack_string('questionnote'), 3);
echo html_writer::tag('p', stack_ouput_castext($question->get_question_summary()),
        array('class' => 'questionnote'));

// Display the question variables.
echo $OUTPUT->heading(stack_string('questionvariablevalues'), 3);
echo html_writer::start_tag('div', array('class' => 'questionvariables'));
$displayqvs = '';
foreach ($question->get_question_var_values() as $key => $value) {
    $displayqvs .= s($key) . ' : ' . s($value). ";\n";
}
echo  html_writer::tag('pre', $displayqvs);
echo html_writer::end_tag('div');

// Display the general feedback, aka "Worked solution".
$qa = new question_attempt($question, 0);
echo $OUTPUT->heading(stack_string('generalfeedback'), 3);
echo html_writer::tag('div', html_writer::tag('div', $renderer->general_feedback($qa),
        array('class' => 'outcome generalfeedback')), array('class' => 'que'));

// Add a link to the cas chat to facilitate editing the general feedback.
if ($question->options->get_option('simplify')) {
    $simp = 'on';
} else {
    $simp = '';
}

$questionvarsinputs = $question->questionvariables;
foreach ($question->get_correct_response() as $key => $val) {
    if (substr($key, -4, 4) !== '_val') {
        $questionvarsinputs .= "\n{$key}:{$val};";
    }
}
$chatparams = $urlparams;
$chatparams['vars'] = $questionvarsinputs;
$chatparams['simp'] = $simp;
$chatparams['cas'] = $question->generalfeedback;
// We've chosen not to send a specific seed since it is helpful
// to test the general feedback in a random context.
echo $OUTPUT->single_button(new moodle_url('/question/type/stack/caschat.php', $chatparams), stack_string('chat'));

// Finish output.
echo $OUTPUT->footer();
