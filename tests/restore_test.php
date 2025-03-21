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

namespace qtype_stack;

use advanced_testcase;
use backup_controller;
use restore_controller;
use backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');
/**
 * Unit tests for restore.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \qtype_stack
 */
final class restore_test extends advanced_testcase {

    /**
     * Restore a quiz with duplicate questions (same stamp and questions) into the same course.
     *
     */
    public function test_restore_quiz_with_duplicate_questions(): void {
        global $DB, $USER, $CFG;
        if ($CFG->version < 2024100703) {
            return;
        }
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a user with editing teacher capabilities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $teacher = $USER;
        $generator->enrol_user($teacher->id, $course1->id, 'editingteacher');
        $coursecontext = \context_course::instance($course1->id);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a question category.
        $cat = $questiongenerator->create_question_category(['contextid' => $coursecontext->id]);

        // Create a quiz with 2 identical but separate questions.
        $quiz1 = $this->create_test_quiz($course1);
        $question1 = $questiongenerator->create_question('stack', 'test1', ['category' => $cat->id]);
        \quiz_add_quiz_question($question1->id, $quiz1, 0);
        $question2 = $questiongenerator->create_question('stack', 'test1', ['category' => $cat->id]);
        \quiz_add_quiz_question($question2->id, $quiz1, 0);
        $question3 = $questiongenerator->create_question('stack', 'test1', ['category' => $cat->id]);
        $qtype = new \qtype_stack();
        $qtype->deploy_variant($question3->id, 1234);
        $qtype->deploy_variant($question3->id, 27);
        \quiz_add_quiz_question($question3->id, $quiz1, 0);
        $question4 = $questiongenerator->create_question('stack', 'test1', ['category' => $cat->id]);
        \quiz_add_quiz_question($question4->id, $quiz1, 0);
        $qtype->deploy_variant($question4->id, 1234);
        $qtype->deploy_variant($question4->id, 27);
        $question5 = $questiongenerator->create_question('stack', 'test3', ['category' => $cat->id]);
        \quiz_add_quiz_question($question5->id, $quiz1, 0);
        $question6 = $questiongenerator->create_question('stack', 'test3', ['category' => $cat->id]);
        \quiz_add_quiz_question($question6->id, $quiz1, 0);

        // Update question2 to have the same times and stamp as question1.
        $DB->update_record('question', [
            'id' => $question2->id,
            'stamp' => $question1->stamp,
            'timecreated' => $question1->timecreated,
            'timemodified' => $question1->timemodified,
        ]);

        $DB->update_record('question', [
            'id' => $question4->id,
            'stamp' => $question3->stamp,
            'timecreated' => $question3->timecreated,
            'timemodified' => $question3->timemodified,
        ]);

        $DB->update_record('question', [
            'id' => $question6->id,
            'stamp' => $question5->stamp,
            'timecreated' => $question5->timecreated,
            'timemodified' => $question5->timemodified,
        ]);

        // Backup quiz.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $quiz1->cmid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $teacher->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup into the same course.
        $rc = new restore_controller($backupid, $course1->id, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $teacher->id, backup::TARGET_CURRENT_ADDING);
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Expect that the restored quiz will have the second question in both its slots
        // by virtue of identical stamp, version, and hash of question answer texts.
        $modules = \get_fast_modinfo($course1->id)->get_instances_of('quiz');
        $this->assertCount(2, $modules);
        $quiz2 = end($modules);
        $quiz2structure = \mod_quiz\question\bank\qbank_helper::get_question_structure($quiz2->instance, $quiz2->context);
        $this->assertEquals($quiz2structure[1]->questionid, $quiz2structure[2]->questionid);
        $this->assertEquals($quiz2structure[3]->questionid, $quiz2structure[4]->questionid);
        $this->assertEquals($quiz2structure[5]->questionid, $quiz2structure[6]->questionid);
    }

    /**
     * Create a test quiz for the specified course.
     *
     * @param \stdClass $course
     * @return  \stdClass
     */
    protected function create_test_quiz(\stdClass $course): \stdClass {
        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        return $quizgenerator->create_instance([
            'course' => $course->id,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);
    }
}
