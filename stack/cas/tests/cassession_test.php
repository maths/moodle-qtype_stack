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
require_once(dirname(__FILE__) . '/../cassession.class.php');


/**
 * Unit tests for {@link stack_cas_session}.
 * @group qtype_stack
 */
class stack_cas_session_test extends qtype_stack_testcase {

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
        $this->assertEquals($val, $at1->get_valid());
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
        $this->assertEquals('x^2', $at1->get_display_key('a'));
        $this->assertEquals('\frac{1}{1+x^2}', $at1->get_display_key('b'));
        $this->assertEquals('e^{\mathrm{i}\cdot \pi}', $at1->get_display_key('c'));

    }

    public function test_multiplication_option_dot() {

        $cs=array('a:x*y', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'dot');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\cdot y', $at1->get_display_key('a'));

    }

    public function test_multiplication_option_none() {

        $cs=array('a:x*y', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\,y', $at1->get_display_key('a'));

    }

    public function test_multiplication_option_cross() {

        $cs=array('a:x*y', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'cross');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\times y', $at1->get_display_key('a'));

    }

    public function test_keyval_representation_1() {

        $cs=array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $at1 = new stack_cas_session($s1, null, 0);
        $this->assertEquals('a:x^2; b:1/(1+x^2); c:e^(i*pi);', $at1->get_keyval_representation());
        $this->assertEquals(array('a', 'b', 'c'), $at1->get_all_keys());

        $at1->prune_session(1);
        $this->assertEquals(array('a'), $at1->get_all_keys());
    }

    public function test_keyval_representation_2() {

        $cs=array('a:(-1)^2');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $at1 = new stack_cas_session($s1, null, 0);
        $this->assertEquals('a:(-1)^2;', $at1->get_keyval_representation());
        $at1->instantiate();
        $this->assertEquals('a:1;', $at1->get_keyval_representation());
    }

    public function test_get_display_unary_minus() {

        $cs = array('p1:y^3-2*y^2-8*y', 'p2:y^2-2*y-8', 'p3:y^2-2*y-0.5', 'p4:x+-3+y', 'p5:x+(-5+y)');
        /* Notice the subtle difference in p4 & p5 */
        /* Where extra brackets are put in they should stay... */
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('y^3-2\\cdot y^2-8\\cdot y', $at1->get_display_key('p1'));
        $this->assertEquals('y^2-2\\cdot y-8', $at1->get_display_key('p2'));
        $this->assertEquals('y^2-2\\cdot y-0.5', $at1->get_display_key('p3'));
        $this->assertEquals('x-3+y', $at1->get_display_key('p4'));
        $this->assertEquals('x+\\left(-5+y\\right)', $at1->get_display_key('p5'));
    }

    public function test_string1() {

        $cs=array('s:"This is a string"');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('"This is a string"', $at1->get_value_key('s'));
    }

    public function test_qmchar() {

        $cs=array('s:5*?+6*?', 'A:matrix([?,1],[1,?])');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();

        $this->assertEquals('11*?', $at1->get_value_key('s'));
        $this->assertEquals('11\cdot \color{red}{?}', $at1->get_display_key('s'));

        $this->assertEquals('matrix([?,1],[1,?])', $at1->get_value_key('A'));
    }

    public function test_simplify_false() {

        $cs=array('a:2+3', 'b:ev(a,simp)');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('2+3', $at1->get_value_key('a'));
        $this->assertEquals('5', $at1->get_value_key('b'));

    }

    public function test_redefine_variable() {

        // This example redefines the value of n.
        // It should return the last value.
        $cs = array('n:3', 'n:n+3', 'n:n^2');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('36', $at1->get_value_key('n'));
        $this->assertEquals('36', $at1->get_display_key('n'));
    }

    public function test_indirect_redefinition_of_varibale() {

        // This example uses a loop to change the values of elements of C.
        // However the loop returns "done", and the values of C are changed.
        $cs = array('A:matrix([5,2],[4,3])', 'B:matrix([4,5],[6,5])',
                'C:zeromatrix (first(matrix_size(A)), second(matrix_size(A)))');
        $cs[] = 'BT:transpose(B)';
        $cs[] = 'S:for a:1 thru first(matrix_size(A)) do for b:1 thru second(matrix_size(A)) do ' .
                'C[ev(a,simp),ev(b,simp)]:apply("+",zip_with("*",A[ev(a,simp)],BT[ev(b,simp)]))';
        $cs[] = 'D:ev(C,simp)';
        // We need this last assignment to re-evaluate C, and then we can grab the results.....
        $cs[] = 'C:C';

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->validate('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals(false, $at1->get_value_key('a'));
        $this->assertEquals('matrix([5,2],[4,3])', $at1->get_value_key('A'));
        $this->assertEquals('matrix([5*4+2*6,5*5+2*5],[4*4+3*6,4*5+3*5])', $at1->get_value_key('C'));
    }
}


/**
 * Unit tests for {@link stack_cas_session}.
 * @group qtype_stack
 */
class stack_cas_session_exception_test extends qtype_stack_testcase {

    public function test_exception_1() {
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_session("x=1", false, false);
    }

    public function test_exception_2() {
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_session(array(), null, false);
        $at1->get_valid();
    }

    public function test_exception_3() {
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_session(array(1, 2, 3), null, false);
    }

    public function test_exception_4() {
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_session(null, 123, false);
    }

    public function test_exception_5() {
        $pref = new stack_options();
        $this->setExpectedException('stack_exception');
        $at1 = new stack_cas_session(null, $pref, 'abc');
    }
}
