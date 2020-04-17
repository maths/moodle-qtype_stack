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
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for stack_potentialresponse_tree.
//
// @copyright 2012 The University of Birmingham.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_potentialresponsetree_test extends qtype_stack_testcase {

    public function test_do_test_pass() {

        $sans = stack_ast_container::make_from_teacher_source('sans');
        $sans->get_valid();
        $tans = stack_ast_container::make_from_teacher_source('(x+1)^3/3+c');
        $tans->get_valid();
        $node = new stack_potentialresponse_node($sans, $tans, 'Int', 'x', false);
        $node->add_branch(0, '=', 0, '', -1, 'Boo!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 1, '', -1, 'Yeah!', FORMAT_HTML, '1-0-1');

        $potentialresponses[] = $node;

        $tree = new stack_potentialresponse_tree('', '', true, 5, null, $potentialresponses, '0', 1);

        $questionvars = new stack_cas_session2(array());
        $options = new stack_options();
        $answers = array('sans' => '(x+1)^3/3+c');
        $seed = 12345;
        $result = $tree->evaluate_response($questionvars, $options, $answers, $seed);

        $this->assertTrue($result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(1, $result->score);
        $this->assertEquals(0, $result->penalty);
        $this->assertEquals(1, count($result->feedback));
        $this->assertEquals('Yeah!', $result->feedback[0]->feedback);
        $this->assertEquals(array('ATInt_true.', '1-0-1'), $result->answernotes);
        $this->assertEquals(array('NULL' => 'NULL', '1-0-1' => '1-0-1', '1-0-0' => '1-0-0'), $tree->get_all_answer_notes());
    }

    public function test_do_test_2() {

        $sans = stack_ast_container::make_from_teacher_source('sans');
        $sans->get_valid();
        $tans = stack_ast_container::make_from_teacher_source('ta');
        $tans->get_valid();
        $node = new stack_potentialresponse_node($sans, $tans, 'Diff', 'x', false);
        $node->add_branch(0, '=', 0, '', -1, 'Can not diff!', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 1, '', 1, 'Ok, you can diff. ', FORMAT_HTML, '1-0-1');
        $potentialresponses[] = $node;

        $sans = stack_ast_container::make_from_teacher_source('sans');
        $sans->get_valid();
        $tans = stack_ast_container::make_from_teacher_source('ta');
        $tans->get_valid();
        $node = new stack_potentialresponse_node($sans, $tans, 'FacForm', 'x', true);
        $node->add_branch(0, '+', 0, '', -1, 'Do not expand!', FORMAT_HTML, '1-1-0');
        $node->add_branch(1, '+', 0, '', -1, 'Yeah!', FORMAT_HTML, '1-1-1');
        $potentialresponses[] = $node;

        $tree = new stack_potentialresponse_tree('', '', true, 5, null, $potentialresponses, '0', 1);

        $seed = 12345;
        $options = new stack_options();
        $questionvars = new stack_cas_keyval('n:3; p:(x+1)^n; ta:diff(p,x);', $options, $seed);
        $questionvars->instantiate();

        $answers = array('sans' => '3*x^2+6*x+3');
        $result = $tree->evaluate_response($questionvars->get_session(), $options, $answers, $seed);

        $this->assertTrue($result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(1, $result->score);
        $this->assertEquals(0, $result->penalty);
        $this->assertEquals(2, count($result->feedback));
        $this->assertEquals('Ok, you can diff.', $result->feedback[0]->feedback);
        $this->assertEquals('Do not expand!', $result->feedback[1]->feedback);
        $this->assertEquals(array('ATDiff_true.', '1-0-1', 'ATFacForm_notfactored.', '1-1-0'), $result->answernotes);

        // Now have another attempt at the same PRT!
        // Need this test to ensure PRT is "reset" and has no hangover data inside the potential resposnes.
        $answers = array('sans' => '3*(x+1)^2');
        $result = $tree->evaluate_response($questionvars->get_session(), $options, $answers, $seed);

        $this->assertTrue($result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(1, $result->score);
        $this->assertEquals(0, $result->penalty);
        $this->assertEquals(2, count($result->feedback));
        $this->assertEquals('Ok, you can diff.', $result->feedback[0]->feedback);
        $this->assertEquals('Yeah!', $result->feedback[1]->feedback);

        $this->assertEquals(array('ATDiff_true.', '1-0-1', 'ATFacForm_true.', '1-1-1'), $result->answernotes);
        $this->assertEquals(array('NULL' => 'NULL', '1-0-1' => '1-0-1', '1-0-0' => '1-0-0',
                '1-1-1' => '1-1-1', '1-1-0' => '1-1-0'), $tree->get_all_answer_notes());
    }

    public function test_do_test_3() {

        // Nontrivial use of the feeback variables.
        // Error in authoring ends up in loop.   STACK should bail.
        $options = new stack_options();
        $seed = 12345;

        $questionvars = new stack_cas_keyval('n:3; p:(x+1)^n; ta:p;', $options, $seed);

        // Feeback variables.
        $cstrings = array('sa1:sans', 'sa2:expand(sans)');
        foreach ($cstrings as $s) {
            $cs = stack_ast_container::make_from_teacher_source($s);
            $cs->get_valid();
            $s1[] = $cs;
        }
        $feedbackvars = new stack_cas_session2($s1, $options, $seed);
        $feedbackvars->get_valid();

        // Define the tree itself.
        $sans = stack_ast_container::make_from_teacher_source('sa1');
        $sans->get_valid();
        $tans = stack_ast_container::make_from_teacher_source('ta');
        $tans->get_valid();
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node->add_branch(0, '=', 0, '', -1, 'Test 1 false. Look: \[{@(sa1)^2@} \neq {@(sa2)^2@}\]', FORMAT_HTML, '1-0-0');
        $node->add_branch(1, '=', 1, '', 1, 'Test 1 true. ', FORMAT_HTML, '1-0-1');
        $potentialresponses[] = $node;

        $sans = stack_ast_container::make_from_teacher_source('sa2');
        $sans->get_valid();
        $tans = stack_ast_container::make_from_teacher_source('ta');
        $tans->get_valid();
        $node = new stack_potentialresponse_node($sans, $tans, 'FacForm', 'x', true);
        $node->add_branch(0, '-', 0.7, '', 0, 'Test 2 false.', FORMAT_HTML, '1-1-0');
        $node->add_branch(1, '+', 1, '', 3, 'Test 2 true', FORMAT_HTML, '1-1-1');
        $potentialresponses[] = $node;

        $tree = new stack_potentialresponse_tree('', '', true, 5, $feedbackvars, $potentialresponses, '0', 1);

        // Some data from students.
        $answers = array('sans' => '(x+1)^3');
        $result = $tree->evaluate_response($questionvars->get_session(), $options, $answers, $seed);

        $this->assertTrue($result->valid);
        $this->assertEquals('', $result->errors);
        $this->assertEquals(0.3, $result->score);
        $this->assertEquals(0, $result->penalty);
        $this->assertEquals(2, count($result->feedback));
        $this->assertEquals('Test 1 true.', $result->feedback[0]->feedback);
        $this->assertEquals('Test 2 false.', $result->feedback[1]->feedback);
        $this->assertEquals(array('1-0-1', 'ATFacForm_notfactored.', '1-1-0', '[PRT-CIRCULARITY]=0'), $result->answernotes);

        $this->assertEquals(array('sa1', 'ta'), $tree->get_required_variables(array('sa1', 'sa3', 'ta', 'ssa1', 'a1', 't')));
    }
}
