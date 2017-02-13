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
        $syscontext = context_system::instance();
        $category = question_make_default_categories(array($syscontext));
        $fakequestion = new stdClass();
        $fakequestion->qtype = 'stack';
        $fakequestion->category = $category->id;
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

    // Make this public so we can test it.
    public function get_input_names_from_question_text() {
        return parent::get_input_names_from_question_text();
    }

    // Make this public so we can test it.
    public function get_prt_names_from_question() {
        return parent::get_prt_names_from_question();
    }

    // Make this public so we can test it.
    public function get_sloppy_tags($text) {
        return parent::get_sloppy_tags($text);
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

        $this->assertEquals(array('ans1' => array(1, 1)),
                $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_input_only() {
        $form = $this->get_form('[[input:ans123]]', '');

        $this->assertEquals(array('ans123' => array(1, 0)),
                $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_validation_only() {
        $form = $this->get_form('[Blah] [[validation:ans123]] [Blah]', '');

        $this->assertEquals(array('ans123' => array(0, 1)),
                $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_invalid() {
        $form = $this->get_form('[[input:123]]', '');

        $this->assertEquals(array(), $form->get_input_names_from_question_text());
    }

    public function test_get_input_names_from_question_text_sloppy() {
        $text = 'What is \(1+1\)?  [[input: ans1]]';
        $form = $this->get_form($text, '');

        $this->assertEquals(array('[[input: ans1]]'), $form->get_sloppy_tags($text));
    }

    public function test_get_prt_names_from_question_default() {
        $form = $this->get_form(qtype_stack_edit_form::DEFAULT_QUESTION_TEXT,
                qtype_stack_edit_form::DEFAULT_SPECIFIC_FEEDBACK);

        $this->assertEquals(array('prt1' => 1), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_text() {
        $form = $this->get_form('[[feedback:prt123]]', '');

        $this->assertEquals(array('prt123' => 1), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_feedback() {
        $form = $this->get_form('What is $1 + 1$? [[input:ans1]]', '[[feedback:prt123]]');

        $this->assertEquals(array('prt123' => 1), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_both() {
        $form = $this->get_form('[Blah] [[feedback:prt1]] [Blah]', '[Blah] [[feedback:prt2]] [Blah]');

        $this->assertEquals(array('prt1' => 1, 'prt2' => 1), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_invalid() {
        $form = $this->get_form('[[feedback:123]]', '');

        $this->assertEquals(array(), $form->get_prt_names_from_question());
    }

    public function test_get_prt_names_from_question_duplicate() {
        $form = $this->get_form('[[feedback:prt1]] [[feedback:prt1]]', '');

        $this->assertEquals(array('prt1' => 2), $form->get_prt_names_from_question());
    }
}
