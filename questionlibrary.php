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
 * This script lets the user import questions from sample questions
 *
 * @copyright  2024 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/api/util/StackQuestionLoader.php');
require_once(__DIR__ . '/stack/questionlibrary.class.php');

use api\util\StackQuestionLoader;
// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/questionlibrary.php', $urlparams);
$title = stack_string('stack_library', format_string($question->name));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
require_login();
//$q = file_get_contents('samplequestions/stacklibrary/Algebra-Refresher/1-Combinations-of-arithmetic-operations/AlgMap-1-4.xml');
//$q = file_get_contents('samplequestions/JSXGraph-behat.xml');
//$q = file_get_contents('samplequestions/plottest.xml');
$contextId = 1;
$divId = 'stack_library_display';
$PAGE->requires->js_amd_inline(
    'require(["qtype_stack/library"], '
    . 'function(library,){library.setup(' . $contextId . ',"' . $divId . '");});'
);
$cache = cache::make('qtype_stack', 'librarycache');
$files = $cache->get('library_file_list');
if (!$files) {
    $files = stack_question_library::get_file_list('samplequestions/stacklibrary/*');
    $cache->set('library_file_list', $files);
}
$outputdata = new StdClass();
$outputdata->questions = $files;
$outputdata->displayDivId = $divId;
echo $OUTPUT->render_from_template('qtype_stack/questionlibrary', $outputdata);
// Finish output.
echo $OUTPUT->footer();
