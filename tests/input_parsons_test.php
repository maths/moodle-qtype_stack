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

/**
 * Unit tests for stack_parsons_input.
 *
 * @package    qtype_stack
 * @copyright  2024 The University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_parsons_input
 */
final class input_parsons_test extends qtype_stack_testcase {

    public function test_render_blank(): void {

        $el = stack_input_factory::make('parsons', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" '
                . 'size="16.5" spellcheck="false" class="maxima-string" style="display:none" value="" '
                . 'data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_hello_world(): void {

        $el = stack_input_factory::make('parsons', 'ans1', '"Hello world"');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" autocapitalize="none" '
                . 'size="16.5" spellcheck="false" class="maxima-string" style="display:none" value="0" '
                . 'data-stack-input-type="string" />',
                $el->render(new stack_input_state(stack_input::VALID, ['0'], '', '', '', '', ''),
                        'stack1__ans1', false, null));
        // Parson's input type never gets displayed.
        $this->assertEquals('',
                $el->get_teacher_answer_display('"Hello world"', '\\text{Hello world}'));
    }

    public function test_validate_parsons_string_input(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'Hello world'], $options, $ta,
                new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">&quot;Hello world&quot;</span>', $state->contentsdisplayed);
        $this->assertEquals('',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_parsons_state_input(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '[[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]'],
            $options, $ta,
                new stack_cas_security());

        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('"[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]"', $state->contentsmodified);
        $this->assertEquals('\[ \text{[{&quot;used&quot;:[[[]]],&quot;available&quot;:[&quot;hello&quot;,&quot;world&quot;]},0]} \]', $state->contentsdisplayed);
        $this->assertEquals('',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $expected = 'sana1: "[[{\\"used\\":[[[]]],\\"available\\":[\\"hello\\",\\"world\\"]},0]]" [valid]';
        $this->assertEquals($expected,
            $el->summarise_response('sana1', $state, null));
    }

    public function test_validate_parsons_state_input_malformed(): void {
        // We should never get malformed input back from a user's browser, but when we do the input needs to be OK.
        $options = new stack_options();
        $ta = "\"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]\"";
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '{"used":[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        // The input does not modify the string contents.
        $this->assertEquals('"{\"used\":[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]"',
            $state->contentsmodified);
        $this->assertEquals(
            '<span class="stacksyntaxexample">&quot;{\&quot;used\&quot;:[[]]],\&quot;available\&quot;:' .
            '[\&quot;aGVsbG8=\&quot;,\&quot;d29ybGQ=\&quot;]},0]]&quot;</span>', $state->contentsdisplayed);
        $this->assertEquals('',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $expected = 'sana1: "{\"used\":[[]]],\"available\":' .
            '[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]" [invalid]';
        $this->assertEquals($expected,
            $el->summarise_response('sana1', $state, null));
    }

    public function test_validate_string_singlequotes_input(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        // Note here the student has used string quotes which are no longer respected.
        $state = $el->validate_student_response(['sans1' => '\'[[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]\''],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals('"\'[[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]]\'"', $state->contentsmodified);
        // This will fail internal evaluation in the Parson's decode filter due to the extra quotes, so will remain unhashed.
        $this->assertEquals(
            '<span class="stacksyntaxexample">&quot;\'[[{\&quot;used\&quot;:[[[]]],\&quot;available\&quot;:[\&quot;aGVsbG8=\&quot;,\&quot;d29ybGQ=\&quot;]},0]]\'&quot;</span>',
             $state->contentsdisplayed
        );
    }

    public function test_validate_remains_hashed_if_invalid_state(): void {

        $options = new stack_options();
        $ta = "\"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]\"";
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '[\"aGVsbG8=\",\"d29ybGQ=\"]'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals('"[\\\\\"aGVsbG8=\\\\\",\\\\\"d29ybGQ=\\\\\"]"', $state->contentsmodified);
        // This will fail internal evaluation in the Parson's decode filter due to invalid state, so will remain unhashed.
        $this->assertEquals('<span class="stacksyntaxexample">&quot;[\\\\\&quot;aGVsbG8=\\\\\&quot;,' .
            '\\\\\&quot;d29ybGQ=\\\\\&quot;]&quot;</span>', $state->contentsdisplayed);
    }

    public function test_validate_remains_hashed_if_invalid_timestamp(): void {

        $options = new stack_options();
        $ta = "\"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]\"";
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(
            ['sans1' => '[[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},"I am invalid timestamp"]]'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals('"[[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},\"I am invalid timestamp\"]]"',
            $state->contentsmodified);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        // This will fail internal evaluation in the Parson's decode filter due to invalid state, so will remain unhashed.
        $this->assertEquals(
            '<span class="stacksyntaxexample">&quot;[[{\&quot;used\&quot;:[[[]]],\&quot;available\&quot;:' .
            '[\&quot;aGVsbG8=\&quot;,\&quot;d29ybGQ=\&quot;]},\&quot;I am invalid timestamp\&quot;]]&quot;</span>',
            $state->contentsdisplayed
        );
    }

    public function test_validate_parsons_whitespace(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => ' [[{"used":[[[]]],"available":["aGVsbG8=","d29ybGQ="]},0]]  '],
                $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"[{\"used\":[[[]]],\"available\":[\"aGVsbG8=\",\"d29ybGQ=\"]},0]"',
            $state->contentsmodified);
        $this->assertEquals('\[ \text{[{&quot;used&quot;:[[[]]],&quot;available&quot;:'
                . '[&quot;hello&quot;,&quot;world&quot;]},0]} \]', $state->contentsdisplayed);
    }

    public function test_validate_parsons_empty(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $state = $el->validate_student_response(['sans1' => ''], $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_parsons_explicitempty(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => '""'], $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals('"\"\""', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">&quot;\&quot;\&quot;&quot;</span>', $state->contentsdisplayed);
    }

    public function test_validate_parsons_allowempty(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => ''], $options, $ta, new stack_cas_security());
        // We do not allow empty in Parson's block
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals('""', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">&quot;&quot;</span>', $state->contentsdisplayed);
    }

    public function test_validate_student_response_xss_4(): void {

        $options = new stack_options();
        $ta = '"[[{\"used\":[[[\"UzE=\",\"UzI=\",\"UzQ=\",\"UzU=\",\"UzM=\",\"QzY=\"]]],\"available\":[]},1738672937]]"';
        $el = stack_input_factory::make('parsons', 'sans1', $ta);

        $sa = '"<div onclick=\'dosuchandsuch\'></div>"';
        $cm = '"\"&lt;&#8203;div on&#0;click&#0;&#61;\'dosuchandsuch\'>&lt;&#8203;/div&gt;\""';
        $cd = '<span class="stacksyntaxexample">&quot;\&quot;&lt;div on&#0;click&#0;&#61;\'dosuchandsuch\'&gt;&lt;/div&gt;\&quot;&quot;</span>';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<div onmousemove     =\'dosuchandsuch\'></div>"';
        $cm = '"\"&lt;&#8203;div on&#0;mousemove     &#0;&#61;\'dosuchandsuch\'>&lt;&#8203;/div&gt;\""';
        $cd = '<span class="stacksyntaxexample">&quot;\&quot;&lt;div on&#0;mousemove     &#0;&#61;\'dosuchandsuch\'&gt;&lt;/div&gt;\&quot;&quot;</span>';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('Invalid state for Parson\'s input.', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);
    }
}
