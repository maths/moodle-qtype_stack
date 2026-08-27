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

/**
 * Unit tests for the question dashboard class
 *
 * @package    qtype_stack
 * @copyright  2026 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

use stack_question_dashboard;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questiondashboard.class.php');
use qtype_stack_testcase;
use test_question_maker;
use context_system;

/**
 * Test the class for the question dashboard page.
 * @group qtype_stack
 *
 * @covers \stack_question_dashboard::class
 */
final class questiondashboard_test extends qtype_stack_testcase {
    public function test_create_default_test_when_question_has_no_inputs(): void {
        $dashboard = new stack_question_dashboard(
            test_question_maker::make_question('stack', 'information'),
            null,
            context_system::instance()
        );

        $this->assertFalse($dashboard->create_default_test());
    }

    public function test_create_default_test_when_question_has_inputs(): void {
        $dashboard = new stack_question_dashboard(
            test_question_maker::make_question('stack', 'test3'),
            null,
            context_system::instance()
        );

        // Should only create a test the first time.
        $this->assertTrue($dashboard->create_default_test());
        $testcases = \question_bank::get_qtype('stack')->load_question_tests($dashboard->question->id);
        $this->assertEquals(1, count($testcases));
        $this->assertEquals(stack_string('autotestcase'), array_values($testcases)[0]->description);
        $this->assertFalse($dashboard->create_default_test());
    }

    public function test_run_test_cases(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $question = \question_bank::load_question($question->id);
        $dashboard = new stack_question_dashboard(
            $question,
            null,
            context_system::instance()
        );
        $dashboard->testprogress = new class {
            /**
             * Mock progress bar update.
             * @return void
             */
            public function update() {
                return;
            }
        };
        $qtest = new \stack_question_test('', ['ans1' => 'x^3']);
        $qtest->add_expected_result('odd', new \stack_potentialresponse_tree_state(
            1,
            true,
            1,
            0,
            '',
            ['odd-1-T']
        ));
        \question_bank::get_qtype('stack')->save_question_test($question->id, $qtest);

        $qtest = new \stack_question_test('', ['ans1' => 'x^2']);
        $qtest->add_expected_result('odd', new \stack_potentialresponse_tree_state(
            1,
            true,
            0,
            0.4,
            '',
            ['odd-1-F']
        ));
        \question_bank::get_qtype('stack')->save_question_test($question->id, $qtest);
        $result = $dashboard->run_test_cases();

        $this->assertFalse($result->demotest);
        $this->assertTrue($result->allpassed);
        $this->assertFalse($result->hasrandomvariants);
        $this->assertTrue($result->hasinputs);
        $this->assertTrue($result->hasresults);
        $this->assertEquals('', $result->runtimeerrors);
        $this->assertEquals(2, count($result->results));
        $this->assertEquals(2, count($result->summary));
        $this->assertEquals(true, $result->summary[0]->scorepass);
        $this->assertEquals(true, $result->summary[0]->penaltypass);
        $this->assertEquals(true, $result->summary[0]->notepass);
        $this->assertEquals(true, $result->summary[1]->scorepass);
        $this->assertEquals(true, $result->summary[1]->penaltypass);
        $this->assertEquals(true, $result->summary[1]->notepass);

        $qtest = new \stack_question_test('', ['ans1' => 'x^2']);
        $qtest->add_expected_result('odd', new \stack_potentialresponse_tree_state(
            1,
            true,
            0,
            0.4,
            '',
            ['odd-1-T']
        ));
        \question_bank::get_qtype('stack')->save_question_test($question->id, $qtest);
        $result = $dashboard->run_test_cases();
        $this->assertEquals(3, count($result->summary));
        $this->assertEquals(true, $result->summary[0]->scorepass);
        $this->assertEquals(true, $result->summary[0]->penaltypass);
        $this->assertEquals(true, $result->summary[0]->notepass);
        $this->assertEquals(true, $result->summary[1]->scorepass);
        $this->assertEquals(true, $result->summary[1]->penaltypass);
        $this->assertEquals(true, $result->summary[1]->notepass);
        $this->assertEquals(true, $result->summary[2]->scorepass);
        $this->assertEquals(true, $result->summary[2]->penaltypass);
        $this->assertEquals(false, $result->summary[2]->notepass);
        $qtest = new \stack_question_test('', ['ans1' => 'x^2']);
        $qtest->add_expected_result('odd', new \stack_potentialresponse_tree_state(
            1,
            true,
            1,
            0.3,
            '',
            ['odd-1-F']
        ));
        \question_bank::get_qtype('stack')->save_question_test($question->id, $qtest);
        $result = $dashboard->run_test_cases();
        $this->assertEquals(false, $result->summary[3]->scorepass);
        $this->assertEquals(false, $result->summary[3]->penaltypass);
        $this->assertEquals(true, $result->summary[3]->notepass);
    }

    public function test_list_variants_detects_note_deployed_already_and_sorts_notes(): void {
        global $PAGE;
        $PAGE->set_url('/question/type/stack/questiontestrun.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'dashboard_test', ['category' => $cat->id]);
        $question = \question_bank::load_question($question->id);
        $dashboard = new stack_question_dashboard(
            $question,
            5, // Gives note 4.
            context_system::instance()
        );
        $dashboard->variantprogress = new class {
            /**
             * Mock progress bar update.
             * @return void
             */
            public function update() {
                return;
            }
        };

        $result = $dashboard->list_variants();

        $this->assertTrue($result->hasrandomvariants);
        $this->assertFalse($result->seedmatched);
        $this->assertTrue($result->variantdeployed);
        $this->assertFalse($result->duplicateerror);
        $this->assertEquals(3, $result->deployedcount);
        $this->assertCount(3, $result->notes);
        $this->assertEquals('\({3}\), thing1_true.', $result->notes[0]->questionnote);
        $this->assertEquals('\({4}\), thing1_true.', $result->notes[1]->questionnote);
        $this->assertEquals('\({6}\), thing1_true.', $result->notes[2]->questionnote);
    }

    public function test_list_variants_detects_duplicates(): void {
        global $PAGE;
        $PAGE->set_url('/question/type/stack/questiontestrun.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'dashboard_test_2', ['category' => $cat->id]);
        $question = \question_bank::load_question($question->id);
        $dashboard = new stack_question_dashboard(
            $question,
            8, // Gives note 6.
            context_system::instance()
        );
        $variantprogress = new class {
            /**
             * Mock progress bar update.
             * @return void
             */
            public function update() {
                return;
            }
        };
        $dashboard->variantprogress = $variantprogress;

        $result = $dashboard->list_variants();

        $this->assertTrue($result->hasrandomvariants);
        $this->assertTrue($result->seedmatched);
        $this->assertTrue($result->variantdeployed);
        $this->assertTrue($result->duplicateerror);
        $this->assertEquals(4, $result->deployedcount);
        $this->assertCount(4, $result->notes);

        $dashboard = new stack_question_dashboard(
            $question,
            17, // Gives note 7.
            context_system::instance()
        );
        $dashboard->variantprogress = $variantprogress;
        $result = $dashboard->list_variants();
        $this->assertFalse($result->seedmatched);
        $this->assertFalse($result->variantdeployed);
    }
}
