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
 * This script lets the user see what attempts have been made at this question.
 *
 * The script loops over summarise_response data from the database, and does not
 * re-generate reports.  The script is designed to let a question author improve feedback
 * and assessment by looking at what students type, easily and without going through a quiz report.
 *
 * @copyright  2020 the University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/stack/questionreport.class.php');

// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$quizcontext = optional_param('context', null, PARAM_INT);
// Load the necessary data.
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'view');
$canedit = question_has_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/questiontestreport.php', $urlparams);
$title = stack_string('basicquestionreport');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// This layout has minimal header/footer.
$PAGE->set_pagelayout('popup');

$testquestionlink = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$qurl = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);

require_login();

// Start output.
echo $OUTPUT->header();

// Get quizzes in which the course is used.
// Add data for creating quiz selection dropdown.
$quizzes = stack_question_report::get_relevant_quizzes($questionid);
$quizoutput = [];
foreach ($quizzes as $contextid => $quiz) {
    $quiz->url = new moodle_url('/question/type/stack/questiontestreport.php',
            $urlparams + ['context' => $quiz->quizcontextid]);
    $quiz->url = $quiz->url->out();
    $quiz->active = ($contextid === $quizcontext) ? true : false;
    $quizoutput[] = $quiz;
}

if ($quizcontext === null) {
    // No quiz selected so we can only show question data.
    $outputdata = new StdClass();
    $outputdata->question = stack_question_report::format_question_data($question);
} else {
    // Create analysis report.
    $report = new stack_question_report($question, $quizcontext, $quizzes[$quizcontext]->coursecontextid);
    $outputdata = $report->outputdata;
}

// Add additional page creation data.
$outputdata->quizzes = $quizoutput;
$outputdata->general = new Stdclass();
$outputdata->general->testquestionlink = $testquestionlink->out();
$outputdata->general->previewquestionlink = $qurl->out();

// Rennder report.
echo $OUTPUT->render_from_template('qtype_stack/questionreport', $outputdata);
echo $OUTPUT->footer();
