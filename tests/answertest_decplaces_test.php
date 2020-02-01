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

// Unit tests for stack_anstest_decplaces.
//
// @copyright  2012 The University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/castext.class.php');
require_once(__DIR__ . '/../stack/answertest/anstest.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/answertest/controller.class.php');

/**
 * @group qtype_stack
 */
class stack_anstest_atdecplaces_test extends qtype_stack_testcase {

    public function test_is_true_for_equal_expressions() {
        $at = new stack_ans_test_controller('NumDecPlaces',
                stack_ast_container::make_from_teacher_source('1.01'),
                stack_ast_container::make_from_teacher_source('1.01'),
                stack_ast_container::make_from_teacher_source('2'),
                null);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertTrue(stack_ans_test_controller::required_atoptions('NumDecPlaces'));
    }

    public function test_is_false_for_unequal_expressions() {
        $at = new stack_ans_test_controller('NumDecPlaces',
            stack_ast_container::make_from_teacher_source('2'),
            stack_ast_container::make_from_teacher_source('1'),
            stack_ast_container::make_from_teacher_source('4'),
            null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_2() {
        $at = new stack_ans_test_controller('NumDecPlaces',
            stack_ast_container::make_from_teacher_source('2.000'),
            stack_ast_container::make_from_teacher_source('1'),
            stack_ast_container::make_from_teacher_source('3'),
            null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATNumDecPlaces_Correct. ATNumDecPlaces_Not_equiv.', $at->get_at_answernote());
    }
}
