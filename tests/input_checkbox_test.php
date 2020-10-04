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

// Unit tests for stack_checkbox_input.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_checkbox_input_test extends qtype_stack_walkthrough_test_base {

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

    protected function make_checkbox($parameters = array()) {
        $el = stack_input_factory::make('checkbox', 'ans1', $this->make_ta(), null, $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_checkbox() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1+x,true],[2+y,false]]', null, array());
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+x\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(2+y\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array(''), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+x\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_simple_casstring_checkbox() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1+x,true],[2+y,false]]',
                null, array('options' => 'casstring'));
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer">'
                . '<div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
                . '<label for="stack1__ans1_1"><code>1+x</code></label></div><div class="option">'
                . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label for="stack1__ans1_2">'
                . '<code>2+y</code></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array(''), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_bad_teacheranswer() {
        $el = $this->make_checkbox();
        $el->adapt_to_model_answer('[x]');
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                . ' Please contact your teacher.</p><p>The model answer field for this input is malformed: <code>[x]</code>.'
                . '</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_duplicate_values() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1,true],[2,false]]', null, array());
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
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1,true],[2,false]]', null, array());
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        // @codingStandardsIgnoreStart
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_not_answered() {
        $el = $this->make_checkbox();
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_x_plus_1() {
        $el = $this->make_checkbox(array('options' => 'casstring'));
        $expected = '<div class="answer">'
            . '<div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" checked="checked" />'
            . '<label for="stack1__ans1_1"><code>x+1</code></label></div><div class="option">'
            . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label for="stack1__ans1_2"><code>x+2</code></label></div>'
            . '<div class="option"><input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />'
            . '<label for="stack1__ans1_3"><code>sin(pi*n)</code></label></div></div>';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(
                        stack_input::SCORE, array('1'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_latex() {
        $el = $this->make_checkbox(array('options' => 'LaTeX'));
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+2\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\sin \left( \pi\cdot n \right)\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x+1\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latexdisplay() {
        $el = $this->make_checkbox(array('options' => 'LaTeXdisplay'));
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+1\]</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+2\]</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[\sin \left( \pi\cdot n \right)\]</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[x+1\]</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_render_latexdisplaystyle() {
        $el = $this->make_checkbox(array('options' => 'LaTeXdisplaystyle'));
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle x+1\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\(\displaystyle x+2\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\displaystyle \sin \left( \pi\cdot n \right)\)</span></span></label>' .
                '</div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_checkbox();
        $state = $el->validate_student_response(array('ans1_' => ''), $options, 'x+1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_checkbox();
        $state = $el->validate_student_response(array('ans1_1' => '1'), $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('1'), $state->contents);
        $this->assertEquals('[x+1]', $state->contentsmodified);
    }

    public function test_validate_student_response_x_plus_1_2() {
        $options = new stack_options();
        $el = $this->make_checkbox();
        $state = $el->validate_student_response(array('ans1_1' => '1', 'ans1_2' => '2'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('1', '2'), $state->contents);
        $this->assertEquals('[x+1,x+2]', $state->contentsmodified);
    }

    public function test_casstring_value() {
        $options = new stack_options();
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1+x,true],[2+x^2,false],[{},false,"None of these"]]',
                null, array('options' => 'casstring'));
        $el->adapt_to_model_answer('[[1+x,true],[2+x^2,false],[{},false,"None of these"]]');
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
                . '<label for="stack1__ans1_1"><code>1+x</code></label></div><div class="option">'
                . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />'
                . '<label for="stack1__ans1_2"><code>2+x^2</code></label></div><div class="option">'
                . '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />'
                . '<label for="stack1__ans1_3">None of these</label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1_3' => '3'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('3'), $state->contents);
        $this->assertEquals('[{}]', $state->contentsmodified);
        $expected = 'A correct answer is: <code>[1+x]</code>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_logic_casstring() {
        $options = new stack_options();
        $el = stack_input_factory::make('checkbox', 'ans1', '[[x=1 nounor x=2,true],[x=1 nounand x=2,false],[x=1 nounor x=3,false]]',
                null, array('options' => 'casstring'));
        $el->adapt_to_model_answer('[[x=1 nounor x=2,true],[x=1 nounand x=2,false],[x=1 nounor x=3,false]]');
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'.
                    '<label for="stack1__ans1_1"><code>x = 1 or x = 2</code></label></div><div class="option">'.
                    '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label for="stack1__ans1_2"><code>x = 1 and x = 2</code>'.
                    '</label></div><div class="option"><input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />'.
                    '<label for="stack1__ans1_3"><code>x = 1 or x = 3</code></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1_3' => '3'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('3'), $state->contents);
        $this->assertEquals('[x = 1 nounor x = 3]', $state->contentsmodified);
        $expected = 'A correct answer is: <code>[x = 1 or x = 2]</code>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_logic_latex() {
        $options = new stack_options();
        $el = stack_input_factory::make('checkbox', 'ans1', '[[x=1 nounor x=2,true],[x=1 nounand x=2,false],[x=1 nounor x=3,false]]',
                null, array('options' => 'latex'));
        $el->adapt_to_model_answer('[[x=1 nounor x=2,true],[x=1 nounand x=2,false],[x=1 nounor x=3,false]]');
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x=1\,{\mbox{ or }}\, x=2\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label for="stack1__ans1_2">' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(x=1\,{\mbox{ and }}\, x=2\)' .
                '</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\(x=1\,{\mbox{ or }}\, x=3\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1_3' => '3'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('3'), $state->contents);
        $this->assertEquals('[x = 1 nounor x = 3]', $state->contentsmodified);
    }

    public function test_simp_false() {        

        $options = new stack_options();
        $el = stack_input_factory::make('checkbox', 'ans1', '[[abs(x-5)=abs(5-x),true],[1+1,false],[x=3 nounor x=1,false]]',
                null, array('options' => 'latex'));
        $el->adapt_to_model_answer('[[abs(x-5)=abs(5-x),true],[1+1,false],[x=3 nounor x=1,false]]');
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\left| x-5\right| =\left| 5-x\right| \)</span></span></label></div>' .
                '<div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(1+1\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(x=3\,{\mbox{ or }}\, x=1\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1_1' => '2'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('2'), $state->contents);
        $this->assertEquals('[1+1]', $state->contentsmodified);
    }

    public function test_stack_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('checkbox', 'ans1', '[[stackunits(9.81,m/s^2),true],[stackunits(9.81,m/s),false],[stackunits(9.8,m/s^2),false]]',
                null, array('options' => 'latex'));
        $el->adapt_to_model_answer('[[stackunits(9.81,m/s^2),true],[stackunits(9.81,m/s),false],[stackunits(9.8,m/s^2),false]]');
        $expected = '<div class="answer"><div class="option"><input type="checkbox" name="stack1__ans1_1" value="1" ' .
                'id="stack1__ans1_1" /><label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(9.81\, \frac{m}{s^2}\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\(9.81\, \frac{m}{s}\)</span></span></label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(9.8\, \frac{m}{s^2}\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('ans1_1' => '2'), $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('2'), $state->contents);
        $this->assertEquals('[stackunits(9.81,m/s)]', $state->contentsmodified);
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(9.81\, \frac{m}{s^2}\)</span></span></li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_show_teacher_answer() {
        $options = new stack_options();
        $ta = '[[A, true, "Integration by parts"],[B, true, "Integration by substitution"],' .
                '[C, true, "Apply a trig formula to remove product"],' .
                '[D, true, "Remove trig with complex exponentials, then integrate"],' .
                '[X, false, "None of the other options"]]';
        $el = stack_input_factory::make('checkbox', 'ans1', $ta, null, array('options' => ''));
        $expected = '<div class="answer"><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1">Integration by parts</label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" checked="checked" />' .
                '<label for="stack1__ans1_2">Integration by substitution</label></div><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3">Apply a trig formula to remove product</label></div>' .
                '<div class="option"><input type="checkbox" name="stack1__ans1_4" value="4" id="stack1__ans1_4" />' .
                '<label for="stack1__ans1_4">Remove trig with complex exponentials, then integrate</label></div>' .
                '<div class="option"><input type="checkbox" name="stack1__ans1_5" value="5" id="stack1__ans1_5" />' .
                '<label for="stack1__ans1_5">None of the other options</label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, array()));
        $state = $el->validate_student_response(array('ans1_2' => '2', 'ans1_3' => '3'),
                $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('2', '3'), $state->contents);
        $this->assertEquals('[B,C]', $state->contentsmodified);

        $this->assertEquals($ta, $el->get_teacher_answer());
        $el->adapt_to_model_answer($ta);
        $expected = 'A correct answer is: <ul><li>Integration by parts</li><li>Integration by substitution</li>' .
                '<li>Apply a trig formula to remove product</li>' .
                '<li>Remove trig with complex exponentials, then integrate</li></ul>';
        // This input type ignores the inputs to the function.
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }

    public function test_stack_disp() {
        $options = new stack_options();
        $ta = '[[oc(-inf,a),true,stack_disp(oc(-inf,a),"i")],[cc(-inf,a),false],[oo(-inf,a),false]]';
        $el = stack_input_factory::make('checkbox', 'ans1', $ta, null, array('options' => ''));
        $expected = '<div class="answer"><div class="option">' .
                '<input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />' .
                '<label for="stack1__ans1_1"><span class="filter_mathjaxloader_equation">' .
                '\(<span class="nolink">\(\left( -\infty ,\, a\right]\)</span></span>\)</label></div>' .
                '<div class="option"><input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" ' .
                'checked="checked" /><label for="stack1__ans1_2"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\left[ -\infty ,\, a\right]\)</span></span></label></div>' .
                '<div class="option"><input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />' .
                '<label for="stack1__ans1_3"><span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(\left( -\infty ,\, a\right)\)</span></span></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false, array()));
        $state = $el->validate_student_response(array('ans1_2' => '2', 'ans1_3' => '3'),
                $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('2', '3'), $state->contents);
        $this->assertEquals('[cc(-inf,a),oo(-inf,a)]', $state->contentsmodified);

        $this->assertEquals($ta, $el->get_teacher_answer());
        $el->adapt_to_model_answer($ta);
        $expected = 'A correct answer is: <ul><li><span class="filter_mathjaxloader_equation">\(<span class="nolink">' .
                '\(\left( -\infty ,\, a\right]\)</span></span>\)</li></ul>';
        $this->assertEquals($expected, $el->get_teacher_answer_display(false, false));
    }
}
