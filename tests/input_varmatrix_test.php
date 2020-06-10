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
class stack_varmatrix_input_test extends qtype_stack_testcase {

    public function test_render_blank() {
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $this->assertEquals('<div class="matrixroundbrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="5">' .
                '</textarea></div>',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_no_errors_if_garbled() {
        // The teacher does not need to use a matrix here but there will be errors later!
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');

        $this->assertEquals('<div class="matrixroundbrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="5">' .
                '</textarea></div>',
                $el->render(new stack_input_state(stack_input::BLANK, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_render_syntax_hint() {
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('syntaxHint', 'matrix([a,b],[?,d])');
        $this->assertEquals('<div class="matrixroundbrackets"><textarea name="ans1" id="ans1" autocapitalize="none" ' .
                'spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" rows="5" cols="10">a b' ."\n" .
                '? d</textarea></div>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'ans1', false, null));
    }

    public function test_validate_student_response_na() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $state = $el->validate_student_response(array(), $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::BLANK, $state->status);
        $this->assertEquals('', $state->contentsmodified);
        $this->assertEquals('', $state->contentsdisplayed);
    }

    public function test_validate_student_response_valid() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = array(
            'ans1' => "1 2 3\n4 a a+b",
        );
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
        $inputvals = array(
            'ans1' => "0\n0",
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([0],[0])', new stack_cas_security());
        $this->assertEquals(stack_input::VALID, $state->status);
        $this->assertEquals('matrix([0],[0])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{c} 0 \\\\ 0 \end{array}\right] \]',
                $state->contentsdisplayed);
    }

    public function test_validate_student_response_invalid_one_blank() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = array(
            'ans1' => "1 2 3\n4   6",
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([1,2,3],[4,6,QMCHAR])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{ccc} 1 & 2 & 3 \\\\ 4 & 6 & \color{red}{?} \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = array(
            'ans1' => "1 2x 3\n4 5 6",
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars', $state->note);
        $this->assertEquals('matrix(EMPTYCHAR,[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x,3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_bracket() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = array(
            'ans1' => "1 2x) 3\n4 5 6",
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | missingLeftBracket', $state->note);
        $this->assertEquals('matrix(EMPTYCHAR,[4,5,6])', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x),3],[4,5,6])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">[1,2*x),3]</span>.', $state->errors);
        $this->assertEquals('', $state->lvars);
    }

    public function test_validate_student_response_invalid_multiple() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $inputvals = array(
            'ans1' => "1 2x) 3\n4 5 6a",
        );
        $state = $el->validate_student_response($inputvals, $options, 'matrix([1,2,3],[3,4,5])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('missing_stars | missingLeftBracket', $state->note);
        $this->assertEquals('matrix(EMPTYCHAR,EMPTYCHAR)', $state->contentsmodified);
        $this->assertEquals('<span class="stacksyntaxexample">matrix([1,2x),3],[4,5,6a])</span>',
                $state->contentsdisplayed);
        $this->assertEquals('You have a missing left bracket <span class="stacksyntaxexample">(</span> in the expression: ' .
                '<span class="stacksyntaxexample">[1,2*x),3]</span>. ' .
                'You seem to be missing * characters. Perhaps you meant to type ' .
                '<span class="stacksyntaxexample">[4,5,6<span class="stacksyntaxexamplehighlight">*</span>a]</span>.',
                $state->errors);
        $this->assertEquals('', $state->lvars);
    }

    public function test_render_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'x^2');
        $el->set_parameter('options', 'allowempty');
        $this->assertEquals('<div class="matrixroundbrackets"><textarea name="stack1__ans1" id="stack1__ans1" ' .
                'autocapitalize="none" spellcheck="false" class="varmatrixinput" size="5.5" style="width: 4.6em" ' .
                'rows="5" cols="5"></textarea></div>',
                $el->render(new stack_input_state(stack_input::VALID, array(), '', '', '', '', ''),
                        'stack1__ans1', false, null));
    }

    public function test_validate_student_response_blank_allowempty() {
        $options = new stack_options();
        $el = stack_input_factory::make('varmatrix', 'ans1', 'M');
        $el->set_parameter('options', 'allowempty');
        $inputvals = array(
            'ans1' => "",
        );
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
        $inputvals = array(
            'ans1' => "",
        );
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
        $inputvals = array(
            'ans1' => "1 2\nx",
        );
        $state = $el->validate_student_response($inputvals, $options,
                'matrix([null,null],[null,null])', new stack_cas_security());
        $this->assertEquals(stack_input::INVALID, $state->status);
        $this->assertEquals('', $state->note);
        $this->assertEquals('matrix([1,2],[x,QMCHAR])', $state->contentsmodified);
        $this->assertEquals('\[ \left[\begin{array}{cc} 1 & 2 \\\\ x & \color{red}{?} \end{array}\right] \]',
                $state->contentsdisplayed);
        $this->assertEquals('\( \left[ x \right]\) ', $state->lvars);
    }
}
