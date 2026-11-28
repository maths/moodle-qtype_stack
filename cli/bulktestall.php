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
 * With --library or --library-php it tests the STACK question library instead,
 * importing each file in turn as a temporary question.
 *
 * @package    qtype_stack
 * @subpackage cli
 * @copyright  2019 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/bulktester.class.php');

$start = microtime(true);

// Get cli options.
[$options, $unrecognized] = cli_get_params(
    [
        'help' => false, 'id' => false, 'remote' => false, 'addtags' => false,
        'library' => false, 'library-php' => false, 'path' => false, 'dir' => false, 'json' => false,
        'exitcode' => false,
    ],
    ['h' => 'help']
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo "STACK CLI bulk test.\n
This script runs all the quesion tests for all deployed versions of all
questions in all contexts in the Moodle site. This is intended for regression
testing, before you release a new version of STACK to your site.\n
Use with --id=n to start generation from question id is n.\n
Use with --addtags to add [[todo]] tags to questions needing attention.\n
Use with --library-php to test the STACK question library (samplequestions/stacklibrary)
instead: each file is imported as a temporary question, which is checked by the PHP
validation and the upgrade checks, then deleted.\n
Use with --library to do the same, and also run all the question tests on all
deployed seeds of each question that passes those checks.\n
Use with --dir=folder to test the question files in folder (any folder of Moodle XML files)
instead of the STACK question library.\n
Use with --path=dir to test only the library files below dir (relative to the library).\n
Use with --json to print the report on every question as JSON, instead of the summary.\n
Use with --exitcode to exit with status 1 if anything failed (by default the exit status is 0).\n";
    exit(0);
}
$librarymode = $options['library'] || $options['library-php'];
if ($librarymode && ($options['id'] || $options['addtags'] || $options['remote'])) {
    cli_error('--library and --library-php cannot be used with --id, --addtags or --remote.');
}
if (($options['path'] || $options['dir']) && !$librarymode) {
    cli_error('--path and --dir need --library or --library-php.');
}
if ($options['remote']) {
    if (!$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary)) {
        throw new dml_exception('dbdriverproblem', "Unknown driver $CFG->dblibrary/$CFG->dbtype");
    }
    // phpcs:disable
    //$DB->connect('live.database.host.name', 'read_only_user', 'pa55w0rd', 'live_database_name', 'mdl_', $CFG->dboptions);
    // phpcs:enable
}

$outputmode = $options['json'] ? 'json' : 'cli';
// Create the helper class.
$bulktester = new stack_bulk_tester();
$formatter = new stack_bulk_tester_formatter($outputmode);
$allpassed = true;

if ($librarymode) {
    // Importing questions needs a user who may create them.
    \core\session\manager::set_user(get_admin());
    if ($options['dir']) {
        // Relative to the folder this script was run from.
        $librarydir = realpath((string) $options['dir']);
        if ($librarydir === false || !is_dir($librarydir)) {
            cli_error('--dir is not a folder: ' . $options['dir']);
        }
    } else {
        $librarydir = $CFG->dirroot . '/question/type/stack/samplequestions/stacklibrary';
    }
    $librarydir = stack_utils::convert_slash_paths($librarydir);
    $subpath = $options['path'] ? (string) $options['path'] : '';
    if ($outputmode == 'cli') {
        echo "\n\n# " . stack_string('bulktestlibrarytitle', $librarydir . ($subpath ? '/' . $subpath : ''));
    }
    try {
        [$allpassed, $failing] = $bulktester->run_all_tests_for_library(
            $librarydir,
            $subpath,
            $outputmode,
            (bool) $options['library']
        );
    } catch (coding_exception $e) {
        cli_error($e->getMessage());
    }
    if ($outputmode == 'cli') {
        $formatter->print_cli_result($allpassed, $failing);
    }
} else {
    // Run the tests.
    $contexts = $bulktester->get_stack_questions_by_context();

    // Take only the contexts from the one containing the question id.
    $partialcontext = false;
    if ($options['id']) {
        $usecontexts = [];
        $found = false;
        foreach ($contexts as $contextid => $numstackquestions) {
            $testcontext = context::instance_by_id($contextid);

            $categories = qbank_managecategories\helper::question_category_options([$testcontext]);
            $categories = reset($categories);
            foreach ($categories as $key => $category) {
                [$categoryid] = explode(',', $key);
                $questions = $bulktester->stack_questions_in_category($categoryid);
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

        if ($outputmode == 'cli') {
            echo "\n\n# " . $contextid . ": " . stack_string('bulktesttitle', $testcontext->get_context_name());
        }

        [$passed, $failing] = $bulktester->run_all_tests_for_context(
            $testcontext,
            null,
            $outputmode,
            ($partialcontext === $contextid) ? (int) $options['id'] : false,
            false,
            (bool) $options['addtags'],
            false
        );

        $allpassed = $allpassed && $passed;
        if ($outputmode == 'cli') {
            $formatter->print_cli_result($passed, $failing);
        }
    }
}

$took = (microtime(true) - $start);
$rtook = round($took, 5);

if ($outputmode == 'json') {
    echo stack_bulk_tester_formatter::to_json($allpassed, $bulktester->get_reports(), $rtook) . "\n";
    exit(($options['exitcode'] && !$allpassed) ? 1 : 0);
}

echo "\n\n";

if ($allpassed) {
    echo "** " . stack_string('stackInstall_testsuite_pass');
} else {
    echo "** " . stack_string('stackInstall_testsuite_fail');
}
echo "\n";

echo "Time taken: " . $rtook;
echo "\n\n";

exit(($options['exitcode'] && !$allpassed) ? 1 : 0);
