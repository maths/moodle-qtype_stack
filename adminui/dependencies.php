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
 * This script lets the user find questions with particular features
 * and extract statistics about the use of some other things.
 *
 * This thing leverages the compiledcache as it can be used to query
 * the whole logic in a fast way. Thus this does not work for questions
 * that have not been compiled.
 *
 * @copyright  2022 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../../../engine/lib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../vle_specific.php');

require_login();
$context = context_system::instance();
require_capability('qtype/stack:usediagnostictools', $context);

$urlparams = [];

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/adminui/dependencies.php', $urlparams);
$title = 'Dependency checker';
$PAGE->set_title($title);

// Figure out the number of questions that can be explored.
// In Moodle 4+ hidden questions occur when they are included in a quiz, but then are deleted from the question bank.
// In this case the database sets the field `status` to `'hidden'` within the question versions database.
$query = 'SELECT count(*) as notcompiled FROM {question} q, ' .
    '{qtype_stack_options} o, {question_versions} v WHERE q.id = o.questionid AND q.id = v.id ' . '
    AND NOT v.status = "hidden" AND o.compiledcache = ?;';

$notcompiled = $DB->get_recordset_sql($query, ['{}']);

$nnotcompiled = 0;
$ncompiled = 0;
foreach ($notcompiled as $item) {
    $nnotcompiled = $item->notcompiled;
}
$notcompiled->close();

$query = 'SELECT count(*) as compiled FROM {question} q, ' .
    '{qtype_stack_options} o, {question_versions} v WHERE q.id = o.questionid AND q.id = v.id ' . '
    AND NOT v.status = "hidden" AND o.compiledcache = ?;';

$compiled = $DB->get_recordset_sql($query, ['{}']);

foreach ($compiled as $item) {
    $ncompiled = $item->compiled;
}
$compiled->close();

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo '<p>This tool only acts on succesfully compiled questions, to compile questions run the bulk tester ' .
    'or preview/use those questions.</p><p>';
if ($ncompiled !== $notcompiled) {
    echo stack_string_error('errors') . '. ';
}
echo  'Currently there are ' . $ncompiled . ' compiled questions and ' . $nnotcompiled .
    ' questions that have not been succesfully compiled.</p>';

echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['includes' => 1, 'sesskey' => sesskey()]),
    'Find "includes"');
echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['jsxgraphs' => 1, 'sesskey' => sesskey()]),
    'Find "jsxgraphs"');
echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['geogebras' => 1, 'sesskey' => sesskey()]),
    'Find "geogebra"');
echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['script' => 1, 'sesskey' => sesskey()]),
    'Find "<script"');
echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['PLUGINFILE' => 1, 'sesskey' => sesskey()]),
    'Find "@@PLUGINFILE@@"');
echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['langs' => 1, 'sesskey' => sesskey()]),
    'Find "langs"');
echo $OUTPUT->single_button(
    new moodle_url($PAGE->url, ['todo' => 1, 'sesskey' => sesskey()]),
    'Find "todo"');

if (data_submitted() && optional_param('includes', false, PARAM_BOOL)) {
    /*
     * Search for questions that have any includes, both keyval and CASText.
     * Both are noted in the compiled cache as important meta.
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' .
        $DB->sql_like('o.compiledcache', ':trg') . ';', ['trg' => '%"includes"%']);
    echo '<h4>Questions using includes</h4>';
    echo '<table><thead><tr><th>Question</th><th>Keyval includes</th><th>Castext includes</th></tr></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        // Confirm that it does have these.
        if (isset($q->compiledcache['includes']) && (
            (isset($q->compiledcache['includes']['keyval']) && count($q->compiledcache['includes']['keyval']) > 0) ||
            (isset($q->compiledcache['includes']['castext']) && count($q->compiledcache['includes']['castext']) > 0))) {
            list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
            $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                    null, null, null, null, $context);
            echo "<tr><td>" . $q->name . ' ' .
                $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) . '</td>';
            echo '<td>';
            if (isset($q->compiledcache['includes']['keyval'])) {
                foreach ($q->compiledcache['includes']['keyval'] as $url) {
                    echo html_writer::start_tag('a', ['href' => $url]);
                    echo $url;
                    echo html_writer::end_tag('a');
                }
            }
            echo '</td>';
            echo '<td>';
            if (isset($q->compiledcache['includes']['castext'])) {
                foreach ($q->compiledcache['includes']['castext'] as $url) {
                    echo html_writer::start_tag('a', ['href' => $url]);
                    echo $url;
                    echo html_writer::end_tag('a');
                }
            }
            echo '</td></tr>';
        }
    }
    echo '</tbody></table>';
}

if (data_submitted() && optional_param('jsxgraphs', false, PARAM_BOOL)) {
    /*
     * JSXGraphs are spotted from the compiled cache, finding '["jsxgraph",'
     * means that there are STACK block based JSXGraphs. '</jsxgraph>' would
     * mean that the official filter is in play, if we find "jsxgraph" in any other
     * form then we probably have something else in play or a "TO-DO" note.
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' .
        $DB->sql_like('o.compiledcache', ':trg', false) . ';', ['trg' => '%jsxgraph%']);
    echo '<h4>Questions containing JSXGraph related terms</h4>';
    echo '<table><thead><tr><th>Question</th>' .
        '<th>[[jsxgraph]]</th><th>&lt;jsxgraph</th><th>Other</th></tr></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        $block = 'false';
        $filter = 'false';
        $other = 'false';
        $json = json_encode($q->compiledcache);
        if (mb_strpos($json, 'STACK JSXGraph') !== false && mb_strpos($json, 'JSXGRAPH_COUNT') !== false) {
            $block = 'true';
            $json = str_replace('STACK JSXGraph', '', $json);
            $json = str_replace('JSXGRAPH_COUNT', '', $json);
        }
        if (mb_strpos($json, '</jsxgraph>') !== false) {
            $filter = 'true';
            $json = str_replace('</jsxgraph>', '', $json);
            $json = str_replace('<jsxgraph', '', $json);
        }
        if (mb_stripos($json, 'jsxgraph') !== false) {
            // This currentyl provides false positives, as it cannot separate loaded
            // scripts from those loaded through the block.
            $other = 'true';
            if ($block === 'true') {
                $other = 'maybe';
            }
        }
        // Confirm that it does have these.
        if ($block || $filter || $other) {
            list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
            $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                    null, null, null, null, $context);
            echo "<tr><td>" . $q->name . ' ' .
                $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) . '</td>';
            echo "<td>$block</td><td>$filter</td><td>$other</td></tr>";
        }
    }
    echo '</tbody></table>';
}

if (data_submitted() && optional_param('geogebras', false, PARAM_BOOL)) {
    /*
     * GeoGebra Graphs are spotted from the compiled cache, finding '["geogebra",'
     * means that there are STACK block based GeoGebra. '</geogebra>' would
     * mean that the official filter is in play, if we find "geogebra" in any other
     * form then we probably have something else in play or a "TO-DO" note.
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' .
        $DB->sql_like('o.compiledcache', ':trg', false) . ';', ['trg' => '%geogebra%']);
    echo '<h4>Questions containing GeogGebra related terms</h4>';
    echo '<table><thead><tr><th>Question</th>' .
        '<th>[[geogebra]]</th><th>&lt;geogebra</th><th>Other</th></tr></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        $block = 'false';
        $filter = 'false';
        $other = 'false';
        $json = json_encode($q->compiledcache);
        if (mb_strpos($json, '[\\"geogebra\\",') !== false) {
            $block = 'true';
            $json = str_replace('[\\"geogebra\\",', '', $json);
        }
        if (mb_strpos($json, '</geogebra>') !== false) {
            $filter = 'true';
            $json = str_replace('</geogebra>', '', $json);
            $json = str_replace('<geogebra', '', $json);
        }
        if (mb_stripos($json, 'geogebra') !== false) {
            $other = 'true';
        }
        // Confirm that it does have these.
        if ($block || $filter || $other) {
            list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
            $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                    null, null, null, null, $context);
            echo "<tr><td>" . $q->name . ' ' .
                $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) . '</td>';
            echo "<td>$block</td><td>$filter</td><td>$other</td></tr>";
        }
    }
    echo '</tbody></table>';
}

if (data_submitted() && optional_param('script', false, PARAM_BOOL)) {
    /*
     * <script present in the question
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' .
        $DB->sql_like('o.compiledcache', ':trg', false) . ';', ['trg' => '%<script%']);
    echo '<h4>Questions containing script tags</h4>';
    echo '<table><thead><tr><th>Question</th></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
        $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                null, null, null, null, $context);
        echo "<tr><td>" . $q->name . ' ' .
            $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) . '</td></tr>';
    }
    echo '</tbody></table>';
}

if (data_submitted() && optional_param('PLUGINFILE', false, PARAM_BOOL)) {
    /*
     * @@PLUGINFILE@@ present in the question.
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' .
        $DB->sql_like('o.compiledcache', ':trg') . ';', ['trg' => '%@@PLUGINFILE@@%']);
    echo '<h4>Questions containing attached files handled by Moodle</h4>';
    echo '<table><thead><tr><th>Question</th></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
        $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                null, null, null, null, $context);
        echo "<tr><td>" . $q->name . ' ' .
            $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) . '</td></tr>';
    }
    echo '</tbody></table>';
}

if (data_submitted() && optional_param('langs', false, PARAM_BOOL)) {
    /*
     * Questions that have localisation.
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' . $DB->sql_like('o.compiledcache', ':trg') . ' AND NOT ' .
        $DB->sql_like('o.compiledcache', ':other') . ';', ['trg' => '%"langs":[%', 'other' => '%"langs":[]%']);
    echo '<h4>Questions containing localisation using means we understand.</h4>';
    echo '<table><thead><tr><th>Question</th><th>Langs</th></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
        $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                null, null, null, null, $context);

        echo "<tr><td>" . $q->name . ' ' .
            $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) . '</td><td>';
        echo implode(', ', $q->get_cached('langs'));
        echo '</td></tr>';
    }
    echo '</tbody></table>';
}

if (data_submitted() && optional_param('todo', false, PARAM_BOOL)) {
    /*
     * Todo blocks present in the question.
     */
    $qs = $DB->get_recordset_sql('SELECT q.id as questionid FROM {question} q, {qtype_stack_options} o WHERE ' .
        'q.id = o.questionid AND ' .
        $DB->sql_like('o.compiledcache', ':trg') . ';', ['trg' => '%stack_todo%']);
    echo '<h4>Questions containing [[todo]] blocks</h4>';
    echo '<table><thead><tr><th>Question</th><th>Tags</th></thead><tbody>';
    // Load the whole question, simpler to get the contexts correct that way.
    foreach ($qs as $item) {
        $q = question_bank::load_question($item->questionid);
        $tags = $q->get_question_todos();
        list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($q);
        $qurl = qbank_previewquestion\helper::question_preview_url($item->questionid,
                null, null, null, null, $context);
        echo "<tr><td>" . $q->name . ' ' .
            $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))) .
            '</td><td>' . implode(', ', $tags). '<td></tr>';
    }
    echo '</tbody></table>';
}

echo $OUTPUT->footer();
