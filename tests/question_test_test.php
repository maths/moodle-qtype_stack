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
use stack_question_test;
use stack_potentialresponse_tree_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once(__DIR__ . '/../question.php');

/**
 * Unit tests for type_stack question test mechanism.
 *
 * @package    qtype_stack
 * @copyright 2026 The University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \qtype_stack_question
 */
final class question_test_test extends qtype_stack_testcase {
    public function test_basic_examples(): void {
        // This unit test runs a question test.
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a test question.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $questionid = $question->id;
        $seed = 1;

        $qtest = new stack_question_test('', ['ans1' => 'x^3']);
        $qtest->add_expected_result(
            'odd',
            new stack_potentialresponse_tree_state(
                1,
                true,
                1,
                0,
                '',
                ['[ odd-1-T ]']
            )
        );
        $result = $qtest->test_question($questionid, $seed, context_system::instance());
        $this->assertTrue($result->passed());

        $qtest = new stack_question_test('', ['ans3' => 'x^3']);
        $qtest->add_expected_result(
            'oddeven',
            new stack_potentialresponse_tree_state(
                1,
                true,
                0,
                0,
                '',
                ['[ oddeven-1-T | oddeven-2-F ]']
            )
        );
        $result = $qtest->test_question($questionid, $seed, context_system::instance());
        $state = $result->get_prt_states();
        $state = $state['oddeven'];
        $this->assertEquals('(Score, Penalty)', trim($state->reason));
        $this->assertFalse($result->passed());

        $qtest = new stack_question_test('', ['ans3' => 'x^3']);
        $qtest->add_expected_result(
            'oddeven',
            new stack_potentialresponse_tree_state(
                1,
                true,
                0.5,
                0.4,
                '',
                ['[ oddeven-1-T ]']
            )
        );
        $result = $qtest->test_question($questionid, $seed, context_system::instance());
        $state = $result->get_prt_states();
        $state = $state['oddeven'];
        $this->assertEquals(
            '(Answer note: Expected last node of oddeven-1-T but got oddeven-2-F.)',
            trim($state->reason)
        );
        $this->assertEquals('oddeven-1-T | oddeven-2-F', trim($state->answernote));
        $this->assertFalse($result->passed());

        $qtest = new stack_question_test('', ['ans3' => 'x^3']);
        $qtest->add_expected_result(
            'oddeven',
            new stack_potentialresponse_tree_state(
                1,
                true,
                0.5,
                0.4,
                '',
                ['NULL']
            )
        );
        $result = $qtest->test_question($questionid, $seed, context_system::instance());
        $state = $result->get_prt_states();
        $state = $state['oddeven'];
        $this->assertEquals(
            '(Answer note: Expected a null result, but got: oddeven-2-F.)',
            trim($state->reason)
        );
        $this->assertFalse($result->passed());

        $qtest = new stack_question_test('', ['ans3' => 'x^3']);
        $qtest->add_expected_result(
            'oddeven',
            new stack_potentialresponse_tree_state(
                1,
                true,
                0.5,
                0.4,
                '',
                ['()']
            )
        );
        $result = $qtest->test_question($questionid, $seed, context_system::instance());
        $state = $result->get_prt_states();
        $state = $state['oddeven'];
        $this->assertEquals(
            '',
            trim($state->reason)
        );
        $this->assertTrue($result->passed());
    }

    public function test_test_answer_note(): void {
        // This unit test runs comprehensive tests of the test_answer_note function.
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a test question, to set up test cases.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $questionid = $question->id;
        $seed = 1;
        $qtest = new stack_question_test('', ['ans1' => 'x^3']);
        $qtest->add_expected_result('odd', new stack_potentialresponse_tree_state(
            1,
            true,
            1,
            0,
            '',
            ['[ odd-1-T ]']
            )
        );
        $result = $qtest->test_question($questionid, $seed, context_system::instance());

        // At this point we have a working and testable stack_question_test_result object.
        $this->assertEquals(
            [false, ['Expected result is empty, which is a test case construction error.  ' .
            'Use NULL for PRTs which do not activate.']],
            $result->test_answer_note('', [])
        );

        $this->assertEquals([true, []], $result->test_answer_note('NULL', []));
        $this->assertEquals(
            [false, ['Got an unexpected null result.']],
            $result->test_answer_note('prt1-1-T', [])
        );
        $this->assertEquals(
            [false, ['Expected a null result, but got: prt1-1-T.']],
            $result->test_answer_note('NULL', ['prt1-1-T'])
        );

        $this->assertEquals([true, []], $result->test_answer_note('()', []));
        $this->assertEquals([true, []], $result->test_answer_note('()', ['prt1-1-T', 'prt1-1-F']));

        // Legacy case.
        $this->assertEquals([true, []], $result->test_answer_note('prt1-1-T', ['prt1-1-T']));
        // New default.
        $this->assertEquals([true, []], $result->test_answer_note('( prt1-1-T ]', ['prt1-1-T']));
        // Strict.
        $this->assertEquals([true, []], $result->test_answer_note('[ prt1-1-T ]', ['prt1-1-T']));
        // Relaxed.
        $this->assertEquals([true, []], $result->test_answer_note('( prt1-1-T )', ['prt1-1-T']));

        $this->assertEquals(
            [false, ['Expected last node of prt1-1-T but got prt1-1-F.']],
            $result->test_answer_note('prt1-1-T', ['prt1-1-F'])
        );
        $this->assertEquals(
            [false, ['Expected last node of prt1-1-T but got prt1-1-F.']],
            $result->test_answer_note('(prt1-1-T]', ['prt1-1-F'])
        );

        $expected = ['Expected first node of prt1-1-T but got prt1-1-F.', 'Expected last node of prt1-1-T but got prt1-1-F.'];
        $this->assertEquals(
            [false, $expected],
            $result->test_answer_note('[prt1-1-T]', ['prt1-1-F'])
        );
        $expected = ['Expected first node of prt1-1-T but got prt1-2-F.', 'Expected last node of prt1-4-T but got prt1-3-F.'];
        $this->assertEquals(
            [false, $expected],
            $result->test_answer_note('[prt1-1-T | prt1-4-T]', ['prt1-2-F', 'prt1-3-F'])
        );

        // Condone extra notes.
        $this->assertEquals(
            [true, []],
            $result->test_answer_note(
                'prt1-1-T | prt1-2-T | prt1-4-T',
                ['prt1-1-T', 'prt1-2-T', 'prt1-3-T', 'prt1-4-T']
            )
        );
        $this->assertEquals(
            [true, []],
            $result->test_answer_note(
                '[ prt1-1-T | prt1-2-T | prt1-4-T ]',
                ['prt1-1-T', 'prt1-2-T', 'prt1-3-T', 'prt1-4-T']
            )
        );
        $this->assertEquals(
            [true, []],
            $result->test_answer_note(
                '( prt1-1-T | prt1-2-T | prt1-4-T ]',
                ['prt1-0-T', 'prt1-1-T', 'prt1-2-T', 'prt1-3-T', 'prt1-4-T']
            )
        );

        // Missing notes.
        $this->assertEquals(
            [false, ['Expected node: prt1-2-T.']],
            $result->test_answer_note(
                '( prt1-1-T | prt1-2-T | prt1-4-T ]',
                ['prt1-0-T', 'prt1-1-T', 'prt1-3-T', 'prt1-4-T']
            )
        );
        $this->assertEquals(
            [false, ['Expected node: prt1-2-T.', 'Expected node: prt1-5-T.']],
            $result->test_answer_note(
                '( prt1-1-T | prt1-2-T | prt1-5-T | prt1-4-T ]',
                ['prt1-0-T', 'prt1-1-T', 'prt1-3-T', 'prt1-4-T']
            )
        );
        // Notes appear out of order.
        // We match prt1-1-T with the first actual occurance (slot 3 in the array).
        // This means although 'prt1-order' exists in the answer note array it's gone.
        $this->assertEquals(
            [false, ['Expected node: prt1-2-T.', 'Expected node: prt1-order.']],
            $result->test_answer_note(
                '( prt1-1-T | prt1-2-T | prt1-order | prt1-4-T ]',
                ['prt1-0-T', 'prt1-order', 'prt1-1-T', 'prt1-3-T', 'prt1-4-T']
            )
        );
    }
}
