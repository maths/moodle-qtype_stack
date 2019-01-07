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
 * @copyright 2015 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

if (function_exists('yaml_parse_file')) {
    require_once(__DIR__ . '/api/libs/yaml.php');
    require_once(__DIR__ . '/api/libs/yaml_defaults.php');
    require_once(__DIR__ . '/api/libs/export.php');
}

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once(__DIR__ . '/locallib.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$exportformat = required_param('exportformat', PARAM_TEXT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
get_question_options($questiondata);
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);
$contexts = new question_edit_contexts($context);

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
$qformat->setQuestions(array($questiondata));
$qformat->setCattofile(false);
$qformat->setContexttofile(false);

// Do the export.
if (!$qformat->exportpreprocess()) {
    send_file_not_found();
}
if (!$content = $qformat->exportprocess(true)) {
    send_file_not_found();
}

if ($exportformat == 'xml') {
    // Send the xml.
    send_file($content, $filename, 0, 0, true, true, $qformat->mime_type());
}

if (!function_exists('yaml_parse_file')) {
    throw new stack_exception("You must enable YAML support to export in YAML format.");
}
// Add in the conversion to YAML.
$defaults = new qtype_stack_api_yaml_defaults(null);
// We take the _site_ defaults here, not the YAML defaults.
$settings = get_config('qtype_stack');
$defaults->moodle_settings_to_yaml_defaults($settings);

$export = new qtype_stack_api_export($content, $defaults);
$yamlstring = $export->yaml();

$rows = substr_count($yamlstring, "\n") + 3;

echo "<form method = 'post' action = 'exportone.php'>";
echo "<textarea name = 'yaml' cols = '200' rows = '$rows'>";
echo $yamlstring;
echo "</textarea>\n";
echo "</form>\n\n";
