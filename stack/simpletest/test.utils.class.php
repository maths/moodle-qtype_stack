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

    public function test_check_bookends() {
        $this->assertIdentical('left', stack_utils::check_bookends('x+1)^2', '(', ')'));
        $this->assertIdentical('right', stack_utils::check_bookends('(x+1', '(', ')'));
        $this->assertIdentical('left', stack_utils::check_bookends('(y^2+1))', '(', ')'));
        $this->assertIdentical('left', stack_utils::check_bookends('[sin(x)+1)', '(', ')'));
        $this->assertIdentical('right', stack_utils::check_bookends('[sin(x)+1)', '[', ']'));
        $this->assertIdentical(true, stack_utils::check_bookends('x+1', '(', ')'));
        $this->assertIdentical(true, stack_utils::check_bookends('x+1', '[', ']'));
        $this->assertIdentical(true, stack_utils::check_bookends('x+1', '{', '}'));
        $this->assertIdentical(true, stack_utils::check_bookends('(sin(x)+1)', '[', ']'));
        $this->assertIdentical(true, stack_utils::check_bookends('(sin(x)+1)', '(', ')'));
        $this->assertIdentical(true, stack_utils::check_bookends('[sin(x)+1)', '{', '}'));
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

    public function test_all_substring_between() {
        $this->assertEqual(array(), stack_utils::all_substring_between('hello world!', '[', ']'));
        $this->assertEqual(array('hello'), stack_utils::all_substring_between('[hello] world!', '[', ']'));
        $this->assertEqual(array('hello', 'world'), stack_utils::all_substring_between('[hello] [world]!', '[', ']'));

        $this->assertEqual(array(), stack_utils::all_substring_between('hello world!', '$'));
        $this->assertEqual(array('hello'), stack_utils::all_substring_between('$hello$ world!', '$'));
        $this->assertEqual(array('hello', 'world'), stack_utils::all_substring_between('$hello$ $world$!', '$'));

        // This is current behaviour, but I am not sure it is correct.
        $this->assertEqual(array('hello', 'wor'), stack_utils::all_substring_between('[he[llo] [wor]ld]!', '[', ']'));
    }

    public function test_replace_between() {
        $this->assertEqual('hello world!', stack_utils::replace_between('hello world!', '[', ']', array()));
        $this->assertEqual('[goodbye] world!', stack_utils::replace_between('[hello] world!', '[', ']', array('goodbye')));
        $this->assertEqual('[goodbye] [all]!', stack_utils::replace_between('[hello] [world]!', '[', ']', array('goodbye', 'all')));

        $this->assertEqual('hello world!', stack_utils::replace_between('hello world!', '$', '$', array()));
        $this->assertEqual('$goodbye$ world!', stack_utils::replace_between('$hello$ world!', '$', '$', array('goodbye')));
        $this->assertEqual('$goodbye$ $all$!', stack_utils::replace_between('$hello$ $world$!', '$', '$', array('goodbye', 'all')));

        $this->expectException();
        $this->assertEqual('goodbye all!', stack_utils::replace_between('$hello$ $world$!', '$', '$', array('1', '2', '3')));
    }

    public function test_underscore() {
        $this->assertEqual('hello_world!', stack_utils::underscore('hello world!'));
        $this->assertEqual('he_he_hello_world_', stack_utils::underscore('he-he-hello world!', array('!')));
    }

    public function test_list_to_array() {
        // Do not recurse over lists
        $a=array();
        $strin = '';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $a=array();
        $strin = '[]';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $a=array('1');
        $strin = '[1]';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $a=array('1', '2');
        $strin = '[1,2]';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $strin = '[x^2, sin(x)]';
        $a = array('x^2', ' sin(x)');
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $a=array('1', 'x+y');
        $strin = '[1,x+y]';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $a=array('[1,2]');
        $strin = '[[1,2]]';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $strin = '[[1,2,3], {x^2,x^3}]';
        $a = array('[1,2,3]', ' {x^2,x^3}');
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        $a=array('1', '1/sum([1,3])', 'matrix([1],[2])');
        $strin = '[1,1/sum([1,3]),matrix([1],[2])]';
        $this->assertEqual($a, stack_utils::list_to_array($strin, false));

        // Recurse over lists
        $strin = '[[1,2,3], {x^2,x^3}]';
        $a = array(array('1', '2', '3'), ' {x^2,x^3}');
        $this->assertEqual($a, stack_utils::list_to_array($strin, true));
    }
}
