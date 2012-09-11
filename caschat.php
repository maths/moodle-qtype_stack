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
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');

require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/stack/utils.class.php');
require_once(dirname(__FILE__) . '/stack/options.class.php');
require_once(dirname(__FILE__) . '/stack/cas/castext.class.php');
require_once(dirname(__FILE__) . '/stack/cas/casstring.class.php');
require_once(dirname(__FILE__) . '/stack/cas/cassession.class.php');


require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/caschat.php');
$title = stack_string('chattitle');
$PAGE->set_title($title);

// Enable testing of CAStext in with a background of a non-trivial session, with some options set.

/* 
$options = new stack_options();
$options->set_option('simplify', false);

$a1 = array('a:-2', 'b:ev(-1-2,simp)');

$s1 = array();
foreach ($a1 as $s) {
    $cs = new stack_cas_casstring($s);
    $cs->validate('t');
    $s1[] = $cs;
}
$cs1 = new stack_cas_session($s1, $options);
*/

$string = optional_param('cas', '', PARAM_RAW);

$debuginfo = '';
if ($string) {
    $ct           = new stack_cas_text($string); // Need to add in $cs1 here if we intend to use it...
    $displaytext  = $ct->get_display_castext();
    $errs         = $ct->get_errors();
    $debuginfo    = $ct->get_debuginfo();
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo html_writer::tag('p', stack_string('chatintro'));

if ($string) {
    echo html_writer::tag('p', format_text($displaytext));
    echo $errs;
}

echo html_writer::tag('form',
            html_writer::tag('p', html_writer::tag('textarea', $string,
                    array('cols' => 80, 'rows' => 5, 'name' => 'cas'))) .
            html_writer::tag('p', html_writer::empty_tag('input',
                    array('type' => 'submit', 'value' => stack_string('chat')))),
        array('action' => $PAGE->url, 'method' => 'post'));

if ('' != trim($debuginfo)) {
    echo $OUTPUT->box($debuginfo);
}

echo $OUTPUT->footer();
