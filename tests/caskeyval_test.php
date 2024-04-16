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

use qtype_stack_testcase;
use stack_ast_container;
use stack_cas_keyval;
use stack_cas_security;
use stack_cas_session2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

// Unit tests for {@link stack_cas_keyval}.

/**
 * @group qtype_stack
 * @covers \stack_cas_keyval
 */
class caskeyval_test extends qtype_stack_testcase {

    public function get_valid($s, $val, $session) {
        $kv = new stack_cas_keyval($s, null, 123);
        $kv->instantiate();
        $this->assertEquals($val, $kv->get_valid());

        // In the old world (<4.3) we compared the raw objects.
        // But now the objects contain complex references and positional data
        // so we comapre the representations of those objects.
        $this->assertEquals($session->get_keyval_representation(),
                            $kv->get_session()->get_keyval_representation());
    }

    public function test_get_valid() {

        $cs0 = new stack_cas_session2([], null, 123);
        $cs0->instantiate();

        $a1 = ['a:x^2', 'b:(x+1)^2'];
        $s1 = [];
        foreach ($a1 as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), []);
        }
        $cs1 = new stack_cas_session2($s1, null, 123);
        $cs1->instantiate();

        $a2 = ['a:1/0'];
        $s2 = [];
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), []);
        }
        $cs2 = new stack_cas_session2($s2, null, 123);
        $cs2->instantiate();

        $cases = [
                ['', true, $cs0],
                ["a:x^2 \n b:(x+1)^2", true, $cs1],
                ["a:x^2; b:(x+1)^2", true, $cs1],
                // In the new setup the parsing of the keyvals does not match the sessions created above.
                // This is because of a failure to split the text into statements.
                // This is a serious drawback when we try to identify which statement is throwing an error!
                ["a:x^2) \n b:(x+1)^2", false, $cs0],
                ['a:x^2); b:(x+1)^2', false, $cs0],
                ['a:1/0', true, $cs2],
                ['@', false, $cs0],
                ['$', false, $cs0],
        ];

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1], $case[2]);
        }
    }

    public function test_empty_case_1() {
        $at1 = new stack_cas_keyval('', null, 123);
        $this->assertTrue($at1->get_valid());
    }

    // Now here we have a problem, keyvals do not generate output values
    // they just load stuff to the session, therefore you cannot get
    // the instantiated values.
    public function test_equations_1() {
        $at1 = new stack_cas_keyval('ta1 : x=1; ta2 : x^2-2*x=1; ta3:x=1 nounor x=2', null, 123);
        $at1->instantiate();
        $s = $at1->get_session();
        $s->instantiate();
        $this->assertEquals($s->get_by_key('ta1')->get_evaluationform(), 'ta1:x = 1');
        $this->assertEquals($s->get_by_key('ta2')->get_evaluationform(), 'ta2:x^2-2*x = 1');
        $this->assertEquals($s->get_by_key('ta3')->get_evaluationform(), 'ta3:x = 1 nounor x = 2');
    }

    public function test_keyval_session_keyval_0() {
        $kvin = "";
        $at1 = new stack_cas_keyval($kvin, null, 123);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();
        $this->assertEquals($kvin, $kvout);
    }

    public function test_keyval_session_keyval_1() {
        $kvin = "a:1;\nc:3;";
        $at1 = new stack_cas_keyval($kvin, null, 123);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();
        $this->assertEquals($kvin, $kvout);
    }

    public function test_keyval_session_keyval_2() {
        // Equation and function.
        $kvin = "ans1:x^2-2*x = 1;\nf(x):=x^2;\nsin(x^3);";
        $at1 = new stack_cas_keyval($kvin, null, 123);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();
        $this->assertEquals($kvin, $kvout);
    }

    public function test_basic_logic() {
        $tests = "t1: is(1>0);
                t2: t1 and true;
                t3: true or true;
                f4: false;
                f5: not(t1) and false;
                f6: not(true and true);
                t7: not(false);
                t8: not(f6);
                t9: t8 and true;
        ";

        $kv = new stack_cas_keyval($tests);
        $this->assertTrue($kv->get_valid());
        $kv->instantiate();
        foreach ($kv->get_session()->get_session() as $cs) {
            $expect = (strpos($cs->get_key(), 't') === 0) ? 'true' : 'false';
            $this->assertEquals($expect, $cs->get_value());
        }
    }

    public function test_keyval_input_capture() {
        $s = 'a:x^2; ans1:a+1; ta:a^2';
        $kv = new stack_cas_keyval($s, null, 123);
        $this->assertFalse($kv->get_valid(['ans1']));
    }

    public function test_remove_comment() {
        $at1 = new stack_cas_keyval("a:1\n /* This is a comment \n b:2\n */\n c:3^2", null, 123);
        $this->assertTrue($at1->get_valid());
        $at1->instantiate();

        $session = $at1->get_session()->get_session();
        $expected = ['a:1', 'c:3^2'];
        foreach ($session as $key => $statement) {
            $this->assertEquals($expected[$key], $statement->get_inputform());
        }
        $expected = ['1', '9'];
        foreach ($session as $key => $statement) {
            $this->assertEquals($expected[$key], $statement->get_value());
        }
    }

    public function test_remove_comment_hanging() {
        $at1 = new stack_cas_keyval("a:1\n /* This is an open comment \n b:2\n \n c:3^2", null, 123);
        $this->assertFalse($at1->get_valid());
        $at1->instantiate();
    }

    public function test_multiline_input() {
        $tests = "n:3;\nif is(n=3) then (\nk1:1,\nk2:2\n) else (\nk1:3,\nk2:4\n);\na:k2^2;";

        $kv = new stack_cas_keyval($tests);
        $this->assertTrue($kv->get_valid());
        $kv->instantiate();
        $s = $kv->get_session();
        $expected = "n:3;\nif is(n = 3) then (k1:1,k2:2) else (k1:3,k2:4);\na:k2^2;";
        $this->assertEquals($expected, $s->get_keyval_representation());

        $expected = "n:3;\na:4;";
        $this->assertEquals($expected, $s->get_keyval_representation(true));
    }

    public function test_brackets_in_strings() {
        $tests = "k1:4^2;\nprefix:\"[\";\nsuffix:\"]\";";

        $kv = new stack_cas_keyval($tests);
        $this->assertTrue($kv->get_valid());
        $kv->instantiate();
        $s = $kv->get_session();
        $expected = "k1:4^2;\nprefix:\"[\";\nsuffix:\"]\";";
        $this->assertEquals($expected, $s->get_keyval_representation());

        $expected = "k1:16;\nprefix:\"[\";\nsuffix:\"]\";";
        $this->assertEquals($expected, $s->get_keyval_representation(true));
    }

    public function test_ampersand_in_strings() {
        $tests = 'k1:"~@r";n1:2*4;';

        $kv = new stack_cas_keyval($tests);
        $this->assertTrue($kv->get_valid());
        $kv->instantiate();
        $s = $kv->get_session();
        $expected = "k1:\"~@r\";\nn1:2*4;";
        $this->assertEquals($expected, $s->get_keyval_representation());

        $expected = "k1:\"~@r\";\nn1:8;";
        $this->assertEquals($expected, $s->get_keyval_representation(true));
    }

    public function test_ampersand_outside_strings() {
        $tests = 'k1:u@x;n1:2*4;';

        $kv = new stack_cas_keyval($tests);
        $this->assertFalse($kv->get_valid());
        $expected = ['The characters @, $ and \ are not allowed in CAS input.'];
        $this->assertEquals($expected, $kv->get_errors());
    }

    public function test_needs_mbstring() {

        $tests = "x : rand([1,2,3])\ny : rand([2,3,4])\nA : matrix([x,2,1],[3,4,2],[1,y,5])\n" .
                 "R : get_lu_factors(lu_factor(A))\nL : R[2]\nU : R[3]\n\n/* Help for worked solutions */\n" .
                 "a11 : A[1,1]\na12 : A[1,2]\na13 : A[1,3]\na21 : A[2,1]\na22 : A[2,2]\na23 : A[2,3]\na31 : A[3,1]" .
                 "a32 : A[3,2]\na33 : A[3,3]\nB :\nmatrix([a11,a12,a13],[a21-a21/a11*a11,a22-a21/a11*a12," .
                 "a23-a21/a11*a13],[a31-a31/a11*a11,a32-a31/a11*a12,a33-a31/a11*a13])\n".
                 "C : B-matrix([0,0,0],[0,0,0],[0,B[3,2]/B[2,2]*B[2,2], B[3,2]/B[2,2]*B[2,3]])\n".
                 "coef1 : a21/a11\ncoef2 : a31/a11\ncoef3 : B[3,2]/B[2,2]";

        $kv = new stack_cas_keyval($tests);
        $this->assertTrue($kv->get_valid());
        $kv->instantiate();
        $s = $kv->get_session();
        $expected = "x:rand([1,2,3]);\ny:rand([2,3,4]);\nA:matrix([x,2,1],[3,4,2],[1,y,5]);\n" .
                    "R:get_lu_factors(lu_factor(A));\nL:R[2];\nU:R[3];\na11:A[1,1];\na12:A[1,2];\n" .
                    "a13:A[1,3];\na21:A[2,1];\na22:A[2,2];\na23:A[2,3];\na31:A[3,1];\na32:A[3,2];\n" .
                    "a33:A[3,3];\n" .
                    "B:matrix([a11,a12,a13],[a21-a21/a11*a11,a22-a21/a11*a12,a23-a21/a11*a13]," .
                    "[a31-a31/a11*a11,a32-a31/a11*a12,a33-a31/a11*a13]);\n" .
                    "C:B-matrix([0,0,0],[0,0,0],[0,B[3,2]/B[2,2]*B[2,2],B[3,2]/B[2,2]*B[2,3]]);\n" .
                    "coef1:a21/a11;\ncoef2:a31/a11;\ncoef3:B[3,2]/B[2,2];";
        $this->assertEquals($expected, $s->get_keyval_representation());
    }

    public function test_usage() {
        // Notes, for global variable usage:
        // The ev case where both : and = work as the definition of values.
        // The block case where some variables may be listed as locals.
        // The function definition case where the arguments are locals.
        // The multiple assing case where more than one is written.
        // Evaluation-flags.
        // By refrence function like push.
        $kv = new stack_cas_keyval("foo:ev(bar,x:y,z=y);" .
            "f(x,y):=block([bar],bar:1+x,[y,x]);" .
            "g(x,y):=(x:1+x,[y,x]:[x,y]);" .
            "[baz,T]:f(x,y);" .
            "g(1,2),x=3,y:4" .
            "push(x,V);" .
            "block([bar],push(x,bar));");
        $this->assertTrue($kv->get_valid());
        $usage = $kv->get_variable_usage();
        // Variables x, y, z, and bar are never globally written.
        $this->assertFalse(isset($usage['write']['x']));
        $this->assertFalse(isset($usage['write']['y']));
        $this->assertFalse(isset($usage['write']['z']));
        $this->assertFalse(isset($usage['write']['bar']));
        // Functions foo, baz, and T are being written globally.
        $this->assertTrue(isset($usage['write']['foo']));
        $this->assertTrue(isset($usage['write']['baz']));
        $this->assertTrue(isset($usage['write']['T']));
        $this->assertTrue(isset($usage['write']['V']));
    }

    public function test_unclear_subs() {
        $tests = 'v:2;trig:[sin,cos][v];sub:[(sin(x))^2=1-(cos(x))^2,(cos(x))^2=1-(sin(x))^2][v];f:(trig(x))^n;'
            . 'df:diff(f,x);df_simp:(subst(sub,df));ta1:expand(df_simp);';

        $kv = new stack_cas_keyval($tests);
        // This changed since we check Maxima-side.
        $this->assertTrue($kv->get_valid());
        $expected = [];
        $this->assertEquals($expected, $kv->get_errors());

        $kv->instantiate();
        $s = $kv->get_session();
        $expected = "v:2;\n" .
                    "trig:[sin,cos][v];\n" .
                    "sub:[(sin(x))^2 = 1-(cos(x))^2,(cos(x))^2 = 1-(sin(x))^2][v];\n" .
                    "f:(trig(x))^n;\n" .
                    "df:diff(f,x);\n" .
                    "df_simp:(subst(sub,df));\n" .
                    "ta1:expand(df_simp);";
        $this->assertEquals($expected, $s->get_keyval_representation());
    }

    public function test_stack_seed_redef() {
        $tests = 'v:2;stack_seed:2';
        $kv = new stack_cas_keyval($tests);
        $this->assertFalse($kv->get_valid());
        $expected = ['Redefinition of key constants is forbidden: ' .
            '<span class="stacksyntaxexample">stack_seed</span>.'];
        $this->assertEquals($expected, $kv->get_errors());
    }
}
