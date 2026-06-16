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
 * Placeholder export page for external library exports.
 *
 * @package    qtype_stack
 * @copyright  2026 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/questionlibrary.class.php');

$questionid = required_param('questionid', PARAM_INT);
[$qversion, $questionid] = get_latest_question_version($questionid);

$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);

[$context, , $urlparams] = qtype_stack_setup_question_test_page($question);

question_require_capability_on($questiondata, 'view');
require_capability('qtype/stack:exporttoexternallibraries', $context);

if (!get_config('qtype_stack', 'nrwupload')) {
    throw new moodle_exception('nopermissions', 'error', '', 'export to NRW');
}

$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/questionexport.php', $urlparams);
$title = stack_string('exporttonrw');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

require_login();

$dashboardlink = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$previewquestionlink = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);
$editparams = $urlparams;
unset($editparams['questionid']);
unset($editparams['seed']);
$editparams['id'] = $question->id;
$editquestionlink = new moodle_url('/question/type/stack/questioneditlatest.php', $editparams);

$uploadresult = null;
if (optional_param('uploadtonrw', false, PARAM_BOOL)) {
    require_sesskey();
    $apikey = get_config('qtype_stack', 'nrwapikey');
    $uploadresult = stack_question_library::upload_nrw_question($questiondata, $apikey);
}

echo $OUTPUT->header();

$outputdata = new stdClass();
$outputdata->question = new stdClass();
$outputdata->question->name = format_string($question->name);
$outputdata->question->version = $qversion;
$outputdata->general = new stdClass();
$outputdata->general->testquestionlink = $dashboardlink->out();
$outputdata->general->previewquestionlink = $previewquestionlink->out();
$outputdata->general->editquestionlink = $editquestionlink->out();
$outputdata->general->uploadurl = $PAGE->url->out(false);
$outputdata->general->sesskey = sesskey();
$outputdata->uploadresult = $uploadresult;

echo $OUTPUT->render_from_template('qtype_stack/questionexport', $outputdata);

echo $OUTPUT->footer();
