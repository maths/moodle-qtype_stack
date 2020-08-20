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
 * questions in a given context.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/bulktester.class.php');

// Increase memory limit: some users with very large numbers of questions/variants have needed this.
raise_memory_limit(MEMORY_HUGE);

// Get the parameters from the URL.
$contextid = required_param('contextid', PARAM_INT);
$context = context::instance_by_id($contextid);

$skippreviouspasses = optional_param('skippreviouspasses', false, PARAM_BOOL);
$urlparams = ['contextid' => $context->id];
if ($skippreviouspasses) {
    $urlparams['skippreviouspasses'] = 1;
}

// Login and check permissions.
require_login();
require_capability('qtype/stack:usediagnostictools', $context);
$PAGE->set_url('/question/type/stack/bulktest.php', $urlparams);
$PAGE->set_context($context);
$title = stack_string('bulktesttitle', $context->get_context_name());
$PAGE->set_title($title);

if ($context->contextlevel == CONTEXT_MODULE) {
    // Calling $PAGE->set_context should be enough, but it seems that it is not.
    // Therefore, we get the right $cm and $course, and set things up ourselves.
    $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);
    $PAGE->set_cm($cm, $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST));
}

// Create the helper class.
$bulktester = new stack_bulk_tester();

// Release the session, so the user can do other things while this runs.
\core\session\manager::write_close();

// Display.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Run the tests.
list($allpassed, $failing) = $bulktester->run_all_tests_for_context(
        $context, 'web', false, $skippreviouspasses);

// Display the final summary.
$bulktester->print_overall_result($allpassed, $failing);

// If we used the cache, report state.
if (class_exists('stack_cas_connection_db_cache')) {
    echo html_writer::tag('p', stack_string('healthcheckcachestatus',
            stack_cas_connection_db_cache::entries_count($DB)));
}

echo $OUTPUT->footer();
