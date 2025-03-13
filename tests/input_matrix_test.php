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
use function stack_utils\get_config;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

// Unit tests for the stack_matrix_input class.
//
// @copyright 2012 The University of Birmingham.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_matrix_input
 */
class input_matrix_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody>' .
                '<tr><td style="padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_2" name="ans1_sub_0_2" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_2" name="ans1_sub_1_2" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_no_errors_if_garbled() {
        // If the teacher does not know the right syntax for a matrix, we should
        // not give PHP errors.
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('[[1,0],[0,1]]');

        $versionused = \get_config('qtype_stack', 'maximaversion');
        $errmsg = '<div class="error"><p><i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The input has ' .
                      'generated the following runtime error which prevents you from answering. Please contact your teacher." ' .
                      'aria-label="The input has generated the following runtime error which prevents you from answering. Please ' .
                      'contact your teacher."></i>The input has generated the following runtime error which prevents you from ' .
                      'answering. Please contact your teacher.</p>' .
                '<p>The "$first" argument of the function "$matrix_size" must be a matrix</p></div>';
        if ($this->adapt_to_new_maxima('5.44.0')) {
            $errmsg = '<div class="error"><p><i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The input has ' .
                      'generated the following runtime error which prevents you from answering. Please contact your teacher." ' .
                      'aria-label="The input has generated the following runtime error which prevents you from answering. Please ' .
                      'contact your teacher."></i>The input has generated the following runtime error which prevents you from ' .
                      'answering. Please contact your teacher.</p>' .
                      '<p>The first argument of the function matrix_size must be a matrix</p></div>';
        } else if ($this->adapt_to_new_maxima('5.42.3')) {
            $errmsg = '<div class="error"><p><i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The input has ' .
                      'generated the following runtime error which prevents you from answering. Please contact your teacher." ' .
                      'aria-label="The input has generated the following runtime error which prevents you from answering. Please ' .
                      'contact your teacher."></i>The input has generated the following runtime error which prevents you from ' .
                      'answering. Please contact your teacher.</p>' .
                      '<p>The $first argument of the function $matrix_size must be a matrix</p></div>';
        }
        $this->assertEquals($errmsg, $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $el->adapt_to_model_answer('matrix([1,0],[0,1])');
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
                '<td style="padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="a" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="b" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="?" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="d" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint_spaces() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('syntaxHint', 'matrix([a,b], [?,d])');
        $el->adapt_to_model_answer('matrix([1,0],[0,1])');
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="ans1_container" ' .
            'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
            '<td style="padding-top: 0.5em">&nbsp;</td>' .
            '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="a" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="b" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
            '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="?" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="d" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
            $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                'ans1', false, null));
    }

    public function test_render_syntax_hint_round() {
        $options = new stack_options();
        $options->set_option('matrixparens', '(');
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $el->adapt_to_model_answer('matrix([1,0],[0,1])');
        $this->assertEquals('<div class="matrixroundbrackets"><table class="matrixtable" id="ans1_container" ' .
            'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
            '<td style="padding-top: 0.5em">&nbsp;</td>' .
            '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="a" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="b" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
            '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="?" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="d" size="5" ' .
                'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
            '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
            $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                'ans1', false, null));
    }

    public function test_render_syntax_hint_placeholder() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $el->set_parameter('syntaxAttribute', '1');
        $el->adapt_to_model_answer('matrix([1,0],[0,1])');
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
                '<td style="padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" placeholder="a" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" placeholder="b" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" placeholder="?" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" placeholder="d" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_null_ta() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
                '<td style="padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_validate_student_response_na() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $state = $el->validate_student_response([], $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_student_response_valid() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => 'a',
            'ans1_sub_1_2' => 'a+b',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([1,2,3],[4,a,a+b])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ 4 & a & a+b \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a , b \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_decimals() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('forbidFloats', false);
        // Teacher's answer must be in strict syntax.
        $el->adapt_to_model_answer('matrix([3.14,2.71],[4,5])');
        $inputvals = [
            'ans1_sub_0_0' => '3,14',
            'ans1_sub_0_1' => '2,71',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([3.14,2.71],[4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        // Contents are as typed by the student.
        $this->assertEquals([['3,14', '2,71'], ['4', '5']], $state->contents);
        // Modified contents must be strict Maxima syntax.
        $this->assertEquals('matrix([3.14,2.71],[4,5])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} 3{,}14 & 2{,}71 \\\\ 4 & 5 \end{array}\right] \]',
            $state->contentsdisplayed);
    }

    public function test_validate_student_response_valid_round() {
        $options = new stack_options();
        $options->set_option('matrixparens', '(');
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2],[3,4])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2],[3,4])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([1,2],[4,5])', $state->contentsmodified);
        $this->assertEquals('\[ \left(\begin{array}{cc} 1 & 2 \\\\ 4 & 5 \end{array}\right) \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
        $this->assertEquals('<div class="matrixroundbrackets"><table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
                '<td style="padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="1" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="2" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="4" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="5" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
                $el->render($state, 'ans1', false, null));
    }

    public function test_validate_student_response_valid_square() {
        $options = new stack_options();
        $options->set_option('matrixparens', '[');
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2],[3,4])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2],[3,4])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([1,2],[4,5])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} 1 & 2 \\\\ 4 & 5 \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody><tr>' .
                '<td style="padding-top: 0.5em">&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_0_0" name="ans1_sub_0_0" value="1" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_0_1" name="ans1_sub_0_1" value="2" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-top: 0.5em">&nbsp;</td></tr><tr><td>&nbsp;</td>' .
                '<td><input type="text" id="ans1_sub_1_0" name="ans1_sub_1_0" value="4" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td><input type="text" id="ans1_sub_1_1" name="ans1_sub_1_1" value="5" size="5" ' .
                    'autocapitalize="none" spellcheck="false" data-stack-input-type="matrix" ' .
                    'data-stack-input-decimal-separator="." data-stack-input-list-separator=","></td>' .
                '<td style="padding-bottom: 0.5em">&nbsp;</td></tr></tbody></table></div>',
                $el->render($state, 'ans1', false, null));
    }

    public function test_validate_student_response_invalid_one_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '',
            'ans1_sub_1_1' => '5',
            'ans1_sub_1_2' => '6',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('qm_error', $state->note);
        $this->assertEquals('matrix([1,2,3],[QMCHAR,5,6])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ \color{red}{?} & 5 & 6 \end{array}\right] \]',
                $state->contentsdisplayed);
        // Note that QMCHAR does not appear in the list of variables any more.
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2x',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
            'ans1_sub_1_2' => '6',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('matrix([1,EMPTYANSWER,3],[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x,3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_bracket() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2x)',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
            'ans1_sub_1_2' => '6',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | missingLeftBracket', $state->note);
        $this->assertEquals('matrix([1,EMPTYANSWER,3],[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x),3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">2*x)</span>.', $state->errors);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_multiple() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([1,2,3],[3,4,5])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2x)',
            'ans1_sub_0_2' => '3',
            'ans1_sub_1_0' => '4',
            'ans1_sub_1_1' => '5',
            'ans1_sub_1_2' => '6a',
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | missingLeftBracket', $state->note);
        $this->assertEquals('matrix([1,EMPTYANSWER,3],[4,5,EMPTYANSWER])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x),3],[4,5,6a])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">2*x)</span>.    ' .
                'You seem to be missing * characters. Perhaps you meant to type ' .
                '<span class="stacksyntaxexample">6<span class="stacksyntaxexamplehighlight">*</span>a</span>.', $state->errors);
        $this->assertEquals('', $state->lvars);
    }

    public function test_modinput_tokenizer_1() {
        $in = '[1,2],[2,3]';
        $out = ['[1,2]', '[2,3]'];

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_2() {
        $in = '[1,2,3],[4,5,6]';
        $out = ['[1,2,3]', '[4,5,6]'];

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_row() {
        $in = '1,2,3';
        $out = ['1', '2', '3'];

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_incomplete() {
        $in = '[1,],[,]';
        $out = ['[1,]', '[,]'];

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_modinput_tokenizer_incomplete_row() {
        $in = '1,';
        $out = ['1', ''];

        $el = stack_input_factory::make('matrix', 'ans1', 'M');
        $this->assertEquals($out, $el->modinput_tokenizer($in));
    }

    public function test_render_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'x^2', $options);
        $el->set_parameter('options', 'allowempty');
        $this->assertEquals('<div class="matrixsquarebrackets"><table class="matrixtable" id="stack1__ans1_container" ' .
                'style="display:inline; vertical-align: middle;" cellpadding="1" cellspacing="0"><tbody></tbody>' .
                '</table></div>',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('options', 'allowempty');
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1_sub_0_0' => '',
            'ans1_sub_0_1' => '',
            'ans1_sub_1_0' => '',
            'ans1_sub_1_1' => '',
        ];
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([null,null],[null,null])', $state->contentsmodified);
        $this->assertEquals('',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1_sub_0_0' => '',
            'ans1_sub_0_1' => '',
            'ans1_sub_1_0' => '',
            'ans1_sub_1_1' => '',
        ];
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_blank_part() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1_sub_0_0' => '1',
            'ans1_sub_0_1' => '2',
            'ans1_sub_1_0' => 'x',
            'ans1_sub_1_1' => '',
        ];
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('qm_error', $state->note);
        $this->assertEquals('matrix([1,2],[x,QMCHAR])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} 1 & 2 \\\\ x & \color{red}{?} \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);
    }

    public function test_validate_consolidatesubscripts() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('options', 'consolidatesubscripts');
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1_sub_0_0' => 'a1',
            'ans1_sub_0_1' => 'a_2',
            'ans1_sub_1_0' => 'abc123',
            'ans1_sub_1_1' => 'abc_45',
        ];

        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('consolidate_subscripts', $state->note);
        $this->assertEquals('matrix([a1,a2],[abc123,abc45])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} a_{1} & a_{2} \\\\ ' .
            '{\it abc}_{123} & {\it abc}_{45} \end{array}\right] \]',
            $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a_{1} , a_{2} , {\it abc}_{123} , {\it abc}_{45} \right]\) ',
            $state->lvars);
    }

    public function test_validate_forbid_sin() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        $el->set_parameter('forbidWords', 'int, sin, diff');
        // We need to set the sameType to allow matrix within matrix.
        $el->set_parameter('sameType', false);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1_sub_0_0' => 'a1',
            'ans1_sub_0_1' => 'a_2',
            'ans1_sub_1_0' => '1+sin(x)',
            'ans1_sub_1_1' => 'abc_45',
        ];
        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">sin</span>.', $state->errors);
        $this->assertEquals('matrix([a1,a_2],[EMPTYANSWER,abc_45])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([a1,a_2],[1+sin(x),abc_45])</span>',
            $state->contentsdisplayed);

        // Matrix inside should be accepted.
        $inputvals = [
            'ans1_sub_0_0' => 'a',
            'ans1_sub_0_1' => 'b',
            'ans1_sub_1_0' => 'c',
            'ans1_sub_1_1' => 'matrix([a,b],[c,d])',
        ];
        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('matrix([a,b],[c,matrix([a,b],[c,d])])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} a & b \\\\ c & ' .
            '\left[\begin{array}{cc} a & b \\\\ c & d \end{array}\right] \end{array}\right] \]',
            $state->contentsdisplayed);
    }

    public function test_validate_forbid_matrix() {
        $options = new stack_options();
        $el = stack_input_factory::make('matrix', 'ans1', 'M', $options);
        // Matrix here should not forbid the top-level matrix.
        $el->set_parameter('forbidWords', 'matrix, sin, diff');
        $el->set_parameter('sameType', false);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1_sub_0_0' => 'a1',
            'ans1_sub_0_1' => 'a_2',
            'ans1_sub_1_0' => '1+x^2',
            'ans1_sub_1_1' => 'abc_45',
        ];
        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('', $state->errors);
        $this->assertEquals('matrix([a1,a_2],[1+x^2,abc_45])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} a_{1} & {a}_{2} \\\\ 1+x^2 & {{\it abc}}_{45} \end{array}\right] \]',
            $state->contentsdisplayed);

        // Matrix inside should be forbidden.
        $inputvals = [
            'ans1_sub_0_0' => 'a',
            'ans1_sub_0_1' => 'b',
            'ans1_sub_1_0' => 'c',
            'ans1_sub_1_1' => 'matrix([a,b],[c,d])',
        ];
        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">matrix</span>.', $state->errors);
        $this->assertEquals('matrix([a,b],[c,EMPTYANSWER])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([a,b],[c,matrix([a,b],[c,d])])</span>',
            $state->contentsdisplayed);
    }
}
