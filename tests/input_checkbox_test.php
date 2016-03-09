<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

/**
 * Unit tests for stack_checkbox_input.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        $el = stack_input_factory::make('checkbox', 'ans1', $this->make_ta(), $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_checkbox() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1+x,true],[2+y,false]]', array());
        // @codingStandardsIgnoreEnd
        $expected = '<div class="answer"><div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
                . '<label><code>1+x</code></label></div><div>'
                . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label>'
                . '<code>2+y</code></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array(''), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_bad_teacheranswer() {
        $el = $this->make_checkbox();
        $el->adapt_to_model_answer('[x]');
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                . ' Please contact your teacher.</p><p>The model answer field for this input is malformed: <code>[x]</code>. '
                . '</p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_duplicate_values() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1,true],[2,false]]', array());
        $el->adapt_to_model_answer('[[1,true],[1,false]]');
        // @codingStandardsIgnoreEnd
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>Duplicate values have been found when generating the input options. </p>'
                .'</div>';
         $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_duplicate_values_ok() {
        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1,true],[2,false]]', array());
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        // @codingStandardsIgnoreStart
        $expected = '<div class="answer"><div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
            . '<label><code>1</code></label></div><div>'
            . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" checked="checked" /><label>'
            . '<code>1</code></label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_not_answered() {
        $el = $this->make_checkbox();
        $expected = '<div class="answer"><div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
            . '<label><code>x+1</code></label></div><div>'
            . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label><code>x+2</code></label></div>'
            . '<div><input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" /><label><code>sin(pi*n)</code>'
            . '</label></div></div>';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_x_plus_1() {
        $el = $this->make_checkbox();
        $expected = '<div class="answer">'
            . '<div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" checked="checked" />'
            . '<label><code>x+1</code></label></div><div>'
            . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" /><label><code>x+2</code></label></div>'
            . '<div><input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" />'
            . '<label><code>sin(pi*n)</code></label></div></div>';
        $this->assertEquals($expected,
                $el->render(new stack_input_state(
                        stack_input::SCORE, array('1'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_latex() {
        $el = $this->make_checkbox(array('options' => 'LaTeX'));
        $expected = '<div class="answer"><div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
            . '<label>\(x+1\)</label></div><div><input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />'
            . '<label>\(x+2\)</label></div><div>'
            . '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />'
            . '<label>\(\sin \left( \pi\cdot n \right)\)</label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_latexdisplay() {
        $el = $this->make_checkbox(array('options' => 'LaTeXdisplay'));
        $expected = '<div class="answer"><div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
            . '<label>\[x+1\]</label></div><div><input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />'
            . '<label>\[x+2\]</label></div><div>'
            . '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />'
            . '<label>\[\sin \left( \pi\cdot n \right)\]</label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_checkbox();
        $state = $el->validate_student_response(array('ans1_' => ''), $options, 'x+1', null);
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_checkbox();
        $state = $el->validate_student_response(array('ans1_1' => '1'), $options, '1', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('1'), $state->contents);
        $this->assertEquals('[x+1]', $state->contentsmodified);
    }

    public function test_validate_student_response_x_plus_1_2() {
        $options = new stack_options();
        $el = $this->make_checkbox();
        $state = $el->validate_student_response(array('ans1_1' => '1', 'ans1_2' => '2'), $options, '2', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('1', '2'), $state->contents);
        $this->assertEquals('[x+1,x+2]', $state->contentsmodified);
    }

    public function test_string_value() {
        $options = new stack_options();
        $el = stack_input_factory::make('checkbox', 'ans1', '[[1+x,true],[2+x^2,false],[{},false,"None of these"]]', array());
        $el->adapt_to_model_answer('[[1+x,true],[2+x^2,false],[{},false,"None of these"]]');
        $expected = '<div class="answer"><div><input type="checkbox" name="stack1__ans1_1" value="1" id="stack1__ans1_1" />'
                . '<label><code>1+x</code></label></div><div>'
                . '<input type="checkbox" name="stack1__ans1_2" value="2" id="stack1__ans1_2" />'
                . '<label><code>2+x^2</code></label></div><div>'
                . '<input type="checkbox" name="stack1__ans1_3" value="3" id="stack1__ans1_3" checked="checked" />'
                . '<label>None of these</label></div></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false));
        $state = $el->validate_student_response(array('ans1_3' => '3'), $options, '2', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('3'), $state->contents);
        $this->assertEquals('[{}]', $state->contentsmodified);
    }
}
