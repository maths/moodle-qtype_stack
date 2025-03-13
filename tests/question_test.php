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

namespace qtype_stack;

use qtype_stack_question;
use context_system;
use qtype_stack_testcase;
use test_question_maker;
use question_attempt_step;
use question_state;
use qbehaviour_adaptivemultipart_part_result;
use question_classified_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');

/**
 * Unit tests for (some of) question/type/stack/questiontype.php.
 *
 * @package    qtype_stack
 * @copyright 2008 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \qtype_stack_question
 */
final class question_test extends qtype_stack_testcase {
    /**
     * Add description here.
     * @return qtype_stack_question the requested question object.
     */
    protected function get_test_stack_question($which = null) {
        return test_question_maker::make_question('stack', $which);
    }

    public function test_get_expected_data(): void {

        $q = $this->get_test_stack_question();
        $this->assertEquals(['ans1' => PARAM_RAW, 'ans1_val' => PARAM_RAW, 'step_lang' => PARAM_RAW], $q->get_expected_data());
    }

    public function test_get_expected_data_test3(): void {

        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $this->assertEquals(['ans1' => PARAM_RAW, 'ans1_val' => PARAM_RAW,
                'ans2' => PARAM_RAW, 'ans2_val' => PARAM_RAW, 'ans3' => PARAM_RAW, 'ans3_val' => PARAM_RAW,
                'ans4' => PARAM_RAW, 'step_lang' => PARAM_RAW,
        ], $q->get_expected_data());
    }

    public function test_get_correct_response_test0(): void {

        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(['ans1' => '2', 'ans1_val' => '2'], $q->get_correct_response());
    }

    public function test_get_correct_response_test1(): void {

        $q = $this->get_test_stack_question('test1');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(['ans1' => '(x-7)^4/4+c', 'ans1_val' => '(x-7)^4/4+c'],
                $q->get_correct_response());
    }

    public function test_get_correct_response_test3(): void {

        $q = $this->get_test_stack_question('test3');
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals([
            'ans1' => 'x^3', 'ans2' => 'x^4', 'ans3' => '0', 'ans4' => 'true',
            'ans1_val' => 'x^3', 'ans2_val' => 'x^4', 'ans3_val' => '0',
        ],
                $q->get_correct_response());
    }

    public function test_get_is_same_response_test0(): void {

        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));

        $this->assertFalse($q->is_same_response([], ['ans1' => '2']));
        $this->assertTrue($q->is_same_response(['ans1' => '2'], ['ans1' => '2']));
        $this->assertFalse($q->is_same_response(['_seed' => '123'], ['ans1' => '2']));
        $this->assertFalse($q->is_same_response(['ans1' => '2'], ['ans1' => '3']));
    }

    public function test_get_is_same_response_for_part_test3(): void {

        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($q->is_same_response_for_part('oddeven', ['ans3' => 'x'], ['ans3' => 'x']));
        $this->assertTrue($q->is_same_response_for_part('oddeven', ['ans1' => 'x', 'ans3' => 'x'],
                ['ans1' => 'y', 'ans3' => 'x']));
        $this->assertFalse($q->is_same_response_for_part('oddeven', ['ans3' => 'x'], ['ans3' => 'y']));
    }

    public function test_is_complete_response_test0(): void {

        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_complete_response([]));
        $this->assertFalse($q->is_complete_response(['ans1' => '2']));
        $this->assertTrue($q->is_complete_response(['ans1' => '2', 'ans1_val' => '2']));
    }

    public function test_is_gradable_response_test0(): void {

        $q = $this->get_test_stack_question('test0');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_gradable_response([]));
        $this->assertTrue($q->is_gradable_response(['ans1' => '2']));
        $this->assertTrue($q->is_gradable_response(['ans1' => '2', 'ans1_val' => '2']));
    }

    public function test_is_gradable_response_test3(): void {

        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_gradable_response([]));
        $this->assertTrue($q->is_gradable_response(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false']));
        $this->assertTrue($q->is_gradable_response([
            'ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x',
            'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => 'x', 'ans4' => 'false',
        ]));
    }

    public function test_is_complete_response_test3(): void {

        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_complete_response([]));
        $this->assertFalse($q->is_complete_response(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false']));
        $this->assertFalse($q->is_complete_response([
            'ans1' => 'x+1', 'ans1_val' => 'x+1',
            'ans2' => 'x+1', 'ans2_val' => 'x+1', 'ans3' => 'x+1', 'ans3_val' => 'x+1', 'ans4' => '',
        ]));
        $this->assertTrue($q->is_complete_response([
            'ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x',
            'ans1_val' => 'x^3', 'ans2_val' => 'x^2', 'ans3_val' => 'x', 'ans4' => 'false',
        ]));
    }

    public function test_is_complete_response_divide(): void {

        $q = $this->get_test_stack_question('divide');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($q->is_complete_response(['ans1' => '0']));
        // This response is not 'complete' because it causes CAS errors.
        $this->assertFalse($q->is_complete_response(['ans1' => '0', 'ans1_val' => '0']));
    }

    public function test_grade_response_test3(): void {

        $q = $this->get_test_stack_question('test3');
        $q->start_attempt(new question_attempt_step(), 1);

        $result = $q->grade_response(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x^2', 'ans4' => 'false']);
        $this->assertEquals([2.5 / 4, question_state::$gradedpartial], $result);

        $result = $q->grade_response(['ans1' => 'x^3', 'ans2' => 'x^2', 'ans3' => 'x', 'ans4' => 'false']);
        $this->assertEquals([2.5 / 4, question_state::$gradedpartial], $result);
    }

    public function test_grade_response_test3_incomplete(): void {

        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        // Response that has three parts wrong, and one not completed.
        $this->assertEquals([0 / 4, question_state::$gradedwrong],
                $q->grade_response(['ans1' => '1 + x', 'ans2' => '1 + x', 'ans3' => '1 + x', 'ans4' => '']));
    }

    public function test_grade_response_divide(): void {

        $q = $this->get_test_stack_question('divide');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals([1, question_state::$gradedright],
                $q->grade_response(['ans1' => '1/2']));
    }

    public function test_grade_response_will_not_accept_input_name(): void {

        $q = $this->get_test_stack_question('divide');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals([0, question_state::$gradedwrong],
                $q->grade_response(['ans1' => 'ans1']));
    }

    public function test_grade_parts_that_can_be_graded(): void {

        $q = $this->get_test_stack_question('test3');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 4);

        $response = [
            'ans1' => '(x', 'ans2' => '(x', 'ans3' => 'x+1', 'ans4' => 'false',
            'ans1_val' => 'x^3', 'ans3_val' => 'x',
        ];
        $lastgradedresponses = [
            'odd'     => ['ans1' => 'x^3', 'ans2' => '', 'ans3' => 'x', 'ans4' => '', 'ans1_val' => 'x^3', 'ans3_val' => 'x'],
            'oddeven' => ['ans1' => 'x^3', 'ans2' => '', 'ans3' => 'x', 'ans4' => '', 'ans1_val' => 'x^3', 'ans3_val' => 'x'],
        ];
        $partscores = $q->grade_parts_that_can_be_graded($response, $lastgradedresponses, false);

        $expected = [
            'unique' => new qbehaviour_adaptivemultipart_part_result('unique', 0, 1),
        ];
        $this->assertEquals($expected, $partscores);
    }

    public function test_classify_response_test0(): void {

        $q = test_question_maker::make_question('stack', 'test0');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 4);

        $expected = [
            'firsttree-0' => new question_classified_response('firsttree-1-F',
                    'ATEqualComAss (AlgEquiv-false). | firsttree-1-F', 0),
        ];
        $this->assertEquals($expected, $q->classify_response(['ans1' => '7']));

        $expected = [
            'firsttree-0' => new question_classified_response('firsttree-1-T', 'firsttree-1-T', 1),
        ];
        $this->assertEquals($expected, $q->classify_response(['ans1' => '2']));

        $expected = [
            'firsttree-0' => question_classified_response::no_response(),
        ];
        $this->assertEquals($expected, $q->classify_response(['ans1' => '']));

    }

    public function test_classify_response_test3(): void {

        $q = test_question_maker::make_question('stack', 'test3');
        $q->start_attempt(new question_attempt_step(), 4);

        $this->assertTrue(true);
    }

    public function test_get_question_var_values0(): void {

        $q = test_question_maker::make_question('stack', 'test2');
        $this->assertEquals('', $q->validate_against_stackversion(context_system::instance()));
        $q->start_attempt(new question_attempt_step(), 4);

        $expected = "a:3;\nb:9;\nta:x+y;";
        $this->assertEquals($expected, $q->get_question_session_keyval_representation());
    }

    public function test_question_addrow(): void {

        $q = test_question_maker::make_question('stack', 'addrow');
        $expected = 'This question uses addrow in the Question variables, which changed in STACK version ' .
             '2018060601 and is no longer supported. An alternative is rowadd. ';
        $expected .= 'This question uses addrow in the Feedback variables (firsttree), which changed in STACK version ' .
             '2018060601 and is no longer supported. An alternative is rowadd. ';
        $expected .= 'This question uses texdecorate in the Question variables, which changed in STACK version ' .
             '2018080600 and is no longer supported.';

        $this->assertEquals($expected, $q->validate_against_stackversion(context_system::instance()));
    }

    public function test_question_mul(): void {

        $q = test_question_maker::make_question('stack', 'mul');
        $expected = 'This question has an input which uses the "mul" option, '
            .'which is not suppored after STACK version 4.2.  Please edit this question.';

            $this->assertEquals($expected, $q->validate_against_stackversion(context_system::instance()));
    }
}
