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
require_once(__DIR__ . '/stack/questionxmlcompare.class.php');

require_login();

// Get the parameters from the URL.
$questionid = required_param('id', PARAM_INT);
$requestedcompareversion = optional_param('compareversion', null, PARAM_INT);

[$qversion, $questionid, , $versions] = get_latest_question_version($questionid);
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);
[$context, , $urlparams] = qtype_stack_setup_question_test_page($question);

// Check permissions. Raw XML compare uses the same capability as XML edit.
question_require_capability_on($questiondata, 'edit');

$compareversion = stack_question_xml_compare::get_compare_version($requestedcompareversion, $versions, $qversion);
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
$pageparams['compareversion'] = $compareversion;

$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/questionxmlcompare.php', $pageparams);
$title = stack_string('comparexmltitle');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$currentxml = stack_question_xml_compare::export_question_version_xml($questiondata);
$comparexml = stack_question_xml_compare::export_question_version_xml($comparequestiondata);
$notices = '';
if (!$currentxml || !$comparexml) {
    $notices = stack_string('xmldisplayerror');
    $currentxml = $currentxml ?: '';
    $comparexml = $comparexml ?: '';
}

$qtype = new qtype_stack();
$general = new stdClass();
$general->testquestionlink = $qtype->get_question_test_url($question)->out();
$general->previewquestionlink = qbank_previewquestion\helper::question_preview_url(
    $questionid,
    null,
    null,
    null,
    null,
    $context
)->out();
$general->editquestionlink = (new moodle_url('/question/type/stack/questioneditlatest.php', $editparams))->out();
$general->editxmllink = (new moodle_url('/question/type/stack/questionxmledit.php', $editparams))->out();
$general->formaction = (new moodle_url('/question/type/stack/questionxmlcompare.php'))->out(false);
$general->formparams = stack_question_xml_compare::form_params($editparams);
$general->refreshlink = (new moodle_url('/question/type/stack/questionxmlcompare.php', $pageparams))->out();

$output = new stdClass();
$output->question = new stdClass();
$output->question->name = $question->name;
$output->question->currentversion = $qversion;
$output->question->compareversion = $compareversion;
$output->general = $general;
$output->hasnotices = $notices !== '';
$output->notices = $notices;
$output->nootherversions = count($versions) < 2;
$output->versions = stack_question_xml_compare::version_options($versions, $compareversion);
$output->rows = stack_question_xml_compare::diff_rows($currentxml, $comparexml);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('qtype_stack/questionxmlcompare', $output);
echo $OUTPUT->footer();
