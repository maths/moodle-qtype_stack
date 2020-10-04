<?php
// This file is part of STACK - https://stack.maths.ed.ac.uk
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

/**
 * Unit tests for the stack_algebra_input class.
 *
 * @copyright  2015 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

/**
 * Unit tests for stack_equiv_input.
 *
 * @copyright  2015 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_equiv_input_test extends qtype_stack_testcase {

    public function test_internal_validate_parameter() {
        $el = stack_input_factory::make('equiv', 'input', 'x^2');
        $this->assertTrue($el->validate_parameter('boxWidth', 30));
        $this->assertFalse($el->validate_parameter('boxWidth', -10));
        $this->assertFalse($el->validate_parameter('boxWidth', "30"));
        $this->assertFalse($el->validate_parameter('boxWidth', ''));
        $this->assertFalse($el->validate_parameter('boxWidth', null));
        $this->assertTrue($el->validate_parameter('showValidation', 1));
        $this->assertFalse($el->validate_parameter('showValidation', true));
        $this->assertFalse($el->validate_parameter('showValidation', 5));
    }

    public function test_render_blank() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        $this->assertEquals('<textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="25" ' .
                'autocapitalize="none" spellcheck="false"></textarea>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_syntaxhint() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        $el->set_parameter('syntaxHint',
            '[r1=0,r2=0,r3=0,r4=0,r5=0,r6=0,t*h*i*s+i*s+a+v*e*r*y+l*o*n*g+e*x*p*r*e*s*s*i*o*n=g*o*o*d+t*e*s*t!]');
        $this->assertEquals("<textarea name=\"stack1__ans1\" id=\"stack1__ans1\" " .
            "rows=\"8\" cols=\"50\" autocapitalize=\"none\" spellcheck=\"false\">" .
            "r1 = 0\nr2 = 0\nr3 = 0\nr4 = 0\nr5 = 0\nr6 = 0\n" .
            "t*h*i*s+i*s+a+v*e*r*y+l*o*n*g+e*x*p*r*e*s*s*i*o*n = g*o*o*d+t*e*s*t!" .
            "</textarea>",
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                    'stack1__ans1', false, null));
    }

    public function test_render_firstline() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        $el->set_parameter('syntaxHint', 'firstline');
        $this->assertEquals('<textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="25" ' .
                'autocapitalize="none" spellcheck="false">x^2 = 4</textarea>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, '[x^2=4,x=2 or x=-2]'));
    }

    public function test_render_hint() {
        $el = stack_input_factory::make('equiv', 'ans1', '[]');
        // Note the syntax hint must be a list.
        $el->set_parameter('syntaxHint', '[x^2=3]');
        $this->assertEquals('<textarea name="stack1__ans1" id="stack1__ans1" rows="3" cols="25" ' .
                'autocapitalize="none" spellcheck="false">x^2 = 3</textarea>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, '[x^2=4,x=2 or x=-2]'));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-2*x+1=0]');
        $state = $el->validate_student_response(array('sans1' => 'x^2-2*x+1=0'), $options, '[x^2-2*x+1=0]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $excont = array(0 => 'x^2-2*x+1=0');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[x^2-2*x+1 = 0]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2-2\cdot x+1=0& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('<span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \begin{array}{lll} &x^2-2\cdot x+1=0& \cr \end{array} \]</span></span>' .
                '<input type="hidden" name="q140:1_ans1_val" value="[x^2-2*x+1=0]" />The variables found in your ' .
                'answer were: <span class="filter_mathjaxloader_equation"><span class="nolink">\( \left[ x \right]\)' .
                '</span></span> ',
                $el->render_validation($state, 'q140:1_ans1'));
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\nx=2 or x=3"), $options, '[x^2-5*x+6=0]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\n x={2,3}"), $options, '[x^2-5*x+6=0]',
                new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Sets are not allowed when reasoning by equivalence.', $state->errors);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_invalid_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6,stackeq((x-2)*(x-3))]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6\n =(x-2)(x-3)"), $options,
                '[x^2-5*x+6,stackeq((x-2)*(x-3))]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You seem to be missing * characters. Perhaps you meant to type '.
                '<span class="stacksyntaxexample">=(x-2)<span class="stacksyntaxexamplehighlight">*</span>(x-3)</span>.',
                $state->errors);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_invalid_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6,stackeq((x-2)*(x-3))]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\n(x-2)(x-3)=0"), $options,
                '[x^2-5*x+6,stackeq((x-2)*(x-3))]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You seem to be missing * characters. Perhaps you meant to type '.
                '<span class="stacksyntaxexample">(x-2)<span class="stacksyntaxexamplehighlight">*</span>(x-3) = 0</span>.',
                $state->errors);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_invalid_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6,stackeq((x-2)*(x-3))]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6\n =(x-2)*x^"), $options,
                '[x^2-5*x+6,stackeq((x-2)*(x-3))]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('\'^\' is an invalid final character in <span class="stacksyntaxexample">=(x-2)*x^</span>',
                $state->errors);
        $this->assertEquals('finalChar', $state->note);
    }

    public function test_validate_student_response_invalid_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0,(x-2)*(x-3)=0]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\n x=2 or x=3)"), $options,
                '[x^2-5*x+6=0\n x=2 or x=3)]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the ' .
                'expression: <span class="stacksyntaxexample">x=2 or x=3)</span>.', $state->errors);
        $this->assertEquals('\(\displaystyle x^2-5\cdot x+6=0 \)<br/>' .
                '<span class="stacksyntaxexample">x=2 or x=3)</span> ' .
                'You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">x=2 or x=3)</span>.<br/>',
                $state->contentsdisplayed);
        $this->assertEquals('missingLeftBracket', $state->note);
    }

    public function test_validate_student_response_invalid_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0,(x-2)*(x-3)=0]');
        $el->set_parameter('showValidation', 3);
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\n x=2 or x=3)"), $options,
                '[x^2-5*x+6=0\n x=2 or x=3)]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the ' .
                'expression: <span class="stacksyntaxexample">x=2 or x=3)</span>.', $state->errors);
        $this->assertEquals('\(\displaystyle x^2-5\cdot x+6=0 \)<br/>' .
                '<span class="stacksyntaxexample">x=2 or x=3)</span> ' .
                'You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">x=2 or x=3)</span>.<br/>',
                $state->contentsdisplayed);
        $this->assertEquals('missingLeftBracket', $state->note);
    }

    public function test_validate_student_response_with_equiv() {
        $options = new stack_options();
        $val = '[x^2-5*x+6=0, x = 2 nounor x = 3]';
        $el = stack_input_factory::make('equiv', 'sans1', $val);
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\nx=2 or x=3"), $options, $val,
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2-5*x+6 = 0,x = 2 nounor x = 3]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2-5\cdot x+6=0& \cr'.
            ' \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=3& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
        $this->assertEquals('<textarea name="q140:1_ans1" id="q140:1_ans1" rows="3" cols="25" ' .
                'autocapitalize="none" spellcheck="false">x^2-5*x+6=0' . "\n" . 'x=2 or x=3</textarea>',
                $el->render($state, 'q140:1_ans1', false, null));
        $this->assertEquals('<span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[ \begin{array}{lll} &x^2-5\cdot x+6=0& \cr' .
                ' \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=3& \cr \end{array} \]</span></span>' .
                '<input type="hidden" name="q140:1_ans1_val" value="[x^2-5*x+6=0,x=2 or x=3]" />' .
                'The variables found in your answer were: <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( \left[ x \right]\)</span></span> ',
                $el->render_validation($state, 'q140:1_ans1'));

        // The test below does not use the LaTeX of the teacher's answer.
        // The test just confirms nounor in $val get converted to something the student should type in.
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( ### \)</span></span>, which can be typed in as follows: <br/>' .
                '<code>x^2-5*x+6 = 0</code><br/><code>x = 2 or x = 3</code>',
                $el->get_teacher_answer_display($val, '###'));
    }

    public function test_validate_student_response_without_equiv() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6=0]');
        $el->set_parameter('options', 'hideequiv');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6=0\nx=2 or x=3"), $options, '[x^2-5*x+6=0]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2-5*x+6 = 0,x = 2 nounor x = 3]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}x^2-5\cdot x+6=0& \cr'.
                ' x=2\,{\mbox{ or }}\, x=3& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_without_domain() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[1/(x-1)+1/(x-2)=0]');
        $el->set_parameter('options', 'hidedomain');
        $state = $el->validate_student_response(array('sans1' => "1/(x-1)+1/(x+1)=0\n2*x/(x^2-1)=0"),
                $options, '[1/(x-1)+1/(x-2)=0]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[1/(x-1)+1/(x+1) = 0,2*x/(x^2-1) = 0]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\frac{1}{x-1}+\frac{1}{x+1}=0& \cr ' .
                '\color{green}{\Leftrightarrow}&\frac{2\cdot x}{x^2-1}=0& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_without_assume_pos() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2=4,x=2 nounor x=-2]');
        $state = $el->validate_student_response(array('sans1' => "x^2=4\nx=2 or x=-2"), $options, '[x^2=4,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = 4,x = 2 nounor x = -2]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2=4& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=-2& \cr'.
                ' \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_without_assume_pos_wrong() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2=4,x=2 nounor x=-2]');
        $state = $el->validate_student_response(array('sans1' => "x^2=4\nx=2"), $options, '[x^2=4,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = 4,x = 2]', $state->contentsmodified);
        // Note this is an implication, not equivalence.
        $this->assertEquals('\[ \begin{array}{lll} &x^2=4& \cr \color{red}{\Leftarrow}&x=2& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_assume_pos() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2=4,x=2 nounor x=-2]');
        $el->set_parameter('options', 'assume_pos');
        $state = $el->validate_student_response(array('sans1' => "x^2=4\nx=2"), $options, '[x^2=4,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = 4,x = 2]', $state->contentsmodified);
        // In this example, we have assumed x is positive so we do have an equivalence. Note the feedback.
        $this->assertEquals('\[ \begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=4& \cr '.
                '\color{green}{\Leftrightarrow}&x=2& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_firstline() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2=4,x=2 nounor x=-2]');
        $el->set_parameter('options', 'firstline');
        $state = $el->validate_student_response(array('sans1' => "x^2=4\nx=2 or x=-2"), $options, '[x^2=4,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2 = 4,x = 2 nounor x = -2]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2=4& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=-2& \cr'.
                ' \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_firstline_false() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2=4,x=2 nounor x=-2]');
        $el->set_parameter('options', 'firstline');
        $state = $el->validate_student_response(array('sans1' => "x^2-4=0\nx=2"), $options, '[x^2=4,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('[x^2-4 = 0,x = 2]', $state->contentsmodified);
        $this->assertEquals('\(\displaystyle x^2-4=0 \) ' .
                'You have used the wrong first line in your argument!<br/>\(\displaystyle x=2 \)<br/>',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_insert_stars_0_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]');
        $el->set_parameter('insertStars', 1);

        $state = $el->validate_student_response(array('sans1' => "(x-1)(x+4)\n=x^2-x+4x-4\n=x^2+3x-4"), $options,
                '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', new stack_cas_security());
        $excont = array(0 => '(x-1)(x+4)', 1 => '=x^2-x+4x-4', 2 => '=x^2+3x-4');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[(x-1)*(x+4),stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\left(x-1\right)\cdot \left(x+4\right)& \cr \color{green}{\checkmark}'.
                '&=x^2-x+4\cdot x-4& \cr \color{green}{\checkmark}&=x^2+3\cdot x-4& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_insert_stars_0_false() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]');
        $el->set_parameter('insertStars', 0);

        $state = $el->validate_student_response(array('sans1' => "(x-1)(x+4)"), $options,
                '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $excont = array(0 => '(x-1)*(x+4)');
        $this->assertEquals('You seem to be missing * characters. Perhaps you meant to type '.
                '<span class="stacksyntaxexample">(x-1)<span class="stacksyntaxexamplehighlight">*</span>(x+4)</span>.',
                $state->errors);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_equational_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]');
        $state = $el->validate_student_response(array('sans1' => "(x-1)*(x+4)\n=x^2-x+4*x-4\n=x^2+3*x-4"), $options,
                '[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', new stack_cas_security());
        $excont = array(0 => '(x-1)*(x+4)', 1 => '=x^2-x+4*x-4', 2 => '=x^2+3*x-4');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[(x-1)*(x+4),stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\left(x-1\right)\cdot \left(x+4\right)& \cr \color{green}{\checkmark}'.
                '&=x^2-x+4\cdot x-4& \cr \color{green}{\checkmark}&=x^2+3\cdot x-4& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_equational_insert_stars_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[a^2-a*b, stackeq(a*(a-b))]');
        $el->set_parameter('insertStars', 2);

        $state = $el->validate_student_response(array('sans1' => "a^2-ab\n=a*(a-b)"), $options,
                '[a^2-a*b,stackeq(a*(a-b))]', new stack_cas_security());
        $excont = array(0 => 'a^2-ab', 1 => '=a*(a-b)');
        $this->assertEquals($excont, $state->contents);
        $this->assertEquals('[a^2-a*b,stackeq(a*(a-b))]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &a^2-a\cdot b& \cr \color{green}{\checkmark}&=a\cdot \left(a-b\right)& \cr'.
                ' \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_without_assume_real() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^4=16,x=2 nounor x=-2]');
        $state = $el->validate_student_response(array('sans1' => "x^4=16\nx=2 or x=-2"), $options, '[x^4=16,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^4 = 16,x = 2 nounor x = -2]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^4=16& \cr \color{red}{\Leftarrow}&x=2\,{\mbox{ or }}\, '.
                'x=-2& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_assume_real() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^4=16,x=2 nounor x=-2]');
        $el->set_parameter('options', 'assume_real');
        $state = $el->validate_student_response(array('sans1' => "x^4=16\nx=2 or x=-2"), $options, '[x^4=16,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^4 = 16,x = 2 nounor x = -2]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\color{blue}{(\mathbb{R})}&x^4=16& \cr \color{green}{\Leftrightarrow}\, '.
                '\color{blue}{(\mathbb{R})}&x=2\,{\mbox{ or }}\, x=-2& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_assume_wrong() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^4=16,x=2 nounor x=-2]');
        $el->set_parameter('options', 'assume_real');
        $state = $el->validate_student_response(array('sans1' => "x^4=16\nx=1 or x=-1"), $options, '[x^4=16,x=2 nounor x=-2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^4 = 16,x = 1 nounor x = -1]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\color{blue}{(\mathbb{R})}&x^4=16& \cr '.
                '\color{red}{?}&x=1\,{\mbox{ or }}\, x=-1& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_assume_real_complex() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^4=16,x=2 nounor x=-2]');
        $el->set_parameter('options', 'assume_real');
        $state = $el->validate_student_response(array('sans1' => "x^4=16\nx=2 or x=-2 or x=2*i or x=-2*i"), $options,
                '[x^4=16,x=2 nounor x=-2]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^4 = 16,x = 2 nounor x = -2 nounor x = 2*i nounor x = -2*i]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\color{blue}{(\mathbb{R})}&x^4=16& \cr '.
                '\color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&x=2\,{\mbox{ or }}\, '.
                'x=-2\,{\mbox{ or }}\, x=2\cdot \mathrm{i}\,{\mbox{ or }}\, x=-2\cdot \mathrm{i}& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_hideequiv() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^4=16,x=2 nounor x=-2]');
        $el->set_parameter('options', 'hideequiv');
        $state = $el->validate_student_response(array('sans1' => "x^4=16\nx=1 or x=-1"), $options,
            '[x^4=16,x=2 nounor x=-2]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^4 = 16,x = 1 nounor x = -1]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}x^4=16& \cr x=1\,{\mbox{ or }}\, x=-1& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_natural_domain_sqrt() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[sqrt(3*x+4) = 2+sqrt(x+2), 3*x+4=4+4*sqrt(x+2)+(x+2),x-1=2*sqrt(x+2),x^2-2*x+1 '.
                '= 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 nounor x=-1]';
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => "sqrt(3*x+4) = 2+sqrt(x+2)\n3*x+4=4+4*sqrt(x+2)+(x+2)\n".
            "x-1=2*sqrt(x+2)\nx^2-2*x+1 = 4*x+8\nx^2-6*x-7 = 0\n(x-7)*(x+1) = 0\nx=7 or x=-1"), $options, $ta,
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[sqrt(3*x+4) = 2+sqrt(x+2),3*x+4 = 4+4*sqrt(x+2)+(x+2),x-1 = 2*sqrt(x+2),'.
                    'x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x = 7 nounor x = -1]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\sqrt{3\,x+4}=2+\sqrt{x+2}&'.
            '{\color{blue}{{x \in {\left[ -\frac{4}{3},\, \infty \right)}}}}\cr \color{red}{\Rightarrow}&3\,x+4=4+4\,'.
            '\sqrt{x+2}+\left(x+2\right)&{\color{blue}{{x \in {\left[ -2,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}'.
            '&x-1=2\,\sqrt{x+2}&{\color{blue}{{x \in {\left[ -2,\, \infty \right)}}}}\cr \color{red}{\Rightarrow}'.
            '&x^2-2\,x+1=4\,x+8& \cr \color{green}{\Leftrightarrow}&x^2-6\,x-7=0& \cr \color{green}{\Leftrightarrow}'.
            '&\left(x-7\right)\,\left(x+1\right)=0& \cr \color{green}{\Leftrightarrow}'.
            '&x=7\,{\mbox{ or }}\, x=-1& \cr \end{array} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_natural_domain_rational() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[5*x/(2*x+1)-3/(x+1) = 1,5*x*(x+1)-3*(2*x+1)=(x+1)*(2*x+1),(x-2)*(3*x+2)=0,x=2 nounor x=-2/3];';
        $sa = "5*x/(2*x+1)-3/(x+1) = 1\n5*x*(x+1)-3*(2*x+1)=(x+1)*(2*x+1)\n(x-2)*(3*x+2)=0\nx=2 or x=-2/3";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[5*x/(2*x+1)-3/(x+1) = 1,5*x*(x+1)-3*(2*x+1) = (x+1)*(2*x+1),(x-2)*(3*x+2) = 0,x = 2 nounor x = -2/3]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\frac{5\,x}{2\,x+1}-\frac{3}{x+1}=1'.
                '&{\color{blue}{{x \not\in {\left \{-1 , -\frac{1}{2} \right \}}}}}\cr \color{green}{\Leftrightarrow}&'.
                '5\,x\,\left(x+1\right)-3\,\left(2\,x+1\right)=\left(x+1\right)\,\left(2\,x+1\right)'.
                '& \cr \color{green}{\Leftrightarrow}&\left(x-2\right)\,\left(3\,x+2\right)=0& \cr \color{green}{\Leftrightarrow}'.
                '&x=2\,{\mbox{ or }}\, x=\frac{-2}{3}& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_natural_domain_logs() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[lg(x+17,3)-2=lg(2*x,3),lg(x+17,3)-lg(2*x,3)=2,lg((x+17)/(2*x),3)=2,(x+17)/(2*x)=3^2,(x+17)=18*x,17*x=17,x=1]';
        $sa = "lg(x+17,3)-2=lg(2*x,3)\nlg(x+17,3)-lg(2*x,3)=2\nlg((x+17)/(2*x),3)=2\n(x+17)/(2*x)=3^2\n(x+17)=18*x\n17*x=17\nx=1";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[lg(x+17,3)-2 = lg(2*x,3),lg(x+17,3)-lg(2*x,3) = 2,lg((x+17)/(2*x),3) = 2,'.
                '(x+17)/(2*x) = 3^2,(x+17) = 18*x,17*x = 17,x = 1]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\log_{3}\left(x+17\right)-2=\log_{3}\left(2\,x\right)&'.
                '{\color{blue}{{x \in {\left( 0,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}'.
                '&\log_{3}\left(x+17\right)-\log_{3}\left(2\,x\right)=2&{\color{blue}{{x \in {\left( 0,\, \infty \right)}}}}'.
                '\cr \color{green}{\Leftrightarrow}&\log_{3}\left(\frac{x+17}{2\,x}\right)=2& \cr \color{green}{\log(?)}'.
                '&\frac{x+17}{2\,x}=3^2&{\color{blue}{{x \not\in {\left \{0 \right \}}}}}\cr '.
                '\color{green}{\Leftrightarrow}&x+17=18\,x'.
                '& \cr \color{green}{\Leftrightarrow}&17\,x=17& \cr \color{green}{\Leftrightarrow}&x=1& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_equational_reasoning() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[x^2+2*a*x,stackeq(x^2+2*a*x+a^2-a^2),stackeq((x+a)^2-a^2)]';
        $sa = "x^2+2*a*x\n= x^2+2*a*x+a^2-a^2\n= (x+a)^2-a^2";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2+2*a*x,stackeq(x^2+2*a*x+a^2-a^2),stackeq((x+a)^2-a^2)]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2+2\,a\,x& \cr \color{green}{\checkmark}&=x^2+2\,a\,x+a^2-a^2& '.
                '\cr \color{green}{\checkmark}&={\left(x+a\right)}^2-a^2& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_equational_nontrivial_difference() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[abs(x-1/2)+abs(x+1/2)-2,stackeq(abs(x)-1)]';
        $sa = "abs(x-1/2)+abs(x+1/2)-2\n= abs(x)-1";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[abs(x-1/2)+abs(x+1/2)-2,stackeq(abs(x)-1)]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\left| x-\frac{1}{2}\right| +\left| x+\frac{1}{2}\right| -2& \cr '.
                '\color{red}{?}&=\left| x\right| -1& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_equation_then_equational() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[(x-1)^2=(x-1)*(x-1), stackeq(x^2-2*x+1)]';
        $sa = "(x-1)^2=(x-1)*(x-1)\n=x^2-2*x+1";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[(x-1)^2 = (x-1)*(x-1),stackeq(x^2-2*x+1)]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\color{green}{\checkmark}&'.
                '{\left(x-1\right)}^2=\left(x-1\right)\,\left(x-1\right)& \cr \color{green}{\checkmark}&=x^2-2\,x+1'.
                '& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_equation_then_equational_1() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[(x-1)^2=(x-1)*(x-1), stackeq(x^2-2*x+2)]';
        $sa = "(x-1)^2=(x-1)*(x-1)\n= x^2-2*x+2";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[(x-1)^2 = (x-1)*(x-1),stackeq(x^2-2*x+2)]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\color{green}{\checkmark}&'.
                '{\left(x-1\right)}^2=\left(x-1\right)\,\left(x-1\right)& \cr \color{red}{?}&=x^2-2\,x+2& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_with_equation_then_equational_2() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[(x-2)^2=x^2-2*x+1, stackeq(x^2-2*x+1)]';
        $sa = "(x-2)^2=(x-1)*(x-1)\n= x^2-2*x+1";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[(x-2)^2 = (x-1)*(x-1),stackeq(x^2-2*x+1)]',
                $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll}\color{red}{?}&{\left(x-2\right)}^2=\left(x-1\right)\,\left(x-1\right)& \cr '.
                '\color{green}{\checkmark}&=x^2-2\,x+1& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_invalid_comments() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6,stackeq((x-2)*(x-3))]');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6\n \"Factoring gives \"\n=(x-2)*(x-3)"), $options,
                '[x^2-5*x+6,stackeq((x-2)*(x-3))]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('[x^2-5*x+6,"Factoring gives ",stackeq((x-2)*(x-3))]', $state->contentsmodified);
        $this->assertEquals('You are not permitted to use comments in this input type.  '.
                'Please just work line by line.', $state->errors);
        $this->assertEquals('equivnocomments', $state->note);
    }

    public function test_validate_student_response_valid_comments() {
        $options = new stack_options();
        $el = stack_input_factory::make('equiv', 'sans1', '[x^2-5*x+6,stackeq((x-2)*(x-3))]');
        $el->set_parameter('options', 'comments');
        $state = $el->validate_student_response(array('sans1' => "x^2-5*x+6\n\"Factoring gives \"\n=(x-2)*(x-3)"), $options,
                '[x^2-5*x+6,stackeq((x-2)*(x-3))]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_forbid_comments() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[(x-2)^2=x^2-2*x+1, stackeq(x^2-2*x+1)]';
        $sa = "x^2-1\nstackeq((x-1)*(x+1))\n\"Comments are forbidden normally\"\nx^2-1=0\n(x-1)*(x+1)=0";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('[x^2-1,stackeq((x-1)*(x+1)),"Comments are forbidden normally",x^2-1 = 0,(x-1)*(x+1) = 0]',
                $state->contentsmodified);
        $this->assertEquals('\(\displaystyle x^2-1 \)<br/>\(\displaystyle =\left(x-1\right)\,\left(x+1\right) \)<br/>' .
                '<span class="stacksyntaxexample">"Comments are forbidden normally"</span> You are not permitted to ' .
                'use comments in this input type. Please just work line by line.<br/>\(\displaystyle x^2-1=0 \)<br/>' .
                '\(\displaystyle \left(x-1\right)\,\left(x+1\right)=0 \)<br/>',
                $state->contentsdisplayed);
        $this->assertEquals('equivnocomments', $state->note);
    }

    public function test_validate_student_response_with_comments() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[(x-2)^2=x^2-2*x+1, stackeq(x^2-2*x+1)]';
        // This long example also tests a switch from equational reasoning to equivalence reasoning and back again.
        $sa = "x^2-1\nstackeq((x-1)*(x+1))\n\"Comments are not forbidden!\"\nx^2-1=0\n(x-1)*(x+1)=0\n\"Comment 2\"\n".
            "x^2-1\n=(x-1)*(x+1)";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $el->set_parameter('options', 'comments');
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^2-1,stackeq((x-1)*(x+1)),"Comments are not forbidden!",x^2-1 = 0,(x-1)*(x+1) = 0,'.
                '"Comment 2",x^2-1,stackeq((x-1)*(x+1))]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2-1& \cr \color{green}{\checkmark}&=\left(x-1\right)\,\left(x+1\right)&'.
                ' \cr &\mbox{Comments are not forbidden!}& \cr &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)'.
                '\,\left(x+1\right)=0& \cr &\mbox{Comment 2}& \cr &x^2-1& \cr \color{green}{\checkmark}'.
                '&=\left(x-1\right)\,\left(x+1\right)& \cr \end{array} \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_surds() {
        $options = new stack_options();
        $options->set_option('multiplicationsign', 'none');
        $ta = '[sqrt((x-3)*(x-5)),stackeq(x^2-8*x+15)]';
        $sa = "sqrt((x-3)*(x-5))\n=sqrt(x-3)*sqrt(x-5)";
        $el = stack_input_factory::make('equiv', 'sans1', $ta);
        $state = $el->validate_student_response(array('sans1' => $sa), $options, $ta, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[sqrt((x-3)*(x-5)),stackeq(sqrt(x-3)*sqrt(x-5))]', $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &\sqrt{\left(x-3\right)\,\left(x-5\right)}&' .
            '{\color{blue}{{x \in {\left[ 5,\, \infty \right) \cup \left( -\infty ,\, 3\right]}}}}\cr \color{red}{?}&' .
            '=\sqrt{x-3}\,\sqrt{x-5}&{\color{blue}{{x \in {\left[ 5,\, \infty \right)}}}}\cr \end{array} \]',
            $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_stacklet() {
        $options = new stack_options();
        $val = '[x^2=a,stacklet(a,4),x^2=4,x=2 nounor x=-2]';
        $el = stack_input_factory::make('equiv', 'sans1', $val);
        $state = $el->validate_student_response(array('sans1' => "x^2=a\nlet a=4\nx^2=4\nx=2 or x=-2"), $options,
            $val, new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
        $this->assertEquals('[x^2 = a,stacklet(a,4),x^2 = 4,x = 2 nounor x = -2]',
            $state->contentsmodified);
        $this->assertEquals('\[ \begin{array}{lll} &x^2=a& \cr &\mbox{Let }a = 4& \cr ' .
            '\color{green}{\Leftrightarrow}&x^2=4& \cr ' .
            '\color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=-2& \cr \end{array} \]',
            $state->contentsdisplayed);

        $ta = $el->get_teacher_answer();
        $this->assertEquals($ta, $val);

        $cr = $el->get_correct_response($val);
        $sans1 = "x^2 = a\nlet a=4\nx^2 = 4\nx = 2 or x = -2";
        $sansv = '[x^2 = a,stacklet(a,4),x^2 = 4,x = 2 or x = -2]';

        $this->assertEquals($cr['sans1'], $sans1);
        $this->assertEquals($cr['sans1_val'], $sansv);
    }

    public function test_validate_student_pm() {
        $options = new stack_options();
        // This contains infix and prefix +- operations.
        $val = '[(x-a)^2=4,x-a= #pm#2,x=a#pm#2]';
        $el = stack_input_factory::make('equiv', 'sans1', $val);
        $state = $el->validate_student_response(array('sans1' => "(x-a)^2=4\nx-a= +-2\nx=a+-2"), $options,
                $val, new stack_cas_security());

        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('', $state->note);
        $this->assertEquals('[(x-a)^2 = 4,x-a = #pm#2,x = a#pm#2]',
                $state->contentsmodified);
        $this->assertEquals( '\[ \begin{array}{lll} &{\left(x-a\right)}^2=4& \cr ' .
                '\color{green}{\Leftrightarrow}&x-a= \pm 2& \cr ' .
                '\color{green}{\Leftrightarrow}&x={a \pm 2}& \cr \end{array} \]',
                $state->contentsdisplayed);

        $ta = $el->get_teacher_answer();
        $this->assertEquals($ta, $val);

        $cr = $el->get_correct_response($val);
        $sans1 = "(x-a)^2 = 4\nx-a = +-2\nx = a+-2";
        $sansv = '[(x-a)^2 = 4,x-a = #pm#2,x = a#pm#2]';

        $this->assertEquals($cr['sans1'], $sans1);
        $this->assertEquals($cr['sans1_val'], $sansv);

        // The test below does not use the LaTeX of the teacher's answer.
        // The test just confirms #pm# in $val get converted to something the student should type in.
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( ### \)</span></span>, which can be typed in as follows: <br/>' .
                '<code>(x-a)^2 = 4</code><br/><code>x-a = +-2</code><br/><code>x = a+-2</code>',
                $el->get_teacher_answer_display($val, '###'));
    }

    public function test_validate_student_response_forbidwords_lists() {
        // This input type always returns a list, so if we forbid lists then we need a special case.
        $options = new stack_options();

        $val = '[x^2=-4,x^2+4=0,{}]';
        $el = stack_input_factory::make('equiv', 'sans1', $val);
        $el->set_parameter('forbidWords', 'solve, [, factor');
        $state = $el->validate_student_response(array('sans1' => "x^2=-4\nx^2+4=0\n[]"), $options,
                $val, new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Forbidden operator: <span class="stacksyntaxexample">[</span>.', $state->errors);
        $this->assertEquals('forbiddenOp', $state->note);
        $this->assertEquals('[x^2 = -4,x^2+4 = 0,EMPTYCHAR]',
                $state->contentsmodified);
        $this->assertEquals('\(\displaystyle x^2=-4 \)<br/>\(\displaystyle x^2+4=0 \)<br/>' .
                '<span class="stacksyntaxexample">[]</span> Forbidden operator: ' .
                '<span class="stacksyntaxexample">[</span>.<br/>',
                $state->contentsdisplayed);

        $ta = $el->get_teacher_answer();
        $this->assertEquals($ta, $val);

        $cr = $el->get_correct_response($val);
        $sans1 = "x^2 = -4\nx^2+4 = 0\n{}";
        $sansv = '[x^2 = -4,x^2+4 = 0,{}]';

        $this->assertEquals($cr['sans1'], $sans1);
        $this->assertEquals($cr['sans1_val'], $sansv);
    }
}
