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

// Unit tests for the stack_matrix_input class.
//
// @copyright 2012 The University of Birmingham.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_matrix_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $this->assertEquals('<table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" border="0" cellpadding="1" cellspacing="0">' .
                '<tbody><tr><td style="border-width: 2px 0px 0px 2px; padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" name="ans1_sub_0_0" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_0_1" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_0_2" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td style="border-width: 2px 2px 0px 0px; padding-top: 0.5em">&nbsp;</td></tr>' .
                '<tr><td style="border-width: 0px 0px 2px 2px;">&nbsp;</td>' .
                '<td><input type="text" name="ans1_sub_1_0" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_1_1" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_1_2" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td style="border-width: 0px 2px 2px 0px; padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table>',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_no_errors_if_garbled() {
        // If the teacher does not know the right syntax for a matrix, we should
        // not give PHP errors.
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('[[1,0],[0,1]]');
        $this->assertEquals('<div class="error"><p>The input has generated the following runtime error which prevents you '.
                'from answering. Please contact your teacher.</p><p><span class="error">The CAS returned the following '.
                'error(s):</span><span class="stacksyntaxexample">ta:matrix_size([[1,0],[0,1]])</span> caused the following '.
                'error: The "$first" argument of the function "$matrix_size" must be a matrix</p></div>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint() {
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $el->adapt_to_model_answer('matrix([1,0],[0,1])');
        $this->assertEquals('<table class="matrixtable" id="ans1_container" style="display:inline; vertical-align: middle;" '.
                'border="0" cellpadding="1" cellspacing="0">' .
                '<tbody><tr><td style="border-width: 2px 0px 0px 2px; padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" name="ans1_sub_0_0" value="a" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_0_1" value="b" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td style="border-width: 2px 2px 0px 0px; padding-top: 0.5em">&nbsp;</td></tr>' .
                '<tr><td style="border-width: 0px 0px 2px 2px;">&nbsp;</td>' .
                '<td><input type="text" name="ans1_sub_1_0" value="?" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_1_1" value="d" size="5" autocapitalize="none" spellcheck="false"></td>'.
                '<td style="border-width: 0px 2px 2px 0px; padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_null_ta() {
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $this->assertEquals('<table class="matrixtable" id="ans1_container" style="display:inline; vertical-align: middle;" '.
                'border="0" cellpadding="1" cellspacing="0">' .
                '<tbody><tr><td style="border-width: 2px 0px 0px 2px; padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" name="ans1_sub_0_0" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_0_1" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td style="border-width: 2px 2px 0px 0px; padding-top: 0.5em">&nbsp;</td></tr>' .
                '<tr><td style="border-width: 0px 0px 2px 2px;">&nbsp;</td>' .
                '<td><input type="text" name="ans1_sub_1_0" value="" size="5" autocapitalize="none" spellcheck="false"></td>' .
                '<td><input type="text" name="ans1_sub_1_1" value="" size="5" autocapitalize="none" spellcheck="false"></td>'.
                '<td style="border-width: 0px 2px 2px 0px; padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table>',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_validate_student_response_na() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $state = $el->validate_student_response(array(), $options, 'matrix([1,2,3],[3,4,5])', null);
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_student_response_valid() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = array(
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => 'a',
            'ans1_sub_1_2' => 'a+b'
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', null);
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([1,2,3],[4,a,a+b])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ 4 & a & a+b \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a , b \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_invalid_one_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = array(
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '',
            'ans1_sub_1_1' => '5',
            'ans1_sub_1_2' => '6'
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([1,2,3],[?,5,6])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ \color{red}{?} & 5 & 6 \end{array}\right] \]',
                $state->contentsdisplayed);
        // Note that QMCHAR does not appear in the list of variables any more.
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = array(
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2x',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
            'ans1_sub_1_2' => '6'
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('matrix([1,2x,3],[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x,3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_modinput_tokenizer_1() {
        $in = '[1,2],[2,3]';
        $out = array('[1,2]', '[2,3]');

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_2() {
        $in = '[1,2,3],[4,5,6]';
        $out = array('[1,2,3]', '[4,5,6]');

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_row() {
        $in = '1,2,3';
        $out = array('1', '2', '3');

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_incomplete() {
        $in = '[1,],[,]';
        $out = array('[1,]', '[,]');

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_incomplete_row() {
        $in = '1,';
        $out = array('1', '');

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_render_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'x^2');
        $el->set_parameter('options', 'allowempty');
        $this->assertEquals('<table class="matrixtable" id="stack1__ans1_container" style="display:inline; ' .
                'vertical-align: middle;" border="0" cellpadding="1" cellspacing="0"><tbody></tbody></table>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->set_parameter('options', 'allowempty');
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = array(
            'ans1_sub_0_0' => '',
            'ans1_sub_0_1' => '',
            'ans1_sub_1_0' => '',
            'ans1_sub_1_1' => '',
        );
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', null);
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([null,null],[null,null])', $state->contentsmodified);
        $this->assertEquals('',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = array(
            'ans1_sub_0_0' => '',
            'ans1_sub_0_1' => '',
            'ans1_sub_1_0' => '',
            'ans1_sub_1_1' => '',
        );
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', null);
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_blank_part() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = array(
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_1_0' => 'x',
            'ans1_sub_1_1' => '',
        );
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', null);
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([1,2],[x,?])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} 1 & 2 \\\\ x & \color{red}{?} \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);
    }
}
