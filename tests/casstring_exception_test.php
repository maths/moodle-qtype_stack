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
 * Unit tests for stack_cas_casstring.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/cas/casstring.class.php');


/**
 * Unit tests for {@link stack_cas_casstring}.
 * @group qtype_stack
 */
class stack_cas_casstring_exception_test extends basic_testcase {

    public function test_exception_1() {
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_casstring(array());
    }

    public function test_exception_2() {
        $at1 = new stack_cas_casstring("x=1");
        $this->setExpectedException('stack_exception');
        $at1->get_valid(false, false, false);
    }

    public function test_exception_3() {
        $at1 = new stack_cas_casstring("x=1");
        $this->setExpectedException('stack_exception');
        $at1->get_valid('z', false, 0);
    }

    public function test_exception_4() {
        $at1 = new stack_cas_casstring("x=1");
        $this->setExpectedException('stack_exception');
        $at1->get_valid('t', 'a', 0);
    }

    public function test_exception_5() {
        $at1 = new stack_cas_casstring("x=1");
        $this->setExpectedException('stack_exception');
        $at1->get_valid('t', true, 'a');
    }
}
