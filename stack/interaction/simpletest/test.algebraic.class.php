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
 * Unit tests for the stack_interaction_algebra class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../controller.class.php');
require_once(dirname(__FILE__) . '/../../options.class.php');

/**
 * Unit tests for stack_interaction_algebra.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_interaction_algebra_test extends UnitTestCase {

    public function test_internal_validate_parameter() {
        $el = stack_interaction_controller::make_element('algebraic', 'input', 'x^2');
        $this->assertTrue($el->validate_parameter('boxWidth', 30));
        $this->assertFalse($el->validate_parameter('boxWidth', -10));
        $this->assertFalse($el->validate_parameter('boxWidth', "30"));
        $this->assertFalse($el->validate_parameter('boxWidth', ''));
        $this->assertFalse($el->validate_parameter('boxWidth', null));
    }

    public function test_get_xhtml_blank() {
        $el = stack_interaction_controller::make_element('algebraic', 'ans1', 'x^2');
        $this->assertEqual('<input type="text" name="stack1__ans1" size="15" value="" />',
                $el->get_xhtml('', 'stack1__ans1', false));
    }

    public function test_get_xhtml_zero() {
        $el = stack_interaction_controller::make_element('algebraic', 'ans1', '0');
        $this->assertEqual('<input type="text" name="stack1__ans1" size="15" value="0" />',
                $el->get_xhtml('0', 'stack1__ans1', false));
    }

    public function test_get_xhtml_pre_filled() {
        $el = stack_interaction_controller::make_element('algebraic', 'test', 'x^2');
        $this->assertEqual('<input type="text" name="stack1__test" size="15" value="x+y" />',
                $el->get_xhtml('x+y', 'stack1__test', false));
    }

    public function test_get_xhtml_pre_filled_nasty_input() {
        $el = stack_interaction_controller::make_element('algebraic', 'test', 'x^2');
        $this->assertEqual('<input type="text" name="stack1__test" size="15" value="x&lt;y" />',
                $el->get_xhtml('x<y', 'stack1__test', false));
    }

    public function test_get_xhtml_max_length() {
        $el = stack_interaction_controller::make_element('algebraic', 'test', 'x^2');
        $this->assertEqual('<input type="text" name="stack1__test" size="15" value="x+y" />',
                $el->get_xhtml('x+y', 'stack1__test', false));
    }

    public function test_get_xhtml_disabled() {
        $el = stack_interaction_controller::make_element('algebraic', 'input', 'x^2');
        $this->assertEqual('<input type="text" name="stack1__input" size="15" value="x+1" readonly="readonly" />',
                $el->get_xhtml('x+1', 'stack1__input', true));
    }

    public function test_get_xhtml_different_size() {
        $el = stack_interaction_controller::make_element('algebraic', 'input', 'x^2');
        $el->set_parameter('boxWidth', 30);
        $this->assertEqual('<input type="text" name="stack1__input" size="30" value="x+1" />',
                $el->get_xhtml('x+1', 'stack1__input', false));
    }

    public function test_get_xhtml_syntaxhint() {
        $el = stack_interaction_controller::make_element('algebraic', 'sans1', '[a, b, c]');
        $el->set_parameter('syntaxHint', '[?, ?, ?]');
        $this->assertEqual('<input type="text" name="stack1__sans1" size="15" value="[?, ?, ?]" />',
                $el->get_xhtml('', 'stack1__sans1', false));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_interaction_controller::make_element('algebraic', 'sans1', 'x^2/(1+x^2)');
        list ($valid, $feedback) = $el->validate_student_response('x^2', $options);
        $this->assertEqual('score', $valid);
    }

    public function test_validate_student_response_2() {
        $options = new stack_options();
        $el = stack_interaction_controller::make_element('algebraic', 'sans1', 'x^2/(1+x^2)');
        list($valid, $feedback) = $el->validate_student_response('2x(1+x^2)', $options);
        $this->assertEqual('invalid', $valid);
    }

    public function test_validate_student_response_3() {
        $options = new stack_options();
        $el = stack_interaction_controller::make_element('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', true);
        $el->set_parameter('strictSyntax', false);
        list($valid, $feedback) = $el->validate_student_response('2x', $options);
        $this->assertEqual('score', $valid);
    }

    public function test_validate_student_response_4() {
        $options = new stack_options();
        $el = stack_interaction_controller::make_element('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', true);
        $el->set_parameter('strictSyntax', false);
        list($valid, $feedback) = $el->validate_student_response('2x(1+x^2)', $options);
        $this->assertEqual('score', $valid);
    }
}
