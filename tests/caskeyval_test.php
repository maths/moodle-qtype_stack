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
require_once(__DIR__ . '/../stack/cas/cassession.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

// Unit tests for {@link stack_cas_keyval}.

/**
 * @group qtype_stack
 */
class stack_cas_keyval_test extends qtype_stack_testcase {

    public function get_valid($s, $val, $session) {
        $kv = new stack_cas_keyval($s, null, 123, 's', true, 0);
        $kv->instantiate();
        $this->assertEquals($val, $kv->get_valid());

        $kvsession = $kv->get_session();
        $this->assertEquals($session->get_session(), $kvsession->get_session());
    }

    public function test_get_valid() {

        $cs0 = new stack_cas_session(null, null, 123);
        $cs0->instantiate();

        $a1 = array('a:x^2', 'b:(x+1)^2');
        $s1 = array();
        foreach ($a1 as $s) {
            $s1[] = new stack_cas_casstring($s);
        }
        $cs1 = new stack_cas_session($s1, null, 123);
        $cs1->instantiate();

        $a2 = array('a:x^2)', 'b:(x+1)^2');
        $s2 = array();
        foreach ($a2 as $s) {
            $s2[] = new stack_cas_casstring($s);
        }
        $cs2 = new stack_cas_session($s2, null, 123);
        $cs2->instantiate();

        $a3 = array('a:1/0');
        $s3 = array();
        foreach ($a3 as $s) {
            $s3[] = new stack_cas_casstring($s);
        }
        $cs3 = new stack_cas_session($s3, null, 123);
        $cs3->instantiate();

        $cs4 = new stack_cas_session(null, null, 123);

        $cases = array(
                array('', true, $cs0),
                array("a:x^2 \n b:(x+1)^2", true, $cs1),
                array("a:x^2; b:(x+1)^2", true, $cs1),
                array('a:x^2); b:(x+1)^2', false, $cs2),
                array('@', false, $cs4),
                array('$', false, $cs4),
        );

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1], $case[2]);
        }
    }

    public function test_empty_case_1() {
        $at1 = new stack_cas_keyval('', null, 123, 's', true, 0);
        $this->assertTrue($at1->get_valid());
    }

    public function test_equations_1() {
        $at1 = new stack_cas_keyval('ta1 : x=1; ta2 : x^2-2*x=1', null, 123, 's', true, 0);
        $at1->instantiate();
        $s = $at1->get_session();
        $this->assertEquals($s->get_value_key('ta1'), 'x = 1');
        $this->assertEquals($s->get_value_key('ta2'), 'x^2-2*x = 1');
    }

    public function test_remove_comment() {
        $at1 = new stack_cas_keyval("a:1\n /* This is a comment \n b:2\n */\n c:3", null, 123, 's', true, 0);
        $this->assertTrue($at1->get_valid());

        $a3 = array('a:1', 'c:3');
        $s3 = array();
        foreach ($a3 as $s) {
            $s3[] = new stack_cas_casstring($s);
        }
        $cs3 = new stack_cas_session($s3, null, 123);
        $cs3->instantiate();
        $at1->instantiate();

        // This looks strange, but the cache layer gives inconsistent results if the first
        // of these populates the cache, and the second one uses it.
        $this->assertEquals($cs3->get_session(), $at1->get_session()->get_session());
    }

    public function test_remove_comment_fail() {
        $at1 = new stack_cas_keyval("a:1\n /* This is a comment \n b:2\n */\n c:3", null, 123, 's', true, 0);
        $this->assertTrue($at1->get_valid());

        $a3 = array('a:1', 'c:4');
        $s3 = array();
        foreach ($a3 as $s) {
            $s3[] = new stack_cas_casstring($s);
        }
        $cs3 = new stack_cas_session($s3, null, 123);
        $cs3->instantiate();
        $at1->instantiate();

        // This looks strange, but the cache layer gives inconsistent results if the first
        // of these populates the cache, and the second one uses it.
        $this->assertNotEquals($cs3->get_session(), $at1->get_session()->get_session());
    }

    public function test_keyval_session_keyval_0() {
        $kvin = "";
        $at1 = new stack_cas_keyval($kvin, null, 123, 's', true, 0);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();
        $this->assertEquals($kvin, $kvout);
    }

    public function test_keyval_session_keyval_1() {
        $kvin = "a:1; c:3;";
        $at1 = new stack_cas_keyval($kvin, null, 123, 's', true, 0);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();
        $this->assertEquals($kvin, $kvout);
    }

    public function test_keyval_session_keyval_2() {
        // Equation and function.
        $kvin = "ans1:x^2-2*x=1; f(x):=x^2; sin(x^3);";
        $at1 = new stack_cas_keyval($kvin, null, 123, 's', true, 0);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();
        $this->assertEquals($kvin, $kvout);
    }

    public function test_keyval_session_keyval_3() {
        // Inserting stars: strict syntax is *false* and we add in more stars.
        $kvin  = "a:2x; b:(x+1)(x-1); b:f(x); c:x7;";
        $kvins = "a:2*x; b:(x+1)*(x-1); b:f*(x); c:x*7;";
        $at1 = new stack_cas_keyval($kvin, null, 123, 's', false, 1);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();

        $this->assertEquals($kvins, $kvout);
    }

    public function test_keyval_session_keyval_4() {
        // Inserting stars. Strict syntax is *true*.
        $kvin  = "a:2x; b:(x+1)(x-1); b:f(x); c:x7;";
        $kvins = "a:2*x; b:(x+1)*(x-1); b:f(x); c:x7;";
        $at1 = new stack_cas_keyval($kvin, null, 123, 's', true, 1);
        $session = $at1->get_session();
        $kvout = $session->get_keyval_representation();

        $this->assertEquals($kvins, $kvout);
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
        $kv = new stack_cas_keyval($s, null, 123, 's', true, 0);
        $this->assertFalse($kv->get_valid(array('ans1')));
        $this->assertEquals('You may not use input names as variables.  '.
            'You have tried to define <code>ans1</code>', $kv->get_errors());
    }
}
