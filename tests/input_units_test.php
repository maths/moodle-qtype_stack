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

// Unit tests for stack_units_input.
//
// @copyright 2016 The University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_units_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('units', 'ans1', 'x^2');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="" />',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_zero() {
        // We must have some units for this input type.
        $el = stack_input_factory::make('units', 'ans1', '0');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="0" />',
                $el->render(new stack_input_state(stack_input::INVALID, array('0'), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_render_pre_filled() {
        $el = stack_input_factory::make('units', 'test', 'm/s');
        $this->assertEquals('<input type="text" name="stack1__test" id="stack1__test" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="m/s" />',
                $el->render(new stack_input_state(stack_input::VALID, array('m/s'), '', '', '', '', ''),
                        'stack1__test', false, null));
    }

    public function test_render_basic() {
        $el = stack_input_factory::make('units', 'input', '9.81*m/s^2');
        $this->assertEquals(
                '<input type="text" name="stack1__input" id="stack1__input" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="9.81*m/s^2" '
                .'readonly="readonly" />',
                $el->render(new stack_input_state(stack_input::VALID, array('9.81*m/s^2'), '', '', '', '', ''),
                        'stack1__input', true, null));
    }

    public function test_render_different_size() {
        $el = stack_input_factory::make('units', 'input', '-9.81*m/s^2');
        $el->set_parameter('boxWidth', 30);
        $this->assertEquals('<input type="text" name="stack1__input" id="stack1__input" size="33" style="width: 27.1em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="-9.81*m/s^2" />',
                $el->render(new stack_input_state(stack_input::VALID, array('-9.81*m/s^2'), '', '', '', '', ''),
                        'stack1__input', false, null));
    }

    public function test_render_syntaxhint() {
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('syntaxHint', '?*m/s^2');
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="?*m/s^2" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'stack1__sans1', false, null));
    }

    public function test_render_placeholder() {
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('syntaxHint', 'Remove me');
        $el->set_parameter('syntaxAttribute', 1);
        $this->assertEquals('<input type="text" name="stack1__sans1" id="stack1__sans1" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" placeholder="Remove me" />',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'stack1__sans1', false, null));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2', 'sans1_val' => '9.81m/s^2'),
                $options, '9.81*m/s^2', new stack_cas_security(true));
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('ta')));
        $this->assertEquals(stack_input::VALID, $state->status);
    }

    public function test_validate_student_response_5() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2+tans'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenVariable', $state->note);
    }

    public function test_validate_student_response_6() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2*sillyname(x)'),
                $options, '9.81*m/s^2', new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
    }

    public function test_validate_student_response_7() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2+tans'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | forbiddenVariable', $state->note);
    }

    public function test_validate_student_response_both_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_student_only_units_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => 'm/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_only_units", $state->note);
        $this->assertEquals('m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ \, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_only_units_negpow_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => 'm/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_only_units", $state->note);
        $this->assertEquals('m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ \, \mathrm{m}\, \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_only_units_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => 'm'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_only_units", $state->note);
        $this->assertEquals('m', $state->contentsmodified);
        $this->assertEquals('\[ \, \mathrm{m} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_missing_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_no_units", $state->note);
        $this->assertEquals('9.81', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_missing_units_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => 'pi*sin(2)'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_no_units", $state->note);
        $this->assertEquals('%pi*sin(2)', $state->contentsmodified);
        $this->assertEquals('\[ \pi\, \sin \left( 2 \right)\, \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_bad_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81+m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_bad_units", $state->note);
        $this->assertEquals('9.81+m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81+{\mathrm{m}}/{\mathrm{s}^2}\, \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_bad_spaces() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81 m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("spaces", $state->note);
        $this->assertEquals('9.81*m/s^2', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">9.81 m/s^2</span>', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_spaces() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '5*10^2*kg');
        $el->set_parameter('insertStars', 3);
        // In prior versions the string replace added extra *s to give 5***10^2, which was silly.
        $state = $el->validate_student_response(array('sans1' => '5 * 10^2 kg'), $options, '5*10^2*kg',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals("spaces", $state->note);
        $this->assertEquals('5*10^2*kg', $state->contentsmodified);
        $this->assertEquals('\[ 5\times 10^2\, \mathrm{k}\mathrm{g} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_edgecase() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '1'), $options, '9.81',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1', $state->contentsmodified);
        $this->assertEquals('\[ 1\, \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_powers_ten() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => '9*10^2m^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('9*10^2*m^2', $state->contentsmodified);
        $this->assertEquals('\[ 9\times 10^2\, \mathrm{m}^2 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_trailingzeros() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.8100*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.8100*m/s^2', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('\[ 9.8100\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_trailingzeros_insertstar_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.8100m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.8100*m/s^2', $state->contentsmodified);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('\[ 9.8100\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_trailingzeros_insertstar_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m*s^2');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(array('sans1' => '9.8100ms^2'), $options, '9.81*m*s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.8100*ms^2', $state->contentsmodified);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('\[ 9.8100\, \mathrm{m}\mathrm{s}^2 \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_trailingzeros_neg() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '-9.8100*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('-9.8100*m/s^2', $state->contentsmodified);
        $this->assertEquals('', $state->note);
        $this->assertEquals('\[ -9.8100\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_teacher_missing_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81'), $options, '9.81',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        // This is ok, so we have no note.
        $this->assertEquals('', $state->note);
    }

    public function test_validate_student_response_student_excess_units() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s'), $options, '9.81',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_excess_units", $state->note);
        $this->assertEquals('9.81*m/s', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, {\mathrm{m}}/{\mathrm{s}} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_excess_units_negpow() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 0);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s'), $options, '9.81',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("Units_SA_excess_units", $state->note);
        $this->assertEquals('9.81*m/s', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, \mathrm{m}\, \mathrm{s}^ {- 1 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_student_excess_units_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81s'), $options, '9.81',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("missing_stars | Units_SA_excess_units", $state->note);
        $this->assertEquals('9.81*s', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, \mathrm{s} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_true_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('ta')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_true_negpow_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('ta')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, \mathrm{m}\, \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_true_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.81*m*s');
        $el->set_parameter('insertStars', 2);
        $state = $el->validate_student_response(array('sans1' => '7.81ms'), $options, '7.81*m*s',
                new stack_cas_security(true, '', '', array('ta')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.81*ms', $state->contentsmodified);
        $this->assertEquals('\[ 7.81\, \mathrm{m}\mathrm{s} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_insertstars_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 0);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('ta')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
    }

    public function test_validate_student_response_wrongtype_false_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('sameType', true);
        $state = $el->validate_student_response(array('sans1' => 'y=9.81*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals("SA_not_expression", $state->note);
    }

    public function test_validate_student_response_sum() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81+m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('9.81+m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81+{\mathrm{m}}/{\mathrm{s}^2}\, \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_rational_number_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '1/3*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1/3*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ {1}/{3}\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_negative_number_1() {
        $options = new stack_options();
        $options->set_option('simplify', false);
        $el = stack_input_factory::make('units', 'sans1', 'stackunits(-330,N)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '-330*N'), $options, 'stackunits(-330,N)',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('-330*N', $state->contentsmodified);
        $this->assertEquals('\[ -330\, \mathrm{N} \]', $state->contentsdisplayed);
        $this->assertEquals('stackunits(-330,N)', $el->get_teacher_answer());
    }

    public function test_validate_student_response_negative_number_2() {
        $options = new stack_options();
        $options->set_option('simplify', false);
        $el = stack_input_factory::make('units', 'sans1', 'stackunits(-9.81,m*s^-2)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '-9.81m/s^2'), $options, 'stackunits(-9.81,m*s^-2)',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('-9.81*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ -9.81\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
        $this->assertEquals('stackunits(-9.81,m*s^-2)', $el->get_teacher_answer());
    }

    public function test_validate_student_response_negative_number_3() {
        $options = new stack_options();
        $options->set_option('simplify', false);
        $el = stack_input_factory::make('units', 'sans1', 'stackunits(-9.81,m*s^-2)');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '(-9.81)m/s^2'), $options, 'stackunits(-9.81,m*s^-2)',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('(-9.81)*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ -9.81\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
        $this->assertEquals('stackunits(-9.81,m*s^-2)', $el->get_teacher_answer());
    }

    public function test_validate_student_response_rational_number_negpow_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '1/3*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1/3*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ \frac{1}{3}\, \mathrm{m}\, \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_rational_number_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '2/6*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
        $this->assertEquals('2/6*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ {2}/{6}\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_rational_number_negpow_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '2/6*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('Lowest_Terms', $state->note);
        $this->assertEquals('2/6*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ \frac{2}{6}\, \mathrm{m}\, \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => 'm/2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('m/2', $state->contentsmodified);
        $this->assertEquals('\[ {1}/{2}\, \mathrm{m} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m*s^-2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81*m*s^-2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, {\mathrm{m}}/{\mathrm{s}^2} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_4() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '1.3410*10^4*m*Hz');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '1.3410*10^4*m*Hz'), $options, '1.3410*10^4*m*Hz',
            new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1.3410*10^4*m*Hz', $state->contentsmodified);
        $this->assertEquals('\[ 1.3410\times 10^4\, \mathrm{Hz}\, \mathrm{m} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_negpow_3() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m*s^-2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '9.81*m*s^-2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('9.81*m*s^-2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\, \mathrm{m}\, \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_litre_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*l');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7.2*l'), $options, '7.2*l', new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*l', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\, \mathrm{l} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_hz_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*Hz');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7.2*Hz'), $options, '7.2*Hz', new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*Hz', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\, \mathrm{Hz} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_hz_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*Hz');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7.2*hz'), $options, '7.2*Hz', new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
    }

    public function test_validate_student_response_litre_2() {
        // Respect upper case "L".
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*l');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7.2*L'), $options, '7.2*l', new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*L', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\, \mathrm{L} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_ohm() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7.2*uohm');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7.2*uohm'), $options, '7.2*uohm', new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('7.2*uohm', $state->contentsmodified);
        $this->assertEquals('\[ 7.2\, \mu \Omega \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ \mu \Omega \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_display_qmchar_negpow() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '?*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('QMCHAR*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ \color{red}{?}\, \mathrm{m}\, \mathrm{s}^ {- 2 } \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_display_zero() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '0*s');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '0*s'), $options, '0*s', new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('0*s', $state->contentsmodified);
        $this->assertEquals('\[ 0\, \mathrm{s} \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ \mathrm{s} \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_display_one() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '1*s');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '1*s'), $options, '1*s', new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('1*s', $state->contentsmodified);
        $this->assertEquals('\[ 1\, \mathrm{s} \]', $state->contentsdisplayed);
        $this->assertEquals('\( \left[ \mathrm{s} \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_sqrt2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '1.41*m');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => 'sqrt(2)*m', 'sans1_val' => 'sqrt(2)*m'),
                $options, '1.41*m', new stack_cas_security(true));
        $this->assertEquals(stack_input::SCORE, $state->status);
    }

    public function test_validate_student_response_option_mul_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.2*m/s');
        $el->set_parameter('options', 'mul');
        $state = $el->validate_student_response(array('sans1' => '3.2*m/s', 'sans1_val' => '3.2*m/s'),
                $options, '3.2*m/s', new stack_cas_security(true));
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('3.2*m/s', $state->contentsmodified);
    }

    public function test_validate_student_response_mhz() {
        // Case sensitity disambiguation.
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*mHz');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81*mhz'),
                $options, '9.81*mHz', new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownUnitsCase', $state->note);
        $this->assertEquals('9.81*mhz', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">9.81*mhz</span>', $state->contentsdisplayed);
        $this->assertEquals('Input of units is case sensitive:  <span class="stacksyntaxexample">mhz</span> is an unknown unit. ' .
                'Did you mean one from the following list <span class="stacksyntaxexample">[mHz, MHz]</span>?', $state->errors);
    }

    public function test_student_response_units_hz() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9');
        $state = $el->validate_student_response(array('sans1' => '9*hz'), $options, '9', new stack_cas_security(true));
        // In the algebraic input this would be VALID as the hz/Hz test is only done for units.
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('9*hz', $state->contentsmodified);
        $this->assertEquals('unitssynonym', $state->note);
        $this->assertEquals('9*hz', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">9*hz</span>', $state->contentsdisplayed);
        $this->assertEquals('You appear to have units <span class="stacksyntaxexample">hz</span>.  ' .
                'Did you mean <span class="stacksyntaxexample">Hz</span>?', $state->errors);
    }

    public function test_validate_student_hours() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '5*hr');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '5*hr'),
                $options, '5*hr', new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unitssynonym', $state->note);
        $this->assertEquals('5*hr', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">5*hr</span>', $state->contentsdisplayed);
        $this->assertEquals('You appear to have units <span class="stacksyntaxexample">hr</span>.  ' .
                'Did you mean <span class="stacksyntaxexample">h</span>?', $state->errors);
    }

    public function test_validate_student_response_display_recip() {
        // This test is for the awkarward edge case where we have 1/unit.  We don't want an extra one in the numbers.
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.88e-4*1/s');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '3.88e-4*1/s'), $options, '3.88e-4*1/s',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('3.88e-4*1/s', $state->contentsmodified);
        // This is a special display rule to highlight the multiplication with 1/unit.
        $this->assert_equals_ignore_spaces_and_e('\[ 3.88e-4\times {1}/{\mathrm{s}} \]',
                qtype_stack_testcase::prepare_actual_maths($state->contentsdisplayed));
        $this->assertEquals('\( \left[ \mathrm{s} \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_display_recip_negpow() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.88e-4*1/s');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('options', 'negpow');
        $state = $el->validate_student_response(array('sans1' => '3.88e-4*1/s'), $options, '3.88e-4*1/s',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('3.88e-4*1/s', $state->contentsmodified);
        $this->assert_equals_ignore_spaces_and_e('\[ 3.88e-4\, \mathrm{s}^ {- 1 } \]',
                qtype_stack_testcase::prepare_actual_maths($state->contentsdisplayed));
        $this->assertEquals('\( \left[ \mathrm{s} \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_display_recip_multi() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.88e-4*1/s');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '3.88e-4*1/(M*s)'), $options, '3.88e-4*1/s',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assert_equals_ignore_spaces_and_e('3.88e-4*1/(M*s)', $state->contentsmodified);
        if ($this->adapt_to_new_maxima('5.34.2')) {
            $this->assert_equals_ignore_spaces_and_e('\[ 3.88e-4\times {1}/{\left(\mathrm{M}\, \mathrm{s}\right)} \]',
                qtype_stack_testcase::prepare_actual_maths($state->contentsdisplayed));
            $this->assertEquals('\( \left[ \mathrm{M} , \mathrm{s} \right]\) ', $state->lvars);
        } else {
            $this->assert_equals_ignore_spaces_and_e('\[ 3.88e-4\times {1}/{\left(\mathrm{s}\, \mathrm{M}\right)} \]',
                qtype_stack_testcase::prepare_actual_maths($state->contentsdisplayed));
            $this->assertEquals('\( \left[ \mathrm{s} , \mathrm{M} \right]\) ', $state->lvars);
        }
    }

    public function test_validate_student_response_display_errors1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '9.81+-0.01m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | Units_SA_errorbounds_invalid', $state->note);
        $this->assertEquals('9.81#pm#0.01*m/s^2', $state->contentsmodified);
        $this->assertEquals('\[ 9.81\pm 0.01\, {\mathrm{m}}/{\mathrm{s}^2} \]',
                qtype_stack_testcase::prepare_actual_maths($state->contentsdisplayed));
        $this->assertEquals('\( \left[ \mathrm{m} , \mathrm{s} \right]\) ', $state->lvars);
    }

    public function test_validate_student_minsf_maxsf_equal_true() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '9.81*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_equal_low() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '9.8*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply exactly <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_equal_high() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '9.81*m/s^2');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '9.816*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('You must supply exactly <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\( 3 \)</span></span> significant figures.', $state->errors);
    }

    public function test_validate_student_minsf_maxsf_equal_ambiguous() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '10000*m/s^2');
        $el->set_parameter('options', 'minsf:3, maxsf:3');
        $state = $el->validate_student_response(array('sans1' => '1000*m/s^2'), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->errors);
    }

    public function test_render_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '10000*m/s^2');
        $el->set_parameter('options', 'allowempty');
        $this->assertEquals('<input type="text" name="stack1__ans1" id="stack1__ans1" size="16.5" style="width: 13.6em" '
                .'autocapitalize="none" spellcheck="false" class="algebraic-units" value="" />',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
        $state = $el->validate_student_response(array('sans1' => ''), $options, '9.81*m/s^2',
                new stack_cas_security(true, '', '', array('tans')));
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('EMPTYANSWER', $state->contentsmodified);
        $this->assertEquals('',
                qtype_stack_testcase::prepare_actual_maths($state->contentsdisplayed));
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_mol_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.14*mol');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '3.14*mol'), $options, '3.14*mol',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('3.14*mol', $state->contentsmodified);
        $this->assertEquals('\[ 3.14\, \mathrm{mol} \]', $state->contentsdisplayed);
    }

    public function test_validate_student_response_moles_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.14*mol');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '3.14*moles'), $options, '3.14*mol',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unitssynonym', $state->note);
        $this->assertEquals('3.14*moles', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">3.14*moles</span>', $state->contentsdisplayed);
        $this->assertEquals('You appear to have units <span class="stacksyntaxexample">moles</span>.  ' .
                'Did you mean <span class="stacksyntaxexample">mol</span>?', $state->errors);
    }

    public function test_validate_student_response_moles_2() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '3.14*mol');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '3.14*Moles'), $options, '3.14*mol',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unitssynonym', $state->note);
        $this->assertEquals('3.14*Moles', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">3.14*Moles</span>', $state->contentsdisplayed);
        $this->assertEquals('You appear to have units <span class="stacksyntaxexample">Moles</span>.  ' .
                'Did you mean <span class="stacksyntaxexample">mol</span>?', $state->errors);
    }

    public function test_validate_student_response_mmhg() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7*mmHg');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7*mmHg'), $options, '7*mmHg',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('7*mmHg', $state->contentsmodified);
        $this->assertEquals('\[ 7\, \mathrm{mmHg} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_mmhg_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '7*mmHg');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '7*mmhg'), $options, '7*mmHg',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('unknownUnitsCase', $state->note);
        $this->assertEquals('7*mmhg', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">7*mmhg</span>', $state->contentsdisplayed);
        $this->assertEquals('Input of units is case sensitive:  <span class="stacksyntaxexample">mmhg</span> is an unknown unit. '
                   . 'Did you mean one from the following list <span class="stacksyntaxexample">[mmHg]</span>?', $state->errors);
    }

    public function test_validate_student_response_amu() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '520*amu');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '520*amu'), $options, '520*amu',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('520*amu', $state->contentsmodified);
        $this->assertEquals('\[ 520\, \mathrm{amu} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_mamu() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '520*mamu');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '520*mamu'), $options, '520*mamu',
                new stack_cas_security(true));
        // If we do not toggle units then mamu if forbiddenly long.
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenVariable', $state->note);
        $this->assertEquals('520*mamu', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">520*mamu</span>', $state->contentsdisplayed);
        $this->assertEquals('Forbidden variable or constant: <span class="stacksyntaxexample">mamu</span>.', $state->errors);
    }

    public function test_validate_student_response_brackets() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '(1+1/sqrt(2)+3)*N
                ');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '(1+1/sqrt(2)+3)*N'), $options, '(1+1/sqrt(2)+3)*N',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('(1+1/sqrt(2)+3)*N', $state->contentsmodified);
        $this->assertEquals('\[ \left( 1+1\, {\sqrt{2}}^ {- 1 }+3\right)\, \mathrm{N} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }

    public function test_validate_student_response_10x() {

        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '23.2*10^2*m');
        $el->set_parameter('insertStars', 1);
        $state = $el->validate_student_response(array('sans1' => '523.2x10^2m'), $options, '23.2*10^2*m',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | Illegal_x10', $state->note);
        $this->assertEquals('523.2*x10^2*m', $state->contentsmodified);
        $this->assertEquals('Your answer appears to use the character "x" as a multiplication sign.  ' .
                'Please use <code>*</code> for multiplication.', $state->errors);

        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '23.2*10^2*m');
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => '9.34x10^3.4'), $options, '23.2*10^2*m',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | Illegal_x10 | Units_SA_bad_units', $state->note);
        $this->assertEquals('9.34*x*10^3.4', $state->contentsmodified);
        $this->assertEquals('Your answer appears to use the character "x" as a multiplication sign.  ' .
                'Please use <code>*</code> for multiplication. Your answer must have units, ' .
                'and you must use multiplication to attach the units to a value, e.g. <code>3.2*m/s</code>.',
                $state->errors);

        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '23.2*10^2*m');
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => '523.2 x 10^2 N*m'), $options, '23.2*10^2*m',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('spaces | Illegal_x10', $state->note);
        $this->assertEquals('523.2*x*10^2*N*m', $state->contentsmodified);
        $this->assertEquals('Your answer appears to use the character "x" as a multiplication sign.  ' .
                'Please use <code>*</code> for multiplication.', $state->errors);

        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '23.2*10^2*m');
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => '523.2 x 10^2m/s'), $options, '23.2*10^2*m',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | spaces | Illegal_x10', $state->note);
        $this->assertEquals('523.2*x*10^2*m/s', $state->contentsmodified);
        $this->assertEquals('Your answer appears to use the character "x" as a multiplication sign.  ' .
                'Please use <code>*</code> for multiplication.', $state->errors);

        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '23.2*10^2*m');
        $el->set_parameter('insertStars', 5);
        $state = $el->validate_student_response(array('sans1' => '9.81 x 10^3/s'), $options, '23.2*10^2*m',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('spaces | Illegal_x10', $state->note);
        $this->assertEquals('9.81*x*10^3/s', $state->contentsmodified);
        $this->assertEquals('Your answer appears to use the character "x" as a multiplication sign.  ' .
                'Please use <code>*</code> for multiplication.', $state->errors);
    }

    public function test_validate_student_response_btu() {
        $options = new stack_options();
        $el = stack_input_factory::make('units', 'sans1', '520*Btu');
        $el->set_parameter('insertStars', 1);
        $el->set_parameter('forbidFloats', false);
        $state = $el->validate_student_response(array('sans1' => '520*Btu'), $options, '520*Btu',
                new stack_cas_security(true));
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('520*Btu', $state->contentsmodified);
        $this->assertEquals('\[ 520\, \mathrm{Btu} \]', $state->contentsdisplayed);
        $this->assertEquals('', $state->errors);
    }
}
