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

require_once(__DIR__ . '/../stack/potentialresponsetree.class.php');
require_once(__DIR__ . '/../stack/cas/castext.class.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for stack_potentialresponse_node.
//
// @copyright 2012 The University of Birmingham.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_potentialresponse_node_test extends qtype_stack_testcase {

    public function test_constructor() {
        $sans = new stack_cas_casstring('x^2+2*x+1');
        $tans = new stack_cas_casstring('(x+1)^2');
        $tans->get_valid('t');
        $options = new stack_options();
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', false);
        $node->add_branch(0, '=', 0, '', -1, '', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', FORMAT_HTML, '1-0-1');

        $this->assertEquals($sans, $node->sans);
        $this->assertEquals($tans, $node->tans);
    }

    public function test_do_test_pass() {
        $sans = new stack_cas_casstring('x^2+2*x+1');
        $tans = new stack_cas_casstring('(x+1)^2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', false);
        $node->add_branch(0, '=', 0, '', -1, 'Boo!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 2, '', 3, 'Yeah!', FORMAT_HTML, '1-0-1');

        $options = new stack_options();
        $result = new stack_potentialresponse_tree_state(1);
        $nextnode = $node->do_test('x^2+2*x+1', '(x+1)^2', '', $options, $result);

        $this->assertEquals(true, $result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(1, count($result->feedback));
        $this->assertEquals('Yeah!', $result->feedback[0]->feedback);
        $this->assertEquals(3, $nextnode);
    }

    public function test_do_test_fail() {
        $sans = new stack_cas_casstring('x^2+2*x-1');
        $tans = new stack_cas_casstring('(x+1)^2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', false);
        $node->add_branch(0, '=', 0, '', -1, 'Boo!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 2, '', 3, 'Yeah!', FORMAT_HTML, '1-0-1');

        $options = new stack_options();
        $result = new stack_potentialresponse_tree_state(1);
        $nextnode = $node->do_test('x^2+2*x-1', '(x+1)^2', '', $options, $result);

        $this->assertEquals(true, $result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(1, count($result->feedback));
        $this->assertEquals('Boo!', $result->feedback[0]->feedback);
        $this->assertEquals(-1, $nextnode);
    }

    public function test_do_test_cas_error() {
        $sans = new stack_cas_casstring('x^2+2*x-1');
        $tans = new stack_cas_casstring('(x+1)^2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', false);
        $node->add_branch(0, '=', 0, '', -1, 'Boo!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 2, '', 3, 'Yeah!', FORMAT_HTML, '1-0-1');

        $options = new stack_options();
        $result = new stack_potentialresponse_tree_state(1);
        $nextnode = $node->do_test('1/0', '(x+1)^2', '', $options, $result);

        $this->assertEquals(false, $result->valid);
        $this->assertNotEquals('', $result->errors);
        $this->assertEquals(2, count($result->feedback));
        $this->assertRegExp('~The answer test failed to execute correctly: ' .
                'please alert your teacher. Division by (zero\.|0)~',
                $result->feedback[0]->feedback);
        $this->assertEquals('Boo!', $result->feedback[1]->feedback);
        $this->assertEquals(-1, $nextnode);
    }

    public function test_do_test_pass_atoption() {
        $sans = new stack_cas_casstring('(x+1)^2');
        $tans = new stack_cas_casstring('(x+1)^2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'FacForm', 'x', false);
        $node->add_branch(0, '=', 0, '', -1, 'Boo!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 2, '', 3, 'Yeah!', FORMAT_HTML, '1-0-1');

        $options = new stack_options();
        $result = new stack_potentialresponse_tree_state(1);
        $nextnode = $node->do_test('(x+1)^2', '(x+1)^2', 'x', $options, $result);

        $this->assertEquals(true, $result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(1, count($result->feedback));
        $this->assertEquals('Yeah!', $result->feedback[0]->feedback);
        $this->assertEquals(3, $nextnode);
    }

    public function test_do_test_fail_atoption() {
        $sans = new stack_cas_casstring('ans1');
        $tans = new stack_cas_casstring('3*(x+2)');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'FacForm', 'x', false);
        $node->add_branch(0, '=', 0, '', -1, 'Boo!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 2, '', 3, 'Yeah!', FORMAT_HTML, '1-0-1');

        $options = new stack_options();
        $result = new stack_potentialresponse_tree_state(1);
        $nextnode = $node->do_test('3*x+6', '3*(x+2)', 'x', $options, $result);

        $this->assertEquals(true, $result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(2, count($result->feedback));
        $this->assertEquals('Your answer is not factored. You need to take out a common factor.',
                $result->feedback[0]->feedback);
        $this->assertEquals('Boo!', $result->feedback[1]->feedback);

    }

    public function test_do_test_fail_quiet() {
        $sans = new stack_cas_casstring('ans1');
        $tans = new stack_cas_casstring('3*(x+2)');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'FacForm', 'x', true);
        $node->add_branch(0, '+', 0.5, '', -1, 'Boo! Your answer should be in factored form, i.e. {@factor(ans1)@}.',
                FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 2, '', 3, 'Yeah!', FORMAT_HTML, '1-0-1');

        $options = new stack_options();
        $result = new stack_potentialresponse_tree_state(1, true, 1);
        $nextnode = $node->do_test('3*x+6', '3*(x+2)', 'x', $options, $result);

        $this->assertEquals(1, count($result->feedback));
        $this->assertEquals('Boo! Your answer should be in factored form, i.e. {@factor(ans1)@}.',
                $result->feedback[0]->feedback);

        $this->assertEquals(1.5, $result->score);

        $data = array('factor(ans1)', 'ans1', '3*(x+2)', 'x');
        $this->assertEquals($data, $node->get_required_cas_strings());
    }
}
