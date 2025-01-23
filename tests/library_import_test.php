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
 * Unit tests for library_import webservice
 *
 * @package    qtype_stack
 * @copyright  2024 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use context_course;
use externallib_advanced_testcase;
use external_api;
use required_capability_exception;
use require_login_exception;

/**
 * Test the library_import webservice function.
 * @runTestsInSeparateProcesses
 * @group qtype_stack
 *
 * @covers \stack\library_import::import_execute
 */
final class library_import_test extends externallib_advanced_testcase {
    /** @var \core_question_generator plugin generator */
    protected \core_question_generator  $generator;
    /** @var \stdClass generated course object */
    protected \stdClass $course;
    /** @var \stdClass generated question categoryobject */
    protected \stdClass $qcategory;
    /** @var string File to import */
    protected string $filepath = 'importtest/Course1/top/CR_Diff_01/CR-Diff-01-basic-1-e.xml';
    /** @var \stdClass generated user object */
    protected \stdClass $user;

    public function setUp(): void {
        parent::setUp();
        global $DB;
        $this->resetAfterTest();
        $this->generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $this->course = $this->getDataGenerator()->create_course();
        $this->qcategory = $this->generator->create_question_category(
                        ['contextid' => \context_course::instance($this->course->id)->id]);
        $user = $this->getDataGenerator()->create_user();
        $this->user = $user;
        $this->setUser($user);
    }

    /**
     * Test the library_import function when capabilities are present.
     */
    public function test_capabilities(): void {
        global $DB;
        // Set the required capabilities - webservice access and export rights on course.
        $context = context_course::instance($this->course->id);
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $this->user->id, $context->id);

        $returnvalue = library_import::import_execute($this->course->id, $this->qcategory->id, $this->filepath, false);

        // We need to execute the return values cleaning process to simulate
        // the web service server.
        $returnvalue = external_api::clean_returnvalue(
            library_import::import_execute_returns(),
            $returnvalue
        );

        // Assert that there was a response.
        // The actual response is tested in other tests.
        $this->assertNotNull($returnvalue);
    }

    /**
     * Test the library_import function fails when not logged in.
     */
    public function test_not_logged_in(): void {
        global $DB;
        $this->setUser();
        $this->expectException(require_login_exception::class);
        // Exception messages don't seem to get translated.
        $this->expectExceptionMessage('not logged in');
        library_import::import_execute($this->course->id, $this->qcategory->id, $this->filepath, false);
    }

    /**
     * Test the library_import function fails when no capability to add questions assigned.
     */
    public function test_no_access(): void {
        global $DB;
        $context = context_course::instance($this->course->id);
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'teacher']);
        role_assign($teacherroleid, $this->user->id, $context->id);
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);
        $this->expectException(required_capability_exception::class);
        $this->expectExceptionMessage('you do not currently have permissions to do that (Add new questions).');
        library_import::import_execute($this->course->id, $this->qcategory->id, $this->filepath, false);
    }

    /**
     * Test the library_import function fails when user has no access to supplied context.
     */
    public function test_export_capability(): void {
        $this->expectException(require_login_exception::class);
        $this->expectExceptionMessage('Not enrolled');
        library_import::import_execute($this->course->id, $this->qcategory->id, $this->filepath, false);
    }

    /**
     * Test output of library_import function.
     */
    public function test_library_import(): void {
        global $DB;
        // Set the required capabilities - webservice access and export rights on course.
        $context = context_course::instance($this->course->id);
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $this->user->id, $context->id);
        $sink = $this->redirectEvents();

        $returnvalue = library_import::import_execute($this->course->id, $this->qcategory->id, $this->filepath, false);

        // We need to execute the return values cleaning process to simulate
        // the web service server.
        $returnvalue = external_api::clean_returnvalue(
            library_import::import_execute_returns(),
            $returnvalue
        );

        $this->assertEquals(1, count($returnvalue));
        $this->assertEquals(true, $returnvalue[0]['success']);
        $this->assertEquals('CR-Diff-01-basic-1.e', $returnvalue[0]['questionname']);
        $this->assertEquals(basename($this->filepath), $returnvalue[0]['filename']);
        $this->assertEquals(true, $returnvalue[0]['isstack']);

        $events = $sink->get_events();
        $this->assertEquals(count($events), 2);
        $this->assertInstanceOf('\core\event\question_created', $events['0']);
        $this->assertInstanceOf('\core\event\questions_imported', $events['1']);

        $dbquestion = $DB->get_record('question', ['name' => 'CR-Diff-01-basic-1.e'], '*', MUST_EXIST);
        $this->assertEquals($dbquestion->id, $returnvalue[0]['questionid']);
    }

    /**
     * Test output of library_import function for an entire folder.
     */
    public function test_library_import_folder(): void {
        global $DB;
        // Set the required capabilities - webservice access and export rights on course.
        $context = context_course::instance($this->course->id);
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $this->user->id, $context->id);
        $sink = $this->redirectEvents();

        $returnvalue = library_import::import_execute($this->course->id, $this->qcategory->id, $this->filepath, true);

        // We need to execute the return values cleaning process to simulate
        // the web service server.
        $returnvalue = external_api::clean_returnvalue(
            library_import::import_execute_returns(),
            $returnvalue
        );

        $this->assertEquals(18, count($returnvalue));
        $this->assertEquals(true, $returnvalue[0]['success']);
        $this->assertEquals('CR-Diff-01-basic-1.b', $returnvalue[0]['questionname']);
        $this->assertEquals('CR-Diff-01-basic-1-b.xml', $returnvalue[0]['filename']);
        $this->assertEquals(true, $returnvalue[0]['isstack']);

        $events = $sink->get_events();
        $this->assertEquals(count($events), 19);
        $this->assertInstanceOf('\core\event\question_created', $events['0']);
        $this->assertInstanceOf('\core\event\question_created', $events['17']);
        $this->assertInstanceOf('\core\event\questions_imported', $events['18']);

        $dbquestion = $DB->get_record('question', ['name' => 'CR-Diff-01-basic-1.b'], '*', MUST_EXIST);
        $this->assertEquals($dbquestion->id, $returnvalue[0]['questionid']);
        $dbquestions = $DB->get_records('question', ['qtype' => 'stack'], '', 'id');
        $this->assertEquals(18, count($dbquestions));
    }

    /**
     * Test output of library_import function for an entire folder.
     */
    public function test_library_import_quiz(): void {
        global $DB;
        // Set the required capabilities - webservice access and export rights on course.
        $context = context_course::instance($this->course->id);
        $quizfilepath = 'importtest/Course1_quiz_quiz-1/quiz-1_quiz.json';
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $this->user->id, $context->id);
        $sink = $this->redirectEvents();

        $returnvalue = library_import::import_execute($this->course->id, $this->qcategory->id, $quizfilepath, true);

        // We need to execute the return values cleaning process to simulate
        // the web service server.
        $returnvalue = external_api::clean_returnvalue(
            library_import::import_execute_returns(),
            $returnvalue
        );

        // Four questions plus the quiz itself.
        $this->assertEquals(5, count($returnvalue));
        $this->assertEquals(true, $returnvalue[0]['success']);
        $this->assertEquals('Checkbox', $returnvalue[0]['questionname']);
        $this->assertEquals('Checkbox.xml', $returnvalue[0]['filename']);
        $this->assertEquals(true, $returnvalue[0]['isstack']);
        $this->assertEquals('Algebraic input', $returnvalue[1]['questionname']);
        $this->assertEquals('Algebraic-input.xml', $returnvalue[1]['filename']);
        $this->assertEquals(true, $returnvalue[1]['isstack']);
        $this->assertEquals('Dropdown (shuffle)', $returnvalue[2]['questionname']);
        $this->assertEquals('Dropdown-(shuffle).xml', $returnvalue[2]['filename']);
        $this->assertEquals(true, $returnvalue[2]['isstack']);
        $this->assertEquals('Matrix', $returnvalue[3]['questionname']);
        $this->assertEquals('Matrix.xml', $returnvalue[3]['filename']);
        $this->assertEquals(true, $returnvalue[3]['isstack']);
        $this->assertEquals('Quiz: Quiz 1', $returnvalue[4]['questionname']);
        $this->assertEquals('quiz-1_quiz.json', $returnvalue[4]['filename']);
        $this->assertEquals(false, $returnvalue[4]['isstack']);

        $events = $sink->get_events();
        $categoriescreated = 0;
        $questionscreated = 0;
        $questionsimported = 0;
        foreach ($events as $currentevent) {
            $eventclass = get_class($currentevent);
            switch ($eventclass) {
                case 'core\event\question_category_created':
                    $categoriescreated++;
                    break;
                case 'core\event\question_created':
                    $questionscreated++;
                    break;
                case 'core\event\questions_imported':
                    // Fired once for each question category.
                    $questionsimported++;
                    break;
            }
        }
        $this->assertEquals(1, $categoriescreated);
        $this->assertEquals(4, $questionscreated);
        $this->assertEquals(2, $questionsimported);

        $dbquestion = $DB->get_record('question', ['name' => 'Checkbox'], '*', MUST_EXIST);
        $this->assertEquals($dbquestion->id, $returnvalue[0]['questionid']);
        $dbquestions = $DB->get_records('question', ['qtype' => 'stack'], '', 'id');
        $this->assertEquals(4, count($dbquestions));

        $quizzes = $DB->get_records('quiz');
        $quiz = array_shift($quizzes);
        $this->assertEquals('Quiz 1', $quiz->name);
        $this->assertEquals('A highly interesting quiz involving maths.', $quiz->intro);
        $this->assertEquals(1, $quiz->questionsperpage);
        $this->assertEquals('deferredfeedback', $quiz->preferredbehaviour);
        $this->assertEquals(2, $quiz->decimalpoints);
        $this->assertEquals(4352, $quiz->reviewmarks);
        $this->assertEquals(10, $quiz->grade);

        $sections = $DB->get_records('quiz_sections');
        $this->assertEquals(2, count($sections));
        $section1 = array_shift($sections);
        $section2 = array_shift($sections);
        $this->assertEquals('', $section1->heading);
        $this->assertEquals(1, $section1->firstslot);
        $this->assertEquals('New heading 1', $section2->heading);
        $this->assertEquals(3, $section2->firstslot);

        $slots = $DB->get_records('quiz_slots');
        $this->assertEquals(4, count($slots));
        $slot1 = array_shift($slots);
        $slot2 = array_shift($slots);
        $this->assertEquals(0, $slot1->requireprevious);
        $this->assertEquals(1, $slot1->page);
        $this->assertEquals(1, $slot1->maxmark);
        $this->assertEquals(0, $slot2->requireprevious);
        $this->assertEquals(2, $slot2->page);
        $this->assertEquals(1, $slot2->maxmark);

        $feedback = $DB->get_records('quiz_feedback');
        $this->assertEquals(1, count($feedback));
        $feedback1 = array_shift($feedback);
        $this->assertEquals('Low score', $feedback1->feedbacktext);
        $this->assertEquals(1, $feedback1->feedbacktextformat);
        $this->assertEquals(0, $feedback1->mingrade);
        $this->assertEquals(6, $feedback1->maxgrade);
    }

    /**
     * Test import of quiz without sections.
     */
    public function test_quiz_import_without_sections_and_feedback(): void {
        global $DB;
        // Set the required capabilities - webservice access and export rights on course.
        $context = context_course::instance($this->course->id);
        $quizfilepath = 'importtest/Course1_quiz_quiz-1/quiz-no-sections_quiz.json';
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $this->user->id, $context->id);

        library_import::import_execute($this->course->id, $this->qcategory->id, $quizfilepath, true);

        $sections = $DB->get_records('quiz_sections');
        $this->assertEquals(1, count($sections));
        $section1 = array_shift($sections);
        $this->assertEquals('', $section1->heading);
        $this->assertEquals(1, $section1->firstslot);

        $slots = $DB->get_records('quiz_slots');
        $this->assertEquals(4, count($slots));
        $slot1 = array_shift($slots);
        $slot2 = array_shift($slots);
        $this->assertEquals(0, $slot1->requireprevious);
        $this->assertEquals(0, $slot2->requireprevious);

        $feedback = $DB->get_records('quiz_feedback');
        $this->assertEquals(1, count($feedback));
    }

    /**
     * Test import of quiz with require previous.
     */
    public function test_import_with_require_previous(): void {
        global $DB;
        // Set the required capabilities - webservice access and export rights on course.
        $context = context_course::instance($this->course->id);
        $quizfilepath = 'importtest/Course1_quiz_quiz-1/quiz-require-prev_quiz.json';
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $this->user->id, $context->id);

        library_import::import_execute($this->course->id, $this->qcategory->id, $quizfilepath, true);

        $slots = $DB->get_records('quiz_slots');
        $this->assertEquals(4, count($slots));
        $slot1 = array_shift($slots);
        $slot2 = array_shift($slots);
        $slot3 = array_shift($slots);
        $this->assertEquals(0, $slot1->requireprevious);
        $this->assertEquals(1, $slot2->requireprevious);
        $this->assertEquals(0, $slot3->requireprevious);
    }
}
