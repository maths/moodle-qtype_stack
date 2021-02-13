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

define('CLI_SCRIPT', true);

// This file allows developers to update the static docs which illustrate what
// the answer tests actually do.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir .'/filelib.php');
require_once($CFG->libdir .'/tablelib.php');

require_once($CFG->dirroot . '/question/type/stack/locallib.php');
require_once($CFG->dirroot . '/question/type/stack/stack/options.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/answertest/controller.class.php');
require_once($CFG->dirroot . '/question/type/stack/tests/fixtures/answertestfixtures.class.php');

$anstest = 'ALL';

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
$table->define_baseurl('');
$table->setup();

// Run the tests.
$allpassed = true;
$failedtable = array();
$notests = 0;
$start = microtime(true);

ob_start( );

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
    list($passed, $error, $rawmark, $feedback, $ansnote, $expectednote, $trace)
    = stack_answertest_test_data::run_test($test);
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
        $row['answernote'] .= html_writer::tag('pre', $trace);
        $failedtable[] = $row;
    }

    $table->add_data_keyed($row, $class);
}

$table->finish_output();

$output = ob_get_clean( );

$output = stack_string('stackDoc_AnswerTestResults') . "\n\n" . $output;

// Add the Maxima version at the end of the table for reference.
$settings = get_config('qtype_stack');
$libs = array_map('trim', explode(',', $settings->maximalibraries));
asort($libs);
$libs = implode(', ', $libs);
$vstr = $settings->version . ' (' . $libs . ')';
$output .= '<br/>'.stack_string('stackDoc_version', $vstr);

file_put_contents('../doc/en/Authoring/Answer_tests_results.md', $output);