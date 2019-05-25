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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

// Unit tests for {@link stack_cas_keyval} involving exceptions.

/**
 * @group qtype_stack
 */
class stack_cas_keyval_exception_test extends basic_testcase {
    /**
     * @expectedException stack_exception
     */
    public function test_exception_1() {
        $at1 = new stack_cas_keyval(array(), false, false);
    }

    /**
     * @expectedException stack_exception
     */
    public function test_exception_2() {
        $at1 = new stack_cas_keyval(1, false, false);
    }

    /**
     * @expectedException stack_exception
     */
    public function test_exception_3() {
        $at1 = new stack_cas_keyval('x=1', false, false);
    }

    /**
     * @expectedException stack_exception
     */
    public function test_exception_4() {
        $at1 = new stack_cas_keyval('x=1', null, false);
    }

    /**
     * @expectedException stack_exception
     */
    public function test_exception_5() {
        $at1 = new stack_cas_keyval('x=1', 'z', false);
    }

    /**
     * @expectedException stack_exception
     */
    public function test_exception_7() {
        $at1 = new stack_cas_keyval('x=1', 't', false);
    }
}
