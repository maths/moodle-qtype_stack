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

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/answertest/controller.class.php');

// Unit tests for stack_anstest_atstring.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_anstest_atstring_test extends basic_testcase {

    public function test_is_true_for_equal_strings() {
        $at = new stack_ans_test_controller('String',
                stack_ast_container::make_from_teacher_source('"hello"'),
                stack_ast_container::make_from_teacher_source('"hello"'),
                null);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_strings() {
        $at = new stack_ans_test_controller('String',
                stack_ast_container::make_from_teacher_source('"hello"'),
                stack_ast_container::make_from_teacher_source('"heloo"'),
                null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_for_strings_with_different_case() {
        $at = new stack_ans_test_controller('String',
                stack_ast_container::make_from_teacher_source('"Hello"'),
                stack_ast_container::make_from_teacher_source('"hello"'),
                null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }
}
