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

require_once(dirname(__FILE__) . '/../keyval.class.php');

class stack_cas_keyval_test
extends UnitTestCase {

    public function get_valid($s, $val) {
        $at1 = new stack_cas_keyval($s);
        $this->assertEqual($val, $at1->get_valid());
    }

    public function test_get_valid() {

        $a1=array('a:x^2', 'b:(x+1)^2');
        $s1=array();
        foreach ($a1 as $s) {
            $s1[] = new stack_cas_casstring($s);
        }
        $cs1 = new stack_cas_session($s1);
        $cs1->instantiate();

        $a2=array('a:x^2)', 'b:(x+1)^2');
        $s2=array();
        foreach ($a2 as $s) {
            $s2[] = new stack_cas_casstring($s);
        }
        $cs2 = new stack_cas_session($s2);
        $cs2->instantiate();

        $a3=array('a:1/0');
        $s3=array();
        foreach ($a3 as $s) {
            $s3[] = new stack_cas_casstring($s);
        }
        $cs3 = new stack_cas_session($s3);
        $cs3->instantiate();

        return array(
                array('', true, null),
                array("a=x^2 \n b=(x+1)^2", true, $cs1),
                array("a:x^2 \n b:(x+1)^2", true, $cs1),
                array("a=x^2; b=(x+1)^2", true, $cs1),
                array('a=x^2); b=(x+1)^2', false, $cs2),
                array('a=1/0', true, $cs3)
            );

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1], $case[2]);
        }
    }
}


class stack_cas_keyval_exception_test
extends UnitTestCase
{
    public function exception($a, $b, $c, $d) {
        $this->expectException();
        $at1 = new stack_cas_keyval($a, $b, $c, $d);
    }

    public function test_exception() {
        // __construct($raw, $securityLevel='s', $syntax=true, $stars=false)

        $cases = array(
                array(array(), false, false, false),
                array(1, false, false, false),
                array('x=1', false, false, false),
                array('x=1', null, false, false),
                array('x=1', 'z', false, false),
                array('x=1', 't', 1, false),
                array('x=1', 't', false, 1),
            );

        foreach ($cases as $case) {
            $this->exception($case[0], $case[1], $case[2], $case[3]);
        }

    }
}
