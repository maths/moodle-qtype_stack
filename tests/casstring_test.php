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

/**
 * Unit tests for stack_cas_casstring.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/cas/casstring.class.php');


/**
 * Unit tests for {@link stack_cas_casstring}.
 * @group qtype_stack
 */
class stack_cas_casstring_test extends basic_testcase {

    public function get_valid($s, $st, $te) {

        $at1 = new stack_cas_casstring($s);
        $this->assertEquals($st, $at1->get_valid('s'));

        $at2 = new stack_cas_casstring($s);
        $this->assertEquals($te, $at2->get_valid('t'));
    }

    public function test_get_valid() {
        $cases = array(
            array('', false, false),
            array('1', true, true),
            array('a b', false, true),
            array('%pi', true, true), // Only %pi %e, %i, %gamma, %phi.
            array('1+%e', true, true),
            array('e^%i*%pi', true, true),
            array('%gamma', true, true),
            array('%phi', true, true),
            array('%o1', false, false),
            // Literal unicode character, instead of name.
            array('π', false, false),
            // Non-matching brackets.
            array('(x+1', false, false),
            array('(y^2+1))', false, false),
            array('[sin(x)+1)', false, false),
            array('([y^2+1)]', false, false),
            // Function which does not appears on the teacher's list.
            array('setelmx(2,1,1,C)', false, true),
            array('2*reallytotalnonsensefunction(x)', false, true),
            array('system(rm *)', false, false), // This should never occur.
            array('$', false, false),
            array('@', false, false),
            // Inequalities.
            array('x>=1', true, true),
            array('x=>1', false, false),
            // Unencapsulated commas.
            array('a,b', false, false),
        );

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1], $case[2]);
        }
    }

    public function test_validation_error() {
        $casstring = new stack_cas_casstring('π');
        $casstring->get_valid('s');
        $this->assertEquals(stack_string('stackCas_forbiddenChar', array('char' => 'π')),
                $casstring->get_errors());
        $this->assertEquals('forbiddenChar', $casstring->get_answernote());
    }

    public function test_validation_error_global_forbid() {
        $casstring = new stack_cas_casstring('system(rm)');
        $this->assertFalse($casstring->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">system</span> is forbidden.',
                $casstring->get_errors());
        $this->assertEquals('forbiddenWord', $casstring->get_answernote());
    }

    public function test_spurious_operators() {
        $casstring = new stack_cas_casstring('2/*x');
        $casstring->get_valid('s');
        $this->assertEquals('Unknown operator: <span class="stacksyntaxexample">/*</span>.',
                $casstring->get_errors());
        $this->assertEquals('spuriousop', $casstring->get_answernote());
    }

    public function test_get_valid_inequalities() {
        $cases = array(
                array('x>1 and x<4', true, true),
                array('not (x>1)', true, true),
                array('x<=2 or not (x>1)', true, true),
                array('x<1 or (x>1 and t<sin(x))', true, true),
                array('[x<1, x>3]', true, true),
                array('pg:if x<x0 then f0 else if x<x1 then 1000 else f1', false, true),
                array('1<x<7', false, false),
                array('1<a<=x^2', false, false),
                array('{1<x<y, c>0}', false, false),
        );

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1], $case[2]);
        }
    }

    public function get_key($s, $key, $val) {
        $at1 = new stack_cas_casstring($s);
        $this->assertEquals($key, $at1->get_key());
        $this->assertEquals($s, $at1->get_raw_casstring()); // Note the difference between the two!
        $this->assertEquals($val, $at1->get_casstring());
    }

    public function test_get_key() {
        $cases = array(
            array('x=1', '', 'x=1'),
            array('a:1', 'a', '1'),
            array('a1:1', 'a1', '1'),
            array('f(x):=x^2', '', 'f(x):=x^2'),
            array('a:b:1', 'a', 'b:1'),
            array('ta:x^3=-3', 'ta', 'x^3=-3')
        );

        foreach ($cases as $case) {
            $this->get_key($case[0], $case[1], $case[2]);
        }
    }

    public function test_global_forbidden_words() {

        $s = 'system(rm *)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">system</span> is forbidden.',
                $at1->get_errors());

        $at2 = new stack_cas_casstring($s);
        $this->assertFalse($at2->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">system</span> is forbidden.',
                $at2->get_errors());
    }

    public function test_global_forbidden_words_case() {

        $s = 'System(rm *)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">system</span> is forbidden.',
                $at1->get_errors());

        $at2 = new stack_cas_casstring($s);
        $this->assertFalse($at2->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">system</span> is forbidden.',
                $at2->get_errors());
    }

    public function test_teacher_only_words() {

        $s = 'setelmx(2,1,1,C)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('Unknown function: <span class="stacksyntaxexample">setelmx</span>.',
                $at1->get_errors());

        $at2 = new stack_cas_casstring($s);
        $this->assertTrue($at2->get_valid('t'));
        $this->assertEquals('', $at2->get_errors());
    }

    public function test_allow_words() {
        $s = '2*dumvariable+3';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0, 'dumvariable'));
    }

    public function test_allow_words_fail() {
        $s = 'sin(2*dumvariable+3)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0, 'dvariable'));
    }

    public function test_allow_words_teacher() {
        $s = 'sin(2*dumvariable+3)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t', true, 0, 'dvariable'));
    }

    public function test_check_external_forbidden_words() {
        // Remember, this function returns true if the literal is found.
        $cases = array(
            array('sin(ta)', array('ta'), true),
            array('sin(ta)', array('ta', 'a', 'b'), true),
            array('sin(ta)', array('sa'), false),
            array('sin(a)', array('a'), false), // This ignores single letters.
            array('diff(x^2,x)', array('[[BASIC-CALCULUS]]'), true),
        );

        foreach ($cases as $case) {
            $cs = new stack_cas_casstring($case[0]);
            $this->assertEquals($case[2], $cs->check_external_forbidden_words($case[1]));
        }
    }

    public function test_check_external_forbidden_words_literal() {
        $cases = array(
            array('3+5', '+', true),
            array('sin(a)', 'a', true), // It includes single letters.
            array('sin(a)', 'i', true), // Since it is a string match, this can be inside a name.
            array('sin(a)', 'b', false),
            array('sin(a)', 'b,\,,c', false), // Test escaped commas.
            array('[x,y,z]', 'b,\,,c', true),
            array('diff(x^2,x)', '[[BASIC-CALCULUS]]', true), // From lists.
            array('solve((x-6)^4,x)', '[[BASIC-ALGEBRA]]', true), // From lists.
        );

        foreach ($cases as $case) {
            $cs = new stack_cas_casstring($case[0]);
            $this->assertEquals($case[2], $cs->check_external_forbidden_words_literal($case[1]));
        }
    }

    public function test_check_external_allow_words() {
        $cases = array(
            array('popup(ta)', 'popup', true),
            array('popup(ta)', 'silly, n, popup, flop', true),
            array('plopup(ta)', 'silly, n, popup, flop', false),
            array('plopup(ta)', 'popup', false)
        );

        foreach ($cases as $case) {
            $cs = new stack_cas_casstring($case[0]);
            $this->assertEquals($case[2], $cs->get_valid('s', true, 0, $case[1]));
        }
    }

    public function test_html_1() {
        $s = '</span>n';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('You appear to have some HTML elements in your expression. <pre></span>n</pre>',
                $at1->get_errors());
    }

    public function test_html_2() {
        $s = '<span>n';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('You appear to have some HTML elements in your expression. <pre><span>n</pre>',
                $at1->get_errors());
    }

    public function test_strings_1() {
        $s = 'a:"hello"';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t'));
    }

    public function test_strings_2() {
        $s = 'a:"hello';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('You are missing a quotation sign <code>"</code>. ',
                $at1->get_errors());
    }

    public function test_strings_3() {
        $s = 'a:["2x)",3*x]';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t'));
    }

    public function test_strings_4() {
        $s = 'a:["system(\'rm *\')",3*x]';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">system</span> is forbidden.',
                $at1->get_errors());
    }

    public function test_scientific_1() {
        $s = 'a:3e2';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true));
    }

    public function test_scientific_2() {
        $s = 'a:3e2';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', false, 0));
        $this->assertEquals('3e2', $at1->get_casstring());
        $this->assertEquals('missing_stars', $at1->get_answernote());
    }

    public function test_scientific_3() {
        $s = 'a:3e2';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', false, 1));
        $this->assertEquals('3*e*2', $at1->get_casstring());
        $this->assertEquals('missing_stars', $at1->get_answernote());
    }

    public function test_trig_1() {
        $s = 'a:sin[2*x]';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('trigparens', $at1->get_answernote());
    }

    public function test_trig_2() {
        $s = 'a:cot*2*x';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('trigop', $at1->get_answernote());
    }

    public function test_trig_3() {
        $s = 'a:tan^-1(x)-1';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('trigexp | missing_stars', $at1->get_answernote());
    }

    public function test_trig_4() {
        $s = 'a:Sim(x)-1';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('unknownFunction', $at1->get_answernote());
    }

    public function test_trig_5() {
        $s = 'a:Sin(x)-1';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('unknownFunctionCase', $at1->get_answernote());
    }

    public function test_trig_6() {
        $s = 'a:Sin(x)-1';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t'));
    }

    public function test_in_1() {
        $s = 'a:1+In(x)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('stackCas_badLogIn', $at1->get_answernote());
    }

    public function test_in_2() {
        $s = 'a:1+In(x)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t'));
    }

    public function test_unencapsulated_commas_1() {
        $s = 'a,b';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('unencpsulated_comma', $at1->get_answernote());
    }

    public function test_implied_complex_mult1() {
        $s = 'sa:-(1/512)+i(sqrt(3)/512)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', false, 1));
        $this->assertEquals('-(1/512)+i*(sqrt(3)/512)',
                $at1->get_casstring());
    }

    public function test_implied_complex_mult2() {
        $s = 'sa:-(1/512)+i(sqrt(3)/512)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
    }

    public function test_implied_complex_mult3() {
        // This function name ends in an "i", so we need to check *s are not being inserted too many times here.
        $s = 'sa:cdf_bernoulli(x,p)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', false, 1));
        $this->assertEquals('cdf_bernoulli(x,p)',
                $at1->get_casstring());
    }

    public function test_conditionals_1() {
        $s = 'x#a';
        $c1 = new stack_cas_casstring($s);
        $s = '1/(x-a)';
        $at1 = new stack_cas_casstring($s, array($c1));
        $this->assertTrue($at1->get_valid('s'));
        $this->assertEquals(array($c1), $at1->get_conditions());
    }

    public function test_units_1() {
        $s = 'sa:3.14*mol';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
    }

    public function test_units_2() {
        $s = 'sa:3.14*moles';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unitssynonym | unknownFunction', $at1->get_answernote());
    }

    public function test_units_3() {
        $s = 'sa:3.14*Moles';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unitssynonym | unknownFunction', $at1->get_answernote());
    }

    public function test_units_allow_moles() {
        $s = 'sa:3.14*moles';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0, 'moles'));
    }

    public function test_units_4() {
        $s = '52.3*km';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
    }

    public function test_units_5() {
        $s = 'sa:52.3*MHz';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
    }

    public function test_units_6() {
        $s = 'sa:52.3*Mhz';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unknownUnitsCase', $at1->get_answernote());
        $err = 'Input of units is case sensitive:  <span class="stacksyntaxexample">Mhz</span> is an unknown unit. '
                   . 'Did you mean one from the following list <span class="stacksyntaxexample">[mHz, MHz]</span>?';
        $this->assertEquals($err, $at1->get_errors());
    }

    public function test_units_amu() {
        $s = '520*amu';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
    }

    public function test_units_mamu() {
        $s = '520*mamu';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
    }

    public function test_units_mmhg() {
        $s = '7*mmhg';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unknownUnitsCase', $at1->get_answernote());
        $err = 'Input of units is case sensitive:  <span class="stacksyntaxexample">mmhg</span> is an unknown unit. '
                   . 'Did you mean one from the following list <span class="stacksyntaxexample">[mmHg]</span>?';
        $this->assertEquals($err, $at1->get_errors());
    }

}
