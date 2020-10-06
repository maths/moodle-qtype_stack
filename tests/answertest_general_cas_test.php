<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk//
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

// Unit tests for stack_answertest_general_cas.
//
// @copyright  2012 The University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../stack/answertest/controller.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/answertest/at_general_cas.class.php');
require_once(__DIR__ . '/../locallib.php');

/**
 * @group qtype_stack
 */
class stack_answertest_general_cas_test extends qtype_stack_testcase {

    public function stack_answertest_general_cas_builder($sans, $tans, $atname,
            $atop = 'null', $options = null) {
        $sa = stack_ast_container::make_from_teacher_source($sans, '', new stack_cas_security());
        $ta = stack_ast_container::make_from_teacher_source($tans, '', new stack_cas_security());
        $op = stack_ast_container::make_from_teacher_source($atop, '', new stack_cas_security());

        return new stack_answertest_general_cas($sa, $ta, $atname, $op, $options);
    }

    public function test_is_true_for_equivalent_expressions_diff() {
        $at = $this->stack_answertest_general_cas_builder('2*x', '2*x', 'Diff', 'x');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_equivalent_expressions_diff() {
        $at = $this->stack_answertest_general_cas_builder('x^3/3', '2*x', 'Diff', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_diff() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '2*x', 'Diff', '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_algequiv() {
        $at = $this->stack_answertest_general_cas_builder('1', '1', 'AlgEquiv');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('', $at->get_at_feedback());
    }

    public function test_is_false_for_unequal_expressions_algequiv() {
        $at = $this->stack_answertest_general_cas_builder('x^2+2*x-1', '(x+1)^2', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('', $at->get_at_feedback());
    }

    public function test_is_false_for_expressions_with_different_type_algequiv() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '[a,b,c]', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals("Your answer should be a list, but is not. Note that the syntax to enter a list" .
                " is to enclose the comma separated values with square brackets.", $at->get_at_feedback());
        $this->assertEquals("ATAlgEquiv_SA_not_list.", $at->get_at_answernote());
    }

    public function test_algequivfeedback_1() {
        $at = $this->stack_answertest_general_cas_builder('[1,2]', '[1,2,3]', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('Your list should have <span class="filter_mathjaxloader_equation">' .
                '<span class="nolink">\(3\)</span></span> elements, but it actually has ' .
                '<span class="filter_mathjaxloader_equation"><span class="nolink">\(2\)</span></span>.',
                $at->get_at_feedback());
        $this->assertEquals("ATList_wronglen.", $at->get_at_answernote());
    }

    public function test_algequivfeedback_2() {
        $at = $this->stack_answertest_general_cas_builder('x', '{1,2,3}', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals("Your answer should be a set, but is not. Note that the syntax to enter a set " .
                "is to enclose the comma separated values with curly brackets.", $at->get_at_feedback());
        $this->assertEquals("ATAlgEquiv_SA_not_set.", $at->get_at_answernote());
    }

    public function test_algequivfeedback_3() {
        $at = $this->stack_answertest_general_cas_builder('{1,2}', '{1,2,3}', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $s = "stack_trans('ATSet_wrongsz' , !quot!\\(3\\)!quot!  , !quot!\\(2\\)!quot! );";
        $this->assertEquals(stack_maxima_translate($s), $at->get_at_feedback());
        $this->assertEquals("ATSet_wrongsz.", $at->get_at_answernote());
    }

    public function test_is_true_for_equal_expressions_comass() {
        $at = $this->stack_answertest_general_cas_builder('x+y', 'x+y', 'EqualComAss');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_comass() {
        $at = $this->stack_answertest_general_cas_builder('x+x', '2*x', 'EqualComAss');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_for_expressions_with_different_type_comass() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '[a,b,c]', 'EqualComAss');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_caseq() {
        $at = $this->stack_answertest_general_cas_builder('x+y', 'x+y', 'CasEqual');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_caseq() {
        $at = $this->stack_answertest_general_cas_builder('(1-x)^2', '(x-1)^2', 'CasEqual');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_for_expressions_with_different_type_caseq() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '[a,b,c]', 'CasEqual');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_sametype() {
        $at = $this->stack_answertest_general_cas_builder('x+1', 'x^3+x', 'SameType');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_sametype() {
        $at = $this->stack_answertest_general_cas_builder('x^2+2*x-1', '{(x+1)^2}', 'SameType');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_substequiv() {
        $at = $this->stack_answertest_general_cas_builder('a^2+b^2=c^2', 'x^2+y^2=z^2', 'SubstEquiv', '[]');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_substequiv() {
        $at = $this->stack_answertest_general_cas_builder('2*x', '3*z', 'SubstEquiv', '[]');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_substequiv_op_true() {
        $at = $this->stack_answertest_general_cas_builder('A*cos(t)+B', 'P*cos(t)+Q', 'SubstEquiv', '[t]');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATSubstEquiv_Subst [A = P,B = Q].', $at->get_at_answernote());
        $this->assertEquals('ATSubstEquiv(A*cos(t)+B, P*cos(t)+Q, [t]);', $at->get_trace(false));
    }

    public function test_is_substequiv_op_false() {
        $at = $this->stack_answertest_general_cas_builder('A*cos(x)+B', 'P*cos(t)+Q', 'SubstEquiv', '[t]');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('', $at->get_at_answernote());
        $this->assertEquals('ATSubstEquiv(A*cos(x)+B, P*cos(t)+Q, [t]);', $at->get_trace(false));
    }

    public function test_is_true_for_equal_expressions_expanded() {
        $at = $this->stack_answertest_general_cas_builder('x^2+2*x-1', 'x^2+2*x-1', 'Expanded');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_expanded() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'Expanded');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATExpanded((x+1)^2, (x+1)^2, null);', $at->get_trace(false));
    }

    public function test_is_true_for_equal_expression_facforms() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'FacForm', 'x');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATFacForm((x+1)^2, (x+1)^2, x);', $at->get_trace(false));
    }

    public function test_is_false_for_unequal_expressions_facform() {
        $at = $this->stack_answertest_general_cas_builder('x^2+2*x+1', '(x+1)^2', 'FacForm', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_facform() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'FacForm', '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_atsinglefrac() {
        $at = $this->stack_answertest_general_cas_builder('1/(x*(x+1))', '1/(x*(x+1))', 'SingleFrac');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_atsinglefrac() {
        $at = $this->stack_answertest_general_cas_builder('1/n+1/(n+1)', '1/n+1/(n+1)', 'SingleFrac');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_partfrac() {
        $at = $this->stack_answertest_general_cas_builder('1/n+1/(n+1)', '1/n+1/(n+1)', 'PartFrac', 'n');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_partfrac() {
        $at = $this->stack_answertest_general_cas_builder('1/(x*(x+1))', '1/(x*(x+1))', 'PartFrac', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATPartFrac(1/(x*(x+1)), 1/(x*(x+1)), x);', $at->get_trace(false));
    }

    public function test_is_null_for_missing_option_partfrac() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'PartFrac', '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals(array(true, ''), $at->validate_atoptions('x'));
    }

    public function test_is_true_for_completed_quadratics_compsquare() {
        $at = $this->stack_answertest_general_cas_builder('(x-1)^2-2', '(x-1)^2-2', 'CompSquare', 'x');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_wrong_form_compsquare() {
        $at = $this->stack_answertest_general_cas_builder('x^2+2*x+1', '(x+1)^2', 'CompSquare', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATCompSquare(x^2+2*x+1, (x+1)^2, x);', $at->get_trace(false));
    }

    public function test_is_null_for_missing_option_compsquare() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'CompSquare', '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_gt() {
        $at = $this->stack_answertest_general_cas_builder('2', '1', 'GT');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_gte() {
        $at = $this->stack_answertest_general_cas_builder('2', '1', 'GTE');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_true_for_equivalent_expressions_int() {
        $at = $this->stack_answertest_general_cas_builder('x^3/3+c', 'x^3/3', 'Int', 'x');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_equivalent_expressions_int() {
        $at = $this->stack_answertest_general_cas_builder('x^3/3', '2*x', 'Int', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_int() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'Int', '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_invalid_option_int() {
        $at = $this->stack_answertest_general_cas_builder('(x+1)^2', '(x+1)^2', 'Int', '(x');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertTrue(stack_ans_test_controller::required_atoptions('Int'));

        list ($valid, $err) = $at->validate_atoptions('x');
        $this->assertTrue($valid);
        $this->assertEquals('', $err);

        list ($valid, $err) = $at->validate_atoptions('2x');
        $this->assertFalse($valid);
        $this->assertEquals('You seem to be missing * characters. Perhaps you meant to type ' .
                '<span class="stacksyntaxexample">2<span class="stacksyntaxexamplehighlight">*</span>x</span>.', $err);
    }

    public function test_is_true_numabsolute() {
        $at = $this->stack_answertest_general_cas_builder('1.05', '1', 'NumAbsolute', '0.05');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_numabsolute() {
        $at = $this->stack_answertest_general_cas_builder('1.0501', '1', 'NumAbsolute', '0.01');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATNumAbsolute(1.0501, 1, 0.01);', $at->get_trace(false));
    }

    public function test_is_missingopt_numabsolute() {
        $at = $this->stack_answertest_general_cas_builder('1.05', '1', 'NumAbsolute');
        // If the option is missing then we take 5% of the teacher's answer.
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_numrelative() {
        $at = $this->stack_answertest_general_cas_builder('1.05', '1', 'NumRelative', '0.05');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_numrelative() {
        $at = $this->stack_answertest_general_cas_builder('1.0501', '1', 'NumRelative', '0.01');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_missingopt_numrelative() {
        $at = $this->stack_answertest_general_cas_builder('1.05', '1', 'NumRelative');
        // If the option is missing then we take 5%.
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_invalidopt_numrelative() {
        $at = $this->stack_answertest_general_cas_builder('1.05', '1', 'NumRelative', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    // @codingStandardsIgnoreStart

    // Goal:  Have maxima generate a string which will work in the moodle
    // translation system. For example, the student has been asked to integrate
    // x^5 wrt x, and has answered x^6, not x^6/6.
    // The process starts in Maxima (stackmaxima.mac line 1589). Execute the
    // following Maxima code in a STACK-maxima sandbox:
    //
    // make_multsgn(dot);
    // v:x;
    // SA:x^6;
    // SB:x^6/6;
    // SAd:diff(SA,v);
    // SBd:diff(SB,v);
    // StackAddFeedback("","ATInt_generic", stack_disp(SBd,"d"), stack_disp(v,"i"), stack_disp(SAd,"d"));
    //
    // There is a lot more going on in the real answer test (such as stripping
    // of constants of integration) but this is enough for now.....
    //
    // stack_disp(SBd,"d") creates a *string* of the displayed/inline form of
    // variable SBd etc.
    //
    // This generates a string
    // "stack_trans('Int_generic' , !quot!\\[x^5\\]!quot!  , !quot!\\(x\\)!quot!  , !quot!\\[6\\cdot x^5\\]!quot! ); "
    // which gets passed back into PHP. The strings !quot! need to be replaced
    // by actual "s.  This has proved to be too complex to protect all the way
    // through the Maxima and PHP code with \s on all platforms.
    //
    // This needs to be converted into something which can be translated by Moodle.
    // This is the role of stack_maxima_translate in locallib.php.
    // @codingStandardsIgnoreEND
    public function test_stack_maxima_translate_int() {
        $at = $this->stack_answertest_general_cas_builder('x^6', 'x^6/6', 'Int', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'The derivative of your answer should be equal to the expression ' .
                'that you were asked to integrate, that was: \[x^5\] In fact, ' .
                'the derivative of your answer, with respect to \(x\) is: ' .
                '\[6\cdot x^5\] so you must have done something wrong!';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }

    public function test_stack_maxima_translate_algequiv_list() {
        // This test points out which element in the list is incorrect.
        $at = $this->stack_answertest_general_cas_builder('[x^2,x^2,x^4]', '[x^2,x^3,x^4]', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fb = 'stack_trans(\'ATList_wrongentries\' , !quot!\[\left[ x^2 , {\color{red}{\underline{x^2}}} , x^4 \right] \]!quot! );';
        $this->assertEquals(stack_maxima_translate($fb), $at->get_at_feedback());

        $fbt = 'The entries underlined in red below are those that are incorrect. ' .
                '\[\left[ x^2 , {\color{red}{\underline{x^2}}} , x^4 \right] \]';
        $this->assert_content_with_maths_equals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_translate_algequiv_matrix() {
        // Matrices have newline characters in them.
        $at = $this->stack_answertest_general_cas_builder('matrix([1,2],[2,4])', 'matrix([1,2],[3,4])', 'AlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fb = 'stack_trans(\'ATMatrix_wrongentries\' , ' .
                '!quot!\[ \left[\begin{array}{cc} 1 & 2 \\\\ {\color{red}{\underline{2}}} & 4 \end{array}\right]\]!quot! );';
        $this->assertEquals(stack_maxima_translate($fb), $at->get_at_feedback());

        $fbt = 'The entries underlined in red below are those that are incorrect. ' .
                '\[ \left[\begin{array}{cc} 1 & 2 \\\\ {\color{red}{\underline{2}}} & 4 \end{array}\right]\]';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }

    public function test_stack_maxima_int_feedback_1() {
        $at = $this->stack_answertest_general_cas_builder('((5*%e^7*x-%e^7)*%e^(5*x))',
                '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 'Int', 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: '.
               '\[\frac{e^{5\cdot x+7}}{5}+\frac{\left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}}{5}\] '.
               'In fact, the derivative of your answer, with respect to \(x\) is: '.
               '\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\] '.
               'so you must have done something wrong!';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }

    public function test_stack_maxima_int_feedback_2() {
        $at = $this->stack_answertest_general_cas_builder('((5*%e^7*x-%e^7)*%e^(5*x))',
                '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 'Int', '[x,x*%e^(5*x+7)]');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: '.
               '\[x\cdot e^{5\cdot x+7}\] In fact, the derivative of your answer, with respect to \(x\) is: '.
               '\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\] '.
               'so you must have done something wrong!';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }

    public function test_is_true_units_relative() {
        $at = $this->stack_answertest_general_cas_builder('3.1*m/s', '3.2*m/s', 'UnitsRelative', '0.1');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_is_false_units_relative() {
        $at = $this->stack_answertest_general_cas_builder('3.0*m/s', '3.2*m/s', 'UnitsRelative', '0.05');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_is_true_units_absolute() {
        $at = $this->stack_answertest_general_cas_builder('3.1*m/s', '3.2*m/s', 'UnitsAbsolute', '0.2');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_is_false_units_absolute() {
        $at = $this->stack_answertest_general_cas_builder('3.1*m/s', '3.2*m/s', 'UnitsAbsolute', '0.05');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_equiv_true() {
        $at = $this->stack_answertest_general_cas_builder('[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]', '[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]',
                'Equiv', 'null');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]', $at->get_at_answernote());
        $fbt = '\[\begin{array}{lll} &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)=0& '.
            '\cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ or }}\, x=-1& \cr \end{array}\]';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }

    public function test_equiv_false() {
        $at = $this->stack_answertest_general_cas_builder('[x^2-1=0,(x-1)*(x+1)=0,x=i or x=-1]', '[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]',
                'Equiv', 'null');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('[EMPTYCHAR,EQUIVCHAR,QMCHAR]', $at->get_at_answernote());
        $fbt = '\[\begin{array}{lll} &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)=0&'.
            ' \cr \color{red}{?}&x=\mathrm{i}\,{\mbox{ or }}\, x=-1& \cr \end{array}\]';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }

    public function test_equiv_comment() {
        $at = $this->stack_answertest_general_cas_builder('[x^2-1=0,(x-1)*(x+1)=0,"Could be",x=i or x=-1]',
                '[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]', 'Equiv', 'null');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('[EMPTYCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR]', $at->get_at_answernote());
        $fbt = '\[\begin{array}{lll} &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)=0& '.
            '\cr &\mbox{Could be}& \cr &x=\mathrm{i}\,{\mbox{ or }}\, x=-1& \cr \end{array}\]';
        $this->assert_content_with_maths_equals($fbt, $at->get_at_feedback());
    }
}
