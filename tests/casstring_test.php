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
require_once(__DIR__ . '/../stack/cas/casstring.class.php');

// Unit tests for {@link stack_cas_casstring}.
// @copyright  2012 The University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
// @group qtype_stack.

/**
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
            array('"system(rm *)"', true, true), // There is nothing wrong with this.
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

    public function test_spurious_operators() {
        $casstring = new stack_cas_casstring('2/*x');
        $casstring->get_valid('s');
        $this->assertEquals('Unknown operator: <span class="stacksyntaxexample">/*</span>.',
                $casstring->get_errors());
        $this->assertEquals('spuriousop', $casstring->get_answernote());
    }

    public function test_spurious_operators_2() {
        $casstring = new stack_cas_casstring('x==2*x');
        $casstring->get_valid('s');
        $this->assertEquals('Unknown operator: <span class="stacksyntaxexample">==</span>.',
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
                // Change for issue #324 now stops checking chained inequalities for teachers.
                array('1<x<7', false, true),
                array('1<a<=x^2', false, true),
                array('{1<x<y, c>0}', false, true),
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

    public function test_strings_1() {
        $s = 'a:"hello"';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t'));
    }

    public function test_strings_2() {
        $s = 'a:["2x)",3*x]';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t'));
    }

    /* TODO: we need a full parser to check for mismatched string delimiters.
     * Below are some test cases which need a parser.
     *
     *  $s = 'a:"hello';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertEquals('You are missing a quotation sign <code>"</code>. ', $at1->get_errors());
     *  $s = 'a:""hello""';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:"hello"   "hello"';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:"hello"5';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:"hello"*5';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:"hello"  +  "hello"';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:(5)*"hello"';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:(5)/"hello"';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:5-"hello"';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:[{"hello"},"hello",["hello"]]';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertTrue($at1->get_valid('t'));
     *  $s = 'a:cos(pi)"hello"';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     *  $s = 'a:[{"hello"}"hello"["hello"]]';
     *  $at1 = new stack_cas_casstring($s);
     *  $this->assertFalse($at1->get_valid('t'));
     */

    public function test_system_execution() {
        // First the obvious one, just eval that string.
        $s = 'a:eval_string("system(\\"rm /tmp/test\\")")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">eval_string</span> is forbidden.',
                $at1->get_errors());

        $s = 'a:eval_string("system(rm /tmp/test)")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">eval_string</span> is forbidden.',
                $at1->get_errors());

        // The second requires a bit more, parse but do the eval later.
        $s = 'a:ev(parse_string("system(\\"rm /tmp/test\\")"),eval)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">parse_string</span> is forbidden.',
                $at1->get_errors());

        // Then things get tricky, one needs to write the thing out and eval when reading in.
        // Luckilly, appendfile, save, writefile, and stringout commands would require manual eval.
        // But lets test them anyway.
        $s = 'a:appendfile("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">appendfile</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:writefile("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">writefile</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:save("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">save</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:stringout("/tmp/test", "system(\\"rm /tmp/test\\");")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">stringout</span> is forbidden.',
                $at1->get_errors());

        // The corresponding read commands load, loadfile, batch, and batchload are all bad.
        $s = 'a:load("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">load</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:loadfile("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">loadfile</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:batch("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">batch</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:batchload("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">batchload</span> is forbidden.',
                $at1->get_errors());

        // Naturally, lower level functions can allow you to actually edit or generate files to execute.
        // The opena, openw, and openr and their binary versions could do even more harm.
        // Even scarier is naturally, the possibility to edit files...
        $s = 'a:opena("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">opena</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:openw("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">openw</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:openr("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">openr</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:opena_binary("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">opena_binary</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:openw_binary("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">openw_binary</span> is forbidden.',
                $at1->get_errors());
        $s = 'a:openr_binary("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">openr_binary</span> is forbidden.',
                $at1->get_errors());

        // And lets not forget being able to output file contents.
        $s = 'a:printfile("/tmp/test")';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">printfile</span> is forbidden.',
                $at1->get_errors());

        // And then there is the possibility of using lisp level functions.
        $s = ':lisp (with-open-file (stream "/tmp/test" :direction :output) (format stream "system(\\"rm /tmp/test\\");"))';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">lisp</span> is forbidden.',
                $at1->get_errors());
        // That last goes wrong due to "strings" not being usable in the lisp way.
        // Assuming those are in variables we can try this.
        $s = ':lisp (with-open-file (stream a :direction :output) (format stream b))';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('t'));
        $this->assertEquals('The expression <span class="stacksyntaxexample">lisp</span> is forbidden.',
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

    public function test_greek_1() {
        $s = 'a:Delta-1';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s'));
    }

    public function test_greek_2() {
        $s = 'a:DELTA-1';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('unknownFunctionCase', $at1->get_answernote());
        // Note the capital D in the feedback here.  We suggest a capital Delta.
        $this->assertEquals('Input is case sensitive:  <span class="stacksyntaxexample">DELTA</span> '.
                'is an unknown function.  Did you mean <span class="stacksyntaxexample">Delta</span>?',
                $at1->get_errors());
    }

    public function test_forbid_function_single_letter() {
        $s = 'a:x^2+a+f(x)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s'));
        // This next test returns false, because the check only looks for keys longer than one character.
        $this->assertFalse($at1->check_external_forbidden_words(array('f')));
    }

    public function test_forbid_function_multiple_letter() {
        $s = 'a:x^2+a+fn(x)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s'));
        $this->assertTrue($at1->check_external_forbidden_words(array('fn')));
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

    public function test_semicolon() {
        $s = 'a:3;b:4';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s'));
        $this->assertEquals('forbiddenChar', $at1->get_answernote());
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
        $this->assertEquals('unknownFunction', $at1->get_answernote());
    }

    public function test_units_2_u() {
        $s = 'sa:3.14*moles';
        $at1 = new stack_cas_casstring($s);
        $at1->set_units(true);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unitssynonym', $at1->get_answernote());
    }

    public function test_units_3() {
        $s = 'sa:3.14*Moles';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unknownFunction', $at1->get_answernote());
    }

    public function test_units_3_u() {
        $s = 'sa:3.14*Moles';
        $at1 = new stack_cas_casstring($s);
        $at1->set_units(true);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('unitssynonym', $at1->get_answernote());
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

    public function test_units_7() {
        $s = '56.7*hr';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('56.7*hr', $at1->get_casstring());
        $this->assertEquals('', $at1->get_key());
        $this->assertEquals('', $at1->get_answernote());
    }

    public function test_units_8() {
        $s = '56.7*hr';
        $at1 = new stack_cas_casstring($s);
        $at1->set_units(true);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('56.7*hr', $at1->get_casstring());
        $this->assertEquals('', $at1->get_key());
        $this->assertEquals('unitssynonym', $at1->get_answernote());
    }

    public function test_units_9() {
        $s = '56.7*kgm/s';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('56.7*kgm/s', $at1->get_casstring());
        $this->assertEquals('', $at1->get_key());
        $this->assertEquals('unknownFunction', $at1->get_answernote());
    }

    public function test_units_10() {
        $s = '56.7*kgm/s';
        $at1 = new stack_cas_casstring($s);
        $at1->set_units(true);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('56.7*kg*m/s', $at1->get_casstring());
        $this->assertEquals('', $at1->get_key());
        $this->assertEquals('', $at1->get_answernote());
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

    public function test_logic_noun_sort_1() {
        $s = 'a:x=1 or x=2';
        $s = stack_utils::logic_nouns_sort($s, 'add');
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s'));
        $this->assertEquals('x=1 nounor x=2', $at1->get_casstring());
    }

    public function test_spaces_1_simple() {
        $s = 'a b';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 1));
        $this->assertEquals('a b', $at1->get_casstring());
        $err = 'Illegal spaces found in expression <span class="stacksyntaxexample">a<font color="red">_</font>b</span>.';
        $this->assertEquals($err, $at1->get_errors());
        $this->assertEquals('spaces', $at1->get_answernote());
    }

    public function test_spaces_1_multiple() {
        $s = 'a   b';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 1));
        $this->assertEquals('a   b', $at1->get_casstring());
        $err = 'Illegal spaces found in expression <span class="stacksyntaxexample">a<font color="red">_</font>b</span>.';
        $this->assertEquals($err, $at1->get_errors());
        $this->assertEquals('spaces', $at1->get_answernote());
    }

    public function test_spaces_1_brackets() {
        $s = 'a (b c)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 1));
        $this->assertEquals('a (b c)', $at1->get_casstring());
        $err = 'Illegal spaces found in expression '
                .'<span class="stacksyntaxexample">a<font color="red">_</font>(b<font color="red">_</font>c)</span>.';
                $this->assertEquals($err, $at1->get_errors());
                $this->assertEquals('spaces', $at1->get_answernote());
    }

    public function test_spaces_1_bracket_brackets() {
        $s = '(1+c) (x+1)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 1));
        $this->assertEquals('(1+c) (x+1)', $at1->get_casstring());
        $err = 'Illegal spaces found in expression '
                .'<span class="stacksyntaxexample">(1+c)<font color="red">_</font>(x+1)</span>.';
                $this->assertEquals($err, $at1->get_errors());
                $this->assertEquals('spaces', $at1->get_answernote());
    }

    public function test_spaces_1_logic() {
        $s = 'a b and c';
        $s = stack_utils::logic_nouns_sort($s, 'add');
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 1));
        $this->assertEquals('a b nounand c', $at1->get_casstring());
        $err = 'Illegal spaces found in expression <span class="stacksyntaxexample">a<font color="red">_</font>b and c</span>.';
        $this->assertEquals($err, $at1->get_errors());
        $this->assertEquals('spaces', $at1->get_answernote());
    }

    public function test_spaces_3_simple() {
        $s = 'a b';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 3));
        $this->assertEquals('a*b', $at1->get_casstring());
    }

    public function test_spaces_3_multiple() {
        $s = 'a   b';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 3));
        $this->assertEquals('a*b', $at1->get_casstring());
    }

    public function test_spaces_3_scientific() {
        // We probably don't really want this behaviour, but this is a consequence of adding *s.
        $s = '3 E 4';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 3));
        $this->assertEquals('3*E*4', $at1->get_casstring());
    }

    public function test_spaces_3_brackets() {
        $s = 'a (b c)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 3));
        $this->assertEquals('a*(b*c)', $at1->get_casstring());
    }

    public function test_spaces_3_bracket_brackets() {
        $s = '(1+c) (x+1)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 3));
        $this->assertEquals('(1+c)*(x+1)', $at1->get_casstring());
    }

    public function test_spaces_3_logic() {
        $s = 'a b and c';
        $s = stack_utils::logic_nouns_sort($s, 'add');
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 3));
        $this->assertEquals('a*b nounand c', $at1->get_casstring());
    }

    public function test_spaces_0_insertneeded() {
        $s = '3sin(a+b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('3sin(a+b)', $at1->get_casstring());
        $err = 'You seem to be missing * characters. Perhaps you meant to type '
                .'<span class="stacksyntaxexample">3<font color="red">*</font>sin(a+b)</span>.';
                $this->assertEquals($err, $at1->get_errors());
                $this->assertEquals('missing_stars', $at1->get_answernote());
    }

    public function test_spaces_0_insertneeded_andspace() {
        $s = '3sin(a b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('3sin(a b)', $at1->get_casstring());
        $err = 'Illegal spaces found in expression <span class="stacksyntaxexample">3sin(a<font color="red">_</font>b)</span>. '
                .'You seem to be missing * characters. Perhaps you meant to type '
                        .'<span class="stacksyntaxexample">3<font color="red">*</font>sin(a b)</span>.';
                        $this->assertEquals($err, $at1->get_errors());
                        $this->assertEquals('spaces | missing_stars', $at1->get_answernote());
    }

    public function test_spaces_1_insertneeded_andspace() {
        $s = '3sin(a b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 1));
        $this->assertEquals('3*sin(a b)', $at1->get_casstring());
        $err = 'Illegal spaces found in expression <span class="stacksyntaxexample">3sin(a<font color="red">_</font>b)</span>.';
        $this->assertEquals($err, $at1->get_errors());
        $this->assertEquals('spaces | missing_stars', $at1->get_answernote());
    }

    public function test_spaces_3_insertneeded_andspace() {
        $s = '3sin(a b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 3));
        $this->assertEquals('3sin(a*b)', $at1->get_casstring());
        $err = 'You seem to be missing * characters. Perhaps you meant to type '
                .'<span class="stacksyntaxexample">3<font color="red">*</font>sin(a*b)</span>.';
                $this->assertEquals($err, $at1->get_errors());
                $this->assertEquals('spaces | missing_stars', $at1->get_answernote());
    }

    public function test_spaces_4_insertneeded_andspace() {
        $s = '3sin(a b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 4));
        $this->assertEquals('3*sin(a*b)', $at1->get_casstring());
        $this->assertEquals('spaces | missing_stars', $at1->get_answernote());
    }

    public function test_spaces_5_insertneeded_andspace_trigexp() {
        $s = '3sin^3(ab)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 4));
        $this->assertEquals('3*sin^3*(ab)', $at1->get_casstring());
        $this->assertEquals('trigexp | missing_stars', $at1->get_answernote());
    }

    public function test_spaces_3_sin() {
        $s = 'sin x';
        $at1 = new stack_cas_casstring($s);
        $at1->get_valid('s', true, 3);
        $this->assertEquals('sin x', $at1->get_casstring());
        $err = 'To apply a trig function to its arguments you must use brackets, not spaces.  '.
            'For example use <span class="stacksyntaxexample">sin(...)</span> instead.';
                        $this->assertEquals($err, $at1->get_errors());
                        $this->assertEquals('trigspace | spaces', $at1->get_answernote());
    }

    public function test_spaces_4_insertneeded_true() {
        $s = '3b(x y)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 4));
        $this->assertEquals('3*b(x*y)', $at1->get_casstring());
        $this->assertEquals('spaces | missing_stars', $at1->get_answernote());
    }

    public function test_spaces_4_insertneeded_true_2() {
        $s = '3sin(a b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 4));
        $this->assertEquals('3*sin(a*b)', $at1->get_casstring());
    }

    public function test_log_sugar_1() {
        $s = 'log(x)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('log(x)', $at1->get_casstring());
    }

    public function test_log_sugar_2() {
        $s = 'log_10(a+x^2)+log_a(b)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('lg(a+x^2, 10)+lg(b, a)', $at1->get_casstring());
        $this->assertEquals('logsubs', $at1->get_answernote());
    }

    public function test_log_sugar_3() {
        // Note that STACK spots there is a missing * here.
        $s = 'log_5x(3)';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('lg(3, 5x)', $at1->get_casstring());
        $this->assertEquals('logsubs | missing_stars', $at1->get_answernote());
    }

    public function test_log_sugar_4() {
        // The missing * in this expression is correctly inserted.
        $s = 'log_5x(3)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 1));
        $this->assertEquals('lg(3, 5*x)', $at1->get_casstring());
        $this->assertEquals('logsubs | missing_stars', $at1->get_answernote());
    }

    public function test_log_sugar_5() {
        $s = 'log_x^2(3)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('lg(3, x^2)', $at1->get_casstring());
        $this->assertEquals('logsubs', $at1->get_answernote());
    }

    public function test_log_sugar_6() {
        $s = 'log_%e(%e)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('lg(%e, %e)', $at1->get_casstring());
        $this->assertEquals('logsubs', $at1->get_answernote());
    }

    public function test_log_key_vals_1() {
        $s = 'log_x:log_x(a)';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('s', true, 0));
        $this->assertEquals('lg(a, x)', $at1->get_casstring());
        $this->assertEquals('log_x', $at1->get_key());
        $this->assertEquals('logsubs', $at1->get_answernote());
    }

    public function test_chained_inequalities_s() {
        $s = 'sa:3<x<5';
        $at1 = new stack_cas_casstring($s);
        $this->assertFalse($at1->get_valid('s', true, 0));
        $this->assertEquals('3<x<5', $at1->get_casstring());
        $this->assertEquals('sa', $at1->get_key());
        $this->assertEquals('chained_inequalities', $at1->get_answernote());
    }

    public function test_chained_inequalities_t() {
        $s = 'f(x) := if x < 0 then (if x < 1 then 1 else 2) else 3';
        $at1 = new stack_cas_casstring($s);
        $this->assertTrue($at1->get_valid('t', true, 0));
        $this->assertEquals('f(x) := if x < 0 then (if x < 1 then 1 else 2) else 3', $at1->get_casstring());
        $this->assertEquals('', $at1->get_key());
        $this->assertEquals('', $at1->get_answernote());
    }
}
