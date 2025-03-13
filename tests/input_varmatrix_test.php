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

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

// Unit tests for the stack_matrix_input class.
//
// @copyright 2012 The University of Birmingham.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_varmatrix_input
 */
class input_varmatrix_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="5" ' .
                'data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' .
                '</textarea></div>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_no_errors_if_garbled() {
        // The teacher does not need to use a matrix here but there will be errors later!
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');

        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="5" ' .
                'data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' .
                '</textarea></div>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint() {
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="10" ' .
                'data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">a b' ."\n" .
                '? d</textarea></div>',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint_placeholder() {
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $el->set_parameter('syntaxAttribute', '1');
        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" placeholder="a b' .
                "\n" . '? d" rows="5" cols="10" data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator=","></textarea></div>',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint_round() {
        $options = new stack_options();
        $options->set_option('matrixparens', '(');
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M', $options);
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $this->assertEquals('<div class="matrixroundbrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
            'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="10" ' .
            'data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">a b' ."\n" .
            '? d</textarea></div>',
            $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                'ans1', false, null));
    }

    public function test_render_monospace() {
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('options', 'monospace:true');
        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput input-monospace" size="5.5" style="width: 4.6em" rows="5" cols="5" ' .
                'data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' .
                '</textarea></div>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_no_monospace_default_on() {
        set_config('inputmonospace', '3', 'qtype_stack');
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput input-monospace" size="5.5" style="width: 4.6em" rows="5" cols="5" ' .
                'data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." data-stack-input-list-separator=",">' .
                '</textarea></div>',
                $el->render(new stack_input_state(stack_input::BLANK, [], '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_validate_student_response_na() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $state = $el->validate_student_response([], $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_student_response_valid() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2 3\n4 a a+b",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([1,2,3],[4,a,a+b])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ 4 & a & a+b \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('\( \left[ a , b \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_valid_zeros() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "0\n0",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([0],[0])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([0],[0])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{c} 0 \\\\ 0 \end{array}\right] \]',
                $state->contentsdisplayed);
    }

    public function test_validate_student_response_invalid_one_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2 3\n4   6",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('qm_error', $state->note);
        $this->assertEquals('matrix([1,2,3],[4,6,QMCHAR])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ 4 & 6 & \color{red}{?} \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_two_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2 3\n4",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('qm_error', $state->note);
        $this->assertEquals('matrix([1,2,3],[4,QMCHAR,QMCHAR])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ 4 & \color{red}{?} & \color{red}{?} \end{array}\right] \]',
            $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2x 3\n4 5 6",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('matrix([1,EMPTYCHAR,3],[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x,3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_bracket() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2x) 3\n4 5 6",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | missingLeftBracket', $state->note);
        $this->assertEquals('matrix([1,EMPTYCHAR,3],[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x),3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">2*x)</span>.', $state->errors);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_multiple() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2x) 3\n4 5 6a",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | missingLeftBracket', $state->note);
        $this->assertEquals('matrix([1,EMPTYCHAR,3],[4,5,EMPTYCHAR])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x),3],[4,5,6a])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">2*x)</span>.    ' .
                'You seem to be missing * characters. Perhaps you meant to type ' .
                '<span class="stacksyntaxexample">6<span class="stacksyntaxexamplehighlight">*</span>a</span>.',
                $state->errors);
        $this->assertEquals('', $state->lvars);
    }

    public function test_render_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'x^2');
        $el->set_parameter('options', 'allowempty');
        $this->assertEquals('<div class="matrixsquarebrackets"><textarea name="stack1__ans1" id="stack1__ans1" ' .
                'autocapitalize="none" spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" ' .
                'rows="5" cols="5" data-stack-input-type="varmatrix" data-stack-input-decimal-separator="." ' .
                'data-stack-input-list-separator=","></textarea></div>',
                $el->render(new stack_input_state(stack_input::VALID, [], '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('options', 'allowempty');
        $inputvals = [
            'ans1' => "",
        ];
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', new stack_cas_security());
        $this->assertEquals(stack_input::SCORE, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix(EMPTYCHAR)', $state->contentsmodified);
        $this->assertEquals('',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "",
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
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "1 2\nx",
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

    public function test_validate_student_response_valid_logs() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = [
            'ans1' => "log(9)^2*y^2*9^(x*y) log(9)^2*x*y*9^(x*y)+log(9)*9^(x*y)\n" .
            "log(9)^2*x*y*9^(x*y)+log(9)*9^(x*y) log(9)^2*x^2*9^(x*y)",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([log(9)^2*y^2*9^(x*y),log(9)^2*x*y*9^(x*y)+log(9)*9^(x*y)],'.
            '[log(9)^2*x*y*9^(x*y)+log(9)*9^(x*y),log(9)^2*x^2*9^(x*y)])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} \ln ^2\left(9\right)\cdot y^2\cdot 9^{x\cdot y} & ' .
            '\ln ^2\left(9\right)\cdot x\cdot y\cdot 9^{x\cdot y}+\ln \left( 9 \right)\cdot 9^{x\cdot y} \\\\ ' .
            '\ln ^2\left(9\right)\cdot x\cdot y\cdot 9^{x\cdot y}+\ln \left( 9 \right)\cdot 9^{x\cdot y} & ' .
            '\ln ^2\left(9\right)\cdot x^2\cdot 9^{x\cdot y} \end{array}\right] \]',
            $state->contentsdisplayed);
    }

    public function test_validate_student_response_decimals_dot() {
        $options = new stack_options();
        $options->set_option('decimals', '.');
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('forbidFloats', false);
        $inputvals = [
            'ans1' => "x 2.7\n sqrt(2) 3.14",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([x,2.7],[sqrt(2),3.14])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} x & 2.7 \\\\ \sqrt{2} & 3.14 \end{array}\right] \]',
            $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);
    }

    public function test_validate_student_response_decimals_continental() {
        $options = new stack_options();
        $options->set_option('decimals', ',');
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('forbidFloats', false);
        $inputvals = [
            'ans1' => "x 2,7\n sqrt(2) 3,14",
        ];
        $state = $el->validate_student_response($inputvals, $options, 'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([x,2.7],[sqrt(2),3.14])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} x & 2{,}7 \\\\ \sqrt{2} & 3{,}14 \end{array}\right] \]',
            $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);
    }

    public function test_validate_forbid_sin() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M', $options);
        $el->set_parameter('forbidWords', 'int, sin, diff');
        // We need to set the sameType to allow matrix within matrix.
        $el->set_parameter('sameType', false);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1' => "a1 a_2\n 1+sin(x) abc_45",
        ];
        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">sin</span>.', $state->errors);
        $this->assertEquals('matrix([a1,a_2],[EMPTYCHAR,abc_45])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([a1,a_2],[1+sin(x),abc_45])</span>',
            $state->contentsdisplayed);

        // Matrix inside should be accepted.
        $inputvals = [
            'ans1' => "a b\n c matrix([a,b],[c,d])"
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
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M', $options);
        // Matrix here should not forbid the top-level matrix.
        $el->set_parameter('forbidWords', 'matrix, sin, diff');
        $el->set_parameter('sameType', false);
        $el->adapt_to_model_answer('matrix([null,null],[null,null])');
        $inputvals = [
            'ans1' => "a1 a_2\n 1+x^2 abc_45",
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
            'ans1' => "a b\n c matrix([a,b],[c,d])"
        ];
        $state = $el->validate_student_response($inputvals, $options,
            'matrix([a,b],[c,d])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('forbiddenFunction', $state->note);
        $this->assertEquals('Forbidden function: <span class="stacksyntaxexample">matrix</span>.', $state->errors);
        $this->assertEquals('matrix([a,b],[c,EMPTYCHAR])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([a,b],[c,matrix([a,b],[c,d])])</span>',
            $state->contentsdisplayed);
    }
}
