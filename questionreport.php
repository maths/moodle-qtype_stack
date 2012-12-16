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
 * This script reports detailed analysis of attempts at a particular question.
 *
 * @copyright  2012 the University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');

require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
require_once($CFG->dirroot . '/mod/quiz/report/default.php');

require_once($CFG->dirroot . '/question/type/stack/stack/report.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$quizid = required_param('quizid', PARAM_INT);

//$questionid = 93;
//$quizid = 5;

if (!$question = $DB->get_record('question', array('id' => $questionid))) {
    print_error('invalidquestionid', 'quiz');
}
get_question_options($question, true);

if (!$quiz = $DB->get_record('quiz', array('id' => $quizid))) {
    print_error('invalidquizid', 'quiz');
}
if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
    print_error('invalidcourseid');
}
if (!$cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course, false, $cm);
$context = context_module::instance($cm->id);

// Initialise $PAGE.
$urlparams = array();
$PAGE->set_url('/question/type/stack/questionreport.php', $urlparams);
$title = stack_string('questionreporting');
$PAGE->set_title($title);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('report');

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$report = new quiz_stack_report();
$report->add_questionid($questionid);
$report->display($quiz, $cm, $course);



// Finish output.
echo $OUTPUT->footer();
