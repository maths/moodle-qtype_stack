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

    public function test_is_true_for_equivalent_expressions_diff() {
        $at = new stack_answertest_general_cas('2*x', '2*x', 'ATDiff', true, 'x', null);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_equivalent_expressions_diff() {
        $at = new stack_answertest_general_cas('x^3/3', '2*x', 'ATDiff', true, 'x', null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_diff() {
        $at = new stack_answertest_general_cas('(x+1)^2', '2*x', 'ATDiff', true, '', null);
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_algequiv() {
        $at = new stack_answertest_general_cas('1', '1', 'ATAlgEquiv');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('', $at->get_at_feedback());
    }

    public function test_is_false_for_unequal_expressions_algequiv() {
        $at = new stack_answertest_general_cas('x^2+2*x-1', '(x+1)^2', 'ATAlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('', $at->get_at_feedback());
    }

    public function test_is_false_for_expressions_with_different_type_algequiv() {
        $at = new stack_answertest_general_cas('(x+1)^2', '[a,b,c]', 'ATAlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals("stack_trans('ATAlgEquiv_SA_not_list');", $at->get_at_feedback());
        $this->assertEquals("ATAlgEquiv_SA_not_list.", $at->get_at_answernote());
    }

    public function test_algequivfeedback_1() {
        $at = new stack_answertest_general_cas('[1,2]', '[1,2,3]', 'ATAlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals("stack_trans('ATList_wronglen' , !quot!\\(3\\)!quot!  , !quot!\\(2\\)!quot! );",
                $at->get_at_feedback());
        $this->assertEquals("ATList_wronglen.", $at->get_at_answernote());
    }

    public function test_algequivfeedback_2() {
        $at = new stack_answertest_general_cas('x', '{1,2,3}', 'ATAlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals("stack_trans('ATAlgEquiv_SA_not_set');", $at->get_at_feedback());
        $this->assertEquals("ATAlgEquiv_SA_not_set.", $at->get_at_answernote());
    }

    public function test_algequivfeedback_3() {
        $at = new stack_answertest_general_cas('{1,2}', '{1,2,3}', 'ATAlgEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals("stack_trans('ATSet_wrongsz' , !quot!\\(3\\)!quot!  , !quot!\\(2\\)!quot! );",
                $at->get_at_feedback());
        $this->assertEquals("ATSet_wrongsz.", $at->get_at_answernote());
    }

    public function test_is_true_for_equal_expressions_comass() {
        $at = new stack_answertest_general_cas('x+y', 'x+y', 'ATEqualComAss', false, null, null, false);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_comass() {
        $at = new stack_answertest_general_cas('x+x', '2*x', 'ATEqualComAss', false, null, null, false);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_for_expressions_with_different_type_comass() {
        $at = new stack_answertest_general_cas('(x+1)^2', '[a,b,c]', 'ATEqualComAss', false, null, null, false);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_caseq() {
        $at = new stack_answertest_general_cas('x+y', 'x+y', 'ATCASEqual', false);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_caseq() {
        $at = new stack_answertest_general_cas('(1-x)^2', '(x-1)^2', 'ATCASEqual', false);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_for_expressions_with_different_type_caseq() {
        $at = new stack_answertest_general_cas('(x+1)^2', '[a,b,c]', 'ATCASEqual', false);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_sametype() {
        $at = new stack_answertest_general_cas('x+1', 'x^3+x', 'ATSameType');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_sametype() {
        $at = new stack_answertest_general_cas('x^2+2*x-1', '{(x+1)^2}', 'ATSameType');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_substequiv() {
        $at = new stack_answertest_general_cas('a^2+b^2=c^2', 'x^2+y^2=z^2', 'ATSubstEquiv');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_substequiv() {
        $at = new stack_answertest_general_cas('2*x', '3*z', 'ATSubstEquiv');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_expanded() {
        $at = new stack_answertest_general_cas('x^2+2*x-1', 'x^2+2*x-1', 'ATExpanded');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_expanded() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATExpanded');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expression_facforms() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATFacForm', true, 'x', null);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_unequal_expressions_facform() {
        $at = new stack_answertest_general_cas('x^2+2*x+1', '(x+1)^2', 'ATFacForm', true, 'x', null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_facform() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATFacForm', true, '', null);
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_atsinglefrac() {
        $at = new stack_answertest_general_cas('1/(x*(x+1))', '1/(x*(x+1))', 'ATSingleFrac', false, '', null, false);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_atsinglefrac() {
        $at = new stack_answertest_general_cas('1/n+1/(n+1)', '1/n+1/(n+1)', 'ATSingleFrac', false, '', null, false);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_partfrac() {
        $at = new stack_answertest_general_cas('1/n+1/(n+1)', '1/n+1/(n+1)', 'ATPartFrac', true, 'n');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_partfrac() {
        $at = new stack_answertest_general_cas('1/(x*(x+1))', '1/(x*(x+1))', 'ATPartFrac', true, 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_partfrac() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATPartFrac', true, '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals(array(true, ''), $at->validate_atoptions('x'));
    }

    public function test_is_true_for_completed_quadratics_compsquare() {
        $at = new stack_answertest_general_cas('(x-1)^2-2', '(x-1)^2-2', 'ATCompSquare', true, 'x');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_wrong_form_compsquare() {
        $at = new stack_answertest_general_cas('x^2+2*x+1', '(x+1)^2', 'ATCompSquare', true, 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_compsquare() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATCompSquare', true, '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_gt() {
        $at = new stack_answertest_general_cas('2', '1', 'ATGT');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_true_for_equal_expressions_gte() {
        $at = new stack_answertest_general_cas('2', '1', 'ATGTE');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_true_for_equivalent_expressions_int() {
        $at = new stack_answertest_general_cas('x^3/3+c', 'x^3/3', 'ATInt', true, 'x');
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_for_equivalent_expressions_int() {
        $at = new stack_answertest_general_cas('x^3/3', '2*x', 'ATInt', true, 'x');
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_null_for_missing_option_int() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATInt', true, '');
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_invalid_option_int() {
        $at = new stack_answertest_general_cas('(x+1)^2', '(x+1)^2', 'ATInt', true, '(x', null, true, true);
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertTrue($at->required_atoptions());

        list ($valid, $err) = $at->validate_atoptions('x');
        $this->assertTrue($valid);
        $this->assertEquals('', $err);

        list ($valid, $err) = $at->validate_atoptions('2x');
        $this->assertFalse($valid);
        $this->assertEquals("You seem to be missing * characters. Perhaps you meant to type " .
                "<span class=\"stacksyntaxexample\">2<font color=\"red\">*</font>x</span>.", $err);
    }

    public function test_is_true_numabsolute() {
        $at = new stack_answertest_general_cas('1.05', '1', 'ATNumAbsolute', true, '0.05', null, true, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_numabsolute() {
        $at = new stack_answertest_general_cas('1.0501', '1', 'ATNumAbsolute', true, '0.01', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_missingopt_numabsolute() {
        $at = new stack_answertest_general_cas('1.05', '1', 'ATNumAbsolute', true);
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_numrelative() {
        $at = new stack_answertest_general_cas('1.05', '1', 'ATNumRelative', true, '0.05', null, true, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_numrelative() {
        $at = new stack_answertest_general_cas('1.0501', '1', 'ATNumRelative', true, '0.01', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_missingopt_numrelative() {
        $at = new stack_answertest_general_cas('1.05', '1', 'ATNumRelative', true);
        $this->assertNull($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_invalidopt_numrelative() {
        $at = new stack_answertest_general_cas('1.05', '1', 'ATNumRelative', true, 'x', null, true, true);
        $this->assertNull($at->do_test());
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
    // "stack_trans('ATInt_generic' , !quot!\\[x^5\\]!quot!  , !quot!\\(x\\)!quot!  , !quot!\\[6\\cdot x^5\\]!quot! ); "
    // which gets passed back into PHP. The strings !quot! need to be replaced
    // by actual "s.  This has proved to be too complex to protect all the way
    // through the Maxima and PHP code with \s on all platforms.
    //
    // This needs to be converted into something which can be translated by Moodle.
    // This is the role of stack_maxima_translate in locallib.php.
    // @codingStandardsIgnoreEND
    public function test_stack_maxima_translate_int() {
        $at = new stack_answertest_general_cas('x^6', 'x^6/6', 'ATInt', true, 'x', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fb = "stack_trans('ATInt_generic' , !quot!\\[x^5\\]!quot!  , !quot!\\(x\\)!quot!  , !quot!\\[6\\cdot x^5\\]!quot! );";
        $this->assertEquals($fb, $at->get_at_feedback());

        $fbt = 'The derivative of your answer should be equal to the expression ' .
                'that you were asked to integrate, that was: \[x^5\]  In fact, ' .
                'the derivative of your answer, with respect to \(x\) is: ' .
                '\[6\cdot x^5\] so you must have done something wrong!';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_translate_algequiv_list() {
        // This test points out which element in the list is incorrect.
        $at = new stack_answertest_general_cas('[x^2,x^2,x^4]', '[x^2,x^3,x^4]', 'ATAlgEquiv',
                false, '', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fb = 'stack_trans(\'ATList_wrongentries\' , !quot!\[\left[ x^2 , {\color{red}{\underline{x^2}}} , x^4 \right] \]!quot! );';
        $this->assertEquals($fb, $at->get_at_feedback());

        $fbt = 'The entries underlined in red below are those that are incorrect. ' .
                '\[\left[ x^2 , {\color{red}{\underline{x^2}}} , x^4 \right] \]';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_translate_algequiv_matrix() {
        // Matrices have newline characters in them.
        $at = new stack_answertest_general_cas('matrix([1,2],[2,4])', 'matrix([1,2],[3,4])', 'ATAlgEquiv',
                false, '', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fb = 'stack_trans(\'ATMatrix_wrongentries\' , ' .
                '!quot!\[\left[\begin{array}{cc} 1 & 2 \\\\ {\color{red}{\underline{2}}} & 4 \end{array}\right]\]!quot! );';
        $this->assertEquals($fb, $at->get_at_feedback());

        $fbt = 'The entries underlined in red below are those that are incorrect. ' .
                '\[\left[\begin{array}{cc} 1 & 2 \\\\ {\color{red}{\underline{2}}} & 4 \end{array}\right]\]';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_int_feedback_1() {
        $at = new stack_answertest_general_cas('((5*%e^7*x-%e^7)*%e^(5*x))',
                '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 'ATInt', true, 'x', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'stack_trans(\'ATInt_generic\' , !quot!\[\frac{e^{5\cdot x+7}}{5}+\frac{\left(5\cdot e^7\cdot x-e^7\right)'.
               '\cdot e^{5\cdot x}}{5}\]!quot!  , !quot!\(x\)!quot!  , '.
               '!quot!\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right)\cdot e^{5\cdot x}\]!quot! );';
        $this->assertEquals($fbt, $at->get_at_feedback());

        $fbt = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: '.
               '\[\frac{e^{5\cdot x+7}}{5}+\frac{\left(5\cdot e^7\cdot x-e^7\right)\cdot e^{5\cdot x}}{5}\]  '.
               'In fact, the derivative of your answer, with respect to \(x\) is: '.
               '\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right)\cdot e^{5\cdot x}\] '.
               'so you must have done something wrong!';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_int_feedback_2() {
        $at = new stack_answertest_general_cas('((5*%e^7*x-%e^7)*%e^(5*x))',
                '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 'ATInt', true, '[x,x*%e^(5*x+7)]', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'stack_trans(\'ATInt_generic\' , !quot!\[x\cdot e^{5\cdot x+7}\]!quot!  , !quot!\(x\)!quot!  , '.
               '!quot!\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right)\cdot e^{5\cdot x}\]!quot! );';
        $this->assertEquals($fbt, $at->get_at_feedback());

        $fbt = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: '.
               '\[x\cdot e^{5\cdot x+7}\]  In fact, the derivative of your answer, with respect to \(x\) is: '.
               '\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right)\cdot e^{5\cdot x}\] '.
               'so you must have done something wrong!';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_is_true_units_relative() {
        $at = new stack_answertest_general_cas('3.1*m/s', '3.2*m/s', 'ATUnitsRelative', true, '0.1', null, false, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_is_false_units_relative() {
        $at = new stack_answertest_general_cas('3.0*m/s', '3.2*m/s', 'ATUnitsRelative', true, '0.05', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_is_true_units_absolute() {
        $at = new stack_answertest_general_cas('3.1*m/s', '3.2*m/s', 'ATUnitsAbsolute', true, '0.2', null, false, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_is_false_units_absolute() {
        $at = new stack_answertest_general_cas('3.1*m/s', '3.2*m/s', 'ATUnitsAbsolute', true, '0.05', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_units_match.', $at->get_at_answernote());
    }

    public function test_equiv_true() {
        $at = new stack_answertest_general_cas('[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]', '[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]',
                'ATEquiv', true, 'null', null);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]', $at->get_at_answernote());
        $fbt = '\[\begin{array}{lll} &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)=0& '.
            '\cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ or }}\, x=-1& \cr \end{array}\]';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_equiv_false() {
        $at = new stack_answertest_general_cas('[x^2-1=0,(x-1)*(x+1)=0,x=i or x=-1]', '[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]',
                'ATEquiv', true, 'null', null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('[EMPTYCHAR,EQUIVCHAR,QMCHAR]', $at->get_at_answernote());
        $fbt = '\[\begin{array}{lll} &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)=0&'.
            ' \cr \color{red}{?}&x=\mathrm{i}\,{\mbox{ or }}\, x=-1& \cr \end{array}\]';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_equiv_comment() {
        $at = new stack_answertest_general_cas('[x^2-1=0,(x-1)*(x+1)=0,"Could be",x=i or x=-1]',
                '[x^2-1=0,(x-1)*(x+1)=0,x=1 or x=-1]', 'ATEquiv', true, 'null', null);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('[EMPTYCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR]', $at->get_at_answernote());
        $fbt = '\[\begin{array}{lll} &x^2-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)=0& '.
            '\cr  &\mbox{Could be}& \cr  &x=\mathrm{i}\,{\mbox{ or }}\, x=-1& \cr \end{array}\]';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }
}
