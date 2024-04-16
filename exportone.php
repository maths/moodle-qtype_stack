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
 * Script to download the export of a single STACK question.
 *
 * TODO: Since MDL-63738 landed in Moodle 3.6, this has been a core Moodle
 * feature, so we don't really need to keep mainaining this file. We could
 * use question/exportone.php, or question/bank/exporttoxml/exportone.php,
 * as it later became, instead.
 *
 * @copyright 2015 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once(__DIR__ . '/locallib.php');

// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

// Load the necessary data.
$questiondata = question_bank::load_question_data($questionid);
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);
        // Support both Moodle 4.x and 3.x.
if (class_exists('\core_question\local\bank\question_edit_contexts')) {
    $contexts = new \core_question\local\bank\question_edit_contexts($context);
} else {
    $contexts = new question_edit_contexts($context);
}

// Check permissions.
question_require_capability_on($questiondata, 'edit');
require_sesskey();

// Initialise $PAGE.
$nexturl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$PAGE->set_url($nexturl); // Since this script always ends in a redirect.
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('admin');

require_login();

// Set up the export format.
$qformat = new qformat_xml();
$filename = question_default_export_filename($COURSE, $questiondata) .
        $qformat->export_file_extension();
$qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
$qformat->setCourse($COURSE);
$qformat->setQuestions([$questiondata]);
$qformat->setCattofile(false);
$qformat->setContexttofile(false);

// Do the export.
if (!$qformat->exportpreprocess()) {
    send_file_not_found();
}
if (!$content = $qformat->exportprocess(true)) {
    send_file_not_found();
}
send_file($content, $filename, 0, 0, true, true, $qformat->mime_type());
