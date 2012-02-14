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


require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/test.php');
$title = 'Display test Stack question';
$PAGE->set_title($title);


$options = new question_display_options();
$q = test_question_maker::make_question('stack');
$quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
$quba->set_preferred_behaviour('deferredfeedback');
$slot = $quba->add_question($q, 1);
$quba->start_question($slot);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $quba->render_question($slot, $options);

echo $OUTPUT->footer();
