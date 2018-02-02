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
require_once(__DIR__ . '/tests/fixtures/answertestfixtures.class.php');
require_once(__DIR__ . '/stack/utils.class.php');


// Get the parameters from the URL.
$anstest = optional_param('anstest', '', PARAM_ALPHA);
$questionid = optional_param('questionid', null, PARAM_INT);

// Authentication. Because of the cache, it is safe to make this available to any
// logged in user.
require_login();
require_capability('qtype/stack:usediagnostictools', context_system::instance());

// Set up the page object.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/question/type/stack/answertests.php');
$title = stack_string('stackInstall_testsuite_title');
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);

// Get the list of available tests.
$availabletests = stack_answertest_test_data::get_available_tests();
$availabletests['ALL'] = stack_string('all');
if (!array_key_exists($anstest, $availabletests)) {
    $anstest = '';
}

if ($anstest === 'ALL') {
    $tests = stack_answertest_test_data::get_all();
} else if (!$anstest) {
    $tests = array();
} else {
    $tests = stack_answertest_test_data::get_tests_for($anstest);
}

// Set up the results table.
$columns = array(
    'name'          => stack_string('answertest'),
    'passed'        => stack_string('testsuitecolpassed'),
    'studentanswer' => stack_string('studentanswer'),
    'teacheranswer' => stack_string('teacheranswer'),
    'options'       => stack_string('options'),
    'error'         => stack_string('testsuitecolerror'),
    'rawmark'       => stack_string('testsuitecolrawmark'),
    'expectedscore' => stack_string('testsuitecolexpectedscore'),
    'feedback'      => stack_string('testsuitefeedback'),
    'answernote'    => stack_string('answernote'),
    'expectednote'  => '',
);
if ($anstest !== 'ALL') {
    array_shift($columns);
}
$table = new flexible_table('stack_answertests');
$table->define_columns(array_keys($columns));
$table->define_headers(array_values($columns));
$table->set_attribute('class', 'generaltable generalbox stacktestsuite');
$table->define_baseurl($PAGE->url);
$table->setup();

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo html_writer::tag('p', stack_string('stackInstall_testsuite_intro'));
echo html_writer::tag('p', stack_string('stackInstall_testsuite_choose'));
echo $OUTPUT->single_select($PAGE->url, 'anstest', $availabletests, $anstest);

// Run the tests.
$allpassed = true;
$failedtable = array();
$notests = 0;
$start = microtime(true);

$oldtest = '';
foreach ($tests as $test) {

    $notests++;

    if ($oldtest != $test->name) {
        if ('' != $oldtest) {
            $table->add_separator();
        }
        $oldtest = $test->name;
    }

    if ($test->notes) {
        reset($columns);
        $firstcol = key($columns);
        $table->add_data_keyed(array($firstcol => $test->notes), 'notes');
    }

    set_time_limit(30);
    list($passed, $error, $rawmark, $feedback, $ansnote, $expectednote) = stack_answertest_test_data::run_test($test);
    $allpassed = $allpassed && $passed;

    if ($passed) {
        $class = 'pass';
        if (-1 === $test->expectedscore) {
            $class = 'expectedfail';
            $passedcol = stack_string('testsuiteknownfail');
        } else if (-2 === $test->expectedscore) {
            $class = 'expectedfail';
            $passedcol = stack_string('testsuiteknownfailmaths');
        } else {
            $passedcol = stack_string('testsuitepass');
        }
    } else {
        $class = 'fail';
        $passedcol = stack_string('testsuitefail');
    }

    $row = array(
        'name'          => $test->name,
        'passed'        => $passedcol,
        'studentanswer' => s($test->studentanswer),
        'teacheranswer' => s($test->teacheranswer),
        'options'       => s($test->options),
        'error'         => $error,
        'rawmark'       => $rawmark,
        'expectedscore' => $test->expectedscore,
        'feedback'      => format_text($feedback),
        'answernote'    => $ansnote,
        'expectednote'  => $expectednote,
    );
    if (!$passed) {
        $failedtable[] = $row;
    }

    $table->add_data_keyed($row, $class);
    flush();
}

$table->finish_output();

// Overall summary.
if ($notests > 0) {
    $took = (microtime(true) - $start);
    $rtook = round($took, 5);
    $pertest = round($took / $notests, 5);
    echo '<p>'.stack_string('testsuitenotests', array('no' => $notests));
    echo '<br/>'.stack_string('testsuiteteststook', array('time' => $rtook));
    echo '<br/>'.stack_string('testsuiteteststookeach', array('time' => $pertest));
    echo '</p>';

    $config = stack_utils::get_config();
    echo html_writer::tag('p', stack_string('healthcheckcache_' . $config->casresultscache));
    echo html_writer::tag('p',  stack_string('settingcasmaximaversion').': '.$config->maximaversion);
}

if ($anstest) {
    if ($allpassed) {
        echo $OUTPUT->heading(stack_string('stackInstall_testsuite_pass'), 2, 'pass');
    } else {
        echo $OUTPUT->heading(stack_string('stackInstall_testsuite_fail'), 2, 'fail');
        // Print a copy of the failing rows in a separate table.
        $tablef = new flexible_table('stack_answertests');
        $tablef->define_columns(array_keys($columns));
        $tablef->define_headers(array_values($columns));
        $tablef->set_attribute('class', 'generaltable generalbox stacktestsuite');
        $tablef->define_baseurl($PAGE->url);
        $tablef->setup();

        $class = 'fail';
        foreach ($failedtable as $row) {
            $tablef->add_data_keyed($row, $class);
            flush();
        }
        $table->finish_output();
    }
}

// Finish output.
echo $OUTPUT->footer();
