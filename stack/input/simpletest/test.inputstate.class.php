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
 * Unit tests for the stack_input_state class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../inputbase.class.php');


/**
 * Unit tests for stack_input_state.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_input_state_test extends UnitTestCase {

    public function test_create_and_get() {
        $state = new stack_input_state(stack_input::INVALID, 'frog', 'frog', 'Your answer is not an expression.');
        $this->assertEqual(stack_input::INVALID, $state->status);
        $this->assertEqual('frog', $state->contents);
        $this->assertEqual('frog', $state->contentsinterpreted);
        $this->assertEqual('Your answer is not an expression.', $state->errors);
    }

    public function test_unrecognised_property() {
        $state = new stack_input_state(stack_input::INVALID, 'frog', 'frog', 'Your answer is not an expression.');
        $this->expectException();
        $x = $state->unknownproperty;
    }
}
