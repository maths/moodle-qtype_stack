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
 * Unit tests for the Stack question edit form.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');
require_once($CFG->dirroot . '/question/type/stack/edit_stack_form.php');


/**
 * Subclass of qtype_stack_edit_form_testable that is easier to use in unit tests.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_edit_form_testable extends qtype_stack_edit_form {
    public function __construct($questiontext, $specificfeedback) {
        $syscontext = context_system::instance();
        $category = question_get_default_category($syscontext->id);
        $fakequestion = new stdClass();
        $fakequestion->qtype = 'stack';
        $fakequestion->category = $category->id;
        $fakequestion->questiontext = $questiontext;
        $fakequestion->options->specificfeedback = $specificfeedback;
        $fakequestion->formoptions->movecontext = null;
        parent::__construct(new moodle_url('/'), $fakequestion, $category,
                new question_edit_contexts($syscontext));
    }

    // Make this public so we can test it.
    public function get_input_names_from_question_text() {
        return parent::get_input_names_from_question_text();
    }

    // Make this public so we can test it.
    public function get_prt_names_from_question() {
        return parent::get_prt_names_from_question();
    }
}


/**
 * Unit tests for Stack question editing form.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_edit_form_test extends UnitTestCase {

    protected function get_form($questiontext, $specificfeedback) {
        return new qtype_stack_edit_form_testable($questiontext, $specificfeedback);
    }

    public function test_get_input_names_from_question_text_default() {
        $form = $this->get_form(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);

        $this->assertEqual(array('ans1' => qtype_stack_edit_form::INPUT_AND_VALIDATION),
                $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_input_only() {
        $form = $this->get_form('[[input:ans123]]', '');

        $this->assertEqual(array('ans123' => qtype_stack_edit_form::INPUT_ONLY),
                $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_validation_only() {
        $form = $this->get_form('[Blah] [[validation:ans123]] [Blah]', '');

        $this->assertEqual(array('ans123' => qtype_stack_edit_form::INPUT_MISSING_FOR_VALIDATION),
                $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_invalid() {
        $form = $this->get_form('[[input:123]]', '');

        $this->assertEqual(array(), $form->get_input_names_from_question_text());
    }

    public function test_get_prt_names_from_question_default() {
        $form = $this->get_form(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);

        $this->assertEqual(array('prt1'), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_text() {
        $form = $this->get_form('[[feedback:prt123]]', '');

        $this->assertEqual(array('prt123'), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_feedback() {
        $form = $this->get_form('What is $1 + 1$? [[input:ans1]]', '[[feedback:prt123]]');

        $this->assertEqual(array('prt123'), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_both() {
        $form = $this->get_form('[Blah] [[feedback:prt1]] [Blah]', '[Blah] [[feedback:prt2]] [Blah]');

        $this->assertEqual(array('prt1', 'prt2'), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_invalid() {
        $form = $this->get_form('[[feedback:123]]', '');

        $this->assertEqual(array(), $form->get_prt_names_from_question());
    }
}
