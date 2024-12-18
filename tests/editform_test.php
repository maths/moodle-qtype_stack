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

/**
 * Subclass of qtype_stack_edit_form_testable that is easier to use in unit tests.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once(__DIR__ . '/../edit_stack_form.php');

/**
 * Subclass of qtype_stack_edit_form_testable that is easier to use in unit tests.
 * @group qtype_stack
 * @covers \qtype_stack_edit_form
 */
final class editform_test_class extends \qtype_stack_edit_form {

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function __construct($questiontext, $specificfeedback, $quizmoduleid) {
        global $USER;
        // ISS1325 - Use quiz context rather than system context as
        // question categories only allowed in modules from Moodle 5.
        $quizcontext = \context_module::instance($quizmoduleid);
        if (function_exists('question_get_default_category')) {
            // This function exists from Moodle 4.5 onwards but the second parameter
            // which creates the category if it doesn't exist is 5.0 onwards.
            $category = question_get_default_category($quizcontext->id, true);
            if (!$category) {
                $category = $category = question_make_default_categories([$quizcontext]);
            }
        } else {
            // Deprecated from 5.0.
            $category = $category = question_make_default_categories([$quizcontext]);
        }
        $fakequestion = new \stdClass();
        $fakequestion->qtype = 'stack';
        $fakequestion->category = $category->id;
        $fakequestion->contextid = $quizcontext->id;
        $fakequestion->createdby = $USER->id;
        $fakequestion->modifiedby = $USER->id;
        $fakequestion->questiontext = $questiontext;
        $fakequestion->options = new \stdClass();
        $fakequestion->options->specificfeedback = $specificfeedback;
        $fakequestion->formoptions = new \stdClass();
        $fakequestion->formoptions->movecontext = null;
        $fakequestion->formoptions->repeatelements = true;
        $fakequestion->inputs = null;
        // Support both Moodle 4.x and 3.x.
        if (class_exists('\core_question\local\bank\question_edit_contexts')) {
            $contexts = new \core_question\local\bank\question_edit_contexts($quizcontext);
        } else {
            $contexts = new \question_edit_contexts($quizcontext);
        }
        parent::__construct(new \moodle_url('/'), $fakequestion, $category, $contexts);
    }

}

/**
 * Unit tests for Stack question editing form.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 * @covers \qtype_stack_edit_form
 */
final class editform_test extends \advanced_testcase {

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    protected function get_form($questiontext, $specificfeedback) {
        $this->setAdminUser();
        $this->resetAfterTest();
        $quizgenerator = self::getDataGenerator()->get_plugin_generator('mod_quiz');
        $site = get_site();
        // Add a quiz to the site course.
        $quiz = $quizgenerator->create_instance(['course' => $site->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);
        $quizmoduleid = $quiz->cmid;
        return new editform_test_class($questiontext, $specificfeedback, $quizmoduleid);
    }

    public function test_get_input_names_from_question_text_default(): void {

        $form = $this->get_form(\qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                \qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);
        $qtype = new \qtype_stack();

        $this->assertEquals(['ans1' => [1, 1]],
                $qtype->get_input_names_from_question_text(\qtype_stack_edit_form::DEFAULT_QUESTION_TEXT));
    }

    public function test_get_prt_names_from_question_default(): void {

        $form = $this->get_form(\qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                \qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);
        $qtype = new \qtype_stack();

        $this->assertEquals(['prt1' => 1],
                $qtype->get_prt_names_from_question(\qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                \qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK));
    }
}
