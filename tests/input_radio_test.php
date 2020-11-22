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

// Unit tests for stack_radio_input.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_radio_input_test extends qtype_stack_walkthrough_test_base {
    protected function expected_choices() {
        return array(
            '' => stack_string('notanswered'),
            '1' => 'x+1',
            '2' => 'x+2',
            '3' => 'sin(pi*n)'
        );
    }

    protected function expected_choices_latex() {
        return array(
            '' => stack_string('notanswered'),
            '1' => 'x+1',
            '2' => 'x+2',
            '3' => 'sin(\pi*n)'
        );
    }

    protected function make_radio($parameters = array()) {
        $el = stack_input_factory::make('radio', 'ans1', $this->make_ta(), null, $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_radio() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1+x,true],[2+y,false]]', null, array());
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div><div class="option">' .
                '<br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+x\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(2+y\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+x\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_simple_casstring_radio() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1+x,true],[2+y,false]]', null,
                array('options' => 'casstring'));
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer">'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="" id="stack1__ans1_" />'
            . '<label for="stack1__ans1_">(No answer given)</label></div>'
            . '<div class="option"><br /></div><div class="option">'
            . '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />'
            . '<label for="stack1__ans1_1"><code>1+x</code></label></div>'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" />'
            . '<label for="stack1__ans1_2">'
            . '<code>2+y</code></label></div>'
            . '</div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_no_correct_answer() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1,false],[2,false]]', null, array());
        // @codingStandardsIgnoreEnd
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>The teacher did not indicate at least one correct answer.</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_bad_teacheranswer() {
        $el = $this->make_radio();
        $el->adapt_to_model_answer('[x]');
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>The model answer field for this input is malformed: <code>[x]</code>.'
                .' The teacher did not indicate at least one correct answer.</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_duplicate_values() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1,true],[2,false]]', null, array());
        $el->adapt_to_model_answer('[[1,true],[1,false]]');
        // @codingStandardsIgnoreEnd
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>Duplicate values have been found when generating the input options.</p>'
                .'</div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_duplicate_values_ok() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1', '[[1,true],[2,false]]', null, array());
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        // @codingStandardsIgnoreStart
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div><div class="option">' .
                '<br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_not_answered() {
        $el = $this->make_radio();
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div><div class="option">' .
                '<br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_default() {
        $el = $this->make_radio();
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_casstring() {
        $el = $this->make_radio(array('options' => 'casstring'));
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<div class="answer">'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="" id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div>'
            . '<div class="option"><br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />'
            . '<label for="stack1__ans1_1"><code>x+1</code></label></div>'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" /><label for="stack1__ans1_2"><code>x+2</code></label></div>'
            . '<div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" /><label for="stack1__ans1_3">'
            . '<code>sin(pi*n)</code></label>'
            . '</div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <code>x+1</code>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latex() {
        $el = $this->make_radio(array('options' => 'LaTeX'));
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latexdisplay() {
        $el = $this->make_radio(array('options' => 'LaTeXdisplay'));
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div><div class="option">' .
                '<br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+1\]</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+2\]</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[\sin \left( \pi\cdot n \right)\]</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+1\]</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latexdisplaystyle() {
        $el = $this->make_radio(array('options' => 'LaTeXdisplaystyle'));
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+1\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+2\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle \sin \left( \pi\cdot n \right)\)</span>' .
                '</span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_no_not_answered() {
        $el = $this->make_radio(array('options' => 'nonotanswered'));
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div>' .
                '<div class="option"><input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
                $this->assertEquals($expected,
                    $el->render(new stack_input_state(
                    stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_radio();
        $state = $el->validate_student_response(array('ans1' => ''), $options, 'x+1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_radio();
        $state = $el->validate_student_response(array('ans1' => '1'), $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_x_plus_2() {
        $options = new stack_options();
        $el = $this->make_radio();
        $state = $el->validate_student_response(array('ans1' => '2'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_question_level_options() {
        // @codingStandardsIgnoreStart
        $options = new stack_options();
        $options->set_option('simplify', false);
        $options->set_option('multiplicationsign', 'none');

        $el = stack_input_factory::make('radio', 'ans1', '[[1+2,true],[2*x,false]]', $options, array());
        $el->adapt_to_model_answer('[[1+2,true],[2*x,false]]');
        // @codingStandardsIgnoreStart
        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" /><label for="stack1__ans1_">(No answer given)</label></div><div class="option">' .
                '<br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+2\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(2\,x\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_radio_plots() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('radio', 'ans1',
                '[[1,true,plot(x,[x,-2,2],[y,-3,3])],[2,false,plot(x^2,[x,-2,2],[y,-3,3])],'
                . '[3,false,plot(x^3,[x,-2,2],[y,-3,3])]]',
                null, array());
        // @codingStandardsIgnoreEnd
        $render = $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null);
        $this->assertTrue(is_int(strpos($render, "<img src='https://www.example.com/moodle/question/type/stack/plot.php")));
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
        $el = stack_input_factory::make('radio', 'ans1', $ta, null, array());
        $el->adapt_to_model_answer($ta);

        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" value="" ' .
                'id="stack1__ans1_" checked="checked" /><label for="stack1__ans1_">n/a</label></div>' .
                '<div class="option"><br /></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(A\)</span></span></label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(B\)</span></span></label></div></div>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::BLANK, array(''), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1' => ''), $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals(array(), $state->contents);
        $this->assertEquals('', $state->contentsmodified);
        $correctresponse = array('ans1' => 2);
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));
    }

    public function test_teacher_answer_protect_string_html() {
        $options = new stack_options();
        $ta = '[[notanswered,false,"n/a"],["{",true],["[",false],["(",false]]';
        $el = stack_input_factory::make('radio', 'ans1', $ta, null, array());
        $el->adapt_to_model_answer($ta);

        $correctresponse = array('ans1' => 1);
        $this->assertEquals($correctresponse, $el->get_correct_response($ta));
        $expected = 'A correct answer is: <ul><li>{</li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));

        $expected = '<div class="answer"><div class="option"><input type="radio" name="stack1__ans1" ' .
                'value="" id="stack1__ans1_" /><label for="stack1__ans1_">n/a</label></div><div class="option">' .
                '<br /></div><div class="option"><input type="radio" name="stack1__ans1" value="1" id="stack1__ans1_1" ' .
                'checked="checked" /><label for="stack1__ans1_1">{</label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2">[</label></div><div class="option">' .
                '<input type="radio" name="stack1__ans1" value="3" id="stack1__ans1_3" /><label for="stack1__ans1_3">(' .
                '</label></div></div>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('1'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1' => '1'), $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('"{"', $state->contentsmodified);
        // The response below is a complete edge case: mismatching curly brackets inside a string!
        // To fix this we need to dig into how Maxima creates LaTeX, and protects strings.
        // The place to fix this is in Maxima: tex("{"); gives the wrong result.
        // This applys to any strings in STACK/Maxima, so this isn't the place to record this failing unit test.
        // But, I'll ask the Maxima people to fix it and see when this unit test "breaks" to correct behaviour!
        $this->assertEquals('\[ \mbox{{} \]', $state->contentsdisplayed);
    }
}
