<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
 * Unit tests for stack_answertest_general_cas.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../stack/answertest/controller.class.php');
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/answertest/at_general_cas.class.php');
require_once(__DIR__ . '/../locallib.php');


/**
 * Unit tests for stack_answertest_general_cas.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    // StackAddFeedback("","ATInt_generic",StackDISP(SBd,"d"),StackDISP(v,"i"),StackDISP(SAd,"d"));
    //
    // There is a lot more going on in the real answer test (such as stripping
    // of constants of integration) but this is enough for now.....
    //
    // StackDISP(SBd,"d") creates a *string* of the displayed/inline form of
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
                '!quot!\[ \left[\begin{array}{cc} 1 & 2 \\\\ {\color{red}{\underline{2}}} & 4 \end{array}\right]\]!quot! );';
        $this->assertEquals($fb, $at->get_at_feedback());

        $fbt = 'The entries underlined in red below are those that are incorrect. ' .
                '\[ \left[\begin{array}{cc} 1 & 2 \\\\ {\color{red}{\underline{2}}} & 4 \end{array}\right]\]';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_int_feedback_1() {
        $at = new stack_answertest_general_cas('((5*%e^7*x-%e^7)*%e^(5*x))',
                '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 'ATInt', true, 'x', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'stack_trans(\'ATInt_generic\' , !quot!\[\frac{e^{5\cdot x+7}}{5}+\frac{\left(5\cdot e^7\cdot x-e^7\right) '.
               '\cdot e^{5\cdot x}}{5}\]!quot!  , !quot!\(x\)!quot!  , '.
               '!quot!\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\]!quot! );';
        $this->assertEquals($fbt, $at->get_at_feedback());

        $fbt = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: '.
               '\[\frac{e^{5\cdot x+7}}{5}+\frac{\left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}}{5}\]  '.
               'In fact, the derivative of your answer, with respect to \(x\) is: '.
               '\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\] '.
               'so you must have done something wrong!';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_stack_maxima_int_feedback_2() {
        $at = new stack_answertest_general_cas('((5*%e^7*x-%e^7)*%e^(5*x))',
                '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 'ATInt', true, '[x,x*%e^(5*x+7)]', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());

        $fbt = 'stack_trans(\'ATInt_generic\' , !quot!\[x\cdot e^{5\cdot x+7}\]!quot!  , !quot!\(x\)!quot!  , '.
               '!quot!\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\]!quot! );';
        $this->assertEquals($fbt, $at->get_at_feedback());

        $fbt = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: '.
               '\[x\cdot e^{5\cdot x+7}\]  In fact, the derivative of your answer, with respect to \(x\) is: '.
               '\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\] '.
               'so you must have done something wrong!';
        $this->assertContentWithMathsEquals($fbt, stack_maxima_translate($at->get_at_feedback()));
    }

    public function test_is_true_numsigfigs() {
        // This example has caused problems in Maxima in the past.
        $at = new stack_answertest_general_cas('0.1667', '0.1667', 'ATNumSigFigs', true, '4', null, true, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_numsigfigs() {
        // This example has caused problems in Maxima in the past.
        $at = new stack_answertest_general_cas('0.1660', '0.1667', 'ATNumSigFigs', true, '4', null, true, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_true_units() {
        $at = new stack_answertest_general_cas('3.2*m/s', '3.2*m/s', 'ATUnits', true, '2', null, false, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
    }

    public function test_is_false_units() {
        $at = new stack_answertest_general_cas('3.1*m/s', '3.2*m/s', 'ATUnits', true, '2', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
    }

    public function test_is_false_missingunits() {
        $at = new stack_answertest_general_cas('3.1', '3.2*m/s', 'ATUnits', true, '2', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_SA_no_units.', $at->get_at_answernote());
    }

    public function test_is_false_wrongunits() {
        $at = new stack_answertest_general_cas('3.2*g', '3.2*m/s', 'ATUnits', true, '2', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_incompatible_units. ATUnits_correct_numerical.', $at->get_at_answernote());
    }

    public function test_is_false_badunits() {
        $at = new stack_answertest_general_cas('3.1+g', '3.2*m/s', 'ATUnits', true, '2', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_SA_bad_units.', $at->get_at_answernote());
    }

    public function test_is_true_compatibleunits() {
        $at = new stack_answertest_general_cas('32*g', '0.032*kg', 'ATUnits', true, '2', null, false, true);
        $this->assertTrue($at->do_test());
        $this->assertEquals(1, $at->get_at_mark());
        $this->assertEquals('ATUnits_compatible_units.', $at->get_at_answernote());
    }

    public function test_is_true_compatibleunits_strict() {
        $at = new stack_answertest_general_cas('32*g', '0.032*kg', 'ATUnitsStrict', true, '2', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_compatible_units.', $at->get_at_answernote());
    }

    public function test_is_false_compatibleunits() {
        $at = new stack_answertest_general_cas('0.032*g', '0.032*kg', 'ATUnits', true, '2', null, false, true);
        $this->assertFalse($at->do_test());
        $this->assertEquals(0, $at->get_at_mark());
        $this->assertEquals('ATUnits_compatible_units. ATUnits_correct_numerical.', $at->get_at_answernote());
    }
}
