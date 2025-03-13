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
 * This script lets the user send commands to the Maxima, and see the response.
 * This can be useful for learning about the CAS syntax, and also for testing
 * that maxima is working correctly.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../vle_specific.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

require_login();

// Get the parameters from the URL.
$questionid = optional_param('questionid', null, PARAM_INT);
$qubaid = optional_param('qubaid', '', PARAM_RAW);
$slot = optional_param('slot', '', PARAM_RAW);

if (!$questionid) {
    $context = context_system::instance();
    $PAGE->set_context($context);
    require_capability('qtype/stack:usediagnostictools', $context);
    $urlparams = [];
} else {
    // Load the necessary data.
    if ($qubaid !== '') {
        // ISS-1110 If question usage by activity has been supplied, load the question
        // from that so we can load correct responses later.
        $quba = question_engine::load_questions_usage_by_activity(optional_param('qubaid', '', PARAM_RAW));
        $question = $quba->get_question($slot);
    } else {
        $question = question_bank::load_question($questionid);
    }
    $questiondata = $DB->get_record('question', ['id' => $questionid], '*', MUST_EXIST);

    // Process any other URL parameters, and do require_login.
    list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

    // Check permissions.
    question_require_capability_on($questiondata, 'edit');
}

$PAGE->set_url('/question/type/stack/adminui/caschat.php', $urlparams);
$title = stack_string('chattitle');
$PAGE->set_title($title);

$displaytext = '';
$debuginfo = '';
$errs = '';
$varerrs = [];
$pslash = false;

if ($qubaid !== '' && optional_param('initialise', '', PARAM_RAW)) {
    // ISS-1110 Handle calls from questiontestrun.php.
    if ($question->options->get_option('simplify')) {
        $simp = 'on';
    } else {
        $simp = '';
    }
    $questionvarsinputs = '';
    foreach ($question->get_correct_response_testcase() as $key => $val) {
        if (substr($key, -4, 4) !== '_val') {
            $questionvarsinputs .= "\n{$key}:{$val};";
        }
    }

    $vars = $question->questionvariables;
    $inps   = $questionvarsinputs;
    $string = $question->generalfeedback;
} else {
    $vars   = optional_param('maximavars', '', PARAM_RAW);
    $inps   = optional_param('inputs', '', PARAM_RAW);
    $string = optional_param('cas', '', PARAM_RAW);
    $simp   = optional_param('simp', '', PARAM_RAW);
    $pslash = optional_param('pslash', '', PARAM_RAW);
}
$savedb = false;
$savedmsg = '';
if (trim(optional_param('action', '', PARAM_RAW)) == trim(stack_string('savechat'))) {
    $savedb = true;
}

// Always fix dollars in this script.
// Very useful for converting existing text for use elswhere in Moodle, such as in pages of text.
$string = stack_maths::replace_dollars($string);

// Sort out simplification.
if ('on' == $simp) {
    $simp = true;
} else {
    $simp = false;
}
if ('on' == $pslash) {
    $pslash = true;
}
// Initially simplification should be on.
if (!$vars && !$string) {
    $simp = true;
}

if ($string) {
    $options = new stack_options();
    $options->set_site_defaults();
    $options->set_option('simplify', $simp);

    $session = new stack_cas_session2([], $options);
    if ($vars || $inps) {
        $keyvals = new stack_cas_keyval($vars . "\n" . $inps, $options, 0, '', $pslash);
        $keyvals->get_valid();
        $varerrs = $keyvals->get_errors();
        if ($keyvals->get_valid()) {
            $kvcode = $keyvals->compile('test');
            $statements = [];
            if ($kvcode['blockexternal']) {
                $statements[] = new stack_secure_loader($kvcode['blockexternal'], 'caschat', 'blockexternal');
            }
            if ($kvcode['contextvariables']) {
                $statements[] = new stack_secure_loader($kvcode['contextvariables'], 'caschat');
            }
            if ($kvcode['statement']) {
                $statements[] = new stack_secure_loader($kvcode['statement'], 'caschat');
            }
            if ($pslash) {
                $vars = $keyvals->get_raw();
            }
        }
    }

    $ct = null;
    if (!$varerrs) {
        $ct = castext2_evaluatable::make_from_source($string, 'caschat');
        $statements[] = $ct;
        $session = new stack_cas_session2($statements, $options);
        if ($ct->get_valid()) {
            $session->instantiate();
            // Over here we are not sending it through filters so we can directly
            // restore the held ones.
            $displaytext  = $ct->apply_placeholder_holder($ct->get_rendered());
        }
        // Only print each error once.
        $errs = $ct->get_errors(false);
        $errs = array_merge($errs, $session->get_errors(false));
        if ($errs) {
            $errs = stack_string_error('errors') . ': ' . implode(' ', array_unique($errs));
            $errs = html_writer::tag('div', $errs, ['class' => 'error']);
        } else {
            $errs = '';
        }
        $debuginfo = $session->get_debuginfo();

        // Save updated data in the DB when everything is valid.
        if ($questionid && $savedb) {
            $DB->set_field('question', 'generalfeedback', $string,
                ['id' => $questionid]);
            $DB->set_field('qtype_stack_options', 'questionvariables', $vars,
                ['questionid' => $questionid]);
            $DB->set_field('qtype_stack_options', 'compiledcache', null, ['questionid' => $questionid]);
            // Invalidate the question definition cache.
            stack_clear_vle_question_cache($questionid);

            $savedmsg = stack_string('savechatmsg');
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// If we are editing the General Feedback from a question it is very helpful to see the question text.
if ($questionid) {

    $qtype = new qtype_stack();
    $qtestlink = html_writer::link($qtype->get_question_test_url($question), stack_string('runquestiontests'),
        ['class' => 'nav-link']);
    echo html_writer::tag('nav', $qtestlink, ['class' => 'nav']);

    if ($savedmsg) {
        echo html_writer::tag('p', $savedmsg, ['class' => 'overallresult pass']);
    }

    $out = html_writer::tag('summary', stack_string('questiontext'));
    $out .= html_writer::tag('pre', $question->questiontext, ['class' => 'questiontext']);
    echo html_writer::tag('details', $out);
}

if (!$varerrs) {
    if ($string) {
        echo $OUTPUT->box(stack_ouput_castext($displaytext));
    }
}


$fout  = html_writer::tag('h2', stack_string('questionvariables'));
$fout .= html_writer::tag('p', implode($varerrs));
$varlen = substr_count($vars, "\n") + 3;
$fout .= html_writer::tag('p', html_writer::tag('textarea', $vars,
            ['cols' => 100, 'rows' => $varlen, 'name' => 'maximavars']));
if ($questionid) {
    $inplen = substr_count($inps, "\n");
    $fout .= html_writer::tag('p', html_writer::tag('textarea', $inps,
            ['cols' => 100, 'rows' => $inplen, 'name' => 'inputs']));
}
if ($simp) {
    $fout .= stack_string('autosimplify').' '.
        html_writer::empty_tag('input', ['type' => 'checkbox', 'checked' => $simp, 'name' => 'simp']);
} else {
    $fout .= stack_string('autosimplify').' '.html_writer::empty_tag('input', ['type' => 'checkbox', 'name' => 'simp']);
}
if ($questionid) {
    $fout .= html_writer::tag('h2', stack_string('generalfeedback'));
} else {
    $fout .= html_writer::tag('h2', stack_string('castext'));
}
$fout .= html_writer::tag('p', $errs);
$stringlen = max(substr_count($string, "\n") + 3, 8);
$fout .= html_writer::tag('p', html_writer::tag('textarea', $string,
            ['cols' => 100, 'rows' => $stringlen, 'name' => 'cas']));
$fout .= html_writer::start_tag('p');
$fout .= html_writer::empty_tag('input',
            ['type' => 'submit', 'name' => 'action', 'value' => stack_string('chat')]);
if ($questionid && !$varerrs) {
    $fout .= html_writer::empty_tag('input',
        ['type' => 'submit',  'name' => 'action', 'value' => stack_string('savechat')]);
}
$fout .= html_writer::end_tag('p');

$fout .= html_writer::start_tag('p') . stack_string('pslash');
$fout .= html_writer::empty_tag('input', ['type' => 'checkbox', 'name' => 'pslash']);
$fout .= html_writer::end_tag('p');

echo html_writer::tag('form', $fout, ['action' => $PAGE->url, 'method' => 'post']);

if ('' != trim($debuginfo)) {
    echo $OUTPUT->box($debuginfo);
}

echo html_writer::tag('p', stack_string('chatintro'));
echo $OUTPUT->footer();
