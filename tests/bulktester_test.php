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
 * Tests for bulk tester class (incomplete)
 *
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/question/type/stack/stack/bulktester.class.php');

/**
 * Test the class for the bulk tester.
 * @group qtype_stack
 *
 * @covers \qtype_stack\stack_question_library::class
 */
final class bulktester_test extends \advanced_testcase {
    /**
     * Test todo blocks are found.
     * @return void
     */
    public function test_find_todo(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a test question.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $question2 = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $question3 = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $question4 = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $question5 = $generator->create_question('stack', 'test3', ['category' => $cat->id]);
        $question6 = $generator->create_question('stack', 'test3', ['category' => $cat->id]);

        $DB->set_field('question', 'questiontext', 'Block %_[[todo]] in the middle',
                ['id' => $question1->id]);
        $DB->set_field('question', 'generalfeedback', '[[todo]]Block at %%the beginning',
                ['id' => $question2->id]);
        $DB->set_field('qtype_stack_options', 'questionnote', 'Block at the end[[todo]]',
                ['questionid' => $question3->id]);
        $DB->set_field('qtype_stack_options', 'specificfeedback', 'Block%[[todo]]%in the middle',
                ['questionid' => $question4->id]);
        $DB->set_field('qtype_stack_options', 'questiondescription', 'Block [[todo]] in the middle',
                ['questionid' => $question5->id]);
        $DB->set_field('qtype_stack_options', 'questiondescription', 'Block [todo] in the middle',
                ['questionid' => $question6->id]);

        $bulktester = new \stack_bulk_tester();
        $result = (array) $bulktester->stack_questions_in_category_with_todo($cat->id);
        $this->assertEquals(5, count($result));
        $this->assertEquals(true, isset($result[$question1->id]));
        $this->assertEquals(true, isset($result[$question2->id]));
        $this->assertEquals(true, isset($result[$question3->id]));
        $this->assertEquals(true, isset($result[$question4->id]));
        $this->assertEquals(true, isset($result[$question5->id]));
        $this->assertEquals(false, isset($result[$question6->id]));
    }
}
