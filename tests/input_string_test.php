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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

require_once(__DIR__ . '/../stack/input/factory.class.php');

// Unit tests for stack_algebra_input.
//
// @copyright  2018 The University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_string_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('string', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="maxima-string" value="" />',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_hello_world() {
        $el = stack_input_factory::make('string', 'ans1', '"Hello world"');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="maxima-string" value="0" />',
                $el->render(new stack_input_state(stack_input::VALID, array('0'), '', '', '', '', ''),
                        'stack1__ans1', false, null));
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\( \\mbox{Hello world} \)</span></span>, which can be typed in as follows: <code>Hello world</code>',
                $el->get_teacher_answer_display('"Hello world"', '\\mbox{Hello world}'));
    }

    public function test_validate_string_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => 'Hello world'), $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{Hello world} \]', $state->contentsdisplayed);
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[ \[ \mbox{Hello world} \]</span></span> \), ' .
                'which can be typed in as follows: <code>Hello world</code>',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_string_string_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are respected.
        $state = $el->validate_student_response(array('sans1' => '"Hello world"'), $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\"Hello world\""', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{"Hello world"} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_singlequotes_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are no longer respected.
        $state = $el->validate_student_response(array('sans1' => '\'Hello world\''), $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\'Hello world\'"', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{\'Hello world\'} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_within_string() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are ignored.
        $state = $el->validate_student_response(array('sans1' => 'I said "Hello world" to fred'),
                $options, '"A random string"', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"I said \"Hello world\" to fred"', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{I said "Hello world" to fred} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_broken_string() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => '".'), $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\"."', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{".} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_whitespace() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => ' Some whitespace  '), $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('" Some whitespace  "', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{ Some whitespace } \]', $state->contentsdisplayed);
    }

    public function test_validate_string_hideanswer() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'state', '"[SOME JSON]"');
        $el->set_parameter('options', 'hideanswer');
        $state = $el->validate_student_response(array('state' => '[SOME MORE JSON]'), $options, '"[SOME JSON]"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"[SOME MORE JSON]"', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{[SOME MORE JSON]} \]', $state->contentsdisplayed);
        $this->assertEquals('', $el->get_teacher_answer_display("[SOME JSON]", "\[ \mbox{[SOME MORE JSON]} \]"));
    }
}
