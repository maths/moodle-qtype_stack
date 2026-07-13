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
 * This script lets the user compare the XML of two versions of a question.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/locallib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/questionxmlcompare.class.php');

require_login();

// Get the parameters from the URL.
$questionid = required_param('id', PARAM_INT);
$requestedcurrentversion = optional_param('currentversion', null, PARAM_INT);
$requestedcompareversion = optional_param('compareversion', null, PARAM_INT);
$requesteddisplay = optional_param('display', null, PARAM_ALPHA);
$requesteddiffonly = optional_param('diffonly', stack_question_xml_compare::FILTER_ALL, PARAM_BOOL);

[$latestversion, , , $versions] = get_latest_question_version($questionid);
$currentversion = stack_question_xml_compare::get_current_version($requestedcurrentversion, $versions, $latestversion);
$questionid = $versions[$currentversion];
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);
$isstackquestion = ($questiondata->qtype === 'stack');
$urlparams = [];
if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
    $cm = get_coursemodule_from_id(false, $cmid);
    require_login($cm->course, false, $cm);
    $context = context_module::instance($cmid);
    $urlparams['cmid'] = $cmid;
} else if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    require_login($courseid);
    $context = context_course::instance($courseid);
    $urlparams['courseid'] = $courseid;
} else {
    $context = $question->get_context();
    if ($context->contextlevel == CONTEXT_MODULE) {
        $urlparams['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $urlparams['courseid'] = $context->instanceid;
    } else {
        $urlparams['courseid'] = SITEID;
    }
}

// Check permissions. Raw XML compare uses the same capability as XML edit.
question_require_capability_on($questiondata, 'edit');

$compareversion = stack_question_xml_compare::get_compare_version($requestedcompareversion, $versions, $currentversion);
$displaymode = stack_question_xml_compare::get_display_mode($requesteddisplay);
$diffonly = stack_question_xml_compare::get_diff_only($requesteddiffonly);
$comparequestionid = $versions[$compareversion];
$comparequestiondata = question_bank::load_question_data($comparequestionid);
if (!$comparequestiondata) {
    throw new stack_exception('questiondoesnotexist');
}
question_require_capability_on($comparequestiondata, 'edit');

$editparams = $urlparams;
unset($editparams['questionid']);
unset($editparams['seed']);
$editparams['id'] = $question->id;
$pageparams = $editparams;
$pageparams['currentversion'] = $currentversion;
$pageparams['compareversion'] = $compareversion;
$pageparams['display'] = $displaymode;
$pageparams['diffonly'] = $diffonly ? stack_question_xml_compare::FILTER_DIFFERENCES : stack_question_xml_compare::FILTER_ALL;
if ($requestedcurrentversion === null) {
    unset($pageparams['currentversion']);
}

$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/questionxmlcompare.php', $pageparams);
$title = stack_string('comparexmltitle');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

$currentxml = stack_question_xml_compare::export_question_version_xml($questiondata);
$comparexml = stack_question_xml_compare::export_question_version_xml($comparequestiondata);
$notices = '';
if (!$currentxml || !$comparexml) {
    $notices = stack_string('xmldisplayerror');
    $currentxml = $currentxml ?: '';
    $comparexml = $comparexml ?: '';
}

$general = new stdClass();
$general->isstackquestion = $isstackquestion;
if ($isstackquestion) {
    $qtype = new qtype_stack();
    $general->testquestionlink = $qtype->get_question_test_url($question)->out();
}
$general->previewquestionlink = qbank_previewquestion\helper::question_preview_url(
    $questionid,
    null,
    null,
    null,
    null,
    $context
)->out();
$general->editquestionlink = (new moodle_url('/question/type/stack/questioneditlatest.php', $editparams))->out();
if ($isstackquestion) {
    $general->editxmllink = (new moodle_url('/question/type/stack/questionxmledit.php', $editparams))->out();
}
$general->formaction = (new moodle_url('/question/type/stack/questionxmlcompare.php'))->out(false);
$formparams = $editparams;
$formparams['display'] = $displaymode;
$formparams['diffonly'] = $pageparams['diffonly'];
$general->formparams = stack_question_xml_compare::form_params($formparams);
$questionformparams = $editparams;
unset($questionformparams['id']);
$questionformparams['display'] = $displaymode;
$questionformparams['diffonly'] = $pageparams['diffonly'];
$general->questionformparams = stack_question_xml_compare::form_params($questionformparams);
$latestparams = $pageparams;
unset($latestparams['currentversion']);
$general->latestlink = (new moodle_url('/question/type/stack/questionxmlcompare.php', $latestparams))->out();
$toggleparams = $pageparams;
$toggleparams['display'] = stack_question_xml_compare::toggle_display_mode($displaymode);
$general->displaytogglelink = (new moodle_url('/question/type/stack/questionxmlcompare.php', $toggleparams))->out();
$diffonlytoggleparams = $pageparams;
$diffonlytoggleparams['diffonly'] = stack_question_xml_compare::toggle_diff_only($diffonly);
$general->diffonlytogglelink = (new moodle_url('/question/type/stack/questionxmlcompare.php', $diffonlytoggleparams))->out();

$output = new stdClass();
$output->question = new stdClass();
$output->question->name = $question->name;
$output->question->currentversion = $currentversion;
$output->question->compareversion = $compareversion;
$output->displaymode = $displaymode;
$output->displayunified = ($displaymode === stack_question_xml_compare::DISPLAY_UNIFIED);
$output->displaysplit = ($displaymode === stack_question_xml_compare::DISPLAY_SPLIT);
$output->diffonly = $diffonly;
$output->showalllines = !$diffonly;
$output->general = $general;
$output->hasnotices = $notices !== '';
$output->notices = $notices;
$output->nootherversions = count($versions) < 2;
$output->currentversions = stack_question_xml_compare::version_options($versions, $currentversion);
$output->versions = stack_question_xml_compare::version_options($versions, $compareversion);
$output->questionoptions = stack_question_xml_compare::questions_in_category($questiondata->category, $questionid);
$output->rows = stack_question_xml_compare::diff_rows($currentxml, $comparexml, $displaymode, $diffonly);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('qtype_stack/questionxmlcompare', $output);
echo $OUTPUT->footer();
