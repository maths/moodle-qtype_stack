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
 * This script runs all the quesion tests for all deployed versions of all
 * questions in all contexts in the Moodle site. This is intended for regression
 * testing, before you release a new version of STACK to your site.
 *
 * @package    qtype_stack
 * @subpackage cli
 * @copyright  2019 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/bulktester.class.php');

// Get cli options.
list($options, $unrecognized) = cli_get_params(array('help' => false, 'id' => false), array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo "This script runs all the quesion tests for all deployed versions of all
questions in all contexts in the Moodle site. This is intended for regression
testing, before you release a new version of STACK to your site.\n
Use with --id=n to start generation from question id is n.\n";
    exit(0);
}

$context = context_system::instance();
// Create the helper class.
$bulktester = new stack_bulk_tester();
$allpassed = true;
$allfailing = array();

// Run the tests.
$testno = 0;
$contexts = $bulktester->get_stack_questions_by_context();

// Take only the contexts from the one containing the question id.
$partialcontext = false;
if ($options['id']) {
    $usecontexts = array();
    $found = false;
    foreach ($contexts as $contextid => $numstackquestions) {
        $testcontext = context::instance_by_id($contextid);

        $categories = question_category_options(array($testcontext));
        $categories = reset($categories);
        foreach ($categories as $key => $category) {
            list($categoryid) = explode(',', $key);
            $questions = $bulktester->get_stack_questions($categoryid);
            if (array_key_exists($options['id'], $questions)) {
                $found = true;
                $partialcontext = $contextid;
            }
        }
        if ($found) {
            $usecontexts[$contextid] = $numstackquestions;
        }
    }
    $contexts = $usecontexts;
}

foreach ($contexts as $contextid => $numstackquestions) {

    $testcontext = context::instance_by_id($contextid);

    echo "\n\n# " . $contextid . ": " . stack_string('bulktesttitle', $testcontext->get_context_name());

    if ($partialcontext === $contextid) {
        list($passed, $failing) = $bulktester->run_all_tests_for_context($testcontext, 'cli', (int) $options['id']);
    } else {
        list($passed, $failing) = $bulktester->run_all_tests_for_context($testcontext, 'cli', false);
    }

    echo "\n";
    if ($allpassed) {
        echo "** " . stack_string('stackInstall_testsuite_pass');
    } else {
        echo "** " . stack_string('stackInstall_testsuite_fail');
    }
    echo "\n";

    $allpassed = $allpassed && $passed;
    echo "\n";
    foreach ($failing as $key => $arrvals) {
        if ($arrvals !== array()) {
            echo "\n* " . stack_string('stackInstall_testsuite_' . $key) . "\n";
            echo implode("\n", $arrvals);
        }
    }
}

echo "\n\n";
exit(0);
