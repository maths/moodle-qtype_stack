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
 * Unit tests for the stack_dropdown_input class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../stack/input/factory.class.php');


/**
 * Unit tests for stack_dropdown_input.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_dropdown_input_test extends qtype_stack_walkthrough_test_base {
    protected function expected_choices() {
        return array(
            '' => stack_string('notanswered'),
            'x+1' => 'x+1',
            'x+2' => 'x+2',
            'x+3' => 'x+3',
        );
    }

    protected function make_dropdown() {
        $el = stack_input_factory::make('dropdown', 'ans1', 'x+1');
        $el->set_parameter('ddl_values', 'x+1,x+2,x+3');
        return $el;
    }

    public function test_render_not_answered() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_x_plus_1() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+1'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, array('x+1'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_x_plus_3() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+3'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, array('x+3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_disabled() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), '', false),
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', true));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => ''), $options, 'x+1', null);
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => 'x+1'), $options, 'x+1', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_x_plus_3() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => 'x+3'), $options, 'x+1', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_error() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => 'x+4'), $options, 'x+1', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
    }
}
