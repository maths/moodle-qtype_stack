<?php
// This file is part of STACK - http://stack.maths.ed.ac.uk/
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
 * Configuration settings declaration information for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2021 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../locallib.php');

// Authentication. Because of the cache, it is safe to make this available to any
// logged in user.
require_login();
require_capability('qtype/stack:usediagnostictools', context_system::instance());

// Useful links.
$links = array(
    get_string('stackDoc_docs_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/doc/doc.php/'))),
    get_string('chat_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/adminui/caschat.php'))),
    get_string('stackInstall_testsuite_title_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/adminui/answertests.php'))),
    get_string('stackInstall_input_title_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/adminui/studentinputs.php'))),
    get_string('bulktestindexintro_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/adminui/bulktestindex.php'))),
    get_string('stackInstall_replace_dollars_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/adminui/replacedollarsindex.php'))),
);

// Set up the page object.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/question/type/stack/adminui/index.php');
$title = stack_string('settingusefullinks');
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);


echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$list = '';
foreach ($links as $link) {
    $list .= html_writer::tag('li', $link);
}
echo html_writer::tag('ul', $list);

/* Add the version number and logos to the front page.  */
$settings = get_config('qtype_stack');
$libs = array_map('trim', explode(',', $settings->maximalibraries));
asort($libs);
$libs = implode(', ', $libs);
$vstr = $settings->version . ' (' . $libs . ')';
echo html_writer::tag('p', stack_string('stackDoc_version', $vstr));

echo $OUTPUT->footer();
