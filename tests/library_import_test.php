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
    protected string $filepath = 'stacklibrary/Calculus-Refresher/CR_Diff_01/CR-Diff-01-basic-1-e.xml';
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

        $returnvalue = library_import::import_execute($this->qcategory->id, $this->filepath);

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
        library_import::import_execute($this->qcategory->id, $this->filepath);
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
        library_import::import_execute($this->qcategory->id, $this->filepath);
    }

    /**
     * Test the library_import function fails when user has no access to supplied context.
     */
    public function test_export_capability(): void {
        $this->expectException(require_login_exception::class);
        $this->expectExceptionMessage('Not enrolled');
        library_import::import_execute($this->qcategory->id, $this->filepath);
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

        $returnvalue = library_import::import_execute($this->qcategory->id, $this->filepath);

        // We need to execute the return values cleaning process to simulate
        // the web service server.
        $returnvalue = external_api::clean_returnvalue(
            library_import::import_execute_returns(),
            $returnvalue
        );

        $this->assertEquals(true,
            $returnvalue['success']);

        $events = $sink->get_events();
        $this->assertEquals(count($events), 2);
        $this->assertInstanceOf('\core\event\question_created', $events['0']);
        $this->assertInstanceOf('\core\event\questions_imported', $events['1']);

        $DB->get_record('question', ['name' => 'CR-Diff-01-basic-1.e'], '*', MUST_EXIST);
    }
}
