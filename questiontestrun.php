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
 * @package    qtype_stack
 * @copyright  2012 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/questiontest.php');
require_once(__DIR__ . '/stack/bulktester.class.php');
require_once(__DIR__ . '/stack/questiondashboard.class.php');

use stack_question_dashboard;

// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);

[$qversion, $questionid] = get_latest_question_version($questionid);

// Load the necessary data.
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
[$context, $seed, $urlparams] = qtype_stack_setup_question_test_page($question);
unset($urlparams['sesskey']);
unset($urlparams['defaulttestcase']);
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

$dashboard = new stack_question_dashboard($question, $seed, $context);

// Create some other useful links.
$qbankparams = $urlparams;
unset($qbankparams['questionid']);
unset($qbankparams['seed']);
unset($qbankparams['deploy']);
unset($qbankparams['undeploy']);
unset($qbankparams['undeployall']);
unset($qbankparams['testall']);

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
$exportparams = $urlparams;
$exportparams['id'] = $question->id;
$sesskey = sesskey();

$questionbanklinkedit = new moodle_url('/question/type/stack/questioneditlatest.php', $editparams);
$questionxmllink = new moodle_url('/question/type/stack/questionxmledit.php', $editparams);
$questionbanklink = new moodle_url('/question/edit.php', $qbankparams);
$exportquestionlink = new moodle_url('/question/bank/exporttoxml/exportone.php', $exportparams);
$exportquestionlink->param('sesskey', $sesskey);
$todolink = new moodle_url('/question/type/stack/adminui/todo.php', $todoparams);
$reportlink = new moodle_url('/question/type/stack/questiontestreport.php', $urlparams);
$bulktestlink = new moodle_url('/question/type/stack/questionbulktest.php', $urlparams);

// We've chosen not to send a specific seed since it is helpful to test the general feedback in a random context.
$chatparams = $urlparams;
$chatparams['initialise'] = true;
$chatlink = new moodle_url('/question/type/stack/adminui/caschat.php', $chatparams);


// Start output.
echo $OUTPUT->header();
$initialdata = $dashboard->initial_load();
$initialdata->general = new Stdclass();
if (optional_param('defaulttestcase', null, PARAM_INT) && $canedit && $question->inputs !== []) {
    $dashboard->create_default_test();
    $initialdata->general->testcreated = true;
} else {
    $initialdata->general->testcreated = false;
}

$initialdata->question->version = $qversion;
$initialdata->general->editquestionlink = $questionbanklinkedit->out();
$initialdata->general->editxmllink = $questionxmllink->out();
$initialdata->general->questionbanklink = $questionbanklink->out();
$initialdata->general->chatlink = $chatlink->out();
$initialdata->general->tidylink = $question->qtype->get_tidy_question_url($question);
$initialdata->general->exportquestionlink = $exportquestionlink->out();
$initialdata->general->reportlink = $reportlink->out();
$initialdata->general->todolink = $todolink->out();
$initialdata->general->bulktestlink = $bulktestlink->out();
$initialdata->general->canedit = $canedit;
$dashboard->create_progress_bar();
echo $OUTPUT->render_from_template('qtype_stack/questiontestrun', $initialdata);
flush();
$testeditlink = new moodle_url('/question/type/stack/questiontestedit.php', $urlparams);
$initialdata->tests->testeditlink = $testeditlink->out();
foreach($initialdata->tests->results as $key => $result) {
    $test = new StdClass();
    $test->output = $result->html_output($question, $key);
    $testeditlink = new moodle_url('/question/type/stack/questiontestedit.php', array_merge($urlparams, ['testcase' => $key]));
    $testconfirmlink = new moodle_url('/question/type/stack/questiontestedit.php', array_merge($urlparams, ['testcase' => $key, 'confirmthistestcase' => true]));
    $testdeletelink = new moodle_url('/question/type/stack/questiontestdelete.php', array_merge($urlparams, ['testcase' => $key]));
    $test->editlink = $testeditlink->out();
    $test->confirmlink = $testconfirmlink->out();
    $test->deletelink = $testdeletelink->out();
    $test->canedit = $canedit;
    echo $OUTPUT->render_from_template('qtype_stack/questiontestruntest', $test);
    flush();
}
$variantdata = $dashboard->list_variants();
$variantdata->deployfeedback = optional_param('deployfeedback', null, PARAM_TEXT);
$variantdata->deployfeedbackerr = optional_param('deployfeedbackerr', null, PARAM_TEXT);
if ($variantdata->deployfeedback || $variantdata->deployfeedbackerr) {
    $PAGE->set_url('/question/type/stack/questiontestrun.php#variants-pane', $urlparams);
}
$variantdata->canedit = $canedit;
foreach($variantdata->notes as $variant) {
    $variant->canedit = $canedit;
    $vdeletelink = new moodle_url(
        '/question/type/stack/deploy.php',
        $urlparams + ['undeploy' => $seed, 'sesskey' => $sesskey],
        'variants-pane'
    );
    $variant->deletelink = $vdeletelink->out();
}
$deploysinglelink = new moodle_url(
    '/question/type/stack/deploy.php',
    $urlparams + ['deploy' => $seed, 'sesskey' => $sesskey],
    'variants-pane'
);
$variantdata->deploysinglelink = $deploysinglelink->out();
$deploylink = new moodle_url(
    '/question/type/stack/deploy.php',
    [],
    'variants-pane'
);
$variantdata->deploylink = $deploylink->out();
$undeployalllink = new moodle_url(
    '/question/type/stack/deploy.php',
    $urlparams + ['undeployall' => true, 'sesskey' => $sesskey],
    'variants-pane'
);
$variantdata->undeployalllink = $undeployalllink->out();

$pageparams = $urlparams;
unset($pageparams['seed']);
$pagelink = new moodle_url('/question/type/stack/questiontestrun.php', [], 'variants-pane');
$variantdata->pagelink = $pagelink->out();
$variantdata->courseid = $courseid;
$variantdata->questionid = $questionid;
$variantdata->sesskey = $sesskey;
$variantdata->newseed = mt_rand();
$variantdata->deployedseeds = $question->deployedseeds;
$variantdata->deploylistrows = min(count($question->deployedseeds), 5);
$testalllink = new moodle_url(
        '/question/type/stack/questiontestrun.php',
        $urlparams + ['testall' => '1', 'sesskey' => $sesskey],
        'variants-pane'
    );
$variantdata->testalllink = $testalllink->out();
echo $OUTPUT->render_from_template('qtype_stack/questiontestrunvariants', $variantdata);

echo $OUTPUT->footer();
