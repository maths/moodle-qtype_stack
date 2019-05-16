<?php
// This file is part of STACK - https://stack.maths.ed.ac.uk
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
 * This script runs the answers tests and verifies the results.
 *
 * This serves two purposes. First, it verifies that the answer tests are working
 * correctly, and second it serves to document the expected behaviour of answer
 * tests, which is useful for learning how they work.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir .'/filelib.php');
require_once($CFG->libdir .'/tablelib.php');

require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/options.class.php');
require_once(__DIR__ . '/stack/answertest/controller.class.php');
require_once(__DIR__ . '/tests/fixtures/subscriptsfixtures.class.php');

// Authentication. It is safe to make this available to any logged in user.
require_login();
require_capability('qtype/stack:usediagnostictools', context_system::instance());

// Set up the page object.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/question/type/stack/subscripts.php');
$title = 'Subscript testing'; // Don't add language strings to the system.  This page is temporary.
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo html_writer::tag('p', "This is a temporary page to run subscript examples. ".
        "These will eventually become part of the unit tests only.");

// Set up the results table.
$columns = array(
    'outcome'       => 'Test passed?',
    'expression'    => 'Expression',
    'rawinput'      => 'Raw input',
    'maxima'        => 'Expected value',
    'value'         => 'CAS value',
    'tex'           => 'Expected TeX',
    'tex-display'   => 'CAS TeX',
    'display'       => 'Display',
    'errors'        => 'Errors',
    'notes'         => 'Notes'
);

$table = new flexible_table('stack_answertests');
$table->define_columns(array_keys($columns));
$table->define_headers(array_values($columns));
$table->set_attribute('class', 'generaltable generalbox stacktestsuite');
$table->define_baseurl($PAGE->url);
$table->setup();

$testdata = stack_subscripts_test_data::get_raw_test_data();
$notes = array();
$ni = 1;
foreach ($testdata as $data) {
    $test = stack_subscripts_test_data::test_from_raw($data);
    $simp = true;
    $result = stack_subscripts_test_data::run_test($test, $simp);

    $class = 'pass';
    $outcome = '';
    $note = '';
    if ($test->notes != '') {
        $notes[$ni] = $test->notes;
        $note = '('.$ni.')';
        $ni++;
    }

    if ('invalid' == $test->maxima) {
        if ($test->valid) {
            $class = 'fail';
            $outcome .= 'Expected invalid expression. ';
        }
    } else {
        $vtarget = $test->maxima;
        if ($simp and $test->maximasimp != '!') {
            $vtarget = $test->maximasimp;
        }
        if ($vtarget != $test->value) {
            $class = 'fail';
            $outcome .= 'CAS value. ';
        }
        $dtarget = $test->tex;
        if ($simp and $test->texsimp != '!') {
            $dtarget = $test->texsimp;
        }
        if ($test->value != '' && $dtarget != $test->display) {
            $class = 'fail';
            $outcome .= 'Display. ';
        }
    }

    $row = array(
        'outcome'       => $outcome,
        'expression'    => format_text('\('.$test->tex.'\)'),
        'rawinput'      => '<pre>'.$test->rawinput.'</pre>',
        'maxima'        => '<pre>'.$vtarget.'</pre>',
        'value'         => '<pre>'.$test->value.'</pre>',
        'tex'           => '<pre>'.$dtarget.'</pre>',
        'tex-display'   => '<pre>'.$test->display.'</pre>',
        'display'       => format_text('\('.$test->display.'\)'),
        'errors'        => $test->errors,
        'notes'         => $note,
    );

    $table->add_data_keyed($row, $class);

    flush();
}

$table->finish_output();

echo "<ol>";
foreach ($notes as $note) {
    echo "<li>". $note . "</li>";
}
echo "</ol>";

// Finish output.
echo $OUTPUT->footer();

