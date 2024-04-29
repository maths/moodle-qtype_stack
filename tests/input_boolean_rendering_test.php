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

namespace qtype_stack;

use stack_boolean_input;
use stack_input;
use stack_input_factory;
use stack_input_state;
use question_testcase;

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
 * @covers \stack_boolean_input
 */
class input_boolean_rendering_test extends question_testcase {

    protected function expected_choices() {
        return [
            stack_boolean_input::F => stack_string('false'),
            stack_boolean_input::T => stack_string('true'),
            stack_boolean_input::NA => stack_string('notanswered'),
        ];
    }

    public function test_render_not_answered() {
        $el = stack_input_factory::make('boolean', 'ans1', stack_boolean_input::T);
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), stack_boolean_input::NA),
                $el->render(new stack_input_state(
                        stack_input::BLANK, [stack_boolean_input::NA], '', '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_true() {
        $el = stack_input_factory::make('boolean', 'ans2', stack_boolean_input::T);
        $this->assert(new \question_contains_select_expectation('stack1__ans2', $this->expected_choices(),
                stack_boolean_input::T), $el->render(new stack_input_state(
                        stack_input::VALID, [stack_boolean_input::T], '', '', '', '', '', ''),
                        'stack1__ans2', false, null));
    }

    public function test_render_false() {
        $el = stack_input_factory::make('boolean', 'ans3', stack_boolean_input::T);
        $this->assert(new \question_contains_select_expectation('stack1__ans3', $this->expected_choices(),
                stack_boolean_input::F), $el->render(new stack_input_state(
                        stack_input::VALID, [stack_boolean_input::F], '', '', '', '', '', ''),
                        'stack1__ans3', false, null));
    }

    public function test_render_disabled() {
        $el = stack_input_factory::make('boolean', 'input', stack_boolean_input::T);
        $this->assert(new \question_contains_select_expectation('stack1__ans1', $this->expected_choices(),
                stack_boolean_input::NA, false), $el->render(new stack_input_state(
                        stack_input::BLANK, [], '', '', '', '', ''),
                        'stack1__ans1', true, null));
    }
}
