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

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

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
        $this->assertEquals('<table><tr><td><textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="25"></textarea></td>' .
                '<td><div class="stackinputfeedback" id="stack1__ans1_val">' .
                '<input type="hidden" name="stack1__ans1_val" value="[]" /></div></td></tr></table>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_firstline() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        $el->set_parameter('syntaxHint', 'firstline');
        $this->assertEquals('<table><tr><td><textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="25">x^2=4</textarea></td>' .
                '<td><div class="stackinputfeedback" id="stack1__ans1_val">' .
                '<input type="hidden" name="stack1__ans1_val" value="[]" /></div></td></tr></table>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, '[x^2=4,x=2 or x=-2]'));
    }

    public function test_render_hint() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        // Note the syntax hint must be a list.
        $el->set_parameter('syntaxHint', '[x^2=3]');
        $this->assertEquals('<table><tr><td><textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="25">x^2=3</textarea></td>' .
                '<td><div class="stackinputfeedback" id="stack1__ans1_val">' .
                '<input type="hidden" name="stack1__ans1_val" value="[]" /></div></td></tr></table>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, '[x^2=4,x=2 or x=-2]'));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-2*x+1=0]');
        $state = $el->validate_student_response(array('sans1' => 'x^2-2*x+1=0'), $options, '[x^2-2*x+1=0]', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $excont = array(0 => 'x^2-2*x+1=0');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[x^2-2*x+1=0]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\ &x^2-2\cdot x+1=0\cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
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

    public function test_validate_student_response_insert_stars_0_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]');
        $el->set_parameter('insertStars', 2);
        $el->set_parameter('strictSyntax', false);

        $state = $el->validate_student_response(array('sans1' => "(x-1)(x+4)\n=x^2-x+4x-4\n=x^2+3x-4"), $options,
                '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', null);
        $excont = array(0 => '(x-1)(x+4)', 1 => '=x^2-x+4x-4', 2 => '=x^2+3x-4');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[(x-1)*(x+4),stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\ &\left(x-1\right)\cdot \left(x+4\right)\cr  \color{green}{\checkmark}'.
                '&=x^2-x+4\cdot x-4\cr  \color{green}{\checkmark}&=x^2+3\cdot x-4\cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('  ', $state->errors);
    }

    public function test_validate_student_response_insert_stars_0_false() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);

        $state = $el->validate_student_response(array('sans1' => "(x-1)(x+4)"), $options,
                '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $excont = array(0 => '(x-1)*(x+4)');
        $this->assertEquals(' You seem to be missing * characters. Perhaps you meant to type '.
                '<span class="stacksyntaxexample">(x-1)<font color="red">*</font>(x+4)</span>.', $state->errors);
    }

    public function test_validate_student_response_equational_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]');
        $state = $el->validate_student_response(array('sans1' => "(x-1)*(x+4)\n=x^2-x+4*x-4\n=x^2+3*x-4"), $options,
                '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', null);
        $excont = array(0 => '(x-1)*(x+4)', 1 => '=x^2-x+4*x-4', 2 => '=x^2+3*x-4');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[(x-1)*(x+4),stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\ &\left(x-1\right)\cdot \left(x+4\right)\cr  \color{green}{\checkmark}'.
                '&=x^2-x+4\cdot x-4\cr  \color{green}{\checkmark}&=x^2+3\cdot x-4\cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('  ', $state->errors);
    }

}

