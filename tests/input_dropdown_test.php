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
 * Unit tests for stack_dropdown_input.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_dropdown_input_test extends qtype_stack_walkthrough_test_base {
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

    protected function make_dropdown($parameters = array()) {
        $el = stack_input_factory::make('dropdown', 'ans1', $this->make_ta(), $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_dropdown() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,true],[2+y,false]]', array());
        $expected = '<select id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                .'<option value="">Not answered</option><option value="1"><code>1+x</code></option>'
                .'<option selected="selected" value="2"><code>2+y</code></option></select>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_no_correct_answer() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,false],[2,false]]', array());
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>The teacher did not indicate at least one correct answer. </p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_bad_teacheranswer() {
        $el = $this->make_dropdown();
        $el->adapt_to_model_answer('[x]');
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>The model answer field for this input is malformed: <code>[x]</code>.'
                .' The teacher did not indicate at least one correct answer. </p></div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_duplicate_values() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,true],[2,false]]', array());
        $el->adapt_to_model_answer('[[1,true],[1,false]]');
        $expected = '<div class="error"><p>The input has generated the following runtime error which prevents you from answering.'
                .' Please contact your teacher.</p><p>Duplicate values have been found when generating the input options. </p>'
                .'</div>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_duplicate_values_ok() {
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,true],[2,false]]', array());
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        $expected = '<select id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                . '<option value="">Not answered</option><option value="1"><code>1</code></option>'
                . '<option selected="selected" value="2"><code>1</code></option></select>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_not_answered() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_x_plus_1() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+1'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, array('x+1'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_string() {
        $el = $this->make_dropdown();
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<select id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                .'<option value="">Not answered</option>'
                .'<option value="1"><code>x+1</code></option><option value="2"><code>x+2</code></option>'
                .'<option selected="selected" value="3"><code>sin(pi*n)</code></option></select>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_latex() {
        $el = $this->make_dropdown(array('options' => 'LaTeX'));
        $expected = '<select id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                .'<option value="">Not answered</option><option value="1">\(x+1\)</option>'
                .'<option value="2">\(x+2\)</option>'
                .'<option selected="selected" value="3">\(\sin \left( \pi\cdot n \right)\)</option></select>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_latexdisplay() {
        $el = $this->make_dropdown(array('options' => 'LaTeXdisplay'));
        $expected = '<select id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                .'<option value="">Not answered</option><option value="1">\[x+1\]</option>'
                .'<option value="2">\[x+2\]</option>'
                .'<option selected="selected" value="3">\[\sin \left( \pi\cdot n \right)\]</option></select>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, array('3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_x_plus_2() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+2'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, array('x+3'), '', '', '', '', ''), 'stack1__ans1', false));
    }

    public function test_render_disabled() {
        $el = $this->make_dropdown();
        $this->assert(new question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, array(), '', '', '', '', ''), 'stack1__ans1', true));
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => ''), $options, 'x+1', null);
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => '1'), $options, '1', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_x_plus_2() {
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => '2'), $options, '2', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_string_value() {
        $options = new stack_options();
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', array());
        $el->adapt_to_model_answer('[[1+x,true],[2+x^2,false],[{},false,"None of these"]]');
        $expected = '<select id="menustack1__ans1" class="select menustack1__ans1" name="stack1__ans1">'
                . '<option value="">Not answered</option><option value="1"><code>1+x</code></option>'
                . '<option selected="selected" value="2"><code>2+x^2</code></option>'
                . '<option value="3">None of these</option></select>';
        $this->assertEquals($expected, $el->render(new stack_input_state(
                stack_input::SCORE, array('2'), '', '', '', '', ''), 'stack1__ans1', false));
        $state = $el->validate_student_response(array('ans1' => '3'), $options, '2', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals(array('3'), $state->contents);
        $this->assertEquals('{}', $state->contentsmodified);
    }

    public function test_teacher_answer() {
        $options = new stack_options();
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', array());
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,true],[{},false,"None of these"]]');

        $correctresponse = array('ans1' => 2);
        $this->assertEquals($correctresponse,
                $el->get_correct_response('[[1+x,false],[2+x^2,true],[{},false,"None of these"]]'));

        $correctresponse = 'A correct answer is <code>2+x^2</code> .';
        $this->assertEquals($correctresponse,
                $el->get_teacher_answer_display(null, null));
    }

    public function test_teacher_answer_display() {
        $options = new stack_options();
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', array());
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]');

        $correctresponse = array('ans1' => 3);
        $this->assertEquals($correctresponse,
                $el->get_correct_response('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]'));

        $correctresponse = 'A correct answer is <code>"None of these"</code> .';
        $this->assertEquals($correctresponse,
                $el->get_teacher_answer_display(null, null));
    }
}
