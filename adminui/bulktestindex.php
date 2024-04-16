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

require_once(__DIR__.'/../../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/bulktester.class.php');

$skippreviouspasses = optional_param('skippreviouspasses', false, PARAM_BOOL);
$urlparams = [];
if ($skippreviouspasses) {
    $urlparams['skippreviouspasses'] = 1;
}

// Login and check permissions.
$context = context_system::instance();
require_login();
require_capability('qtype/stack:usediagnostictools', $context);
$PAGE->set_url('/question/type/stack/adminui/bulktestindex.php', $urlparams);
$PAGE->set_context($context);
$PAGE->set_title(stack_string('bulktestindextitle'));

require_login();

// Create the helper class.
$bulktester = new stack_bulk_tester();

// Display.
echo $OUTPUT->header();
echo $OUTPUT->heading(stack_string('replacedollarsindex'));

// Find in which contexts the user can edit questions.
$questionsbycontext = $bulktester->get_num_stack_questions_by_context();
$availablequestionsbycontext = [];
foreach ($questionsbycontext as $contextid => $numquestions) {
    $context = context::instance_by_id($contextid);
    if (has_capability('moodle/question:editall', $context)) {
        $name = $context->get_context_name(true, true);
        if (strpos($name, 'Quiz:') === 0) { // Quiz-specific question category.
            $course = $context->get_course_context(false);
            if ($course === false) {
                $name = 'UnknownCourse: ' . $name;
            } else {
                $name = $course->get_context_name(true, true) . ': ' . $name;
            }
        }
        $availablequestionsbycontext[$name] = [
            'contextid' => $contextid,
            'numquestions' => $numquestions];
    }
}

ksort($availablequestionsbycontext);

// List all contexts available to the user.
if (count($availablequestionsbycontext) == 0) {
    echo html_writer::tag('p', get_string('unauthorisedbulktest', 'qtype_stack'));
} else {
    echo html_writer::start_tag('ul');
    foreach ($availablequestionsbycontext as $name => $info) {
        $contextid = $info['contextid'];
        $numquestions = $info['numquestions'];

        $testallurl = new moodle_url('/question/type/stack/adminui/bulktest.php', ['contextid' => $contextid]);
        $testalllink = html_writer::link($testallurl,
            get_string('bulktestallincontext', 'qtype_stack'), ['title' => get_string('testalltitle', 'qtype_stack')]);
        $litext = $name . ' (' . $numquestions . ') ' . $testalllink;

        echo html_writer::start_tag('details');
        echo html_writer::tag('summary', $litext);

        $categories = $bulktester->get_categories_for_context($contextid);
        echo html_writer::start_tag('ul', ['class' => 'expandable']);
        foreach ($categories as $cat) {
            if ($cat->count > 0) {
                $url = new moodle_url('/question/type/stack/adminui/bulktest.php',
                    ['contextid' => $contextid, 'categoryid' => $cat->id]);
                $linktext = $cat->name . ' (' . $cat->count . ')';
                $link = html_writer::link($url, $linktext);
                echo html_writer::tag('li', $link,
                    ['title' => get_string('testallincategory', 'qtype_stack')]);
            }
        }
        echo html_writer::end_tag('ul');
        echo html_writer::end_tag('details');
    }
    echo html_writer::end_tag('ul');

    if (has_capability('moodle/site:config', context_system::instance())) {
        echo html_writer::tag('p', html_writer::link(
            new moodle_url('/question/type/stack/adminui/bulktestall.php'), get_string('bulktestrun', 'qtype_stack')));
    }
}

echo $OUTPUT->footer();
