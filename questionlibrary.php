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
 * This script lets the user import questions from the library folder.
 *
 * @package   qtype_stack
 * @copyright  2024 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/questionlibrary.class.php');
require_once(__DIR__ . '/classes/form/category_form.php');

use category_form;

if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
    $cm = get_coursemodule_from_id(false, $cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = context_module::instance($cmid);
    $coursename = $DB->get_field('course', 'fullname', ['id' => $cm->course]);
    $courseid = $cm->course;
    $urlparams['cmid'] = $cmid;
    if (strpos(optional_param('returnurl', null, PARAM_LOCALURL), 'quiz') !== false) {
        $returntext = get_string('stack_library_quiz_return', 'qtype_stack');
    } else {
        $returntext = get_string('stack_library_qb_return', 'qtype_stack');
    }
} else if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    require_login($courseid);
    $thiscontext = context_course::instance($courseid);
    $coursename = $DB->get_field('course', 'fullname', ['id' => $courseid]);
    $urlparams['courseid'] = $courseid;
    $returntext = get_string('stack_library_qb_return', 'qtype_stack');
}

// Check user has add capability for the required context.
require_capability('moodle/question:add', $thiscontext);

$contexts = new core_question\local\bank\question_edit_contexts($thiscontext);
$contexts = $contexts->having_cap('moodle/question:add');

// Initialise $PAGE.
$PAGE->set_context($thiscontext);
$PAGE->set_url('/question/type/stack/questionlibrary.php', $urlparams);
$title = stack_string('stack_library');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();

if ($backurl = optional_param('returnurl', null, PARAM_LOCALURL)) {
    $returnlink = new moodle_url($backurl);
} else {
    $returnlink = new moodle_url('/question/edit.php', $urlparams);
    $returntext = get_string('stack_library_qb_return', 'qtype_stack');
}

$dashboardlink = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$quizlink = new moodle_url('/mod/quiz/view.php');

$PAGE->requires->js_amd_inline(
    'require(["qtype_stack/library"], '
    . 'function(library,){library.setup();});'
);

// Get list of files.
$cache = cache::make('qtype_stack', 'librarycache');

// Make sure we're only listing contents of STACK library or site library.
$location = optional_param('location', '', PARAM_RAW);
$cacheid = 'library_file_list';
$libraryname = stack_string('stack_library');
if (str_starts_with($location, 'sitelibrary')) {
    $libraryname = explode('/', $location)[1];
    $cacheid = 'sitelibrary_' . $libraryname . '_file_list';
    $location = "{$CFG->dataroot}/stack/{$location}";
    if (!str_starts_with(realpath($location), "{$CFG->dataroot}/stack/sitelibrary")) {
        $location = __DIR__ . '/samplequestions/stacklibrary/*';
        $libraryname = stack_string('stack_library');
        $cacheid = 'library_file_list';
    } else {
        $location .= '/*';
    }
} else {
    $location = __DIR__ . '/samplequestions/stacklibrary/*';
}

$files = $cache->get($cacheid);
if (!$files) {
    $files = stack_question_library::get_file_list($location);
    $cache->set($cacheid, $files);
}

$mform = new category_form(null, ['qcontext' => $contexts]);
// Prepare data for template.
$outputdata = new StdClass();
$outputdata->returnlink = $returnlink->out();
$outputdata->dashboardlink = $dashboardlink->out();
$outputdata->quizlink = $quizlink->out();
$outputdata->returntext = $returntext;
$outputdata->files = $files->children;
$outputdata->category = $mform->render();
$outputdata->coursename = $coursename;
$outputdata->courseid = $courseid;
$outputdata->libraries = new StdClass();
$outputdata->libraries->items = [];
$outputdata->libraries->hasitems = false;

$libraries = glob("{$CFG->dataroot}/stack/sitelibrary/*");
if ($libraries) {
    $libentry = new StdClass();
    $libentry->name = stack_string('stack_library');
    $urlparams['location'] = "/samplequestions/stacklibrary";
    $libentry->url = new moodle_url('/question/type/stack/questionlibrary.php', $urlparams);
    $libentry->url = $libentry->url->out();
    $libentry->active = ($libentry->name === $libraryname) ? true : false;
    $outputdata->libraries->items[] = $libentry;
    $outputdata->libraries->hasitems = true;
}
foreach ($libraries as $library) {
    $libentry = new StdClass();
    $parts = explode('/', $library);
    $libentry->name = end($parts);
    $urlparams['location'] = "sitelibrary/{$libentry->name}";
    $libentry->url = new moodle_url('/question/type/stack/questionlibrary.php', $urlparams);
    $libentry->url = $libentry->url->out();
    $libentry->active = ($libentry->name === $libraryname) ? true : false;
    $outputdata->libraries->items[] = $libentry;
}

echo $OUTPUT->render_from_template('qtype_stack/questionlibrary', $outputdata);

// Finish output.
echo $OUTPUT->footer();
