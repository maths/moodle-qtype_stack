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

use qtype_stack_testcase;
use stack_cas_security;
use stack_input;
use stack_input_factory;
use stack_input_state;
use stack_options;


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
 * @covers \stack_string_input
 */
class input_string_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('string', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="maxima-string" value="" data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_hello_world() {
        $el = stack_input_factory::make('string', 'ans1', '"Hello world"');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="maxima-string" value="0" data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, ['0'], '', '', '', '', ''),
                        'stack1__ans1', false, null));
        $this->assertEquals('The answer Hello world would be correct.',
                $el->get_teacher_answer_display('"Hello world"', '\\text{Hello world}'));
    }

    public function test_validate_string_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'Hello world'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Hello world} \]', $state->contentsdisplayed);
        $this->assertEquals('The answer Hello world would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_string_string_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are respected.
        $state = $el->validate_student_response(['sans1' => '"Hello world"'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\"Hello world\""', $state->contentsmodified);
        $this->assertEquals('\[ \text{&quot;Hello world&quot;} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_singlequotes_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are no longer respected.
        $state = $el->validate_student_response(['sans1' => '\'Hello world\''], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\'Hello world\'"', $state->contentsmodified);
        $this->assertEquals('\[ \text{&apos;Hello world&apos;} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_within_string() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are ignored.
        $state = $el->validate_student_response(['sans1' => 'I said "Hello world" to fred'],
                $options, '"A random string"', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"I said \"Hello world\" to fred"', $state->contentsmodified);
        $this->assertEquals('\[ \text{I said &quot;Hello world&quot; to fred} \]', $state->contentsdisplayed);
    }

    public function test_validate_qm_within_string() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used ?, $ etc. within a string.
        $state = $el->validate_student_response(['sans1' => 'Lots of stuff:!$%^&*?@;'],
            $options, '"A random string"', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Lots of stuff:!$%^&*?@;"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Lots of stuff:!\$\%^&*?@;} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_broken_string() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '".'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\"."', $state->contentsmodified);
        $this->assertEquals('\[ \text{&quot;.} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_whitespace() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => ' Some whitespace  '], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('" Some whitespace  "', $state->contentsmodified);
        $this->assertEquals('\[ \text{ Some whitespace } \]', $state->contentsdisplayed);
    }

    public function test_validate_string_hideanswer() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'state', '"[SOME JSON]"');
        $el->set_parameter('options', 'hideanswer');
        $state = $el->validate_student_response(['state' => '[SOME MORE JSON]'], $options, '"[SOME JSON]"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"[SOME MORE JSON]"', $state->contentsmodified);
        $this->assertEquals('\[ \text{[SOME MORE JSON]} \]', $state->contentsdisplayed);
        $this->assertEquals('', $el->get_teacher_answer_display("[SOME JSON]", "\[ \text{[SOME MORE JSON]} \]"));
    }

    public function test_validate_string_string_empty() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $state = $el->validate_student_response(['sans1' => ''], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_string_string_explicitempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => '""'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        // Note here the student has used string quotes which are respected.
        $this->assertEquals('"\"\""', $state->contentsmodified);
        $this->assertEquals('\[ \text{&quot;&quot;} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_string_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => ''], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('""', $state->contentsmodified);
        $this->assertEquals('\[ \text{ } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_xss_4() {
        $options = new stack_options();
        $ta = '"Hello world"';
        $el = stack_input_factory::make('string', 'sans1', '"A random string"');

        $sa = '"<div onclick=\'dosuchandsuch\'></div>"';
        $cm = '"\"&lt;&#8203;div on&#0;click&#0;&#61;\'dosuchandsuch\'>&lt;&#8203;/div&gt;\""';
        $cd = '\[ \text{&quot;&lt;div on&#0;click&#0;&#61;&apos;dosuchandsuch&apos;&gt;&lt;/div&gt;&quot;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<div onmousemove     =\'dosuchandsuch\'></div>"';
        $cm = '"\"&lt;&#8203;div on&#0;mousemove     &#0;&#61;\'dosuchandsuch\'>&lt;&#8203;/div&gt;\""';
        $cd = '\[ \text{&quot;&lt;div on&#0;mousemove &#0;&#61;&apos;dosuchandsuch&apos;&gt;&lt;/div&gt;&quot;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);
    }

    public function test_validate_student_response_too_long() {
        $options = new stack_options();
        $el = stack_input_factory::make('string', 'sans1', '"Hello world"');
        // Maxima is very slow to parse long strings.
        $sa = '"Hell' . str_repeat('o', 1000) . ' world"';
        $state = $el->validate_student_response(['sans1' => $sa], $options, '"Hello world"',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);

        $sa = '"Hell' . str_repeat('o', 262144) . ' world"';
        $state = $el->validate_student_response(['sans1' => $sa], $options, 'x^2/(1+x^2)',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('too_long', $state->note);
        $this->assertEquals('Your input is longer than permitted by STACK.', $state->errors);
    }
}
