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
require_once(__DIR__ . '/../stack/cas/castext.class.php');

// Unit tests for {@link stack_cas_text}.

/**
 * @group qtype_stack
 */
class stack_cas_text_exception_test extends basic_testcase {

    /**
     * @expectedException TypeError
     */
    public function test_exception_1() {
        $session = new stack_cas_session2(null);
        $at1 = new stack_cas_text(array(), null, null);
        $at1->get_valid();
    }

    /**
     * @expectedException TypeError
     */
    public function test_exception_2() {
        $session = new stack_cas_session2(null);
        $at1 = new stack_cas_text("Hello world", array(1), null);
        $at1->get_valid();
    }

    /**
     * @expectedException TypeError
     */
    public function test_exception_3() {
        $session = new stack_cas_session2(null);
        $at1 = new stack_cas_text("Hello world", $session, "abc");
        $at1->get_valid();
    }
}
