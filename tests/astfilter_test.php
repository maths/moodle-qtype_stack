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

// Unit tests for verious AST filters.
//
// @copyright  2019 Aalto University
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../stack/cas/parsingrules/002_log_candy.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/040_common_function_name_multiplier.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/043_no_calling_function_returns.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/050_split_floats.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/051_no_floats.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../stack/maximaparser/corrective_parser.php');

require_once(__DIR__ . '/fixtures/test_base.php');

/**
 * @group qtype_stack
 */
class stack_astfilter_test extends qtype_stack_testcase {

    public function test_002_log_candy() {
        $teststring  = 'log_5(x)+log_x+y(x)+log_x^y(y)-log_(x-y)(z);';
        $result      = 'lg(x,5)+lg(x,x+y)+lg(y,x^y)-lg(z,(x-y));' . "\n";
        $ast         = maxima_parser_utils::parse($teststring);
        $answernotes = array();
        $errors      = array();

        $astfilter   = new stack_ast_log_candy_002();

        // This test might allow functions that are allowed, but not yet.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertEquals(0, count($errors));
        $this->assertContains('logsubs', $answernotes);
        $this->assertEquals($result, $filtered->toString());
    }

    public function test_002_log_candy_corrective1() {
        $teststring  = 'log_5-1(x)+log_x+3y(x)+log_x^3(y)-log_(x-y)(z);';
        $result      = 'lg(x,5-1)+lg(x,x+3*y)+lg(y,x^3)-lg(z,(x-y));' . "\n";
        $answernotes = array();
        $errors      = array();
        $ast         = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array());

        $astfilter   = new stack_ast_log_candy_002();

        // This test might allow functions that are allowed, but not yet.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertContains('logsubs', $answernotes);
        $this->assertEquals($result, $filtered->toString());
        $this->assertEquals(0, count($errors));
    }

    public function test_002_log_candy_corrective2() {
        $teststring  = 'log_10(a+x^2)+log_a(b)*log_%e(%e);';
        $result      = 'lg(a+x^2,10)+lg(b,a)*lg(%e,%e);' . "\n";
        $answernotes = array();
        $errors      = array();
        $ast         = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array());

        $astfilter   = new stack_ast_log_candy_002();

        // This test might allow functions that are allowed, but not yet.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertContains('logsubs', $answernotes);
        $this->assertEquals($result, $filtered->toString());
        $this->assertEquals(0, count($errors));
    }

    public function test_002_log_candy_corrective3() {
        $teststring  = 'log_x:log_x(a);';
        $result      = 'log_x:lg(a,x);' . "\n";
        $answernotes = array();
        $errors      = array();
        $ast         = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array());

        $astfilter   = new stack_ast_log_candy_002();

        // This test might allow functions that are allowed, but not yet.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertContains('logsubs', $answernotes);
        $this->assertEquals($result, $filtered->toString());
        $this->assertEquals(0, count($errors));
    }

    public function test_040_function_prefix() {
        $teststring  = 'foosin(x)+ratan(ylg(y))+sinsin;';
        $result      = 'foo*sin(x)+r*atan(y*lg(y))+sinsin;' . "\n";
        $ast         = maxima_parser_utils::parse($teststring);
        $answernotes = array();
        $errors      = array();

        $astfilter   = new stack_ast_common_function_name_multiplier_040();

        // This test might allow functions that are allowed, but not yet.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertEquals(0, count($errors));
        $this->assertContains('missing_stars', $answernotes);
        $this->assertEquals($result, $filtered->toString());
    }

    public function test_043_no_calling_function_returns() {
        $teststring  = 'foo(x)(y);';
        $result      = 'foo(x)*(y);' . "\n";
        $ast         = maxima_parser_utils::parse($teststring);
        $answernotes = array();
        $errors      = array();

        $astfilter   = new stack_ast_filter_no_calling_function_returns_43();

        // This test does not require knowledge of security but the interface does.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertEquals(array(0 => 'You seem to be missing * characters. ' .
                'Perhaps you meant to type <span class="stacksyntaxexample">foo(x)*(y)</span>.'), $errors);
        $this->assertContains('calling_function_returns', $answernotes);
        $this->assertEquals($result, $filtered->toString());
    }

    public function test_043_no_calling_function_returns_ok() {
        $teststring  = 'foo(x)*(y);';
        $result      = $teststring . "\n";
        $ast         = maxima_parser_utils::parse($teststring);
        $answernotes = array();
        $errors      = array();

        $astfilter   = new stack_ast_filter_no_calling_function_returns_43();

        // This test does not require knowledge of security but the interface does.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertEquals(array(), $errors);
        $this->assertEquals(array(), $answernotes);
        $this->assertEquals($result, $filtered->toString());
    }

    public function test_050_float_split() {
        $teststring  = '[xsin(x)*1.0*2.0e-1,2e2,sqrt(2E-1),.1e-90];';
        $result      = '[xsin(x)*1.0*2.0*e-1,2*e*2,sqrt(2*E-1),.1*e-90];' . "\n";
        $ast         = maxima_parser_utils::parse($teststring);
        $answernotes = array();
        $errors      = array();

        $astfilter   = new stack_ast_filter_split_floats_050();

        // This test does not require knowledge of security but the interface does.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertEquals(0, count($errors));
        $this->assertContains('missing_stars', $answernotes);
        $this->assertEquals($result, $filtered->toString());
    }

    public function test_051_no_float_split() {
        $teststring  = '1+0.5*x;';
        $result      = $teststring . "\n";
        $ast         = maxima_parser_utils::parse($teststring);
        $answernotes = array();
        $errors      = array();

        $astfilter   = new stack_ast_filter_no_floats_051();

        // This test does not require knowledge of security but the interface does.
        $security    = new stack_cas_security();
        $filtered    = $astfilter->filter($ast, $errors, $answernotes, $security);

        $this->assertEquals(0, count($errors));
        $this->assertContains('Illegal_floats', $answernotes);
        $this->assertEquals($result, $filtered->toString());
    }

}