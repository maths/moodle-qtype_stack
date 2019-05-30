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
        $this->assertContains('expt: undefined: 0 to a negative exponent.', $divzero->get_errors(true));
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

        // If our PRT requires simplification
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


}
