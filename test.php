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
 * Crude test script that just displays a question.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/unittest/simpletestlib.php');
require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


$question = optional_param('question', null, PARAM_ALPHANUM);

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/test.php');
$title = 'Stack test questions';
$PAGE->set_title($title);

// Make sure the global question category exists.
$category = $DB->get_record('question_categories', array('contextid' => $context->id, 'name' => 'Stack test questions'));
if (!$category) {
    $category = new stdClass();
    $category->name = 'Stack test questions';
    $category->contextid = $context->id;
    $category->info = '';
    $category->infoformat = FORMAT_HTML;
    $category->stamp = make_unique_id_code();
    $category->parent = 0;
    $category->sort = 999;
    $category->id = $DB->insert_record('question_categories', $category);
}

// See what existing and expected question definitions we have.
$existing = $DB->get_records('question', array('qtype' => 'stack', 'category' => $category->id),
        'questiontext', 'questiontext, id');
$known = test_question_maker::get_test_helper('stack')->get_test_questions();

// Create any missing ones.
foreach ($known as $name) {
    if (array_key_exists($name, $existing)) {
        continue;
    }
    $question = new stdClass();
    $question->id = 0;
    $question->category = $category->id;
    $question->parent = 0;
    $question->qtype = 'stack';
    $question->name = 'Fake Stack question ' . $name;
    $question->questiontext = $name;
    $question->questiontextformat = FORMAT_HTML;
    $question->generalfeedback = '';
    $question->generalfeedbackformat = FORMAT_HTML;
    $question->defaultmark = 1;
    $question->penalty = 0.3333333;
    $question->length = 1;
    $question->stamp = make_unique_id_code();
    $question->version = make_unique_id_code();
    $question->hidden = 0;
    $question->timecreated = time();
    $question->timemodified = time();
    $question->createdby = $USER->id;
    $question->modifiedby = $USER->id;
    $question->id = $DB->insert_record('question', $question);
    $existing[$name] = $question;
}
ksort($existing);

// Prepare some bits for output.
$qburl = new moodle_url('/question/edit.php', array('courseid' => $PAGE->course->id,
        'category' => $category->id . ',' . $context->id));
$previewurl = new moodle_url('/question/preview.php', array('courseid' => $PAGE->course->id));

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo html_writer::tag('p', 'These faked Stack questions can also be found in ' .
        html_writer::link($qburl, 'the top level of the question bank') . '.');

echo html_writer::start_tag('ul');
foreach ($existing as $name => $q) {
    echo html_writer::tag('li', html_writer::tag('b', $name) . ' ' .
            html_writer::link(new moodle_url($previewurl, array('id' => $q->id)),
                    '[preview]'));
}
echo html_writer::end_tag('ul');

echo $OUTPUT->footer();
