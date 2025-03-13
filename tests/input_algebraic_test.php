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

// Unit tests for stack_algebra_input.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_algebraic_input
 */
class input_algebraic_test extends qtype_stack_testcase {

    public function test_internal_validate_parameter() {
        $el = stack_input_factory::make('algebraic', 'input', 'x^2');
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
        $el = stack_input_factory::make('algebraic', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="" '
                .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
                .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'ans1', 'x^2');
        $el->set_parameter('options', 'allowempty');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" '
                .'size="16.5" style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="" '
                .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
                .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_zero() {
        $el = stack_input_factory::make('algebraic', 'ans1', '0');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
            .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="0" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, ['0'], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_pre_filled() {
        $el = stack_input_factory::make('algebraic', 'test', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" size="16.5" '
            .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="x+y" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, ['x+y'], '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_pre_filled_nasty_input() {
        $el = stack_input_factory::make('algebraic', 'test', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" size="16.5" '
            .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="x&lt;y" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, ['x<y'], '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_max_length() {
        $el = stack_input_factory::make('algebraic', 'test', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" size="16.5" '
            .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="x+y" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, ['x+y'], '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_disabled() {
        $el = stack_input_factory::make('algebraic', 'input', 'x^2');
        $this->assertEquals(
                '<input type="text" name="stack1__input" id="stack1__input" size="16.5" style="width: 13.6em" '
            .'autocapitalize="none" spellcheck="false" class="algebraic" value="x+1" readonly="readonly" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, ['x+1'], '', '', '', '', ''),
                        'stack1__input', true, null));
    }

    public function test_render_different_size() {
        $el = stack_input_factory::make('algebraic', 'input', 'x^2');
        $el->set_parameter('boxWidth', 30);
        $this->assertEquals('<input type="text" name="stack1__input" id="stack1__input" size="33" style="width: 27.1em" '
            .'autocapitalize="none" spellcheck="false" class="algebraic" value="x+1" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::VALID, ['x+1'], '', '', '', '', ''),
                        'stack1__input', false, null));
    }

    public function test_render_placeholder() {
        $el = stack_input_factory::make('algebraic', 'sans1', '[a, b, c]');
        $el->set_parameter('syntaxHint', 'Remove me');
        $el->set_parameter('syntaxAttribute', 1);
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" size="16.5" style="width: 13.6em" '
            .'autocapitalize="none" spellcheck="false" class="algebraic" placeholder="Remove me" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'stack1__sans1', false, null));
    }

    public function test_syntaxhint() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '[a, b, c]');
        $el->set_parameter('syntaxHint', '[?, ?, ?]');
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" size="16.5" '
            .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="[?, ?, ?]" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." '
            .'data-stack-input-list-separator="," />',
            $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                'stack1__sans1', false, null));

        $state = $el->validate_student_response(['sans1' => '[?, ?, ?]', 'sans1_val' => '[?, ?, ?]'],
            $options, '[a, b, c]', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('qm_error', $state->note);
        $this->assertEquals('[QMCHAR,QMCHAR,QMCHAR]', $state->contentsmodified);
        $this->assertEquals('\[ \left[ \color{red}{?} , \color{red}{?} , \color{red}{?} \right] \]',
            $state->contentsdisplayed);
        $this->assertEquals('Your answer contains question mark characters, ?, which are not permitted in answers.  ' .
            'You should replace these with a specific value.', $state->errors);
    }

    public function test_validate_student_response_algebraic_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $state = $el->validate_student_response(['sans1' => 'x^2'], $options, 'x^2/(1+x^2)', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
          . '<span class="nolink">\( \frac{x^2}{1+x^2} \)</span></span>, which can be typed as '
          . '<code>x^2/(1+x^2)</code>, would be correct.',
            $el->get_teacher_answer_display('x^2/(1+x^2)', '\frac{x^2}{1+x^2}'));

        $el->set_parameter('showValidation', 1);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val" aria-live="assertive">' .
                '<p>Your last answer was interpreted as follows: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\[ x^2 \]</span></span></p>' .
                '<input type="hidden" name="sans1_val" value="x^2" />The variables found in your answer were: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\( \left[ x \right]\)</span></span> ' .
                '</div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));

        $el->set_parameter('showValidation', 2);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val" aria-live="assertive">' .
                '<p>Your last answer was interpreted as follows: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\[ x^2 \]</span></span></p>' .
                '<input type="hidden" name="sans1_val" value="x^2" /></div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));

        $el->set_parameter('showValidation', 3);
        // We re-generate the state to get inline displayed equations.
        $state = $el->validate_student_response(['sans1' => 'x^2'], $options, 'x^2/(1+x^2)', new stack_cas_security());
        $vr = '<span class="stackinputfeedback compact" id="sans1_val" aria-live="assertive">' .
                '<span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( x^2 \)</span></span><input type="hidden" name="sans1_val" value="x^2" /></span>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));
    }

    public function test_validate_student_response_algebraic_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $state = $el->validate_student_response(['sans1' => '2x(1+x^2)'], $options, 'x^2/(1+x^2)', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);

        $el->set_parameter('showValidation', 1);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val" aria-live="assertive">' .
                '<p>Your last answer was interpreted as follows: ' .
                '<span class="stacksyntaxexample">2x(1+x^2)</span></p>' .
                '<input type="hidden" name="sans1_val" value="2x(1+x^2)" /><div class="alert alert-danger stackinputerror">' .
                'This answer is invalid. You seem to be missing * characters. ' .
                'Perhaps you meant to type <span class="stacksyntaxexample">2' .
                '<span class="stacksyntaxexamplehighlight">*</span>x' .
                '<span class="stacksyntaxexamplehighlight">*</span>(1+x^2)</span>.</div></div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));
    }

    public function test_validate_student_response_algebraic_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '2x', 'sans1_val' => '2x'], $options, 'x^2/(1+x^2)',
                new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_algebraic_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '2x(1+x^2)'], $options, 'x^2/(1+x^2)',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars | Variable_function', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_algebraic_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '2x(1+x^2)+tans'], $options, 'x^2/(1+x^2)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | Variable_function | forbiddenVariable', $state->note);
    }

    public function test_validate_student_response_algebraic_6() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '2*x/(1+x^2)+sillyname(x)'],
                $options, 'x^2/(1+x^2)', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
    }

    public function test_validate_student_response_algebraic_7() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(['sans1' => '2x(1+x^2)+tans'], $options, 'x^2/(1+x^2)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | Variable_function | forbiddenVariable', $state->note);
    }

    public function test_validate_student_response_algebraic_8() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2+1/3');
        $el->set_parameter('forbidFloats', true);
        $state = $el->validate_student_response(['sans1' => 'x^2+0.33'], $options, 'x^2+1/3',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Illegal_floats', $state->note);
    }

    public function test_validate_student_response_algebraic_9() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1<x nounand x<8');
        $state = $el->validate_student_response(['sans1' => '1<x and x<7'], $options, '1<x nounand x<8',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('1 < x nounand x < 7', $state->contentsmodified);
        $this->assertEquals('\[ 1 < x\,{\text{ and }}\, x < 7 \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
            . '<span class="nolink">\( 1<x \,{\text{and}}\,x<8 \)</span></span>, which can be typed as '
            . '<code>1 < x and x < 8</code>, would be correct.',
            $el->get_teacher_answer_display('1<x nounand x<8', '1<x \,{\text{and}}\,x<8'));
    }

    public function test_validate_student_response_algebraic_10() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'not false xor not(false)');
        $state = $el->validate_student_response(['sans1' => 'not false xor not(false)'], $options,
                'not false xor not(false)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('nounnot false xor nounnot(false)', $state->contentsmodified);
        $this->assertEquals('\[ {\rm not}\left( \mathbf{False} \right)\,{\text{ xor }}\, ' .
                '{\rm not}\left( \mathbf{False} \right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_algebraic_11() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'i*(x+1) + j*(2*x+3) + k*(3*x+4)');
        $el->set_parameter('insertStars', 5);

        // Juxtaposition.
        $state = $el->validate_student_response(['sans1' => ' i(x+1) + j(2x+3) + k(3x+4)'], $options,
            'i*(x+1) + j*(2*x+3) + k*(3*x+4)',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        // Note that we have a special filter: stack_ast_filter_005_i_is_never_a_function.
        $this->assertEquals('i*(x+1)+j(2*x+3)+k(3*x+4)', $state->contentsmodified);
        $this->assertEquals('\[ \mathrm{i}\cdot \left(x+1\right)+j\left(2\cdot x+3\right)+k\left(3\cdot x+4\right) \]',
            $state->contentsdisplayed);

        // Spaces.
        $state = $el->validate_student_response(['sans1' => ' i (x+1) + j (2x+3) + k (3x+4)'], $options,
            'i*(x+1) + j*(2*x+3) + k*(3*x+4)',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars | spaces', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('i*(x+1)+j*(2*x+3)+k*(3*x+4)', $state->contentsmodified);
        $this->assertEquals('\[ \mathrm{i}\cdot \left(x+1\right)+j\cdot \left(2\cdot x+3\right)+' .
            'k\cdot \left(3\cdot x+4\right) \]',
            $state->contentsdisplayed);

        // Replace unknown functions with multiplication.  Note, i can never be a function.
        // The sin(x) is a known function and so is retained.
        $el->set_parameter('insertStars', 6);
        $state = $el->validate_student_response(['sans1' => ' j(2 x+3) + k(3ab+4) + sin(x)'], $options,
            'j*(2*x+3) + k*(3*x+4)',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars | spaces | function_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('j*(2*x+3)+k*(3*ab+4)+sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ j\cdot \left(2\cdot x+3\right)+k\cdot \left(3\cdot {\it ab}+4\right)+\sin \left( x \right) \]',
            $state->contentsdisplayed);

        // In addition we assume single variable names.
        $el->set_parameter('insertStars', 7);
        $state = $el->validate_student_response(['sans1' => ' j(2 x+3) + k(3ab+4) + sin(x)'], $options,
            'j*(2*x+3) + k*(3*x+4)',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars | spaces | function_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('j*(2*x+3)+k*(3*a*b+4)+sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ j\cdot \left(2\cdot x+3\right)+k\cdot \left(3\cdot a\cdot b+4\right)+\sin \left( x \right) \]',
            $state->contentsdisplayed);

        // No option yet to split up pi in to p*i, even with "single letter names".
        $el->set_parameter('insertStars', 7);
        $state = $el->validate_student_response(['sans1' => '3pi'], $options,
            '3*pi',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('3*%pi', $state->contentsmodified);
        $this->assertEquals('\[ 3\cdot \pi \]',
            $state->contentsdisplayed);
    }

    public function test_validate_student_response_too_long() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $sa = '1' . str_repeat('0', 32768);
        $state = $el->validate_student_response(['sans1' => $sa], $options, 'x^2/(1+x^2)',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('too_long', $state->note);
        $this->assertEquals('Your input is longer than permitted by STACK.', $state->errors);
    }

    public function test_validate_student_response_ex() {
        // The variable ex is used an argument to some Maxima functions and as a local variable.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3*ex+2*ey+5*ez');
        $state = $el->validate_student_response(['sans1' => '3*ex+2*ey+5*ez'], $options,
            '3*ex+2*ey+5*ez',
            new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('3*ex+2*ey+5*ez', $state->contentsmodified);
        $this->assertEquals('\[ 3\cdot {\it ex}+2\cdot {\it ey}+5\cdot {\it ez} \]',
            $state->contentsdisplayed);

        $el->set_parameter('showValidation', 1);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val" aria-live="assertive">' .
            '<p>Your last answer was interpreted as ' .
            'follows: <span class="filter_mathjaxloader_equation"><span class="nolink">' .
            '\[ 3\cdot {\it ex}+2\cdot {\it ey}+5\cdot {\it ez} \]</span></span></p>' .
            '<input type="hidden" name="sans1_val" value="3*ex+2*ey+5*ez" />The variables found in your answer ' .
            'were: <span class="filter_mathjaxloader_equation"><span class="nolink">' .
            '\( \left[ {\it ex} , {\it ey} , {\it ez} \right]\)</span></span> </div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));
    }

    public function test_validate_student_lowest_terms_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '12/4');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(['sans1' => '12/4'], $options, '3',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
    }

    public function test_validate_student_lowest_terms_2() {
        // This test checks the unary minus is *not* in lowest terms.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '-10/-1');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(['sans1' => '-10/-1'], $options, '10',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
    }

    public function test_validate_student_response_with_minus_zero() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['sans1' => "x-0"], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x-0', $state->contentsmodified);
        $this->assertEquals('\[ x-0 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);

        $state = $el->validate_student_response(['sans1' => "x-0.0"], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x-0.0', $state->contentsmodified);
        $this->assertEquals('\[ x-0.0 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);

        $state = $el->validate_student_response(['sans1' => "x*(-0.0)"], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x*(-0.0)', $state->contentsmodified);
        $this->assertEquals('\[ x\cdot \left(-0.0\right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_with_rationalized() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'rationalized');
        $state = $el->validate_student_response(['sans1' => "x^2+x/sqrt(2)"], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2+x/sqrt(2)', $state->contentsmodified);
        $this->assertEquals('\[ x^2+\frac{x}{\sqrt{2}} \]', $state->contentsdisplayed);
        $this->assertEquals('You must clear the following from the denominator of your fraction: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\[ \left[ \sqrt{2} \right] \]' .
                '</span></span>', $state->errors);
    }

    public function test_validate_student_response_subscripts() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))');
        $state = $el->validate_student_response(['sans1' => 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))'],
                $options, 'x^2+1/3', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_subscript_compare() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x_1');

        $state = $el->validate_student_response(['sans1' => 'x_1'],
                $options, 'x_1', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('x_1', $state->contentsmodified);
        $this->assertEquals('\[ {x}_{1} \]', $state->contentsdisplayed);

        $state = $el->validate_student_response(['sans1' => 'x1'],
                $options, 'x_1', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('x1', $state->contentsmodified);
        $this->assertEquals('\[ x_{1} \]', $state->contentsdisplayed);

        $state = $el->validate_student_response(['sans1' => 'x[1]'],
                $options, 'x_1', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('x[1]', $state->contentsmodified);
        $this->assertEquals('\[ x_{1} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_trigexp_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'sin(ab)^2');
        $state = $el->validate_student_response(['sans1' => 'sin^2(ab)'], $options, 'sin(ab)^2',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('sin^2*(ab)', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">sin^2(ab)</span>', $state->contentsdisplayed);
        $this->assertEquals('missing_stars | trigexp', $state->note);
    }

    public function test_validate_student_response_insertstars_true_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '2x'], $options, '2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_insertstars_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(['sans1' => '2x'], $options, '2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_insertstars_sqrt_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*sqrt(2)/3');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '2*sqrt(+2)/3'], $options, '2*sqrt(2)/3',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('2*sqrt(+2)/3', $state->contentsmodified);
        // Maxima's TeX code pulls out the + to outside the sqrt. Known edge case.
        $this->assertEquals('\[ \frac{2\cdot +\sqrt{2}}{3} \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
            . '<span class="nolink">\( \frac{2\cdot \sqrt{2}}{3} \)</span></span>, which can be typed as '
            . '<code>2*sqrt(2)/3</code>, would be correct.',
            $el->get_teacher_answer_display('2*sqrt(2)/3', '\frac{2\cdot \sqrt{2}}{3}'));
    }

    public function test_validate_student_response_sametype_true_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(['sans1' => '2*x'], $options, 'y=2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_sametype_true_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'y=2*x');
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(['sans1' => '2*x'], $options, 'y=2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_sametype_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'y=2*x');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '2*x'], $options, 'y=2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_equation', $state->note);
    }

    public function test_validate_student_response_sametype_false_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'm*x+c');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'y=m*x+c'], $options, 'm*x+c',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("ATAlgEquiv_TA_not_equation", $state->note);
    }

    public function test_validate_student_response_sametype_false_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '{1,2}');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '1'], $options, '{1,2}',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_set', $state->note);
    }

    public function test_validate_student_response_sametype_false_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '{x}'], $options, 'x',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_expression', $state->note);
    }

    public function test_validate_student_response_sametype_subscripts_true_valid() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'mu_0*(I_0-I_1)');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'mu_0*(I_1-I_2)'], $options, 'x',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('mu_0*(I_1-I_2)', $state->contentsmodified);
        $this->assertEquals('\[ {\mu}_{0}\cdot \left({I}_{1}-{I}_{2}\right) \]', $state->contentsdisplayed);
        if ($this->adapt_to_new_maxima('5.34.2')) {
            // Why change the order here?
            $this->assertEquals('\( \left[ {I}_{1} , {I}_{2} , {\mu}_{0} \right]\) ', $state->lvars);
        } else {
            $this->assertEquals('\( \left[ {\mu}_{0} , {I}_{1} , {I}_{2} \right]\) ', $state->lvars);
        }
    }

    public function test_validate_student_response_sametype_subscripts_true_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'mu_0*(I_0-I_1)');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '{mu_0*(I_1-I_2)}'], $options, 'x',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_expression', $state->note);
        $this->assertEquals('{mu_0*(I_1-I_2)}', $state->contentsmodified);
        $this->assertEquals('\[ \left \{{\mu}_{0}\cdot \left({I}_{1}-{I}_{2}\right) \right \} \]',
            $state->contentsdisplayed);
        if ($this->adapt_to_new_maxima('5.34.2')) {
            // Why change the order here?
            $this->assertEquals('\( \left[ {I}_{1} , {I}_{2} , {\mu}_{0} \right]\) ', $state->lvars);
        } else {
            $this->assertEquals('\( \left[ {\mu}_{0} , {I}_{1} , {I}_{2} \right]\) ', $state->lvars);
        }
    }

    public function test_validate_student_response_display_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '-3*x^2-4');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '-3x^2-4'], $options, '-3*x^2-4', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        // Hack to accomodate Maxima version 5.37.0 onwards.
        $content = $state->contentsmodified;
        if ($content === '(-3)*x^2-4') {
            $content = '-3*x^2-4';
        }
        $this->assertEquals('-3*x^2-4', $content);
        $this->assertEquals('\[ -3\cdot x^2-4 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '(3*x+1)*(x+ab)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '(3x+1)(x+ab)'], $options, '(3*x+1)*(x+ab)',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(3*x+1)*(x+ab)', $state->contentsmodified);
        $this->assertEquals('\[ \left(3\cdot x+1\right)\cdot \left(x+{\it ab}\right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 's^(24*r)');
        $el->set_parameter('insertStars', 1);
        // For this test, if sameType is true, old versions of Maxima blow up with
        // Heap exhausted during allocation: 8481509376 bytes available, 35303692080 requested.
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(['sans1' => 's^r^24'], $options, 's^(24*r)', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('s^r^24', $state->contentsmodified);
        $this->assertEquals('\[ s^{r^{24}} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_noundiff() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'noundiff(y/x^2,x,1)-(2*y)/x = x^3*sin(3*x)');
        $el->set_parameter('insertStars', 1);
        // For this test, if sameType is true, old versions of Maxima blow up with
        // Heap exhausted during allocation: 8481509376 bytes available, 35303692080 requested.
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(['sans1' => 'noundiff(y/x^2,x,1)-(2*y)/x = x^3*sin(3*x)'],
                $options, 'diff(y/x^2,x,1)-(2*y)/x = x^3*sin(3*x)', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('noundiff(y/x^2,x,1)-(2*y)/x = x^3*sin(3*x)', $state->contentsmodified);
        $this->assertEquals('\[ \left(\frac{\mathrm{d}}{\mathrm{d} x} \frac{y}{x^2}\right)-\frac{2\cdot y}{x}=x^3\cdot ' .
                '\sin \left( 3\cdot x \right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_extra_evaluation() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'noundiff(y/x^2,x,1)-(2*y)/x = x^3*sin(3*x)');
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(['sans1' => "''diff(y/x^2,x,1)"],
            $options, 'diff(y/x^2,x,1)-(2*y)/x = x^3*sin(3*x)', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Illegal_extraevaluation', $state->note);
        $this->assertEquals("Maxima's extra evaluation operator <code>''</code> is not supported by STACK.",
            $state->errors);
        $this->assertEquals("''%_E(%_E(noundiff(y/x^2,x,1)))", $state->contentsmodified);
        $this->assertEquals("<span class=\"stacksyntaxexample\">''diff(y/x^2,x,1)</span>",
            $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_var_chars_on() {
        // Check the single variable character option is tested.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '(3*x+1)*(x+ab)');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => '(3x+1)(x+ab)'], $options, '(3*x+1)*(x+ab)',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('(3*x+1)*(x+a*b)', $state->contentsmodified);
        $this->assertEquals('\[ \left(3\cdot x+1\right)\cdot \left(x+a\cdot b\right) \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a , b , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_single_var_chars_off() {
        // Check the single variable character option is tested.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '(3x+1)*(x+ab)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '(3x+1)(x+ab)'], $options, '(3*x+1)*(x+ab)',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('(3*x+1)*(x+ab)', $state->contentsmodified);
        $this->assertEquals('\[ \left(3\cdot x+1\right)\cdot \left(x+{\it ab}\right) \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ {\it ab} , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_single_var_chars_div() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '0<x^2');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => '4<div'], $options, '0<x^2',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('4 < d*i*v', $state->contentsmodified);
        $this->assertEquals('\[ 4 < d\cdot \mathrm{i}\cdot v \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ d , v \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_allowwords_false() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $state = $el->validate_student_response(['sans1' => 'unknownfunction(x^2+1)+3*x'], $options, '2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
    }

    public function test_validate_student_response_allowwords_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('allowWords', 'pop, funney1, unknownfunction');
        $state = $el->validate_student_response(['sans1' => 'unknownfunction(x^2+1)+3*x'], $options, '2*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_allowwords_402() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');

        $state = $el->validate_student_response(['sans1' => 'inverse_erf(x)+1'], $options, '2*x',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | (402) | forbiddenVariable', $state->note);

        $el->set_parameter('allowWords', 'inverse_erf');
        $state = $el->validate_student_response(['sans1' => 'inverse_erf(x)+1'], $options, '2*x',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_forbidwords_none() {
        // Some functions are converted to "noun" forms.
        // When we give feedback "your last answer was..." we want the correct forms, not the "nounint" alternatives.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $state = $el->validate_student_response(['sans1' => 'int(x^2+1,x)+c'], $options, 'int(x^2+1,x)+c',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('nounint(x^2+1,x)+c', $state->contentsmodified);
        $this->assertEquals('\[ \int {x^2+1}{\;\mathrm{d}x}+c \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ c , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_forbidwords_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('forbidWords', 'int, diff');
        $state = $el->validate_student_response(['sans1' => 'int(x^2+1,x)+c'], $options, 'int(x^2+2,x)+c',
                new stack_cas_security(false, '', '', ['ta']));
        // Note the "nounint" in the contentsmodified.
        $this->assertEquals('nounint(x^2+1,x)+c', $state->contentsmodified);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">int</span>.', $state->errors);
        // The noun form has been converted back to "int" in the contentsdisplayed.
        $this->assertEquals('<span class="stacksyntaxexample">int(x^2+1,x)+c</span>', $state->contentsdisplayed);
    }

    public function test_validate_student_response_forbidwords_int() {
        // We need this as an alias.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'int(x^2+1,x)+c');
        $state = $el->validate_student_response(['sans1' => 'integrate(x^2+1,x)+c'], $options, 'int(x^2+1,x)+c',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('nounint(x^2+1,x)+c', $state->contentsmodified);
        $this->assertEquals('\[ \int {x^2+1}{\;\mathrm{d}x}+c \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ c , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_forbidwords_int_true() {
        // We need this as an alias.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('forbidWords', 'int, diff');
        $state = $el->validate_student_response(['sans1' => 'integrate(x^2+1,x)+c'], $options, 'int(x^2+1,x)+c',
                new stack_cas_security(false, '', '', ['ta']));
        // Note the "nounint" in the contentsmodified.
        $this->assertEquals('nounint(x^2+1,x)+c', $state->contentsmodified);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('<span class="stacksyntaxexample">integrate(x^2+1,x)+c</span>', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'cos(a*x)/(x*(ln(x)))');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => 'cos(ax)/(x(ln(x)))'], $options, 'cos(a*x)/(x*(ln(x)))',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars | Variable_function', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('cos(a*x)/(x*(ln(x)))', $state->contentsmodified);
        $this->assertEquals('\[ \frac{\cos \left( a\cdot x \right)}{x\cdot \ln \left( x \right)} \]',
                $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_xx() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => 'xx'], $options, 'x*x',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x*x', $state->contentsmodified);
        $this->assertEquals('\[ x\cdot x \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_subscripts() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'a*b_c*d');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => 'ab_cd'], $options, 'a*b_c*d',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('a*b_cd', $state->contentsmodified);
        $this->assertEquals('\[ a\cdot {b}_{{\it cd}} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_subscripts2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'a*v_max');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => 'av_max'], $options, 'a*v_max',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('a*v_max', $state->contentsmodified);
        $this->assertEquals('\[ a\cdot {v}_{{\it max}} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_trigexp() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'sin(ab)^2');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(['sans1' => 'sin(ab)^2'], $options, 'sin(ab)^2',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('sin(a*b)^2', $state->contentsmodified);
        $this->assertEquals('\[ \sin ^2\left(a\cdot b\right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_trigexp_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'sin(ab)^2');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(['sans1' => 'sin^2(ab)'], $options, 'sin(ab)^2',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | trigexp', $state->note);
        $this->assertEquals('s*i*n^2*(a*b)', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">sin^2(ab)</span>', $state->contentsdisplayed);
        $this->assertEquals('missing_stars | trigexp', $state->note);
    }

    public function test_validate_student_response_functions_variable() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'a/(a*(x+1)+2)');

        $state = $el->validate_student_response(['sans1' => 'a/(a(x+1)+2)'], $options, 'a/(a*(x+1)+2)',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("missing_stars | Variable_function", $state->note);
    }

    public function test_validate_student_response_simp_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '[1,4,9,16,25,36,49,64]');
        $el->set_parameter('options', 'simp');
        $state = $el->validate_student_response(['sans1' => 'makelist(k^2,k,1,8)'], $options,
                '[1,4,9,16,25,36,49,64]', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $content = $state->contentsmodified;
        $this->assertEquals('makelist(k^2,k,1,8)', $content);
        $this->assertEquals('\[ \left[ 1 , 4 , 9 , 16 , 25 , 36 , 49 , 64 \right] \]',
                $state->contentsdisplayed);
    }

    public function test_validate_student_response_simp_float_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '7.0');
        $el->set_parameter('forbidFloats', false);
        $el->set_parameter('options', 'simp');
        $state = $el->validate_student_response(['sans1' => '3.5*2.000'], $options,
            '7.0', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $content = $state->contentsmodified;
        $this->assertEquals('3.5*2.000', $content);
        $this->assertEquals('\[ 7.000 \]',
            $state->contentsdisplayed);
    }

    public function test_validate_student_response_simp_float_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '7.0');
        $el->set_parameter('forbidFloats', false);
        $el->set_parameter('options', 'simp');
        $state = $el->validate_student_response(['sans1' => '3.50*2*x^2+2.000*7.45'], $options,
            '7.0', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $content = $state->contentsmodified;
        $this->assertEquals('3.50*2*x^2+2.000*7.45', $content);
        // We throw away dp information, but we do the calculations.
        $this->assertEquals('\[ 7.0\cdot x^2+14.9 \]',
            $state->contentsdisplayed);
    }

    public function test_validate_lg_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'lg(27,3)');
        $state = $el->validate_student_response(['sans1' => 'lg(27,3)'], $options, 'lg(27,3)', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('lg(27,3)', $state->contentsmodified);
        $this->assertEquals('\[ \log_{3}\left(27\right) \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[ \[ \log_{3}\left(27\right) \]</span></span> \), ' .
                'which can be typed as <code>lg(27,3)</code>, would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_lg_10() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'lg(23,10)');
        $state = $el->validate_student_response(['sans1' => 'lg(23,10)'], $options, 'lg(23,10)', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('lg(23,10)', $state->contentsmodified);
        $this->assertEquals('\[ \log_{10}\left(23\right) \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[ \[ \log_{10}\left(23\right) \]</span></span> \), ' .
                'which can be typed as <code>lg(23,10)</code>, would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_lg_10b() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'lg(19)');
        $state = $el->validate_student_response(['sans1' => 'lg(19)'], $options, 'lg(19)', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('lg(19)', $state->contentsmodified);
        $this->assertEquals('\[ \log_{10}\left(19\right) \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\[ \[ \log_{10}\left(19\right) \]</span></span> \), ' .
                'which can be typed as <code>lg(19)</code>, would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_ln() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3 ln 19');
        $el->set_parameter('insertStars', 4);
        $state = $el->validate_student_response(['sans1' => '3 ln 19'], $options, '3*ln(19)', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('spaces | forbiddenVariable', $state->note);
        $this->assertEquals('3*ln*19', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">3 ln 19</span>', $state->contentsdisplayed);
    }

    public function test_validate_set_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '{a,b,c}');
        $state = $el->validate_student_response(['sans1' => '{a,b,c}'], $options, '{a,b,c}', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('{a,b,c}', $state->contentsmodified);
        $this->assertEquals('\[ \left \{a , b , c \right \} \]', $state->contentsdisplayed);
    }

    public function test_validate_or_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x=1 or x=1');
        $state = $el->validate_student_response(['sans1' => 'x=1 or x=1'], $options, 'x=1 or x=1',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x = 1 nounor x = 1', $state->contentsmodified);
        $this->assertEquals('\[ x=1\,{\text{ or }}\, x=1 \]', $state->contentsdisplayed);
    }

    public function test_validate_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '9');
        $state = $el->validate_student_response(['sans1' => '9*hz'], $options, '9', new stack_cas_security());
        // In the units input this would be INVALID as hz should be Hz.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9*hz', $state->contentsmodified);
        $this->assertEquals('\[ 9\cdot {\it hz} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '"Hello world"'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Hello world} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type_lt() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '"Hello < world"'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello < world"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Hello &lt; world} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type_invalid1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '"Hello world"'], $options, 'x^2', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Hello world} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type_invalid2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'x^2'], $options, '"A random string"',
                new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2', $state->contentsmodified);
        $this->assertEquals('\[ x^2 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_with_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'allowempty');
        $state = $el->validate_student_response(['sans1' => ''], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('This input can be left blank.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_student_response_with_allowempty_stars() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'allowempty');

        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => ''], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('This input can be left blank.',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));

        // Assuming single character variable names.
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => ''], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('This input can be left blank.',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));

        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(['sans1' => ''], $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('This input can be left blank.',
            $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_string_same_type_invalid_division_zero() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^3');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'x/0'], $options, 'x^3', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x/0', $state->contentsmodified);
        $this->assertEquals('\[ \frac{x}{0} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_star_space_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3*sin(a*b)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(['sans1' => '3sin(a b)'], $options, '3sin(a b)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | spaces', $state->note);
        $this->assertEquals('3*sin(a*b)', $state->contentsmodified);
        $this->assertEquals('Illegal spaces found in expression <span class="stacksyntaxexample">' .
                '3*sin(a<span class="stacksyntaxexamplehighlight">_</span>b)</span>.', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3\, \sin(a \cdot b) \)</span></span>, ' .
                'which can be typed as <code>3*sin(a*b)</code>, would be correct.',
                $el->get_teacher_answer_display('3*sin(a*b)', '3\\, \\sin(a \cdot b)'));
    }

    public function test_validate_student_response_star_space_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3*sin(a*b)');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => '3sin(a b)'], $options, '3sin(a b)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('3*sin(a*b)', $state->contentsmodified);
        $this->assertEquals('missing_stars | spaces', $state->note);
        $this->assertEquals('Illegal spaces found in expression <span class="stacksyntaxexample">' .
                '3*sin(a<span class="stacksyntaxexamplehighlight">_</span>b)</span>.', $state->errors);
    }

    public function test_validate_student_response_star_space_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3*sin(a*b)');
        $el->set_parameter('insertStars', 3);
        $state = $el->validate_student_response(['sans1' => '3sin(a b)'], $options, '3*sin(a*b)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('3*sin(a*b)', $state->contentsmodified);
        $this->assertEquals('missing_stars | spaces', $state->note);
        $this->assertEquals('You seem to be missing * characters. ' .
                'Perhaps you meant to type <span class="stacksyntaxexample">3' .
                '<span class="stacksyntaxexamplehighlight">*</span>sin(a*b)</span>.',
                $state->errors);
    }

    public function test_validate_student_response_star_space_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3*sin(a*b)');
        $el->set_parameter('insertStars', 4);
        $state = $el->validate_student_response(['sans1' => '3sin(a b)'], $options, '3*sin(a*b)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3*sin(a*b)', $state->contentsmodified);
        $this->assertEquals('missing_stars | spaces', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_star_space_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3*sin(a*b)');
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(['sans1' => '3sin(a b)'], $options, '3*sin(a*b)',
                new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3*sin(a*b)', $state->contentsmodified);
        $this->assertEquals('missing_stars | spaces', $state->note);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_almost_cardano() {
        // This has a double +- in the input.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x=-b+-sqrt(b*c^2-a)');
        $state = $el->validate_student_response(['sans1' => 'x=(-q+-sqrt(q^2-p^3))^(1/3)+(-q+-sqrt(q^2-p^3))^(1/3)'],
            $options, 'x=-b#pm#sqrt(b*c^2-a)', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x = (-q#pm#sqrt(q^2-p^3))^(1/3)+(-q#pm#sqrt(q^2-p^3))^(1/3)', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('\[ x={\left({-q \pm \sqrt{q^2-p^3}}\right)}^{\frac{1}{3}}+' .
            '{\left({-q \pm \sqrt{q^2-p^3}}\right)}^{\frac{1}{3}} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_prefixpm() {
        // This has a prefix +- in the input.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x= +- b');
        $state = $el->validate_student_response(['sans1' => 'x= +- b'],
            $options, 'x= #pm# b', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x = #pm#b', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('\[ x= \pm b \]', $state->contentsdisplayed);
        // Internally the teacher's answer will be in the #pm# form, which is not what students type.
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
                . '<span class="nolink">\( x= \pm b \)</span></span>, which can be typed as '
                . '<code>x = +-b</code>, would be correct.', $el->get_teacher_answer_display('x= #pm# b', 'x= \pm b'));
    }

    public function test_validate_student_response_pm_expr() {
        // This has an expression with more than one +- in the input.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'a +- b +- c');
        $state = $el->validate_student_response(['sans1' => 'a +- b +- c'],
            $options, 'a#pm#b#pm#c', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('a#pm#b#pm#c', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('\[ {a \pm b \pm c} \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
                . '<span class="nolink">\( {a \pm b \pm c} \)</span></span>, which can be typed as '
                . '<code>a+-b+-c</code>, would be correct.', $el->get_teacher_answer_display('a#pm#b#pm#c', '{a \pm b \pm c}'));
    }

    public function test_validate_student_response_pm_eq() {
        // This has an expression with more than one +- in an equation in the input.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x +- a = y +- b');
        $state = $el->validate_student_response(['sans1' => 'x +- a = y +- b'],
            $options, 'x #pm# a = y #pm# b', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x#pm#a = y#pm#b', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('\[ {x \pm a}={y \pm b} \]', $state->contentsdisplayed);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
                . '<span class="nolink">\( {x \pm a}={y \pm b} \)</span></span>, which can be typed as '
                . '<code>x+-a = y+-b</code>, would be correct.',
                $el->get_teacher_answer_display('x #pm# a = y #pm# b', '{x \pm a}={y \pm b}'));
    }

    public function test_validate_student_response_without_pm() {
        // This has an expression without +- in the input.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x-3');
        $state = $el->validate_student_response(['sans1' => 'x+ -3'],
                $options, 'x-3', new stack_cas_security(false, '', '', ['tans']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x+ -3', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('\[ x-3 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_with_align_right() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
            'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic-right" value="sin(x)" ' .
            'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
            'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_monospace() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right, monospace');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right input-monospace" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_no_monospace_default_on() {
        $options = new stack_options();
        set_config('inputmonospace', '0,1,2', 'qtype_stack');
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right input-monospace" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_no_monospace_single_default_on() {
        $options = new stack_options();
        set_config('inputmonospace', '0', 'qtype_stack');
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right input-monospace" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_no_monospace_default_off() {
        $options = new stack_options();
        set_config('inputmonospace', '1,2', 'qtype_stack');
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_monospace_true_default_off() {
        $options = new stack_options();
        set_config('inputmonospace', '1,2', 'qtype_stack');
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'monospace:true, align:right');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right input-monospace" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_monospace_false_default_on() {
        $options = new stack_options();
        set_config('inputmonospace', '0,1,2', 'qtype_stack');
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right, monospace:false');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_monospace_false_default_off() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'align:right, monospace:false');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_with_monospace_default_on() {
        $options = new stack_options();
        set_config('inputmonospace', '0', 'qtype_stack');
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'monospace:true, align:right');
        $state = $el->validate_student_response(['sans1' => 'sin(x)'], $options, '3.14', new stack_cas_security());
        // In this case empty responses jump straight to score.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(x)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( x \right) \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \sin \left( x \right) \]</span></span> \), which can be typed as <code>sin(x)</code>,' .
                ' would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" ' .
                'style="width: 13.6em" autocapitalize="none" spellcheck="false" ' .
                'class="algebraic-right input-monospace" value="sin(x)" ' .
                'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator="," />',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_response_noununits() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '9.81*m/s');
        $el->set_parameter('forbidFloats', false);

        $secutity = new stack_cas_security();
        // Set units (from another context).
        $secutity->set_units(true);

        $state = $el->validate_student_response(['sans1' => '9.81*a*m/s'], $options, '3.14', $secutity);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('9.81*a*m/s', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">9.81*a*m/s</span>', $state->contentsdisplayed);
        $this->assertEquals('unknownUnitsCase', $state->note);

        $el->set_parameter('options', 'nounits');
        $state = $el->validate_student_response(['sans1' => '9.81*a*m/s'], $options, '3.14', $secutity);

        $state = $el->validate_student_response(['sans1' => '9.81*a*m/s'], $options, '3.14', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*a*m/s', $state->contentsmodified);
        $this->assertEquals('\[ \frac{9.81\cdot a\cdot \mathrm{m}}{\mathrm{s}} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        // Note the unknown unit is not in roman here.
        $this->assertEquals('\( \left[ a , \mathrm{m} , \mathrm{s} \right]\) ', $state->lvars);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\[ \[ \frac{9.81\cdot a\cdot \mathrm{m}}{\mathrm{s}} \]</span></span> \), ' .
                'which can be typed as <code>9.81*a*m/s</code>, would be correct.',
                $el->get_teacher_answer_display($state->contentsmodified, $state->contentsdisplayed));
    }

    public function test_validate_student_response_subtlesurds() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'ans1', '((-1)+sqrt(11))/10');

        $secutity = new stack_cas_security();

        $state = $el->validate_student_response(['ans1' => '((-1)+sqrt(11))/10'], $options,
                '((-1)+sqrt(11))/10', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('((-1)+sqrt(11))/10', $state->contentsmodified);
        $this->assertEquals('\[ \frac{-1+\sqrt{11}}{10} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);

        $state = $el->validate_student_response(['ans1' => '(-(1-sqrt(11)))/10'], $options,
                '((-1)+sqrt(11))/10', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(-(1-sqrt(11)))/10', $state->contentsmodified);
        $this->assertEquals('\[ \frac{-\left(1-\sqrt{11}\right)}{10} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);

        $state = $el->validate_student_response(['ans1' => '-(1-sqrt(11))/10'], $options,
                '((-1)+sqrt(11))/10', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('-(1-sqrt(11))/10', $state->contentsmodified);
        $this->assertEquals('\[ \frac{-\left(1-\sqrt{11}\right)}{10} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);

        $state = $el->validate_student_response(['ans1' => '-((1-sqrt(11))/10)'], $options,
                '((-1)+sqrt(11))/10', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('\[ -\frac{1-\sqrt{11}}{10} \]', $state->contentsdisplayed);
        $this->assertEquals('-((1-sqrt(11))/10)', $state->contentsmodified);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_subtlefrac() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'ans1', '-a/b');

        $secutity = new stack_cas_security();

        $state = $el->validate_student_response(['ans1' => '-a/b'], $options,
                '-a/b', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('-a/b', $state->contentsmodified);
        $this->assertEquals('\[ \frac{-a}{b} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);

        $state = $el->validate_student_response(['ans1' => '(-a)/b'], $options,
                '-a/b', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(-a)/b', $state->contentsmodified);
        $this->assertEquals('\[ \frac{-a}{b} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);

        $state = $el->validate_student_response(['ans1' => '-(a/b)'], $options,
                '-a/b', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('-(a/b)', $state->contentsmodified);
        $this->assertEquals('\[ -\frac{a}{b} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_subtle_pm() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'ans1', '-a/b');

        $secutity = new stack_cas_security();

        $state = $el->validate_student_response(['ans1' => 'a*x+a*y-b*x-b*y'], $options,
                'a*x+a*y-b*x-b*y', $secutity);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('a*x+a*y-b*x-b*y', $state->contentsmodified);
        $this->assertEquals('\[ a\cdot x+a\cdot y+\left(-b\right)\cdot x+\left(-b\right)\cdot y \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_realsets_sametype_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union(oo(1,2),(3,4))');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => 'oo(1,2)'], $options, '%union(oo(1,2),oo(3,4))',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals($state->contentsmodified, 'oo(1,2)');
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( 1,\, 2\right) \]');
        $this->assertEquals('', $state->note);

        $state = $el->validate_student_response(['sans1' => '{1,2,3}'], $options, '%union(oo(1,2),oo(3,4))',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_realsets_sametype_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '{3,4,5}');
        $el->set_parameter('sameType', true);
        // In this case the student's answer is not a set.
        $state = $el->validate_student_response(['sans1' => 'oc(-1,2)'], $options, '{3,4,5}',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('SA_not_set', $state->note);
        $this->assertEquals($state->contentsmodified, 'oc(-1,2)');
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( -1,\, 2\right] \]');

        // Bump the status of the teacher's answer to a real set, not just a "set" set.
        $state = $el->validate_student_response(['sans1' => 'co(3,4)'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals($state->contentsmodified, 'co(3,4)');
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left[ 3,\, 4\right) \]');
    }

    public function test_validate_student_response_realsets_sametype_err() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union({3,4,5})');
        $el->set_parameter('sameType', true);

        $state = $el->validate_student_response(['sans1' => 'oc(1,2,3)'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('Interval construction must have exactly two arguments, so this must be an error: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(\text{oc(1,2,3)}\)</span></span>.',
                $state->errors);
        $this->assertEquals($state->contentsmodified, 'oc(1,2,3)');
        // Note, the tex function only prints out two of the arguments!
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( 1,\, 2\right] \]');
        $el->set_parameter('showValidation', 1);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val" aria-live="assertive">' .
              '<p>Your last answer was interpreted as follows: ' .
              '<span class="filter_mathjaxloader_equation"><span class="nolink">\[ \left( 1,\, 2\right] \]</span></span>' .
              '</p><input type="hidden" name="sans1_val" value="oc(1,2,3)" />' .
              '<div class="alert alert-danger stackinputerror">' .
              '<span class="filter_mathjaxloader_equation">' .
              'This answer is invalid. Interval construction must have ' .
              'exactly two arguments, so this must be an error: <span class="filter_mathjaxloader_equation">' .
              '<span class="nolink"><span class="nolink">\(\text{oc(1,2,3)}\)</span></span></span>.</span></div></div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));

        $state = $el->validate_student_response(['sans1' => 'oc(3,2)'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('When constructing a real interval the end points must be ordered. ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(\left( 3,\, 2\right]\)</span>' .
                '</span> should be <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\(\left( 2,\, 3\right]\)</span></span>.',
                $state->errors);
        $this->assertEquals($state->contentsmodified, 'oc(3,2)');
        // Note, the tex function only prints out two of the arguments!
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( 3,\, 2\right] \]');

        $state = $el->validate_student_response(['sans1' => 'union(oc(3,2),cc(-1,1))'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('When constructing a real interval the end points must be ordered. ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(\left( 3,\, 2\right]\)</span>' .
                '</span> should be <span class="filter_mathjaxloader_equation"><span class="nolink">' .
                '\(\left( 2,\, 3\right]\)</span></span>.',
                $state->errors);
        $this->assertEquals($state->contentsmodified, '%union(oc(3,2),cc(-1,1))');
        // Note, the tex function only prints out two of the arguments!
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( 3,\, 2\right] \cup \left[ -1,\, 1\right] \]');

        $state = $el->validate_student_response(['sans1' => 'union(oo(minf,-4),x^2)'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('ATAlgEquiv_SA_not_realset', $state->note);
        $this->assertEquals('Your answer should be a subset of the real numbers. ' .
                'This could be a set of numbers, or a collection of intervals. ' .
                'The following should not appear during construction of real sets: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(x^2\)</span></span>',
                $state->errors);
        $this->assertEquals($state->contentsmodified, '%union(oo(minf,-4),x^2)');
        // Note, the tex function only prints out two of the arguments!
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( -\infty ,\, -4\right) \cup x^2 \]');

        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union({3,4,5})');
        $el->set_parameter('sameType', false);

        $state = $el->validate_student_response(['sans1' => 'union(oo(minf,-4),x^2)'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('The following should not appear during construction of real sets: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(x^2\)</span></span>',
                $state->errors);
        $this->assertEquals($state->contentsmodified, '%union(oo(minf,-4),x^2)');
        // Note, the tex function only prints out two of the arguments!
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( -\infty ,\, -4\right) \cup x^2 \]');
    }

    public function test_validate_student_response_realsets_sametype_ok() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union({3,4,5})');
        $el->set_parameter('sameType', true);

        // We don't require intervals to have real numbers in them.
        $state = $el->validate_student_response(['sans1' => 'oc(a,b)'], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($state->contentsmodified, 'oc(a,b)');
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left( a,\, b\right] \]');
    }

    public function test_validate_student_response_root() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^(1/n)');
        $el->set_parameter('sameType', true);

        // We don't require intervals to have real numbers in them.
        $state = $el->validate_student_response(['sans1' => 'root(x,n)'], $options, 'x^(1/n)',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($state->contentsmodified, '\'root(x,n)');
        $this->assertEquals($state->contentsdisplayed,
            '\[ \sqrt[n]{x} \]');
    }

    public function test_validate_student_response_tex() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '{}');

        $state = $el->validate_student_response(['sans1' => '\[x^2\]'], $options, '{}',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('illegalcaschars', $state->note);
        $this->assertEquals('The characters @ and \ are not allowed in CAS input.', $state->errors);
        $this->assertEquals($state->contentsmodified, '');
        // This appears to the student to display correctly, since the TeX is picked up by MathJax.
        $this->assertEquals($state->contentsdisplayed,
                '<span class="stacksyntaxexample">\&#8203;[x^2\&#8203;]</span>');
    }

    public function test_validate_student_response_xss_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union({3,4,5})');

        $sa = '$$ \unicode{<script>eval(atob("ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pbm5lckhU' .
                'TUwgPSAiQSIucmVwZWF0KDY2Nik"))</script><iframe src="https://www.youtube.com/embed/IB3d1Ut' .
                'hDrk?autoplay=1&amp;loop=1;controls=0"<https://www.youtube.com/embed/IB3d1UthDrk?autoplay' .
                '=1&amp;loop=1;controls=0> allow="accelerometer; autoplay; encrypted-media; gyroscope; ' .
                'picture-in-picture" allowfullscreen="" width="0" height="0" frameborder="0"></iframe>}$$';
        $ta = '<span class="stacksyntaxexample">$&#8203;$ \unicode{&lt;script&gt;eval(atob(&quot;ZG9jdW1l' .
                'bnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pbm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik&quot;))&lt' .
                ';/script&gt;&lt;iframe src&#0;&#61;&quot;https://www.youtube.com/embed/IB3d1UthDrk?autopla' .
                'y=1&amp;amp;loop=1;con&#0;trols&#0;&#61;0&quot;&lt;https://www.youtube.com/embed/IB3d1UthD' .
                'rk?autoplay=1&amp;amp;loop=1;con&#0;trols&#0;&#61;0&gt; allow=&quot;accelerometer; autopla' .
                'y; encrypted-media; gyroscope; picture-in-picture&quot; allowfullscreen=&quot;&quot; width' .
                '=&quot;0&quot; height=&quot;0&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;}$&#8203;$' .
                '</span>';
        // We don't require intervals to have real numbers in them.
        $state = $el->validate_student_response(['sans1' => $sa], $options, '%union({3,4,5})',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('spaces | forbiddenChar_parserError', $state->note);
        $this->assertEquals('CAS commands may not contain the following characters: ;.', $state->errors);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals($ta, $state->contentsdisplayed);
    }

    public function test_validate_student_response_xss_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union({3,4,5})');

        $sa = '1+"\\unicode{<script>eval(atob(\"ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pbm5lckhU' .
                'TUwgPSAiQSIucmVwZWF0KDY2Nik\"))</script><iframe src=\"https://www.youtube.com/embed/IB3d1Ut' .
                'hDrk?autoplay=1\" allow=\"autoplay\" allowfullscreen=\"\" width=\"0\" height=\"0\" framebor' .
                'der=\"0\"></iframe>}"';
        $ta = '1+"unicode{&lt;&#8203;script>eval(atob(\"ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5p' .
              'bm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik\"))&lt;&#8203;/script&gt;&lt;&#8203;iframe src&#0;&#61;\"' .
              'https://www.youtube.com/embed/IB3d1UthDrk?autoplay=1\" allow=\"autoplay\" allowfullscreen=\"\"' .
              ' width=\"0\" height=\"0\" frameborder=\"0\">&lt;&#8203;/iframe&gt;}"';
        $ua = '\[ 1+\text{unicode{&lt;script&gt;eval(atob(&quot;ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAi' .
              'KVswXS5pbm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik&quot;))&lt;/script&gt;&lt;iframe src&#0;&#61;&quo' .
              't;https://www.youtube.com/embed/IB3d1UthDrk?autoplay=1&quot; allow=&quot;autoplay&quot; allow' .
              'fullscreen=&quot;&quot; width=&quot;0&quot; height=&quot;0&quot; frameborder=&quot;0&quot;&gt' .
              ';&lt;/iframe&gt;}} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, '1+x^2',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($ta, $state->contentsmodified);
        $this->assertEquals($ua, $state->contentsdisplayed);
    }

    public function test_validate_student_response_xss_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '%union({3,4,5})');

        $sa = '1+"\\unicode{<script>eval(atob(\"ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pbm5lckhUT' .
                'UwgPSAiQSIucmVwZWF0KDY2Nik\"))</script><iframesrc=\"https://www.youtube.com/embed/IB3d1UthDr' .
                'k&quest;autoplay=1\"allow=\"autoplay\" allowfullscreen=\"\" width=\"0\" height=\"0\" framebo' .
                'rder=\"0\"></iframe>}"';
        $ta = '1+"unicode{&lt;&#8203;script>eval(atob(\"ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pb' .
                'm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik\"))&lt;&#8203;/script&gt;&lt;&#8203;iframesrc&#0;&#61;\"https://w' .
                'ww.youtube.com/embed/IB3d1UthDrk&quest;autoplay=1\"allow=\"autoplay\" allowfullscreen=\"\" w' .
                'idth=\"0\" height=\"0\" frameborder=\"0\">&lt;&#8203;/iframe&gt;}"';
        $ua = '\[ 1+\text{unicode{&lt;script&gt;eval(atob(&quot;ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVsw' .
              'XS5pbm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik&quot;))&lt;/script&gt;&lt;iframesrc&#0;&#61;&quot;https://ww' .
              'w.youtube.com/embed/IB3d1UthDrk&quest;autoplay=1&quot;allow=&quot;autoplay&quot; allowfullscreen=&qu' .
              'ot;&quot; width=&quot;0&quot; height=&quot;0&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;}} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, '1+x^2',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($ta, $state->contentsmodified);
        $this->assertEquals($ua, $state->contentsdisplayed);

        $sa = '1+"\\unicode{<script>eval(atob(\"ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pbm5lckhUT' .
            'UwgPSAiQSIucmVwZWF0KDY2Nik\"))</script><iframe src=\"https://www.youtube.com/embed/IB3d1UthDr' .
            'k&quest;autoplay=1\"allow=\"autoplay\" allowfullscreen=\"\" width=\"0\" height=\"0\" framebo' .
            'rder=\"0\"></iframe>}"';
        $ta = '1+"unicode{&lt;&#8203;script>eval(atob(\"ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS5pb' .
            'm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik\"))&lt;&#8203;/script&gt;&lt;&#8203;iframe src&#0;&#61;\"https://w' .
            'ww.youtube.com/embed/IB3d1UthDrk&quest;autoplay=1\"allow=\"autoplay\" allowfullscreen=\"\" w' .
            'idth=\"0\" height=\"0\" frameborder=\"0\">&lt;&#8203;/iframe&gt;}"';
        $ua = '\[ 1+\text{unicode{&lt;script&gt;eval(atob(&quot;ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInAiKVswXS' .
              '5pbm5lckhUTUwgPSAiQSIucmVwZWF0KDY2Nik&quot;))&lt;/script&gt;&lt;iframe src&#0;&#61;&quot;https://ww' .
              'w.youtube.com/embed/IB3d1UthDrk&quest;autoplay=1&quot;allow=&quot;autoplay&quot; allowfullscreen=&q' .
              'uot;&quot; width=&quot;0&quot; height=&quot;0&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;}} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, '1+x^2',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($ta, $state->contentsmodified);
        $this->assertEquals($ua, $state->contentsdisplayed);
    }

    public function test_validate_student_response_xss_4() {
        $options = new stack_options();
        $ta = '"Hello world"';
        $el = stack_input_factory::make('algebraic', 'sans1', $ta);

        $sa = '"<script>RandomPresumablyBadStuff</script>"';
        $cm = '"&lt;&#8203;script>RandomPresumablyBadStuff&lt;&#8203;/script&gt;"';
        $cd = '\[ \text{&lt;script&gt;RandomPresumablyBadStuff&lt;/script&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<div>WhoKnowsWhatButStudentsDontNeedHTMLInStrings</div>"';
        $cm = '"&lt;&#8203;div>WhoKnowsWhatButStudentsDontNeedHTMLInStrings&lt;&#8203;/div&gt;"';
        $cd = '\[ \text{&lt;div&gt;WhoKnowsWhatButStudentsDontNeedHTMLInStrings&lt;/div&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<!-->MostlyToPreventAnnoyingInput<-->"';
        $cm = '"&lt;!--&gt;MostlyToPreventAnnoyingInput<--&gt;"';
        $cd = '\[ \text{&lt;!--&gt;MostlyToPreventAnnoyingInput&lt;--&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<div onclick=\'dosuchandsuch\'></div>"';
        $cm = '"&lt;&#8203;div on&#0;click&#0;&#61;\'dosuchandsuch\'>&lt;&#8203;/div&gt;"';
        $cd = '\[ \text{&lt;div on&#0;click&#0;&#61;&apos;dosuchandsuch&apos;&gt;&lt;/div&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<div onmousemove     =\'dosuchandsuch\'></div>"';
        $cm = '"&lt;&#8203;div on&#0;mousemove     &#0;&#61;\'dosuchandsuch\'>&lt;&#8203;/div&gt;"';
        $cd = '\[ \text{&lt;div on&#0;mousemove &#0;&#61;&apos;dosuchandsuch&apos;&gt;&lt;/div&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<img src=\'picture\' />"';
        $cm = '"<img src&#0;&#61;\'picture\' /&gt;"';
        $cd = '\[ \text{&lt;img src&#0;&#61;&apos;picture&apos; /&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);

        $sa = '"<a href=\'link\' />I do not think so.</a>"';
        $cm = '"<a href&#0;&#61;\'link\' /&gt;I do not think so.&lt;/a>"';
        $cd = '\[ \text{&lt;a href&#0;&#61;&apos;link&apos; /&gt;I do not think so.&lt;/a&gt;} \]';
        $state = $el->validate_student_response(['sans1' => $sa], $options, $ta,
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals($cm, $state->contentsmodified);
        $this->assertEquals($cd, $state->contentsdisplayed);
    }

    public function test_validate_hideanswer() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'state', '[x^2]');
        $el->set_parameter('options', 'hideanswer');
        $state = $el->validate_student_response(['state' => '[x^3]'], $options, '[x^2]',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('[x^3]', $state->contentsmodified);
        $this->assertEquals('\[ \left[ x^3 \right] \]', $state->contentsdisplayed);
        $this->assertEquals('', $el->get_teacher_answer_display("[SOME JSON]", "\[ \text{[SOME MORE JSON]} \]"));
    }

    public function test_validate_student_response_ntuple() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'ntuple(1,-1)');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(['sans1' => '(1,2)'], $options, 'ntuple(1,-1)',
                new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::VALID);
        $this->assertEquals($state->contentsmodified, 'ntuple(1,2)');
        $this->assertEquals($state->contentsdisplayed,
                '\[ \left(1, 2\right) \]');
        $this->assertEquals('', $state->note);

        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
                . '<span class="nolink">\( \left(1, -1\right) \)</span></span>, which can be typed as '
                . '<code>(1,-1)</code>, would be correct.',
            $el->get_teacher_answer_display('ntuple(1,-1)', '\left(1, -1\right)'));
    }

    public function test_validate_student_response_ntuple_forbid() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '[1,2]');
        $el->set_parameter('forbidWords', 'ntuple');
        $state = $el->validate_student_response(['sans1' => '(1,2)'], $options, '[1,2]',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals($state->contentsmodified, 'ntuple(1,2)');
        $this->assertEquals($state->contentsdisplayed,
            '<span class="stacksyntaxexample">(1,2)</span>');
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('Coordinates are not permitted in this input.', $state->errors);
    }

    public function test_validate_student_response_no_dot_dot() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '3.14*2.78');
        $el->set_parameter('forbidFloats', false);
        $state = $el->validate_student_response(['sans1' => '3.14.2.78'], $options, '3.14*2.78',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals($state->status, stack_input::INVALID);
        $this->assertEquals('3.14 . 2.78', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">3.14.2.78</span>',
            $state->contentsdisplayed);
        $this->assertEquals('MatrixMultWithFloat', $state->note);
        $this->assertEquals('Using matrix multiplication "." with scalar floats is forbidden, ' .
            'use normal multiplication "*" instead for the same result. 3.14 . 2.78', $state->errors);
    }

    public function test_validate_consolidatesubscripts() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'state', 'M_1');
        $el->set_parameter('options', 'consolidatesubscripts');
        $state = $el->validate_student_response(['state' => 'M_1'], $options, 'M_1',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('M1', $state->contentsmodified);
        $this->assertEquals('\[ M_{1} \]', $state->contentsdisplayed);
    }

    public function test_validate_checkvars() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'ans2', 'a+b+c');

        $el->set_parameter('options', 'checkvars:1');
        $state = $el->validate_student_response(['ans2' => 'x^2+z'], $options, 'a+b+c',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2+z', $state->contentsmodified);
        $this->assertEquals('\[ x^2+z \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
        $this->assertEquals('These variables are not needed: x, z.', $state->errors);

        $el->set_parameter('options', 'checkvars:2');
        $state = $el->validate_student_response(['ans2' => 'x^2+z'], $options, 'a+b+c',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2+z', $state->contentsmodified);
        $this->assertEquals('\[ x^2+z \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
        $this->assertEquals('These variables are missing: a, b, c.', $state->errors);

        $el->set_parameter('options', 'checkvars:3');
        $state = $el->validate_student_response(['ans2' => 'x^2+z'], $options, 'a+b+c',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2+z', $state->contentsmodified);
        $this->assertEquals('\[ x^2+z \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
        $this->assertEquals('These variables are not needed: x, z. ' .
            'These variables are missing: a, b, c.', $state->errors);
    }

    public function test_invalid_validator_name() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'state', 'x^2');
        $el->set_parameter('options', 'validator:bad%functionname');
        $el->validate_extra_options();

        $this->assertEquals([
            'The name of a validator function must be a valid maxima identifier ' .
            'in the form of letters a-zA-Z optionally followed by digits.',
        ],
            $el->get_errors());
    }

    public function test_missing_validator_() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'state', 'x^2');
        $el->set_parameter('options', 'validator:missingfunction');
        $el->validate_extra_options();
        $this->assertEquals([], $el->get_errors());

        $state = $el->validate_student_response(['state' => 'x^2'], $options, 'x^2',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2', $state->contentsmodified);
        $this->assertEquals('\[ x^2 \]', $state->contentsdisplayed);
        $this->assertEquals('The optional validator threw internal Maxima errors.',
            $state->errors);
    }

    public function test_validate_student_response_conjugate() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*conjugate(x)');
        $state = $el->validate_student_response(['sans1' => 'conjugate(x)'], $options, 'conjugate(x)',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('conjugate(x)', $state->contentsmodified);
        $displayed = '\[ x^\star \]';
        if ($this->adapt_to_new_maxima('5.47.0')) {
            $displayed = '\[ x^{\ast} \]';
        }
        $this->assertEquals($displayed, $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);

        $state = $el->validate_student_response(['sans1' => 'conjugate(x)^2'], $options, 'conjugate(x)^2',
            new stack_cas_security(false, '', '', ['ta']));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('conjugate(x)^2', $state->contentsmodified);
        $displayed = '\[ {x^\star}^2 \]';
        if ($this->adapt_to_new_maxima('5.47.0')) {
            // Personally I (CJS) prefer these brackets, so I'm going to keep them.
            $displayed = '\[ {\left(x^{\ast}\right)}^2 \]';
        }
        $this->assertEquals($displayed, $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);
    }

    public function test_decimal_output_0() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $el = stack_input_factory::make('algebraic', 'state', '{3.1415,2.71}', $options);
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['state' => '{3.1415,2.71}'], $options, '{3.1415,2.71}',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        // With a strict interpretation we would have to change the , to a . In this case it results in ...
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">{3.1415,2.71}</span>', $state->contentsdisplayed);
        $this->assertEquals('You have used the full stop <code>.</code>, but you must use the comma <code>,</code> as a ' .
            'decimal separator!',
            $state->errors);
        $this->assertEquals('<input type="text" name="state" id="state" size="16.5" style="width: 13.6em" '
            .'autocapitalize="none" spellcheck="false" class="algebraic" value="{3.1415,2.71}" '
            .'data-stack-input-type="algebraic" data-stack-input-decimal-separator="," '
            .'data-stack-input-list-separator=";" />',
            $el->render($state, 'state', false, null));
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
            . '<span class="nolink">\( \{3{,}1415 ; 2{,}7100 \right \} \)</span></span>, which can be typed as '
            . '<code>{3,1415;2,71}</code>, would be correct.',
            $el->get_teacher_answer_display('{3.1415,2.71}', '\{3{,}1415 ; 2{,}7100 \right \}'));
    }

    public function test_decimal_output_1() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $el = stack_input_factory::make('algebraic', 'state', '{3.1415,2.71}', $options);
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['state' => '{3.1415;2.71}'], $options, '{3.1415,2.71}',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        // With a strict interpretation we have to change the , to a .  But actually we don't change it...
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">{3.1415;2.71}</span>', $state->contentsdisplayed);
        $this->assertEquals('You have used the full stop <code>.</code>, but you must use the comma ' .
              '<code>,</code> as a decimal separator!', $state->errors);
        $this->assertEquals('The answer <span class="filter_mathjaxloader_equation">'
            . '<span class="nolink">\( \{3{,}1415 ; 2{,}71 \right \} \)</span></span>, which can be typed as '
            . '<code>{3,1415;2,71}</code>, would be correct.',
            $el->get_teacher_answer_display('{3.1415,2.71}', '\{3{,}1415 ; 2{,}71 \right \}'));
    }

    public function test_decimal_output_2() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $el = stack_input_factory::make('algebraic', 'state', '{3.1415,2.71}', $options);
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['state' => '{3,1415;2,71}'], $options, '{3.1415,2.71}',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        // With a strict interpretation we have to change the , to a .
        $this->assertEquals('{3.1415,2.71}', $state->contentsmodified);
        $this->assertEquals('\[ \left \{3{,}1415 ; 2{,}71 \right \} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_decimal_output_3() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $el = stack_input_factory::make('algebraic', 'state', '{3.14,2.7100}', $options);
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['state' => '{3,14;2,7100}'], $options, '{3.1415,2.7100}',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        // With a strict interpretation we have to change the , to a .
        $this->assertEquals('{3.14,2.7100}', $state->contentsmodified);
        $this->assertEquals('\[ \left \{3{,}14 ; 2{,}7100 \right \} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_decimal_output_4() {
        $options = new stack_options();
        $options->set_option('decimals', '.');
        $options->set_option('scientificnotation', 'E');
        $el = stack_input_factory::make('algebraic', 'state', '3.14000E-10', $options);
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['state' => '3.14000E-10'], $options, '3.14000E-10',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3.14000E-10', $state->contentsmodified);
        $this->assertEquals('\[ 3.14000E{-10} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_decimal_output_5() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $options->set_option('scientificnotation', 'E');
        $el = stack_input_factory::make('algebraic', 'state', '3.14000E-10', $options);
        $el->set_parameter('forbidFloats', false);

        $state = $el->validate_student_response(['state' => '3,14000E-10'], $options, '3.14000E-10',
            new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3.14000E-10', $state->contentsmodified);
        $this->assertEquals('\[ 3{,}14000E{-10} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_decimal_output_matrix_1() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        // Teacher must use correct syntax.
        $el = stack_input_factory::make('algebraic', 'state', 'matrix([3.1415,2.71])', $options);
        $el->set_parameter('forbidFloats', false);

        // Student uses commas and semicolons.
        $state = $el->validate_student_response(['state' => 'matrix([3,1415;2,71])'], $options,
            'matrix([3.1415,2.71])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([3.1415,2.71])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} 3{,}1415 & 2{,}71 \end{array}\right] \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);

        // Student uses commas and semicolons for separation of items in functions.
        $state = $el->validate_student_response(['state' => 'matrix([3,1415];[2,71])'], $options,
            'matrix([3.1415],[2.71])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([3.1415],[2.71])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{c} 3{,}1415 \\\\ 2{,}71 \end{array}\right] \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_decimal_output_matrix_2() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        // Teacher must use correct syntax.
        $el = stack_input_factory::make('algebraic', 'state', 'matrix([3.1415,2.71])', $options);
        $el->set_parameter('forbidFloats', false);

        // Student uses commas and semicolons.
        // But only for numbers, not for list separators in function arguments or matrix multiplication!
        $state = $el->validate_student_response(['state' => 'matrix([3,1415;2,71]).matrix([1];[2])'], $options,
            'matrix([3.1415,2.71])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([3,1415;2,71]).matrix([1];[2])</span>',
            $state->contentsdisplayed);
        $this->assertEquals('forbiddenCharDecimal', $state->note);
    }

    public function test_validate__string() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '"A random string"');
        $state = $el->validate_student_response(['sans1' => '"Lots of stuff:!$%^&*?@;"'],
            $options, '"A random string"', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Lots of stuff:!$%^&*?@;"', $state->contentsmodified);
        $this->assertEquals('\[ \text{Lots of stuff:!\$\%^&*?@;} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_var_chars_unicode_superscript() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(['sans1' => 'x²'], $options, 'x^2',
            new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        // The rest needs to be updated once we know what the expected result is.
        $this->assertEquals('forbiddenChar', $state->note);
        $this->assertEquals('CAS commands may not contain the following characters: ².',
            $state->errors);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">x&sup2;</span>', $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }
}
