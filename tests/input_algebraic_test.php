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

// Unit tests for stack_algebra_input.
//
// @copyright  2012 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_algebra_input_test extends qtype_stack_testcase {
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
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" '
                .'size="16.5" style="width: 13.6em" value="" />',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_zero() {
        $el = stack_input_factory::make('algebraic', 'ans1', '0');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" '
                .'size="16.5" style="width: 13.6em" value="0" />',
                $el->render(new stack_input_state(stack_input::VALID, array('0'), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_pre_filled() {
        $el = stack_input_factory::make('algebraic', 'test', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" '
                .'size="16.5" style="width: 13.6em" value="x+y" />',
                $el->render(new stack_input_state(stack_input::VALID, array('x+y'), '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_pre_filled_nasty_input() {
        $el = stack_input_factory::make('algebraic', 'test', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" '
                .'size="16.5" style="width: 13.6em" value="x&lt;y" />',
                $el->render(new stack_input_state(stack_input::VALID, array('x<y'), '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_max_length() {
        $el = stack_input_factory::make('algebraic', 'test', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" '
                .'size="16.5" style="width: 13.6em" value="x+y" />',
                $el->render(new stack_input_state(stack_input::VALID, array('x+y'), '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_disabled() {
        $el = stack_input_factory::make('algebraic', 'input', 'x^2');
        $this->assertEquals(
                '<input type="text" name="stack1__input" id="stack1__input" '
                .'size="16.5" style="width: 13.6em" value="x+1" readonly="readonly" />',
                $el->render(new stack_input_state(stack_input::VALID, array('x+1'), '', '', '', '', ''),
                        'stack1__input', true, null));
    }

    public function test_render_different_size() {
        $el = stack_input_factory::make('algebraic', 'input', 'x^2');
        $el->set_parameter('boxWidth', 30);
        $this->assertEquals('<input type="text" name="stack1__input" id="stack1__input" '
                .'size="33" style="width: 27.1em" value="x+1" />',
                $el->render(new stack_input_state(stack_input::VALID, array('x+1'), '', '', '', '', ''),
                        'stack1__input', false, null));
    }

    public function test_render_syntaxhint() {
        $el = stack_input_factory::make('algebraic', 'sans1', '[a, b, c]');
        $el->set_parameter('syntaxHint', '[?, ?, ?]');
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" '
                .'size="16.5" style="width: 13.6em" value="[?, ?, ?]" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'stack1__sans1', false, null));
    }

    public function test_render_placeholder() {
        $el = stack_input_factory::make('algebraic', 'sans1', '[a, b, c]');
        $el->set_parameter('syntaxHint', 'Remove me');
        $el->set_parameter('syntaxAttribute', 1);
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" '
                .'size="16.5" style="width: 13.6em" placeholder="Remove me" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'stack1__sans1', false, null));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $state = $el->validate_student_response(array('sans1' => 'x^2'), $options, 'x^2/(1+x^2)', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">'
          . '<span class="nolink">\( \frac{x^2}{1+x^2} \)</span></span>, which can be typed in as follows: '
          . '<code>x^2/(1+x^2)</code>', $el->get_teacher_answer_display('x^2/(1+x^2)', '\frac{x^2}{1+x^2}'));
    }

    public function test_validate_student_response_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $state = $el->validate_student_response(array('sans1' => '2x(1+x^2)'), $options, 'x^2/(1+x^2)', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
    }

    public function test_validate_student_response_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2x', 'sans1_val' => '2x'), $options, 'x^2/(1+x^2)', array());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2x(1+x^2)'), $options, 'x^2/(1+x^2)', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2x(1+x^2)+tans'), $options, 'x^2/(1+x^2)', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownFunction', $state->note);
    }

    public function test_validate_student_response_6() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2*x/(1+x^2)+sillyname(x)'),
                $options, 'x^2/(1+x^2)', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownFunction', $state->note);
    }

    public function test_validate_student_response_7() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2/(1+x^2)');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '2x(1+x^2)+tans'), $options, 'x^2/(1+x^2)', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownFunction | missing_stars', $state->note);
    }

    public function test_validate_student_response_8() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2+1/3');
        $el->set_parameter('forbidFloats', true);
        $state = $el->validate_student_response(array('sans1' => 'x^2+0.33'), $options, 'x^2+1/3', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Illegal_floats', $state->note);
    }

    public function test_validate_student_response_9() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1<x nounand x<7');
        $state = $el->validate_student_response(array('sans1' => '1<x and x<7'), $options, '1<x nounand x<7',
            array('tans'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1<x nounand x<7', $state->contentsmodified);
        $this->assertEquals('\[ 1 < x\,{\mbox{ and }}\, x < 7 \]', $state->contentsdisplayed);
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">'
            . '<span class="nolink">\( 1<x \,{\mbox{and}}\,x<7 \)</span></span>, which can be typed in as follows: '
            . '<code>1<x and x<7</code>', $el->get_teacher_answer_display('1<x nounand x<7', '1<x \,{\mbox{and}}\,x<7'));
    }

    public function test_validate_student_lowest_terms_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '12/4');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(array('sans1' => '12/4'), $options, '3', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
    }

    public function test_validate_student_lowest_terms_2() {
        // This test checks the unary minus is *not* in lowest terms.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '-10/-1');
        $el->set_parameter('lowestTerms', true);
        $state = $el->validate_student_response(array('sans1' => '-10/-1'), $options, '10', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
    }

    public function test_validate_student_response_with_rationalized() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '1/2');
        $el->set_parameter('options', 'rationalized');
        $state = $el->validate_student_response(array('sans1' => "x^2+x/sqrt(2)"), $options, '3.14', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2+x/sqrt(2)', $state->contentsmodified);
        $this->assertEquals('\[ x^2+\frac{x}{\sqrt{2}} \]', $state->contentsdisplayed);
        $this->assertEquals(' You must clear the following from the denominator of your fraction: ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\[ \left[ \sqrt{2} \right] \]' .
                '</span></span>', $state->errors);
    }

    public function test_validate_student_response_subscripts() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))');
        $state = $el->validate_student_response(array('sans1' => 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))'),
                $options, 'x^2+1/3', array('tans'));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_insertstars_true_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2x'), $options, '2*x', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_insertstars_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '2x'), $options, '2*x', array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_insertstars_sqrt_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*sqrt(2)/3');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2*sqrt(+2)/3'), $options, '2*sqrt(2)/3', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('2*sqrt(+2)/3', $state->contentsmodified);
        $this->assertEquals('\[ \frac{2\cdot \sqrt{2}}{3} \]', $state->contentsdisplayed);
        $this->assertEquals('A correct answer is <span class="filter_mathjaxloader_equation">'
            . '<span class="nolink">\( \frac{2\cdot \sqrt{2}}{3} \)</span></span>, which can be typed in as follows: '
            . '<code>2*sqrt(2)/3</code>', $el->get_teacher_answer_display('2*sqrt(2)/3', '\frac{2\cdot \sqrt{2}}{3}'));
    }

    public function test_validate_student_response_sametype_true_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(array('sans1' => '2*x'), $options, 'y=2*x', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_sametype_true_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'y=2*x');
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(array('sans1' => '2*x'), $options, 'y=2*x', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_sametype_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'y=2*x');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => '2*x'), $options, 'y=2*x', array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_equation', $state->note);
    }

    public function test_validate_student_response_sametype_false_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'm*x+c');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => 'y=m*x+c'), $options, 'm*x+c', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("CASError: stack_trans('ATAlgEquiv_TA_not_equation');", $state->note);
    }

    public function test_validate_student_response_sametype_false_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '{1,2}');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => '1'), $options, '{1,2}', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_set', $state->note);
    }

    public function test_validate_student_response_sametype_false_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => '{x}'), $options, 'x', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('SA_not_expression', $state->note);
    }

    public function test_validate_student_response_display_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '-3*x^2-4');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '-3x^2-4'), $options, '-3*x^2-4', null);
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
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '(3x+1)(x+ab)'), $options, '(3*x+1)*(x+ab)', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(3*x+1)*(x+ab)', $state->contentsmodified);
        $this->assertEquals('\[ \left(3\cdot x+1\right)\cdot \left(x+{\it ab}\right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 's^(24*r)');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        // For this test, if sameType is true, old versions of Maxima blow up with
        // Heap exhausted during allocation: 8481509376 bytes available, 35303692080 requested.
        $el->set_parameter('sameType', false);
        $state = $el->validate_student_response(array('sans1' => 's^r^24'), $options, 's^(24*r)', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('s^r^24', $state->contentsmodified);
        $this->assertEquals('\[ s^{r^{24}} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_var_chars_on() {
        // Check the single variable character option is tested.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '(3*x+1)*(x+ab)');
        $el->set_parameter('insertStars', 2);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '(3x+1)(x+ab)'), $options, '(3*x+1)*(x+ab)', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(3*x+1)*(x+a*b)', $state->contentsmodified);
        $this->assertEquals('\[ \left(3\cdot x+1\right)\cdot \left(x+a\cdot b\right) \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a , b , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_single_var_chars_off() {
        // Check the single variable character option is tested.
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '(3x+1)*(x+ab)');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '(3x+1)(x+ab)'), $options, '(3*x+1)*(x+ab)', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(3*x+1)*(x+ab)', $state->contentsmodified);
        $this->assertEquals('\[ \left(3\cdot x+1\right)\cdot \left(x+{\it ab}\right) \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ {\it ab} , x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_allowwords_false() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $state = $el->validate_student_response(array('sans1' => 'unknownfunction(x^2+1)+3*x'), $options, '2*x', array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
    }

    public function test_validate_student_response_allowwords_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '2*x');
        $el->set_parameter('allowWords', 'pop, funney1, unknownfunction');
        $state = $el->validate_student_response(array('sans1' => 'unknownfunction(x^2+1)+3*x'), $options, '2*x', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_single_variable() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'cos(a*x)/(x*(ln(x)))');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(array('sans1' => 'cos(ax)/(x(ln(x)))'), $options, 'cos(a*x)/(x*(ln(x)))',
                array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('cos(a*x)/(x(ln(x)))', $state->contentsmodified);
        $this->assertEquals('\[ \frac{\cos \left( a\cdot x \right)}{x\left(\ln \left( x \right)\right)} \]',
                $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_subscripts() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'a*b_c*d');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => 'ab_cd'), $options, 'a*b_c*d',
                array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('a*b_c*d', $state->contentsmodified);
        $this->assertEquals('\[ a\cdot {b}_{c}\cdot d \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_trigexp() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'sin(ab)^2');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => 'sin(ab)^2'), $options, 'sin(ab)^2',
                array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('sin(a*b)^2', $state->contentsmodified);
        $this->assertEquals('\[ \sin ^2\left(a\cdot b\right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_single_variable_trigexp_fail() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'sin(ab)^2');
        // Assuming single character variable names.
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => 'sin^2(ab)'), $options, 'sin(ab)^2',
                array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('sin^2*(ab)', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">sin^2*(ab)</span>', $state->contentsdisplayed);
        $this->assertEquals('trigexp', $state->note);
    }

    public function test_validate_student_response_functions_variable() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'a/(a*(x+1)+2)');

        $state = $el->validate_student_response(array('sans1' => 'a/(a(x+1)+2)'), $options, 'a/(a*(x+1)+2)', array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Variable_function", $state->note);
    }

    public function test_validate_lg_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'lg(27,3)');
        $state = $el->validate_student_response(array('sans1' => 'lg(27,3)'), $options, 'lg(27,3)', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('lg(27,3)', $state->contentsmodified);
        $this->assertEquals('\[ \log_{3}\left(27\right) \]', $state->contentsdisplayed);
    }

    public function test_validate_set_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '{a,b,c}');
        $state = $el->validate_student_response(array('sans1' => '{a,b,c}'), $options, '{a,b,c}', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('{a,b,c}', $state->contentsmodified);
        $this->assertEquals('\[ \left \{a , b , c \right \} \]', $state->contentsdisplayed);
    }

    public function test_validate_or_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x=1 or x=1');
        $state = $el->validate_student_response(array('sans1' => 'x=1 or x=1'), $options, 'x=1 or x=1', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('x=1 nounor x=1', $state->contentsmodified);
        $this->assertEquals('\[ x=1\,{\mbox{ or }}\, x=1 \]', $state->contentsdisplayed);
    }

    public function test_validate_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '9');
        $state = $el->validate_student_response(array('sans1' => '9*hz'), $options, '9', null);
        // In the units input this would be INVALID as hz should be Hz.
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9*hz', $state->contentsmodified);
        $this->assertEquals('\[ 9\cdot {\it hz} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => '"Hello world"'), $options, '"A random string"', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('\[ \mbox{Hello world} \]', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type_invalid1() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', 'x^2');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => '"Hello world"'), $options, 'x^2', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('"Hello world"', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">"Hello world"</span>', $state->contentsdisplayed);
    }

    public function test_validate_string_same_type_invalid2() {
        $options = new stack_options();
        $el = stack_input_factory::make('algebraic', 'sans1', '"A random string"');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => 'x^2'), $options, '"A random string"', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('x^2', $state->contentsmodified);
        $this->assertEquals('\[ x^2 \]', $state->contentsdisplayed);
    }
}
