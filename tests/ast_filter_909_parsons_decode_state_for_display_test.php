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

namespace qtype_stack;

use qtype_stack_ast_testcase;
use stack_cas_security;
use stack_parsing_rule_factory;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../tests/fixtures/ast_filter_test_base.php');

// Auto-generated unit tests for AST-filter DO NOT EDIT!
/**
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 * @covers \ast_filter_909_parsons_state_for_display
 */

class ast_filter_909_parsons_decode_state_for_display_test extends qtype_stack_ast_testcase {

    public function test_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"hello\",\"world\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);
    }

    public function test_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"hello\",\"world\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\"]]],\"available\":[\"d29ybGQ=\"]},0]]"',
                      '"[[{\"used\":[[[\"hello\"]]],\"available\":[\"world\"]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]"',
                      '"[[{\"used\":[[[]]],\"available\":[\"hello\",\"world\"]},0]]"',
                      [],
                      true, false);
    }

    public function test_non_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        $this->expect('"hello world"',
                      '"hello world"',
                      [],
                      true, false);
    }

    public function test_non_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        $this->expect('"hello world"',
                      '"hello world"',
                      [],
                      true, false);
    }
}
