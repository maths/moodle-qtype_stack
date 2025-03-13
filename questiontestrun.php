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
 * This script lets the user test a question using any question tests defined
 * in the database. It also displays some of the internal workins of questions.
 *
 * Users with moodle/question:view capability can use this script to view the
 * results of the tests.
 *
 * Users with moodle/question:edit can edit the test cases and deployed variant,
 * as well as just run them.
 *
 * The script takes one parameter id which is a questionid as a parameter.
 * In can optionally also take a random seed.
 *
 * @copyright  2012 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/questiontest.php');
require_once(__DIR__ . '/stack/bulktester.class.php');

// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

$qversion = null;

// We should always run tests on the latest version of the question.
// This means we can refresh/reload the page even if the question has been edited and saved in another window.
// When we click "edit question" button we automatically jump to the last version, and don't edit this version.
$query = 'SELECT qv.questionid, qv.version FROM {question_versions} qv
                JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                WHERE qbe.id = (SELECT be.id FROM {question_bank_entries} be
                                JOIN {question_versions} v ON v.questionbankentryid = be.id
                                WHERE v.questionid = ' . $questionid . ')
            ORDER BY qv.questionid';
global $DB;
$result = $DB->get_records_sql($query);
$result = end($result);
$qversion = $result->version;
$questionid = $result->questionid;

// Load the necessary data.
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);
// We hard-wire decimals to be a full stop when testing questions.
$question->options->set_option('decimals', '.');

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

require_login();

// Create some other useful links.
$qbankparams = $urlparams;
unset($qbankparams['questionid']);
unset($qbankparams['seed']);
$editparams = $qbankparams;
$editparams['id'] = $question->id;
$qbankparams['qperpage'] = 1000; // Should match MAXIMUM_QUESTIONS_PER_PAGE but that constant is not easily accessible.
$qbankparams['category'] = $questiondata->category . ',' . $question->contextid;
$qbankparams['lastchanged'] = $question->id;
if (property_exists($questiondata, 'hidden') && $questiondata->hidden) {
    $qbankparams['showhidden'] = 1;
}
$todoparams = $qbankparams;
$todoparams['contextid'] = $question->contextid;

$questionbanklinkedit = new moodle_url('/question/bank/editquestion/question.php', $editparams);
$questionbanklink = new moodle_url('/question/edit.php', $qbankparams);
$exportquestionlink = new moodle_url('/question/type/stack/exportone.php', $urlparams);
$exportquestionlink->param('sesskey', sesskey());
$todolink = new moodle_url('/question/type/stack/adminui/todo.php', $todoparams);

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
question_engine::save_questions_usage_by_activity($quba);

// Prepare the display options.
$options = question_display_options();
// Start output.
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('qtype_stack');
echo $OUTPUT->heading($question->name, 2);
if ($qversion !== null) {
    echo html_writer::tag('p', stack_string('version') . ' ' . $qversion);
}

// We've chosen not to send a specific seed since it is helpful to test the general feedback in a random context.
$chatparams = $urlparams;
// ISS-1110 Rather than send parts of the question, save the quba and
// supply the qubaid and slot so the details can be loaded on the caschat page.
// This avoids a long URI causing an Apache error.
$chatparams['initialise'] = true;
$chatparams['qubaid'] = $quba->get_id();
$chatparams['slot'] = $slot;
$chatlink = new moodle_url('/question/type/stack/adminui/caschat.php', $chatparams);

$links = [];
if ($canedit) {
    $links[] = html_writer::link($questionbanklinkedit, stack_string('editquestioninthequestionbank'),
        ['class' => 'nav-link']);
}
$links[] = html_writer::link($questionbanklink, stack_string('seethisquestioninthequestionbank'),
    ['class' => 'nav-link']);
if ($canedit) {
    $links[] = html_writer::link($chatlink, stack_string('sendgeneralfeedback'), ['class' => 'nav-link']);
    $links[] = html_writer::link($question->qtype->get_tidy_question_url($question),
        stack_string('tidyquestion'), ['class' => 'nav-link']);
    $links[] = html_writer::link($exportquestionlink, stack_string('exportthisquestion'), ['class' => 'nav-link']);
}
$links[] = html_writer::link(new moodle_url('/question/type/stack/questiontestreport.php', $urlparams),
    stack_string('basicquestionreport'), ['class' => 'nav-link']);
$links[] = html_writer::link($todolink, stack_string('seetodolist'),
    ['class' => 'nav-link']);
echo html_writer::tag('nav', implode(' ', $links), ['class' => 'nav']);

flush();

$question->castextprocessor = new castext2_qa_processor($quba->get_question_attempt($slot));
$generalfeedback = $question->get_generalfeedback_castext();
$rendergeneralfeedback = $renderer->general_feedback($quba->get_question_attempt($slot));
$generalfeedbackerr = $generalfeedback->get_errors();

$questiondescription = $question->get_questiondescription_castext();
$renderquestiondescription = $renderer->question_description($quba->get_question_attempt($slot));
$questiondescription = $questiondescription->get_errors();

// Store a rendered version of the blank question here.
// Runtime errors generated by test cases might change rendering later.
$renderquestion = $quba->render_question($slot, $options);
// Make sure the seed is available for later use.
$seed = $question->seed;
$questionvariablevalues = $question->get_question_session_keyval_representation();

// Load the list of test cases.
$testscases = question_bank::get_qtype('stack')->load_question_tests($question->id);
// Create the default test case.
$defaulttest = null;
$defaulttestresult = null;

if (optional_param('defaulttestcase', null, PARAM_INT) && $canedit && $question->inputs !== []) {
    $defaulttest = stack_bulk_tester::create_default_test($question);
    question_bank::get_qtype('stack')->save_question_test($questionid, $defaulttest);
    $testscases = question_bank::get_qtype('stack')->load_question_tests($question->id);

    echo html_writer::tag('p', stack_string_error('runquestiontests_auto'));
}
// Prompt user to create the default test case.
if (empty($testscases) && $canedit && $question->inputs !== []) {
    // Add in a default test case and give it full marks.
    echo html_writer::start_tag('form', [
        'method' => 'get', 'class' => 'defaulttestcase',
        'action' => new moodle_url('/question/type/stack/questiontestrun.php', $urlparams),
    ]);
    echo html_writer::input_hidden_params(new moodle_url($PAGE->url,
        ['sesskey' => sesskey(), 'defaulttestcase' => 1]));
    echo ' ' . html_writer::empty_tag('input', [
        'type' => 'submit', 'class' => 'btn btn-danger',
        'value' => stack_string('runquestiontests_autoprompt'),
    ]);
    echo html_writer::end_tag('form');
}

if (empty($testscases) && $question->inputs !== []) {
    echo "\n<hr/>\n";
    $defaulttest = stack_bulk_tester::create_default_test($question);
    $defaulttestresult = $defaulttest->test_question($questionid, $seed, $context);
    echo stack_string('runquestiontests_explanation');
    echo $defaulttestresult->html_output($question, stack_string('runquestiontests_example'));
    echo "\n<hr/>\n";
}

$deployfeedback = optional_param('deployfeedback', null, PARAM_TEXT);
if (!is_null($deployfeedback)) {
    echo html_writer::tag('p', $deployfeedback, ['class' => 'overallresult pass']);
}
$deployfeedbackerr = optional_param('deployfeedbackerr', null, PARAM_TEXT);
if (!is_null($deployfeedbackerr)) {
    echo html_writer::tag('p', $deployfeedbackerr, ['class' => 'overallresult fail']);
}

$upgradeerrors = $question->validate_against_stackversion($context);
if ($upgradeerrors != '') {
    echo html_writer::tag('p', $upgradeerrors, ['class' => 'fail']);
}

// Display the list of deployed variants, with UI to edit the list.
if ($question->deployedseeds) {
    echo $OUTPUT->heading(stack_string('deployedvariantsn', count($question->deployedseeds)), 3);
} else {
    echo $OUTPUT->heading(stack_string('deployedvariants'), 3);
}

$variantmatched = false;
$variantdeployed = false;
$questionnotes = [];

$qurl = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);

if (!$question->has_random_variants()) {
    echo "\n";
    echo html_writer::tag('p', stack_string('questiondoesnotuserandomisation') . ' ' .
        $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))));
    $variantmatched = true;
}

if (empty($question->deployedseeds)) {
    if ($question->has_random_variants()) {
        echo html_writer::tag('p', stack_string_error('runquestiontests_alert') . ' ' .
                stack_string('questionnotdeployedyet') . ' ' .
                $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))));
    }
} else {

    $notestable = new html_table();
    $notestable->head = [
        stack_string('variant'),
        stack_string('questionnote'),
        ' ',
        ' ',
    ];
    $notestable->attributes['class'] = 'generaltable stacktestsuite';

    $a = ['total' => count($question->deployedseeds), 'done' => 0];
    $progressevery = (int) min(max(1, count($question->deployedseeds) / 500), 100);
    $pbar = new progress_bar('testingquestionvariants', 500, true);

    foreach ($question->deployedseeds as $key => $deployedseed) {
        if (!is_null($question->seed) && $question->seed == $deployedseed) {
            $choice = html_writer::tag('b', $deployedseed,
                    ['title' => stack_string('currentlyselectedvariant')]);;
            $variantmatched = true;
        } else {
            $choice = html_writer::link(new moodle_url($PAGE->url, ['seed' => $deployedseed]),
                    $deployedseed, ['title' => stack_string('testthisvariant')]);
        }

        $qurl = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, $key + 1, $context);

        $choice .= ' ' . $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview')));

        if ($canedit) {
            $choice .= ' ' . $OUTPUT->action_icon(new moodle_url('/question/type/stack/deploy.php',
                        $urlparams + ['undeploy' => $deployedseed, 'sesskey' => sesskey()]),
                    new pix_icon('t/delete', stack_string('undeploy')));
        }

        $bulktestresults = [false, ''];
        if (optional_param('testall', null, PARAM_INT)) {
            // Bulk test all variants.
            $bulktester = new stack_bulk_tester();
            $bulktestresults = $bulktester->qtype_stack_test_question($context, $questionid,
                    $testscases, 'web', $deployedseed, true);
        }

        // Print out question notes of all deployed variants.
        $qn = question_bank::load_question($questionid);
        $qn->seed = (int) $deployedseed;
        $cn = $qn->get_context();
        $qunote = question_engine::make_questions_usage_by_activity('qtype_stack', $cn);
        $qunote->set_preferred_behaviour('adaptive');
        $slotnote = $qunote->add_question($qn, $qn->defaultmark);
        $qunote->start_question($slotnote);
        // Check for duplicate question notes.
        $questionnotes[] = $qn->get_question_summary();

        // Check if the question note has already been deployed.
        if ($qn->get_question_summary() == $question->get_question_summary()) {
            $variantdeployed = true;
        }

        $icon = '';
        if ($bulktestresults[0]) {
            $icon = $OUTPUT->pix_icon('t/check', stack_string('questiontestspass'));
        }
        $notestable->data[] = [
            $choice,
            stack_ouput_castext($qn->get_question_summary()),
            $icon,
            $bulktestresults[1],
        ];

        $a['done'] += 1;
        if ($a['done'] % $progressevery == 0 || $a['done'] == $a['total']) {
            core_php_time_limit::raise(60);
            $pbar->update($a['done'], $a['total'], get_string('testingquestionvariants', 'qtype_stack', $a));
        }
    }

    function sort_by_note($a1, $b1) {
        $a = $a1['1'];
        $b = $b1['1'];
        if ($a == $b) {
            return 0;
        }
        if ($a < $b) {
            return -1;
        }
        return 1;
    }
    usort($notestable->data, 'sort_by_note');

    if (count($questionnotes) != count(array_flip($questionnotes))) {
        echo "\n";
        echo html_writer::tag('p', stack_string_error('deployduplicateerror'));
        echo "\n";
    }
    echo html_writer::table($notestable);
    echo "\n";
}
flush();

if (!$variantmatched) {
    if ($canedit) {
        $deploybutton = ' ' . $OUTPUT->single_button(new moodle_url('/question/type/stack/deploy.php',
                $urlparams + ['deploy' => $question->seed]),
                stack_string('deploy'));
        if ($variantdeployed) {
            $deploybutton = stack_string('alreadydeployed');
        }
    } else {
        $deploybutton = '';
    }
    echo html_writer::tag('div', stack_string('showingundeployedvariant',
            html_writer::tag('b', $question->seed)) . $deploybutton,
            ['class' => 'undeployedvariant']);
    echo "\n";
}

if (!(empty($question->deployedseeds)) && $canedit) {
    // Undeploy all the variants.
    echo html_writer::start_tag('form', [
        'method' => 'get', 'class' => 'deploymany',
        'action' => new moodle_url('/question/type/stack/deploy.php', $urlparams),
    ]);
    echo html_writer::input_hidden_params(new moodle_url($PAGE->url, [
        'sesskey' => sesskey(),
        'undeployall' => 'true',
    ]));
    echo ' ' . html_writer::empty_tag('input', [
        'type' => 'submit', 'class' => 'btn btn-danger',
        'value' => stack_string('deployremoveall'),
    ]);
    echo html_writer::end_tag('form');
}

// Add in some logic for a case where the author removes randomization after variants have been deployed.
if ($question->has_random_variants()) {
    echo "\n";
    echo html_writer::start_tag('p');
    echo html_writer::start_tag('form', [
        'method' => 'get', 'class' => 'switchtovariant',
        'action' => new moodle_url('/question/type/stack/questiontestrun.php'),
    ]);
    echo html_writer::input_hidden_params($PAGE->url, ['seed']);

    echo ' ' . html_writer::empty_tag('input', [
        'type' => 'submit', 'class' => 'btn btn-secondary',
        'value' => stack_string('switchtovariant'),
    ]);
    echo ' ' . html_writer::empty_tag('input', [
        'type' => 'text', 'size' => 7,
        'id' => 'seedfield', 'name' => 'seed', 'value' => mt_rand(),
    ]);
    echo html_writer::end_tag('form');

    if ($canedit) {
        // Deploy many variants.
        echo html_writer::start_tag('form', [
            'method' => 'get', 'class' => 'deploymany',
            'action' => new moodle_url('/question/type/stack/deploy.php', $urlparams),
        ]);
        echo html_writer::input_hidden_params(new moodle_url($PAGE->url, ['sesskey' => sesskey()]), ['seed']);
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'submit', 'class' => 'btn btn-secondary',
            'value' => stack_string('deploymanybtn'),
        ]);
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'text', 'size' => 4,
            'id' => 'deploymanyfield', 'name' => 'deploymany', 'value' => '',
        ]);
        echo ' ' . stack_string('deploymanynotes');
        echo html_writer::end_tag('form');

        // Systematic deployment of variants (from 1 to ...).
        echo html_writer::start_tag('form', [
            'method' => 'get', 'class' => 'deploysystematic',
            'action' => new moodle_url('/question/type/stack/deploy.php', $urlparams),
        ]);
        echo html_writer::input_hidden_params(new moodle_url($PAGE->url, ['sesskey' => sesskey()]), ['seed']);
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'submit', 'class' => 'btn btn-secondary',
            'value' => stack_string('deploysystematicbtn'),
        ]);
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'text', 'size' => 3,
            'id' => 'deploysystematicfield', 'name' => 'deploysystematic', 'value' => '',
        ]);
        echo html_writer::end_tag('form');

        // Systematic deployment of variants (from ... to ...).
        echo html_writer::start_tag('form', [
            'method' => 'get', 'class' => 'deploysystematicfromto',
            'action' => new moodle_url('/question/type/stack/deploy.php', $urlparams),
        ]);
        echo html_writer::input_hidden_params(new moodle_url($PAGE->url, ['sesskey' => sesskey()]), ['seed']);
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'submit', 'class' => 'btn btn-secondary',
            'value' => stack_string('deploysystematicfrombtn'),
        ]);
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'text', 'size' => 3,
            'id' => 'deploysystematicfromfield', 'name' => 'deploysystematicfrom', 'value' => '',
        ]);
        echo ' ' . stack_string('deploysystematicto');
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'text', 'size' => 3,
            'id' => 'deploysystematictofield', 'name' => 'deploysystematicto', 'value' => '',
        ]);
        echo html_writer::end_tag('form');

        // Deploy many from a CS list of integer seeds.
        echo "\n" . html_writer::start_tag('form', [
            'method' => 'get', 'class' => 'deployfromlist',
            'action' => new moodle_url('/question/type/stack/deploy.php', $urlparams),
        ]);
        echo html_writer::input_hidden_params(new moodle_url($PAGE->url, ['sesskey' => sesskey()]), ['seed']);
        echo "\n" . html_writer::start_tag('table');
        echo html_writer::start_tag('tr');
        echo html_writer::start_tag('td');
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'submit', 'class' => 'btn btn-secondary',
            'value' => stack_string('deployfromlistbtn'),
        ]);
        echo html_writer::end_tag('td');
        echo html_writer::start_tag('td');
        echo ' ' . html_writer::start_tag('textarea', [
            'cols' => 15, 'rows' => min(count($question->deployedseeds), 5),
            'id' => 'deployfromlist', 'name' => 'deployfromlist',
        ]);
        echo html_writer::end_tag('textarea');
        echo html_writer::end_tag('td');
        echo html_writer::start_tag('td');
        echo stack_string('deployfromlist');
        echo html_writer::end_tag('td');
        $out = html_writer::tag('summary', stack_string('deployfromlistexisting'));
        $out .= html_writer::tag('pre', implode("\n", $question->deployedseeds));
        $out = html_writer::tag('details', $out);
        echo html_writer::tag('td', $out);
        echo html_writer::end_tag('tr');
        echo "\n" . html_writer::end_tag('table');
        echo "\n" . html_writer::end_tag('form');

        // Run tests on all the variants.
        echo html_writer::start_tag('form', [
            'method' => 'get', 'class' => 'deploymany',
            'action' => new moodle_url('/question/type/stack/questiontestrun.php', $urlparams),
        ]);
        echo html_writer::input_hidden_params(new moodle_url($PAGE->url, [
            'sesskey' => sesskey(),
            'testall' => '1',
        ]));
        echo ' ' . html_writer::empty_tag('input', [
            'type' => 'submit', 'class' => 'btn btn-warning',
            'value' => stack_string('deploytestall'),
        ]);
        echo html_writer::end_tag('form');
        echo "\n";
    }
}

// Execute the tests.
$testresults = [];
$allpassed = true;
foreach ($testscases as $key => $testcase) {
    $testresults[$key] = $testcase->test_question($questionid, $seed, $context);
    if (!$testresults[$key]->passed()) {
        $allpassed = false;
    }
}

\core\session\manager::write_close();

if ($question->runtimeerrors || $generalfeedbackerr) {
    echo html_writer::tag('p', stack_string('errors'), ['class' => 'overallresult fail']);
    echo html_writer::tag('p', implode('<br />', array_keys($question->runtimeerrors)));
    echo html_writer::tag('p', stack_string('generalfeedback') . ': ' . $generalfeedbackerr);
}

// Make sure the question has inputs, otherwise testing is uncessary.
if ($question->inputs !== []) {
    echo $OUTPUT->heading(stack_string('questiontestsfor', $seed), 2);

    // Display the test results.
    $addlabel = stack_string('addanothertestcase', 'qtype_stack');
    $basemsg = '';
    if ($question->has_random_variants()) {
        $basemsg = stack_string('questiontestsfor', $seed) . ': ';
    }

    if (empty($testresults)) {
        echo html_writer::tag('p', stack_string_error('runquestiontests_alert') . ' ' . stack_string('notestcasesyet'));
        $addlabel = stack_string('addatestcase', 'qtype_stack');
    } else if ($allpassed) {
        echo html_writer::tag('p', $basemsg .
            stack_string('stackInstall_testsuite_pass'), ['class' => 'overallresult pass']);
    } else {
        echo html_writer::tag('p', $basemsg .
            stack_string_error('stackInstall_testsuite_fail'), ['class' => 'overallresult fail']);
    }

    if ($canedit) {
        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
                $urlparams), $addlabel, 'get');
    }
}

foreach ($testresults as $key => $result) {

    echo $result->html_output($question, $key);
    flush(); // Force output to prevent timeouts and to make progress clear.

    if ($canedit) {
        echo "\n";
        echo html_writer::start_tag('div', ['class' => 'testcasebuttons']);
        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
                $urlparams + ['testcase' => $key]),
                stack_string('editthistestcase', 'qtype_stack'), 'get');

        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestedit.php',
            $urlparams + ['testcase' => $key, 'confirmthistestcase' => true]),
            stack_string('confirmthistestcase', 'qtype_stack'), 'get');

        echo $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestdelete.php',
                $urlparams + ['testcase' => $key]),
                stack_string('deletethistestcase', 'qtype_stack'), 'get');
        echo html_writer::end_tag('div');
        echo "\n";
    }
}

// Display the question variables.
echo $OUTPUT->heading(stack_string('questionvariablevalues'), 3);
echo "\n";
echo html_writer::start_tag('div', ['class' => 'questionvariables']);
echo html_writer::tag('pre', $questionvariablevalues);
echo html_writer::end_tag('div');
echo "\n";

// Question variables and PRTs in a summary tag.
$out = html_writer::tag('summary', stack_string('prts'));
$out .= html_writer::start_tag('div', ['class' => 'questionvariables']);
$out .= html_writer::tag('pre', $questionvariablevalues);
$out .= html_writer::end_tag('div');
// Display a representation of the PRT for offline use.
$offlinemaxima = [];
foreach ($question->prts as $name => $prt) {
    $offlinemaxima[] = $prt->get_maxima_representation();
}
$offlinemaxima = s(implode("\n", $offlinemaxima));
$out .= html_writer::start_tag('div', ['class' => 'questionvariables']);
$out .= html_writer::tag('pre', $offlinemaxima);
$out .= html_writer::end_tag('div');
echo html_writer::tag('details', $out);
echo "\n";

echo $OUTPUT->heading(stack_string('questionpreview'), 3);
echo "\n";
echo $renderquestion;
echo "\n";

// Display the question note.
echo $OUTPUT->heading(stack_string('questionnote'), 3);
echo html_writer::tag('div', html_writer::tag('div', stack_ouput_castext($question->get_question_summary()),
    ['class' => 'questionnote']), ['class' => 'que']);

// Display the general feedback, aka "Worked solution".
echo $OUTPUT->heading(stack_string('generalfeedback'), 3);
echo html_writer::tag('div', html_writer::tag('div', $rendergeneralfeedback,
    ['class' => 'outcome generalfeedback']), ['class' => 'que']);

echo $OUTPUT->heading(stack_string('questiondescription'), 3);
echo html_writer::tag('div', html_writer::tag('div', $renderquestiondescription,
    ['class' => 'outcome generalfeedback']), ['class' => 'que']);

echo "\n";
if ($question->stackversion == null) {
    echo html_writer::tag('p', stack_string('stackversionnone'));
} else {
    echo html_writer::tag('p', stack_string('stackversionedited', $question->stackversion)
            . stack_string('stackversionnow', get_config('qtype_stack', 'version')));
}

// Finish output.
echo $OUTPUT->footer();
