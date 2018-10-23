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
require_once(__DIR__ . '/fixtures/numbersfixtures.class.php');
require_once(__DIR__ . '/../stack/cas/cassession.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');

// Unit tests for {@link stack_cas_session}.
//
// @copyright  2012 The University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
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

    public function test_multiplication_option_complexno_i() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'i');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('a+b\cdot \mathrm{i}', $at1->get_display_key('p'));
        $this->assertEquals('a+b\cdot \mathrm{i}', $at1->get_display_key('q'));
        $this->assertEquals('a+b\cdot j', $at1->get_display_key('r'));
}

    public function test_multiplication_option_complexno_j() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'j');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('a+b\cdot \mathrm{j}', $at1->get_display_key('p'));
        $this->assertEquals('a+b\cdot i', $at1->get_display_key('q'));
        $this->assertEquals('a+b\cdot \mathrm{j}', $at1->get_display_key('r'));
    }

    public function test_multiplication_option_complexno_symi() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'symi');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('a+b\cdot \mathrm{i}', $at1->get_display_key('p'));
        $this->assertEquals('a+b\cdot i', $at1->get_display_key('q'));
        $this->assertEquals('a+b\cdot j', $at1->get_display_key('r'));
    }

    public function test_multiplication_option_complexno_symj() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'symj');

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('a+b\cdot \mathrm{j}', $at1->get_display_key('p'));
        $this->assertEquals('a+b\cdot i', $at1->get_display_key('q'));
        $this->assertEquals('a+b\cdot j', $at1->get_display_key('r'));
    }

    public function test_multiplication_option_dot() {

        $cs = array('a:x*y', 'b:x*y*z', 'c:x*(y*z)', 'd:(x*y)*z');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'dot');
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\cdot y', $at1->get_display_key('a'));
        $this->assertEquals('x\cdot y\cdot z', $at1->get_display_key('b'));
        $this->assertEquals('x\cdot \left(y\cdot z\right)', $at1->get_display_key('c'));
        // Notice the associativity of Maxima suppresses the extra explicit brackets here.
        $this->assertEquals('x\cdot y\cdot z', $at1->get_display_key('d'));
    }

    public function test_multiplication_option_none() {

        $cs = array('a:x*y', 'b:x*y*z', 'c:x*(y*z)', 'd:(x*y)*z');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\,y', $at1->get_display_key('a'));
        $this->assertEquals('x\,y\,z', $at1->get_display_key('b'));
        $this->assertEquals('x\,\left(y\,z\right)', $at1->get_display_key('c'));
        // Notice the associativity of Maxima suppresses the extra explicit brackets here.
        $this->assertEquals('x\,y\,z', $at1->get_display_key('d'));
    }

    public function test_multiplication_option_cross() {

        $cs = array('a:x*y', 'b:x*y*z', 'c:x*(y*z)', 'd:(x*y)*z');
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'cross');
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('x\times y', $at1->get_display_key('a'));
        $this->assertEquals('x\times y\times z', $at1->get_display_key('b'));
        $this->assertEquals('x\times \left(y\times z\right)', $at1->get_display_key('c'));
        // Notice the associativity of Maxima suppresses the extra explicit brackets here.
        $this->assertEquals('x\times y\times z', $at1->get_display_key('d'));
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
        // Notice the subtle difference in p4 & p5.
        // Where extra brackets are put in they should stay.
        foreach ($cs as $s) {
            $s1[] = new stack_cas_casstring($s);
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertEquals('y^3-2\\cdot y^2-8\\cdot y', $at1->get_display_key('p1'));
        $this->assertEquals('y^2-2\\cdot y-8', $at1->get_display_key('p2'));
        $this->assertEquals('y^2-2\\cdot y-0.5', $at1->get_display_key('p3'));
        // Since we introduced a +- operator, changes from Maxima's x-3+y.
        $this->assertEquals('{x \pm 3}+y', $at1->get_display_key('p4'));
        $this->assertEquals('x+\\left(-5+y\\right)', $at1->get_display_key('p5'));
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

    public function test_subscript_disp() {
        // Fails with actual display output like '{\it pi_{025}}'.
        $this->skip_if_old_maxima('5.23.2');

        $cs = array('a:pi_25', 'b:1+x_3', 'c:f(x):=x^3', 'd:gamma_7^3', 'a2:pi_4^5');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals($at1->get_value_key('a'), 'pi_25');
        $this->assertEquals($at1->get_display_key('a'), '{\pi}_{25}');

        $this->assertEquals($at1->get_value_key('b'), '1+x_3');
        $this->assertEquals($at1->get_display_key('b'), '1+{x}_{3}');

        $this->assertEquals($at1->get_value_key('c'), 'f(x):=x^3');
        $this->assertEquals($at1->get_display_key('c'), 'f(x):=x^3');

        $this->assertEquals($at1->get_value_key('d'), 'gamma_7^3');
        $this->assertEquals($at1->get_display_key('d'), '{\gamma}_{7}^3');

        $this->assertEquals($at1->get_value_key('a2'), 'pi_4^5');
        $this->assertEquals($at1->get_display_key('a2'), '{\pi}_{4}^5');
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


    public function test_disp_control_structures() {

        $csl = array('p:if a>b then setelmx(0,m[k],m[j],A)',
                'addto1(ex):=thru ex do x:0.5*(x+5.0/x)',
                'addto2(ex):=for a from -3 step 7 thru ex do a^2',
                'addto3(ex):=for i from 2 while ex <= 10 do s:s+i',
                'addto4(ex):=block([l],l:ex,for f in [log,rho,atan] do l:append(l,[f]),l)',
                'l:addto4([sin,cos])');

        foreach ($csl as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('', $at1->get_errors(false));

        $this->assertEquals('if a > b then setelmx(0,m[k],m[j],A)', $at1->get_value_key('p'));
        $this->assertEquals('\mathbf{if}\;a > b\;\mathbf{then}\;{\it setelmx}\left(0 , m_{k} , m_{j} , A\right)',
                $at1->get_display_key('p'));

        // Confirm these expressions are unchanged by the CAS.
        $atsession = $at1->get_session();
        for ($i = 1; $i <= 4; $i++) {
            $cs = $atsession[$i];
            $this->assertEquals($csl[$i], $cs->get_value());
        }

        $this->assertEquals('[sin,cos,log,rho,atan]', $at1->get_value_key('l'));
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

        // @codingStandardsIgnoreStart
        // Warning to developers.   The behaviour of rat is not stable accross versions of Maxima.
        // In Maxima 5.25.1: rat(sqrt(27)) gives sqrt(3)^3.
        // In Maxima 5.36.1: rat(sqrt(27)) gives (3^(1/2)^3).
        // In Maxima 5.37.1: rat(sqrt(27)) gives sqrt(3)^3.
        // @codingStandardsIgnoreEnd
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

    public function test_rand_selection_err_1() {
        $cs = array('a:rand_selection(1,1)');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[]', $at1->get_value_key('a'));
        $this->assertEquals('rand_selection error: first argument must be a list.', $at1->get_errors_key('a'));
    }

    public function test_rand_selection_err_2() {
        $cs = array('a:rand_selection([a,b,c,d], 7)');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[]', $at1->get_value_key('a'));
        $this->assertEquals('rand_selection error: insuffient elements in the list.', $at1->get_errors_key('a'));
    }

    public function test_rand_selection() {
        $cs = array('a:rand_selection([a,b,c,d], 4)', 'b:sort(a)');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[a,b,c,d]', $at1->get_value_key('b'));
    }

    public function test_trivial_rand_range() {
        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();
        $cmds = array();

        $cases[] = array('rand_zero(0)', '0', '0');
        $cases[] = array('rand_range(5,5)', '5', '5');
        $cases[] = array('rand_range(6,6,5)', '6', '6');

        $i = 0;
        foreach ($cases as $case) {
            $cmds[$i] = 'd'.$i.':' . $case[0];
            $i++;
        }

        $options = new stack_options();
        $kv = new stack_cas_keyval(implode(';', $cmds), $options, 0, 't');
        $s = $kv->get_session(); // This does a validation on the side.

        $s->instantiate();

        $i = 0;
        foreach ($cases as $case) {
            $this->assertEquals($case[1], $s->get_value_key('d'.$i));
            $this->assertEquals($case[2], $s->get_display_key('d'.$i));
            $i++;
        }
    }

    public function test_greek_lower() {
        // The case gamma is separated out below, so we can skip it on old Maxima where it is a known fail.
        $cs = array('greek1:[alpha,beta,delta,epsilon]',
                    'greek2:[zeta,eta,theta,iota,kappa]',
                    'greek3:[lambda,mu,nu,xi,omicron,pi,rho]',
                    'greek4:[sigma,tau,upsilon,phi,psi,chi,omega]');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[alpha,beta,delta,epsilon]', $at1->get_value_key('greek1'));
        $this->assertEquals('\left[ \alpha , \beta , \delta , \varepsilon \right]',
                $at1->get_display_key('greek1'));
        $this->assertEquals('[zeta,eta,theta,iota,kappa]', $at1->get_value_key('greek2'));
        $this->assertEquals('\left[ \zeta , \eta , \theta , \iota , \kappa \right]',
                $at1->get_display_key('greek2'));
        // Note here that pi is returned as the constant %pi.
        $this->assertEquals('[lambda,mu,nu,xi,omicron,%pi,rho]', $at1->get_value_key('greek3'));
        $this->assertEquals('\left[ \lambda , \mu , \nu , \xi ,  o , \pi , \rho \right]',
                $at1->get_display_key('greek3'));
        $this->assertEquals('[sigma,tau,upsilon,phi,psi,chi,omega]', $at1->get_value_key('greek4'));
        $this->assertEquals('\left[ \sigma , \tau , \upsilon , \varphi , \psi , \chi , \omega \right]',
                $at1->get_display_key('greek4'));
    }

    public function test_greek_lower_gamma() {
        // In old maxima, you get '\Gamma' for the display output.
        $this->skip_if_old_maxima('5.23.2');
        $cs = new stack_cas_casstring('greek1:gamma');
        $cs->get_valid('s');
        $at1 = new stack_cas_session(array($cs), null, 0);
        $at1->instantiate();
        $this->assertEquals('gamma', $at1->get_value_key('greek1'));
        $this->assertEquals('\gamma', $at1->get_display_key('greek1'));
    }

    public function test_greek_upper() {
        $cs = array('greek1:[Alpha,Beta,Gamma,Delta,Epsilon]',
                    'greek2:[Zeta,Eta,Theta,Iota,Kappa]',
                    'greek3:[Lambda,Mu,Nu,Xi,Omicron,Pi,Rho]',
                    'greek4:[Sigma,Tau,Upsilon,Phi,Chi,Psi,Omega]',
                    'v:round(float(Pi))');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('3', $at1->get_value_key('v'));
        $this->assertEquals('[Alpha,Beta,Gamma,Delta,Epsilon]', $at1->get_value_key('greek1'));
        $this->assertEquals('\left[ {\rm A} , {\rm B} , \Gamma , \Delta , {\rm E} \right]',
                $at1->get_display_key('greek1'));
        $this->assertEquals('[Zeta,Eta,Theta,Iota,Kappa]', $at1->get_value_key('greek2'));
        $this->assertEquals('\left[ {\rm Z} , {\rm H} , \Theta , {\rm I} , {\rm K} \right]',
                $at1->get_display_key('greek2'));
        // Note here that pi is returned as the constant %pi.
        $this->assertEquals('[Lambda,Mu,Nu,Xi,Omicron,%pi,Rho]', $at1->get_value_key('greek3'));
        $this->assertEquals('\left[ \Lambda , {\rm M} , {\rm N} , \Xi , {\rm O} , \pi , {\rm P} \right]',
                $at1->get_display_key('greek3'));
        $this->assertEquals('[Sigma,Tau,Upsilon,Phi,Chi,Psi,Omega]', $at1->get_value_key('greek4'));
        $this->assertEquals('\left[ \Sigma , {\rm T} , \Upsilon , \Phi , {\rm X} , \Psi , \Omega \right]',
                $at1->get_display_key('greek4'));
    }

    public function test_taylor_cos_simp() {
        $cs = array('c1:taylor(cos(x),x,0,1)',
                    'c3:taylor(cos(x),x,0,3)');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', true);

        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        // For some reason Maxima's taylor function doesn't always put \cdots at the end.
        $this->assertEquals('+1', $at1->get_value_key('c1'));
        $this->assertEquals('+1+\cdots', $at1->get_display_key('c1'));
        $this->assertEquals('1-x^2/2', $at1->get_value_key('c3'));
        $this->assertEquals('1-\frac{x^2}{2}+\cdots', $at1->get_display_key('c3'));
    }

    public function test_taylor_cos_nosimp() {
        $cs = array('c1:taylor(cos(x),x,0,1)',
                    'c3:taylor(cos(x),x,0,3)');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', true);

        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        // For some reason Maxima's taylor function doesn't always put \cdots at the end.
        $this->assertEquals('+1', $at1->get_value_key('c1'));
        $this->assertEquals('+1+\cdots', $at1->get_display_key('c1'));
        $this->assertEquals('1-x^2/2', $at1->get_value_key('c3'));
        $this->assertEquals('1-\frac{x^2}{2}+\cdots', $at1->get_display_key('c3'));
    }

    public function test_lambda() {
        $cs = array('l1:lambda([ex], ex^3)',
                    'l2:[1,2,3]',
                    'l3:maplist(l1, l2)'
        );
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }
        $at1 = new stack_cas_session($s1, null, 0);
        $at1->instantiate();
        // For some reason Maxima's taylor function doesn't always put \cdots at the end.
        $this->assertEquals('lambda([ex],ex^3)', $at1->get_value_key('l1'));
        $this->assertEquals('[1,8,27]', $at1->get_value_key('l3'));
    }

    public function test_sets_simp() {
        $cs = array('c1:{}',
            'c2:{b,a,c}');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('{}', $at1->get_value_key('c1'));
        $this->assertEquals('\left \{ \right \}', $at1->get_display_key('c1'));
        $this->assertEquals('{a,b,c}', $at1->get_value_key('c2'));
        $this->assertEquals('\left \{a , b , c \right \}', $at1->get_display_key('c2'));
    }

    public function test_sets_simp_false() {
        $cs = array('c1:{}',
            'c2:{b,a,c}');
        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('{}', $at1->get_value_key('c1'));
        $this->assertEquals('\left \{ \right \}', $at1->get_display_key('c1'));
        $this->assertEquals('{b,a,c}', $at1->get_value_key('c2'));
        $this->assertEquals('\left \{b , a , c \right \}', $at1->get_display_key('c2'));
    }

    public function test_numerical_rounding() {

        $tests = stack_numbers_test_data::get_raw_test_data();
        $s1 = array();
        foreach ($tests as $key => $test) {
            $cs = new stack_cas_casstring('dispdp('.$test[0].', '.$test[3] .')');
            $cs->get_valid('t');
            $cs->set_key('p'.$key);
            $s1[$key] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $this->assertTrue($at1->get_valid());
        $at1->instantiate();
        foreach ($tests as $key => $test) {
            $this->assertEquals($at1->get_value_key('p'.$key, true), $test[5]);
        }

    }

    public function test_dispdp() {
        // @codingStandardsIgnoreStart

        // Tests in the following form.
        // 0. Input string.
        // 1. Number of decimal places.
        // 2. Displayed form in LaTeX.
        // 3. Value form after rounding.
        // E.g. dispdp(3.14159,2) -> displaydp(3.14,2).

        // @codingStandardsIgnoreEnd

        $tests = array(
                    array('3.14159', '2', '3.14', 'displaydp(3.14,2)'),
                    array('100', '1', '100.0', 'displaydp(100.0,1)'),
                    array('100', '2', '100.00', 'displaydp(100.0,2)'),
                    array('100', '3', '100.000', 'displaydp(100.0,3)'),
                    array('100', '4', '100.0000', 'displaydp(100.0,4)'),
                    array('100', '5', '100.00000', 'displaydp(100.0,5)'),
                    array('0.99', '1', '1.0', 'displaydp(1.0,1)'),
        );

        foreach ($tests as $key => $c) {
            $s = "p{$key}:dispdp({$c[0]},{$c[1]})";
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        foreach ($tests as $key => $c) {
            $sk = "p{$key}";
            $this->assertEquals($c[2], $at1->get_display_key($sk));
            // Test the difference between value and dispvalue.
            $this->assertEquals($c[2], $at1->get_value_key($sk, true));
            $this->assertEquals($c[3], $at1->get_value_key($sk, false));
            $this->assertEquals($c[3], $at1->get_value_key($sk));
        }
    }

    public function test_dispdp_systematic() {
        $cs = new stack_cas_casstring("L:makelist(dispdp(10^-1+10^-k,k+1),k,12,20)");
        $cs->get_valid('t');
        $s1[] = $cs;

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        // The purpose of this test is to ilustrate how numerical precision runs out.
        // This is currently in the 16th decimal place, where we loose the 10^-k from the displayed output.
        $this->assertEquals('\left[ 0.1000000000010 , 0.10000000000010 , 0.100000000000010 , 0.1000000000000010 , '.
                '0.10000000000000010 , 0.100000000000000000 , 0.1000000000000000000 , 0.10000000000000000000 , '.
                '0.100000000000000000000 \right]', $at1->get_display_key('L'));
        // Even more worryingly, the "value" of the expression looses this at the 14th place.
        // This is because of the precision specified in the "string" routine of Maxima, which sends "values" from Maxima to PHP.
        $this->assertEquals('[displaydp(0.100000000001,13),displaydp(0.1,14),displaydp(0.1,15),displaydp(0.1,16),'.
                'displaydp(0.1,17),displaydp(0.1,18),displaydp(0.1,19),displaydp(0.1,20),displaydp(0.1,21)]',
                $at1->get_value_key('L', false));
        // The internal printf function is perfectly capable of printing more, if we use the "dispval" field.
        // This gives up at the same point as the displayed values above.
        $this->assertEquals('[0.1000000000010,0.10000000000010,0.100000000000010,0.1000000000000010,0.10000000000000010,'.
                '0.100000000000000000,0.1000000000000000000,0.10000000000000000000,0.100000000000000000000]',
                $at1->get_value_key('L', true));
    }

    public function test_dispdp_systematic_longer() {

        $cs = new stack_cas_casstring("fpprintprec:16");
        $cs->get_valid('t');
        $s1[] = $cs;
        $cs = new stack_cas_casstring("L:makelist(dispdp(10^-1+10^-k,k+1),k,12,20)");
        $cs->get_valid('t');
        $s1[] = $cs;

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('\left[ 0.1000000000010 , 0.10000000000010 , 0.100000000000010 , 0.1000000000000010 , '.
                '0.10000000000000010 , 0.100000000000000000 , 0.1000000000000000000 , 0.10000000000000000000 , '.
                '0.100000000000000000000 \right]', $at1->get_display_key('L'));
        // Note the difference, having increased fpprintprec:16 which is the maximum value permitted in Maxima.
        $this->assertEquals('[displaydp(0.100000000001,13),displaydp(0.1000000000001,14),displaydp(0.10000000000001,15),'.
                'displaydp(0.100000000000001,16),displaydp(0.1000000000000001,17),'.
                // We get more decimal places preserved here that the above test cases.
                'displaydp(0.1,18),displaydp(0.1,19),displaydp(0.1,20),displaydp(0.1,21)]',
                $at1->get_value_key('L', false));
        $this->assertEquals('[0.1000000000010,0.10000000000010,0.100000000000010,0.1000000000000010,0.10000000000000010,'.
                '0.100000000000000000,0.1000000000000000000,0.10000000000000000000,0.100000000000000000000]',
                $at1->get_value_key('L', true));
    }

    public function test_dispsf() {
        // @codingStandardsIgnoreStart

        // Tests in the following form.
        // 0. Input string.
        // 1. Number of significant figures.
        // 2. Displayed form.
        // 3. Value form after rounding.
        // E.g. dispsf(3.14159,2) -> displaydp(3.1,1).

        // @codingStandardsIgnoreEnd

        $tests = array(
                    array('3.14159', '2', '3.1', 'displaydp(3.1,1)'),
                    array('100', '1', '100', '100'),
                    array('100', '2', '100', '100'),
                    array('100', '3', '100', '100'),
                    array('100', '4', '100.0', 'displaydp(100,1)'),
                    array('100', '5', '100.00', 'displaydp(100,2)'),
                    array('100.00000000000001', '3', '100', '100'),
                    array('99', '1', '100', '100'),
                    array('0.99', '1', '1', '1'),
                    array('-0.99', '1', '-1', '-1'),
                    array('0.0000049', '1', '0.000005', 'displaydp(5.0e-6,6)'),
                    array('0', '1', '0', '0'),
                    array('0.0', '1', '0', '0'),
                    array('0', '2', '0.0', 'displaydp(0,1)'),
                    array('0', '3', '0.00', 'displaydp(0,2)'),
        );

        foreach ($tests as $key => $c) {
            $s = "p{$key}:dispsf({$c[0]},{$c[1]})";
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $sk = "p{$key}";
            $this->assertEquals($c[2], $at1->get_display_key($sk));
            $this->assertEquals($c[3], strtolower($at1->get_value_key($sk)));
        }
    }

    public function test_significantfigures_errors() {

        $tests = array(
                    array('significantfigures(%pi/3,3)', '1.05', ''),
                    array('significantfigures(%pi/blah,3)', '',
                        'sigfigsfun(x,n,d) requires a real number as a first argument.  Received:  %pi/blah'),
                    array('significantfigures(%pi/3,n)', '',
                        'sigfigsfun(x,n,d) requires an integer as a second argument. Received:  n'),
        );

        foreach ($tests as $key => $c) {
            $s = "p{$key}:$c[0]";
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $sk = "p{$key}";
            $this->assertEquals($c[1], $at1->get_value_key($sk));
            $this->assertEquals($c[2], $at1->get_errors_key($sk));
        }
    }

    public function test_pm_simp_false() {
        $cs = array('c1:a+-b',
            'c2:x=(-b +- sqrt(b^2-4*a*c))/(2*a)',
            'c3:b+-a^2',
            'c4:(b+-a)^2',
            'c5:+-a',
            'c6:+-a^2',
            'c7:+-sqrt(1-x)',
            'c8:(a+-b)^2',
            'c9:x=+-b',
            'c10:sin(x+-a)^2');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('a +- b', $at1->get_value_key('c1'));
        $this->assertEquals('{a \pm b}', $at1->get_display_key('c1'));
        $this->assertEquals('x = ((-b) +- sqrt(b^2-4*a*c))/(2*a)', $at1->get_value_key('c2'));
        $this->assertEquals('x=\frac{{-b \pm \sqrt{b^2-4\cdot a\cdot c}}}{2\cdot a}', $at1->get_display_key('c2'));
        $this->assertEquals('b +- a^2', $at1->get_value_key('c3'));
        $this->assertEquals('{b \pm a^2}', $at1->get_display_key('c3'));
        $this->assertEquals('(b +- a)^2', $at1->get_value_key('c4'));
        $this->assertEquals('\left({b \pm a}\right)^2', $at1->get_display_key('c4'));
        $this->assertEquals('"+-"(a)', $at1->get_value_key('c5'));
        $this->assertEquals('\pm a', $at1->get_display_key('c5'));
        $this->assertEquals('"+-"(a^2)', $at1->get_value_key('c6'));
        $this->assertEquals('\pm a^2', $at1->get_display_key('c6'));
        $this->assertEquals('"+-"(sqrt(1-x))', $at1->get_value_key('c7'));
        $this->assertEquals('\pm \sqrt{1-x}', $at1->get_display_key('c7'));
        $this->assertEquals('(a +- b)^2', $at1->get_value_key('c8'));
        $this->assertEquals('\left({a \pm b}\right)^2', $at1->get_display_key('c8'));
        $this->assertEquals('x = "+-"(b)', $at1->get_value_key('c9'));
        $this->assertEquals('x= \pm b', $at1->get_display_key('c9'));
        $this->assertEquals('sin(x +- a)^2', $at1->get_value_key('c10'));
        $this->assertEquals('\sin ^2\left({x \pm a}\right)', $at1->get_display_key('c10'));
    }

    public function test_pm_simp_true() {
        $cs = array('c1:a+-b',
            'c2:x=(-b +- sqrt(b^2-4*a*c))/(2*a)',
            'c3:b+-a^2',
            'c4:(b+-a)^2',
            'c5:+-a',
            'c6:+-a^2',
            'c7:+-sqrt(1-x)',
            'c8:(a+-b)^2',
            'c9:x=+-b',
            'c10:sin(x+-a)^2');

        foreach ($cs as $s) {
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('s');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', true);

        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('a +- b', $at1->get_value_key('c1'));
        $this->assertEquals('{a \pm b}', $at1->get_display_key('c1'));
        $this->assertEquals('x = ((-b) +- sqrt(b^2-4*a*c))/(2*a)', $at1->get_value_key('c2'));
        $this->assertEquals('x=\frac{{-b \pm \sqrt{b^2-4\cdot a\cdot c}}}{2\cdot a}', $at1->get_display_key('c2'));
        $this->assertEquals('b +- a^2', $at1->get_value_key('c3'));
        $this->assertEquals('{b \pm a^2}', $at1->get_display_key('c3'));
        $this->assertEquals('(b +- a)^2', $at1->get_value_key('c4'));
        $this->assertEquals('\left({b \pm a}\right)^2', $at1->get_display_key('c4'));
        $this->assertEquals('"+-"(a)', $at1->get_value_key('c5'));
        $this->assertEquals('\pm a', $at1->get_display_key('c5'));
        $this->assertEquals('"+-"(a^2)', $at1->get_value_key('c6'));
        $this->assertEquals('\pm a^2', $at1->get_display_key('c6'));
        $this->assertEquals('"+-"(sqrt(1-x))', $at1->get_value_key('c7'));
        $this->assertEquals('\pm \sqrt{1-x}', $at1->get_display_key('c7'));
        $this->assertEquals('(a +- b)^2', $at1->get_value_key('c8'));
        $this->assertEquals('\left({a \pm b}\right)^2', $at1->get_display_key('c8'));
        $this->assertEquals('x = "+-"(b)', $at1->get_value_key('c9'));
        $this->assertEquals('x= \pm b', $at1->get_display_key('c9'));
        $this->assertEquals('sin(x +- a)^2', $at1->get_value_key('c10'));
        $this->assertEquals('\sin ^2\left({x \pm a}\right)', $at1->get_display_key('c10'));
    }

    public function test_sf() {
        // @codingStandardsIgnoreStart

        // Tests in the following form.
        // 0. Input string.
        // 1. Number of significant figures.
        // 2. Displayed form.
        // E.g. significantfigures(3.14159,2) -> 3.1.

        // @codingStandardsIgnoreEnd

        $tests = array(
                    array('lg(19)', '4', '1.279'),
                    array('pi', '4', '3.142'),
                    array('sqrt(27)', '8', '5.1961524'),
                    array('-5.985', '3', '-5.99'),
        );

        foreach ($tests as $key => $c) {
            $s = "p{$key}:significantfigures({$c[0]},{$c[1]})";
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $sk = "p{$key}";
            $this->assertEquals($c[2], $at1->get_display_key($sk));
        }
    }

    public function test_scientific_notation() {
        // @codingStandardsIgnoreStart

        // Tests in the following form.
        // 0. Input string.
        // 1. Number of significant figures.
        // 2. Displayed form.
        // E.g. scientific_notation(314.159,2) -> 3.1\times 10^2.
        // 3. Dispvalue form, that is how it should be typed in.

        // @codingStandardsIgnoreEnd

        $tests = array(
            array('2.998e8', '2', '3.00 \times 10^{8}', '3.00E8'),
            array('-2.998e8', '2', '-3.00 \times 10^{8}', '-3.00E8'),
            array('6.626e-34', '2', '6.63 \times 10^{-34}', '6.63E-34'),
            array('-6.626e-34', '2', '-6.63 \times 10^{-34}', '-6.63E-34'),
            array('6.022e23', '2', '6.02 \times 10^{23}', '6.02E23'),
            array('5.985e30', '2', '5.99 \times 10^{30}', '5.99E30'),
            array('-5.985e30', '2', '-5.99 \times 10^{30}', '-5.99E30'),
            array('1.6726e-27', '2', '1.67 \times 10^{-27}', '1.67E-27'),
            array('1e5', '2', '1.00 \times 10^{5}', '1.00E5'),
            array('1.9e5', '2', '1.90 \times 10^{5}', '1.90E5'),
            array('1.0e9', '2', '1.00 \times 10^{9}', '1.00E9'),
            array('100000', '2', '1.00 \times 10^{5}', '1.00E5'),
            array('110000', '2', '1.10 \times 10^{5}', '1.10E5'),
            array('54e3', '2', '5.40 \times 10^{4}', '5.40E4'),
            array('0.00000000000067452', '2', '6.75 \times 10^{-13}', '6.75E-13'),
            array('-0.00000000000067452', '2', '-6.75 \times 10^{-13}', '-6.75E-13'),
            array('-0.0000000000006', '2', '-6.00 \times 10^{-13}', '-6.00E-13'),
            array('0.0000000000000000000005555', '2', '5.56 \times 10^{-22}', '5.56E-22'),
            array('0.00000000000000000000055', '2', '5.50 \times 10^{-22}', '5.50E-22'),
            array('-0.0000000000000000000005555', '2', '-5.56 \times 10^{-22}', '-5.56E-22'),
            array('67260000000000000000000000', '2', '6.73 \times 10^{25}', '6.73E25'),
            array('67000000000000000000000000', '2', '6.70 \times 10^{25}', '6.70E25'),
            array('-67260000000000000000000000', '2', '-6.73 \times 10^{25}', '-6.73E25'),
            array('-67000000000000000000000000', '2', '-6.70 \times 10^{25}', '-6.70E25'),
            array('0.001', '2', '1.00 \times 10^{-3}', '1.00E-3'),
            array('-0.001', '2', '-1.00 \times 10^{-3}', '-1.00E-3'),
            array('10', '2', '1.00 \times 10^{1}', '1.00E1'),
            array('2', '0', '2 \times 10^{0}', '2E0'),
            array('300', '0', '3 \times 10^{2}', '3E2'),
            array('4321.768', '3', '4.322 \times 10^{3}', '4.322E3'),
            array('-53000', '2', '-5.30 \times 10^{4}', '-5.30E4'),
            array('6720000000', '3', '6.720 \times 10^{9}', '6.720E9'),
            array('6.0221409e23', '4', '6.0221 \times 10^{23}', '6.0221E23'),
            array('1.6022e-19', '4', '1.6022 \times 10^{-19}', '1.6022E-19'),
            array('9000', '1', '9.0 \times 10^{3}', '9.0E3'),
            array('9000', '0', '9 \times 10^{3}', '9E3'),
            array('1.55E8', '2', '1.55 \times 10^{8}', '1.55E8'),
            array('-0.01', '1', '-1.0 \times 10^{-2}', '-1.0E-2'),
            array('-0.00000001', '3', '-1.000 \times 10^{-8}', '-1.000E-8'),
            array('-0.00000001', '1', '-1.0 \times 10^{-8}', '-1.0E-8'),
            array('-0.00000001', '0', '-1 \times 10^{-8}', '-1E-8'),
            array('-1000', '2', '-1.00 \times 10^{3}', '-1.00E3'),
            array('31415.927', '3', '3.142 \times 10^{4}', '3.142E4'),
            array('-31415.927', '3', '-3.142 \times 10^{4}', '-3.142E4'),
            array('155.5', '2', '1.56 \times 10^{2}', '1.56E2'),
            array('15.55', '2', '1.56 \times 10^{1}', '1.56E1'),
            array('777.7', '2', '7.78 \times 10^{2}', '7.78E2'),
            array('775.5', '2', '7.76 \times 10^{2}', '7.76E2'),
            array('775.55', '2', '7.76 \times 10^{2}', '7.76E2'),
            array('0.5555', '2', '5.56 \times 10^{-1}', '5.56E-1'),
            array('0.05555', '2', '5.56 \times 10^{-2}', '5.56E-2'),
            array('cos(23*pi/180)', '3', '9.205 \times 10^{-1}', '9.205E-1'),
            // Edge case.  Want these ones to be 1*10^3, not 10.0*10^2.
            array('1000', '2', '1.00 \times 10^{3}', '1.00E3'),
            // If we don't supply a number of decimal places, then we return a value form.
            // This is entered as scientific_notation(x).
            // This is displayed normally (without a \times) and always returns a *float*.
            array('9000', '', '9.0\cdot 10^3', '9.0*10^3'),
            array('1000', '', '1.0\cdot 10^3', '1.0*10^3'),
            array('-1000', '', '-1.0\cdot 10^3', '-1.0*10^3'),
            array('1e50', '', '1.0\cdot 10^{50}', '1.0*10^50'),
            // In some versions of Maxima this comes out as -\frac{1.0}{10^8} with simp:true.
            // Adding in compile(scientific_notation)$ after the function definition cures this,
            // but breaks some versions of Maxima.
            // Maxima 5.38.1 gives -1.0*10^-8, which is what we actually want.
            array('-0.00000001', '', '-1.0\cdot 10^ {- 8 }', '-1.0*10^-8'),
            array('-0.000000001', '', '-1.0\cdot 10^ {- 9 }', '-1.0*10^-9'),
            array('-0.000000000001', '', '-1.0\cdot 10^ {- 12 }', '-1.0*10^-12'),
        );

        foreach ($tests as $key => $c) {
            $s = "p{$key}:scientific_notation({$c[0]},{$c[1]})";
            if ($c[1] == '') {
                $s = "p{$key}:scientific_notation({$c[0]})";
            }
            $cs = new stack_cas_casstring($s);
            $cs->get_valid('t');
            $s1[] = $cs;
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $sk = "p{$key}";
            $this->assertEquals($c[2], $at1->get_display_key($sk));
            $this->assertEquals($c[3], $at1->get_value_key($sk, true));
        }
    }

    public function test_odd_logic_eval() {
        // First we have a session. That comes from keyval like question-vars.
        $kv = new stack_cas_keyval('a:true;b:is(1>2);c:false');
        $s = $kv->get_session(); // This does a validation on the side.

        // The form '[[ if test="b" ]]ok4[[elif test="c"]]Ok4[[ else ]]OK4[[/ if ]]' is the castext.
        // Then we start to add some new variables into it as the castext is evaluated.
        // First the conditions that have been extracted from a if-elif-else construct during the "compilation"-step.
        $s->add_vars(array(new stack_cas_casstring('stackparsecond8:b')));
        $s->add_vars(array(new stack_cas_casstring('stackparsecond9:not (stackparsecond8) and (c)')));
        $s->add_vars(array(new stack_cas_casstring('stackparsecond10:not (stackparsecond9)')));
        // After that the if-blocks will map those definitions to their own conditions.
        $s->add_vars(array(new stack_cas_casstring('caschat0:stackparsecond8')));
        $s->add_vars(array(new stack_cas_casstring('caschat1:stackparsecond9')));
        $s->add_vars(array(new stack_cas_casstring('caschat2:stackparsecond10')));

        // Now lets instantiate.
        $s->instantiate();

        $this->assertEquals('false', $s->get_value_key('caschat0'));
        $this->assertEquals('false', $s->get_value_key('caschat1'));
        $this->assertEquals('true', $s->get_value_key('caschat2'));
    }

    public function test_logic_nouns_sort() {

        $cmds = array('p0:x=1 or x=2',
            stack_utils::logic_nouns_sort('p1:x=1 or x=2', 'add'),
            'p2:noun_logic_remove(p1)', 'p3:ev(p2)');
        $options = new stack_options();
        $kv = new stack_cas_keyval(implode(';', $cmds), $options, 0, 't');
        $s = $kv->get_session(); // This does a validation on the side.

        $s->instantiate();

        $this->assertEquals('false', $s->get_value_key('p0'));
        $this->assertEquals('x = 1 nounor x = 2', $s->get_value_key('p1'));
        // Note, that noun_logic_remove(p1) does not give an extra evaluation.
        $this->assertEquals('x = 1 or x = 2', $s->get_value_key('p2'));
        // However, the display function does force an extra evaluation!
        $this->assertEquals('\mathbf{false}', $s->get_display_key('p2'));
        $this->assertEquals('false', $s->get_value_key('p3'));
    }

    public function test_natural_domain() {

        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();
        $cmds = array();

        $cases[] = array('x', 'all', '\mathbb{R}');
        $cases[] = array('1/(x^2+1)', 'all', '\mathbb{R}');
        $cases[] = array('1/x', 'realset(x,%union(oo(0,inf),oo(-inf,0)))', '{x \not\in {\left \{0 \right \}}}');
        $cases[] = array('1+1/x^2+1/(x-1)', 'realset(x,%union(oo(0,1),oo(1,inf),oo(-inf,0)))',
                '{x \not\in {\left \{0 , 1 \right \}}}');
        $cases[] = array('1+1/x^2+1/(x-1)+3/(x-2)', 'realset(x,%union(oo(0,1),oo(1,2),oo(2,inf),oo(-inf,0)))',
                '{x \not\in {\left \{0 , 1 , 2 \right \}}}');
        $cases[] = array('log(x)', 'realset(x,oo(0,inf))', '{x \in {\left( 0,\, \infty \right)}}');
        $i = 0;
        foreach ($cases as $case) {
            $cmds[$i] = 'd'.$i.':natural_domain('.$case[0].')';
            $i++;
        }

        $options = new stack_options();
        $kv = new stack_cas_keyval(implode(';', $cmds), $options, 0, 't');
        $s = $kv->get_session(); // This does a validation on the side.

        $s->instantiate();

        $i = 0;
        foreach ($cases as $case) {
            $this->assertEquals($case[1], $s->get_value_key('d'.$i));
            $this->assertEquals($case[2], $s->get_display_key('d'.$i));
            $i++;
        }
    }

    public function test_union_tex() {

        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();
        $cmds = array();

        $cases[] = array('%union(a,b,c)', 'a \cup b \cup c');
        $cases[] = array('%union(oo(1,2),oo(3,4),oo(4,5))',
            '\left( 1,\, 2\right) \cup \left( 3,\, 4\right) \cup \left( 4,\, 5\right)');
        $cases[] = array('%union(a,b+1,d)', 'a \cup \left(b+1\right) \cup d');

        $i = 0;
        foreach ($cases as $case) {
            $cmds[$i] = 'd'.$i.':'.$case[0];
            $i++;
        }

        $options = new stack_options();
        $kv = new stack_cas_keyval(implode(';', $cmds), $options, 0, 't');
        $s = $kv->get_session(); // This does a validation on the side.

        $s->instantiate();

        $i = 0;
        foreach ($cases as $case) {
            $this->assertEquals($case[0], $s->get_value_key('d'.$i));
            $this->assertEquals($case[1], $s->get_display_key('d'.$i));
            $i++;
        }
    }
}
