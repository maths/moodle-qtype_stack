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
require_once($CFG->dirroot . '/question/format/xml/format.php');

require_login();

/**
 * Export one question version as Moodle XML.
 *
 * @param stdClass $questiondata the question data to export.
 * @return string|false the XML string, or false if it cannot be exported.
 */
function qtype_stack_export_question_version_xml($questiondata) {
    global $COURSE;

    $question = question_bank::load_question($questiondata->id);
    $questiondata = clone($questiondata);
    $questiondata->contextid = $question->contextid;
    $questiondata->idnumber = $question->idnumber;

    $qformat = new qformat_xml();
    $qformat->setCattofile(false);
    $qformat->setContexttofile(false);
    $qformat->setContextfromfile(false);
    $qformat->setStoponerror(true);
    $qformat->setCourse($COURSE);

    if (!$qformat->exportpreprocess()) {
        return false;
    }

    $xml = $qformat->writequestion($questiondata);
    if (!$xml) {
        return false;
    }

    return '<?xml version="1.0" encoding="UTF-8"?>
<quiz>
' . $xml . '</quiz>';
}

/**
 * Split XML into lines for comparison.
 *
 * @param string $xml the XML to split.
 * @return string[] the lines.
 */
function qtype_stack_xml_compare_lines($xml) {
    return explode("\n", str_replace(["\r\n", "\r"], "\n", $xml));
}

/**
 * Build a small line-based diff.
 *
 * @param string $currentxml the current version XML.
 * @param string $comparexml the selected version XML.
 * @return stdClass[] rows for rendering.
 */
function qtype_stack_xml_compare_rows($currentxml, $comparexml) {
    $current = qtype_stack_xml_compare_lines($currentxml);
    $compare = qtype_stack_xml_compare_lines($comparexml);
    $currentcount = count($current);
    $comparecount = count($compare);
    $lcs = array_fill(0, $currentcount + 1, array_fill(0, $comparecount + 1, 0));

    for ($i = $currentcount - 1; $i >= 0; $i--) {
        for ($j = $comparecount - 1; $j >= 0; $j--) {
            if ($current[$i] === $compare[$j]) {
                $lcs[$i][$j] = $lcs[$i + 1][$j + 1] + 1;
            } else {
                $lcs[$i][$j] = max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
            }
        }
    }

    $ops = [];
    $i = 0;
    $j = 0;
    while ($i < $currentcount && $j < $comparecount) {
        if ($current[$i] === $compare[$j]) {
            $ops[] = [
                'type' => 'same',
                'currentline' => $i + 1,
                'compareline' => $j + 1,
                'currenttext' => $current[$i],
                'comparetext' => $compare[$j],
            ];
            $i++;
            $j++;
        } else if ($lcs[$i + 1][$j] >= $lcs[$i][$j + 1]) {
            $ops[] = [
                'type' => 'delete',
                'currentline' => $i + 1,
                'compareline' => null,
                'currenttext' => $current[$i],
                'comparetext' => null,
            ];
            $i++;
        } else {
            $ops[] = [
                'type' => 'add',
                'currentline' => null,
                'compareline' => $j + 1,
                'currenttext' => null,
                'comparetext' => $compare[$j],
            ];
            $j++;
        }
    }
    while ($i < $currentcount) {
        $ops[] = [
            'type' => 'delete',
            'currentline' => $i + 1,
            'compareline' => null,
            'currenttext' => $current[$i],
            'comparetext' => null,
        ];
        $i++;
    }
    while ($j < $comparecount) {
        $ops[] = [
            'type' => 'add',
            'currentline' => null,
            'compareline' => $j + 1,
            'currenttext' => null,
            'comparetext' => $compare[$j],
        ];
        $j++;
    }

    $rows = [];
    $opcount = count($ops);
    for ($i = 0; $i < $opcount; $i++) {
        if ($ops[$i]['type'] === 'same') {
            $rows[] = (object) $ops[$i];
            continue;
        }

        $currentblock = [];
        $compareblock = [];
        while ($i < $opcount && $ops[$i]['type'] !== 'same') {
            if ($ops[$i]['type'] === 'delete') {
                $currentblock[] = $ops[$i];
            } else {
                $compareblock[] = $ops[$i];
            }
            $i++;
        }
        $i--;

        $blockrows = max(count($currentblock), count($compareblock));
        for ($j = 0; $j < $blockrows; $j++) {
            $currentop = $currentblock[$j] ?? null;
            $compareop = $compareblock[$j] ?? null;
            if ($currentop && $compareop) {
                $type = 'changed';
            } else if ($currentop) {
                $type = 'added';
            } else {
                $type = 'deleted';
            }
            $rows[] = (object) [
                'type' => $type,
                'currentline' => $currentop['currentline'] ?? null,
                'compareline' => $compareop['compareline'] ?? null,
                'currenttext' => $currentop['currenttext'] ?? null,
                'comparetext' => $compareop['comparetext'] ?? null,
            ];
        }
    }

    return $rows;
}

/**
 * Split a line into characters.
 *
 * @param string $text text to split.
 * @return string[] characters.
 */
function qtype_stack_xml_compare_chars($text) {
    $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
    if ($chars === false) {
        $chars = str_split($text);
    }

    return $chars;
}

/**
 * Render an inline character diff for two changed lines.
 *
 * @param string $currenttext current version line.
 * @param string $comparetext selected version line.
 * @return string[] current and compared HTML.
 */
function qtype_stack_xml_compare_inline_changed($currenttext, $comparetext) {
    $current = qtype_stack_xml_compare_chars($currenttext);
    $compare = qtype_stack_xml_compare_chars($comparetext);
    $currentcount = count($current);
    $comparecount = count($compare);

    if ($currentcount * $comparecount > 40000) {
        return qtype_stack_xml_compare_inline_changed_region($current, $compare);
    }

    $lcs = array_fill(0, $currentcount + 1, array_fill(0, $comparecount + 1, 0));
    for ($i = $currentcount - 1; $i >= 0; $i--) {
        for ($j = $comparecount - 1; $j >= 0; $j--) {
            if ($current[$i] === $compare[$j]) {
                $lcs[$i][$j] = $lcs[$i + 1][$j + 1] + 1;
            } else {
                $lcs[$i][$j] = max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
            }
        }
    }

    $currenthtml = '';
    $comparehtml = '';
    $i = 0;
    $j = 0;
    while ($i < $currentcount && $j < $comparecount) {
        if ($current[$i] === $compare[$j]) {
            $currenthtml .= s($current[$i]);
            $comparehtml .= s($compare[$j]);
            $i++;
            $j++;
        } else if ($lcs[$i + 1][$j] > $lcs[$i][$j + 1]) {
            $currenthtml .= html_writer::tag('span', s($current[$i]), ['class' => 'stack-xml-compare-inline-added']);
            $i++;
        } else {
            $currenthtml .= html_writer::tag(
                'span',
                s($compare[$j]),
                ['class' => 'stack-xml-compare-inline-deleted stack-xml-compare-inline-missing']
            );
            $comparehtml .= html_writer::tag('span', s($compare[$j]), ['class' => 'stack-xml-compare-inline-deleted']);
            $j++;
        }
    }
    while ($i < $currentcount) {
        $currenthtml .= html_writer::tag('span', s($current[$i]), ['class' => 'stack-xml-compare-inline-added']);
        $i++;
    }
    while ($j < $comparecount) {
        $currenthtml .= html_writer::tag(
            'span',
            s($compare[$j]),
            ['class' => 'stack-xml-compare-inline-deleted stack-xml-compare-inline-missing']
        );
        $comparehtml .= html_writer::tag('span', s($compare[$j]), ['class' => 'stack-xml-compare-inline-deleted']);
        $j++;
    }

    return [$currenthtml, $comparehtml];
}

/**
 * Render inline changed regions for long lines without building an LCS matrix.
 *
 * @param string[] $current current version characters.
 * @param string[] $compare selected version characters.
 * @return string[] current and compared HTML.
 */
function qtype_stack_xml_compare_inline_changed_region($current, $compare) {
    $currentcount = count($current);
    $comparecount = count($compare);
    $prefix = 0;
    while ($prefix < $currentcount && $prefix < $comparecount && $current[$prefix] === $compare[$prefix]) {
        $prefix++;
    }

    $suffix = 0;
    while (
        $suffix < $currentcount - $prefix &&
        $suffix < $comparecount - $prefix &&
        $current[$currentcount - $suffix - 1] === $compare[$comparecount - $suffix - 1]
    ) {
        $suffix++;
    }

    $currenthtml = s(implode('', array_slice($current, 0, $prefix)));
    $comparehtml = s(implode('', array_slice($compare, 0, $prefix)));
    $currentmiddle = implode('', array_slice($current, $prefix, $currentcount - $prefix - $suffix));
    $comparemiddle = implode('', array_slice($compare, $prefix, $comparecount - $prefix - $suffix));
    if ($currentmiddle !== '') {
        $currenthtml .= html_writer::tag('span', s($currentmiddle), ['class' => 'stack-xml-compare-inline-added']);
    }
    if ($comparemiddle !== '') {
        $currenthtml .= html_writer::tag(
            'span',
            s($comparemiddle),
            ['class' => 'stack-xml-compare-inline-deleted stack-xml-compare-inline-missing']
        );
    }
    if ($comparemiddle !== '') {
        $comparehtml .= html_writer::tag('span', s($comparemiddle), ['class' => 'stack-xml-compare-inline-deleted']);
    }
    $currenthtml .= s(implode('', array_slice($current, $currentcount - $suffix)));
    $comparehtml .= s(implode('', array_slice($compare, $comparecount - $suffix)));

    return [$currenthtml, $comparehtml];
}

/**
 * Render one table cell in the XML diff.
 *
 * @param string|null $text cell text.
 * @return string HTML.
 */
function qtype_stack_xml_compare_code_cell($text, $side, $html = null) {
    $classes = 'stack-xml-compare-code stack-xml-compare-code-' . $side;
    if ($html !== null) {
        return html_writer::tag('td', $html, ['class' => $classes]);
    }
    if ($text === null) {
        return html_writer::tag('td', '', ['class' => $classes . ' stack-xml-compare-empty']);
    }

    return html_writer::tag('td', s($text), ['class' => $classes]);
}

/**
 * Render a line number cell in the XML diff.
 *
 * @param int|null $line the line number.
 * @return string HTML.
 */
function qtype_stack_xml_compare_line_cell($line) {
    return html_writer::tag('td', $line === null ? '' : $line, ['class' => 'stack-xml-compare-line']);
}

/**
 * Render the XML diff.
 *
 * @param stdClass[] $rows diff rows.
 * @param string $currentlabel current version label.
 * @param string $comparelabel compared version label.
 * @return string HTML.
 */
function qtype_stack_xml_compare_render_diff($rows, $currentlabel, $comparelabel) {
    $output = html_writer::start_tag('div', ['class' => 'stack-xml-compare-diff-wrap']);
    $output .= html_writer::start_tag('table', ['class' => 'stack-xml-compare-diff']);
    $output .= html_writer::start_tag('thead');
    $output .= html_writer::start_tag('tr');
    $output .= html_writer::tag('th', '', ['class' => 'stack-xml-compare-line']);
    $output .= html_writer::tag('th', $currentlabel);
    $output .= html_writer::tag('th', '', ['class' => 'stack-xml-compare-line']);
    $output .= html_writer::tag('th', $comparelabel);
    $output .= html_writer::end_tag('tr');
    $output .= html_writer::end_tag('thead');
    $output .= html_writer::start_tag('tbody');
    foreach ($rows as $row) {
        $currenthtml = null;
        $comparehtml = null;
        if ($row->type === 'changed') {
            [$currenthtml, $comparehtml] = qtype_stack_xml_compare_inline_changed($row->currenttext, $row->comparetext);
        } else if ($row->type === 'deleted') {
            $currenthtml = html_writer::tag(
                'span',
                s($row->comparetext),
                ['class' => 'stack-xml-compare-inline-deleted stack-xml-compare-inline-missing']
            );
        }
        $output .= html_writer::start_tag('tr', ['class' => 'stack-xml-compare-row-' . $row->type]);
        $output .= qtype_stack_xml_compare_line_cell($row->currentline);
        $output .= qtype_stack_xml_compare_code_cell($row->currenttext, 'current', $currenthtml);
        $output .= qtype_stack_xml_compare_line_cell($row->compareline);
        $output .= qtype_stack_xml_compare_code_cell($row->comparetext, 'compared', $comparehtml);
        $output .= html_writer::end_tag('tr');
    }
    $output .= html_writer::end_tag('tbody');
    $output .= html_writer::end_tag('table');
    $output .= html_writer::end_tag('div');

    return $output;
}

// Get the parameters from the URL.
$questionid = required_param('id', PARAM_INT);
$requestedcompareversion = optional_param('compareversion', null, PARAM_INT);

$versioninfo = get_latest_question_version($questionid);
$qversion = $versioninfo[0];
$questionid = $versioninfo[1];
$versions = $versioninfo[3];
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);
$testpagesetup = qtype_stack_setup_question_test_page($question);
$context = $testpagesetup[0];
$urlparams = $testpagesetup[2];

// Check permissions. Raw XML compare uses the same capability as XML edit.
question_require_capability_on($questiondata, 'edit');

$versionnumbers = array_keys($versions);
$defaultcompareversion = $versionnumbers[1] ?? $qversion;
if ($requestedcompareversion === null) {
    $compareversion = $defaultcompareversion;
} else if (!array_key_exists($requestedcompareversion, $versions)) {
    throw new invalid_parameter_exception('compareversion');
} else {
    $compareversion = $requestedcompareversion;
}

$comparequestionid = $versions[$compareversion];
$comparequestiondata = question_bank::load_question_data($comparequestionid);
if (!$comparequestiondata) {
    throw new stack_exception('questiondoesnotexist');
}

$editparams = $urlparams;
unset($editparams['questionid']);
unset($editparams['seed']);
$editparams['id'] = $question->id;
$pageparams = $editparams;
$pageparams['compareversion'] = $compareversion;

$PAGE->set_url('/question/type/stack/questionxmlcompare.php', $pageparams);
$title = stack_string('comparexmltitle');
$PAGE->set_title($title);

$currentxml = qtype_stack_export_question_version_xml($questiondata);
$comparexml = qtype_stack_export_question_version_xml($comparequestiondata);
$notices = '';
if (!$currentxml || !$comparexml) {
    $notices = stack_string('xmldisplayerror');
    $currentxml = $currentxml ?: '';
    $comparexml = $comparexml ?: '';
}

echo $OUTPUT->header();
$links = [];
$qtype = new qtype_stack();
$qtestlink = $qtype->get_question_test_url($question);
$links[] = html_writer::link($qtestlink, '<i class="fa fa-wrench"></i> '
                            . stack_string('runquestiontests'), ['class' => 'nav-link']);
$qpreviewlink = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);
$links[] = html_writer::link(
    $qpreviewlink,
    '<i class="fa fa-plus-circle"></i> ' . stack_string('questionpreview'),
    ['class' => 'nav-link']
);
$links[] = html_writer::link(
    new moodle_url('/question/type/stack/questioneditlatest.php', $editparams),
    stack_string('editquestioninthequestionbank'),
    ['class' => 'nav-link']
);
$links[] = html_writer::link(
    new moodle_url('/question/type/stack/questionxmledit.php', $editparams),
    stack_string('editxml'),
    ['class' => 'nav-link']
);
echo html_writer::tag('nav', implode(' ', $links), ['class' => 'nav']);

echo $OUTPUT->heading($title);
echo $OUTPUT->heading($question->name, 3);
echo html_writer::tag('p', stack_string('comparexmlcurrentversion', $qversion));
echo html_writer::tag('p', stack_string('comparexmlselectedversion', $compareversion));

if (count($versions) < 2) {
    echo html_writer::tag('div', stack_string('comparexmlnootherversions'), ['class' => 'alert alert-info']);
}
if ($notices) {
    echo html_writer::tag('div', $notices, ['class' => 'alert alert-warning']);
}

$options = [];
foreach (array_keys($versions) as $version) {
    $options[$version] = stack_string('version') . ' ' . $version;
}
$select = html_writer::select(
    $options,
    'compareversion',
    $compareversion,
    false,
    ['id' => 'id_compareversion', 'class' => 'custom-select form-select', 'onchange' => 'this.form.submit();']
);
$form = html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $question->id]);
$form .= html_writer::label(stack_string('comparexmlselectversion'), 'id_compareversion');
$form .= $select;
$form .= html_writer::empty_tag('input', ['type' => 'submit', 'class' => 'btn btn-secondary', 'value' => get_string('go')]);
echo html_writer::tag(
    'form',
    $form,
    [
        'method' => 'get',
        'action' => (new moodle_url('/question/type/stack/questionxmlcompare.php'))->out(false),
        'class' => 'stack-xml-compare-controls',
    ]
);

$rows = qtype_stack_xml_compare_rows($currentxml, $comparexml);
$currentlabel = stack_string('comparexmlcurrentversion', $qversion);
$comparelabel = stack_string('comparexmlselectedversion', $compareversion);
echo qtype_stack_xml_compare_render_diff($rows, $currentlabel, $comparelabel);

echo $OUTPUT->footer();
