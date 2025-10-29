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
 * This script lets the user edit the question XML directly and attempt
 * to import the XML as a new version.
 *
 * @package    qtype_stack
 * @copyright  2025 Universiy of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once(__DIR__ . '/locallib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
use core_question\local\bank\question_edit_contexts;
use qformat_xml;

require_login();

// Get the parameters from the URL.
$questionid = required_param('id', PARAM_INT);

list($qversion, $questionid) = get_latest_question_version($questionid);
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);
// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'edit');
$editparams = $urlparams;
unset($editparams['questionid']);
unset($editparams['seed']);
$editparams['id'] = $question->id;
$questionediturl = new moodle_url('/question/bank/editquestion/question.php', $editparams);
$questioneditlatesturl = new moodle_url('/question/type/stack/questioneditlatest.php', $editparams);

$PAGE->set_url('/question/type/stack/questionxmledit.php', $editparams);
$title = stack_string('editxmltitle');
$PAGE->set_title($title);

echo $OUTPUT->header();
$links = [];
$qtype = new qtype_stack();
$qtestlink = $qtype->get_question_test_url($question);
$links[] = html_writer::link($qtestlink, '<i class="fa fa-wrench"></i> '
                            . stack_string('runquestiontests'), ['class' => 'nav-link']);
$qpreviewlink = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);
$links[] = html_writer::link($qpreviewlink, '<i class="fa fa-plus-circle"></i> '
                            . stack_string('questionpreview'), ['class' => 'nav-link']);
$links[] = html_writer::link($questioneditlatesturl, stack_string('editquestioninthequestionbank'),
                                ['class' => 'nav-link']);
echo html_writer::tag('nav', implode(' ', $links), ['class' => 'nav']);



$question = question_bank::load_question($questionid);
$qformat = new qformat_xml();
$qformat->setCourse($course);
$contexts = new question_edit_contexts($context);
$qformat->setCattofile(false);
$qformat->setContexttofile(false);
$errs = '';
if (trim(optional_param('importasnewversion', '', PARAM_RAW)) == trim(stack_string('editxmlbutton'))) {
    $importfile = make_request_directory() . "/importq.xml";
    file_put_contents($importfile, optional_param('questionxml', '', PARAM_RAW));

    $qformat->setContextfromfile(false);
    $qformat->setStoponerror(true);
    $result = \qbank_importasversion\importer::import_file($qformat, $question, $importfile);
    $errs = $result->error ?? '' . $result->notice ?? '';
    ob_clean();
}
list($qversion, $questionid) = get_latest_question_version($questionid);
if (!empty($result->error)) {
    $string = optional_param('questionxml', '', PARAM_RAW);
} else {
    $question = question_bank::load_question_data($questionid);

    $qformat->setQuestions([$question]);
    if (!$qformat->exportpreprocess()) {
        throw new moodle_exception('exporterror', 'qbank_gitsync', null, $questiondata->questionid);
    }
    if (!$question = $qformat->exportprocess(true)) {
        throw new moodle_exception('exporterror', 'qbank_gitsync', null, $questiondata->questionid);
    }
    $string = $question;
}
echo $OUTPUT->heading($title);
echo $OUTPUT->heading($question->name, 3);
echo html_writer::tag('p', stack_string('version') . ' ' . $qversion);

$fout .= html_writer::tag('h4', stack_string('editxmlquestion'));
$fout .= html_writer::tag('p', $errs);
$stringlen = max(substr_count($string, "\n") + 3, 8);
$fout .= html_writer::tag('p', html_writer::tag('textarea', $string,
            ['cols' => 100, 'rows' => $stringlen, 'name' => 'questionxml']));
$fout .= html_writer::start_tag('p');

$fout .= html_writer::empty_tag('input',
    ['type' => 'submit',  'class' => 'btn-primary', 'name' => 'importasnewversion', 'value' => stack_string('editxmlbutton'), 'formaction' => $PAGE->url]);

$fout .= html_writer::end_tag('p');

echo html_writer::tag('form', $fout, ['method' => 'post']);

if ('' != trim($debuginfo)) {
    echo $OUTPUT->box($debuginfo);
}

echo html_writer::tag('p', stack_string('editxmlintro'));
echo $OUTPUT->footer();

