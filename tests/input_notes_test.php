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

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

// Unit tests for the stack_notes_input class.
//
// @copyright 2017 The University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_notes_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('notes', 'ans1', '');
        $el->adapt_to_model_answer('Hello world');
        $this->assertEquals('<textarea name="ans1" id="ans1" rows="5" cols="50"></textarea>' .
                '<div class="clearfix"></div>',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_validate_student_response_1() {
        $options = new stack_options();
        $el = stack_input_factory::make('notes', 'sans1', 'true');
        $ans1 = 'This input gives an instant rendering of LaTeX e.g. \[ \sum_{n=1}^\infty \frac{1}{n^2}=\frac{\pi^2}{6}.\]';
        $state = $el->validate_student_response(array('sans1' => $ans1), $options, 'true', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->contentsmodified);

        $el->set_parameter('showValidation', 0);
        $vr = '<div class="stackinputfeedback standard empty" id="sans1_val"></div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));

        $el->set_parameter('showValidation', 1);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val"><span class="filter_mathjaxloader_equation">' .
                '<div class="text_to_html"><p>This input gives an instant rendering of LaTeX e.g. ' .
                '<span class="nolink">\[ \sum_{n=1}^\infty \frac{1}{n^2}=\frac{\pi^2}{6}.\]</span></p>' .
                '<p class="stackinputnotice">(This input is not assessed automatically by STACK.)</p></div></span></div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));

        $el->set_parameter('showValidation', 2);
        $vr = '<div class="stackinputfeedback standard" id="sans1_val"><span class="filter_mathjaxloader_equation">' .
                '<div class="text_to_html"><p>This input gives an instant rendering of LaTeX e.g. ' .
                '<span class="nolink">\[ \sum_{n=1}^\infty \frac{1}{n^2}=\frac{\pi^2}{6}.\]</span></p>' .
                '<p class="stackinputnotice">(This input is not assessed automatically by STACK.)</p></div></span></div>';
        $this->assertEquals($vr, $el->replace_validation_tags($state, 'sans1', '[[validation:sans1]]'));
    }
}
