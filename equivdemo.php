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
 * This script lets the user send commands to the Maxima, and see the response.
 * This can be useful for learning about the CAS syntax, and also for testing
 * that maxima is working correctly.
 *
 * @copyright  2015 University of Edinburgh
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
$title = "Equivalence reasoning demo";
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

/* This page is not intended to every be incoprporated into STACK as
 * part of the main codebase.  It is here to test the features of the
 * proposed equivalence reasoning input type.  This script is heavily
 * based on caschat.php
 */

$samplearguments = array();


$newarg = array();
$newarg['title']     = "Empty argument";
$newarg['casstring'] = "[]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 1";
$newarg['casstring'] = "[x^2-x=30,x^2-x-30=0,(x-6)*(x+5)=0,x-6=0 or x+5=0,x=6 or x=-5]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 2";
$newarg['casstring'] = "[x^2=2,x^2-2=0,(x-sqrt(2))*(x+sqrt(2))=0,x=sqrt(2) or x=-sqrt(2)]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 3";
$newarg['casstring'] = "[(2*x-7)^2=(x+1)^2,(2*x-7)^2 -(x+1)^2= 0,(2*x-7+x+1)*(2*x-7-x-1) =0,(3*x-6)*(x-8)=0,x=2 or x=8]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 4 (missing root!)";
$newarg['casstring'] = "[(2*x-7)^2=(x+1)^2,sqrt((2*x-7)^2)=sqrt((x+1)^2),2*x-7=x+1,x=8]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 5";
$newarg['casstring'] = "[x^2-2*p*x-q=0,x^2-2*p*x=q,x^2-2*p*x+p^2=q+p^2,(x-p)^2=q+p^2,x-p=+-sqrt(q+p^2),x-p=sqrt(q+p^2) or x-p=-sqrt(q+p^2),x=p+sqrt(q+p^2) or x=p-sqrt(q+p^2)]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving a quadratic inequality";
$newarg['casstring'] = "[2*x^2+x>=6, 2*x^2+x - 6>= 0, (2*x-3)*(x+2) >= 0,((2*x-3)>=0 and (x+2)>=0) or ((2*x-3)<=0 and (x+2)<=0),(x>=3/2 and x>=-2) or (x<=3/2 and x<=-2), x>=3/2 or x <=-2]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving an inequality (remove redundant inequalities)";
$newarg['casstring'] = "[x^2>=9 and x>3, x^2-9>=0 and x>3, (x>=3 or x<=-3) and x>3, x>3]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving rational expressions (erroneous argument)";
$newarg['casstring'] = "[(x+5)/(x-7)-5= (4*x-40)/(13-x),(x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x), (4*x-40)/(7-x)= (4*x-40)/(13-x),7-x= 13-x,7= 13]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving equations with surds (erroneous argument 1)";
$newarg['casstring'] = "[sqrt(3*x+4) = 2+sqrt(x+2), 3*x+4=4+4*sqrt(x+2)+(x+2),x-1=2*sqrt(x+2),x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 or x=-1]";
$samplearguments[] = $newarg;

// [2*x/abs(x-1) < 1,2*x < abs(x-1), x >= 1 nounand 2*x < x-1 nounor (x < 1 nounand 2*x < 1-x), x < -1 nounand x >= 1 nounor (x < 1 nounand 3*x < 1),x < 1/3];
$newarg = array();
$newarg['title']     = "Solving inequalities with the absolute value function";
$newarg['casstring'] = "[2*x/abs(x-1)<1, 2*x<abs(x-1),(x>=1 and 2*x<x-1) or (x<1 and 2*x<-x+1),(x>=1 and x<-1) or (x<1 and 3*x<1),x<1/3]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Simultaneous equations (must use and to join them)";
$newarg['casstring'] = "[x^2+y^2=8 and x=y, 2*x^2=8 and y=x, x^2=4 and y=x, x= +-2 and y=x, (x= 2 and y=x) or (x=-2 and y=x), (x=2 and y=2) or (x=-2 and y=-2)]";
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Differential quotient as the unknown";
$newarg['casstring'] = "[-12+3*diff(y(x),x)+8-8*diff(y(x),x)=0,-5*diff(y(x),x)=4,diff(y(x),x)=-4/5]";
//$newarg['casstring'] = "[-12+3*'diff(y,x)+8-8*'diff(y,x)=0,-5*'diff(y,x)=4,'diff(y,x)=-4/5]";
$samplearguments[] = $newarg;

/* Loop over each argument, evaluate it and display the results. */

$options = new stack_options();
$options->set_site_defaults();
$options->set_option('simplify', false);

foreach($samplearguments as $argument) {
//$argument = end($samplearguments);

    $cs1 = new stack_cas_casstring($argument['casstring']);
    $cs1->get_valid('t');
    $cs1->set_key('A1');
    $cs2 = new stack_cas_casstring("S1:stack_eval_arg(A1)");
    $cs2->get_valid('t');

    $session      = new stack_cas_session(array($cs1, $cs2), $options);
    $string       = "\[@second(S1)@\]  Overall the argument is @first(S1)@.";
    $ct           = new stack_cas_text($string, $session, 0, 't');
    $displaytext  = $ct->get_display_castext();
    $errs         = $ct->get_errors();
    $debuginfo    = $ct->get_debuginfo();

    echo html_writer::tag('h2', $argument['title']) .
         html_writer::tag('pre', htmlspecialchars($argument['casstring'])).
         html_writer::tag('p', $errs) .
         html_writer::tag('p', stack_ouput_castext($displaytext));
    echo "\n<hr/>\n\n\n";
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
            html_writer::tag('h2', stack_string('questionvariables')) .
            html_writer::tag('p', $varerrs) .
            html_writer::tag('p', html_writer::tag('textarea', $vars,
                    array('cols' => 100, 'rows' => $varlen, 'name' => 'vars'))) .
            html_writer::tag('p', $simp) .
            html_writer::tag('h2', stack_string('castext')) .
            html_writer::tag('p', $errs) .
            html_writer::tag('p', html_writer::tag('textarea', $string,
                    array('cols' => 100, 'rows' => $stringlen, 'name' => 'cas'))) .
            html_writer::tag('p', html_writer::empty_tag('input',
                    array('type' => 'submit', 'value' => stack_string('chat')))),
        array('action' => $PAGE->url, 'method' => 'post'));

if ('' != trim($debuginfo)) {
    echo $OUTPUT->box($debuginfo);
}

echo $OUTPUT->footer();
