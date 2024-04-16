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
use stack_ast_filter_541_no_unknown_functions;
use stack_cas_security;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/541_no_unknown_functions.filter.php');


/**
 * Unit tests for {@link stack_ast_filter_541_no_unknown_functions}.
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 * @covers \ast_filter_541_no_unknown_functions_auto_generated_test
 */
class parser_rule_541_test extends qtype_stack_testcase {

    public function test_no_functions_0() {
        $teststring = '1+x^2/2!-x^3/3!;';
        $result     = $teststring . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_541_no_unknown_functions();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, []);
        $this->assertEquals($note, []);
        $this->assertEquals($ast->toString(), $result);
    }

    public function test_functions_0() {
        $teststring = '1+sin(x)^2/2!-x^3/3!;';
        $result     = $teststring . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_541_no_unknown_functions();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, []);
        $this->assertEquals($note, []);
        $this->assertEquals($ast->toString(), $result);
    }

    public function test_functions_1() {
        // User defined function.
        $teststring = '1+2*f(x^2);';
        $result     = '1+2*f(x^2);' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_541_no_unknown_functions();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'Unknown function: <span class="stacksyntaxexample">f</span> ' .
                'in the term <span class="stacksyntaxexample">f(x^2)</span>.']);
        $this->assertEquals($note, [0 => 'unknownFunction']);
        $this->assertEquals($ast->toString(), $result);
    }

    public function test_functions_2() {
        // User defined function, but sin is known.
        $teststring = '1-2*foo(x^2-1)+sin(x)/7;';
        $result     = '1-2*foo(x^2-1)+sin(x)/7;' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_541_no_unknown_functions();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'Unknown function: <span class="stacksyntaxexample">foo</span> in the term ' .
                '<span class="stacksyntaxexample">foo(x^2-1)</span>.']);
        $this->assertEquals($note, [0 => 'unknownFunction']);
        $this->assertEquals($ast->toString(), $result);
    }

    public function test_functions_3() {
        // Nested user defined functions.
        $teststring = '1+x(t(3)+1);';
        $result     = '1+x(t(3)+1);' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_541_no_unknown_functions();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, [0 => 'Unknown function: <span class="stacksyntaxexample">x</span> in the term ' .
                    '<span class="stacksyntaxexample">x(t(3)+1)</span>.',
                1 => 'Unknown function: <span class="stacksyntaxexample">t</span> in the term ' .
                    '<span class="stacksyntaxexample">t(3)</span>.']);
        $this->assertEquals($note, [0 => 'unknownFunction']);
        $this->assertEquals($ast->toString(), $result);
    }
}
