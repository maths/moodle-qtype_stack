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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext.class.php');


/**
 * Unit tests for {@link stack_hints}.
 * @group qtype_stack
 */
class stack_hints_test extends qtype_stack_testcase {

    public function test_basic_castext_instantiation() {
        $hint = new stack_hints("Hello world");
        $this->assertTrue($hint->validate());
    }

    public function test_trap_bad_hint_names() {
        $hint = new stack_hints("This is some CAStext with a [[hint:bad_hint]] and yet another [[hint:badder_hint]]");
        $this->assertEquals(array(0 => 'bad_hint', 1 => 'badder_hint'), 
                $hint->validate());
    }

    public function test_legacy_convert() {
        $hint = new stack_hints("An <hint>old_hint</hint> and <hint>older_hint</hint>.");
        $this->assertEquals("An [[hint:old_hint]] and [[hint:older_hint]].", 
                $hint->legacy_convert());
    }
}
