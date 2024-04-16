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
require_once(__DIR__ . '/../stack/mathsoutput/fact_sheets.class.php');

require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir .'/filelib.php');
require_once($CFG->libdir .'/tablelib.php');

require_once($CFG->dirroot . '/question/type/stack/locallib.php');
require_once($CFG->dirroot . '/question/type/stack/stack/options.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/answertest/controller.class.php');
require_once($CFG->dirroot . '/question/type/stack/tests/fixtures/answertestfixtures.class.php');

// Get the list of available tests.
$availabletests = stack_answertest_test_data::get_available_tests();
// Create a separate table for each test: breaks up the page better.

foreach ($availabletests as $anstest) {
    // One file per answer test.
    ob_start( );
    echo "\n\n" . html_writer::tag('h2', $anstest);

    $tests = stack_answertest_test_data::get_tests_for($anstest);

    // Set up the results table.
    $columns = [
        'name'          => stack_string('answertest_ab'),
        'passed'        => stack_string('testsuitecolpassed'),
        'studentanswer' => stack_string('studentanswer'),
        'teacheranswer' => stack_string('teacheranswer'),
        'options'       => stack_string('options_short'),
        'rawmark'       => stack_string('testsuitecolmark'),
        'answernote'    => stack_string('answernote'),
    ];

    $table = new flexible_table('stack_answertests');
    $table->define_columns(array_keys($columns));
    $table->define_headers(array_values($columns));
    $table->set_attribute('class', 'generaltable generalbox stacktestsuite');
    $table->define_baseurl('');
    $table->setup();

    // Run the tests.
    $allpassed = true;
    $failedtable = [];
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
            // This is a slight cludge to get multiple columns in a row.
            $notes = html_writer::tag('td', $test->notes, ['colspan' => '6']);
            $table->add_data([$notes], 'notes');
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
            } else if (-3 === $test->expectedscore) {
                $class = 'expectedfail';
                $passedcol = stack_string('testsuiteknownfailmaths');
            } else {
                $passedcol = stack_string('testsuitepass');
            }
        } else {
            $class = 'fail';
            $passedcol = stack_string('testsuitefail');
        }

        $sans = implode("\n", str_split(s($test->studentanswer), 30));
        $tans = implode("\n", str_split(s($test->teacheranswer), 30));
        $topt = '';
        if ($test->options != '') {
            $topt = html_writer::tag('pre', implode("\n", str_split(s($test->options), 15)));
        }
        $mark = $test->expectedscore;
        if ($rawmark !== $test->expectedscore && $test->expectedscore > 0) {
            $mark = $rawmark . ' <> ' . $test->expectedscore;
        }
        $row = [
            'name'          => $test->name,
            'passed'        => $passedcol,
            'studentanswer' => html_writer::tag('pre', $sans),
            'teacheranswer' => html_writer::tag('pre', $tans),
            'options'       => $topt,
            'rawmark'       => $mark,
            'answernote'    => $ansnote,
        ];
        if (!$passed) {
            $row['answernote'] .= html_writer::tag('pre', $trace);
            $failedtable[] = $row;
        }

        $table->add_data_keyed($row, $class);

        // Add errors as a separate row for better spacing.
        $row = [];
        $row[] = html_writer::tag('td', '', ['colspan' => '2']);
        $row[] = html_writer::tag('td', $error, ['colspan' => '4']);
        if ($error != '') {
            $table->add_data($row, $class);
        }
        // Add feeback as a separate row for better spacing.
        $row = [];
        $row[] = html_writer::tag('td', '', ['colspan' => '2']);
        $row[] = html_writer::tag('td', $feedback, ['colspan' => '4']);
        if ($feedback != '' && $feedback != $error) {
            $table->add_data($row, $class);
        }
    }
    $table->finish_output();

    $output = ob_get_clean( );

    // This is to break up the resulting single line in the text file.
    // Otherwise editors, git, etc. have a miserable time.
    $output = str_replace('<td class=', "\n  <td class=", $output);
    $output = str_replace('<tr class=', "\n<tr class=", $output);
    $output = str_replace("</tr>", "\n</tr>", $output);
    $output = str_replace(",EQUIVCHAR", ", EQUIVCHAR", $output);
    $output = str_replace(",EMPTYCHAR", ", EMPTYCHAR", $output);
    $output = str_replace(",CHECKMARK", ", CHECKMARK", $output);
    // If we don't strip id tags the whole file will change everytime we add a test!
    // String too long for a single regular expression match.
    $lines = explode("\n", $output);
    $pat = ['/\sid="stack_answertests_r\d+_c\d+"/',
                 '/\sid="stack_answertests_r\d+"/'];
    $rep = ['', ''];
    foreach ($lines as $key => $line) {
        $lines[$key] = preg_replace($pat, $rep, $line);
    }
    $output = implode("\n", $lines);
    $output = '# ' . $anstest . ': ' . stack_string('stackDoc_AnswerTestResults') . "\n\n" . $output;

    file_put_contents('../doc/en/Authoring/Answer_Tests/Results/'. $anstest .'.md', $output);
}

// Output the factsheet.

$page = get_string('fact_sheet_preamble', 'qtype_stack');
$page .= stack_fact_sheets::generate_docs();
file_put_contents('../doc/en/Authoring/Fact_sheets.md', $page);
