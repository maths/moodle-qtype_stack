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

namespace qtype_stack;

use maxima_parser_utils;
use qtype_stack_testcase;
use stack_ast_filter_542_no_functions_at_all;
use stack_cas_security;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/542_no_functions_at_all.filter.php');

// TODO: update these tests to match the reality of 042 => 442 & 542.

/**
 * Unit tests for {@link stack_ast_filter_no_functions_at_all_042}.
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 * @covers \ast_filter_542_no_functions_at_all_auto_generated_test
 */
class parser_rule_542_test extends qtype_stack_testcase {

    public function test_no_functions_0() {
        $teststring = '1+x^2/2!-x^3/3!;';
        $result = $teststring . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_542_no_functions_at_all();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, []);
        $this->assertEquals($note, []);
        $this->assertEquals($result, $ast->toString());
    }

    public function test_functions_0() {
        $teststring = '1+sin(x)^2/2!-x^3/3!;';
        $result     = '1+sin(x)^2/2!-x^3/3!;' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_542_no_functions_at_all();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'The use of the function <span class="stacksyntaxexample">sin</span> in the term ' .
                '<span class="stacksyntaxexample">sin(x)</span> is not permitted in this context.']);
        $this->assertEquals($note, [0 => 'noFunction']);
        $this->assertEquals($result, $ast->toString());
    }

    public function test_functions_1() {
        // User defined function.
        $teststring = '1-2*f(x^2);';
        $result     = '1-2*f(x^2);' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_542_no_functions_at_all();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'The use of the function <span class="stacksyntaxexample">f</span> in the term ' .
                '<span class="stacksyntaxexample">f(x^2)</span> is not permitted in this context.']);
        $this->assertEquals($note, [0 => 'noFunction']);
        $this->assertEquals($result, $ast->toString());
    }

    public function test_functions_2() {
        // User defined function.
        $teststring = '1-2*f(x^2-1)+sin(x)/7;';
        $result     = '1-2*f(x^2-1)+sin(x)/7;' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_542_no_functions_at_all();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'The use of the function <span class="stacksyntaxexample">f</span> in the term ' .
                    '<span class="stacksyntaxexample">f(x^2-1)</span> is not permitted in this context.',
                1 => 'The use of the function <span class="stacksyntaxexample">sin</span> in the term ' .
                    '<span class="stacksyntaxexample">sin(x)</span> is not permitted in this context.']);
        $this->assertEquals($note, [0 => 'noFunction']);
        $this->assertEquals($result, $ast->toString());
    }

    public function test_functions_3() {
        // Nested user defined function.
        $teststring = '1+x(t(3)+1);';
        $result     = '1+x(t(3)+1);' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_542_no_functions_at_all();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'The use of the function <span class="stacksyntaxexample">x</span> in the term ' .
                    '<span class="stacksyntaxexample">x(t(3)+1)</span> is not permitted in this context.',
                1 => 'The use of the function <span class="stacksyntaxexample">t</span> in the term ' .
                    '<span class="stacksyntaxexample">t(3)</span> is not permitted in this context.']);
        $this->assertEquals($note, [0 => 'noFunction']);
        $this->assertEquals($result, $ast->toString());
    }
}
