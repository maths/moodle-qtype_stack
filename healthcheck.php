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
 * This script helps  that the stack is installed correctly, and that
 * all the parts are working properly, including the conection to the CAS,
 * graph plotting, and equation rendering.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/stack/stringutil.class.php');
require_once(dirname(__FILE__) . '/stack/options.class.php');
require_once(dirname(__FILE__) . '/stack/cas/castext.class.php');
require_once(dirname(__FILE__) . '/stack/cas/casstring.class.php');
require_once(dirname(__FILE__) . '/stack/cas/cassession.class.php');
require_once(dirname(__FILE__) . '/stack/cas/installhelper.class.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/healthcheck.php');
$title = stack_string('healthcheck');
$PAGE->set_title($title);

$sampletex = '\sum_{n=1}^\infty \frac{1}{n^2} = \frac{\pi^2}{6}.';
$samplecastext = 'The derivative of @ x^4/(1+x^4) @ is $$ \frac{d}{dx} \frac{x^4}{1+x^4} = @ diff(x^4/(1+x^4),x) @. $$';
$sampleplots = 'Two example plots below.  @plot([x^4/(1+x^4),diff(x^4/(1+x^4),x)],[x,-3,3])@  @plot([sin(x),x,x^2,x^3],[x,-3,3],[y,-3,3])@';

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// LaTeX
echo $OUTPUT->heading(stack_string('healthchecklatex'), 3);
echo html_writer::tag('p', stack_string('healthchecklatexintro'));

echo html_writer::tag('dt', stack_string('texdoubledollar'));
echo html_writer::tag('dd', format_text('$$' . $sampletex . '$$'));

echo html_writer::tag('dt', stack_string('texsingledollar'));
echo html_writer::tag('dd', format_text('$' . $sampletex . '$'));

echo html_writer::tag('dt', stack_string('texdisplayedbracket'));
echo html_writer::tag('dd', format_text('\[' . $sampletex . '\]'));

echo html_writer::tag('dt', stack_string('texinlinebracket'));
echo html_writer::tag('dd', format_text('\(' . $sampletex . '\)'));

// Maxima config
echo $OUTPUT->heading(stack_string('healthcheckconfig'), 3);
echo html_writer::tag('p', stack_string('healthcheckconfigintro'));

stack_cas_configuration::create_maximalocal();

echo html_writer::tag('textarea', stack_cas_configuration::generate_maximalocal_contents(),
        array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows'=>'10', 'cols'=>'100'));

// Test Maxima connection
echo $OUTPUT->heading(stack_string('healthcheckconnect'), 3);
echo html_writer::tag('p', stack_string('healthcheckconnectintro'));
echo html_writer::tag('pre', s($samplecastext));

$ct          = new stack_cas_text($samplecastext);
$displaytext = $ct->get_display_castext();
$errs        = $ct->get_errors();

echo html_writer::tag('p', format_text($displaytext));
echo $errs;

// Test plots
echo $OUTPUT->heading(stack_string('healthcheckplots'), 3);
echo html_writer::tag('p', stack_string('healthcheckplotsintro'));
echo html_writer::tag('pre', s($sampleplots));

$ct          = new stack_cas_text($sampleplots);
$displaytext = $ct->get_display_castext();
$errs        = $ct->get_errors();

echo html_writer::tag('p', format_text($displaytext));
echo $errs;

echo $OUTPUT->footer();
