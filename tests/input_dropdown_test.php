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

/**
 * Unit tests for stack_dropdown_input.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_dropdown_input
 */
final class input_dropdown_test extends qtype_stack_walkthrough_test_base {
    /**
     * Extra class to add to expected values.
     * @var string
     */
    private static $moodleclass = '';
    /**
     * Set expected value.
     * @return void
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        parent::setUpBeforeClass();
        self::$moodleclass = ($CFG->version >= 2025022100) ? ' form-select' : '';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    protected function expected_choices() {

        return [
            '' => stack_string('notanswered'),
            '1' => 'x+1',
            '2' => 'x+2',
            '3' => 'sin(pi*n)',
        ];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    protected function expected_choices_latex() {

        return [
            '' => stack_string('notanswered'),
            '1' => 'x+1',
            '2' => 'x+2',
            '3' => 'sin(\pi*n)',
        ];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    protected function make_dropdown($parameters = []) {
        $el = stack_input_factory::make('dropdown', 'ans1', $this->make_ta(), null, $parameters);
        return $el;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    protected function make_ta() {

        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_simple_dropdown(): void {

        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,true],[2+y,false]]', null,   );
        // @codingStandardsIgnoreEnd
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" '
                . 'class="select' . self::$moodleclass . ' menustack1__ans1" name="stack1__ans1">'
                . '<option value="">(Clear my choice)</option><option value="1"><code>1+x</code></option>'
                . '<option selected="selected" value="2"><code>2+y</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_no_correct_answer(): void {

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

    public function test_bad_teacheranswer(): void {

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

    public function test_duplicate_values(): void {

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

    public function test_duplicate_values_ok(): void {

        // @codingStandardsIgnoreStart
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1,true],[2,false]]', null, []);
        $el->adapt_to_model_answer('[[1,true],[2,false,1]]');
        // @codingStandardsIgnoreEnd
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" '
                . 'class="select' . self::$moodleclass . ' menustack1__ans1" name="stack1__ans1">'
                . '<option value="">(Clear my choice)</option><option value="1"><code>1</code></option>'
                . '<option selected="selected" value="2"><code>1</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['2'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_not_answered(): void {

        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, [], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_x_plus_1(): void {

        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+1'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, ['x+1'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_string(): void {

        $el = $this->make_dropdown();
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select'
            . self::$moodleclass . ' menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option>'
            . '<option value="1"><code>x+1</code></option><option value="2"><code>x+2</code></option>'
            . '<option selected="selected" value="3"><code>sin(pi*n)</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_nonotanswered(): void {

        $el = $this->make_dropdown(['options' => 'nonotanswered']);
        $el->adapt_to_model_answer($this->make_ta());
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select'
            . self::$moodleclass . ' menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="1"><code>x+1</code></option><option value="2"><code>x+2</code></option>'
            . '<option selected="selected" value="3"><code>sin(pi*n)</code></option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_latex(): void {

        $el = $this->make_dropdown(['options' => 'LaTeX']);
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select'
            . self::$moodleclass . ' menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option><option value="1">\(x+1\)</option>'
            . '<option value="2">\(x+2\)</option>'
            . '<option selected="selected" value="3">\(\sin \left( \pi\cdot n \right)\)</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_latexdisplay(): void {

        $el = $this->make_dropdown(['options' => 'LaTeXdisplay']);
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select'
            . self::$moodleclass . ' menustack1__ans1" '
            . 'name="stack1__ans1">'
            . '<option value="">(Clear my choice)</option><option value="1">\[x+1\]</option>'
            . '<option value="2">\[x+2\]</option>'
            . '<option selected="selected" value="3">\[\sin \left( \pi\cdot n \right)\]</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
                        stack_input::SCORE, ['3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_x_plus_2(): void {

        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), 'x+2'),
                $el->render(new stack_input_state(
                        stack_input::SCORE, ['x+3'], '', '', '', '', ''), 'stack1__ans1', false, null));
    }

    public function test_render_disabled(): void {

        $el = $this->make_dropdown();
        $this->assert(new \question_contains_select_expectation(
                        'stack1__ans1', $this->expected_choices(), ''),
                $el->render(new stack_input_state(
                        stack_input::BLANK, [], '', '', '', '', ''), 'stack1__ans1', true, null));
    }

    public function test_validate_student_response_blank(): void {

        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(['ans1' => ''], $options, 'x+1', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
    }

    public function test_validate_student_response_x_plus_1(): void {

        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(['ans1' => '1'], $options, '1', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_x_plus_2(): void {

        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(['ans1' => '2'], $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_string_value(): void {

        $options = new stack_options();
        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,true],[2+x^2,false],[{},false,"None of these"]]');
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select'
            . self::$moodleclass . ' menustack1__ans1" '
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

    public function test_teacher_answer(): void {

        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,true],[{},false,"None of these"]]');

        $correctresponse = ['ans1' => 2];
        $this->assertEquals($correctresponse,
                $el->get_correct_response('[[1+x,false],[2+x^2,true],[{},false,"None of these"]]'));

        $correctresponse = 'A correct answer is: <code>2+x^2</code>';
        $this->assertEquals($correctresponse,
                $el->get_teacher_answer_display(null, null));
    }

    public function test_teacher_answer_display(): void {

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

    public function test_teacher_answer_display_hideanswer(): void {

        $el = stack_input_factory::make('dropdown', 'ans1', '[[1+x,false],[2+x^2,false],[{},true,"None of these"]]', null, []);
        $el->adapt_to_model_answer('[[1+x,false],[2+x^2,false],[{},true,"None of these"]]');
        $el->set_parameter('options', 'hideanswer');

        $correctresponse = '';
        $this->assertEquals($correctresponse,
            $el->get_teacher_answer_display(null, null));
    }

    public function test_teacher_answer_html_entities(): void {

        $options = new stack_options();
        $ta = '[[A,false,"n/a"],[B,true,"&ge;"],[C,false,"&le;"],[D,false,"="],[E,false,"?"]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" ' .
                'class="select' . self::$moodleclass . ' menustack1__ans1" name="stack1__ans1">' .
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

    public function test_teacher_answer_html_notanswered(): void {

        $options = new stack_options();
        $ta = '[[notanswered,false,"n/a"],[A,false],[B,true]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select'.
            self::$moodleclass . ' menustack1__ans1" ' .
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

    public function test_union(): void {

        $options = new stack_options();
        $ta = '[[%union(oo(-inf,0),oo(0,inf)),true],[%union({1},{2}),false],[union({1},{4}),false],' .
            '[A,false,%union({1},{3})]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, null, []);
        $el->adapt_to_model_answer($ta);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" ' .
            'class="select' . self::$moodleclass . ' menustack1__ans1" name="stack1__ans1">' .
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

    public function test_decimals(): void {

        $options = new stack_options();
        $options->set_option('decimals', ',');
        $ta = '[[3.1415,true],[[a,b,c,2.78],false],[2.78,false,"Euler constant"]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, $options, ['options' => '']);

        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select' . self::$moodleclass .
            ' menustack1__ans1" name="stack1__ans1"><option selected="selected" value="">(Clear my choice)' .
            '</option><option value="1"><code>3,1415</code></option><option value="2">' .
            '<code>[a;b;c;2,78]</code></option><option value="3">Euler constant</option></select>';
        $this->assert_same_select_html($expected, $el->render(new stack_input_state(
            stack_input::BLANK, [''], '', '', '', '', ''), 'stack1__ans1', false, null));
        $expected = '<select data-stack-input-type="dropdown" id="menustack1__ans1" class="select' . self::$moodleclass .
            ' menustack1__ans1" name="stack1__ans1"><option value="">(Clear my choice)' .
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

    public function test_validate_student_response_castext(): void {
        $options = new stack_options();
        $ta = '[[1+x^2,false],[1-x^2,false],' .
            '["1+x^3",true,["%root","Hello world ",["smlt","\\({x^3}\\)"]]]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, $options, ['options' => '']);
        $el->adapt_to_model_answer($ta);
        $state = $el->validate_student_response(['ans1' => '2'], $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);

        $ta = '[[1+x^2,true],[1-x^2,false],' .
            '["1+x^3",false,["%root","Hello world ",["smlt","\\({x^3}\\)"]]]]';
        $el = stack_input_factory::make('dropdown', 'ans1', $ta, $options, ['options' => '']);
        $el->adapt_to_model_answer($ta);
        $state = $el->validate_student_response(['ans1' => '2'], $options, '2', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_with_allowempty(): void {
        $options = new stack_options();
        $ta = '[[A,false],[B,true],[C,false]]';
        $el = stack_input_factory::make('dropdown', 'sans1', $ta, $options, ['options' => '']);
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => ''], $options, '2', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('A correct answer is: <code>B</code>',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_student_response_with_allowempty_nocorrect(): void {
        $options = new stack_options();
        // Normally teachers must have one correct answer.
        $ta = '[[A,false],[B,false],[C,false]]';
        $el = stack_input_factory::make('dropdown', 'sans1', $ta, $options, ['options' => '']);
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => ''], $options, '2', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('A correct answer is: This input can be left blank.',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }
}
