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

namespace qtype_stack;

use qtype_stack_walkthrough_test_base;
use stack_boolean_input;
use question_state;
use question_pattern_expectation;
use question_hint;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for the Stack question type with the interactive behaviour.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class walkthrough_interactive_test extends qtype_stack_walkthrough_test_base {

    public function test_test3_partially_right_the_right() {
        // Create a stack question.
        $q = \test_question_maker::make_question('stack', 'test3');
        $q->hints = [
            new question_hint(1, 'This is the first hint.', FORMAT_HTML),
            new question_hint(2, 'This is the second hint.', FORMAT_HTML),
        ];

        $this->start_attempt_at_question($q, 'interactive', 4);

        // Check the right behaviour is used.
        $this->assertEquals('interactivecountback', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

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
        $this->process_submission(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false',
                '-submit' => 1]);
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $expected = '';
        $this->check_response_summary($expected);
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

        // Re-submit after validation.
        $this->process_submission(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false',
                                        'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => 'x', '-submit' => 1]);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $expected = 'Seed: 1; ans1: x^3 [score]; ans2: x^2 [score]; ans3: x [score]; ans4: false [score]; ' .
            'odd: # = 1 | odd-0-1; even: # = 1 | even-0-1; oddeven: # = 0.5 | oddeven-0-1 | oddeven-1-0; ' .
            'unique: # = 0 | unique-0-0';
        $this->check_response_summary($expected);
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
                $this->get_contains_hint_expectation('This is the first hint.')
        );

        // Try again.
        $this->process_submission(['-tryagain' => 1]);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $expected = '';
        $this->check_response_summary($expected);
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

        // Now change to a correct response.
        $this->process_submission(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => '0', 'ans4' => 'true', '-submit' => 1]);

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->render();
        $expected = '';
        $this->check_response_summary($expected);
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

        // Resubmit after validation.
        $this->process_submission(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => '0', 'ans4' => 'true',
                                        'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => '0', '-submit' => 1]);

        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2.6);
        $this->check_prt_score('odd', 1, 0);
        $this->check_prt_score('even', 1, 0);
        $this->check_prt_score('oddeven', 1, 0);
        $this->check_prt_score('unique', 1, 0);
        $this->render();
        $expected = 'Seed: 1; ans1: x^3 [score]; ans2: x^2 [score]; ans3: 0 [score]; ans4: true [score]; ' .
            'odd: # = 1 | odd-0-1; even: # = 1 | even-0-1; oddeven: # = 1 | oddeven-0-1 | oddeven-1-1; ' .
            'unique: # = 1 | ATLogic_True. | unique-0-1';
        $this->check_response_summary($expected);
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

    public function test_test3_partially_right_three_times_no_validation() {
        // Create a stack question.
        $q = \test_question_maker::make_question('stack', 'test3');
        $q->hints = [
            new question_hint(1, 'This is the first hint.', FORMAT_HTML),
            new question_hint(2, 'This is the second hint.', FORMAT_HTML),
        ];

        // Change input options so validation is not required.
        $q->inputs['ans1']->set_parameter('mustVerify', false);
        $q->inputs['ans2']->set_parameter('mustVerify', false);
        $q->inputs['ans3']->set_parameter('mustVerify', false);
        $this->start_attempt_at_question($q, 'interactive', 4);

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

        // Submit a wrong response.
        $this->process_submission(['ans1' => 'x + 1', 'ans2' => 'x + 1', 'ans3' => 'x + 1',
                'ans4' => 'false', '-submit' => 1]);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x + 1', false);
        $this->check_output_contains_text_input('ans2', 'x + 1', false);
        $this->check_output_contains_text_input('ans3', 'x + 1', false);
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
                $this->get_contains_hint_expectation('This is the first hint.')
        );

        // Try again.
        $this->process_submission(['-tryagain' => 1]);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x + 1');
        $this->check_output_contains_text_input('ans2', 'x + 1');
        $this->check_output_contains_text_input('ans3', 'x + 1');
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

        // Submit a partially correct response.
        $this->process_submission(['ans1' => 'x', 'ans2' => 'x', 'ans3' => 'x',
                'ans4' => 'false', '-submit' => 1]);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x', false);
        $this->check_output_contains_text_input('ans2', 'x', false);
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
                $this->get_contains_hint_expectation('This is the second hint.')
        );

        // Try again.
        $this->process_submission(['-tryagain' => 1]);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x');
        $this->check_output_contains_text_input('ans2', 'x');
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

        // Submit a partially correct response.
        $this->process_submission(['ans1' => 'x', 'ans2' => 'x^2', 'ans3' => 'x - 1',
                'ans4' => 'true', '-submit' => 1]);

        $this->check_current_state(question_state::$gradedpartial);
        // @codingStandardsIgnoreStart
        // The  correct mark calculation is:
        // odd PRT:     0, 1,   1, penalty 0.4: 0.6
        // even PRT:    0, 0,   1, penalty 0.8: 0.2
        // oddeven PRT: 0, 0.5, 0, penalty 1.2: 0
        // unique PRT:  0, 0,   1, penalty 1:   0
        // Total:                               0.8.
        // @codingStandardsIgnoreEnd
        $this->check_prt_score('odd', 1, 0);
        $this->check_prt_score('even', 1, 0);
        $this->check_prt_score('oddeven', 0, 0.4);
        $this->check_prt_score('unique', 1, 0);
        $this->check_current_mark(0.8);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x', false);
        $this->check_output_contains_text_input('ans2', 'x^2', false);
        $this->check_output_contains_text_input('ans3', 'x - 1', false);
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
        $q = \test_question_maker::make_question('stack', 'test3');
        $q->hints = [
            new question_hint(1, 'This is the first hint.', FORMAT_HTML),
            new question_hint(2, 'This is the second hint.', FORMAT_HTML),
        ];

        $this->start_attempt_at_question($q, 'interactive', 4);

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
        $this->process_submission(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false']);

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

    public function test_divide_by_0() {
        // Create a stack question.
        $q = \test_question_maker::make_question('stack', 'divide');
        $this->start_attempt_at_question($q, 'interactive', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_prt_score('prt1', null, null);
        $this->render();
        $this->check_output_contains_text_input('ans1');
        $this->check_output_does_not_contain_input_validation();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Validate the response 0.
        $this->process_submission(['ans1' => '0', '-submit' => 1]);

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_prt_score('prt1', null, null);
        $this->render();
        $this->check_output_contains_text_input('ans1', '0');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();

        // Now submit the response 0. Causes a divide by 0.
        $this->process_submission(['ans1' => '0', 'ans1_val' => '0', '-submit' => 1]);

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_prt_score('prt1', null, null);
        $this->render();
        $this->check_output_contains_text_input('ans1', '0');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_does_not_contain_prt_feedback('prt1');
        $this->check_output_does_not_contain_stray_placeholders();

        // Validate the response 1/2 (correct).
        $this->process_submission(['ans1' => '1/2', 'ans1_val' => '0', '-submit' => 1]);

        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_prt_score('prt1', null, null);
        $this->render();
        $this->check_output_contains_text_input('ans1', '1/2');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();

        // Now submit the response 1/2.
        $this->process_submission(['ans1' => '1/2', 'ans1_val' => '1/2', '-submit' => 1]);

        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(1); // No penalties applied.
        $this->check_prt_score('prt1', 1, 0);
        $this->render();
        $this->check_output_contains_text_input('ans1', '1/2', false);
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_prt_feedback('prt1');
        $this->check_output_does_not_contain_stray_placeholders();
    }

    public function test_test3_submit_and_finish_incomplete() {
        // Create a stack question.
        $q = \test_question_maker::make_question('stack', 'test3');
        $q->hints = [
            new question_hint(1, 'This is the first hint.', FORMAT_HTML),
            new question_hint(2, 'This is the second hint.', FORMAT_HTML),
        ];

        $this->start_attempt_at_question($q, 'interactive', 4);

        // Check the right behaviour is used.
        $this->assertEquals('interactivecountback', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

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

        // Save a response with three parts incorrect and one part not answered.
        $this->process_submission(['ans1' => 'x+1', 'ans1_val' => 'x+1',
                'ans2' => 'x+1', 'ans2_val' => 'x+1', 'ans3' => 'x+1', 'ans3_val' => 'x+1', 'ans4' => '']);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x+1');
        $this->check_output_contains_text_input('ans2', 'x+1');
        $this->check_output_contains_text_input('ans3', 'x+1');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_contains_input_validation('ans2');
        $this->check_output_contains_input_validation('ans3');
        $this->check_output_does_not_contain_input_validation('ans4');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_select_expectation('ans4', stack_boolean_input::get_choices(), '', true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Resubmit after validation.
        $this->finish();

        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);
        $this->check_prt_score('odd', 0, 0.4, 1);
        $this->check_prt_score('even', 0, 0.4, 1);
        $this->check_prt_score('oddeven', 0, 0.4, 1);
        $this->check_prt_score('unique', null, null, 1);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'x+1', false);
        $this->check_output_contains_text_input('ans2', 'x+1', false);
        $this->check_output_contains_text_input('ans3', 'x+1', false);
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
}
