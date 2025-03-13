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
use stack_textarea_input;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');
require_once(__DIR__ . '/../stack/input/textarea/textarea.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');

// Unit tests for stack_textarea_input.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_textarea_input
 */
class input_textarea_test extends qtype_stack_testcase {
    public function test_render_blank() {
        $el = stack_input_factory::make('textArea', 'ans1', null);
        $this->assertEquals('<textarea name="st_ans1" id="st_ans1" autocapitalize="none" spellcheck="false" class="maxima-list" ' .
                'rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=","></textarea>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'st_ans1', false, null));
    }

    public function test_render_pre_filled() {
        $el = stack_input_factory::make('textArea', 'test', null);
        $this->assertEquals('<textarea name="st_ans1" id="st_ans1" autocapitalize="none" spellcheck="false" ' .
                'class="maxima-list" rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' .
                "1\n1/sum([1,3])\nmatrix([1],[2])</textarea>",
                $el->render(new stack_input_state(
                        stack_input::VALID, ["1", "1/sum([1,3])", "matrix([1],[2])"], '', '', '', '', ''),
                        'st_ans1', false, null));
    }

    public function test_render_pre_syntaxhint() {
        $el = stack_input_factory::make('textArea', 'test', null, null, ['syntaxHint' => '[y=?, z=?]']);
        $this->assertEquals('<textarea name="st_ans1" id="st_ans1" autocapitalize="none" spellcheck="false" ' .
                'class="maxima-list" rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' .
                    "y = ?\nz = ?</textarea>",
        $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                            'st_ans1', false, null));
    }

    public function test_render_pre_syntaxhint_placeholder() {
        $el = stack_input_factory::make('textArea', 'test', null, null,
            ['syntaxHint' => '[y=?, z=?]', 'syntaxAttribute' => 1]);
        $this->assertEquals('<textarea name="st_ans1" id="st_ans1" autocapitalize="none" spellcheck="false" ' .
            'class="maxima-list" placeholder="y = ?' ."\n" . 'z = ?" rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=","></textarea>',
            $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                'st_ans1', false, null));
    }

    public function test_render_disabled() {
        $el = stack_input_factory::make('textArea', 'input', null);
        $this->assertEquals('<textarea name="st_ans1" id="st_ans1" autocapitalize="none" spellcheck="false" ' .
                'class="maxima-list" rows="5" cols="20" readonly="readonly" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=","></textarea>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'st_ans1', true, null));
    }

    public function test_maxima_to_response_array_1() {
        $el = stack_input_factory::make('textArea', 'input', '[x=1,x=2]');
        $this->assertEquals($el->maxima_to_response_array('[x=1,x=2]'),
            ['input' => "x = 1\nx = 2", 'input_val' => '[x=1,x=2]']);
    }

    public function test_validate_student_response_single_var_chars_on() {
        // Check the single variable character option is tested.
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x^2=-7*x,a*b=2]');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => "x^2=-7*x\nab=2"], $options, '[x^2=-7*x,a*b=2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = -7*x,a*b = 2]', $state->contentsmodified);
        $this->assertEquals('<table style="vertical-align: middle;" border="0" cellpadding="2" cellspacing="0" align="center">' .
                '<tbody><tr><td><div align="center">\(\displaystyle x^2=-7\cdot x \)</div></td></tr>' .
                '<tr><td><div align="center">\(\displaystyle a\cdot b=2 \)</div></td></tr>' .
                '</tbody></table>', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a , b , x \right]\) ', $state->lvars);
        $this->assertEquals('<textarea name="sans1" id="sans1" autocapitalize="none" spellcheck="false" ' .
                'class="maxima-list" rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">x^2=-7*x'."\n".'ab=2</textarea>',
                $el->render($state, 'sans1', false, null));

        $state = $el->validate_student_response(['sans1' => "x^2=-7x\nab=2", 'sans1_val' => "[x^2=-7x,ab=2]"],
                $options, '[x^2=-7*x,a*b=2]', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('[x^2 = -7*x,a*b = 2]', $state->contentsmodified);
        $this->assertEquals('\( \left[ a , b , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_single_var_chars_off() {
        // Check the single variable character option is tested.
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x^2=-7*x,ab=2]');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => "x^2=-7x\nab=2"], $options, '[x^2=-7*x,ab=2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = -7*x,ab = 2]', $state->contentsmodified);
        $this->assertEquals('\( \left[ {\it ab} , x \right]\) ', $state->lvars);

        $state = $el->validate_student_response(['sans1' => "x^2=-7x\nab=2", 'sans1_val' => "[x^2=-7x,ab=2]"],
                $options, '[x^2=-7*x,ab=2]', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('[x^2 = -7*x,ab = 2]', $state->contentsmodified);
        $this->assertEquals('\( \left[ {\it ab} , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_single_var_chars_raw() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x^2=-7*x,ab=2]');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => "x^2=-7x\nab=2"], $options, '[x^2=-7*x,ab=2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = -7*x,ab = 2]', $state->contentsmodified);
        $this->assertEquals('\( \left[ {\it ab} , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_single_var_chars_raw_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x^2=-7*x,[a=1,b=2]]');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => "x^2=-7x\n[a=1,b=2"], $options, '[x^2=-7*x,[a=1,b=2]]',
                new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        // This is now empty.
        $this->assertEquals('', $state->errors);
        $this->assertEquals('missing_stars | missingRightBracket', $state->note);
        $this->assertEquals('<table style="vertical-align: middle;" border="0" cellpadding="2" cellspacing="0" align="center">' .
                '<tbody><tr><td><div align="center">\(\displaystyle x^2=-7\cdot x \)</div></td></tr><tr>' .
                '<td><div align="center"><span class="stacksyntaxexample">[a=1,b=2</span></div>' .
                '<div class="alert alert-danger stackinputerror">You have a missing right bracket ' .
                '<span class="stacksyntaxexample">]</span> ' .
                'in the expression: <span class="stacksyntaxexample">[a=1,b=2</span>.</div></td></tr>' .
                '</tbody></table>', $state->contentsdisplayed);
        $this->assertEquals('<textarea name="sans1" id="sans1" autocapitalize="none" spellcheck="false" class="maxima-list" ' .
                'rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">x^2=-7x'."\n".'[a=1,b=2</textarea>',
                $el->render($state, 'sans1', false, null));
    }

    public function test_validate_student_response_single_var_chars_raw_invalid_compact() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x^2=-7*x,[a=1,b=2]]');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('showValidation', 3);
        $state = $el->validate_student_response(['sans1' => "x^2=-7x\n[a=1,b=2"], $options, '[x^2=-7*x,[a=1,b=2]]',
                new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        // This is now empty.
        $this->assertEquals('', $state->errors);
        $this->assertEquals('missing_stars | missingRightBracket', $state->note);
        $this->assertEquals('\(\displaystyle x^2=-7\cdot x \) <br/>' .
                '<span class="stacksyntaxexample">[a=1,b=2</span> ' .
                'You have a missing right bracket <span class="stacksyntaxexample">]</span> ' .
                'in the expression: <span class="stacksyntaxexample">[a=1,b=2</span>. <br/>', $state->contentsdisplayed);
        $this->assertEquals('<textarea name="sans1" id="sans1" autocapitalize="none" spellcheck="false" class="maxima-list" ' .
                'rows="5" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">x^2=-7x'."\n".'[a=1,b=2</textarea>',
                $el->render($state, 'sans1', false, null));
    }

    public function test_validate_student_response_same_type_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x=1,{1}]');
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(['sans1' => "x=1\n1"], $options, '[x=1,{1}]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_same_type_false_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x=1,{1}]');
        $el->set_parameter('sameType', false);
        // Student has more lines than the teacher, so extra lines are ignored.
        $state = $el->validate_student_response(['sans1' => "x=1\n1\nx=2"], $options, '[x=1,{1}]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_same_type_true_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x=1,{1}]');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => "x=1\n1"], $options, '[x=1,{1}]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Your answer should be a set, but', substr($state->errors, 0, 32));
    }

    public function test_validate_student_response_same_type_true_valid_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x=1,{1}]');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => "x=1\n{1}"], $options, '[x=1,{1}]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_same_type_true_valid_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x=1,{1}]');
        $el->set_parameter('sameType', true);
        // Student has more lines than the teacher, so extra lines are ignored.
        $state = $el->validate_student_response(['sans1' => "x=1\n{1}\nx=2"], $options, '[x=1,{1}]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_same_type_true_valid_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('textArea', 'sans1', '[x=1,x=2,x=3]');
        // Long answer than re-sizes the rendered version.
        $state = $el->validate_student_response(['sans1' => "x=1\nx=2\nx=3\nx=4\nx=5\nx=6\nx=7\nx=8\nx=9"],
            $options, '[x=1,x=2,x=3]', new stack_cas_security());
        $this->assertEquals('', $state->errors);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('<textarea name="sans1" id="sans1" autocapitalize="none" spellcheck="false" ' .
            'class="maxima-list" rows="10" cols="20" data-stack-input-type="textarea" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' . "x=1\nx=2\nx=3\nx=4\nx=5\nx=6\nx=7\nx=8\nx=9</textarea>",
            $el->render($state, 'sans1', false, null));
    }
}

/**
 * Test helper class that exploses some protected methods.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_stack_textarea_input extends stack_textarea_input {
    // @codingStandardsIgnoreLine
    public function tokenize_list($in) {
        return parent::tokenize_list($in);
    }
}
