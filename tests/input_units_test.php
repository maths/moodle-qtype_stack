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

/**
 * Unit tests for the stack_algebra_input class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

/**
 * Unit tests for stack_algebra_input.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_units_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('units', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" '
                .'size="16.5" style="width: 13.6em" value="" />',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false));
    }

    public function test_render_zero() {
        // We must have some units for this input type.
        $el = stack_input_factory::make('units', 'ans1', '0');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" '
                .'size="16.5" style="width: 13.6em" value="0" />',
                $el->render(new stack_input_state(stack_input::INVALID, array('0'), '', '', '', '', ''),
                        'stack1__ans1', false));
    }

    public function test_render_pre_filled() {
        $el = stack_input_factory::make('units', 'test', 'm/s');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" '
                .'size="16.5" style="width: 13.6em" value="m/s" />',
                $el->render(new stack_input_state(stack_input::VALID, array('m/s'), '', '', '', '', ''),
                        'stack1__test', false));
    }

    public function test_render_basic() {
        $el = stack_input_factory::make('units', 'input', '9.81*m/s^2');
        $this->assertEquals(
                '<input type="text" name="stack1__input" id="stack1__input" '
                .'size="16.5" style="width: 13.6em" value="9.81*m/s^2" readonly="readonly" />',
                $el->render(new stack_input_state(stack_input::VALID, array('9.81*m/s^2'), '', '', '', '', ''),
                        'stack1__input', true));
    }

    public function test_render_different_size() {
        $el = stack_input_factory::make('units', 'input', '-9.81*m/s^2');
        $el->set_parameter('boxWidth', 30);
        $this->assertEquals('<input type="text" name="stack1__input" id="stack1__input" '
                .'size="33" style="width: 27.1em" value="-9.81*m/s^2" />',
                $el->render(new stack_input_state(stack_input::VALID, array('-9.81*m/s^2'), '', '', '', '', ''),
                        'stack1__input', false));
    }

    public function test_render_syntaxhint() {
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('syntaxHint', '?*m/s^2');
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" '
                .'size="16.5" style="width: 13.6em" value="?*m/s^2" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'stack1__sans1', false));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2', 'sans1_val' => '9.81m/s^2'),
                $options, '9.81*m/s^2', array());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2+tans'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownFunction', $state->note);
    }

    public function test_validate_student_response_6() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2*sillyname(x)'),
                $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownFunction', $state->note);
    }

    public function test_validate_student_response_7() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2+tans'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | unknownFunction', $state->note);
    }

    public function test_validate_student_response_both_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_student_only_units_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => 'm/s^2'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_only_units", $state->note);
        $this->assertEquals('m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ \frac{\mathrm{m}}{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_only_units_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => 'm'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_only_units", $state->note);
        $this->assertEquals('m', $state->contentsmodified);
        $this->assertEquals('\[ \mathrm{m} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_missing_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_no_units", $state->note);
        $this->assertEquals('9.81', $state->contentsmodified);
        $this->assertEquals('\[ 9.81 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_missing_units_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => 'pi*sin(2)'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_no_units", $state->note);
        $this->assertEquals('%pi*sin(2)', $state->contentsmodified);
        $this->assertEquals('\[ \pi\cdot \sin \left( 2 \right) \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_bad_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81+m/s^2'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_bad_units", $state->note);
        $this->assertEquals('9.81+m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81+\frac{\mathrm{m}}{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_bad_spaces() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81 m/s^2'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("spaces", $state->note);
        $this->assertEquals('9.81 m/s^2', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">9.81 m/s^2</span>', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_edgecase() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '1'), $options, '9.81', array('tans'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1', $state->contentsmodified);
        $this->assertEquals('\[ 1 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_teacher_missing_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81'), $options, '9.81', array('tans'));
        $this->assertEquals(stack_input::VALID, $state->status);
        // This is ok, so we have no note.
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_student_excess_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s'), $options, '9.81', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_excess_units", $state->note);
        $this->assertEquals('9.81*m*s^-1', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\cdot \mathrm{m}\cdot \mathrm{s}^ {- 1 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_excess_units_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81s'), $options, '9.81', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_excess_units", $state->note);
        $this->assertEquals('9.81*s', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\cdot \mathrm{s} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_true_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\cdot \mathrm{m}\cdot \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_true_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.81*m*s');
        $el->set_parameter('insertStars', 2);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '7.81ms'), $options, '7.81*m*s', array('ta'));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.81*m*s', $state->contentsmodified);
        $this->assertEquals('\[ 7.81\cdot \mathrm{m}\cdot \mathrm{s} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('strictSyntax', true);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2', array('ta'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_wrongtype_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => 'y=9.81*m/s^2'), $options, '9.81*m/s^2', array('tans'));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("SA_not_expression", $state->note);
    }

    public function test_validate_student_response_rational_number_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '1/3*m/s^2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1*3^-1*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ 1\cdot 3^ {- 1 }\cdot \mathrm{m}\cdot \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_rational_number_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '2/6*m/s^2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
        $this->assertEquals('2*6^-1*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ 2\cdot 6^ {- 1 }\cdot \mathrm{m}\cdot \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\cdot \mathrm{m}\cdot \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => 'm/2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('2^-1*m', $state->contentsmodified);
        $this->assertEquals('\[ 2^ {- 1 }\cdot \mathrm{m} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_litre_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*l');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '7.2*l'), $options, '7.2*l', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*l', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\cdot \mathrm{l} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_hz_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*Hz');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '7.2*Hz'), $options, '7.2*Hz', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*Hz', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\cdot \mathrm{Hz} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_hz_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*Hz');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '7.2*hz'), $options, '7.2*Hz', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
    }

    public function test_validate_student_response_litre_2() {
        // Respect upper case "L".
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*l');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '7.2*L'), $options, '7.2*l', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*L', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\cdot \mathrm{L} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_ohm() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*uohm');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '7.2*uohm'), $options, '7.2*uohm', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*uohm', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\cdot \mu \Omega \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_qmchar() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => '?*m/s^2'), $options, '9.81*m/s^2', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('?*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ \color{red}{?}\cdot \mathrm{m}\cdot \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_sqrt2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '1.41*m');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('strictSyntax', false);
        $state = $el->validate_student_response(array('sans1' => 'sqrt(2)*m', 'sans1_val' => 'sqrt(2)*m'),
                $options, '1.41*m', array());
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

}
