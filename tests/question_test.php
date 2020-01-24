<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for (some of) question/type/stack/questiontype.php.
//
// @copyright 2008 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class qtype_stack_question_test extends qtype_stack_testcase {
    /**
     * @return qtype_stack_question the requested question object.
     */
    protected function get_test_stack_question($which = null) {
        return test_question_maker::make_question('stack', $which);
    }

    public function test_get_expected_data() {
        $q = $this->get_test_stack_question();
        $this->assertEquals(array('ans1' => PARAM_RAW, 'ans1_val' => PARAM_RAW), $q->get_expected_data());
    }

    public function test_get_expected_data_test3() {
        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion());
        $this->assertEquals(array('ans1' => PARAM_RAW, 'ans1_val' => PARAM_RAW,
                'ans2' => PARAM_RAW, 'ans2_val' => PARAM_RAW, 'ans3' => PARAM_RAW, 'ans3_val' => PARAM_RAW,
                'ans4' => PARAM_RAW), $q->get_expected_data());
    }

    public function test_get_correct_response_test0() {
        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('ans1' => '2', 'ans1_val' => '2'), $q->get_correct_response());
    }

    public function test_get_correct_response_test1() {
        $q = $this->get_test_stack_question('test1');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('ans1' => '(x-7)^4/4+c', 'ans1_val' => '(x-7)^4/4+c'),
                $q->get_correct_response());
    }

    public function test_get_correct_response_test3() {
        $q = $this->get_test_stack_question('test3');
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('ans1' => 'x^3', 'ans2' => 'x^4', 'ans3' => '0', 'ans4' => 'true',
            'ans1_val' => 'x^3', 'ans2_val' => 'x^4', 'ans3_val' => '0'),
                $q->get_correct_response());
    }

    public function test_get_is_same_response_test0() {
        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion());

        $this->assertFalse($q->is_same_response(array(), array('ans1' => '2')));
        $this->assertTrue($q->is_same_response(array('ans1' => '2'), array('ans1' => '2')));
        $this->assertFalse($q->is_same_response(array('_seed' => '123'), array('ans1' => '2')));
        $this->assertFalse($q->is_same_response(array('ans1' => '2'), array('ans1' => '3')));
    }

    public function test_get_is_same_response_for_part_test3() {
        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($q->is_same_response_for_part('oddeven', array('ans3' => 'x'), array('ans3' => 'x')));
        $this->assertTrue($q->is_same_response_for_part('oddeven', array('ans1' => 'x', 'ans3' => 'x'),
                array('ans1' => 'y', 'ans3' => 'x')));
        $this->assertFalse($q->is_same_response_for_part('oddeven', array('ans3' => 'x'), array('ans3' => 'y')));
    }

    public function test_is_complete_response_test0() {
        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_complete_response(array()));
        $this->assertFalse($q->is_complete_response(array('ans1' => '2')));
        $this->assertTrue($q->is_complete_response(array('ans1' => '2', 'ans1_val' => '2')));
    }

    public function test_is_gradable_response_test0() {
        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_gradable_response(array()));
        $this->assertTrue($q->is_gradable_response(array('ans1' => '2')));
        $this->assertTrue($q->is_gradable_response(array('ans1' => '2', 'ans1_val' => '2')));
    }

    public function test_is_gradable_response_test3() {
        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_gradable_response(array()));
        $this->assertTrue($q->is_gradable_response(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false')));
        $this->assertTrue($q->is_gradable_response(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x',
                'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => 'x', 'ans4' => 'false')));
    }

    public function test_is_complete_response_test3() {
        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_complete_response(array()));
        $this->assertFalse($q->is_complete_response(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false')));
        $this->assertFalse($q->is_complete_response(array('ans1' => 'x+1', 'ans1_val' => 'x+1',
                'ans2' => 'x+1', 'ans2_val' => 'x+1', 'ans3' => 'x+1', 'ans3_val' => 'x+1', 'ans4' => '')));
        $this->assertTrue($q->is_complete_response(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x',
                'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => 'x', 'ans4' => 'false')));
    }

    public function test_is_complete_response_divide() {
        $q = $this->get_test_stack_question('divide');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_complete_response(array('ans1' => '0')));
        // This response is not 'complete' because it causes CAS errors.
        $this->assertFalse($q->is_complete_response(array('ans1' => '0', 'ans1_val' => '0')));
    }

    public function test_grade_response_test3() {
        $q = $this->get_test_stack_question('test3');
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(2.5 / 4, question_state::$gradedpartial),
                $q->grade_response(array('ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false')));
    }

    public function test_grade_response_test3_incomplete() {
        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        // Response that has three parts wrong, and one not completed.
        $this->assertEquals(array(0 / 4, question_state::$gradedwrong),
                $q->grade_response(array('ans1' => '1 + x', 'ans2' => '1 + x', 'ans3' => '1 + x', 'ans4' => '')));
    }

    public function test_grade_response_divide() {
        $q = $this->get_test_stack_question('divide');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
                $q->grade_response(array('ans1' => '1/2')));
    }

    public function test_grade_response_will_not_accept_input_name() {
        $q = $this->get_test_stack_question('divide');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $q->grade_response(['ans1' => 'ans1']));
    }

    public function test_grade_parts_that_can_be_graded() {
        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 4);

        $response = array('ans1' => '(x', 'ans2' => '(x', 'ans3' => 'x+1', 'ans4' => 'false',
                'ans1_val' => 'x^3', 'ans3_val' => 'x');
        $lastgradedresponses = array(
            'odd'     => array('ans1' => 'x^3', 'ans2' => '', 'ans3' => 'x', 'ans4' => '', 'ans1_val' => 'x^3', 'ans3_val' => 'x'),
            'oddeven' => array('ans1' => 'x^3', 'ans2' => '', 'ans3' => 'x', 'ans4' => '', 'ans1_val' => 'x^3', 'ans3_val' => 'x'),
        );
        $partscores = $q->grade_parts_that_can_be_graded($response, $lastgradedresponses, false);

        $expected = array(
            'unique' => new qbehaviour_adaptivemultipart_part_result('unique', 0, 1),
        );
        $this->assertEquals($expected, $partscores);
    }

    public function test_classify_response_test0() {
        $q = test_question_maker::make_question('stack', 'test0');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 4);

        $expected = array(
            'firsttree-0' => new question_classified_response('firsttree-1-F',
                    'ATEqualComAss (AlgEquiv-false). | firsttree-1-F', 0),
        );
        $this->assertEquals($expected, $q->classify_response(array('ans1' => '7')));

        $expected = array(
            'firsttree-0' => new question_classified_response('firsttree-1-T', 'firsttree-1-T', 1),
        );
        $this->assertEquals($expected, $q->classify_response(array('ans1' => '2')));

        $expected = array(
            'firsttree-0' => question_classified_response::no_response(),
        );
        $this->assertEquals($expected, $q->classify_response(array('ans1' => '')));

    }

    public function test_classify_response_test3() {
        $q = test_question_maker::make_question('stack', 'test3');
        $q->start_attempt(new question_attempt_step(), 4);

        $this->assertTrue(true);
    }

    public function test_get_question_var_values0() {
        $q = test_question_maker::make_question('stack', 'test2');
        $this->assertEquals('', $q->validate_against_stackversion());
        $q->start_attempt(new question_attempt_step(), 4);

        $expected = "a:3;\nb:9;\nta:y+x;";
        $s = $q->get_session();
        $this->assertEquals($expected, $s->get_keyval_representation(true));
    }

    public function test_question_addrow() {
        $q = test_question_maker::make_question('stack', 'addrow');
        $expected = 'This question uses addrow in the Question variables, which changed in STACK version ' .
             '2018060601 and is no longer supported. An alternative is rowadd. ';
        $expected .= 'This question uses addrow in the Feedback variables (firsttree), which changed in STACK version ' .
             '2018060601 and is no longer supported. An alternative is rowadd. ';
        $expected .= 'This question uses texdecorate in the Question variables, which changed in STACK version ' .
             '2018080600 and is no longer supported.';

        $this->assertEquals($expected, $q->validate_against_stackversion());
    }

    public function test_question_mul() {
        $q = test_question_maker::make_question('stack', 'mul');
        $expected = 'This question has an input which uses the "mul" option, '
            .'which is not suppored after STACK version 4.2.  Please edit this question.';

        $this->assertEquals($expected, $q->validate_against_stackversion());
    }
}
