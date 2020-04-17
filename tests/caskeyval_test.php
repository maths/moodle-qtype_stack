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
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

// Unit tests for {@link stack_cas_keyval}.

/**
 * @group qtype_stack
 */
class stack_cas_keyval_test extends qtype_stack_testcase {

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

        $cs0 = new stack_cas_session2(array(), null, 123);
        $cs0->instantiate();

        $a1 = array('a:x^2', 'b:(x+1)^2');
        $s1 = array();
        foreach ($a1 as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs1 = new stack_cas_session2($s1, null, 123);
        $cs1->instantiate();

        $a2 = array('a:1/0');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $cs2 = new stack_cas_session2($s2, null, 123);
        $cs2->instantiate();

        $cases = array(
                array('', true, $cs0),
                array("a:x^2 \n b:(x+1)^2", true, $cs1),
                array("a:x^2; b:(x+1)^2", true, $cs1),
                // In the new setup the parsing of the keyvals does not match the sessions created above.
                // This is because of a failure to split the text into statements.
                // This is a serious drawback when we try to identify which statement is throwing an error!
                array("a:x^2) \n b:(x+1)^2", false, $cs0),
                array('a:x^2); b:(x+1)^2', false, $cs0),
                array('a:1/0', true, $cs2),
                array('@', false, $cs0),
                array('$', false, $cs0),
        );

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
        foreach ($kv->get_session() as $cs) {
            $expect = (strpos($cs->get_key(), 't') === 0) ? 'true' : 'false';
            $this->assertEquals($expect, $cs->get_value());
        }
    }

    public function test_keyval_input_capture() {
        $s = 'a:x^2; ans1:a+1; ta:a^2';
        $kv = new stack_cas_keyval($s, null, 123);
        $this->assertFalse($kv->get_valid(array('ans1')));
        $errs = array('You may not use input names as variables.  ' .
                'You have tried to define <code>ans1</code>');
        $this->assertEquals($errs, $kv->get_errors());
    }

    public function test_remove_comment() {
        $at1 = new stack_cas_keyval("a:1\n /* This is a comment \n b:2\n */\n c:3^2", null, 123);
        $this->assertTrue($at1->get_valid());
        $at1->instantiate();

        $session = $at1->get_session()->get_session();
        $expected = array('a:1', 'c:3^2');
        foreach ($session as $key => $statement) {
            $this->assertEquals($expected[$key], $statement->get_inputform());
        }
        $expected = array('1', '9');
        foreach ($session as $key => $statement) {
            $this->assertEquals($expected[$key], $statement->get_value());
        }
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
}
