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

require_once(__DIR__.'/../../../config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/options.class.php');
require_once(__DIR__ . '/stack/cas/castext.class.php');
require_once(__DIR__ . '/stack/cas/casstring.class.php');
require_once(__DIR__ . '/stack/cas/cassession.class.php');
require_once(__DIR__ . '/stack/cas/connector.dbcache.class.php');
require_once(__DIR__ . '/stack/cas/installhelper.class.php');
require_once(__DIR__ . '/stack/cas/platforms.php');


// Check permissions.
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Set up page.
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/healthcheck.php');
$title = stack_string('healthcheck');
$PAGE->set_title($title);

// Clear the cache if requested.
if (data_submitted() && optional_param('clearcache', false, PARAM_BOOL)) {
    require_sesskey();
    stack_cas_connection_db_cache::clear_cache($DB);
    redirect($PAGE->url);
}

// Do this early because any errors from this will prevent image creation.
$platform = stack_platform_base::get_current();
$checkrv = $platform->check_maxima_install();
$platformerrors = $checkrv['errors']; $platformwarnings = $checkrv['warnings'];
$errmsg = ""; $warnmsg = "";
if (count($platformerrors) > 0) {
    $errmsg = "<ul><li>" . implode('</li><li>', $platformerrors) . '</li></ul>';
}
if (count($platformwarnings) > 0) {
    $warnmsg .= "<ul><li>" . implode('</li><li>', $platformwarnings) . '</li></ul>';
}

// Create and store Maxima image if requested.
if (data_submitted() && optional_param('createmaximaimage', false, PARAM_BOOL)) {
    require_sesskey();
    stack_cas_connection_db_cache::clear_cache($DB);
    $ok = true;
    if (count($platformerrors) > 0) {
        $ok = false;
    } else {
        list($ok, $errmsg)  = stack_cas_configuration::create_auto_maxima_image();
    }
    if ($ok) {
        redirect($PAGE->url, stack_string('healthautomaxopt_succeeded'), null,
                \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($PAGE->url, stack_string('healthautomaxopt_failed', array('errmsg' => $errmsg . $warnmsg)), null,
                \core\output\notification::NOTIFY_ERROR);
    }
}

$config = stack_utils::get_config();

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// This array holds summary info, for a table at the end of the pager.
$summary = array();
$summary[] = array('', $platform->get_desc() );

// LaTeX.
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
            array('filter' => stack_maths::configured_output_name(), 'url' => $settingsurl->out())));
}

// Maxima config.
echo $OUTPUT->heading(stack_string('healthcheckconfig'), 3);

// Try to list available versions of Maxima (linux only, without the DB).
if ($platform->can_list_maxima_versions()) {
    $connection = stack_connection_helper::make()->get_raw();
    echo html_writer::tag('pre', $connection->get_maxima_available());
}

// Check for location of Maxima.
$maximalocation = $platform->get_maxima_install();
if (true !== $maximalocation) {
    $message = stack_string('healthcheckconfigintro1a').' '
            .html_writer::tag('tt', $maximalocation ? $maximalocation : "* unknown *");
    echo html_writer::tag('p', $message);
    $summary[] = array($maximalocation !== null, $message);
}

// Report platform configuration errors and warnings from earlier above.
if (count($platformerrors) > 0) {
    echo $OUTPUT->box($OUTPUT->heading(stack_string('errors'), 5) . $errmsg,
            'alert alert-error alert-block');
    $summary[] = array(false, stack_string('healthcheckplatformconfigerrors'));
}

if (count($platformwarnings) > 0) {
    echo $OUTPUT->box($OUTPUT->heading(stack_string('warnings'), 5) . $warnmsg,
            'alert alert-warning alert-block');
    $summary[] = array(null, stack_string('healthcheckplatformconfigwarnings'));
}

// Check if the current options for library packages are permitted (maximalibraries).
list($valid, $message) = stack_cas_configuration::validate_maximalibraries();
if (!$valid) {
    echo html_writer::tag('p', $message);
    $summary[] = array(false, $message);
}

// Try to connect to create maxima local.
if ($platformerrors) {
    echo html_writer::tag('p', stack_string('healthcheckconfigintro2skip'));
} else {
    echo html_writer::tag('p', stack_string('healthcheckconfigintro2'));
    stack_cas_configuration::create_maximalocal();
}

echo html_writer::tag('textarea', stack_cas_configuration::generate_maximalocal_contents(),
        array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '32', 'cols' => '100'));

// Maxima config.
echo $OUTPUT->heading(stack_string('healthcheckmaximabat'), 3);
if ($platform->requires_launch_script()) {
    echo html_writer::tag('p', stack_string('healthcheckmaximabatinfo', $platform->get_launch_script_pathame()));
    if ($platform->check_launch_script()) {
        $message = stack_string('healthcheckmaximabatok', $platform->get_launch_script_pathame());
        echo html_writer::tag('p', $message);
        $summary[] = array(true, $message);
    } else {
        $message = stack_string('healthcheckmaximabaterr', $platform->get_launch_script_pathame());
        echo html_writer::tag('p', $message);
        $summary[] = array(false, $message);
    }
} else {
    $message = stack_string('healthcheckmaximabatnotneeded');
    echo html_writer::tag('p', $message);
    $summary[] = array(null, $message);
}

// Test an *uncached* call to the CAS.  I.e. a genuine call to the process.
echo $OUTPUT->heading(stack_string('healthuncached'), 3);
echo html_writer::tag('p', stack_string('healthuncachedintro'));
list($message, $genuinedebug, $result) = stack_connection_helper::stackmaxima_genuine_connect();
$summary[] = array($result, $message);
echo html_writer::tag('p', $message);
echo output_debug(stack_string('debuginfo'), $genuinedebug);
$genuinecascall = $result;

// Test Maxima connection.
// Intentionally use get_string for the sample CAS and plots, so we don't render
// the maths too soon.
output_cas_text(stack_string('healthcheckconnect'),
        stack_string('healthcheckconnectintro'), get_string('healthchecksamplecas', 'qtype_stack'));

$tryoptimising = ! empty($_REQUEST['trialopt']) && $_REQUEST['trialopt'];

// If we have a platform than can be optimsed and we are testing the raw connection then we should
// attempt to automatically create an optimized maxima image on the system.
if ($tryoptimising && $platform->can_be_auto_optimised() && $genuinecascall) {
    echo $OUTPUT->heading(stack_string('healthautomaxopt'), 3);
    echo html_writer::tag('p', stack_string('healthautomaxoptintro'));
    list($message, $debug, $result, $commandline) = stack_connection_helper::stackmaxima_auto_maxima_optimise($genuinedebug);
    $summary[] = array($result, $message);
    echo html_writer::tag('p', $message);
    echo output_debug(stack_string('debuginfo'), $debug);
}


// Test the version of the STACK libraries that Maxima is using.
// When Maxima is being run pre-compiled (maxima-optimise) or on a server,
// it is possible for the version of the Maxima libraries to get out of synch
// with the qtype_stack code.

echo $OUTPUT->heading(stack_string('healthchecksstackmaximaversion'), 3);
list($message, $details, $result) = stack_connection_helper::stackmaxima_version_healthcheck();
$summary[] = array($result, stack_string($message, $details));
echo html_writer::tag('p', stack_string($message, $details));

// Test plots.
output_cas_text(stack_string('healthcheckplots'),
        stack_string('healthcheckplotsintro'), get_string('healthchecksampleplots', 'qtype_stack'));

// State of the cache.
echo $OUTPUT->heading(stack_string('settingcasresultscache'), 3);
$message = stack_string('healthcheckcache_' . $config->casresultscache);
$summary[] = array(null, $message);
echo html_writer::tag('p', $message);
if ('db' == $config->casresultscache) {
    echo html_writer::tag('p', stack_string('healthcheckcachestatus',
            stack_cas_connection_db_cache::entries_count($DB)));
    echo $OUTPUT->single_button(
            new moodle_url($PAGE->url, array('clearcache' => 1, 'sesskey' => sesskey())),
            stack_string('clearthecache'));
}

// Option to auto-create the Maxima image and store the results.
if ($platform->can_be_auto_optimised()) {
    echo $OUTPUT->single_button(
        new moodle_url($PAGE->url, array('createmaximaimage' => 1, 'sesskey' => sesskey())),
        stack_string('healthcheckcreateimage'));
}


echo '<hr />';
$tab = '';
foreach ($summary as $line) {
    $tl   = '';
    if (true === $line[0]) {
        $tl  .= html_writer::tag('td', '<span class="ok">OK</span>');
    } else if (false === $line[0]) {
        $tl  .= html_writer::tag('td', '<span class="error">FAILED</span>');
    } else {
        $tl  .= html_writer::tag('td', ' ');

    }
    $tl  .= html_writer::tag('td', $line[1]);
    $tab .= html_writer::tag('tr', $tl)."\n";
}
echo html_writer::tag('table', $tab);

echo $OUTPUT->footer();

function output_cas_text($title, $intro, $castext) {
    global $OUTPUT;

    echo $OUTPUT->heading($title, 3);
    echo html_writer::tag('p', $intro);
    echo html_writer::tag('pre', s($castext));

    $ct = new stack_cas_text($castext, null, 0, 't');

    echo html_writer::tag('p', stack_ouput_castext($ct->get_display_castext()));
    echo output_debug(stack_string('errors'), $ct->get_errors());
    echo output_debug(stack_string('debuginfo'), $ct->get_debuginfo());
}


function output_debug($title, $message) {
    global $OUTPUT;

    if (!$message) {
        return;
    }

    return $OUTPUT->box($OUTPUT->heading($title) . $message);
}
