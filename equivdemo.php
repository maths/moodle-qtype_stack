<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * @copyright  2016 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/options.class.php');
require_once(__DIR__ . '/stack/cas/castext.class.php');
require_once(__DIR__ . '/stack/cas/casstring.class.php');
require_once(__DIR__ . '/stack/cas/cassession.class.php');
require_once(__DIR__ . '/stack/cas/keyval.class.php');
require_once(__DIR__ . '/stack/cas/installhelper.class.php');
require_once(__DIR__ . '/stack/input/inputbase.class.php');
require_once(__DIR__ . '/stack/input/equiv/equiv.class.php');
require_once(__DIR__ . '/tests/fixtures/equivfixtures.class.php');

// Get the parameters from the URL.
$questionid = optional_param('questionid', null, PARAM_INT);

if (!$questionid) {
    require_login();
    $context = context_system::instance();
    require_capability('qtype/stack:usediagnostictools', $context);
    $urlparams = array();

} else {
    // Load the necessary data.
    $questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
    $question = question_bank::load_question($questionid);

    // Process any other URL parameters, and do require_login.
    list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

    // Check permissions.
    question_require_capability_on($questiondata, 'view');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/equivdemo.php', $urlparams);
$title = "Equivalence reasoning test cases";
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

/* This page is not intended to ever be incoprporated into STACK as
 * part of the main codebase.  It is here to test the features of the
 * proposed equivalence reasoning input type.  This script is heavily
 * based on caschat.php
 */

$data = new stack_equiv_test_data();
$samplearguments = $data->rawdata;

/* Loop over each argument, evaluate it and display the results. */

$options = new stack_options();
$options->set_site_defaults();
$options->set_option('simplify', false);
$options->set_option('multiplicationsign', 'none');

$casstrings = array();
$i = 0;
$debug = false;
// Set this to display only one argument.  Use the number.
$onlyarg = false;
if (array_key_exists('only', $_GET)) {
    $debug = true;
    $onlyarg = (int) $_GET['only'];
};
$failing = false;
$failingcount = 0;
// Only print the failing tests.
if (array_key_exists('fail', $_GET)) {
    $failing = true;
    $debug = true;
    $onlyarg = false;
};
$verbose = $debug;
/* Just consider the last in the array. */
$sa = array_reverse($samplearguments);
$samplearguments2 = array($sa[0]);

$timestart = microtime(true);
foreach ($samplearguments as $argument) {
    if (array_key_exists('section', $argument)) {
        if (false === $onlyarg && false === $failing) {
            echo html_writer::tag('h2', $argument['section']);
        }
    } else {
        $i++;
        if (false === $onlyarg || $i == $onlyarg) {
            $cskey = 'A'.$i;

            $ap = new stack_cas_casstring('assume_pos:false');
            if (array_key_exists('assumepos', $argument)) {
                $ap = new stack_cas_casstring('assume_pos:true');
            }
            $ap->get_valid('t');

            $ar = new stack_cas_casstring('assume_real:false');
            if (array_key_exists('assumereal', $argument)) {
                $ar = new stack_cas_casstring('assume_real:true');
            }
            $ar->get_valid('t');

            $arg = stack_utils::logic_nouns_sort($argument['casstring'], 'add');
            $cs1 = new stack_cas_casstring($arg);
            $cs1->get_valid('s');
            // This step is needed because validate replaces `or` with `nounor` etc.
            $casstrings[$cskey] = $cs1->get_casstring();
            $casstrings['D'.$i] = $argument['debuglist'];
            $cs1->set_key($cskey);
            if (array_key_exists('debuglist', $argument)) {
                $cs2 = new stack_cas_casstring("DL:" . $argument['debuglist']);
                $cs2->get_valid('t');
            } else {
                $cs2 = new stack_cas_casstring("DL:false");
                $cs2->get_valid('t');
            }
            if ($debug) {
                // Print debug information and show logical connectives on this page.
                $cs3 = new stack_cas_casstring("S1:stack_eval_equiv_arg(" . $cskey. ", true, true, DL)");
            } else {
                // Print only logical connectives on this page.
                $cs3 = new stack_cas_casstring("S1:stack_eval_equiv_arg(" . $cskey. ", true, false, DL)");
            }
            $cs3->get_valid('t');

            $cs4 = new stack_cas_casstring("R1:first(S1)");
            $cs4->get_valid('t');

            $session = new stack_cas_session(array($ap, $ar, $cs1, $cs2, $cs3, $cs4), $options);
            $expected = $argument['outcome'];
            if (true === $argument['outcome']) {
                $expected = 'true';
            } else if (false === $argument['outcome']) {
                $expected = 'false';
            }
            $string       = "\[{@second(S1)@}\]";
            $ct           = new stack_cas_text($string, $session, 0, 't');

            $start = microtime(true);
            $displaytext  = $ct->get_display_castext();
            $took = (microtime(true) - $start);
            $rtook = round($took, 5);

            $argumentvalue = trim($session->get_value_key("R1"));
            $overall = "Overall the argument is {$argumentvalue}.";
            if ('unsupported' !== $argument['outcome']) {
                $overall .= "  We expected the argument to be {$expected}.";
                if ($argumentvalue != $expected) {
                    $overall = "<font color='red'>".$overall."</font>";
                }
            }
            if ($argumentvalue === 'fail') {
                $failingcount++;
            }
            if ($verbose) {
                $displaytext .= $overall;
                $displaytext .= "\n<br>Time taken: ".$rtook;
            }
            $errs = '';
            if ($ct->get_errors() != '') {
                $errs = "<font color='red'>".$ct->get_errors()."</font>";
                $errs .= $ct->get_debuginfo();
            }
            $debuginfo = $ct->get_debuginfo();

            $title = $argument['title'];
            if ('unsupported' === $argument['outcome']) {
                $title .= ' (Unsupported case)';
            }

            $displayargs = true;
            if ($rtook < 1) {
                $displayargs = false;
            }
            if ($argumentvalue != $expected) {
                $displayargs = true;
            }
            $displayargs = true;
            if ($failing && $argumentvalue !== 'fail') {
                $displayargs = false;
            }
            if ($displayargs) {
                echo html_writer::tag('h3', $cskey . ": ". $title).
                    html_writer::tag('p', $argument['narrative']);
                if (!$debug && $verbose) {
                    echo html_writer::tag('pre', htmlspecialchars($argument['casstring'])).
                    html_writer::tag('p', $errs);
                }
                echo html_writer::tag('p', stack_ouput_castext($displaytext));
                if ($debug) {
                    echo html_writer::tag('pre', $cskey . ": ". htmlspecialchars($cs1->get_casstring()) .
                            ";\nDL:" . htmlspecialchars($argument['debuglist']) . ";").
                        html_writer::tag('p', $errs);
                }
                echo "\n<hr/>\n\n\n";
            }
            /* Use the real validation code, and also create something which can be pasted into a live input box. */
            if ($onlyarg) {
                $teacheranswer = $cs1->get_casstring();
                $input = new stack_equiv_input('ans1', $teacheranswer, $options, array('options' => 'comments'));
                $response = $input->get_correct_response($teacheranswer);
                $state = $input->validate_student_response($response, $options, $teacheranswer, null);
                echo $input->render($state, 'ans1', false, $teacheranswer);
            }
        }

        flush(); // Force output to prevent timeouts and to make progress clear.
    }
}
$timetook = (microtime(true) - $timestart);
$timetook = round($timetook, 2);
echo "\n\n<h3 style=\"color:blue;\">Time taken: $timetook</h3>\n";
echo "<h3 style=\"color:blue;\">Number failing: $failingcount</h3>\n\n";

/* Generate offline testing script to cut and paste into desktop Maxima. */
if ($debug) {
    echo '<hr />';
    $script = stack_cas_configuration::generate_maximalocal_contents();
    $script .= "\n";
    $settings = stack_utils::get_config();
    if ($settings->platform == 'unix-optimised') {
        $script .= 'load("stackmaxima.mac")$'."\n";
    }
    $script .= "simp:false;\n";
    echo html_writer::tag('textarea', $script,
            array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '32', 'cols' => '100'));
    echo '<hr />';

    // Have a second text area to facilitate pasting the arguments into separate lines in Maxima.
    $script = '';
    foreach ($casstrings as $key => $val) {
        $script .= $key . ':' . $val . "\$\n";
    }
    $script .= "\n\n".'disp_stack_eval_arg(A22, true, true, D22);';
    echo html_writer::tag('textarea', $script,
            array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '32', 'cols' => '100'));
    echo '<hr />';
}

/* caschat.php script functions. */

$debuginfo = '';
$errs = '';
$varerrs = '';

$vars   = optional_param('vars', '', PARAM_RAW);
$string = optional_param('cas', '', PARAM_RAW);
$simp   = optional_param('simp', '', PARAM_RAW);

// Always fix dollars in this script.
// Very useful for converting existing text for use elswhere in Moodle, such as in pages of text.
$string = stack_maths::replace_dollars($string);

// Sort out simplification.
if ('on' == $simp) {
    $simp = true;
} else {
    $simp = false;
}
// Initially simplification should be on.
if (!$vars and !$string) {
    $simp = true;
}

if ($string) {
    $options = new stack_options();
    $options->set_site_defaults();
    $options->set_option('simplify', $simp);

    $session = new stack_cas_session(null, $options);
    if ($vars) {
        $keyvals = new stack_cas_keyval($vars, $options, 0, 't');
        $session = $keyvals->get_session();
        $varerrs = $keyvals->get_errors();
    }

    if (!$varerrs) {
        $ct           = new stack_cas_text($string, $session, 0, 't');
        $displaytext  = $ct->get_display_castext();
        $errs         = $ct->get_errors();
        $debuginfo    = $ct->get_debuginfo();
    }
}

if (!$varerrs) {
    if ($string) {
        echo $OUTPUT->box(stack_ouput_castext($displaytext));
    }
}

if ($simp) {
    $simp = stack_string('autosimplify').' '.
                html_writer::empty_tag('input', array('type' => 'checkbox', 'checked' => $simp, 'name' => 'simp'));
} else {
    $simp = stack_string('autosimplify').' '.html_writer::empty_tag('input', array('type' => 'checkbox', 'name' => 'simp'));
}

$varlen = substr_count($vars, "\n") + 3;
$stringlen = max(substr_count($string, "\n") + 3, 8);

echo html_writer::tag('form',
            html_writer::tag('h2', stack_string('questionvariables')).
            html_writer::tag('p', $varerrs) .
            html_writer::tag('p', html_writer::tag('textarea', $vars,
                    array('cols' => 100, 'rows' => $varlen, 'name' => 'vars'))).
            html_writer::tag('p', $simp) .
            html_writer::tag('h2', stack_string('castext')) .
            html_writer::tag('p', $errs) .
            html_writer::tag('p', html_writer::tag('textarea', $string,
                    array('cols' => 100, 'rows' => $stringlen, 'name' => 'cas'))).
            html_writer::tag('p', html_writer::empty_tag('input',
                    array('type' => 'submit', 'value' => stack_string('chat')))),
        array('action' => $PAGE->url, 'method' => 'post'));

if ('' != trim($debuginfo)) {
    echo $OUTPUT->box($debuginfo);
}

echo $OUTPUT->footer();
