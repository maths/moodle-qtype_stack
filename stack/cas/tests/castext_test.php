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
require_once(dirname(__FILE__) . '/../../../tests/test_base.php');
require_once(dirname(__FILE__) . '/../castext.class.php');


/**
 * Unit tests for {@link stack_cas_text}.
 * @group qtype_stack
 */
class stack_cas_text_test extends qtype_stack_testcase {

    public function get_valid($strin, $sa, $val, $disp) {

        if (is_array($sa)) {
            $s1=array();
            foreach ($sa as $s) {
                $s1[] = new stack_cas_casstring($s);
            }
            $cs1 = new stack_cas_session($s1, null, 0);
        } else {
            $cs1 = null;
        }

        $at1 = new stack_cas_text($strin, $cs1, 0);
        $this->assertEquals($val, $at1->get_valid());
        $this->assertEquals($disp, $at1->get_display_castext());
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

    public function test_not_confused_by_pluginfile() {
        $ct = new stack_cas_text('Here @x@ is some @@PLUGINFILE@@ @x + 1@ some input', null, 0);
        $this->assertTrue($ct->get_valid());
        $this->assertEquals(array('x', 'x + 1'), $ct->get_all_raw_casstrings());
        $this->assertEquals('Here $x$ is some @@PLUGINFILE@@ $x+1$ some input', $ct->get_display_castext());
    }

    public function test_get_all_raw_casstrings() {
        $raw = 'Take @x^2+2*x@ and then @sin(z^2)@.';
        $at1 = new stack_cas_text($raw, null, 0);
        $val = array('x^2+2*x', 'sin(z^2)');
        $this->assertEquals($val, $at1->get_all_raw_casstrings());
    }

    public function test_get_all_raw_casstrings_empty() {
        $raw = 'Take some text without cas commands.';
        $at1 = new stack_cas_text($raw, null, 0);
        $val = array();
        $this->assertEquals($val, $at1->get_all_raw_casstrings());
    }

    public function test_get_all_raw_casstrings_session() {

        $sa = array('p:diff(sans)', 'q=int(tans)');
        foreach ($sa as $s) {
            $cs    = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }
        $cs1 = new stack_cas_session($s1, null, 0);

        $raw = 'Take @ 1/(1+x^2) @ and then @sin(z^2)@.';
        $at1 = new stack_cas_text($raw, $cs1, 0);
        $val = array('p:diff(sans)', 'q=int(tans)', '1/(1+x^2)', 'sin(z^2)');
        $this->assertEquals($val, $at1->get_all_raw_casstrings());

    }

    public function check_external_forbidden_words($ct, $val, $words) {

        $a2=array('a:x^2)', 'b:(sin(x)+1)^2');
        $s2=array();
        foreach ($a2 as $s) {
            $s2[] = new stack_cas_casstring($s);
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text($ct, $cs2, 0);
        $this->assertEquals($val, $at1->check_external_forbidden_words($words));

    }

    public function test_auto_generated_key_names() {

        $a2=array('a:x^2', 'caschat0:x^3');
        $s2=array();
        foreach ($a2 as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s2[] = $cs;
        }
        $cs2 = new stack_cas_session($s2, null, 0);

        $at1 = new stack_cas_text("This is some text @x^2@, @x^3@", $cs2, 0);
        $at1->get_display_castext();
        $session = $at1->get_session();
        $this->assertEquals(array('a', 'caschat0', 'caschat1', 'caschat2'), $session->get_all_keys());
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


/**
 * Unit tests for {@link stack_cas_text}.
 * @group qtype_stack
 */
class stack_cas_text_exception_test extends basic_testcase {

    public function test_exception_1() {
        $session = new stack_cas_session(null);
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_text(array(), null, null);
        $at1->get_valid();
    }

    public function test_exception_2() {
        $session = new stack_cas_session(null);
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_text("Hello world", array(1), null);
        $at1->get_valid();
    }

    public function test_exception_3() {
        $session = new stack_cas_session(null);
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_text("Hello world", $session, "abc");
        $at1->get_valid();
    }
}
