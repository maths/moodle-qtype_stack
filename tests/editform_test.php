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
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once(__DIR__ . '/../edit_stack_form.php');

// Subclass of qtype_stack_edit_form_testable that is easier to use in unit tests.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class qtype_stack_edit_form_testable extends qtype_stack_edit_form {

    public function __construct($questiontext, $specificfeedback) {
        global $USER;
        $syscontext = context_system::instance();
        $category = question_make_default_categories(array($syscontext));
        $fakequestion = new stdClass();
        $fakequestion->qtype = 'stack';
        $fakequestion->category = $category->id;
        $fakequestion->contextid = $syscontext->id;
        $fakequestion->createdby = $USER->id;
        $fakequestion->modifiedby = $USER->id;
        $fakequestion->questiontext = $questiontext;
        $fakequestion->options = new stdClass();
        $fakequestion->options->specificfeedback = $specificfeedback;
        $fakequestion->formoptions = new stdClass();
        $fakequestion->formoptions->movecontext = null;
        $fakequestion->formoptions->repeatelements = true;
        $fakequestion->inputs = null;
        parent::__construct(new moodle_url('/'), $fakequestion, $category,
                new question_edit_contexts($syscontext));
    }

}


/**
 * Unit tests for Stack question editing form.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class qtype_stack_edit_form_test extends advanced_testcase {

    protected function get_form($questiontext, $specificfeedback) {
        $this->setAdminUser();
        $this->resetAfterTest();

        return new qtype_stack_edit_form_testable($questiontext, $specificfeedback);
    }

    public function test_get_input_names_from_question_text_default() {
        $form = $this->get_form(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);
        $qtype = new qtype_stack();

        $this->assertEquals(array('ans1' => array(1, 1)),
                $qtype->get_input_names_from_question_text(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT));
    }

    public function test_get_prt_names_from_question_default() {
        $form = $this->get_form(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);
        $qtype = new qtype_stack();

        $this->assertEquals(array('prt1' => 1),
                $qtype->get_prt_names_from_question(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK));
    }
}
