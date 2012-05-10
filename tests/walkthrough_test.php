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

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/test_base.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the Stack question type.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class qtype_stack_walkthrough_test extends qbehaviour_walkthrough_test_base {
    public function setUp() {
        parent::setUp();
        qtype_stack_testcase::setup_test_maxima_connection();
        $this->resetAfterTest();
    }

    protected function contains_input_validation() {
        return new question_contains_tag_with_attributes('div', array('class' => 'stackinputfeedback'));
    }

    protected function does_not_contain_input_validation() {
        return new question_does_not_contain_tag_with_attributes('div', array('class' => 'stackinputfeedback'));
    }

    protected function contains_prt_feedback() {
        return new question_contains_tag_with_attributes('div', array('class' => 'stackprtfeedback'));
    }

    protected function does_not_contain_prt_feedback() {
        return new question_does_not_contain_tag_with_attributes('div', array('class' => 'stackprtfeedback'));
    }

    protected function check_no_stray_placeholders() {
        return new question_no_pattern_expectation('~\[\[|\]\]~');
    }

    public function test_adaptivefeedback_behaviour_test1_1() {

        // Create the stack question 'test1'.
        $q = test_question_maker::make_question('stack', 'test1');
        $this->start_attempt_at_question($q, 'adaptive', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
                new question_pattern_expectation('/Find/'),
                $this->does_not_contain_input_validation(),
                $this->does_not_contain_prt_feedback(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation(),
                $this->check_no_stray_placeholders()
        );

        // Process a validate request.
        // Notice here we get away with including single letter question variables in the answer.
        $this->process_submission(array('ans1' => '(v-a)^(n+1)/(n+1)+c', '-submit' => 1));

        $this->check_current_mark(null);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            $this->contains_input_validation(),
            $this->does_not_contain_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

        // Process a submit of the correct answer.
        $this->process_submission(array('ans1' => '(v-a)^(n+1)/(n+1)+c', 'ans1_val' => '(v-a)^(n+1)/(n+1)+c', '-submit' => 1));

        // Verify.
        $this->check_current_mark(1);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            $this->contains_input_validation(),
            $this->contains_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

    }

    public function test_adaptivefeedback_behaviour_test1_2() {

        $q = test_question_maker::make_question('stack', 'test1');
        $this->start_attempt_at_question($q, 'adaptive', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
                new question_pattern_expectation('/Find/'),
                $this->does_not_contain_input_validation(),
                $this->does_not_contain_prt_feedback(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation(),
                $this->check_no_stray_placeholders()
        );

        // Process a validate request.
        $this->process_submission(array('ans1' => '(v-a)^(n+1)/(n+1)', '-submit' => 1));

        $this->check_current_mark(null);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            $this->contains_input_validation(),
            $this->does_not_contain_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

        // Process a submit, but with a changed answer.
        $this->process_submission(array('ans1' => '(v-a)^(n+1)/(n+1)+c', 'ans1_val' => '(v-a)^(n+1)/(n+1)', '-submit' => 1));

        $this->check_current_mark(null);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            $this->contains_input_validation(),
            $this->does_not_contain_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

        // Process a submit with the correct answer.
        $this->process_submission(array('ans1' => '(v-a)^(n+1)/(n+1)+c', 'ans1_val' => '(v-a)^(n+1)/(n+1)+c', '-submit' => 1));

        // Verify.
        $this->check_current_mark(1);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            $this->contains_input_validation(),
            $this->contains_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

    }

    public function test_adaptivefeedback_behaviour_test1_3() {

        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test1');
        $this->start_attempt_at_question($q, 'adaptive', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
                new question_pattern_expectation('/Find/'),
                $this->does_not_contain_input_validation(),
                $this->does_not_contain_prt_feedback(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation(),
                $this->check_no_stray_placeholders()
        );

        // Process a validate request.
        // Invalid answer.
        $this->process_submission(array('ans1' => 'n*(v-a)^(n-1', '-submit' => 1));

        $this->check_current_mark(null);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            new question_pattern_expectation('/missing right/'),
            $this->contains_input_validation(),
            $this->does_not_contain_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

        // Valid answer.
        $this->process_submission(array('ans1' => 'n*(v-a)^(n-1)', '-submit' => 1));

        $this->check_current_mark(null);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            $this->contains_input_validation(),
            $this->does_not_contain_prt_feedback(),
            $this->check_no_stray_placeholders()
        );

        // Submit known mistake - look for specific feedback.
        $this->process_submission(array('ans1' => 'n*(v-a)^(n-1)', 'ans1_val' => 'n*(v-a)^(n-1)', '-submit' => 1));

        $this->check_current_mark(0);
        $this->check_current_output(
            new question_contains_tag_with_attributes('input', array('type' => 'text', 'name' => $this->quba->get_field_prefix($this->slot) . 'ans1')),
            new question_pattern_expectation('/differentiated instead!/'),
            $this->contains_input_validation(),
            $this->contains_prt_feedback(),
            $this->check_no_stray_placeholders()
        );
    }
}
