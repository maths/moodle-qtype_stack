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
 * Unit tests for stack_utils.
 *
 * @copyright  2012 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../utils.class.php');

/**
 * Unit tests for stack_utils.
 *
 * @copyright  2012 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_utils_test extends UnitTestCase {

    public function test_matching_pairs() {
        $this->assertTrue(stack_utils::check_matching_pairs('Hello $world$!', '$'));
        $this->assertFalse(stack_utils::check_matching_pairs('Hello @world!', '@'));
        $this->assertTrue(stack_utils::check_matching_pairs('', '$'));
    }

    public function test_substring_between() {
        $this->assertEqual(array('[hello]', 0, 6), stack_utils::substring_between('[hello] world!', '[', ']'));
        $this->assertEqual(array('[world]', 6, 12), stack_utils::substring_between('hello [world]!', '[', ']'));
        $this->assertEqual(array('[world]', 8, 14), stack_utils::substring_between('[hello] [world]!', '[', ']', 8));
        $this->assertEqual(array('[world]', 8, 14), stack_utils::substring_between('[hello] [world]!', '[', ']', 1));

        $this->assertEqual(array('$hello$', 0, 6), stack_utils::substring_between('$hello$ world!', '$', '$'));
        $this->assertEqual(array('$world$', 6, 12), stack_utils::substring_between('hello $world$!', '$', '$'));
        $this->assertEqual(array('$world$', 8, 14), stack_utils::substring_between('$hello$ $world$!', '$', '$', 8));
        $this->assertEqual(array('$ $', 6, 8), stack_utils::substring_between('$hello$ $world$!', '$', '$', 1));

        $this->assertEqual(array('[he[ll]o]', 0, 8), stack_utils::substring_between('[he[ll]o] world!', '[', ']'));
        $this->assertEqual(array('[[[]w[o]r[[l]d]]]', 6, 22), stack_utils::substring_between('hello [[[]w[o]r[[l]d]]]!', '[', ']'));
    }

}
