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
 * This script shows the results of a compiling for a question.
 * In a readable form, with CAS-content pretty printed.
 *
 * This is for developers.
 *
 * @copyright  2022 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use question_bank;

require_once(__DIR__.'/../../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../../../engine/lib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');

require_login();
$context = context_system::instance();
require_capability('qtype/stack:usediagnostictools', $context);

$urlparams = [];

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/adminui/compiledquestion.php', $urlparams);
$title = 'Compiled cache view';
$PAGE->set_title($title);

if (!isset($_GET['qid']) || !is_numeric($_GET['qid'])) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);

    echo '<p>This tool requires a GET parameter "qid" that defines the question being explored.</p>';

    echo $OUTPUT->footer();
    die();
}

$q = question_bank::load_question($_GET['qid']);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo '<p>This tool presents the cached compialtion results related to a particular question.</p>';

echo '<p>These are mainly for STACK developers, these should not matter for authors or other users. ' .
     'However, one may use these to find out what we do to your code before it gets evaluated.</p>';


echo '<h3>Simple details</h3>';
$selected = [
    'units' => $q->get_cached('units'),
    'langs' => $q->get_cached('langs'),
    'forbiddenkeys' => array_keys($q->get_cached('forbiddenkeys'))];
echo '<pre>' . htmlspecialchars(json_encode($selected, JSON_PRETTY_PRINT), ENT_COMPAT) . '</pre>';


echo '<h3>Question variables</h3>';

if ($q->get_cached('preamble-qv') !== null) {
    echo '<p>Preamble:</p>';
    echo '<pre>' . htmlspecialchars(maxima_parser_utils::parse($q->get_cached('preamble-qv'))->
        toString(['pretty' => true]), ENT_COMPAT) . '</pre>';
}

if ($q->get_cached('contextvariables-qv') !== null) {
    echo '<p>Contextvariables:</p>';
    echo '<pre>' . htmlspecialchars(maxima_parser_utils::parse($q->get_cached('contextvariables-qv'))->
        toString(['pretty' => true]), ENT_COMPAT) . '</pre>';
}

if ($q->get_cached('statement-qv') !== null) {
    echo '<p>Question variables:</p>';
    echo '<pre>' . htmlspecialchars(maxima_parser_utils::parse($q->get_cached('statement-qv'))->
        toString(['pretty' => true]), ENT_COMPAT) . '</pre>';
} else {
    echo '<p>No actual question variables.</p>';
}



echo '<h3>PRTs</h3>';

foreach ($q->prts as $prt) {
    echo '<h4>' . $prt->get_name() . '</h4>';

    echo '<p>Required inputs: ' . json_encode(array_keys($q->get_cached('required')[$prt->get_name()])) . '</p>';

    if (isset($q->get_cached('prt-preamble')[$prt->get_name()]) &&
            $q->get_cached('prt-preamble')[$prt->get_name()] !== null) {
        echo '<p>PRT-preamble:</p>';
        echo '<pre>' . htmlspecialchars(maxima_parser_utils::parse($q->get_cached('prt-preamble')[$prt->get_name()])->
            toString(['pretty' => true]), ENT_COMPAT) . '</pre>';
    }

    if (isset($q->get_cached('prt-contextvariables')[$prt->get_name()]) &&
            $q->get_cached('prt-contextvariables')[$prt->get_name()] !== null) {
        echo '<p>PRT-contextvariables:</p>';
        echo '<pre>' . htmlspecialchars(maxima_parser_utils::parse($q->get_cached('prt-contextvariables')[$prt->get_name()])->
            toString(['pretty' => true]), ENT_COMPAT) . '</pre>';
    }

    echo '<p>PRT-logic:</p>';
    echo '<pre>' . htmlspecialchars(maxima_parser_utils::parse($q->get_cached('prt-definition')[$prt->get_name()])->
        toString(['pretty' => true]), ENT_COMPAT) . '</pre>';
}

echo '<h3>Various text fragments</h3>';

echo '<table><tr><th>Part</th><th>Compiled CASText code</th></tr>';

foreach ($q->compiledcache as $key => $value) {
    if (strpos($key, 'castext-') === 0) {
        echo '<tr><td>'. $key . '</td><td><pre>';
        echo htmlspecialchars(maxima_parser_utils::parse($value)->toString(['pretty' => true]), ENT_COMPAT);
        echo '</pre></td></tr>';
    }
}

echo '</table>';



echo '<h3>Static strings</h3>';
echo '<p>These were extracted to avoid sending these to the CAS and back.</p>';

echo '<table><tr><th>key</th><th>value</th></tr>';

foreach ($q->get_cached('static-castext-strings') as $key => $value) {
    echo '<tr><td><pre>' . $key . '</pre></td><td><pre>' . htmlspecialchars($value, ENT_COMPAT) . '</pre></td></tr>';
}

echo '</table>';


echo $OUTPUT->footer();
