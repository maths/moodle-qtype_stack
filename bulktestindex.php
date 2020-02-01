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
 * This script provdies an index for running the question tests in bulk.
 *
 * @package   qtype_stack
 * @copyright 2013 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/bulktester.class.php');

$skippreviouspasses = optional_param('skippreviouspasses', false, PARAM_BOOL);
$urlparams = [];
if ($skippreviouspasses) {
    $urlparams['skippreviouspasses'] = 1;
}

// Login and check permissions.
$context = context_system::instance();
require_login();
require_capability('qtype/stack:usediagnostictools', $context);
$PAGE->set_url('/question/type/stack/bulktestindex.php', $urlparams);
$PAGE->set_context($context);
$PAGE->set_title(stack_string('bulktestindextitle'));

require_login();

// Create the helper class.
$bulktester = new stack_bulk_tester();

// Display.
echo $OUTPUT->header();
echo $OUTPUT->heading(stack_string('replacedollarsindex'));

echo html_writer::start_tag('ul');
foreach ($bulktester->get_stack_questions_by_context() as $contextid => $numstackquestions) {
    echo html_writer::tag('li', html_writer::link(
            new moodle_url('/question/type/stack/bulktest.php',
                    $urlparams + ['contextid' => $contextid]),
            context::instance_by_id($contextid)->get_context_name(true, true) . ' (' . $numstackquestions . ')'));
}
echo html_writer::end_tag('ul');

if (has_capability('moodle/site:config', context_system::instance())) {
    echo html_writer::tag('p', html_writer::link(
            new moodle_url('/question/type/stack/bulktestall.php', $urlparams),
            stack_string('bulktestrun')));
}

echo $OUTPUT->footer();
