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

use stack_exception;
use stack_input;
use stack_input_state;
use basic_testcase;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/input/inputbase.class.php');

// Unit tests for stack_input_state.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_input_state
 */
class inputstate_test extends basic_testcase {

    public function test_create_and_get() {
        $state = new stack_input_state(stack_input::INVALID, ['frog'],
                'frog', 'frog', 'Your answer is not an expression.', 'CASError', '');
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals(['frog'], $state->contents);
        $this->assertEquals('frog', $state->contentsdisplayed);
        $this->assertEquals('Your answer is not an expression.', $state->errors);
        $this->assertEquals('CASError', $state->note);
    }

    public function test_constructor() {
        $this->expectException(stack_exception::class);
        $state = new stack_input_state(stack_input::INVALID, 'frog',
                'frog', 'frog', 'Your answer is not an expression.', '', '');
    }

    public function test_unrecognised_property() {
        $this->expectException(stack_exception::class);
        $state = new stack_input_state(stack_input::INVALID, ['frog'],
                'frog', 'frog', 'Your answer is not an expression.', '', '');
        $x = $state->unknownproperty;
    }
}
