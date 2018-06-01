<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/input/factory.class.php');

// Unit tests for stack_singlechar_input.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_singlechar_input_test extends basic_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('singleChar', 'ans1', null);
        $this->assertEquals('<input type="text" name="question__ans1" id="question__ans1" size="1" maxlength="1" ' .
                'value="" autocapitalize="none" spellcheck="false" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', '', ''),
                        'question__ans1', false, null));
    }

    public function test_render_pre_filled() {
        $el = stack_input_factory::make('singleChar', 'test', null);
        $this->assertEquals('<input type="text" name="question__ans1" id="question__ans1" size="1" maxlength="1" ' .
                'value="Y" autocapitalize="none" spellcheck="false" />',
                $el->render(new stack_input_state(stack_input::VALID, array('Y'), '', '', '', '', ''),
                        'question__ans1', false, null));
    }

    public function test_render_disabled() {
        $el = stack_input_factory::make('singleChar', 'input', null);
        $expected = '<input type="text" name="question__stack1" id="question__stack1" size="1" maxlength="1" ' .
            'value="a" autocapitalize="none" spellcheck="false" readonly="readonly" />';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(stack_input::VALID, array('a'), '', '', '', '', ''),
                        'question__stack1', true, null));
    }
}
