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

// Unit tests for various AST container features.
//
// @copyright  2019 Aalto University
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../stack/cas/ast.container.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');

/**
 * @group qtype_stack
 */
class stack_astcontainer_test extends qtype_stack_testcase {

    public function test_types() {
        $matrix = stack_ast_container::make_from_teacher_source('foo:matrix([1,2],[3,4])', 'type test', new stack_cas_security());
        $this->assertTrue($matrix->is_matrix());
        $this->assertFalse($matrix->is_int());
        $this->assertFalse($matrix->is_float());

        $string = stack_ast_container::make_from_teacher_source('foo:"matrix([1,2],[3,4])"', 'type test', new stack_cas_security());
        $this->assertTrue($string->is_string());
        $this->assertFalse($string->is_matrix());
        $this->assertFalse($string->is_int());
        $this->assertFalse($string->is_float());
        $this->assertEquals(-1, $string->is_list());

        $float = stack_ast_container::make_from_teacher_source('0.23e2', 'type test', new stack_cas_security());
        $this->assertFalse($float->is_string());
        $this->assertFalse($float->is_matrix());
        $this->assertFalse($float->is_int());
        $this->assertTrue($float->is_float());
        $this->assertEquals(-1, $float->is_list());

        $int = stack_ast_container::make_from_teacher_source('234545323423446526524562', 'type test', new stack_cas_security());
        $this->assertFalse($int->is_string());
        $this->assertFalse($int->is_matrix());
        $this->assertTrue($int->is_int());
        $this->assertFalse($int->is_float());
        $this->assertEquals(-1, $int->is_list());

        $int = stack_ast_container::make_from_teacher_source('x:-234545323423446526524562', 'type test', new stack_cas_security());
        $this->assertFalse($int->is_string());
        $this->assertFalse($int->is_matrix());
        $this->assertTrue($int->is_int());
        $this->assertFalse($int->is_float());
        $this->assertEquals(-1, $int->is_list());

        $list = stack_ast_container::make_from_teacher_source('x:[1,2,3]', 'type test', new stack_cas_security());
        $this->assertFalse($list->is_string());
        $this->assertFalse($list->is_matrix());
        $this->assertFalse($list->is_int());
        $this->assertFalse($list->is_float());
        $this->assertEquals(3, $list->is_list());
    }

    public function test_list_accessor() {
        $list = stack_ast_container::make_from_teacher_source('x:[1,2*x,3-4]', 'list access test', new stack_cas_security());
        $this->assertEquals(3, $list->is_list());

        $this->assertEquals('1', $list->get_list_element(0)->toString());
        $this->assertEquals('2*x', $list->get_list_element(1)->toString());
        $this->assertEquals('3-4', $list->get_list_element(2)->toString());
    }

}