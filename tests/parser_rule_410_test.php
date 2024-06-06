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
use stack_ast_filter_410_single_char_vars;
use stack_cas_security;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/410_single_char_vars.filter.php');

/**
 * Unit tests for {@link stack_ast_filter_410_single_char_vars}.
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 * @covers \ast_filter_410_single_char_vars_auto_generated_test
 */
class parser_rule_410_test extends qtype_stack_testcase {

    public function test_nothing_to_do() {
        $teststring = '2*a*b;';
        $result     = $teststring . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_410_single_char_vars();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, []);
        $this->assertEquals($note, []);
        $this->assertEquals($ast->toString(), $result);
    }

    public function test_simple() {
        $teststring = '2*ab;';
        $result     = '2*a*b;' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_410_single_char_vars();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, []);
        $this->assertEquals($note, ['missing_stars']);
        $this->assertEquals($ast->toString(), $result);
    }

    public function test_greek() {
        $teststring = 'nalpha+sin(pin);';
        $result     = 'n*alpha+sin(pi*n);' . "\n";
        $ast = maxima_parser_utils::parse($teststring);
        $filter = new stack_ast_filter_410_single_char_vars();
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $filter->filter($ast, $errs, $note, $security);
        $this->assertEquals($errs, []);
        $this->assertEquals($note, ['missing_stars']);
        $this->assertEquals($ast->toString(), $result);
    }
}
