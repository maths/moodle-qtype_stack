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

require_once(dirname(__FILE__) . '/../../../locallib.php');
require_once(dirname(__FILE__) . '/../cassession.class.php');

class stack_cas_session_test
extends UnitTestCase {

    public function get_valid($cs, $val) {

        if (is_array($cs)) {
            $s1 = array();
            foreach ($cs as $s) {
                $s1[] = new stack_cas_casstring($s);
            }
        } else {
            $s1 = null;
        }

        $at1 = new stack_cas_session($s1);
        $this->assertEqual($val, $at1->get_valid());
    }

    public function test_get_valid() {

        $a1 = array('x^2', '(x+1)^2');
        $a2 = array('x^2', 'x+1)^2');

        $cases = array(
            array(null, true),
            array($a1, true),
            array($a2, false)
        );

        foreach ($cases as $case) {
            $this->get_valid($case[0], $case[1]);
        }

    }

    public function test_get_display() {

        $cs=array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEqual('x^2', $at1->get_display_key('a'));
        $this->assertEqual('\frac{1}{1+x^2}', $at1->get_display_key('b'));
        $this->assertEqual('e^{\mathrm{i}\cdot \pi}', $at1->get_display_key('c'));

    }

    public function test_keyval_representation_1() {

        $cs=array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $at1 = new stack_cas_session($s1, null, 0);
        $this->assertEqual('a=x^2; b=1/(1+x^2); c=e^(i*pi);', $at1->get_keyval_representation());
        $this->assertEqual(array('a', 'b', 'c'), $at1->get_all_keys());
    }

    public function test_keyval_representation_2() {

        $cs=array('a:(-1)^2');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $at1 = new stack_cas_session($s1, null, 0);
        $this->assertEqual('a=(-1)^2;', $at1->get_keyval_representation());
        $at1->instantiate();
        $this->assertEqual('a=1;', $at1->get_keyval_representation());
    }

    public function test_string1(){

        $cs=array('s:"This is a string"');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEqual('"This is a string"', $at1->get_value_key('s'));
    }
}


class stack_cas_session_exception_test extends UnitTestCase {

    public function test_exception_1() {
        $this->expectException();
        $at1 = new stack_cas_session("x=1", false, false);
    }

    public function test_exception_2() {
        $this->expectException();
        $at1 = new stack_cas_session(array(), null, false);
        $at1->get_valid();
    }

    public function test_exception_3() {
        $this->expectException();
        $at1 = new stack_cas_session(array(1, 2, 3), null, false);
    }

    public function test_exception_4() {
        $this->expectException();
        $at1 = new stack_cas_session(null, 123, false);
    }

    public function test_exception_5() {
        $pref = new stack_options();
        $this->expectException();
        $at1 = new stack_cas_session(null, $pref, "abc");
    }

}
