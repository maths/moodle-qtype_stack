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

// Unit tests for stack_parsons_input.
//
// @copyright  2024 The University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_parsons_input
 */
class input_parsons_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('parsons', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" '
                . 'size="16.5" spellcheck="false" class="maxima-string" style="display:none" value="" '
                . 'data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_hello_world() {
        $el = stack_input_factory::make('parsons', 'ans1', '"Hello world"');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" '
                . 'size="16.5" spellcheck="false" class="maxima-string" style="display:none" value="0" '
                . 'data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, ['0'], '', '', '', '', ''),
                        'stack1__ans1', false, null));
        // Parson's input type never gets displayed
        $this->assertEquals('',
                $el->get_teacher_answer_display('"Hello world"', '\\text{Hello world}'));
    }

    public function test_validate_parsons_string_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'Hello world'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Hello world} \]', $state->contentsdisplayed);
        $this->assertEquals('',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_parsons_state_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '[[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]'],
            $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"[[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]"', $state->contentsmodified);
        $this->assertEquals('\[ \text{[[{&quot;used&quot;:[[[]]],&quot;available&quot;:'
                . '[&quot;hello&quot;,&quot;world&quot;]},0]]} \]', $state->contentsdisplayed);
        $this->assertEquals('',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_string_singlequotes_input() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are no longer respected.
        $state = $el->validate_student_response(['sans1' => '\'[[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]\''], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"\'[[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]\'"', $state->contentsmodified);
        // This will fail internal evaluation in the Parson's decode filter due to the extra quotes, so will remain unhashed.
        $this->assertEquals('\[ \text{&apos;[[{&quot;used&quot;:[[[]]],&quot;available&quot;:[&quot;aGVsbG8=&quot;,&quot;d29ybGQ=&quot;]},0]]&apos;} \]', $state->contentsdisplayed);
    }

    public function test_validate_remains_hashed_if_invalid_state() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used ?, $ etc. within a string.
        $state = $el->validate_student_response(['sans1' => '["aGVsbG8=","d29ybGQ="]'],
            $options, '"A random string"', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"[\"aGVsbG8=\",\"d29ybGQ=\"]"', $state->contentsmodified);
        // This will fail internal evaluation in the Parson's decode filter due to invalid state, so will remain unhashed.
        $this->assertEquals('\[ \text{[&quot;aGVsbG8=&quot;,&quot;d29ybGQ=&quot;]} \]', $state->contentsdisplayed);
    }

    public function test_validate_remains_hashed_if_invalid_timestamp() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        // Note here the student has used ?, $ etc. within a string.
        $state = $el->validate_student_response(['sans1' => '[[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},"I am invalid timestamp"]]'],
            $options, '"A random string"', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"[[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},\"I am invalid timestamp\"]]"',
            $state->contentsmodified);
        // This will fail internal evaluation in the Parson's decode filter due to invalid state, so will remain unhashed.
        $this->assertEquals('\[ \text{[[{&quot;used&quot;:[[[]]],&quot;available&quot;:[&quot;aGVsbG8=&quot;,&quot;d29ybGQ=&quot;]},&quot;I am invalid timestamp&quot;]]} \]', $state->contentsdisplayed);
    }

    public function test_validate_parsons_whitespace() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => ' [[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]  '], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('" [[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]  "',
            $state->contentsmodified);
        $this->assertEquals('\[ \text{[[{&quot;used&quot;:[[[]]],&quot;available&quot;:'
                . '[&quot;hello&quot;,&quot;world&quot;]},0]]} \]', $state->contentsdisplayed);
    }

    public function test_validate_parsons_empty() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $state = $el->validate_student_response(['sans1' => ''], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_parsons_explicitempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => '""'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        // Note here the student has used string quotes which are respected.
        $this->assertEquals('"\"\""', $state->contentsmodified);
        $this->assertEquals('\[ \text{&quot;&quot;} \]', $state->contentsdisplayed);
    }

    public function test_validate_parsons_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');
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
        $el = stack_input_factory::make('parsons', 'sans1', '"A random string"');

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
}
