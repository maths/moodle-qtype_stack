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
 * This script runs all the quesion tests for all deployed variants of all
 * questions in all contexts in the Moodle site. This is intended for regression
 * testing, before you release a new version of STACK to your site.
 *
 * @package   qtype_stack
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/bulktester.class.php');


// Get the parameters from the URL. This is an option to restart the process
// in the middle. Useful if it crashes.
$startfromcontextid = optional_param('startfromcontextid', 0, PARAM_INT);

$skippreviouspasses = optional_param('skippreviouspasses', false, PARAM_BOOL);
$urlparams = array();
if ($skippreviouspasses) {
    $urlparams['skippreviouspasses'] = 1;
}
if ($startfromcontextid) {
    $urlparams['startfromcontextid'] = $startfromcontextid;
}

// Login and check permissions.
$context = context_system::instance();
require_login();
require_capability('moodle/site:config', $context);
$PAGE->set_url('/question/type/stack/bulktestall.php', $urlparams);
$PAGE->set_context($context);
$title = stack_string('bulktesttitle', $context->get_context_name());
$PAGE->set_title($title);

require_login();

// Create the helper class.
$bulktester = new stack_bulk_tester();
$allpassed = true;
$allfailing = array();
$skipping = $startfromcontextid != 0;

// Release the session, so the user can do other things while this runs.
\core\session\manager::write_close();

// Display.
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 1);

// Run the tests.
foreach ($bulktester->get_stack_questions_by_context() as $contextid => $numstackquestions) {
    if ($skipping && $contextid != $startfromcontextid) {
        continue;
    }
    $skipping = false;
    $testcontext = context::instance_by_id($contextid);

    echo $OUTPUT->heading(stack_string('bulktesttitle', $testcontext->get_context_name()));
    echo html_writer::tag('p', html_writer::link(
            new moodle_url('/question/type/stack/bulktestall.php', $urlparams),
            stack_string('bulktestcontinuefromhere')));

    list($passed, $failing) = $bulktester->run_all_tests_for_context($testcontext, 'web', false, $skippreviouspasses);
    $allpassed = $allpassed && $passed;
    foreach ($failing as $key => $arrvals) {
        // Guard clause here to future proof any new fields from the bulk tester.
        if (!array_key_exists($key, $allfailing)) {
            $allfailing[$key] = array();
        }
        $allfailing[$key] = array_merge($allfailing[$key], $arrvals);
    }
}

// Display the final summary.
$bulktester->print_overall_result($allpassed, $allfailing);
echo $OUTPUT->footer();
