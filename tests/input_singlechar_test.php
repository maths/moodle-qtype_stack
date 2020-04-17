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

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

require_once(__DIR__ . '/../stack/input/factory.class.php');

// Unit tests for stack_singlechar_input.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_singlechar_input_test extends qtype_stack_testcase {

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

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('singleChar', 'sans1', 'A');
        $state = $el->validate_student_response(array('sans1' => 'a'), $options, 'A', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">'
                . '<span class="nolink">\( A \)</span></span>.', $el->get_teacher_answer_display('A', 'A'));
    }
}
