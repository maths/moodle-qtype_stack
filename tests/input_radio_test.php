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

// Unit tests for stack_radio_input.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_radio_input
 */
class input_radio_test extends qtype_stack_walkthrough_test_base {
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

    protected function make_radio($parameters = []) {
        $el = stack_input_factory::make('radio', 'ans1', $this->make_ta(), null, $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_radio() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1+x,true],[2+y,false]]', null, []);
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">' .
                '(Clear my choice)</label></div><div class="option">' .
                '<br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" ' .
                'id="stack1__ans1_1" data-stack-input-type="radio" /><label for="stack1__ans1_1">' .
                '<span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+x\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" ' .
                'data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(2+y\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+x\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_simple_casstring_radio() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1+x,true],[2+y,false]]', null,
                ['options' => 'casstring']);
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer">'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="" '
            . 'id="stack1__ans1_" data-stack-input-type="radio" />'
            . '<label for="stack1__ans1_">(Clear my choice)</label></div>'
            . '<div class="option"><br /></div><div class="option">'
            . '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" '
            . 'data-stack-input-type="radio" />'
            . '<label for="stack1__ans1_1"><code>1+x</code></label></div>'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" '
            . 'checked="checked" data-stack-input-type="radio" />'
            . '<label for="stack1__ans1_2">'
            . '<code>2+y</code></label></div>'
            . '</div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_no_correct_answer() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1,false],[2,false]]', null, []);
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
        $el = $this->make_radio();
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
        $el = stack_input_factory::make('radio', 'ans1', '[[1,true],[2,false]]', null, []);
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
        $el = stack_input_factory::make('radio', 'ans1', '[[1,true],[2,false]]', null, []);
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        // @codingStandardsIgnoreStart
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">(Clear my choice)</label></div><div class="option">' .
                '<br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_not_answered() {
        $el = $this->make_radio();
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_">(Clear my choice)</label></div><div class="option">' .
                '<br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(
                        stack_input::BLANK, [], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_default() {
        $el = $this->make_radio();
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">(Clear my choice)</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_casstring() {
        $el = $this->make_radio(['options' => 'casstring']);
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<div class="answer">'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="" id="stack1__ans1_" data-stack-input-type="radio" />'
            . '<label for="stack1__ans1_">(Clear my choice)</label></div>'
            . '<div class="option"><br /></div><div class="option">'
            . '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />'
            . '<label for="stack1__ans1_1"><code>x+1</code></label></div>'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />'
            . '<label for="stack1__ans1_2"><code>x+2</code></label></div>'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" '
            . 'checked="checked" data-stack-input-type="radio" /><label for="stack1__ans1_3">'
            . '<code>sin(pi*n)</code></label>'
            . '</div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <code>x+1</code>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latex() {
        $el = $this->make_radio(['options' => 'LaTeX']);
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">(Clear my choice)</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latexdisplay() {
        $el = $this->make_radio(['options' => 'LaTeXdisplay']);
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">(Clear my choice)</label></div><div class="option">' .
                '<br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+1\]</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+2\]</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[\sin \left( \pi\cdot n \right)\]</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+1\]</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latexdisplaystyle() {
        $el = $this->make_radio(['options' => 'LaTeXdisplaystyle']);
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">(Clear my choice)</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+1\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+2\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle \sin \left( \pi\cdot n \right)\)</span>' .
                '</span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_no_not_answered() {
        $el = $this->make_radio(['options' => 'nonotanswered']);
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="1" ' .
                'id="stack1__ans1_1" data-stack-input-type="radio" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
                $this->assertEquals($expected,
                    $el->render(new stack_input_state(
                    stack_input::BLANK, [], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_radio();
        $state = $el->validate_student_response(['ans1' => ''], $options, 'x+1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_radio();
        $state = $el->validate_student_response(['ans1' => '1'], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_x_plus_2() {
        $options = new stack_options();
        $el = $this->make_radio();
        $state = $el->validate_student_response(['ans1' => '2'], $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_question_level_options() {
        // @codingStandardsIgnoreStart
        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('multiplicationsign', 'none');

        $el = stack_input_factory::make('radio', 'ans1', '[[1+2,true],[2*x,false]]', $options, []);
        $el->adapt_to_model_answer('[[1+2,true],[2*x,false]]');
        // @codingStandardsIgnoreStart
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">(Clear my choice)</label></div><div class="option">' .
                '<br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" data-stack-input-type="radio" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(2\,x\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_radio_plots() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1',
                '[[1,true,plot(x,[x,-2,2],[y,-3,3])],[2,false,plot(x^2,[x,-2,2],[y,-3,3])],'
                . '[3,false,plot(x^3,[x,-2,2],[y,-3,3])]]',
                null, []);
        // @codingStandardsIgnoreEnd
        $render = $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null);
        // We don't test for the < at the start of the img tag as this is now protected as &lt; in the render.
        // However, the plot system does not use the LaTeX.
        $this->assertTrue(is_int(strpos($render, "img src='https://www.example.com/moodle/question/type/stack/plot.php")));
        $this->assertTrue(is_int(strpos($render,
                "alt='STACK auto-generated plot of x with parameters [[x,-2,2],[y,-3,3]]'")));
        $this->assertTrue(is_int(strpos($render,
                "alt='STACK auto-generated plot of x^2 with parameters [[x,-2,2],[y,-3,3]]'")));
        $this->assertTrue(is_int(strpos($render,
                "alt='STACK auto-generated plot of x^3 with parameters [[x,-2,2],[y,-3,3]]'")));
    }

    public function test_teacher_answer_html_notanswered() {
        $options = new stack_options();
        $ta = '[[notanswered,false,"n/a"],[A,false],[B,true]]';
        $el = stack_input_factory::make('radio', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
            'id="stack1__ans1_" checked="checked" data-stack-input-type="radio" /><label for="stack1__ans1_">' .
            'n/a</label></div>' .
            '<div class="option"><br /></div><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
            '<span class="nolink">\(A\)</span></span></label></div><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
            '<span class="nolink">\(B\)</span></span></label></div></div>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::BLANK, [''], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => ''], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals([], $state->contents);
        $this->assertEquals('', $state->contentsmodified);
        $correctresponse = ['ans1' => 2];
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));
    }

    public function test_teacher_answer_protect_string_html() {
        $options = new stack_options();
        $ta = '[[notanswered,false,"n/a"],["{",true],["[",false],["(",false]]';
        $el = stack_input_factory::make('radio', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $correctresponse = ['ans1' => 1];
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));
        $expected = 'A correct answer is: <ul><li>{</li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));

        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" ' .
            'value="" id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">n/a' .
            '</label></div><div class="option">' .
            '<br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" ' .
            'checked="checked" data-stack-input-type="radio" /><label for="stack1__ans1_1">{</label></div><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_2">[</label></div><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_3">(</label></div></div>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['1'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => '1'], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('"{"', $state->contentsmodified);
        // The response below is a complete edge case: mismatching curly brackets inside a string!
        // To fix this we need to dig into how Maxima creates LaTeX, and protects strings.
        // The place to fix this is in Maxima: tex("{"); gives the wrong result.
        // This applys to any strings in STACK/Maxima, so this isn't the place to record this failing unit test.
        // But, I'll ask the Maxima people to fix it and see when this unit test "breaks" to correct behaviour!
        $this->assertEquals('\[ \text{{} \]', $state->contentsdisplayed);
    }

    public function test_union() {
        $options = new stack_options();
        $ta = '[[%union(oo(-inf,0),oo(0,inf)),true],[%union({1},{2}),false],[union({1},{4}),false],' .
            '[A,false,%union({1},{3})]]';
        $el = stack_input_factory::make('radio', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<div class="answer"><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="" id="stack1__ans1_" checked="checked" ' .
            'data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_">(Clear my choice)</label></div><div class="option"><br /></div>' .
            '<div class="option"><input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" ' .
            'data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation"><span class="nolink">' .
            '\(\left( -\infty ,\, 0\right) \cup \left( 0,\, \infty \right)\)</span></span></label></div>' .
            '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" ' .
            'data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
            '<span class="nolink">\(\left \{1 \right \} \cup \left \{2 \right \}\)</span></span></label>' .
            '</div><div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" ' .
            'data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
            '<span class="nolink">\(\left \{1 , 4 \right \}\)</span></span></label></div><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="4" id="stack1__ans1_4" data-stack-input-type="radio" />' .
            '<label for="stack1__ans1_4">' .
            '<span class="filter_mathjaxloader_equation"><span class="nolink">' .
            '\(\left \{1 \right \} \cup \left \{3 \right \}\)</span></span></label></div></div>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
            stack_input::BLANK, [''], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => ''], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals([], $state->contents);
        $this->assertEquals('', $state->contentsmodified);
        $correctresponse = ['ans1' => 1];
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));

        $el->adapt_to_model_answer($ta);
        $correctresponse = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
            '<span class="nolink">\(\left( -\infty ,\, 0\right) \cup \left( 0,\, \infty \right)\)</span>' .
            '</span></li></ul>';
        $this->assertEquals($correctresponse,
            $el->get_teacher_answer_display(null, null));
    }

    public function test_decimals() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $ta = '[[3.1415,false],[[a,b,c,2.78],true]]';
        $el = stack_input_factory::make('radio', 'ans1', $ta, $options, ['options' => '']);
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" ' .
            'value="" id="stack1__ans1_" data-stack-input-type="radio" /><label for="stack1__ans1_">' .
            '(Clear my choice)</label></div><div class="option"><br /></div><div class="option">' .
            '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" checked="checked" ' .
            'data-stack-input-type="radio" /><label for="stack1__ans1_1"><span ' .
            'class="filter_mathjaxloader_equation"><span class="nolink">\(3{,}1415\)</span></span>' .
            '</label></div><div class="option"><input type="radio" name="stack1__ans1" value="2" ' .
            'id="stack1__ans1_2" data-stack-input-type="radio" /><label for="stack1__ans1_2">' .
            '<span class="filter_mathjaxloader_equation"><span class="nolink">' .
            '\(\left[ a ; b ; c ; 2{,}78 \right] \)</span></span></label></div></div>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
            stack_input::SCORE, ['1'], '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(['ans1' => '1'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(['1'], $state->contents);
        $this->assertEquals('3.1415', $state->contentsmodified);
        $state = $el->validate_student_response(['ans1' => '2'],
            $options, $ta, new stack_cas_security());
        $this->assertEquals(['2'], $state->contents);
        $this->assertEquals('[a,b,c,2.78]', $state->contentsmodified);
        $this->assertEquals($ta, $el->get_teacher_answer());
        $el->adapt_to_model_answer($ta);
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation"><span class="nolink">' .
            '\(\left[ a ; b ; c ; 2{,}78 \right] \)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }
}
