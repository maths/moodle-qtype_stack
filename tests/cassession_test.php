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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession.class.php');


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

        $cs = array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
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

        $cs = array('a:x*y', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'dot');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\cdot y', $at1->get_display_key('a'));

    }

    public function test_multiplication_option_none() {

        $cs = array('a:x*y', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\,y', $at1->get_display_key('a'));

    }

    public function test_multiplication_option_cross() {

        $cs = array('a:x*y', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'cross');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\times y', $at1->get_display_key('a'));

    }

    public function test_acos_option_cosmone() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('inversetrig', 'cos-1');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('\cos^{-1}\left( x \right)', $at1->get_display_key('a'));
        $this->assertEquals('\sin^{-1}\left( x \right)', $at1->get_display_key('b'));
        $this->assertEquals('{\rm sinh}^{-1}\left( x \right)', $at1->get_display_key('c'));
    }

    public function test_acos_option_acos() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('inversetrig', 'acos');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('{\rm acos}\left( x \right)', $at1->get_display_key('a'));
        $this->assertEquals('{\rm asin}\left( x \right)', $at1->get_display_key('b'));
        $this->assertEquals('{\rm asinh}\left( x \right)', $at1->get_display_key('c'));
    }

    public function test_acos_option_arccos() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('inversetrig', 'arccos');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('\arccos \left( x \right)', $at1->get_display_key('a'));
        $this->assertEquals('\arcsin \left( x \right)', $at1->get_display_key('b'));
        $this->assertEquals('{\rm arcsinh}\left( x \right)', $at1->get_display_key('c'));
    }

    public function test_keyval_representation_1() {

        $cs = array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
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

        $cs = array('a:(-1)^2');
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

    public function test_single_char_vars_teacher() {

        $testcases = array('ab' => 'a*b',
            'abc' => 'a*b*c',
            'ab*c+a+(b+cd)' => 'a*b*c+a+(b+c*d)',
            'sin(xy)' => 'sin(x*y)',
            'xe^x' => '(x*%e)^x',
            'pix' => 'p*%i*x',
            '2pi+nk' => '2*%pi+n*k',
            '(ax+1)(ax-1)' => '(a*x+1)*(a*x-1)',
            'nx(1+2x)' => 'nx(1+2*x)' // Note, two letter function names are permitted.
        );

        $k = 0;
        $sessionvars = array();
        foreach ($testcases as $test => $result) {
            $cs = new stack_cas_casstring($test);
            $cs->get_valid('t', false, 2);
            $key = 'v'.$k;
            $cs->set_cas_validation_casstring($key, true, false, true, $result, 'checktype', '');
            $sessionvars[] = $cs;
            $k++;
            $this->assertTrue($cs->get_valid());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($sessionvars, $options, 0);
        $at1->instantiate();

        $k = 0;
        $sessionvars = array();
        foreach ($testcases as $test => $result) {
            $this->assertEquals($at1->get_value_key('v'.$k), $result);
            $k++;
        }

    }

    public function test_single_char_vars_student() {

        $testcases = array('ab' => 'a*b',
                'ab*c' => 'a*b*c',
                'ab*c+a+(b+cd)' => 'a*b*c+a+(b+c*d)',
                'sin(xy)' => 'sin(x*y)',
                'xe^x' => '(x*%e)^x',
                '2pi+nk' => '2*%pi+n*k',
                '(ax+1)(ax-1)' => '(a*x+1)*(a*x-1)',
                'nx(1+2x)' => 'nx(1+2*x)' // Note, two letter function names are permitted.
        );

        $k = 0;
        $sessionvars = array();
        foreach ($testcases as $test => $result) {
            $cs = new stack_cas_casstring($test);
            $cs->get_valid('s', false, 2);
            $key = 'v'.$k;
            $cs->set_cas_validation_casstring($key, true, false, true, $result, 'checktype', '');
            $sessionvars[] = $cs;
            $k++;
            $this->assertTrue($cs->get_valid());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($sessionvars, $options, 0);
        $at1->instantiate();

        $k = 0;
        $sessionvars = array();
        foreach ($testcases as $test => $result) {
            $this->assertEquals($at1->get_value_key('v'.$k), $result);
            $k++;
        }

    }

    public function test_string1() {

        $cs = array('s:"This is a string"');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('"This is a string"', $at1->get_value_key('s'));
    }

    public function test_qmchar() {

        $cs = array('s:5*?+6*?', 'A:matrix([?,1],[1,?])');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();

        $this->assertEquals('11*?', $at1->get_value_key('s'));
        $this->assertEquals('11\cdot \color{red}{?}', $at1->get_display_key('s'));

        $this->assertEquals('matrix([?,1],[1,?])', $at1->get_value_key('A'));
    }

    public function test_assignmatrixelements() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3', 'B:A');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();

        // Note we need to re-evaluate the matrix to get its actual value.
        // This is not a bug, but might produce unexpected behaviour within a session.
        // CAStext is fine, because we re-evaluate it anyway when we use it.
        $this->assertEquals('matrix([1,2],[1,1])', $at1->get_value_key('A'));
        $this->assertEquals('matrix([1,3],[1,1])', $at1->get_value_key('B'));
    }

    public function test_simplify_false() {

        $cs = array('a:2+3', 'b:ev(a,simp)');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
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
            $cs->get_valid('t');
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
            $cs->get_valid('t');
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

    public function test_numerical_precision() {

        $cs = array('a:1385715.257');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('1385715.257', $at1->get_value_key('a'));
    }

    public function test_rat() {

        $cs = array('a:ratsimp(sqrt(27))', 'b:rat(sqrt(27))', 'm:MAXIMA_VERSION_NUM');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('3^(3/2)', $at1->get_value_key('a'));

        // Warning to developers.   The behaviour of rat is not stable accross versions of Maxima.
        // In Maxima 5.25.1: rat(sqrt(27)) gives sqrt(3)^3.
        // In Maxima 5.36.1: rat(sqrt(27)) gives (3^(1/2)^3).
        // In Maxima 5.37.1: rat(sqrt(27)) gives sqrt(3)^3.
        $maximaversion = $at1->get_value_key('m');
        if ($maximaversion == '36.1') {
            // Developers should add other versions of Maxima here as needed.
            $this->assertEquals('(3^(1/2))^3', $at1->get_value_key('b'));
        } else {
            $this->assertEquals('sqrt(3)^3', $at1->get_value_key('b'));
        }
    }

    public function test_matrix_eigenvalues() {

        $cs = array('A:matrix([7,1,3],[5,-3,4],[5,3,-4])', 'E:first(eigenvalues(A))', 'dt:determinant(A)');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('matrixparens', '(');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('[1-sqrt(66),sqrt(66)+1,-2]', $at1->get_value_key('E'));
        $this->assertEquals('130', $at1->get_value_key('dt'));
        $this->assertEquals('\left(\begin{array}{ccc} 7 & 1 & 3 \\\\ 5 & -3 & 4 \\\\ 5 & 3 & -4 \end{array}\right)',
                $at1->get_display_key('A'));

    }

    public function test_ordergreat() {

        $cs = array('ordergreat(i,j,k)', 'p:matrix([-7],[2],[-3])', 'q:matrix([i],[j],[k])', 'v:dotproduct(p,q)');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        // There has been a subtle change to associativity in Maxima 5.37.0.
        $this->assertEquals('-7\cdot i+2\cdot j-3\cdot k', $at1->get_display_key('v'));
    }

    public function test_plot_constant_function() {

        $cs = array('a:0', 'p:plot(a*x,[x,-2,2],[y,-2,2])');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('0', $at1->get_value_key('a'));
        $this->assertTrue(is_numeric(strpos($at1->get_value_key('p'), 'STACK auto-generated plot of 0 with parameters')));
        $this->assertEquals('', trim($at1->get_errors_key('p')));
    }

    public function test_plot_fail() {

        $cs = array('a:0', 'p:plot(a*x/0,[x,-2,2],[y,-2,2])');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('0', $at1->get_value_key('a'));
        $this->assertRegExp('/Division by (zero|0)/', trim($at1->get_errors_key('p')));
        $this->assertFalse(strpos($at1->get_value_key('p'), 'STACK auto-generated plot of 0 with parameters'));
    }
}
