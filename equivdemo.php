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
require_once(__DIR__ . '/stack/cas/installhelper.class.php');


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
$newarg['narative']  = '';
$newarg['casstring'] = "[]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Single line argument";
$newarg['narative']  = '';
$newarg['casstring'] = "[x^2=-1]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Vacuous linear equation (1)";
$newarg['narative']  = 'This equation is satisfied by any value of x.';
$newarg['casstring'] = "[2*(x-3) = 5*x-3*(x+2),2*x-6=2*x-6,0=0]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Vacuous linear equation (2)";
$newarg['narative']  = 'This equation is satisfied by no value of x.';
$newarg['casstring'] = "[2*(x-3) = 5*x-3*(x+1),2*x-6=2*x-3,0=3,{}]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Difference of two squares";
$newarg['narative']  = 'This argument is a basic step and should be considered true.';
$newarg['casstring'] = "[a^2=b^2,a^2-b^2=0,(a-b)*(a+b)=0,a=b or a=-b]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Difference of two cubes";
$newarg['narative']  = 'Over the reals, this argument is true.  So this should be fine.';
$newarg['casstring'] = "[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+a*b+b^2)=0,(a-b)=0,a=b]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Difference of two cubes: complex";
$newarg['narative']  = 'We work over the real numbers, but should not reject a correct argument over the complex numbers.';
$newarg['casstring'] = "[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+a*b+b^2)=0,(a-b)=0 or (a^2+a*b+b^2)=0, a=b or (a+(1+%i*sqrt(3))/2*b)*(a+(1-%i*sqrt(3))/2*b)=0, a=b or a=-(1+%i*sqrt(3))/2*b or a=-(1-%i*sqrt(3))/2*b]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 1";
$newarg['narative']  = 'This is the basic method for solving quadratics via factoring over the reals.  This should be true.';
$newarg['casstring'] = "[x^2-x=30,x^2-x-30=0,(x-6)*(x+5)=0,x-6=0 or x+5=0,x=6 or x=-5]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 2";
$newarg['narative']  = '';
$newarg['casstring'] = "[x^2=2,x^2-2=0,(x-sqrt(2))*(x+sqrt(2))=0,x=sqrt(2) or x=-sqrt(2)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 3";
$newarg['narative']  = 'This argument avoids taking the square root of both sides by subtracting and taking the difference of two squares.';
$newarg['casstring'] = "[(2*x-7)^2=(x+1)^2,(2*x-7)^2 -(x+1)^2=0,(2*x-7+x+1)*(2*x-7-x-1)=0,(3*x-6)*(x-8)=0,x=2 or x=8]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 4 (missing root!)";
$newarg['narative']  = 'This argument creates problems by taking the square root of both sides.';
$newarg['casstring'] = "[(2*x-7)^2=(x+1)^2,sqrt((2*x-7)^2)=sqrt((x+1)^2),2*x-7=x+1,x=8]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 5 (specific with pm)";
$newarg['narative']  = 'Uses the \(\pm\) operator to capture both roots.';
$newarg['casstring'] = "[x^2-10*x+9 = 0, (x-5)^2-16 = 0, (x-5)^2 =16, x-5 =+-4, x-5 =4 or x-5=-4, x = 1 or x = 9]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 6 (general with pm)";
$newarg['narative']  = '';
$newarg['casstring'] = "[x^2-2*p*x-q=0,x^2-2*p*x=q,x^2-2*p*x+p^2=q+p^2,(x-p)^2=q+p^2,x-p=+-sqrt(q+p^2),x-p=sqrt(q+p^2) or x-p=-sqrt(q+p^2),x=p+sqrt(q+p^2) or x=p-sqrt(q+p^2)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 7 (Toby's method)";
$newarg['narative']  = '';
$newarg['casstring'] = "[a*x^2+b*x+c=0 or a=0,a^2*x^2+a*b*x+a*c=0,(a*x)^2+b*(a*x)+a*c=0,(a*x)^2+b*(a*x)+b^2/4-b^2/4+a*c=0,(a*x+b/2)^2-b^2/4+a*c=0,(a*x+b/2)^2=b^2/4-a*c,a*x+b/2= +-(b^2/4-a*c),x=(-b+sqrt(a^2-4*a*c))/(2*a) or x=(-b+sqrt(a^2-4*a*c))/(2*a)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 7 (nonsense)";
$newarg['narative']  = 'This argument is by Bill McCullum: matching up the coefficients.  This argument is false, but leads to the correct solution.';
$newarg['casstring'] = "[x^2-6*x+9=0,x^2-6*x=-9,x*(x-6)=3*-3,x=3 or x-6=-3,x=3]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 8 (nonsense)";
$newarg['narative']  = 'Maxwell pg 88.  The wrong method leads to a correct solution.';
$newarg['casstring'] = "[(x+3)*(2-x)=4,x+3=4 or (2-x)=4,x=1 or x=-2]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving quadratic equations 9 (nonsense)";
$newarg['narative']  = 'Maxwell pg 89.  A general version of the previous argument.';
$newarg['casstring'] = "[(x-p)*(x-q)=0,x^2-p*x-q*x+p*q=0,1+q-x-p-p*q+p*x+x+q*x-x^2=1-p+q,(1+q-x)*(1-p+x)=1-p+q,(1+q-x)=1-p+q or (1-p+x)=1-p+q,x=p or x=q]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving a quadratic inequality";
$newarg['narative']  = 'Solving quadratic inequalities using reasoning by equivalence.';
$newarg['casstring'] = "[2*x^2+x>=6, 2*x^2+x-6>=0, (2*x-3)*(x+2)>= 0,((2*x-3)>=0 and (x+2)>=0) or ((2*x-3)<=0 and (x+2)<=0),(x>=3/2 and x>=-2) or (x<=3/2 and x<=-2), x>=3/2 or x <=-2]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving an inequality (remove redundant inequalities)";
$newarg['narative']  = '';
$newarg['casstring'] = "[x^2>=9 and x>3, x^2-9>=0 and x>3, (x>=3 or x<=-3) and x>3, x>3]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Classic nonsense 1.";
$newarg['narative']  = 'Here we create a problem by dividing by a term which is actually zero.';
$newarg['casstring'] = "[a=b, a^2=a*b, a^2-b^2=a*b-b^2, (a-b)*(a+b)=b*(a-b), a-b=b, a=2*b, b=2*b, 1=2]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Classic nonsense 2.";
$newarg['narative']  = 'In this argument we "move over" one term, but end up at the right answer.  Actually, the term we "moved over" is zero, which is why we don\'t pick up the problem';
$newarg['casstring'] = "[6*x-12=3*(x-2),6*x-12+3*(x-2)=0,12*x-24=0,x=2]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Rational expressions 1";
$newarg['narative']  = 'Cancelling factors here is fine.';
$newarg['casstring'] = "[(x^2-4)/(x-2)=0,(x-2)*(x+2)/(x-2)=0,x+2=0,x=-2]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Rational expressions 2";
$newarg['narative']  = 'We should have cancelled the roots. As a result of the failure to do this we have an extra root of this equation.';
$newarg['casstring'] = "[(x^2-4)/(x-2)=0,(x^2-4)=0,(x-2)*(x+2)=0,x=-2 or x=2]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving rational expressions (erroneous argument)";
$newarg['narative']  = 'Here we create a problem by dividing by a term which is actually zero.  The only solution is \(x=10\) which we have cancelled out.';
$newarg['casstring'] = "[(x+5)/(x-7)-5= (4*x-40)/(13-x),(x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x), (4*x-40)/(7-x)= (4*x-40)/(13-x),7-x= 13-x,7= 13]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Solving equations with surds (erroneous argument 1)";
$newarg['narative']  = 'Squaring both sides of an equation leads to possible additional solutions.';
$newarg['casstring'] = "[sqrt(3*x+4) = 2+sqrt(x+2), 3*x+4=4+4*sqrt(x+2)+(x+2),x-1=2*sqrt(x+2),x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 or x=-1]";
$newarg['outcome']   = false;
$samplearguments[] = $newarg;

// [2*x/abs(x-1) < 1,2*x < abs(x-1), x >= 1 nounand 2*x < x-1 nounor (x < 1 nounand 2*x < 1-x), x < -1 nounand x >= 1 nounor (x < 1 nounand 3*x < 1),x < 1/3];
$newarg = array();
$newarg['title']     = "Solving inequalities with the absolute value function";
$newarg['narative']  = '';
$newarg['casstring'] = "[2*x/abs(x-1)<1, 2*x<abs(x-1),(x>=1 and 2*x<x-1) or (x<1 and 2*x<-x+1),(x>=1 and x<-1) or (x<1 and 3*x<1),x<1/3]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Simultaneous equations (must use and to join them)";
$newarg['narative']  = '';
$newarg['casstring'] = "[x^2+y^2=8 and x=y, 2*x^2=8 and y=x, x^2=4 and y=x, x= +-2 and y=x, (x= 2 and y=x) or (x=-2 and y=x), (x=2 and y=2) or (x=-2 and y=-2)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Simultaneous equations (without using square roots or substitution)";
$newarg['narative']  = '';
$newarg['casstring'] = "[x^2+y^2=5 and x*y=2, x^2+y^2-5=0 and x*y-2=0, x^2-2*x*y+y^2-1=0 and x^2+2*x*y+y^2-9=0, (x+y)^2-1=0 and (x-y)^2-3^2=0, (x+y=1 and x-y=3) or (x+y=-1 and x-y=3) or (x+y=1 and x-y=-3) or (x+y=-1 and x-y=-3), (x=1 and y=2) or (x=2 and y=1) or (x=-2 and y=-1) or (x=-1 and y=-2)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Substitution";
$newarg['narative']  = '';
$newarg['casstring'] = "[a^2=b and a^2=1, b=a^2 and (a=1 or a=-1), (b=1 and a=1) or (b=1 and a=-1)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Substitution: Needs fixing....";
$newarg['narative']  = '';
$newarg['casstring'] = "[a^2=b and x=1, b=a^2 and x=1]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Find two distinct numbers which are the square of each other (needs substitution, not equals)";
$newarg['narative']  = '';
$newarg['casstring'] = "[a^2=b and b^2=a, b=a^2 and a^4=a, b=a^2 and a^4-a=0, b=a^2 and a*(a-1)*(a^2+a+1)=0, b=a^2 and (a=0 or a=1 or a^2+a+1=0), (b=0 and a=0) or (b=1 and a=1)]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Differential quotient as the unknown";
$newarg['narative']  = '';
$newarg['casstring'] = "[-12+3*diff(y(x),x)+8-8*diff(y(x),x)=0,-5*diff(y(x),x)=4,diff(y(x),x)=-4/5]";
//$newarg['casstring'] = "[-12+3*'diff(y,x)+8-8*'diff(y,x)=0,-5*'diff(y,x)=4,'diff(y,x)=-4/5]";
$newarg['outcome']   = true;
$samplearguments[] = $newarg;

$newarg = array();
$newarg['title']     = "Find the values of a which satisfy this inequality for all x.";
$newarg['narative']  = 'This argument contains a comment and hence cannot be automatically assessed.  Each internal bit is fine, but the logic uses a universal quantifier which we cannot assess. This argument will need semi-automatic marking.';
$newarg['casstring'] = "[-x^2+a*x+a-3<0, a-3<x^2-a*x, a-3<(x-a/2)^2-a^2/4, a^2/4+a-3<(x-a/2)^2, a^2+4*a-12<4*(x-a/2)^2, (a-2)*(a+6)<4*(x-a/2)^2, \"This inequality is required to be true for all x, it must be true when the right hand side takes its minimum value.  This happens for x=a/2\", (a-2)*(a+6)<0, ((a-2)<0 and (a+6)>0) or ((a-2)>0 and (a+6)<0), (a<2 and a>-6) or (a>2 and a<-6), (-6<a and a<2) or false, (-6<a and a<2)]";
$newarg['outcome']   = 'unknown';
$samplearguments[] = $newarg;

// Eample of equational reasoning, with many variables.
// This example comes from proving Heron's formula
// See http://math.stackexchange.com/questions/255527/show-that-2a2-b2a2c2b2c2-a4b4c4-abc-abca-bcab
/*
[2(a^2*b^2+b^2*c^2+c^2*a^2)−(a^4+b^4+c^4),
4*a^2*b^2−(a^4+b^4+c^4+2*a^2*b^2−2*b^2*c^2−2*c^2*a^2)
,(2*a*b)^2−(b^2+a^2−c^2)^2
,(2*a*b+b^2+a^2−c^2)*(2*a*b−b^2−a^2+c^2)
,((a+b)^2−c^2)*(c^2−(a−b)^2)
,(a+b+c)*(a+b−c)*(c+a−b)*(c−a+b)];
*/

/* Loop over each argument, evaluate it and display the results. */

$options = new stack_options();
$options->set_site_defaults();
$options->set_option('simplify', false);
$casstrings = array();
foreach ($samplearguments as $argument) {
    $cs1 = new stack_cas_casstring($argument['casstring']);
    $cs1->get_valid('t');
    // This step is needed because validate replaces `or` with `nounor` etc.
    $casstrings[] = $cs1->get_casstring();
    $cs1->set_key('A1');
    // Always print debug information and show logical connectives on this page.
    $cs2 = new stack_cas_casstring("S1:disp_stack_eval_arg(A1, true, true)");
    $cs2->get_valid('t');
    $cs3 = new stack_cas_casstring("S2:check_stack_eval_arg(A1)");
    $cs3->get_valid('t');

    $session = new stack_cas_session(array($cs1, $cs2, $cs3), $options);
    $expected = $argument['outcome'];
    if (true === $argument['outcome']) {
        $expected = 'true';
    } else if (false === $argument['outcome']) {
        $expected = 'false';
    }
    $string       = "\[@S1@\]";
    $string      .= "Overall the argument is @S2@.  We expected the argument to be {$expected}.";
    $ct           = new stack_cas_text($string, $session, 0, 't');
    $displaytext  = $ct->get_display_castext();
    $errs         = $ct->get_errors();
    $debuginfo    = $ct->get_debuginfo();

    echo html_writer::tag('h2', $argument['title']).
         html_writer::tag('p', $argument['narative']).
         html_writer::tag('pre', htmlspecialchars($argument['casstring'])).
         html_writer::tag('p', $errs).
         html_writer::tag('p', stack_ouput_castext($displaytext));
    echo "\n<hr/>\n\n\n";

    flush(); // Force output to prevent timeouts and to make progress clear.
}

/* Generate offline testing script to cut and paste into desktop Maxima. */
echo '<hr />';
$script = stack_cas_configuration::generate_maximalocal_contents();
$script .= "\n";
$settings = get_config('qtype_stack');
if ($settings->platform == 'unix-optimised') {
    $script .= 'load("stackmaxima.mac")$'."\n";
}
$script .= "simp:false;\n";
echo html_writer::tag('textarea', $script,
    array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '32', 'cols' => '100'));
echo '<hr />';
// Have a second text area to facilitate pasting the arguments into a separate line in Maxima.
$script = '';
$i = 0;
foreach ($casstrings as $val) {
    $i++;
    $script .= 'A'.$i.':'.$val.";\n";
}
echo html_writer::tag('textarea', $script,
    array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '32', 'cols' => '100'));
echo '<hr />';

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
