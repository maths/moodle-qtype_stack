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
 * This script runs attempts to "validate" a list of potential answer strings from students and verifies the results.
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
require_once(__DIR__ . '/stack/cas/cassession2.class.php');
require_once(__DIR__ . '/stack/input/factory.class.php');
require_once(__DIR__ . '/tests/fixtures/inputfixtures.class.php');

// Get the parameters from the URL.
$questionid = optional_param('questionid', null, PARAM_INT);

// Authentication. Because of the cache, it is safe to make this available to any
// logged in user.
require_login();
require_capability('qtype/stack:usediagnostictools', context_system::instance());

// Set up the page object.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/question/type/stack/studentinputs.php');
$title = stack_string('stackInstall_input_title');
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);

// Set up the results table.
$columns = array(
    'passed'             => stack_string('testsuitecolpassed'),
    'studentanswer'      => stack_string('studentanswer'),
    'phpvalid'           => stack_string('phpvalid'),
    'phpcasstring'       => stack_string('phpcasstring'),
    'answernote'         => stack_string('answernote'),
    'error'              => stack_string('phpsuitecolerror'),
    'casvalid'           => stack_string('casvalid'),
    'casvalue'           => stack_string('casvalue'),
    'casdisplay'         => stack_string('casdisplay'),
    'caserrors'          => stack_string('cassuitecolerrors'),
);

$table = new flexible_table('stack_answertests');
$table->define_columns(array_keys($columns));
$table->define_headers(array_values($columns));
$table->set_attribute('class', 'generaltable generalbox stacktestsuite');
$table->define_baseurl($PAGE->url);
$table->setup();

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo html_writer::tag('p', stack_string('stackInstall_input_intro'));

$tests = stack_inputvalidation_test_data::get_all();
// Run the tests.
$allpassed = true;
$notests = 0;
$start = microtime(true);

foreach ($tests as $test) {

    $notests++;

    if ($test->notes) {
        reset($columns);
        $firstcol = key($columns);
        $table->add_data_keyed(array($firstcol => $test->notes), 'notes');
    }

    set_time_limit(30);
    $testresult = stack_inputvalidation_test_data::run_test($test);
    $passed = $testresult->passed;
    $phpvalid = $testresult->phpvalid;
    $phpcasstring = $testresult->phpcasstring;
    $answernote = $testresult->ansnotes;
    $error = $testresult->errors;
    $casvalid = $testresult->casvalid;
    $caserrors = $testresult->caserrors;
    $casdisplay = $testresult->casdisplay;
    $casvalue = $testresult->casvalue;

    $allpassed = $allpassed && $passed;

    if ($passed) {
        $class = 'pass';
        $passedcol = stack_string('testsuitepass');
    } else {
        $class = 'fail';
        $passedcol = stack_string('testsuitefail');
    }

    $display = '';
    if ('' != $casdisplay) {
        $display = '\('.$casdisplay.'\)';
    }
    $row = array(
        'passed'             => $passedcol,
        'studentanswer'      => s($test->rawstring),
        'phpvalid'           => s($phpvalid),
        'phpcasstring'       => s($phpcasstring),
        'answernote'         => $answernote,
        'error'              => $error,
        'casvalid'           => s($casvalid),
        'casvalue'           => $casvalue,
        'casdisplay'         => format_text(stack_maths::process_lang_string(s($display))) .
                html_writer::tag('pre', s($casdisplay)) . "\n",
        'caserrors'          => $caserrors,
    );
    $table->add_data_keyed($row, $class);
    flush();
}

$table->finish_output();

// Overall summary.
$took = (microtime(true) - $start);
$rtook = round($took, 5);
$pertest = round($took / $notests, 5);
echo '<p>'.stack_string('testsuitenotests', array('no' => $notests));
echo '<br/>'.stack_string('testsuiteteststook', array('time' => $rtook));
echo '<br/>'.stack_string('testsuiteteststookeach', array('time' => $pertest));
echo '</p>';

$config = get_config('qtype_stack');
echo html_writer::tag('p', stack_string('healthcheckcache_' . $config->casresultscache));

// Overall summary.
if ($allpassed) {
    echo $OUTPUT->heading(stack_string('stackInstall_testsuite_pass'), 2, 'pass');
} else {
    echo $OUTPUT->heading(stack_string('stackInstall_testsuite_fail'), 2, 'fail');
}

// Finish output.
echo $OUTPUT->footer();
