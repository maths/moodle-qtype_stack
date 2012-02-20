<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test helper code for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the Stack question type.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('test1', 'test0', 'test2', 'test3');
        //'test4', 'test5', 'test6', 'test7', 'test8', 'test9');
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_test0() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'test-0';
        $q->questionvariables = '';
        $q->questiontext = 'What is $1+1$? #ans1#
                           <IEfeedback>ans1</IEfeedback>
                           <PRTfeedback>firsttree</PRTfeedback>';
        $q->generalfeedback = '';
        $q->qtype = question_bank::get_qtype('stack');

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                'algebraic', 'ans1', '2', array('boxWidth' => 5));

        $q->prts = array();
            $sans = new stack_cas_casstring('ans1', 't');
            $tans = new stack_cas_casstring('2', 't');
            $node = new stack_potentialresponse_node($sans, $tans, 'EqualComAss');
            $node->add_branch(0, '=', 0, '', -1, '', 'firsttree-0-0');
            $node->add_branch(1, '=', 1, '', -1, '', 'firsttree-0-1');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', false, 1, null, array($node));

        $q->options = new stack_options();

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_test1() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'test-1';
        $q->questionvariables = 'n = rand(5)+3; a = rand(5)+3; v = x; p = (v-a)^n; ta = (v-a)^(n+1)/(n+1)';
        $q->questiontext = 'Find
                            \[ \int @p@ d@v@\]
                            #ans1#
                            <IEfeedback>ans1</IEfeedback>
                            <PRTfeedback>PotResTree_1</PRTfeedback>';
        $q->generalfeedback = 'We can either do this question by inspection (i.e. spot the answer)
                               or in a more formal manner by using the substitution
                               \[ u = (@v@-@a@).\]
                               Then, since $\frac{d}{d@v@}u=1$ we have
                               \[ \int @p@ d@v@ = \int u^@n@ du = \frac{u^@n+1@}{@n+1@}+c = @ta@+c.\]';
        $q->qtype = question_bank::get_qtype('stack');

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans1', 'ta+c', array('boxWidth' => 20));

        $q->prts = array();
        $sans = new stack_cas_casstring('ans1', 't');
        $tans = new stack_cas_casstring('ta', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'Int', 'x');
        $node->add_branch(0, '=', 0, '', -1, '', 'PotResTree_1-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'PotResTree_1-0-1');
        $q->prts['PotResTree_1'] = new stack_potentialresponse_tree('PotResTree_1', '', true, 1, null, array($node));

        $q->options = new stack_options();

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_test2() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'test-2';
        $q->questionvariables = '';
        $q->questiontext = 'Expand
                            \[ (x-2)(x-3) = x^2-#ans1# x+#ans2#. \]
                            <IEfeedback>ans1</IEfeedback>
                            <IEfeedback>ans2</IEfeedback>
                            <PRTfeedback>PotResTree_1</PRTfeedback>';
        $q->qtype = question_bank::get_qtype('stack');

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans1', '5', array('boxWidth' => 3));
        $q->interactions['ans2'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans2', '6', array('boxWidth' => 3));

        $q->prts = array();
        $sans = new stack_cas_casstring('x^2-ans1*x+ans2', 't');
        $tans = new stack_cas_casstring('(x-2)*(x-3)', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'PotResTree_1-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'PotResTree_1-0-1');
        $q->prts['PotResTree_1'] = new stack_potentialresponse_tree('PotResTree_1', '', true, 1, null, array($node));

        $q->options = new stack_options();

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_test3() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'test-2';
        $q->questionvariables = '';
        $q->questiontext = '<p>1. Give an example of an odd function by typing
                                  an expression which represents it.
                                  $f_1(x)=$ #ans1#.
                                  <IEfeedback>ans1</IEfeedback>
                                  <PRTfeedback>odd</PRTfeedback></p>
                            <p>2. Give an example of an even function.
                                  $f_2(x)=$ #ans2#.
                                  <IEfeedback>ans2</IEfeedback>
                                  <PRTfeedback>even</PRTfeedback></p>
                            <p>3. Give an example of a function which is odd and even.
                                  $f_3(x)=$ #ans3#.
                                  <IEfeedback>ans3</IEfeedback>
                                  <PRTfeedback>oddeven</PRTfeedback></p>
                            <p>4. Is the answer to 3. unique? #ans4#
                                  (Or are there many different possibilities.)
                                  <IEfeedback>ans4</IEfeedback>
                                  <PRTfeedback>unique</PRTfeedback></p>';
        $q->qtype = question_bank::get_qtype('stack');

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans1', 'x^3', array('boxWidth' => 15));
        $q->interactions['ans2'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans2', 'x^4', array('boxWidth' => 15));
        $q->interactions['ans3'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans3', '0',   array('boxWidth' => 15));
        $q->interactions['ans4'] = stack_interaction_controller::make_element(
                        'boolean',   'ans4', 'true');

        $q->prts = array();
        $sans = new stack_cas_casstring('sa', 't');
        $tans = new stack_cas_casstring('0', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'odd-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'odd-0-1');
        $feedbackvars = new stack_cas_keyval('sa = subst(x=-x,ans1)+ans1');
        $q->prts['PotResTree_odd']     = new stack_potentialresponse_tree('PotResTree_odd',
                '', true, 0.25, $feedbackvars->get_session(), array($node));

        $sans = new stack_cas_casstring('sa', 't');
        $tans = new stack_cas_casstring('0', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'odd-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'odd-0-1');
        $feedbackvars = new stack_cas_keyval('sa = subst(x=-x,ans2)-ans2');
        $q->prts['PotResTree_even']    = new stack_potentialresponse_tree('PotResTree_even',
                '', true, 0.25, $feedbackvars->get_session(), array($node));

        $sans = new stack_cas_casstring('sa1', 't');
        $tans = new stack_cas_casstring('0', 't');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node0->add_branch(0, '=', 0,   '', 1, 'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa1@ \neq 0.\]', 'oddeven-0-0');
        $node0->add_branch(1, '=', 0.5, '', 1, '', 'oddeven-0-1');
        $sans = new stack_cas_casstring('sa2', 't');
        $tans = new stack_cas_casstring('0', 't');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node1->add_branch(0, '+', 0,   '', -1, 'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa2@ \neq 0.\]', 'oddeven-1-0');
        $node1->add_branch(1, '+', 0.5, '', -1, '', 'EVEN');
        $feedbackvars = new stack_cas_keyval('sa1 = subst(x=-x,ans3)+ans3; sa2 = ans3-subst(x=-x,ans3)');
        $q->prts['PotResTree_oddeven'] = new stack_potentialresponse_tree('PotResTree_oddeven',
                '', true, 0.25, $feedbackvars->get_session(), array($node0, $node1));

        $sans = new stack_cas_casstring('ans4', 't');
        $tans = new stack_cas_casstring('true', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'unique-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'unique-0-1');
        $q->prts['PotResTree_unique']  = new stack_potentialresponse_tree('PotResTree_unique',
                '', true, 0.25, null, array($node));

        $q->options = new stack_options();

        return $q;
    }
}
