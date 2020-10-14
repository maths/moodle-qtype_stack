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

/**
 * @copyright 2018 The University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * @group qtype_stack
 */
class stack_numerical_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('numerical', 'ans1', '2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="numerical" value="" />',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_zero() {
        $el = stack_input_factory::make('numerical', 'ans1', '0');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="numerical" value="0" />',
                $el->render(new stack_input_state(stack_input::VALID, array('0'), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $state = $el->validate_student_response(array('sans1' => '3.14'), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('\[ 3.14 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_pi() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $state = $el->validate_student_response(array('sans1' => 'pi/2'), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('\[ \frac{\pi}{2} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_scientific() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $state = $el->validate_student_response(array('sans1' => '2.34e6'), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('\[ 2.34E+6 \]', strtoupper($state->contentsdisplayed));
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_div_zero() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $state = $el->validate_student_response(array('sans1' => '1/0'), $options, '3.14*x^2', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Division by zero. This input expects a number.', $state->errors);
    }

    public function test_validate_student_response_invalid_variables() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $state = $el->validate_student_response(array('sans1' => '3.14*x^2'), $options, '3.14*x^2', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('This input expects a number, and so may not contain variables.', $state->errors);
    }

    public function test_validate_student_response_valid_functions() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        // Technically this is a number, so we accpet it.  You need to forbid things if you want this evaluated.
        $state = $el->validate_student_response(array('sans1' => 'sin(pi/2)'), $options, '3.14*x^2', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_invalid_functions() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $el->set_parameter('forbidWords', 'sin,cos,tan');
        $state = $el->validate_student_response(array('sans1' => 'sin(pi/2)'), $options, '3.14*x^2', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">sin</span>.' .
            ' This input expects a number.',
                $state->errors);
    }

    public function test_validate_student_response_with_floatnum_e() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $el->set_parameter('options', 'floatnum');
        $state = $el->validate_student_response(array('sans1' => "314e-5"), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('314e-5', $state->contentsmodified);
        $this->assertEquals('\[ 3.14E-3 \]', strtoupper($state->contentsdisplayed));
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_with_floatnum() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14');
        $el->set_parameter('options', 'floatnum');
        $state = $el->validate_student_response(array('sans1' => "3.14"), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3.14', $state->contentsmodified);
        $this->assertEquals('\[ 3.14 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_without_floatnum() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '0.33333');
        $el->set_parameter('options', 'floatnum');
        $state = $el->validate_student_response(array('sans1' => "1/3"), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('1/3', $state->contentsmodified);
        $this->assertEquals('\[ \frac{1}{3} \]', $state->contentsdisplayed);
        $this->assertEquals('This input expects a floating point number.', $state->errors);
    }

    public function test_validate_student_response_with_rationalnum() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1/2');
        $el->set_parameter('options', 'rationalnum');
        $state = $el->validate_student_response(array('sans1' => "3/7"), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3/7', $state->contentsmodified);
        $this->assertEquals('\[ \frac{3}{7} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_with_rationalnum_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1/2');
        $el->set_parameter('options', 'rationalnum');
        $state = $el->validate_student_response(array('sans1' => "1 3/7"), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('1*3/7', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">1 3/7</span>', $state->contentsdisplayed);
        $this->assertEquals('Illegal spaces found in expression '.
                '<span class="stacksyntaxexample">1<span class="stacksyntaxexamplehighlight">_</span>3/7</span>.' .
                ' This input expects a number.', $state->errors);
    }

    public function test_validate_student_response_without_rationalized() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', 'sqrt(2)/2');
        $el->set_parameter('options', 'rationalized');
        $state = $el->validate_student_response(array('sans1' => "1/sqrt(2)"), $options, 'sqrt(2)/2', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('1/sqrt(2)', $state->contentsmodified);
        $this->assertEquals('\[ \frac{1}{\sqrt{2}} \]', $state->contentsdisplayed);
        $this->assertEquals('You must clear the following from the denominator of your fraction: '.
                '<span class="filter_mathjaxloader_equation"><span class="nolink">'.
                '\[ \left[ \sqrt{2} \right] \]</span></span>', $state->errors);
    }

    public function test_validate_student_response_with_rationalized() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1/2');
        $el->set_parameter('options', 'rationalized');
        $state = $el->validate_student_response(array('sans1' => "3/7"), $options, '3.14', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('3/7', $state->contentsmodified);
        $this->assertEquals('\[ \frac{3}{7} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_lowest_terms_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '12/4');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(array('sans1' => '12/4'), $options, '3',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
    }

    public function test_validate_student_lowest_terms_2() {
        // This test checks the unary minus is *not* in lowest terms.
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '-10/-1');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(array('sans1' => '-10/-1'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
    }

    public function test_validate_student_respect_trainling_zeros() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '0.33');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(array('sans1' => '0.333000'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('0.333000', $state->contentsmodified);
        $this->assertEquals('\[ 0.333000 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_mindp() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:4');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at least <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 4 \)</span></span> decimal places.', $state->errors);
    }

    public function test_validate_student_mindp_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:4');
        $state = $el->validate_student_response(array('sans1' => '3.1416'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_maxdp() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'maxdp:4');
        $state = $el->validate_student_response(array('sans1' => '3.14159'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at most <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 4 \)</span></span> decimal places.', $state->errors);
    }

    public function test_validate_student_maxdp_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'maxdp:4');
        $state = $el->validate_student_response(array('sans1' => '3.1416'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_mindp_maxdp_err() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:4, maxdp:3');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals('<div class="error"><p>The input has generated the following runtime error which prevents ' .
                'you from answering. Please contact your teacher.</p><p>The required minimum number of numerical places ' .
                'exceeds the maximum number of places!</p></div>',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_mindp_maxdp_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:3, maxdp:4');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_mindp_maxdp_min() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:3, maxdp:4');
        $state = $el->validate_student_response(array('sans1' => '3.14'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at least <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> decimal places.', $state->errors);
    }

    public function test_validate_student_mindp_maxdp_max() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:3, maxdp:4');
        $state = $el->validate_student_response(array('sans1' => '3.14159'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at most <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 4 \)</span></span> decimal places.', $state->errors);
    }

    public function test_validate_student_minsf() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:4');
        $state = $el->validate_student_response(array('sans1' => '3.14'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at least <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 4 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:4');
        $state = $el->validate_student_response(array('sans1' => '3.1416'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_masfp() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'maxsf:4');
        $state = $el->validate_student_response(array('sans1' => '3.14159'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at most <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 4 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_maxsf_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'maxsf:4');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_err() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:4, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals('<div class="error"><p>The input has generated the following runtime error which prevents ' .
                'you from answering. Please contact your teacher.</p><p>The required minimum number of numerical places ' .
                'exceeds the maximum number of places!</p></div>',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_minsf_maxsf_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:4');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_min() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:4');
        $state = $el->validate_student_response(array('sans1' => '10'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at least <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_max() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:4');
        $state = $el->validate_student_response(array('sans1' => '3.14159'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply at most <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 4 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_maxdp_err() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:4, maxdp:3');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals('<div class="error"><p>The input has generated the following runtime error which prevents ' .
                'you from answering. Please contact your teacher.</p><p>Do not specify requirements for both decimal ' .
                'places and significant figures in the same input.</p></div>',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_minsf_int_err() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:x, maxsf:7');
        $state = $el->validate_student_response(array('sans1' => '3.141'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals('<div class="error"><p>The input has generated the following runtime error which prevents ' .
                'you from answering. Please contact your teacher.</p><p>The value of the option <code>minsf</code> ' .
                'should be an integer, but in fact it is <code>x</code>.</p></div>',
                $el->render($state, 'stack1__ans1', false, null));
    }

    public function test_validate_student_minsf_maxsf_equal_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '3.14'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_equal_low() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '3.1'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply exactly <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_equal_high() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '3.114'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply exactly <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_equal_ambiguous() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '1000'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_mindp_maxdp_equal_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:3, maxdp:3');
        $state = $el->validate_student_response(array('sans1' => '3.142'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_mindp_maxdp_equal_low() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:3, maxdp:3');
        $state = $el->validate_student_response(array('sans1' => '3.1'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply exactly <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> decimal places.', $state->errors);
    }

    public function test_validate_student_mindp_maxdp_equal_high() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $el->set_parameter('options', 'mindp:3, maxdp:3');
        $state = $el->validate_student_response(array('sans1' => '3.1416'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply exactly <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> decimal places.', $state->errors);
    }

    public function test_validate_student_mindp_zero() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        // With mindp:0 students can enter an integer.
        $el->set_parameter('options', 'mindp:0, maxdp:3');
        $state = $el->validate_student_response(array('sans1' => '3'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_render_syntaxhint() {
        $el = stack_input_factory::make('numerical', 'sans1', '[a, b, c]');
        $el->set_parameter('syntaxHint', '?/?');
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" size="16.5" '
                .'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="numerical" value="?/?" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'stack1__sans1', false, null));
    }

    public function test_validate_student_letters_only() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '3.14159');
        $state = $el->validate_student_response(array('sans1' => 'letters'), $options, '10',
                new stack_cas_security(false, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenVariable', $state->note);
        $this->assertEquals('Forbidden variable or constant: <span class="stacksyntaxexample">letters</span>. ' .
                'This input expects a number.',
            $state->errors);
    }

    public function test_validate_student_response_10x() {

        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '23.2*10^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '23.2x10^2'), $options, '23.2*10^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | Illegal_x10', $state->note);
        $this->assertEquals('23.2*x10^2', $state->contentsmodified);
        $this->assertEquals('Your answer appears to use the character "x" as a multiplication sign.  ' .
                'Please use <code>*</code> for multiplication.', $state->errors);
    }

    public function test_validate_student_response_with_intnum_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1729');
        $el->set_parameter('options', 'intnum');
        $state = $el->validate_student_response(array('sans1' => "6"), $options, '1729', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('6', $state->contentsmodified);
        $this->assertEquals('\[ 6 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_with_intnum_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1729');
        $el->set_parameter('options', 'intnum');
        $state = $el->validate_student_response(array('sans1' => "-26"), $options, '1729', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('-26', $state->contentsmodified);
        $this->assertEquals('\[ -26 \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_with_intnum_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1729');
        $el->set_parameter('options', 'intnum');
        $state = $el->validate_student_response(array('sans1' => "1-26"), $options, '1729', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('1-26', $state->contentsmodified);
        $this->assertEquals('\[ 1-26 \]', $state->contentsdisplayed);
        $this->assertEquals('This input expects an explicit integer.', $state->errors);
    }

    public function test_validate_student_response_with_intnum_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1729');
        $el->set_parameter('options', 'intnum');
        $state = $el->validate_student_response(array('sans1' => "2+3"), $options, '1729', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('2+3', $state->contentsmodified);
        $this->assertEquals('\[ 2+3 \]', $state->contentsdisplayed);
        $this->assertEquals('This input expects an explicit integer.', $state->errors);
    }

    public function test_validate_student_response_with_intnum_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1729');
        $el->set_parameter('options', 'intnum');
        $state = $el->validate_student_response(array('sans1' => "sqrt(16)"), $options, '1729', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('sqrt(16)', $state->contentsmodified);
        $this->assertEquals('\[ \sqrt{16} \]', $state->contentsdisplayed);
        $this->assertEquals('This input expects an explicit integer.', $state->errors);
    }

    public function test_validate_student_response_with_intnum_6() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'sans1', '1729');
        $el->set_parameter('options', 'intnum');
        $state = $el->validate_student_response(array('sans1' => "sin(pi/2)"), $options, '1729', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('sin(pi/2)', $state->contentsmodified);
        $this->assertEquals('\[ \sin \left( \frac{\pi}{2} \right) \]', $state->contentsdisplayed);
        $this->assertEquals('This input expects an explicit integer.', $state->errors);
    }

    public function test_validate_hideanswer() {
        $options = new stack_options();
        $el = stack_input_factory::make('numerical', 'state', '123');
        $el->set_parameter('options', 'hideanswer');
        $state = $el->validate_student_response(array('state' => '124'), $options, '123',
                new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('124', $state->contentsmodified);
        $this->assertEquals('\[ 124 \]', $state->contentsdisplayed);
        $this->assertEquals('', $el->get_teacher_answer_display("[SOME JSON]", "\[ \mbox{[SOME MORE JSON]} \]"));
    }
}
