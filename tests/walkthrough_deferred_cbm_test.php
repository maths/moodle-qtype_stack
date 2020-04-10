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

// Unit tests for the Stack question type.
//
// Note that none of these tests include clicking the 'Check' button that dfexplicitvaldiation provies.
// That button is simply @author tjh238 way to trigger a save without navigating to a different page of the quiz.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class qtype_stack_walkthrough_deferred_cbm_test extends qtype_stack_walkthrough_test_base {

    public function test_test3_save_answers_to_all_parts_and_stubmit() {
        // Account for the changes in Moodle 2.6.
        if (question_cbm::adjust_fraction(1, question_cbm::HIGH) > 2) {
            // Moodle 2.6+.
            $outof = 4;
        } else {
            // Moodle 2.5-.
            $outof = 12;
        }

        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'deferredcbm', $outof);

        // Check the right behaviour is used.
        $this->assertEquals('dfcbmexplicitvaildate', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

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

        // Save a partially correct response. No certainty, so low will be assumed.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false'));

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

        // Submit all and finish.
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
        $this->check_output_contains_prt_feedback('unique');
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_test3_save_answers_to_all_parts_confirm_valid_and_stubmit() {
        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'deferredfeedback', 12);

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

        // Save a correct response, high certainty.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => '0', 'ans4' => 'true', '-certainty' => 3));

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
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'true', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Save a confirmation this is valid.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => '0', 'ans4' => 'true',
                                        'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => '0', '-certainty' => 3));

        $this->check_current_state(question_state::$complete);
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
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'true', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit all and finish.
        $this->quba->finish_all_questions();

        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(12);
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
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'true', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_test3_save_partially_complete_and_partially_invalid_response_then_stubmit() {
        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'deferredfeedback', 4);

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

        // Save a partially correct response.
        $this->process_submission(array('ans1' => 'x^3', 'ans2' => '(x +', 'ans3' => '', 'ans4' => 'true'));

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3');
        $this->check_output_contains_text_input('ans2', '(x +');
        $this->check_output_contains_text_input('ans3', '');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_does_not_contain_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit all and finish.
        $this->quba->finish_all_questions();

        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x^3', false);
        $this->check_output_contains_text_input('ans2', '(x +', false);
        $this->check_output_contains_text_input('ans3', '', false);
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_does_not_contain_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_contains_prt_feedback('odd');
        $this->check_output_does_not_contain_prt_feedback('even');
        $this->check_output_does_not_contain_prt_feedback('oddeven');
        $this->check_output_contains_prt_feedback('unique');
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), 'false', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_test3_save_completely_blank_response_then_stubmit() {
        // Create a stack question.
        $q = test_question_maker::make_question('stack', 'test3');
        $this->start_attempt_at_question($q, 'deferredfeedback', 4);

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

        // Try to save a blank response. This should not even create a new step..
        $this->process_submission(array('ans1' => '', 'ans2' => '', 'ans3' => '', 'ans4' => ''));

        $this->assertEquals(1, $this->quba->get_question_attempt($this->slot)->get_num_steps());

        // Submit all and finish.
        $this->quba->finish_all_questions();

        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', '', false);
        $this->check_output_contains_text_input('ans2', '', false);
        $this->check_output_contains_text_input('ans3', '', false);
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), '', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_test0_no_validation_required() {
        // Account for the changes in Moodle 2.6.
        if (question_cbm::adjust_fraction(1, question_cbm::HIGH) > 2) {
            // Moodle 2.6+.
            $outof = 1;
        } else {
            // Moodle 2.5-.
            $outof = 3;
        }

        // Create a stack question - we use test0, then replace the input with
        // a dropdown, to get a question that does not require validation.
        $q = test_question_maker::make_question('stack', 'test0');
        // @codingStandardsIgnoreStart
        $q->inputs['ans1'] = stack_input_factory::make(
                'dropdown', 'ans1', '[[1+x,false],[2+x,true]]');
        // @codingStandardsIgnoreEnd

        $sans = stack_ast_container::make_from_teacher_source('ans1');
        $sans->get_valid();
        $tans = stack_ast_container::make_from_teacher_source('2+x');
        $tans->get_valid();
        $node = new stack_potentialresponse_node($sans, $tans, 'EqualComAss');
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-T');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', false, 1, null, array($node), '0', 1);

        $this->start_attempt_at_question($q, 'deferredcbm', $outof);

        // Check the right behaviour is used.
        $this->assertEquals('deferredcbm', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans1',
                        array('' => stack_string('notanswered'), '1' => '1+x', '2' => '2+x'), null, true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Save a correct response, medium certainty.
        $this->process_submission(array('ans1' => '2', '-certainty' => 2));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans1',
                        array('' => stack_string('notanswered'), '1' => '1+x', '2' => '2+x'), '2', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit all and finish.
        $this->quba->finish_all_questions();
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->render();
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_contains_prt_feedback('firsttree');
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans1',
                        array('' => stack_string('notanswered'), '1' => '1+x', '2' => '2+x'), '2', false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }
}
