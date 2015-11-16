<?php
// This file is part of STACK - http://stack.bham.ac.uk/
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
 * Unit tests for the stack_algebra_input class.
 *
 * @copyright  2015 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../stack/input/factory.class.php');

/**
 * Unit tests for stack_equiv_input.
 *
 * @copyright  2015 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_equiv_input_test extends qtype_stack_testcase {

    public function test_internal_validate_parameter() {
        $el = stack_input_factory::make('equiv', 'input', 'x^2');
        $this->assertTrue($el->validate_parameter('boxWidth', 30));
        $this->assertFalse($el->validate_parameter('boxWidth', -10));
        $this->assertFalse($el->validate_parameter('boxWidth', "30"));
        $this->assertFalse($el->validate_parameter('boxWidth', ''));
        $this->assertFalse($el->validate_parameter('boxWidth', null));
        $this->assertTrue($el->validate_parameter('showValidation', 1));
        $this->assertFalse($el->validate_parameter('showValidation', true));
        $this->assertFalse($el->validate_parameter('showValidation', 5));
    }

    public function test_render_blank() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        $this->assertEquals('<table><tr><td><textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="10"></textarea></td><td><div class="stackinputfeedback" id="stack1__ans1_val"><input type="hidden" name="stack1__ans1_val" value="[]" /></div></td></tr></table>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-2*x+1=0]');
        $state = $el->validate_student_response(array('sans1' => 'x^2-2*x+1=0'), $options, '[x^2-2*x+1=0]', null);
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\nx=2 or x=3"), $options, '[x^2-5*x+6=0]', null);
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\n x={2,3}"), $options, '[x^2-5*x+6=0]', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('  Sets are not allowed when reasoning by equivalence.', $state->errors);
    }

}
