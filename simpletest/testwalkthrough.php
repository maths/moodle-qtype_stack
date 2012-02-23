<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains tests that walk Stack questions through various sequences
 * of student interaction with different behaviours.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


/**
 * Unit tests for the Stack question type.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_walkthrough_test extends qbehaviour_walkthrough_test_base {

    public function test_deferredfeedback_behaviour() {

        // Create a stack question.
        $q = test_question_maker::make_question('stack');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new ContainsTagWithAttributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
                new PatternExpectation('/Find/'),
                new DoesNotContainTagWithAttributes('div', array('class' => 'interationfeedback')),
                new DoesNotContainTagWithAttributes('div', array('class' => 'prtfeedback')),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit the correct response:
        // TODO.
    }
}
