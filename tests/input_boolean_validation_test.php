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
require_once(__DIR__ . '/../stack/input/factory.class.php');
require_once(__DIR__ . '/../stack/input/boolean/boolean.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for stack_boolean_input_test.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_boolean_input_validation_test extends qtype_stack_testcase {
    public function test_validate_student_response_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'true');
        $state = $el->validate_student_response(array('sans1' => 'true'), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('true', $state->contentsmodified);
        $this->assertEquals('\[ \mathbf{True} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_false() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'true');
        $state = $el->validate_student_response(array('sans1' => 'false'), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('false', $state->contentsmodified);
        $this->assertEquals('\[ \mathbf{False} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_na() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'true');
        $state = $el->validate_student_response(array(), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_student_response_error() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'true');
        $state = $el->validate_student_response(array('sans1' => 'frog'), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('frog', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">frog</span>', $state->contentsdisplayed);
    }

    public function test_validate_student_response_emptyanswer() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'EMPTYANSWER');
        $state = $el->validate_student_response(array('sans1' => 'true'), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('true', $state->contentsmodified);
        $this->assertEquals('\[ \mathbf{True} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_emptyanswer_option_sa() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'true');
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(array('sans1' => ''), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
    }

    public function test_validate_student_response_emptyanswer_option_ta() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'sans1', 'EMPTYANSWER');
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(array('sans1' => ''), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
    }

    public function test_validate_hideanswer() {
        $options = new stack_options();
        $el = stack_input_factory::make('boolean', 'state', 'false');
        $el->set_parameter('options', 'hideanswer');
        $state = $el->validate_student_response(array('state' => 'true'), $options, 'false',
                new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('true', $state->contentsmodified);
        $this->assertEquals('', $el->get_teacher_answer_display("[SOME JSON]", "\[ \mbox{[SOME MORE JSON]} \]"));
    }
}
