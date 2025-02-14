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
use question_state;
use question_pattern_expectation;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');


// Tests that walk STACK questions that are special cases.
// Specifically a question with neither inputs nor PRTs,
// and a question with inputs but no PRTs.
//
// @copyright 2013 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class walkthrough_survey_test extends qtype_stack_walkthrough_test_base {

    public function test_neither_inputs_nor_prts() {

        // Create the stack question 'test0'.
        $q = \test_question_maker::make_question('stack', 'information');
        $this->start_attempt_at_question($q, 'adaptive', 1);

        // Check the right behaviour is used.
        $this->assertEquals('informationitem', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Process a submit.
        $this->process_submission(['-seen' => 1]);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit all and finish.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$finished);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_contains_general_feedback_expectation($q),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_no_prts_left_blank() {

        // Create the stack question 'test0'.
        $q = \test_question_maker::make_question('stack', 'survey');
        $this->start_attempt_at_question($q, 'adaptive', 1);

        // Check the right behaviour is used.
        $this->assertEquals('manualgraded', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();

        // Submit all and finish.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', null, false);
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }

    public function test_no_prts_answered() {

        // Create the stack question 'test0'.
        $q = \test_question_maker::make_question('stack', 'survey');
        $this->start_attempt_at_question($q, 'interactive', 1);

        // Check the right behaviour is used.
        $this->assertEquals('manualgraded', $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();

        // Save a validated response.
        $this->process_submission(['ans1' => 'e^(i*pi)=-1', 'ans1_val' => 'e^(i*pi)=-1']);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'e^(i*pi)=-1');
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );

        // Submit all and finish.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_text_input('ans1', 'e^(i*pi)=-1', false);
        $this->check_output_contains_input_validation('ans1');
        $this->check_output_does_not_contain_prt_feedback();
        $this->check_output_does_not_contain_stray_placeholders();
        $this->check_current_output(
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation()
        );
    }
}
