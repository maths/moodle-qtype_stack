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

use qtype_stack_walkthrough_test_base;
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

// Unit tests for stack_dropdown_input.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_dropdown_input
 */
class input_dropdown_test extends qtype_stack_walkthrough_test_base {
    protected function expected_choices() {
        return [
            '' => stack_string('notanswered'),
            '1' => 'x+1',
            '2' => 'x+2',
            '3' => 'sin(pi*n)',
        ];
    }

    protected function expected_choices_latex() {
        return [
            '' => stack_string('notanswered'),
            '1' => 'x+1',
            '2' => 'x+2',
            '3' => 'sin(\pi*n)',
        ];
    }

    protected function make_dropdown($parameters = []) {
        $el = stack_input_factory::make('dropdown', 'ans1', $this->make_ta(), null, $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_dropdown() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,true],[2+y,false]]', null,   );
        // @codingStandardsIgnoreEnd
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" '
                . 'class="select menustack1__ans1" name="stack1__ans1">'
                . '<option value="">(Clear my choice)</option><option value="1"><code>1+x</code></option>'
                . '<option selected="selected" value="2"><code>2+y</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_no_correct_answer() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,false],[2,false]]', null, []);
        // @codingStandardsIgnoreEnd
        $expected = '<div class="error"><p><i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The input has ' .
                  'generated the following runtime error which prevents you from answering. Please contact your teacher." ' .
                  'aria-label="The input has generated the following runtime error which prevents you from answering. Please ' .
                  'contact your teacher."></i>The input has generated the following runtime error which prevents you from ' .
                  'answering. Please contact your teacher.</p>' .
                  '<p>The teacher did not indicate at least one correct answer.</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_bad_teacheranswer() {
        $el = $this->make_dropdown();
        $el->adapt_to_model_answer('[x]');
        $expected = '<div class="error"><p><i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The input has ' .
                  'generated the following runtime error which prevents you from answering. Please contact your teacher." ' .
                  'aria-label="The input has generated the following runtime error which prevents you from answering. Please ' .
                  'contact your teacher."></i>The input has generated the following runtime error which prevents you from ' .
                  'answering. Please contact your teacher.</p>' .
                  '<p>The model answer field for this input is malformed: <code>[x]</code>.' .
                  ' The teacher did not indicate at least one correct answer.</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_duplicate_values() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,true],[2,false]]', null, []);
        $el->adapt_to_model_answer('[[1,true],[1,false]]');
        // @codingStandardsIgnoreEnd
        $expected = '<div class="error"><p><i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The input has ' .
                  'generated the following runtime error which prevents you from answering. Please contact your teacher." ' .
                  'aria-label="The input has generated the following runtime error which prevents you from answering. Please ' .
                  'contact your teacher."></i>The input has generated the following runtime error which prevents you from ' .
                  'answering. Please contact your teacher.</p>' .
                  '<p>Duplicate values have been found when generating the input options.</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_duplicate_values_ok() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,true],[2,false]]', null, []);
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        // @codingStandardsIgnoreEnd
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                . '<option value="">(Clear my choice)</option><option value="1"><code>1</code></option>'
                . '<option selected="selected" value="2"><code>1</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_not_answered() {
        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, [], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_x_plus_1() {
        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+1'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, ['x+1'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_string() {
        $el = $this->make_dropdown();
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option>'
            . '<option value="1"><code>x+1</code></option><option value="2"><code>x+2</code></option>'
            . '<option selected="selected" value="3"><code>sin(pi*n)</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_nonotanswered() {
        $el = $this->make_dropdown(['options' => 'nonotanswered']);
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="1"><code>x+1</code></option><option value="2"><code>x+2</code></option>'
            . '<option selected="selected" value="3"><code>sin(pi*n)</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_latex() {
        $el = $this->make_dropdown(['options' => 'LaTeX']);
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option><option value="1">\(x+1\)</option>'
            . '<option value="2">\(x+2\)</option>'
            . '<option selected="selected" value="3">\(\sin \left( \pi\cdot n \right)\)</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_latexdisplay() {
        $el = $this->make_dropdown(['options' => 'LaTeXdisplay']);
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option><option value="1">\[x+1\]</option>'
            . '<option value="2">\[x+2\]</option>'
            . '<option selected="selected" value="3">\[\sin \left( \pi\cdot n \right)\]</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_x_plus_2() {
        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+2'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, ['x+3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_disabled() {
        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, [], '', '', '', '', ''), 'stack1__ans1', true, null));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(['ans1' => ''], $options, 'x+1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(['ans1' => '1'], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_x_plus_2() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(['ans1' => '2'], $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_string_value() {
        $options = new stack_options();
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,true],[2+x^2,false],[{},false,"None of these"]]');
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option><option value="1"><code>1+x</code></option>'
            . '<option selected="selected" value="2"><code>2+x^2</code></option>'
            . '<option value="3">None of these</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => '3'], $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(['3'], $state->contents);
        $this->assertEquals('{}', $state->contentsmodified);
    }

    public function test_teacher_answer() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,true],[{},false,"None of these"]]');

        $correctresponse = ['ans1' => 2];
        $this->assertEquals($correctresponse,
                $el->get_correct_response('[[1+x,false],[2+x^2,true],[{},false,"None of these"]]'));

        $correctresponse = 'A correct answer is: <code>2+x^2</code>';
        $this->assertEquals($correctresponse,
                $el->get_teacher_answer_display(null, null));
    }

    public function test_teacher_answer_display() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]');

        $correctresponse = ['ans1' => 3];
        $this->assertEquals($correctresponse,
                $el->get_correct_response('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]'));

        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]');
        $correctresponse = 'A correct answer is: <code>"None of these"</code>';
        $this->assertEquals($correctresponse,
                $el->get_teacher_answer_display(null, null));
    }

    public function test_teacher_answer_display_hideanswer() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]');
        $el->set_parameter('options', 'hideanswer');

        $correctresponse = '';
        $this->assertEquals($correctresponse,
            $el->get_teacher_answer_display(null, null));
    }

    public function test_teacher_answer_html_entities() {
        $options = new stack_options();
        $ta = '[[A,false,"n/a"],[B,true,"&ge;"],[C,false,"&le;"],[D,false,"="],[E,false,"?"]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" ' .
                'class="select menustack1__ans1" name="stack1__ans1">' .
                '<option value="">(Clear my choice)</option><option selected="selected" value="1">n/a</option>' .
                '<option value="2">&ge;</option><option value="3">&le;</option><option value="4">=</option>' .
                '<option value="5">?</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['1'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => '1'], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(['1'], $state->contents);
        $this->assertEquals('A', $state->contentsmodified);
        $correctresponse = ['ans1' => 2];
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));
    }

    public function test_teacher_answer_html_notanswered() {
        $options = new stack_options();
        $ta = '[[notanswered,false,"n/a"],[A,false],[B,true]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" ' .
            'name="stack1__ans1">' .
            '<option selected="selected" value="">n/a</option>' .
            '<option value="1"><code>A</code></option><option value="2"><code>B</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::BLANK, [''], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => ''], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals([], $state->contents);
        $this->assertEquals('', $state->contentsmodified);
        $correctresponse = ['ans1' => 2];
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));
    }

    public function test_union() {
        $options = new stack_options();
        $ta = '[[%union(oo(-inf,0),oo(0,inf)),true],[%union({1},{2}),false],[union({1},{4}),false],' .
            '[A,false,%union({1},{3})]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">' .
            '<option selected="selected" value="">(Clear my choice)</option>' .
            '<option value="1"><code>union(oo(-inf,0),oo(0,inf))</code></option>' .
            '<option value="2"><code>union({1},{2})</code></option>'.
            '<option value="3"><code>union({1},{4})</code></option>' .
            '<option value="4"><code>union({1},{3})</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
            stack_input::BLANK, [''], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => ''], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals([], $state->contents);
        $this->assertEquals('', $state->contentsmodified);
        $correctresponse = ['ans1' => 1];
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));

        $el->adapt_to_model_answer($ta);
        $correctresponse = 'A correct answer is: <code>union(oo(-inf,0),oo(0,inf))</code>';
        $this->assertEquals($correctresponse,
            $el->get_teacher_answer_display(null, null));
    }

    public function test_decimals() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $ta = '[[3.1415,true],[[a,b,c,2.78],false],[2.78,false,"Euler constant"]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, $options, ['options' => '']);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select ' .
            'menustack1__ans1" name="stack1__ans1"><option selected="selected" value="">(Clear my choice)' .
            '</option><option value="1"><code>3,1415</code></option><option value="2">' .
            '<code>[a;b;c;2,78]</code></option><option value="3">Euler constant</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
            stack_input::BLANK, [''], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select ' .
            'menustack1__ans1" name="stack1__ans1"><option value="">(Clear my choice)' .
            '</option><option selected="selected" value="1"><code>3,1415</code></option><option value="2">' .
            '<code>[a;b;c;2,78]</code></option><option value="3">Euler constant</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
            stack_input::SCORE, ['1'], '', '', '', '', ''), 'stack1__ans1', false, null));

        $state = $el->validate_student_response(['ans1' => '1'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(['1'], $state->contents);
        $this->assertEquals('3.1415', $state->contentsmodified);
        $state = $el->validate_student_response(['ans1' => '2'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(['2'], $state->contents);
        $this->assertEquals('[a,b,c,2.78]', $state->contentsmodified);
        $state = $el->validate_student_response(['ans1' => '3'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(['3'], $state->contents);
        $this->assertEquals('2.78', $state->contentsmodified);

        $this->assertEquals($ta, $el->get_teacher_answer());
        $el->adapt_to_model_answer($ta);
        $expected = 'A correct answer is: <code>3,1415</code>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }
}
