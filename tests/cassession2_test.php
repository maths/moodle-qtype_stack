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
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

/**
 * @group qtype_stack
 */
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
        $bar = stack_ast_container::make_from_teacher_source('simplode(["f","o","o"], "-");', '', new stack_cas_security());

        $session = new stack_cas_session2([$simpon, $divzero, $foo, $bar]);

        $this->assertTrue($session->get_valid());
        $this->assertFalse($session->is_instantiated());
        $session->instantiate();
        $this->assertTrue($session->is_instantiated());

        $this->assertEquals('', $simpon->get_errors());
        $this->assertEquals('', $foo->get_errors());
        $this->assertEquals('"foo"', $foo->get_value());
        $this->assertEquals('"f-o-o"', $bar->get_value());
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

        $this->assertContains('lowest terms', $validation->get_feedback());
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
        $this->assertEquals('x\cdot y\cdot z', $s1[3]->get_display());
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

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)', 'd:asin(x)^3', 'e:asin(x^2+1)^30');
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
        $this->assertEquals('\sin^{-1}^3x', $s1[3]->get_display());
        // Note, the LaTeX below will break MathJax.
        // But if you are willing to have inverses and powers with the same notation then you deserve to break things!
        $this->assertEquals('\sin^{-1}^{30}\left(x^2+1\right)', $s1[4]->get_display());
        // Babbage comlained around 1820 about people using this notation and people still use this notation!
    }

    public function test_acos_option_acos() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)', 'd:asin(x)^3', 'e:asin(x^2+1)^30');
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
        $this->assertEquals('{\rm asin}^3x', $s1[3]->get_display());
        $this->assertEquals('{\rm asin}^{30}\left(x^2+1\right)', $s1[4]->get_display());
    }

    public function test_acos_option_arccos() {

        $cs = array('a:acos(x)', 'b:asin(x)', 'c:asinh(x)', 'd:asin(x)^3', 'e:asin(x^2+1)^30');
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
        $this->assertEquals('\arcsin ^3x', $s1[3]->get_display());
        $this->assertEquals('\arcsin ^{30}\left(x^2+1\right)', $s1[4]->get_display());
    }

    public function test_logicsymbol_option_lang() {

        $cs = array('a:A and B', 'b:A nounand B', 'c:A and (B or C)', 'd:A nounand (B nounor C)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('logicsymbol', 'lang');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('A\,{\mbox{ and }}\, B', $s1[0]->get_display());
        $this->assertEquals('A\,{\mbox{ and }}\, B', $s1[1]->get_display());
        $this->assertEquals('A\,{\mbox{ and }}\, \left(B\,{\mbox{ or }}\, C\right)', $s1[2]->get_display());
        $this->assertEquals('A\,{\mbox{ and }}\, \left(B\,{\mbox{ or }}\, C\right)', $s1[3]->get_display());
    }

    public function test_logicsymbol_option_symbol() {

        $cs = array('a:A and B', 'b:A nounand B', 'c:A and (B or C)', 'd:A nounand (B nounor C)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('logicsymbol', 'symbol');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('A\land B', $s1[0]->get_display());
        $this->assertEquals('A\land B', $s1[1]->get_display());
        $this->assertEquals('A\land \left(B\lor C\right)', $s1[2]->get_display());
        $this->assertEquals('A\land \left(B\lor C\right)', $s1[3]->get_display());
    }

    public function test_keyval_representation_1() {

        $cs = array('a:x^2', 'b:1/(1+x^2)', 'c:e^(i*pi)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals("a:x^2;\nb:1/(1+x^2);\nc:e^(i*%pi);", $at1->get_keyval_representation());
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

        $cs = array('p1:y^3-2*y^2-8*y', 'p2:y^2-2*y-8', 'p3:y^2-2*y-0.5', 'p4:x#pm#3+y', 'p5:x+(-5+y)');
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
        $this->assertEquals($s1[3]->get_display(), '{{\gamma}_{7}}^3');

        $this->assertEquals($s1[4]->get_value(), 'pi_4^5');
        $this->assertEquals($s1[4]->get_display(), '{{\pi}_{4}}^5');
    }

    public function test_matrix_eigenvalues() {

        $cs = array('A:matrix([7,1,3],[5,-3,4],[5,3,-4])', 'E:first(eigenvalues(A))', 'dt:determinant(A)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('matrixparens', '(');

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('[1-sqrt(66),sqrt(66)+1,-2]', $s1[1]->get_value());
        $this->assertEquals('130', $s1[2]->get_value());
        $this->assertEquals('\left(\begin{array}{ccc} 7 & 1 & 3 \\\\ 5 & -3 & 4 \\\\ 5 & 3 & -4 \end{array}\right)',
                $s1[0]->get_display());

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
            'addto3(ex):=for i from 2 while ex <= 10 do s:s+i',
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

        $this->assertEquals('if a > b then setelmx(0,m[k],m[j],A)', $s1[0]->get_value());
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

        // In the new logic we only return the value at the end of execution for
        // statements that have keys, and in this case you have three statements
        // with the same-key and only one will will receive that value. The session
        // could bring that value to every one of them but there is very little
        // need for that. This ties heavily to the concept of keys.
        // Try setting with "set_keyless()" and the value is not collected by the key but
        // instead by statement and it will then return you the values for each
        // statement separately. Keyless behaviour is present in CASText but
        // otherwise one does not really need it beyond some old unit-tests. Once
        // CASText2 appears, keyless behaviour becomes pointless beyond some debug
        // use-cases and unit-tests.
        // So we don't ecpect $s1[0]->get_value()) to return a value.

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

    public function test_numerical_precision() {

        $cs = array('a:1385715.257');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('1385715.257', $s1[0]->get_value());
    }

    public function test_rat() {

        $cs = array('a:ratsimp(sqrt(27))', 'b:rat(sqrt(27))', 'm:MAXIMA_VERSION_NUM');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('3^(3/2)', $s1[0]->get_value());

        // @codingStandardsIgnoreStart
        // Warning to developers.   The behaviour of rat is not stable accross versions of Maxima.
        // In Maxima 5.25.1: rat(sqrt(27)) gives sqrt(3)^3.
        // In Maxima 5.36.1: rat(sqrt(27)) gives (3^(1/2)^3).
        // In Maxima 5.37.1: rat(sqrt(27)) gives sqrt(3)^3.
        // @codingStandardsIgnoreEnd
        $maximaversion = $s1[2]->get_value();
        if ($maximaversion == '36.1') {
            // Developers should add other versions of Maxima here as needed.
            $this->assertEquals('(3^(1/2))^3', $s1[1]->get_value());
        } else {
            $this->assertEquals('sqrt(3)^3', $s1[1]->get_value());
        }
    }

    public function test_ordergreat() {
        $cs = array('stack_reset_vars(true)', 'ordergreat(i,j,k)', 'p:matrix([-7],[2],[-3])',
            'q:matrix([i],[j],[k])', 'v:dotproduct(p,q)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        // There has been a subtle change to associativity in Maxima 5.37.0.
        $this->assertEquals('-7\cdot i+2\cdot j-3\cdot k', $s1[4]->get_display());
    }

    public function test_ordergreat_2() {
        $cs = array('ordergreat(a,b,c)', 'p:matrix([-7],[2],[-3])', 'q:matrix([a],[b],[c])', 'v:dotproduct(p,q)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        // There has been a subtle change to associativity in Maxima 5.37.0.
        $this->assertEquals('-7\cdot a+2\cdot b-3\cdot c', $s1[3]->get_display());
    }

    public function test_plot_constant_function() {

        $cs = array('a:0', 'p:plot(a*x,[x,-2,2],[y,-2,2])');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('0', $s1[0]->get_value());
        $this->assertTrue(is_numeric(strpos($s1[1]->get_value(), 'STACK auto-generated plot of 0 with parameters')));
        $this->assertEquals('', trim($s1[0]->get_errors()));
    }

    public function test_plot_fail() {

        $cs = array('a:0', 'p:plot(a*x/0,[x,-2,2],[y,-2,2])');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('0', $s1[0]->get_value());
        $this->assertRegExp('/Division by (zero|0)/', trim($s1[1]->get_errors()));
        $this->assertFalse(strpos($s1[1]->get_value(), 'STACK auto-generated plot of 0 with parameters'));
    }

    public function test_rand_selection_err_1() {
        $cs = array('a:rand_selection(1,1)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('a', $s1[0]->get_value());
        $this->assertEquals('rand_selection error: first argument must be a list.', $s1[0]->get_errors());
    }

    public function test_rand_selection_err_2() {
        $cs = array('a:rand_selection([a,b,c,d], 7)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('a', $s1[0]->get_value());
        $this->assertEquals('rand_selection error: insuffient elements in the list.', $s1[0]->get_errors());
    }

    public function test_rand_selection() {
        $cs = array('a:rand_selection([a,b,c,d], 4)', 'b:sort(a)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[a,b,c,d]', $s1[1]->get_value());
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
        $kv = new stack_cas_keyval(implode(';', $cmds), $options, 0);
        $s = $kv->get_session(); // This does a validation on the side.

        $s->instantiate();
        $s1 = $s->get_session();

        $i = 0;
        foreach ($cases as $case) {
            $this->assertEquals($case[1], $s1[$i]->get_value());
            $this->assertEquals($case[2], $s1[$i]->get_display());
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
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[alpha,beta,delta,epsilon]', $s1[0]->get_value());
        $this->assertEquals('\left[ \alpha , \beta , \delta , \varepsilon \right]',
                $s1[0]->get_display());
        $this->assertEquals('[zeta,eta,theta,iota,kappa]', $s1[1]->get_value());
        $this->assertEquals('\left[ \zeta , \eta , \theta , \iota , \kappa \right]',
                $s1[1]->get_display());
        // Note here that pi is returned as the constant %pi.
        $this->assertEquals('[lambda,mu,nu,xi,omicron,%pi,rho]', $s1[2]->get_value());
        $this->assertEquals('\left[ \lambda , \mu , \nu , \xi , o , \pi , \rho \right]',
                $s1[2]->get_display());
        $this->assertEquals('[sigma,tau,upsilon,phi,psi,chi,omega]', $s1[3]->get_value());
        $this->assertEquals('\left[ \sigma , \tau , \upsilon , \varphi , \psi , \chi , \omega \right]',
                $s1[3]->get_display());
    }

    public function test_greek_lower_gamma() {
        // In old maxima, you get '\Gamma' for the display output.
        $this->skip_if_old_maxima('5.23.2');
        $cs = stack_ast_container::make_from_student_source('greek1:gamma', '', new stack_cas_security(), array());

        $at1 = new stack_cas_session2(array($cs), null, 0);
        $at1->instantiate();
        $this->assertEquals('gamma', $cs->get_value());
        $this->assertEquals('\gamma', $cs->get_display());
    }

    public function test_greek_upper() {
        $cs = array('greek1:[Alpha,Beta,Gamma,Delta,Epsilon]',
                    'greek2:[Zeta,Eta,Theta,Iota,Kappa]',
                    'greek3:[Lambda,Mu,Nu,Xi,Omicron,Pi,Rho]',
                    'greek4:[Sigma,Tau,Upsilon,Phi,Chi,Psi,Omega]',
                    'v:round(float(Pi))');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        $this->assertEquals('[Alpha,Beta,Gamma,Delta,Epsilon]', $s1[0]->get_value());
        $this->assertEquals('\left[ {\rm A} , {\rm B} , \Gamma , \Delta , {\rm E} \right]',
                $s1[0]->get_display());
        $this->assertEquals('[Zeta,Eta,Theta,Iota,Kappa]', $s1[1]->get_value());
        $this->assertEquals('\left[ {\rm Z} , {\rm H} , \Theta , {\rm I} , {\rm K} \right]',
                $s1[1]->get_display());
        // Note here that pi is returned as the constant %pi.
        $this->assertEquals('[Lambda,Mu,Nu,Xi,Omicron,%pi,Rho]', $s1[2]->get_value());
        $this->assertEquals('\left[ \Lambda , {\rm M} , {\rm N} , \Xi , {\rm O} , \pi , {\rm P} \right]',
                $s1[2]->get_display());
        $this->assertEquals('[Sigma,Tau,Upsilon,Phi,Chi,Psi,Omega]', $s1[3]->get_value());
        $this->assertEquals('\left[ \Sigma , {\rm T} , \Upsilon , \Phi , {\rm X} , \Psi , \Omega \right]',
                $s1[3]->get_display());
        $this->assertEquals('3', $s1[4]->get_value());
    }

    public function test_taylor_cos_simp() {
        $cs = array('c1:taylor(cos(x),x,0,1)',
                    'c3:taylor(cos(x),x,0,3)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', true);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        // For some reason Maxima's taylor function doesn't always put \cdots at the end.
        $this->assertEquals('+1', $s1[0]->get_value());
        $this->assertEquals('+1+\cdots', $s1[0]->get_display());
        $this->assertEquals('1-x^2/2', $s1[1]->get_value());
        $this->assertEquals('1-\frac{x^2}{2}+\cdots', $s1[1]->get_display());
    }

    public function test_taylor_cos_nosimp() {
        $cs = array('c1:taylor(cos(x),x,0,1)',
                    'c3:taylor(cos(x),x,0,3)');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', true);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        // For some reason Maxima's taylor function doesn't always put \cdots at the end.
        $this->assertEquals('+1', $s1[0]->get_value());
        $this->assertEquals('+1+\cdots', $s1[0]->get_display());
        $this->assertEquals('1-x^2/2', $s1[1]->get_value());
        $this->assertEquals('1-\frac{x^2}{2}+\cdots', $s1[1]->get_display());
    }

    public function test_lambda() {
        $cs = array('l1:lambda([ex], ex^3)',
                    'l2:[1,2,3]',
                    'l3:maplist(l1, l2)'
        );
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }
        $at1 = new stack_cas_session2($s1, null, 0);
        $at1->instantiate();
        // For some reason Maxima's taylor function doesn't always put \cdots at the end.
        $this->assertEquals('lambda([ex],ex^3)', $s1[0]->get_value());
        $this->assertEquals('[1,8,27]', $s1[2]->get_value());
    }

    public function test_sets_simp() {
        $cs = array('c1:{}', 'c2:{b,a,c}');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('{}', $s1[0]->get_value());
        $this->assertEquals('\left \{ \right \}', $s1[0]->get_display());
        $this->assertEquals('{a,b,c}', $s1[1]->get_value());
        $this->assertEquals('\left \{a , b , c \right \}', $s1[1]->get_display());
    }

    public function test_sets_simp_false() {
        $cs = array('c1:{}', 'c2:{b,a,c}');
        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('{}', $s1[0]->get_value());
        $this->assertEquals('\left \{ \right \}', $s1[0]->get_display());
        $this->assertEquals('{b,a,c}', $s1[1]->get_value());
        $this->assertEquals('\left \{b , a , c \right \}', $s1[1]->get_display());
    }

    public function test_numerical_rounding() {

        $tests = stack_numbers_test_data::get_raw_test_data();
        $s1 = array();
        foreach ($tests as $key => $test) {
            $s1[] = stack_ast_container::make_from_teacher_source('p'.$key.':dispdp('.$test[0].', '.$test[3] .')',
                '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $this->assertTrue($at1->get_valid());
        $at1->instantiate();

        foreach ($tests as $key => $test) {
            $cs = $at1->get_by_key('p'.$key);
            $this->assertEquals($test[5], $cs->get_display());
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
            array('3.14159', '2', '3.14', '3.14', 'displaydp(3.14,2)'),
            array('100', '1', '100.0', '100.0', 'displaydp(100.0,1)', ''),
            array('100', '2', '100.00', '100.00', 'displaydp(100.0,2)'),
            array('100', '3', '100.000', '100.000', 'displaydp(100.0,3)'),
            array('100', '4', '100.0000', '100.0000', 'displaydp(100.0,4)'),
            array('100', '5', '100.00000', '100.00000', 'displaydp(100.0,5)'),
            array('0.99', '1', '1.0', '1.0', 'displaydp(1.0,1)'),
        );

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source("p{$key}:dispdp({$c[0]},{$c[1]})",
                '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        foreach ($tests as $key => $c) {
            $cs = $at1->get_by_key('p'.$key);
            $this->assertEquals($c[2], $cs->get_display());
            $this->assertEquals($c[3], $cs->get_dispvalue());
            $this->assertEquals($c[4], $cs->get_value());
        }
    }

    public function test_dispdp_systematic() {
        $cs = stack_ast_container::make_from_teacher_source("L:makelist(dispdp(10^-1+10^-k,k+1),k,12,20)",
                '', new stack_cas_security(), array());
        $s1[] = $cs;

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        // The purpose of this test is to ilustrate how numerical precision runs out.
        // This is currently in the 16th decimal place, where we loose the 10^-k from the displayed output.
        $this->assertEquals('\left[ 0.1000000000010 , 0.10000000000010 , 0.100000000000010 , 0.1000000000000010 , '.
            '0.10000000000000010 , 0.100000000000000000 , 0.1000000000000000000 , 0.10000000000000000000 , '.
            '0.100000000000000000000 \right]', $s1[0]->get_display());
        // Even more worryingly, the "value" of the expression looses this at the 14th place.
        // This is because of the precision specified in the "string" routine of Maxima, which sends "values" from Maxima to PHP.
        $expected = '[displaydp(.100000000001,13),displaydp(0.1,14),displaydp(0.1,15),displaydp(0.1,16),displaydp(0.1,17),'.
            'displaydp(0.1,18),displaydp(0.1,19),displaydp(0.1,20),displaydp(0.1,21)]';
        $expectval = '[0.1000000000010,0.10000000000010,0.100000000000010,0.1000000000000010,0.10000000000000010,' .
            '0.100000000000000000,0.1000000000000000000,0.10000000000000000000,0.100000000000000000000]';
        if ($this->adapt_to_new_maxima('5.32.2')) {
            $expected = '[displaydp(0.100000000001,13),displaydp(0.1,14),displaydp(0.1,15),displaydp(0.1,16),'.
                'displaydp(0.1,17),displaydp(0.1,18),displaydp(0.1,19),displaydp(0.1,20),displaydp(0.1,21)]';
            $expectval = '[0.1000000000010,0.10000000000010,0.100000000000010,0.1000000000000010,0.10000000000000010,' .
                '0.100000000000000000,0.1000000000000000000,0.10000000000000000000,0.100000000000000000000]';
        }
        $this->assertEquals($expectval, $s1[0]->get_dispvalue());
        $this->assertEquals($expected, $s1[0]->get_value());
    }

    public function test_dispdp_systematic_longer() {

        $cs = stack_ast_container::make_from_teacher_source("fpprintprec:16", '', new stack_cas_security(), array());
        $s1[] = $cs;
        $cs = stack_ast_container::make_from_teacher_source("L:makelist(dispdp(10^-1+10^-k,k+1),k,12,20)", '',
            new stack_cas_security(), array());
        $s1[] = $cs;

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        $this->assertEquals('\left[ 0.1000000000010 , 0.10000000000010 , 0.100000000000010 , 0.1000000000000010 , '.
            '0.10000000000000010 , 0.100000000000000000 , 0.1000000000000000000 , 0.10000000000000000000 , '.
            '0.100000000000000000000 \right]', $s1[1]->get_display());
        // Note the difference, having increased fpprintprec:16 which is the maximum value permitted in Maxima.
        $expected = '[displaydp(0.100000000001,13),displaydp(0.1000000000001,14),displaydp(0.10000000000001,15),'.
            'displaydp(0.100000000000001,16),displaydp(.1000000000000001,17),'.
            'displaydp(0.1,18),displaydp(0.1,19),displaydp(0.1,20),displaydp(0.1,21)]';
        $expectval = '[0.1000000000010,0.10000000000010,0.100000000000010,0.1000000000000010,0.10000000000000010,' .
            '0.100000000000000000,0.1000000000000000000,0.10000000000000000000,0.100000000000000000000]';
        if ($this->adapt_to_new_maxima('5.32.2')) {
            $expected = '[displaydp(0.100000000001,13),displaydp(0.1000000000001,14),displaydp(0.10000000000001,15),'.
                'displaydp(0.100000000000001,16),displaydp(0.1000000000000001,17),'.
                // We get more decimal places preserved here that the above test cases.
                'displaydp(0.1,18),displaydp(0.1,19),displaydp(0.1,20),displaydp(0.1,21)]';
            $expectval = '[0.1000000000010,0.10000000000010,0.100000000000010,0.1000000000000010,0.10000000000000010,' .
                '0.100000000000000000,0.1000000000000000000,0.10000000000000000000,0.100000000000000000000]';
        }
        $this->assertEquals($expectval, $s1[1]->get_dispvalue());
        $this->assertEquals($expected, $s1[1]->get_value());
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
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $cs = $at1->get_by_key('p'.$key);
            $this->assertEquals($c[2], $cs->get_display());
            $this->assertEquals($c[3], strtolower($cs->get_value()));
        }
    }

    public function test_significantfigures_errors() {
        $tests = array(
            array('significantfigures(%pi/3,3)', '1.05', ''),
            array('significantfigures(%pi/blah,3)', 'p1',
                'sigfigsfun(x,n,d) requires a real number, or a list of real numbers, ' .
                'as a first argument.  Received:  %pi/blah'),
            array('significantfigures(%pi/3,n)', 'p2',
                'sigfigsfun(x,n,d) requires an integer as a second argument. Received:  n'),
        );

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source("p".$key.':'.$c[0],
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $this->assertEquals($c[1], $s1[$key]->get_value());
            $this->assertEquals($c[2], $s1[$key]->get_errors());
        }
    }

    public function test_significantfigures_list() {
        $tests = array(
            array('significantfigures([0.2,0.4,0.5,0.6,0.7,0.8,1]*1.9,3)',
                '[0.38,0.76,0.95,1.14,1.33,1.52,1.9]'),
        );

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source("p".$key.':'.$c[0],
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $this->assertEquals($c[1], $s1[$key]->get_value());
        }
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
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($tests as $key => $c) {
            $sk = "p{$key}";
            $this->assertEquals($c[2], $s1[$key]->get_display());
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
        // 4. Value.
        // 5. Optional: what happens to the display with simp:true.
        // 6. Optional: what happens to the dispvalue form with simp:true.
        // 7. Optional: what happens to the value form with simp:true.

        // @codingStandardsIgnoreEnd

        $tests = array(
            array('2.998e8', '2', '3.00 \times 10^{8}', '3.00E8', 'displaysci(3,2,8)'),
            array('-2.998e8', '2', '-3.00 \times 10^{8}', '-3.00E8', 'displaysci(-3,2,8)'),
            array('6.626e-34', '2', '6.63 \times 10^{-34}', '6.63E-34', 'displaysci(6.63,2,-34)'),
            array('-6.626e-34', '2', '-6.63 \times 10^{-34}', '-6.63E-34', 'displaysci(-6.63,2,-34)'),
            array('6.022e23', '2', '6.02 \times 10^{23}', '6.02E23', 'displaysci(6.02,2,23)'),
            array('5.985e30', '2', '5.99 \times 10^{30}', '5.99E30', 'displaysci(5.99,2,30)'),
            array('-5.985e30', '2', '-5.99 \times 10^{30}', '-5.99E30', 'displaysci(-5.99,2,30)'),
            array('1.6726e-27', '2', '1.67 \times 10^{-27}', '1.67E-27', 'displaysci(1.67,2,-27)'),
            array('1e5', '2', '1.00 \times 10^{5}', '1.00E5', 'displaysci(1,2,5)'),
            array('1.9e5', '2', '1.90 \times 10^{5}', '1.90E5', 'displaysci(1.9,2,5)'),
            array('1.0e9', '2', '1.00 \times 10^{9}', '1.00E9', 'displaysci(1,2,9)'),
            array('100000', '2', '1.00 \times 10^{5}', '1.00E5', 'displaysci(1,2,5)'),
            array('110000', '2', '1.10 \times 10^{5}', '1.10E5', 'displaysci(1.1,2,5)'),
            array('54e3', '2', '5.40 \times 10^{4}', '5.40E4', 'displaysci(5.4,2,4)'),

            array('0.00000000000067452', '2', '6.75 \times 10^{-13}', '6.75E-13', 'displaysci(6.75,2,-13)'),
            array('-0.00000000000067452', '2', '-6.75 \times 10^{-13}', '-6.75E-13', 'displaysci(-6.75,2,-13)'),
            array('-0.0000000000006', '2', '-6.00 \times 10^{-13}', '-6.00E-13', 'displaysci(-6,2,-13)'),
            array('0.0000000000000000000005555', '2', '5.56 \times 10^{-22}', '5.56E-22', 'displaysci(5.56,2,-22)'),
            array('0.00000000000000000000055', '2', '5.50 \times 10^{-22}', '5.50E-22', 'displaysci(5.5,2,-22)'),
            array('-0.0000000000000000000005555', '2', '-5.56 \times 10^{-22}', '-5.56E-22', 'displaysci(-5.56,2,-22)'),
            array('67260000000000000000000000', '2', '6.73 \times 10^{25}', '6.73E25', 'displaysci(6.73,2,25)'),
            array('67000000000000000000000000', '2', '6.70 \times 10^{25}', '6.70E25', 'displaysci(6.7,2,25)'),
            array('-67260000000000000000000000', '2', '-6.73 \times 10^{25}', '-6.73E25', 'displaysci(-6.73,2,25)'),
            array('-67000000000000000000000000', '2', '-6.70 \times 10^{25}', '-6.70E25', 'displaysci(-6.7,2,25)'),
            array('0.001', '2', '1.00 \times 10^{-3}', '1.00E-3', 'displaysci(1,2,-3)'),
            array('-0.001', '2', '-1.00 \times 10^{-3}', '-1.00E-3', 'displaysci(-1,2,-3)'),
            array('10', '2', '1.00 \times 10^{1}', '1.00E1', 'displaysci(1,2,1)'),
            array('2', '0', '2 \times 10^{0}', '2E0', 'displaysci(2,0,0)'),
            array('300', '0', '3 \times 10^{2}', '3E2', 'displaysci(3,0,2)'),
            array('4321.768', '3', '4.322 \times 10^{3}', '4.322E3', 'displaysci(4.322,3,3)'),
            array('-53000', '2', '-5.30 \times 10^{4}', '-5.30E4', 'displaysci(-5.3,2,4)'),
            array('6720000000', '3', '6.720 \times 10^{9}', '6.720E9', 'displaysci(6.72,3,9)'),
            array('6.0221409e23', '4', '6.0221 \times 10^{23}', '6.0221E23', 'displaysci(6.0221,4,23)'),
            array('1.6022e-19', '4', '1.6022 \times 10^{-19}', '1.6022E-19', 'displaysci(1.6022,4,-19)'),
            array('1.55E8', '2', '1.55 \times 10^{8}', '1.55E8', 'displaysci(1.55,2,8)'),
            array('-0.01', '1', '-1.0 \times 10^{-2}', '-1.0E-2', 'displaysci(-1,1,-2)'),
            array('-0.00000001', '3', '-1.000 \times 10^{-8}', '-1.000E-8', 'displaysci(-1,3,-8)'),
            array('-0.00000001', '1', '-1.0 \times 10^{-8}', '-1.0E-8', 'displaysci(-1,1,-8)'),
            array('-0.00000001', '0', '-1 \times 10^{-8}', '-1E-8', 'displaysci(-1,0,-8)'),
            array('-1000', '2', '-1.00 \times 10^{3}', '-1.00E3', 'displaysci(-1,2,3)'),
            array('31415.927', '3', '3.142 \times 10^{4}', '3.142E4', 'displaysci(3.142,3,4)'),
            array('-31415.927', '3', '-3.142 \times 10^{4}', '-3.142E4', 'displaysci(-3.142,3,4)'),
            array('155.5', '2', '1.56 \times 10^{2}', '1.56E2', 'displaysci(1.56,2,2)'),
            array('15.55', '2', '1.56 \times 10^{1}', '1.56E1', 'displaysci(1.56,2,1)'),
            array('777.7', '2', '7.78 \times 10^{2}', '7.78E2', 'displaysci(7.78,2,2)'),
            array('775.5', '2', '7.76 \times 10^{2}', '7.76E2', 'displaysci(7.76,2,2)'),
            array('775.55', '2', '7.76 \times 10^{2}', '7.76E2', 'displaysci(7.76,2,2)'),
            array('0.5555', '2', '5.56 \times 10^{-1}', '5.56E-1', 'displaysci(5.56,2,-1)'),
            array('0.05555', '2', '5.56 \times 10^{-2}', '5.56E-2', 'displaysci(5.56,2,-2)'),
            array('cos(23*pi/180)', '3', '9.205 \times 10^{-1}', '9.205E-1', 'displaysci(9.205,3,-1)'),
            array('9000', '1', '9.0 \times 10^{3}', '9.0E3', 'displaysci(9,1,3)'),
            array('8000', '0', '8 \times 10^{3}', '8E3', 'displaysci(8,0,3)'),
            // Edge case.  Want these ones to be 1*10^3, not 10.0*10^2.
            array('1000', '2', '1.00 \times 10^{3}', '1.00E3', 'displaysci(1,2,3)'),
            // If we don't supply a number of decimal places, then we return a value form.
            // This is entered as scientific_notation(x).
            // This is displayed normally (without a \times) and always returns a *float*.
            // Notice this has different behaviour with simp:true.
            array('7000', '', '7.0\cdot 10^3', '7.0*10^3', '7.0*10^3',
                    '7000.0', '7000.0', '7000.0'),
            array('1000', '', '1.0\cdot 10^3', '1.0*10^3', '1.0*10^3',
                    '1000.0', '1000.0', '1000.0'),
            array('-1000', '', '-1.0\cdot 10^3', '-(1.0*10^3)', '(-1.0)*10^3',
                    '-1000.0', '-1000.0', '-1000.0'),
            array('1e50', '', '1.0\cdot 10^{50}', '1.0*10^50', '1.0*10^50',
                    '1.0E+50', '1.0E+50', '1.0E+50'),
            // @codingStandardsIgnoreStart

            // In some versions of Maxima this comes out as -\frac{1.0}{10^8} with simp:true.
            // Adding in compile(scientific_notation)$ after the function definition cures this,
            // but breaks some versions of Maxima.
            // Maxima 5.38.1 gives -1.0*10^-8, which is what we actually want.
            // Pass: 36.1, 31.3, 38.1, 39.0, 41.0.
            // Fail: 37.0, 37.1, 37.2, 37.3.

            // @codingStandardsIgnoreEnd
            array('-0.00000001', '', '-1.0\cdot 10^ {- 8 }', '-(1.0*10^-8)', '(-1.0)*10^-8',
                    '-1.0E-8', '-1.0E-8', '-1.0E-8'),
            array('-0.000000001', '', '-1.0\cdot 10^ {- 9 }', '-(1.0*10^-9)', '(-1.0)*10^-9',
                    '-1.0E-9', '-1.0E-9', '-1.0E-9'),
            array('-0.000000000001', '', '-1.0\cdot 10^ {- 12 }', '-(1.0*10^-12)', '(-1.0)*10^-12',
                    '-1.0E-12', '-1.0E-12', '-1.0E-12'),
        );

        $s1 = array();
        foreach ($tests as $key => $c) {
            $s = "p{$key}:scientific_notation({$c[0]},{$c[1]})";
            if ($c[1] == '') {
                $s = "p{$key}:scientific_notation({$c[0]})";
            }
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        foreach ($tests as $key => $c) {
            if (!$s1[$key]->get_valid()) {
                // Help output which test fails.
                $this->assertTrue($c[0]);
            }
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $this->assertTrue($at1->get_valid());
        $at1->instantiate();

        $this->assertEquals('', $at1->get_errors());
        // All these tests should work with simp:false.
        foreach ($tests as $key => $c) {
            if ($s1[$key]->is_correctly_evaluated()) {
                $this->assertEquals($c[2], $s1[$key]->get_display());
                $this->assertEquals($c[3], $s1[$key]->get_dispvalue());
                $this->assertEquals($c[4], $s1[$key]->get_value());
            } else {
                // Help output which test fails.

                $this->assertEquals(null, $c[0]);
            }
        }

        // Does simp:true make any difference?
        // For some tests it does.

        $s2 = array();
        foreach ($tests as $key => $c) {
            $s = "p{$key}:scientific_notation({$c[0]},{$c[1]})";
            if ($c[1] == '') {
                $s = "p{$key}:scientific_notation({$c[0]})";
            }
            $s2[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', true);
        $at2 = new stack_cas_session2($s2, $options, 0);
        $at2->instantiate();

        $this->assertEquals('', $at2->get_errors());
        foreach ($tests as $key => $c) {
            $simpdisp = $c[2];
            if (array_key_exists(5, $c)) {
                $simpdisp = $c[5];
            }
            $dispval = $c[3];
            if (array_key_exists(6, $c)) {
                $dispval = $c[6];
            }
            $val = $c[4];
            if (array_key_exists(7, $c)) {
                $val = $c[7];
            }
            $this->assertEquals($simpdisp, $s2[$key]->get_display());
            $this->assertEquals($dispval, $s2[$key]->get_dispvalue());
            $this->assertEquals($val, $s2[$key]->get_value());
        }
    }

    public function test_pm_simp_false() {
        $cs = array('c0:a#pm#b',
            'c1:x = (-b #pm# sqrt(b^2-4*a*c))/(2*a)',
            'c2:b#pm#a^2',
            'c3:(b#pm#a)^2',
            'c4:#pm#a',
            'c5:#pm#a^2',
            'c6:#pm#sqrt(1-x)',
            'c7:(a#pm#b)^2',
            'c8:x = #pm#b',
            'c9:sin(x#pm#a)^2');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('a#pm#b', $s1[0]->get_value());
        $this->assertEquals('{a \pm b}',  $s1[0]->get_display());
        $this->assertEquals('x = (-b#pm#sqrt(b^2-4*a*c))/(2*a)',  $s1[1]->get_value());
        $this->assertEquals('x=\frac{{-b \pm \sqrt{b^2-4\cdot a\cdot c}}}{2\cdot a}', $s1[1]->get_display());
        $this->assertEquals('b#pm#a^2', $s1[2]->get_value());
        $this->assertEquals('{b \pm a^2}', $s1[2]->get_display());
        $this->assertEquals('(b#pm#a)^2', $s1[3]->get_value());
        $this->assertEquals('{\left({b \pm a}\right)}^2', $s1[3]->get_display());
        $this->assertEquals('"#pm#"(a)', $s1[4]->get_value());
        $this->assertEquals('\pm a', $s1[4]->get_display());
        $this->assertEquals('"#pm#"(a^2)', $s1[5]->get_value());
        $this->assertEquals('\pm a^2', $s1[5]->get_display());
        $this->assertEquals('"#pm#"(sqrt(1-x))', $s1[6]->get_value());
        $this->assertEquals('\pm \sqrt{1-x}', $s1[6]->get_display());
        $this->assertEquals('(a#pm#b)^2', $s1[7]->get_value());
        $this->assertEquals('{\left({a \pm b}\right)}^2', $s1[7]->get_display());
        $this->assertEquals('x = "#pm#"(b)', $s1[8]->get_value());
        $this->assertEquals('x= \pm b', $s1[8]->get_display());
        $this->assertEquals('sin(x#pm#a)^2', $s1[9]->get_value());
        $this->assertEquals('\sin ^2\left({x \pm a}\right)', $s1[9]->get_display());
    }

    public function test_pm_simp_true() {
        $cs = array('c1:a#pm#b',
            'c2:x=(-b #pm# sqrt(b^2-4*a*c))/(2*a)',
            'c3:b#pm#a^2',
            'c4:(b#pm#a)^2',
            'c5:#pm#a',
            'c6:#pm#a^2',
            'c7:#pm#sqrt(1-x)',
            'c8:(a#pm#b)^2',
            'c9:x=#pm#b',
            'c10:sin(x#pm#a)^2');

        foreach ($cs as $s) {
            $s1[] = stack_ast_container::make_from_student_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', true);

        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('a#pm#b', $s1[0]->get_value());
        $this->assertEquals('{a \pm b}', $s1[0]->get_display());
        $this->assertEquals('x = (-b#pm#sqrt(b^2-4*a*c))/(2*a)', $s1[1]->get_value());
        $this->assertEquals('x=\frac{{-b \pm \sqrt{b^2-4\cdot a\cdot c}}}{2\cdot a}', $s1[1]->get_display());
        $this->assertEquals('b#pm#a^2', $s1[2]->get_value());
        $this->assertEquals('{b \pm a^2}', $s1[2]->get_display());
        $this->assertEquals('(b#pm#a)^2', $s1[3]->get_value());
        $this->assertEquals('{\left({b \pm a}\right)}^2', $s1[3]->get_display());
        $this->assertEquals('"#pm#"(a)', $s1[4]->get_value());
        $this->assertEquals('\pm a', $s1[4]->get_display());
        $this->assertEquals('"#pm#"(a^2)', $s1[5]->get_value());
        $this->assertEquals('\pm a^2', $s1[5]->get_display());
        $this->assertEquals('"#pm#"(sqrt(1-x))', $s1[6]->get_value());
        $this->assertEquals('\pm \sqrt{1-x}', $s1[6]->get_display());
        $this->assertEquals('(a#pm#b)^2', $s1[7]->get_value());
        $this->assertEquals('{\left({a \pm b}\right)}^2', $s1[7]->get_display());
        $this->assertEquals('x = "#pm#"(b)', $s1[8]->get_value());
        $this->assertEquals('x= \pm b', $s1[8]->get_display());
        $this->assertEquals('sin(x#pm#a)^2', $s1[9]->get_value());
        $this->assertEquals('\sin ^2\left({x \pm a}\right)', $s1[9]->get_display());
    }

    public function test_logic_nouns() {
        // Nouns forms of logic operators are added by student validation.
        $s1 = array();
        $s1[] = stack_ast_container::make_from_teacher_source('p0:x=1 or x=2', '', new stack_cas_security(), array());
        $s1[] = stack_ast_container::make_from_student_source('p1:x=1 or x=2', '', new stack_cas_security(), array());
        $s1[] = stack_ast_container::make_from_teacher_source('p2:noun_logic_remove(p1)',
                '', new stack_cas_security(), array());
        $s1[] = stack_ast_container::make_from_teacher_source('p3:ev(p2)', '', new stack_cas_security(), array());

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        // Teachers must use nouns if the mean it, otherwise things get evaluated.
        $this->assertEquals('false', $s1[0]->get_value());
        // Students always have nouns.
        $this->assertEquals('x = 1 nounor x = 2', $s1[1]->get_value());
        // No extra evaluation at this point, but nouns have been removed.
        $this->assertEquals('x = 1 or x = 2', $s1[2]->get_value());
        // However, display forces an evaluation, and hence the result.
        $this->assertEquals('\mathbf{False}', $s1[2]->get_display());
        $this->assertEquals('false', $s1[3]->get_value());
    }

    public function test_natural_domain() {

        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();
        $cases[] = array('x', 'all', '\mathbb{R}');
        $cases[] = array('1/(x^2+1)', 'all', '\mathbb{R}');
        $cases[] = array('1/x', 'realset(x,%union(oo(0,inf),oo(-inf,0)))', '{x \not\in {\left \{0 \right \}}}');
        $cases[] = array('1+1/x^2+1/(x-1)', 'realset(x,%union(oo(0,1),oo(1,inf),oo(-inf,0)))',
                '{x \not\in {\left \{0 , 1 \right \}}}');
        $cases[] = array('1+1/x^2+1/(x-1)+3/(x-2)', 'realset(x,%union(oo(0,1),oo(1,2),oo(2,inf),oo(-inf,0)))',
                '{x \not\in {\left \{0 , 1 , 2 \right \}}}');
        $cases[] = array('log(x)', 'realset(x,oo(0,inf))', '{x \in {\left( 0,\, \infty \right)}}');

        foreach ($cases as $i => $case) {
            $s = 'd'.$i.':natural_domain('.$case[0].')';
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($cases as $i => $case) {
            $this->assertEquals($case[1], $s1[$i]->get_value());
            $this->assertEquals($case[2], $s1[$i]->get_display());
            $i++;
        }
    }

    public function test_union_tex() {

        // Cases should be in the form array('input value', 'display').
        $cases[] = array('%union(a,b,c)', 'a \cup b \cup c');
        $cases[] = array('%union(oo(1,2),oo(3,4),oo(4,5))',
            '\left( 1,\, 2\right) \cup \left( 3,\, 4\right) \cup \left( 4,\, 5\right)');
        $cases[] = array('%union(a,b+1,d)', 'a \cup \left(b+1\right) \cup d');
        $cases[] = array('%union({5,6})', '\left \{5 , 6 \right \}');

        $cases[] = array('%intersection(a,b,c)', 'a \cap b \cap c');
        $cases[] = array('%intersection(oo(1,2),oo(3,4),oo(4,5))',
            '\left( 1,\, 2\right) \cap \left( 3,\, 4\right) \cap \left( 4,\, 5\right)');
        $cases[] = array('%intersection(a,b+1,d)', 'a \cap \left(b+1\right) \cap d');
        $cases[] = array('%intersection({5,6})', '\left \{5 , 6 \right \}');

        // Add brackets.  Even when not strictly needed.
        $cases[] = array('%union({1,2},A,B,%intersection(C,D,E))',
            '\left \{1 , 2 \right \} \cup A \cup B \cup \left(C \cap D \cap E\right)');
        $cases[] = array('%intersection({1,2},A,B,%union(C,D,E))',
            '\left \{1 , 2 \right \} \cap A \cap B \cap \left(C \cup D \cup E\right)');

        foreach ($cases as $i => $case) {
            $s = 'd'.$i.':'.$case[0];
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($cases as $i => $case) {
            $this->assertEquals($case[0], $s1[$i]->get_value());
            $this->assertEquals($case[1], $s1[$i]->get_display());
            $i++;
        }
    }

    public function test_stack_disp_comma_separate() {

        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();
        // Note in this case we do output Maxima's "%pi", not just pi.
        $cases[] = array('[a,b,sin(pi/7)]', '"a, b, sin(%pi/7)"', '\\mbox{a, b, sin(\\%pi/7)}');

        foreach ($cases as $i => $case) {
            $s = 'd'.$i.':stack_disp_comma_separate('.$case[0].')';
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($cases as $i => $case) {
            $this->assertEquals($case[1], $s1[$i]->get_value());
            $this->assertEquals($case[2], $s1[$i]->get_display());
            $i++;
        }
    }

    public function test_stack_disp_innards_ntuple() {

        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();
        $cases[] = array('ntuple(a,b,c,dotdotdot)', 'ntuple(a,b,c,dotdotdot)', '\\left(a, b, c, \\ldots\\right)');
        $cases[] = array('sequenceify([a,b,c])', 'sequence(a,b,c)', 'a, b, c');

        foreach ($cases as $i => $case) {
            $s = 'd'.$i.':'.$case[0];
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($cases as $i => $case) {
            $this->assertEquals($case[1], $s1[$i]->get_value());
            $this->assertEquals($case[2], $s1[$i]->get_display());
            $i++;
        }
    }

    public function test_stack_stackintfmt() {

        // Cases should be in the form array('input', 'value', 'display').
        $cases = array();

        $cases[] = array('73', '73', '73');
        $cases[] = array('(stackintfmt:"~2r",n0)', '73', '1001001');
        $cases[] = array('(stackintfmt:"~7r",n0)', '73', '133');
        $cases[] = array('(stackintfmt:"~r",n0)', '73', '\mbox{seventy-three}');
        $cases[] = array('(stackintfmt:"~:r",n0)', '73', '\mbox{seventy-third}');
        $cases[] = array('(stackintfmt:"~@R",n0)', '73', 'LXXIII');

        foreach ($cases as $i => $case) {
            $s = 'n' . $i . ':' . $case[0];
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $s = new stack_cas_session2($s1, $options, 0);
        $s->instantiate();

        foreach ($cases as $i => $case) {
            $this->assertEquals($case[1], $s1[$i]->get_value());
            $this->assertEquals($case[2], $s1[$i]->get_display());
            $i++;
        }
    }

    public function test_stack_stack_equiv_find_step() {

        $r1 = array('ta:[lg(25,5),stackeq(lg(5^2,5)),stackeq(2*lg(5,5)),stackeq(2*1),stackeq(2)]',
            'sa1:[lg(25,5),stackeq(lg(5^2,5)),stackeq(2)]',
            'sa0:[lg(25,5),stackeq(2)]'
        );
        foreach ($r1 as $s) {
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $t1 = array();
        $t1[] = array('stack_equiv_find_step(stackeq(2*lg(5,5)), ta)', '[3]');
        $t1[] = array('stack_equiv_find_step(2*lg(5,5), ta)', '[3]');
        $t1[] = array('stack_equiv_find_step(stackeq(lg(5,5)), ta)', '[]');
        $t1[] = array('stack_equiv_find_step(stackeq(lg(5^2,5)), sa1)', '[2]');
        $t1[] = array('stack_equiv_find_step(lg(5^2,5), sa1)', '[2]');
        $t1[] = array('stack_equiv_find_step(stackeq(lg(5^2,5)), sa0)', '[]');
        $t1[] = array('stack_equiv_find_step(lg(5^2,5), sa0)', '[]');

        foreach ($t1 as $i => $case) {
            $s = 'n' . $i . ':' . $case[0];
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $s = new stack_cas_session2($s1, $options, 0);
        $s->instantiate();

        $i = 3;
        foreach ($t1 as $t) {
            $this->assertEquals($t[1], $s1[$i]->get_value());
            $i++;
        }
    }

    public function test_dispvalue() {
        // These are tests of test-case generation.

        $tests = array(
            array('x=dispdp(0.5,3)', 'x=0.500', 'x = 0.500', 'x = displaydp(0.5,3)'),
            array('x=1 nounor x=3.75E3', 'x=1\,{\mbox{ or }}\, x=3750.0',
                    'x = 1 or x = 3750.0', 'x = 1 nounor x = 3750.0'),
            array('[x^2-1,stackeq((x-1)*(x+1))]', '\left[ x^2-1 , =\left(x-1\right)\cdot \left(x+1\right) \right]',
                    '[x^2-1,=(x-1)*(x+1)]', '[x^2-1,stackeq((x-1)*(x+1))]'),
            array('nounint(sin(pi*x),x)', '\int {\sin \left( \pi\cdot x \right)}{\;\mathrm{d}x}',
                    'int(sin(%pi*x),x)', '\'int(sin(%pi*x),x)'),
            // Teachers may now use 'int(...) in STACK 4.3.
            // We should probably just get rid of "nounint" in the near future.
            array('\'int(cos(x),x)', '\int {\cos \left( x \right)}{\;\mathrm{d}x}',
                    'int(cos(x),x)', '\'int(cos(x),x)'),
        );

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source("p{$key}:{$c[0]}",
                '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();
        foreach ($tests as $key => $c) {
            $cs = $at1->get_by_key('p'.$key);
            $this->assertEquals($c[1], $cs->get_display());
            $this->assertEquals($c[2], $cs->get_dispvalue());
            $this->assertEquals($c[3], $cs->get_value());
        }
    }

    public function test_stack_strip_percent() {
        $tests = array('assume(x>0)',
                  'eq1:x^2*\'diff(y,x)+3*y*x=sin(x)/x',
                  'sol1:ode2(eq1,y,x)',
                  'sol2:stack_strip_percent(y=(%c-cos(x))/x^3,k)',
                  'sol3:stack_strip_percent(y=(%c-cos(x))/x^3,[k])'
                );

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('y = (%c-cos(x))/x^3', $s1[2]->get_value());
        $this->assertEquals('y = (k[1]-cos(x))/x^3', $s1[3]->get_value());
        $this->assertEquals('y = (k-cos(x))/x^3', $s1[4]->get_value());
    }

    public function test_stack_quantile_gamma() {
        // This command has lisp throw an error.
        $tests = array('p:quantile_gamma(0.5,26896/81,81/164)');

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $actual = $s1[0]->get_dispvalue();
        if (strpos($actual, 'quantile_gamma(') !== false) {
            // Seems that the distrib package is not available. Skip this test.
            $this->markTestSkipped('Skipping because it seems the distrib package is not installed.');
        }
        $this->assertEquals('163.835395267', $actual);
    }

    public function test_stack_parse_inequalities() {
        // This command has lisp throw an error.
        $tests = array('f(x) := if x < 0 then (if x < 1 then 1 else 2) else 3;', 'v1:f(-5);', 'v2:f(4);');

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('1', $s1[1]->get_dispvalue());
        $this->assertEquals('3', $s1[2]->get_dispvalue());
        $this->assertEquals('f(x):=if x < 0 then (if x < 1 then 1 else 2) else 3', $s1[0]->get_value());
    }

    public function test_stack_rat() {
        $tests = array('s1 : 8.5*sin(rat(2*pi*((0.37/1.86440677966E-4)-(5.8/0.22))))', 's2:1',
                'm:MAXIMA_VERSION_NUM');

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $maximaversion = $s1[2]->get_value();
        if ($maximaversion == '34.1') {
            $this->assertEquals('8.5*sin(66940295262037*%pi/17092461650)', $s1[0]->get_dispvalue());
        } else {
            $this->assertEquals('8.5*sin((66940295262037*%pi)/17092461650)', $s1[0]->get_dispvalue());
        }
    }

    public function test_stack_pmpoly() {
        $tests = array('s1:-(4*x^7)+3*x^5-2*x^3+x',
                       'p1:-a/b', 'p2:(-a)/b', 'p3:-(a/b)',
                       'pm1:a#pm#b'
        );

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('-(4*x^7)+3*x^5+(-2)*x^3+x', $s1[0]->get_value());
        $this->assertEquals('-(4*x^7)+3*x^5-2*x^3+x', $s1[0]->get_dispvalue());
        $this->assertEquals('-4\cdot x^7+3\cdot x^5-2\cdot x^3+x', $s1[0]->get_display());

        $this->assertEquals('(-a)/b', $s1[1]->get_value());
        $this->assertEquals('(-a)/b', $s1[1]->get_dispvalue());
        $this->assertEquals('\frac{-a}{b}', $s1[1]->get_display());

        $this->assertEquals('(-a)/b', $s1[2]->get_value());
        $this->assertEquals('(-a)/b', $s1[2]->get_dispvalue());
        $this->assertEquals('\frac{-a}{b}', $s1[2]->get_display());

        $this->assertEquals('-(a/b)', $s1[3]->get_value());
        $this->assertEquals('-(a/b)', $s1[3]->get_dispvalue());
        $this->assertEquals('-\frac{a}{b}', $s1[3]->get_display());

        $this->assertEquals('a#pm#b', $s1[4]->get_value());
        $this->assertEquals('a+-b', $s1[4]->get_dispvalue());
        $this->assertEquals('{a \pm b}', $s1[4]->get_display());
    }

    public function test_stack_pm_maximaoutput() {
        $tests = array('a*b+c*d+-A*B');

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $expected = '([Root] ([Op: +] ([Op: *] ([Id] a), ([Id] b)), ' .
                '([Op: +-] ([Op: *] ([Id] c), ([Id] d)), ([Op: *] ([Id] A), ([Id] B)))))';
        $this->assertEquals($expected, $s1[0]->get_ast_test());

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('(a*b+c*d)#pm#A*B', $s1[0]->get_value());
        $this->assertEquals('(a*b+c*d)+-A*B', $s1[0]->get_dispvalue());
        $this->assertEquals('{a\cdot b+c\cdot d \pm A\cdot B}',
                $s1[0]->get_display());

        // The evaluated form contains the +- operator.
        $expected = '([Op: #pm#] ([Group] ([Op: +] ([Op: *] ([Id] a), ([Id] b)), ' .
                '([Op: *] ([Id] c), ([Id] d)))), ([Op: *] ([Id] A), ([Id] B)))';
        $this->assertEquals($expected, $s1[0]->get_ast_test());
    }

    public function test_stack_pm_taylor() {
        $tests = array('ta:ev(taylor(10*cos(2*x),x,%pi/4,2),simp)');

        foreach ($tests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source($c,
                    '', new stack_cas_security(), array());
        }

        $expected = '([Root] ([Op: :] ([Id] ta), ([FunctionCall: ([Id] ev)] ' .
            '([FunctionCall: ([Id] taylor)] ([Op: *] ([Int] 10), ' .
            '([FunctionCall: ([Id] cos)] ([Op: *] ([Int] 2), ([Id] x)))),([Id] x),([Op: /] ' .
            '([Id] %pi), ([Int] 4)),([Int] 2)),([Id] simp))))';
        $this->assertEquals($expected, $s1[0]->get_ast_test());

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        $this->assertEquals('+-(20*(x-%pi/4))', $s1[0]->get_value());
        $this->assertEquals('+-(20*(x-%pi/4))', $s1[0]->get_dispvalue());
        $this->assertEquals('+\left(-20\cdot \left(x-\frac{\pi}{4}\right)\right)+\cdots',
                $s1[0]->get_display());

        // The evaluated form does not contain the explicit +- operator.
        $expected = '([PrefixOp: +] ([PrefixOp: -] ([Group] ([Op: *] ([Int] 20), ([Group] ([Op: /] ' .
            '([Op: -] ([Id] x), ([Id] %pi)), ([Int] 4)))))))';
        $this->assertEquals($expected, $s1[0]->get_ast_test());
    }

    public function test_stack_scientific_notationp() {
        $truetests = array('3*10^2',
                '3.1*10^2',
                '3*10^-2',
                '3.3*10^2',
                '3.0*10^2',
                '-3*10^2',
                '-3.1*10^2',
                '-3*10^-2',
                /* Special edge case. */
                '3.3*10',
        );

        $s1 = array();
        foreach ($truetests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source('scientific_notationp(' . $c . ')',
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($s1 as $key => $test) {
            $this->assertEquals('true', $test->get_value());
        }

            $truetests = array('3',
                    '-3',
                    '3.1',
                    '3E-5',
                    '310^-2',
                    'a',
                    '312/1000',
                    '3.3*x',
                    '3.3*sin(x)',
                    '3/9*10^2',
                    '3.3*10^2.78',
                    '3.3*10^x',
                    '3.3*a^2',
                    '3.3*7^2',
            );

        $s1 = array();
        foreach ($truetests as $key => $c) {
            $s1[] = stack_ast_container::make_from_teacher_source('scientific_notationp(' . $c . ')',
                    '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $options->set_option('simplify', false);
        $at1 = new stack_cas_session2($s1, $options, 0);
        $at1->instantiate();

        foreach ($s1 as $key => $test) {
            $this->assertEquals('false', $test->get_value());
        }
    }

    public function test_stack_regex_match_exactp() {

        $t1 = array();
        $t1[] = array('regex_match_exactp("(aaa)*(b|d)c", "aaaaaabc")', 'true');
        $t1[] = array('regex_match_exactp("(aaa)*(b|d)c", "dc")', 'true');
        $t1[] = array('regex_match_exactp("(aaa)*(b|d)c", "aaaaaaabc")', 'false');
        $t1[] = array('regex_match_exactp("(aaa)*(b|d)c", "aaaaaaabc")', 'false');

        foreach ($t1 as $i => $case) {
            $s = 'n' . $i . ':' . $case[0];
            $s1[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $s = new stack_cas_session2($s1, $options, 0);
        $s->instantiate();

        foreach ($t1 as $i => $t) {
            $this->assertEquals($t[1], $s1[$i]->get_value());
        }
    }

    public function test_stack_at_units_sigfigs_wrapper() {

        $t1 = array();
        $t1[] = array('simp:false', 'false');
        $t1[] = array('node_result:ATUnitsSigFigs_CASSigFigsWrapper(1*kg,1000*g,[1,3],"1 kg",false)',
            '[true,true,"ATUnits_compatible_units kg. ",""]');

        foreach ($t1 as $i => $case) {
            $s1[] = stack_ast_container::make_from_teacher_source($case[0], '', new stack_cas_security(), array());
        }

        $options = new stack_options();
        $s = new stack_cas_session2($s1, $options, 0);
        $s->instantiate();

        foreach ($t1 as $i => $t) {
            $this->assertEquals($t[1], $s1[$i]->get_value());
        }
    }
}
