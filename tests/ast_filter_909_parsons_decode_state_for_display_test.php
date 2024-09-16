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

        // Test proof questions without logging
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
        
        // Test proof questions with logging

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},5],[{\"used\":[[[\"d29ybGQ=\"]]],\"available\":[\"aGVsbG8=\"]},3],[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]"',
                      '"[[{\"used\":[[[\"hello\",\"world\"]]],\"available\":[]},5],[{\"used\":[[[\"world\"]]],\"available\":[\"hello\"]},3],[{\"used\":[[[]]],\"available\":[\"hello\",\"world\"]},0]]"',
                      [],
                      true, false);

        // Test grouping questions without logging

        $this->expect('"[[{\"used\":[[[\"bXk=\",\"bmFtZQ==\",\"aXM=\"]],[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"my\",\"name\",\"is\"]],[[\"hello\",\"world\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"bXk=\"]],[[\"aGVsbG8=\"]]],\"available\":[\"bmFtZQ==\",\"aXM=\",\"d29ybGQ=\"]},0]]"',
                      '"[[{\"used\":[[[\"my\"]],[[\"hello\"]]],\"available\":[\"name\",\"is\",\"world\"]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"bXk=\"]],[[\"bmFtZQ==\"]],[[\"aXM=\",\"d29ybGQ=\"]],[[\"aGVsbG8=\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"my\"]],[[\"name\"]],[[\"is\",\"world\"]],[[\"hello\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[]],[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\",\"bXk=\",\"bmFtZQ==\",\"aXM=\"]},0]]"',
                      '"[[{\"used\":[[[]],[[]]],\"available\":[\"hello\",\"world\",\"my\",\"name\",\"is\"]},0]]"',
                      [],
                      true, false);

        // Test grouping questions with logging

        $this->expect('"[[{\"used\":[[[\"bXk=\",\"bmFtZQ==\",\"aXM=\"]],[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},5],[{\"used\":[[[\"bXk=\",\"bmFtZQ==\"]],[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[\"aXM=\"]},0]]"',
                      '"[[{\"used\":[[[\"my\",\"name\",\"is\"]],[[\"hello\",\"world\"]]],\"available\":[]},5],[{\"used\":[[[\"my\",\"name\"]],[[\"hello\",\"world\"]]],\"available\":[\"is\"]},0]]"',
                      [],
                      true, false);

        // Test grid questions without logging

        $this->expect('"[[{\"used\":[[[\"bXk=\"],[\"bmFtZQ==\"],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[\"IQ==\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"my\"],[\"name\"],[\"is\"]],[[\"hello\"],[\"world\"],[\"!\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"bXk=\"],[],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[]]],\"available\":[\"bmFtZQ==\",\"IQ==\"]},0]]"',
                      '"[[{\"used\":[[[\"my\"],[],[\"is\"]],[[\"hello\"],[\"world\"],[]]],\"available\":[\"name\",\"!\"]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[],[],[]],[[],[],[]]],\"available\":[\"bXk=\",\"bmFtZQ==\",\"aXM=\",\"aGVsbG8=\",\"d29ybGQ=\",\"IQ==\"]},0]]"',
                      '"[[{\"used\":[[[],[],[]],[[],[],[]]],\"available\":[\"my\",\"name\",\"is\",\"hello\",\"world\",\"!\"]},0]]"',
                      [],
                      true, false);

        // Test grid questions with logging

        $this->expect('"[[{\"used\":[[[\"bXk=\"],[\"bmFtZQ==\"],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[\"IQ==\"]]],\"available\":[]},5],[{\"used\":[[[\"bXk=\"],[],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[\"IQ==\"]]],\"available\":[\"bmFtZQ==\"]},0]]"',
                      '"[[{\"used\":[[[\"my\"],[\"name\"],[\"is\"]],[[\"hello\"],[\"world\"],[\"!\"]]],\"available\":[]},5],[{\"used\":[[[\"my\"],[],[\"is\"]],[[\"hello\"],[\"world\"],[\"!\"]]],\"available\":[\"name\"]},0]]"',
                      [],
                      true, false);
    }

    public function test_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        // Test proof questions without logging
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
        
        // Test proof questions with logging

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},5],[{\"used\":[[[\"d29ybGQ=\"]]],\"available\":[\"aGVsbG8=\"]},3],[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]"',
                      '"[[{\"used\":[[[\"hello\",\"world\"]]],\"available\":[]},5],[{\"used\":[[[\"world\"]]],\"available\":[\"hello\"]},3],[{\"used\":[[[]]],\"available\":[\"hello\",\"world\"]},0]]"',
                      [],
                      true, false);

        // Test grouping questions without logging

        $this->expect('"[[{\"used\":[[[\"bXk=\",\"bmFtZQ==\",\"aXM=\"]],[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"my\",\"name\",\"is\"]],[[\"hello\",\"world\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"bXk=\"]],[[\"aGVsbG8=\"]]],\"available\":[\"bmFtZQ==\",\"aXM=\",\"d29ybGQ=\"]},0]]"',
                      '"[[{\"used\":[[[\"my\"]],[[\"hello\"]]],\"available\":[\"name\",\"is\",\"world\"]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"bXk=\"]],[[\"bmFtZQ==\"]],[[\"aXM=\",\"d29ybGQ=\"]],[[\"aGVsbG8=\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"my\"]],[[\"name\"]],[[\"is\",\"world\"]],[[\"hello\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[]],[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\",\"bXk=\",\"bmFtZQ==\",\"aXM=\"]},0]]"',
                      '"[[{\"used\":[[[]],[[]]],\"available\":[\"hello\",\"world\",\"my\",\"name\",\"is\"]},0]]"',
                      [],
                      true, false);

        // Test grouping questions with logging

        $this->expect('"[[{\"used\":[[[\"bXk=\",\"bmFtZQ==\",\"aXM=\"]],[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},5],[{\"used\":[[[\"bXk=\",\"bmFtZQ==\"]],[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[\"aXM=\"]},0]]"',
                      '"[[{\"used\":[[[\"my\",\"name\",\"is\"]],[[\"hello\",\"world\"]]],\"available\":[]},5],[{\"used\":[[[\"my\",\"name\"]],[[\"hello\",\"world\"]]],\"available\":[\"is\"]},0]]"',
                      [],
                      true, false);

        // Test grid questions without logging

        $this->expect('"[[{\"used\":[[[\"bXk=\"],[\"bmFtZQ==\"],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[\"IQ==\"]]],\"available\":[]},0]]"',
                      '"[[{\"used\":[[[\"my\"],[\"name\"],[\"is\"]],[[\"hello\"],[\"world\"],[\"!\"]]],\"available\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"bXk=\"],[],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[]]],\"available\":[\"bmFtZQ==\",\"IQ==\"]},0]]"',
                      '"[[{\"used\":[[[\"my\"],[],[\"is\"]],[[\"hello\"],[\"world\"],[]]],\"available\":[\"name\",\"!\"]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[],[],[]],[[],[],[]]],\"available\":[\"bXk=\",\"bmFtZQ==\",\"aXM=\",\"aGVsbG8=\",\"d29ybGQ=\",\"IQ==\"]},0]]"',
                      '"[[{\"used\":[[[],[],[]],[[],[],[]]],\"available\":[\"my\",\"name\",\"is\",\"hello\",\"world\",\"!\"]},0]]"',
                      [],
                      true, false);

        // Test grid questions with logging

        $this->expect('"[[{\"used\":[[[\"bXk=\"],[\"bmFtZQ==\"],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[\"IQ==\"]]],\"available\":[]},5],[{\"used\":[[[\"bXk=\"],[],[\"aXM=\"]],[[\"aGVsbG8=\"],[\"d29ybGQ=\"],[\"IQ==\"]]],\"available\":[\"bmFtZQ==\"]},0]]"',
                      '"[[{\"used\":[[[\"my\"],[\"name\"],[\"is\"]],[[\"hello\"],[\"world\"],[\"!\"]]],\"available\":[]},5],[{\"used\":[[[\"my\"],[],[\"is\"]],[[\"hello\"],[\"world\"],[\"!\"]]],\"available\":[\"name\"]},0]]"',
                      [],
                      true, false);
    }

    public function test_non_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        // Regular strings should not be affected
        $this->expect('"hello world"',
                      '"hello world"',
                      [],
                      true, false);
        
        $this->expect('"0"',
                      '"0"',
                      [],
                      true, false);

        // Strings containing lists of items which do not match Parson's states should not be affected
        $this->expect('"[\"a\",\"b\",\"c\"]"',
                      '"[\"a\",\"b\",\"c\"]"',
                      [],
                      true, false);        
        
        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0],\"b\",\"c\"]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0],\"b\",\"c\"]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"avail\":[]},0]]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"avail\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[0,0]]"',
                      '"[[0,0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":0},0]]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":0},0]]"',
                      [],
                      true, false);
        
        $this->expect('"[[{\"used\":0,\"available\":[]},0]]"',
                      '"[[{\"used\":0,\"available\":[]},0]]"',
                      [],
                      true, false);


    }

    public function test_non_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('909_parsons_decode_state_for_display');

        // Regular strings should not be affected
        $this->expect('"hello world"',
                      '"hello world"',
                      [],
                      true, false);

        $this->expect('"0"',
                      '"0"',
                      [],
                      true, false);

        // Strings containing lists of items which do not match Parson's states should not be affected
        $this->expect('"[\"a\",\"b\",\"c\"]"',
                      '"[\"a\",\"b\",\"c\"]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0],\"b\",\"c\"]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0],\"b\",\"c\"]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":[]},0]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"avail\":[]},0]]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"avail\":[]},0]]"',
                      [],
                      true, false);

        $this->expect('"[[0,0]]"',
                      '"[[0,0]]"',
                      [],
                      true, false);

        $this->expect('"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":0},0]]"',
                      '"[[{\"used\":[[[\"aGVsbG8=\",\"d29ybGQ=\"]]],\"available\":0},0]]"',
                      [],
                      true, false);
        
        $this->expect('"[[{\"used\":0,\"available\":[]},0]]"',
                      '"[[{\"used\":0,\"available\":[]},0]]"',
                      [],
                      true, false);
    }
}
