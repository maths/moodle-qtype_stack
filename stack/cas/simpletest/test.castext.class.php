<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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

require_once(dirname(__FILE__) . '/../../../locallib.php');
require_once(dirname(__FILE__) . '/../castext.class.php');

class stack_cas_text_test
extends UnitTestCase{

    public function get_valid($strin, $sa, $val, $disp) {

        if (is_array($sa)) {
            $s1=array();
            foreach ($sa as $s) {
                $s1[] = new stack_cas_casstring($s);
            }
            $cs1 = new stack_cas_session($s1);
        } else {
            $cs1 = null;
        }

        $at1 = new stack_cas_text($strin, $cs1);
        $this->assertEqual($val, $at1->get_valid());
        $this->assertEqual($disp, $at1->get_display_castext());
    }

    public function test_get_valid() {

        $a1 = array('a:x^2', 'b:(x+1)^2');
        $a2 = array('a:x^2)', 'b:(x+1)^2');

        $cases = array(
                array('', null, true, ''),
                array('Hello world', null, true, 'Hello world'),
                array('$x^2$', null, true, '$x^2$'),
                array('@x*x^2@', null, true, '$x^3$'),
                array('@1+2@', null, true, '$3$'),
                array('\[@x^2@\]', null, true, '\[x^2\]'),
                array('\[@a@\]', $a1, true, '\[x^2\]'),
                array('@a@', $a1, true, '$x^2$'),
                array('@sin(x)@', $a1, true, '$\sin \left( x \right)$'),
                array('\[@a*b@\]', $a1, true, '\[x^2\cdot \left(x+1\right)^2\]'),
                array('@', null, false, false),
                array('@(x^2@', null, false, false),
                array('@1/0@', null, true, '$1/0$'),
                array('@x^2@', $a2, false, false),
        );

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1], $case[2], $case[3]);
        }

    }

    public function check_external_forbidden_words($ct, $val, $words) {

        $a2=array('a:x^2)', 'b:(sin(x)+1)^2');
        $s2=array();
        foreach ($a2 as $s) {
            $s2[] = new stack_cas_casstring($s);
        }
        $cs2 = new stack_cas_session($s2);

        $at1 = new stack_cas_text($ct, $cs2);
        $this->assertEqual($val, $at1->check_external_forbidden_words($words));

    }

    public function testcheck_external_forbidden_words() {
        $cases =  array(
            array('', false, array()),
            array('$\sin(x)$', false, array()),
            array('$\cos(x)$', false, array('cos')),
            array('@cos(x)@', true, array('cos')),
            array('$\cos(x)$', true, array('sin')), // sin(x) is in the session above!
        );

        foreach ($cases as $case) {
            $this->check_external_forbidden_words($case[0], $case[1], $case[2]);
        }

    }
}

class stack_cas_text_exception_test
extends UnitTestCase {

    public function exception($a, $b, $c, $d, $e, $f) {
        $this->expectException();
        $at1 = new stack_cas_session($a, $b, $c, $d, $e, $f);
        $at1->get_valid();
    }

    public function test_exception() {
        // __construct($rawCASText, $session=null, $seed=null, $securityLevel='s', $syntax=true, $stars=false)

        $session = new stack_cas_session(null);

        $cases = array(
            array(array(), null, null, null, false, false),
            array("Hello world", array(1), null, null, false, false),
            array("Hello world", $session, null, null, false, false),
            array("Hello world", $session, "abc", null, false, false),
            array("Hello world", $session, 123, 123, false, false),
            array("Hello world", $session, null, 'z', false, false),
            array("Hello world", $session, null, 't', 1, false),
            array("Hello world", $session, null, 't', false, 1)
            );

        foreach ($cases as $case) {
            $this->Exception($case[0], $case[1], $case[2], $case[3], $case[4], $case[5]);
        }

    }
}
