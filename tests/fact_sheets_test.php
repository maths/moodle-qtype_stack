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

// Unit tests for {@link stack_fact_sheets}.

/**
 * @group qtype_stack
 */
class stack_fact_sheets_test extends qtype_stack_testcase {

    public function test_basic_castext_instantiation() {
        $this->assertEquals(array(), stack_fact_sheets::get_unrecognised_tags('Hello world'));
    }

    public function test_trap_bad_fact_sheet_names() {
        $this->assertEquals(array(0 => 'bad_hint', 1 => 'badder_hint'),
                stack_fact_sheets::get_unrecognised_tags(
                        "This is some CAStext with a [[facts:bad_hint]] " .
                        "and yet another [[facts:badder_hint]]"));
    }

    public function test_legacy_convert() {
        $this->assertEquals("An [[facts:old_hint]] and [[facts:older_hint]].",
                stack_fact_sheets::convert_legacy_tags("An <hint>old_hint</hint> and <hint>older_hint</hint>."));
    }
}
