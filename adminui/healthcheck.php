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
 * This script helps  that the stack is installed correctly, and that
 * all the parts are working properly, including the conection to the CAS,
 * graph plotting, and equation rendering.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__.'/../../../../config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/cas/connector.healthcheck.class.php');

// Check permissions.
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Set up page.
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/adminui/healthcheck.php');
$title = stack_string('healthcheck');
$PAGE->set_title($title);

$config = stack_utils::get_config();

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Clear the cache if requested.
if (data_submitted() && optional_param('clearcache', false, PARAM_BOOL)) {
    require_sesskey();
    stack_cas_connection_db_cache::clear_cache($DB);
    \core\notification::success(stack_string('clearedthecache'));
    echo $OUTPUT->continue_button($PAGE->url);
    echo $OUTPUT->footer();
    exit;
}

// Create and store Maxima image if requested.
if (data_submitted() && optional_param('createmaximaimage', false, PARAM_BOOL)) {
    require_sesskey();
    echo $OUTPUT->heading(stack_string('healthautomaxopt'));
    stack_cas_connection_db_cache::clear_cache($DB);
    list($ok, $errmsg) = stack_cas_configuration::create_auto_maxima_image();
    if ($ok) {
        \core\notification::success(stack_string('healthautomaxopt_succeeded'));
    } else {
        \core\notification::error(stack_string('healthautomaxopt_failed', ['errmsg' => $errmsg]));
    }
    echo $OUTPUT->continue_button($PAGE->url);
    echo $OUTPUT->footer();
    exit;
}

// From this point do all health-related actions.

// Mbstring.  This is an install requirement, rather than a CAS healtcheck.
if (!extension_loaded('mbstring')) {
    echo $OUTPUT->heading(stack_string('healthchecknombstring'), 3);
    echo $OUTPUT->footer();
    exit;
}


// Maxima config.
$healthcheck = new stack_cas_healthcheck($config);
$tab = '';
foreach ($healthcheck->get_test_results() as $test) {
    $tl   = '';
    if (true === $test['result']) {
        $tl  .= html_writer::tag('td', stack_string('testsuitepass'));
    } else if (false === $test['result']) {
        $tl  .= html_writer::tag('td', stack_string('testsuitefail'));
    } else {
        $tl  .= html_writer::tag('td', ' ');
    }
    $tl  .= html_writer::tag('td', $test['summary']);
    $tab .= html_writer::tag('tr', $tl)."\n";
}
echo html_writer::tag('table', $tab);
if ($healthcheck->get_overall_result()) {
    echo html_writer::tag('p', stack_string('healthcheckpass'), ['class' => 'overallresult pass']);
} else {
    echo html_writer::tag('p', stack_string('healthcheckfail'), ['class' => 'overallresult fail']);
}
echo html_writer::tag('p', get_string('healthcheckfaildocs', 'qtype_stack',
    ['link' => (string) new moodle_url('/question/type/stack/doc/doc.php/Installation/Testing_installation.md')])
    );

// State of the cache.
if ('db' == $config->casresultscache) {
    echo html_writer::tag('p', stack_string('healthcheckcachestatus',
        stack_cas_connection_db_cache::entries_count($DB)));
    echo $OUTPUT->single_button(
        new moodle_url($PAGE->url, ['clearcache' => 1, 'sesskey' => sesskey()]),
        stack_string('clearthecache'));
}

// Option to auto-create the Maxima image and store the results.
if ($config->platform != 'win') {
    echo $OUTPUT->single_button(
        new moodle_url($PAGE->url, ['createmaximaimage' => 1, 'sesskey' => sesskey()]),
        stack_string('healthcheckcreateimage'));
}

echo '<hr />';
// LaTeX. This is an install requirement, rather than a CAS healtcheck.
echo $OUTPUT->heading(stack_string('healthchecklatex'), 3);
echo html_writer::tag('p', stack_string('healthcheckmathsdisplaymethod',
    stack_maths::configured_output_name()));
echo html_writer::tag('p', stack_string('healthchecklatexintro'));

echo html_writer::tag('dt', stack_string('texdisplaystyle'));
echo html_writer::tag('dd', stack_string('healthchecksampledisplaytex'));

echo html_writer::tag('dt', stack_string('texinlinestyle'));
echo html_writer::tag('dd', stack_string('healthchecksampleinlinetex'));

if ($config->mathsdisplay === 'mathjax') {
    echo html_writer::tag('p', stack_string('healthchecklatexmathjax'));
} else {
    $settingsurl = new moodle_url('/admin/filters.php');
    echo html_writer::tag('p', stack_string('healthcheckfilters',
        ['filter' => stack_maths::configured_output_name(), 'url' => $settingsurl->out()]));
}

// Output details.
foreach ($healthcheck->get_test_results() as $test) {
    if ($test['details'] !== null) {
        echo '<hr />';
        $heading = stack_string($test['tag']);
        if ($test['result'] === false) {
            $heading = stack_string('testsuitefail') . ' ' . $heading;
        }
        echo $OUTPUT->heading($heading, 3);
        echo $test['details'];
    }
}

echo $OUTPUT->footer();
