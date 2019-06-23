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
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

class stack_cas_session2_test extends qtype_stack_testcase {

    public function test_internal_config() {
        // This test checks if the version number returned by Maxima matches our internal config.
        $cs = array('m:MAXIMA_VERSION_NUM');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();

        $ast = $at1->get_by_key('m');
        $maximaversion = '5.' . $ast->get_value();
        $maximaconfig = get_config('qtype_stack', 'maximaversion');
        $this->assertEquals($maximaconfig, $maximaversion);
    }

    public function test_get_valid() {
        $strings = array('foo', 'bar', 'sqrt(4)');

        $casstrings = array();

        foreach ($strings as $string) {
            $casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_get_valid()',
                    new stack_cas_security());
        }

        $session = new stack_cas_session2($casstrings);

        $this->assertTrue($session->get_valid());
    }

    public function test_get_valid_false() {
        $strings = array('foo', 'bar', 'system(4)');

        $casstrings = array();

        foreach ($strings as $string) {
            $casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_get_valid_false()',
                    new stack_cas_security());
        }

        $session = new stack_cas_session2($casstrings);

        $this->assertFalse($session->get_valid());
    }

    public function test_instantiation_and_return_values() {
        $strings = array('1+2' => '3',
                         'sqrt(4)' => '2',
                         'diff(x^2,x)' => '2*x');

        $casstrings = array();

        foreach ($strings as $string => $result) {
            $casstrings[] = stack_ast_container::make_from_teacher_source($string,
                    'test_instantiation_and_return_values()', new stack_cas_security());
        }

        $session = new stack_cas_session2($casstrings);

        $this->assertTrue($session->get_valid());
        $this->assertFalse($session->is_instantiated());
        $session->instantiate();
        $this->assertTrue($session->is_instantiated());

        $i = 0;
        foreach ($strings as $string => $result) {
            $this->assertEquals($result, $casstrings[$i]->get_evaluated()->toString());
            $i = $i + 1;
        }
    }

    public function test_keys_or_not() {
        // Keys are optional in the new cassession, we can extract the values
        // if need be even if keys do not exist, and if you do an assignement
        // it wont be visible in the return values anyway.
        $strings = array('foo:1+2' => array('3', '3'),
                         '1+2' => array('3', '3'),
                         'bar:diff(x^2,x)' => array('2*x', '2\\cdot x'),
                         'diff(x^2,x)' => array('2*x', '2\\cdot x'));
        $casstrings = array();

        foreach ($strings as $string => $result) {
            $casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_keys_or_not()', new stack_cas_security());
        }

        $session = new stack_cas_session2($casstrings);

        $this->assertTrue($session->get_valid());
        $this->assertFalse($session->is_instantiated());
        $session->instantiate();
        $this->assertTrue($session->is_instantiated());

        $i = 0;
        foreach ($strings as $string => $result) {
            $this->assertEquals($result[0], $casstrings[$i]->get_evaluated()->toString());
            $this->assertEquals($result[1], $casstrings[$i]->get_latex());
            $i = $i + 1;
        }
    }

    public function test_error() {
        $simpon = stack_ast_container::make_from_teacher_source('simp:true', 'test_error()', new stack_cas_security());
        $divzero = stack_ast_container::make_from_teacher_source('1/0', 'test_error()', new stack_cas_security());
        $foo = stack_ast_container::make_from_teacher_source('sconcat("f","o","o")', 'test_error()', new stack_cas_security());

        $session = new stack_cas_session2([$simpon, $divzero, $foo]);

        $this->assertTrue($session->get_valid());
        $this->assertFalse($session->is_instantiated());
        $session->instantiate();
        $this->assertTrue($session->is_instantiated());

        $this->assertEquals('', $simpon->get_errors());
        $this->assertEquals('', $foo->get_errors());
        $this->assertEquals('"foo"', $foo->get_value());
        $this->assertTrue(count($divzero->get_errors(true)) > 0);
        $this->assertContains('Division by zero.', $divzero->get_errors(true));
    }

    public function test_feedback() {
        $simpoff = stack_ast_container::make_from_teacher_source('simp:false', 'test_answernote()', new stack_cas_security());
        $validation = stack_ast_container::make_from_teacher_source('stack_validate_typeless([2/4], true, true,"~a")',
                'test_answernote()', new stack_cas_security());

        $session = new stack_cas_session2([$simpoff, $validation]);
        $this->assertEquals('', $validation->get_answernote());

        $this->assertTrue($session->get_valid());
        $this->assertFalse($session->is_instantiated());
        $session->instantiate();
        $this->assertTrue($session->is_instantiated());

        // TODO: test the translated version...
        $this->assertContains('Lowest_Terms', $validation->get_feedback());
    }


    public function test_answertest_usage() {
        $qv = 'ta:diff(sin(x),x);';
        $qv = new stack_cas_keyval($qv, null, 123);

        $basesession = $qv->get_session();
        $security = new stack_cas_security();
        $security->set_forbiddenkeys($qv->get_variable_usage()['write']);
        // Skip the allowed/forbidden words thing but the input could play with them if need be.

        $input = '-cos(0-x)';
        $input = stack_ast_container::make_from_student_source($input, 'ans1:raw-input', $security);
        // First needs to have a key. As we probably are not adding that at the input side
        // we probably should do that there.
        $input->set_key('ans1');

        $basesession->add_statement($input);
        // We now have a session with the $input and the question variables.

        // If our PRT requires simplification.
        $simpon = stack_ast_container_silent::make_from_teacher_source('simp:true', 'PRT1:simpon', $security);

        // If we have feedbackvariables.
        $fv = '';
        $fv = new stack_cas_keyval($fv, null, 123);

        $prtsession = $fv->get_session();

        // Maybe set the simp before those?
        $prtsession->add_statement($simpon, false);

        // Add the shared stuff there naturelly before those...
        $basesession->prepend_to_session($prtsession);

        // Now then lets make some statements. No options now.
        $sans = 'ans1';
        $tans = 'ta';

        $sans = stack_ast_container::make_from_teacher_source($sans, 'node1:sans', $security);
        $tans = stack_ast_container::make_from_teacher_source($tans, 'node1:tans', $security);
        $sans->set_key('STACKSA');
        $tans->set_key('STACKTA');

        $result = stack_ast_container::make_from_teacher_source('ATAlgEquiv(STACKSA,STACKTA)', 'node1:test', $security);

        // Add those to the session.
        $prtsession->add_statement($sans);
        $prtsession->add_statement($tans);
        $prtsession->add_statement($result);

        // Instanttiate.
        $prtsession->instantiate();

        // Check parameters.
        $this->assertEquals('', $sans->get_errors());
        $this->assertEquals('', $tans->get_errors());
        $this->assertEquals('', $result->get_errors());
        $this->assertEquals('cos(x)', $tans->get_value());

        // Note that the value is available as an AST and there is already
        // an unpacking function to pick it apart.
        $this->assertEquals('[true,false,"",""]', $result->get_value());

    }

    public function test_get_display() {

        $cs = array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('x^2', $s1[0]->get_display());
        $this->assertEquals('\frac{1}{1+x^2}', $s1[1]->get_display());
        $this->assertEquals('e^{\mathrm{i}\cdot \pi}', $s1[2]->get_display());

    }

    public function test_multiplication_option_complexno_i() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'i');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $p = $at1->get_by_key('p');
        $this->assertEquals('a+b\cdot \mathrm{i}', $p->get_display());
        $q = $at1->get_by_key('q');
        $this->assertEquals('a+b\cdot \mathrm{i}', $q->get_display());
        $r = $at1->get_by_key('r');
        $this->assertEquals('a+b\cdot j', $r->get_display());
    }

    public function test_multiplication_option_complexno_j() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'j');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $p = $at1->get_by_key('p');
        $this->assertEquals('a+b\cdot \mathrm{j}', $p->get_display());
        $q = $at1->get_by_key('q');
        $this->assertEquals('a+b\cdot i', $q->get_display());
        $r = $at1->get_by_key('r');
        $this->assertEquals('a+b\cdot \mathrm{j}', $r->get_display());
    }

    public function test_multiplication_option_complexno_symi() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'symi');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $p = $at1->get_by_key('p');
        $this->assertEquals('a+b\cdot \mathrm{i}', $p->get_display());
        $q = $at1->get_by_key('q');
        $this->assertEquals('a+b\cdot i', $q->get_display());
        $r = $at1->get_by_key('r');
        $this->assertEquals('a+b\cdot j', $r->get_display());
    }

    public function test_multiplication_option_complexno_symj() {

        $cs = array('p:a+b*%i', 'q:a+b*i', 'r:a+b*j');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('complexno', 'symj');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $p = $at1->get_by_key('p');
        $this->assertEquals('a+b\cdot \mathrm{j}', $p->get_display());
        $q = $at1->get_by_key('q');
        $this->assertEquals('a+b\cdot i', $q->get_display());
        $r = $at1->get_by_key('r');
        $this->assertEquals('a+b\cdot j', $r->get_display());
    }

    public function test_multiplication_option_dot() {

        $cs = array('a:x*y', 'b:x*y*z', 'c:x*(y*z)', 'd:(x*y)*z');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'dot');
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('x\cdot y', $s1[0]->get_display());
        $this->assertEquals('x\cdot y\cdot z', $s1[1]->get_display());
        $this->assertEquals('x\cdot \left(y\cdot z\right)', $s1[2]->get_display());
        // Notice the associativity of Maxima suppresses the extra explicit brackets here.
        $this->assertEquals('x\cdot y\cdot z',$s1[3]->get_display());
    }

    public function test_multiplication_option_none() {

        $cs = array('a:x*y', 'b:x*y*z', 'c:x*(y*z)', 'd:(x*y)*z');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('x\,y', $s1[0]->get_display());
        $this->assertEquals('x\,y\,z', $s1[1]->get_display());
        $this->assertEquals('x\,\left(y\,z\right)', $s1[2]->get_display());
        // Notice the associativity of Maxima suppresses the extra explicit brackets here.
        $this->assertEquals('x\,y\,z', $s1[3]->get_display());
    }

    public function test_multiplication_option_cross() {

        $cs = array('a:x*y', 'b:x*y*z', 'c:x*(y*z)', 'd:(x*y)*z');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('multiplicationsign', 'cross');
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('x\times y', $s1[0]->get_display());
        $this->assertEquals('x\times y\times z', $s1[1]->get_display());
        $this->assertEquals('x\times \left(y\times z\right)', $s1[2]->get_display());
        // Notice the associativity of Maxima suppresses the extra explicit brackets here.
        $this->assertEquals('x\times y\times z', $s1[3]->get_display());
    }

    public function test_acos_option_cosmone() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('inversetrig', 'cos-1');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('\cos^{-1}\left( x \right)', $s1[0]->get_display());
        $this->assertEquals('\sin^{-1}\left( x \right)', $s1[1]->get_display());
        $this->assertEquals('{\rm sinh}^{-1}\left( x \right)', $s1[2]->get_display());
    }

    public function test_acos_option_acos() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('inversetrig', 'acos');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('{\rm acos}\left( x \right)', $s1[0]->get_display());
        $this->assertEquals('{\rm asin}\left( x \right)', $s1[1]->get_display());
        $this->assertEquals('{\rm asinh}\left( x \right)', $s1[2]->get_display());
    }

    public function test_acos_option_arccos() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('inversetrig', 'arccos');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('\arccos \left( x \right)', $s1[0]->get_display());
        $this->assertEquals('\arcsin \left( x \right)', $s1[1]->get_display());
        $this->assertEquals('{\rm arcsinh}\left( x \right)', $s1[2]->get_display());
    }

    public function test_keyval_representation_1() {

        $cs = array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals("a:x^2;\nb:1/(1+x^2);\nc:e^(i*pi);", $at1->get_keyval_representation());

        // TODO: no longer relevant?
        //$this->assertEquals(array('a', 'b', 'c'), $at1->get_all_keys());
        //$at1->prune_session2(1);
        //$this->assertEquals(array('a'), $at1->get_all_keys());
    }

    public function test_keyval_representation_2() {

        $cs = array('a:(-1)^2');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals("a:(-1)^2;", $at1->get_keyval_representation());
        $this->assertEquals('a:1;', $at1->get_keyval_representation(true));
    }

    public function test_get_display_unary_minus() {

        $cs = array('p1:y^3-2*y^2-8*y', 'p2:y^2-2*y-8', 'p3:y^2-2*y-0.5', 'p4:x+-3+y', 'p5:x+(-5+y)');
        // Notice the subtle difference in p4 & p5.
        // Where extra brackets are put in they should stay.
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('y^3-2\\cdot y^2-8\\cdot y', $s1[0]->get_display());
        $this->assertEquals('y^2-2\\cdot y-8', $s1[1]->get_display());
        $this->assertEquals('y^2-2\\cdot y-0.5', $s1[2]->get_display());
        // Since we introduced a +- operator, changes from Maxima's x-3+y.
        $this->assertEquals('{x \pm 3}+y', $s1[3]->get_display());
        $this->assertEquals('x+\\left(-5+y\\right)', $s1[4]->get_display());
    }

    public function test_string1() {

        $cs = array('s:"This is a string"');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('"This is a string"', $s1[0]->get_value());
    }

    public function test_qmchar() {

        $cs = array('s:5*?+6*?', 'A:matrix([?,1],[1,?])');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();

        $this->assertEquals('11*QMCHAR', $s1[0]->get_value());
        $this->assertEquals('11\cdot \color{red}{?}', $s1[0]->get_display());

        $this->assertEquals('matrix([QMCHAR,1],[1,QMCHAR])', $s1[1]->get_value());
    }

    public function test_subscript_disp() {
        // Fails with actual display output like '{\it pi_{025}}'.
        $this->skip_if_old_maxima('5.23.2');

        $cs = array('a:pi_25', 'b:1+x_3', 'c:f(x):=x^3', 'd:gamma_7^3', 'a2:pi_4^5');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals($s1[0]->get_value(), 'pi_25');
        $this->assertEquals($s1[0]->get_display(), '{\pi}_{25}');

        $this->assertEquals($s1[1]->get_value(), '1+x_3');
        $this->assertEquals($s1[1]->get_display(), '1+{x}_{3}');

        $this->assertEquals($s1[2]->get_value(), 'f(x):=x^3');
        $this->assertEquals($s1[2]->get_display(), 'f(x):=x^3');

        $this->assertEquals($s1[3]->get_value(), 'gamma_7^3');
        $this->assertEquals($s1[3]->get_display(), '{\gamma}_{7}^3');

        $this->assertEquals($s1[4]->get_value(), 'pi_4^5');
        $this->assertEquals($s1[4]->get_display(), '{\pi}_{4}^5');
    }

    public function test_assignmatrixelements() {
        // Assign a value to matrix entries.
        $cs = array('A:matrix([1,2],[1,1])', 'A[1,2]:3', 'B:A');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();

        $this->assertEquals('matrix([1,3],[1,1])', $s1[0]->get_value());
        $this->assertEquals('matrix([1,3],[1,1])', $s1[2]->get_value());
    }

    public function test_simplify_false() {

        $cs = array('a:2+3', 'b:ev(a,simp)');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('2+3', $s1[0]->get_value());
        $this->assertEquals('5', $s1[1]->get_value());
    }

    public function test_disp_control_structures() {

        $csl = array('p:if a>b then setelmx(0,m[k],m[j],A)',
            'addto1(ex):=thru ex do x:0.5*(x+5.0/x)',
            'addto2(ex):=for a from -3 step 7 thru ex do a^2',
            'addto3(ex):=for i from 2 while ex<=10 do s:s+i',
            'addto4(ex):=block([l],l:ex,for f in [log,rho,atan] do l:append(l,[f]),l)',
            'l:addto4([sin,cos])');

        foreach ($csl as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals(array(), $at1->get_errors(false));

        $this->assertEquals('if a>b then setelmx(0,m[k],m[j],A)', $s1[0]->get_value());
        $this->assertEquals('\mathbf{if}\;a > b\;\mathbf{then}\;{\it setelmx}\left(0 , m_{k} , m_{j} , A\right)',
                $s1[0]->get_display());

        // Confirm these expressions are unchanged by the CAS.
        $atsession = $at1->get_session();
        $at1->instantiate();
        for ($i = 1; $i <= 4; $i++) {
            $cs = $atsession[$i];
            $this->assertEquals($csl[$i], $cs->get_value());
        }

        $this->assertEquals('[sin,cos,log,rho,atan]', $s1[5]->get_value());
    }

    public function test_redefine_variable() {

        // This example redefines the value of n.
        // It should return the last value.
        $cs = array('n:3', 'n:n+3', 'n:n^2');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        // TODO: Matti, Why are the following two "unevaluated"?
        //$this->assertEquals('3', $s1[0]->get_value());
        //$this->assertEquals('3', $s1[0]->get_display());
        $this->assertEquals('36', $s1[2]->get_value());
        $this->assertEquals('36', $s1[2]->get_display());
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
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('matrix([5,2],[4,3])', $s1[0]->get_value());
        $this->assertEquals('matrix([5*4+2*6,5*5+2*5],[4*4+3*6,4*5+3*5])', $s1[6]->get_value());
    }

}
