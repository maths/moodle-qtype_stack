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
        return array(
            'test1', // One input, one PRT, randomised. (Integrate (v - a) ^ n, a, n small random ints.)
            'test0', // One input, one PRT, not randomised. (1 + 1 = 2.)
            'test2', // Two inputs, one PRT, not randomises. (Expand (x - 2)(x - 3).)
            'test3', // Four inputs, four PRTs, not randomised. (Even and odd functions.)
            'test4', // One input, one PRT, not randomised, has a plot. (What is the equation of this graph? x^2.)
            'test5', // Three inputs, three PRTs, one with 4 nodes, randomised. (Three steps, rectangle side length from area.)
        );
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
     * @return qtype_stack_question the question from the test1.xml file.
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
     * @return qtype_stack_question the question from the test2.xml file.
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
     * @return qtype_stack_question the question from the test3.xml file.
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

        $feedbackvars = new stack_cas_keyval('sa = subst(x=-x,ans1)+ans1', null, null, 't');
        $sans = new stack_cas_casstring('sa', 't');
        $tans = new stack_cas_casstring('0', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'odd-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'odd-0-1');
        $q->prts['PotResTree_odd']     = new stack_potentialresponse_tree('PotResTree_odd',
                '', true, 0.25, $feedbackvars->get_session(), array($node));

        $feedbackvars = new stack_cas_keyval('sa = subst(x=-x,ans2)-ans2', null, null, 't');
        $sans = new stack_cas_casstring('sa', 't');
        $tans = new stack_cas_casstring('0', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'odd-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'odd-0-1');
        $q->prts['PotResTree_even']    = new stack_potentialresponse_tree('PotResTree_even',
                '', true, 0.25, $feedbackvars->get_session(), array($node));

        $feedbackvars = new stack_cas_keyval('sa1 = subst(x=-x,ans3)+ans3; sa2 = ans3-subst(x=-x,ans3)');

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

    /**
     * @return qtype_stack_question the question from the test4.xml file.
     */
    public static function make_stack_question_test4() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'test-4';
        $q->questionvariables = 'p = x^2';
        $q->questiontext = 'Below is a sketch of a graph. Find an algebraic expression which represents it.
                            @plot(p,[x,-2,2])@
                            $f(x)=$#ans1#.
                            <IEfeedback>ans1</IEfeedback>
                            <PRTfeedback>plots</PRTfeedback>';
        $q->generalfeedback = 'The graph @plot(p,[x,-2,2])@ has algebraic expression \[ f(x)=@p@. \]';
        $q->qtype = question_bank::get_qtype('stack');

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                        'algebraic', 'ans1', 'x^2', array('boxWidth' => 15));

        $q->prts = array();
        $sans = new stack_cas_casstring('ans1', 't');
        $tans = new stack_cas_casstring('x^2', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, 'Your answer and my answer are plotted below. Look they are different! @plot([p,ans1],[x,-2,2])@', 'plots-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'plots-0-1');
        $q->prts['plots'] = new stack_potentialresponse_tree('plots',
                '', true, 1, null, array($node));

        $q->options = new stack_options();

        return $q;
    }

    /**
    * @return qtype_stack_question the question from the test5.xml file.
    */
    public static function make_stack_question_test5() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'test-5';
        $q->questionvariables = 'rn = -1*(rand(4)+1); rp = 8+rand(6); ar = rn*rp; sg = rn+rp; ' .
                'ta1 = x*(x+sg)=-ar; ta2 = x*(x-sg)=-ar; tas = setify(map(rhs,solve(ta1,x)))';
        $q->questiontext = '<p>A rectangle has length @sg@cm greater than its width.
                            If it has an area of $@abs(ar)@cm^2$, find the dimensions of the rectangle.</p>
                            <p>1. Write down an equation which relates the side lengths to the
                                  area of the rectangle.<br />
                                  #ans1#
                                  <IEfeedback>ans1</IEfeedback>
                                  <PRTfeedback>eq</PRTfeedback></p>
                            <p>2. Solve your equation. Enter your answer as a set of numbers.<br />
                                  #ans2#
                                  <IEfeedback>ans2</IEfeedback>
                                  <PRTfeedback>sol</PRTfeedback></p>
                            <p>3. Hence, find the length of the shorter side.<br />
                                  #ans3# cm
                                  <IEfeedback>ans3</IEfeedback>
                                  <PRTfeedback>short</PRTfeedback></p>';
        $q->generalfeedback = 'If $x$cm is the width then $(x+@sg@)$ is the length.
                               Now the area is $@abs(ar)@cm^2$ and so
                               \[ @x*(x+sg)=-ar@.\]
                               \[ @x^2+sg*x+ar@=0\]
                               \[ @(x+rp)*(x+rn)=0@ \]
                               So that $x=@-rp@$ or $x=@-rn@$. Since lengths are positive quantities $x>0$
                               and we discard the negative root. Hence the length of the shorter side is
                               $x=@-rn@$cm.';
        $q->qtype = question_bank::get_qtype('stack');

        $q->questionnote = '@ta1@, @rp@.';

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                            'algebraic', 'ans1', 'ta1', array('boxWidth' => 15));
        $q->interactions['ans2'] = stack_interaction_controller::make_element(
                            'algebraic', 'ans2', 'tas', array('boxWidth' => 15));
        $q->interactions['ans3'] = stack_interaction_controller::make_element(
                            'algebraic', 'ans3', 'rp', array('boxWidth' => 5));

        $q->prts = array();

        $sans = new stack_cas_casstring('ans1', 't');
        $tans = new stack_cas_casstring('ta1', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'eq-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'eq-0-1');
        $q->prts['eq'] = new stack_potentialresponse_tree('eq',
                    '', true, 0.3333333, null, array($node));

        $feedbackvars = new stack_cas_keyval('v1 = first(listofvars(ans1)); ftm = setify(map(rhs,solve(ans1,v1)))');

        $sans = new stack_cas_casstring('ans1', 't');
        $tans = new stack_cas_casstring('ta1', 't');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node0->add_branch(0, '=', 0, '', 1, '', 'sol-0-0');
        $node0->add_branch(1, '=', 1, '', 3, '', 'sol-0-1');

        $sans = new stack_cas_casstring('ans1', 't');
        $tans = new stack_cas_casstring('ta2', 't');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node1->add_branch(0, '=', 0, '', 2, '', 'sol-1-0');
        $node1->add_branch(1, '=', 1, '', 3, '', 'sol-1-1');

        $sans = new stack_cas_casstring('ans2', 't');
        $tans = new stack_cas_casstring('ftm', 't');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node2->add_branch(0, '=', 0, '', -1, '', 'sol-2-0');
        $node2->add_branch(1, '=', 1, '', -1, 'You have correctly solved the equation you have entered in part 1. Please try both parts again!', 'sol-2-1');

        $sans = new stack_cas_casstring('ans2', 't');
        $tans = new stack_cas_casstring('tas', 't');
        $node3 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node3->add_branch(0, '=', 0, '', -1, '', 'sol-3-0');
        $node3->add_branch(1, '=', 1, '', -1, '', 'sol-3-1');

        $q->prts['sol'] = new stack_potentialresponse_tree('sol',
                    '', true, 0.3333333, $feedbackvars->get_session(),
                    array($node0, $node1, $node2, $node3));

        $sans = new stack_cas_casstring('ans3', 't');
        $tans = new stack_cas_casstring('-rn', 't');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'short-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'short-0-1');
        $q->prts['short'] = new stack_potentialresponse_tree('short',
                            '', true, 0.3333333, null, array($node));

        $q->options = new stack_options();

        return $q;
    }
}
