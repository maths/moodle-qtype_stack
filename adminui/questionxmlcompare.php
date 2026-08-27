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

require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/../locallib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/questionxmlcompare.class.php');
require_once(__DIR__ . '/../questionxmlcompare_form.php');

require_login();

// Get the parameters from the URL.
$questionid = optional_param('id', null, PARAM_INT);
$requestedcurrentversion = optional_param('currentversion', null, PARAM_INT);
$requestedcompareversion = optional_param('compareversion', null, PARAM_INT);
$requesteddisplay = optional_param('display', null, PARAM_ALPHA);
$requesteddiffonly = optional_param('diffonly', stack_question_xml_compare::FILTER_ALL, PARAM_BOOL);
$requestedcurrentfile = optional_param('currentfile', 0, PARAM_INT);
$requestedcomparefile = optional_param('comparefile', 0, PARAM_INT);

if ($questionid) {
    [$latestversion, , $selectedquestionbankentryid, $versions] = get_latest_question_version($questionid);
    $currentversion = stack_question_xml_compare::get_current_version($requestedcurrentversion, $versions, $latestversion);
    $questionid = $versions[$currentversion];
    $questiondata = question_bank::load_question_data($questionid);
    if (!$questiondata) {
        throw new stack_exception('questiondoesnotexist');
    }
    $question = question_bank::load_question($questionid);
    $isstackquestion = ($questiondata->qtype === 'stack');
    // Check permissions. Raw XML compare uses the same capability as XML edit.
    question_require_capability_on($questiondata, 'edit');
    // The comparison version defaults to the version before the current one unless the user picked one.
    $compareversion = stack_question_xml_compare::get_compare_version($requestedcompareversion, $versions, $currentversion);
} else {
    require_capability('qtype/stack:usediagnostictools', context_system::instance());
    $questiondata = null;
    $compareversion = null;
    $isstackquestion = false;
}

$urlparams = [];
// Preserve the current course or module context so generated links stay anchored to this page.
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
    if ($questionid) {
        $context = $question->get_context();
        if ($context->contextlevel == CONTEXT_MODULE) {
            $urlparams['cmid'] = $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $urlparams['courseid'] = $context->instanceid;
        } else {
            $urlparams['courseid'] = SITEID;
        }
    }
}


$displaymode = stack_question_xml_compare::get_display_mode($requesteddisplay);
$diffonly = stack_question_xml_compare::get_diff_only($requesteddiffonly);

// Keep the selected versions and context parameters in the URL so the page can be refreshed safely.
$editparams = $urlparams;
unset($editparams['questionid']);
unset($editparams['seed']);
$editparams['id'] = $question->id ?? null;
$pageparams = $editparams;
$pageparams['currentversion'] = $currentversion;
$pageparams['compareversion'] = $compareversion;
$pageparams['display'] = $displaymode;
$pageparams['diffonly'] = $diffonly ? stack_question_xml_compare::FILTER_DIFFERENCES : stack_question_xml_compare::FILTER_ALL;
if ($requestedcurrentversion === null) {
    unset($pageparams['currentversion']);
}

$PAGE->set_context($context);
$uploadformparams = $pageparams;
$uploadform = new qtype_stack_question_xml_compare_form(
    new moodle_url('/question/type/stack/adminui/questionxmlcompare.php', $uploadformparams)
);
if ($fromform = $uploadform->get_data()) {
    // The form posts draft ids, so only keep ids that still resolve to an uploaded file.
    $requestedcurrentfile = !empty($fromform->currentfile) ? (int) $fromform->currentfile : 0;
    $requestedcomparefile = !empty($fromform->comparefile) ? (int) $fromform->comparefile : 0;
    $redirectparams = $pageparams;
    if (stack_question_xml_compare::draft_file($requestedcurrentfile)) {
        $redirectparams['currentfile'] = $requestedcurrentfile;
    }
    if (stack_question_xml_compare::draft_file($requestedcomparefile)) {
        $redirectparams['comparefile'] = $requestedcomparefile;
    }
    redirect(new moodle_url('/question/type/stack/adminui/questionxmlcompare.php', $redirectparams));
}

// Start with the exported XML for each version, then replace either side with uploaded draft content.
$currentfile = $requestedcurrentfile;
$comparefile = $requestedcomparefile;
$noticeitems = [];

$currentxml = '';
if ($currentfile) {
    $uploadedxml = stack_question_xml_compare::draft_file_content($currentfile);
    if ($uploadedxml === null) {
        $uploadedxml = $uploadform->get_file_content('currentfile');
    }
    if ($uploadedxml === null || $uploadedxml === false) {
        $currentfile = 0;
    } else {
        $currentxml = $uploadedxml;
    }
} else {
    // Fall back to the exported XML for the selected version when no upload is present.
    $currentxml = ($questiondata) ? stack_question_xml_compare::export_question_version_xml($questiondata) : '';
}

$comparexml = '';
if ($comparefile) {
    $uploadedxml = stack_question_xml_compare::draft_file_content($comparefile);
    if ($uploadedxml === null) {
        $uploadedxml = $uploadform->get_file_content('comparefile');
    }
    if ($uploadedxml === null || $uploadedxml === false) {
        $comparefile = 0;
    } else {
        $comparexml = $uploadedxml;
    }
} else {
    // Mirror the same fallback on the comparison side.
    if ($compareversion) {
        $comparequestionid = $versions[$compareversion];
        $comparequestiondata = question_bank::load_question_data($comparequestionid);
        if (!$comparequestiondata) {
            throw new stack_exception('questiondoesnotexist');
        }
        question_require_capability_on($comparequestiondata, 'edit');
        $comparexml = stack_question_xml_compare::export_question_version_xml($comparequestiondata);
    } else {
        $comparexml = '';
    }
}

if ((($questionid || $currentfile) && !$currentxml) || (( $comparefile || $compareversion) && !$comparexml)) {
    // Keep the page rendering even if one export or upload fails, and surface a warning instead.
    $noticeitems[] = stack_string('xmldisplayerror');
    $currentxml = $currentxml ?: '';
    $comparexml = $comparexml ?: '';
}
if ($currentfile) {
    $pageparams['currentfile'] = $currentfile;
}
if ($comparefile) {
    $pageparams['comparefile'] = $comparefile;
}

$PAGE->set_url('/question/type/stack/adminui/questionxmlcompare.php', $pageparams);
$title = stack_string('comparexmltitle');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

$uploadform->set_data([
    'currentfile' => $currentfile ?: null,
    'comparefile' => $comparefile ?: null,
]);

$hascurrentfile = !empty($currentfile);
$hascomparefile = !empty($comparefile);
$hasfiles = $hascurrentfile || $hascomparefile;
$general = new stdClass();
$general->isstackquestion = $isstackquestion;
$general->fromdashboard = ($questionid) ? true : false;
$general->adminuilink = (new moodle_url('/question/type/stack/adminui/index.php'))->out();
$general->filesopen = $hasfiles || !$questionid;
$general->questionselectmuted = $hascurrentfile && $hascomparefile;

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
$general->formaction = (new moodle_url('/question/type/stack/adminui/questionxmlcompare.php'))->out(false);
$formparams = $editparams;
unset($formparams['currentversion']);
unset($formparams['compareversion']);
$formparams['display'] = $displaymode;
$formparams['diffonly'] = $pageparams['diffonly'];
if ($currentfile) {
    $formparams['currentfile'] = $currentfile;
}
if ($comparefile) {
    $formparams['comparefile'] = $comparefile;
}
$general->formparams = stack_question_xml_compare::form_params($formparams);
$questionformparams = $editparams;
unset($questionformparams['id']);
$questionformparams['display'] = $displaymode;
$questionformparams['diffonly'] = $pageparams['diffonly'];
$general->questionformparams = stack_question_xml_compare::question_form_params($questionformparams);
$latestparams = $pageparams;
unset($latestparams['currentversion']);
$general->latestlink = (new moodle_url('/question/type/stack/adminui/questionxmlcompare.php', $latestparams))->out();
$toggleparams = $pageparams;
$toggleparams['display'] = stack_question_xml_compare::toggle_display_mode($displaymode);
$general->displaytogglelink = (new moodle_url('/question/type/stack/adminui/questionxmlcompare.php', $toggleparams))->out();
$diffonlytoggleparams = $pageparams;
$diffonlytoggleparams['diffonly'] = stack_question_xml_compare::toggle_diff_only($diffonly);
$general->diffonlytogglelink = (new moodle_url('/question/type/stack/adminui/questionxmlcompare.php', $diffonlytoggleparams))->out();
$general->uploadform = $uploadform->render();

// Template data is intentionally flat: the page template reads the view state directly.
$viewdata = new stdClass();
$viewdata->displaymode = $displaymode;
$viewdata->displayunified = ($displaymode === stack_question_xml_compare::DISPLAY_UNIFIED);
$viewdata->displaysplit = ($displaymode === stack_question_xml_compare::DISPLAY_SPLIT);
$viewdata->diffonly = $diffonly;
$viewdata->showalllines = !$diffonly;
$viewdata->general = $general;
$viewdata->notices = $noticeitems;
if ($questionid) {
    $viewdata->currentversions = stack_question_xml_compare::version_options($versions, $currentversion, $hascurrentfile);
    $viewdata->versions = stack_question_xml_compare::version_options($versions, $compareversion, $hascomparefile);
    $viewdata->questionoptions = stack_question_xml_compare::questions_in_category(
        $questiondata->category,
        $selectedquestionbankentryid
    );
}
// Diff rows are built last so they reflect the final XML, display mode, and row-filter choice.
$viewdata->rows = stack_question_xml_compare::diff_rows($currentxml, $comparexml, $displaymode, $diffonly);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('qtype_stack/questionxmlcompare', $viewdata);
echo $OUTPUT->footer();
