<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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
require_once(__DIR__ . '/../stack/cas/casstring.class.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/042_no_functions_at_all.php');

/**
 * Unit tests for {@link stack_ast_filter_no_functions_at_all_042}.
 * @group qtype_stack
 */
class stack_parser_rule_042_test extends qtype_stack_testcase {

    public function test_no_functions_0() {
        $raw = '1+x^2/2!-x^3/3!';
        $cs = new stack_cas_casstring($raw);
        $filter = new stack_ast_filter_no_functions_at_all_042();
        $errs = array();
        $note = array();
        $security    = new stack_cas_security();

        $this->assertTrue($cs->get_valid());
        $this->assertEquals($cs->ast->toString(), $raw);
        $filter->filter($cs->ast, $errs, $note, $security);
        $this->assertEquals($errs, array());
        $this->assertEquals($note, array());
    }

    public function test_functions_0() {
        $raw = '1+sin(x)^2/2!-x^3/3!';
        $cs = new stack_cas_casstring($raw);
        $filter = new stack_ast_filter_no_functions_at_all_042();
        $errs = array();
        $note = array();
        $security    = new stack_cas_security();

        $this->assertTrue($cs->get_valid());
        $this->assertEquals($cs->ast->toString(), $raw);
        $filter->filter($cs->ast, $errs, $note, $security);
        $this->assertEquals($errs, array());
        $this->assertEquals($note, array(0 => 'functions'));
    }

    public function test_functions_1() {
        // User defined function.
        $raw = '1-2*f(x^2)';
        $cs = new stack_cas_casstring($raw);
        $filter = new stack_ast_filter_no_functions_at_all_042();
        $errs = array();
        $note = array();
        $security    = new stack_cas_security();

        $this->assertTrue($cs->get_valid());
        $this->assertEquals($cs->ast->toString(), $raw);
        $filter->filter($cs->ast, $errs, $note, $security);
        $this->assertEquals($errs, array());
        $this->assertEquals($note, array(0 => 'functions'));
    }

    public function test_functions_2() {
        // User defined function.
        $raw = '1-2*f(x^2-1)+sin(x)/7';
        $cs = new stack_cas_casstring($raw);
        $filter = new stack_ast_filter_no_functions_at_all_042();
        $errs = array();
        $note = array();
        $security    = new stack_cas_security();

        $this->assertTrue($cs->get_valid());
        $this->assertEquals($cs->ast->toString(), $raw);
        $filter->filter($cs->ast, $errs, $note, $security);
        $this->assertEquals($errs, array());
        $this->assertEquals($note, array(0 => 'functions'));
    }

    public function test_functions_3() {

        $predicate = function($node) {
            if ($node instanceof MP_FunctionCall) {
                return true;
            };
            return false;
        };

        // User defined function.
        $raw = '1-2*f(x^2-1)+sin(x)/7';
        $cs = new stack_cas_casstring($raw);

        $this->assertTrue($cs->get_valid());
        $this->assertEquals($cs->ast->toString(), $raw);
        $this->assertFalse($cs->ast->predicate_recurse($predicate));
    }
}
