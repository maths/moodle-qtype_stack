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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');


// Unit tests for the Stack question type with the immediate feedback behaviour.
//
// Note that none of these tests include clicking the 'Check' button that dfexplicitvaldiation provies.
// That button is simply @author tjh238 way to trigger a save without navigating to a different page of the quiz.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class qtype_stack_walkthrough_immediate_feedback_test extends qtype_stack_walkthrough_test_base {

    public function test_test3_partially_right_then_right() {
        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'immediatefeedback', 4);

        // Check the right behaviour is used.
        $this->assertEquals('immediatefeedback', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1');
        $this->check_output_contains_text_input('ans2');
        $this->check_output_contains_text_input('ans3');
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), '', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Save a partially correct response for validation.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false',
                '-submit' => 1));

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3');
        $this->check_output_contains_text_input('ans2', 'x^2');
        $this->check_output_contains_text_input('ans3', 'x');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Re-submit after validation validation.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false',
                                        'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => 'x', '-submit' => 1));

        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2.5);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3', false);
        $this->check_output_contains_text_input('ans2', 'x^2', false);
        $this->check_output_contains_text_input('ans3', 'x', false);
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_contains_prt_feedback('odd');
        $this->check_output_contains_prt_feedback('even');
        $this->check_output_contains_prt_feedback('oddeven');
        $this->check_output_contains_prt_feedback('unique');
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_test3_sumbit_and_finish_before_validating() {
        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'immediatefeedback', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1');
        $this->check_output_contains_text_input('ans2');
        $this->check_output_contains_text_input('ans3');
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), '', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Save a partially correct, partially complete response.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => ''));

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3');
        $this->check_output_contains_text_input('ans2', 'x^2');
        $this->check_output_contains_text_input('ans3', 'x');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit all and finish before validating.
        $this->quba->finish_all_questions();

        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2.5);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3', false);
        $this->check_output_contains_text_input('ans2', 'x^2', false);
        $this->check_output_contains_text_input('ans3', 'x', false);
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_contains_prt_feedback('odd');
        $this->check_output_contains_prt_feedback('even');
        $this->check_output_contains_prt_feedback('oddeven');
        $this->check_output_does_not_contain_prt_feedback('unique');
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), '', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_test3_save_invalid_response_correct_then_stubmit() {
        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'immediatefeedback', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1');
        $this->check_output_contains_text_input('ans2');
        $this->check_output_contains_text_input('ans3');
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), '', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit a partially correct response.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => '(x +', 'ans3' => '0', 'ans4' => 'true', '-submit' => 1));

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3');
        $this->check_output_contains_text_input('ans2', '(x +');
        $this->check_output_contains_text_input('ans3', '0');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Try to submit again without editing. Shoud not be accepted.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => '(x +', 'ans3' => '0', 'ans4' => 'true',
                                        'ans1_val' => 'x^3', 'ans3_val' => '0', '-submit' => 1));

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3');
        $this->check_output_contains_text_input('ans2', '(x +');
        $this->check_output_contains_text_input('ans3', '0');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Now fix the resonse and submit. Should only be validated.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => '0', 'ans4' => 'true',
                                        'ans1_val' => 'x^3', 'ans3_val' => '0', '-submit' => 1));

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3');
        $this->check_output_contains_text_input('ans2', 'x^2');
        $this->check_output_contains_text_input('ans3', '0');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit again. Should now be graded.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => '0', 'ans4' => 'true',
                                        'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => '0', '-submit' => 1));

        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(4);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3', false);
        $this->check_output_contains_text_input('ans2', 'x^2', false);
        $this->check_output_contains_text_input('ans3', '0', false);
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
                $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_contains_prt_feedback('odd');
        $this->check_output_contains_prt_feedback('even');
        $this->check_output_contains_prt_feedback('oddeven');
        $this->check_output_contains_prt_feedback('unique');
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }
}
