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
            // 'test6', // Test of the matrix input type. Not currently supported.
            'test7', // 1 input, 1 PRT with 3 nodes. Solving a diff equation, with intersting feedback.
            'test8', // 1 input, 1 PRT with 3 nodes. Roots of unity. Input has a syntax hint.
            'test9', // 2 inputs, 1 PRT, randomised, worked solution with CAS & plot. Make function continuous.
            // 'test10', // CBM using a slider input for certainty. Not currently supported.
        );
    }

    /**
     * Does the basical initialisation of a new Stack question that all the test
     * questions will need.
     * @return qtype_stack_question the new question.
     */
    protected static function make_a_stack_question() {
        question_bank::load_question_definition_classes('stack');

        $q = new qtype_stack_question();
        test_question_maker::initialise_a_question($q);
        $q->qtype = question_bank::get_qtype('stack');
        $q->contextid = context_system::instance()->id;

        $q->questionvariables = '';
        $q->specificfeedback = '';
        $q->specificfeedbackformat = FORMAT_HTML;
        $q->prtcorrect = 'Correct answer, well done.';
        $q->prtcorrectformat = FORMAT_HTML;
        $q->prtpartiallycorrect = 'Your answer is partially correct.';
        $q->prtpartiallycorrectformat = FORMAT_HTML;
        $q->prtincorrect = 'Incorrect answer.';
        $q->prtincorrectformat = FORMAT_HTML;
        $q->generalfeedback = '';

        $q->inputs = array();
        $q->prts = array();

        $q->markmode = qtype_stack_question::MARK_MODE_PENALTY;
        $q->options = new stack_options();
        $q->questionnote = '';

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_test0() {
        $q = self::make_a_stack_question();

        $q->name = 'test-0';
        $q->questiontext = 'What is $1+1$? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '2', array('boxWidth' => 5));

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'EqualComAss');
        $node->add_branch(0, '=', 0, '', -1, '', 'firsttree-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'firsttree-0-1');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', false, 1, null, array($node));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test1.xml file.
     */
    public static function make_stack_question_test1() {
        $q = self::make_a_stack_question();

        $q->name = 'test-1';
        $q->questionvariables = 'n = rand(5)+3; a = rand(5)+3; v = x; p = (v-a)^n; ta = (v-a)^(n+1)/(n+1)';
        $q->questiontext = 'Find
                            \[ \int @p@ d@v@\]
                            [[input:ans1]]
                            [[validation:ans1]]';
        $q->generalfeedback = 'We can either do this question by inspection (i.e. spot the answer)
                               or in a more formal manner by using the substitution
                               \[ u = (@v@-@a@).\]
                               Then, since $\frac{d}{d@v@}u=1$ we have
                               \[ \int @p@ d@v@ = \int u^@n@ du = \frac{u^@n+1@}{@n+1@}+c = @ta@+c.\]';

        $q->specificfeedback = '[[feedback:PotResTree_1]]';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta+c', array('boxWidth' => 20));

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'Int', 'x');
        $node->add_branch(0, '=', 0, '', -1, '', 'PotResTree_1-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'PotResTree_1-0-1');
        $q->prts['PotResTree_1'] = new stack_potentialresponse_tree('PotResTree_1', '', true, 1, null, array($node));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test2.xml file.
     */
    public static function make_stack_question_test2() {
        $q = self::make_a_stack_question();

        $q->name = 'test-2';
        $q->questiontext = 'Expand
                            \[ (x-2)(x-3) = x^2-[[input:ans1]] x+[[input:ans2]]. \]
                            [[validation:ans1</IEfeedback>
                            [[validation:ans2</IEfeedback>';

        $q->specificfeedback = '[[feedback:PotResTree_1]]';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', '5', array('boxWidth' => 3));
        $q->inputs['ans2'] = stack_input_factory::make(
                        'algebraic', 'ans2', '6', array('boxWidth' => 3));

        $sans = new stack_cas_casstring('x^2-ans1*x+ans2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('(x-2)*(x-3)');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'PotResTree_1-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'PotResTree_1-0-1');
        $q->prts['PotResTree_1'] = new stack_potentialresponse_tree('PotResTree_1', '', true, 1, null, array($node));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test3.xml file.
     */
    public static function make_stack_question_test3() {
        $q = self::make_a_stack_question();

        $q->name = 'test-3';
        $q->questiontext = '<p>1. Give an example of an odd function by typing
                                  an expression which represents it.
                                  $f_1(x)=$ [[input:ans1]].
                                  [[validation:ans1]]
                                  [[feedback:odd]]</p>
                            <p>2. Give an example of an even function.
                                  $f_2(x)=$ [[input:ans2]].
                                  [[validation:ans2]]
                                  [[feedback:even]]</p>
                            <p>3. Give an example of a function which is odd and even.
                                  $f_3(x)=$ [[input:ans3]].
                                  [[validation:ans3]]
                                  [[feedback:oddeven]]</p>
                            <p>4. Is the answer to 3. unique? [[input:ans4]]
                                  (Or are there many different possibilities.)
                                  [[validation:ans4]]
                                  [[feedback:unique]]</p>';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'x^3', array('boxWidth' => 15));
        $q->inputs['ans2'] = stack_input_factory::make(
                        'algebraic', 'ans2', 'x^4', array('boxWidth' => 15));
        $q->inputs['ans3'] = stack_input_factory::make(
                        'algebraic', 'ans3', '0',   array('boxWidth' => 15));
        $q->inputs['ans4'] = stack_input_factory::make(
                        'boolean',   'ans4', 'true');

        $feedbackvars = new stack_cas_keyval('sa = subst(x=-x,ans1)+ans1', null, null, 't');
        $sans = new stack_cas_casstring('sa');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, 'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa@ \neq 0.\]', 'odd-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'odd-0-1');
        $q->prts['odd']     = new stack_potentialresponse_tree('odd',
                '', true, 0.25, $feedbackvars->get_session(), array($node));

        $feedbackvars = new stack_cas_keyval('sa = subst(x=-x,ans2)-ans2', null, null, 't');
        $sans = new stack_cas_casstring('sa');
        $tans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, 'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa@ \neq 0.\]', 'odd-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'odd-0-1');
        $q->prts['even']    = new stack_potentialresponse_tree('even',
                '', true, 0.25, $feedbackvars->get_session(), array($node));

        $feedbackvars = new stack_cas_keyval('sa1 = ans3+subst(x=-x,ans3); sa2 = ans3-subst(x=-x,ans3)');

        $sans = new stack_cas_casstring('sa1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node0->add_branch(0, '=', 0,   '', 1,
                'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa1@ \neq 0.\]', 'oddeven-0-0');
        $node0->add_branch(1, '=', 0.5, '', 1, '', 'oddeven-0-1');

        $sans = new stack_cas_casstring('sa2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node1->add_branch(0, '+', 0,   '', -1,
                'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa2@ \neq 0.\]', 'oddeven-1-0');
        $node1->add_branch(1, '+', 0.5, '', -1, '', 'EVEN');

        $q->prts['oddeven'] = new stack_potentialresponse_tree('oddeven',
                '', true, 0.25, $feedbackvars->get_session(), array($node0, $node1));

        $sans = new stack_cas_casstring('ans4');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('true');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, '', 'unique-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'unique-0-1');
        $q->prts['unique']  = new stack_potentialresponse_tree('unique',
                '', true, 0.25, null, array($node));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test4.xml file.
     */
    public static function make_stack_question_test4() {
        $q = self::make_a_stack_question();

        $q->name = 'test-4';
        $q->questionvariables = 'p = x^2';
        $q->questiontext = 'Below is a sketch of a graph. Find an algebraic expression which represents it.
                            @plot(p,[x,-2,2])@
                            $f(x)=$ [[input:ans1]].
                            [[validation:ans1]]';
        $q->specificfeedback = '[[feedback:plots]]';
        $q->generalfeedback = 'The graph @plot(p,[x,-2,2])@ has algebraic expression \[ f(x)=@p@. \]';
        $q->qtype = question_bank::get_qtype('stack');

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'x^2', array('boxWidth' => 15));

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('x^2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1,
                'Your answer and my answer are plotted below. Look they are different! @plot([p,ans1],[x,-2,2])@', 'plots-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'plots-0-1');
        $q->prts['plots'] = new stack_potentialresponse_tree('plots',
                '', true, 1, null, array($node));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test5.xml file.
     */
    public static function make_stack_question_test5() {
        $q = self::make_a_stack_question();

        $q->name = 'test-5';
        $q->questionvariables = 'rn = -1*(rand(4)+1); rp = 8+rand(6); ar = rn*rp; sg = rn+rp; ' .
                'ta1 = x*(x+sg)=-ar; ta2 = x*(x-sg)=-ar; tas = setify(map(rhs,solve(ta1,x)))';
        $q->questiontext = '<p>A rectangle has length @sg@cm greater than its width.
                            If it has an area of $@abs(ar)@cm^2$, find the dimensions of the rectangle.</p>
                            <p>1. Write down an equation which relates the side lengths to the
                                  area of the rectangle.<br />
                                  [[input:ans1]]
                                  [[validation:ans1]]
                                  [[feedback:eq]]</p>
                            <p>2. Solve your equation. Enter your answer as a set of numbers.<br />
                                  [[input:ans2]]
                                  [[validation:ans2]]
                                  [[feedback:sol]]</p>
                            <p>3. Hence, find the length of the shorter side.<br />
                                  [[input:ans3]] cm
                                  [[validation:ans3]]
                                  [[feedback:short]]</p>';
        $q->generalfeedback = 'If $x$cm is the width then $(x+@sg@)$ is the length.
                               Now the area is $@abs(ar)@cm^2$ and so
                               \[ @x*(x+sg)=-ar@. \]
                               \[ @x^2+sg*x+ar@=0 \]
                               \[ @(x+rp)*(x+rn)=0@ \]
                               So that $x=@-rp@$ or $x=@-rn@$. Since lengths are positive quantities $x>0$
                               and we discard the negative root. Hence the length of the shorter side is
                               $x=@-rn@$cm.';

        $q->questionnote = '@ta1@, @rp@.';

        $q->inputs['ans1'] = stack_input_factory::make(
                            'algebraic', 'ans1', 'ta1', array('boxWidth' => 15));
        $q->inputs['ans2'] = stack_input_factory::make(
                            'algebraic', 'ans2', 'tas', array('boxWidth' => 15));
        $q->inputs['ans3'] = stack_input_factory::make(
                            'algebraic', 'ans3', 'rp', array('boxWidth' => 5));

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta1');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, 'Not correct.', 'eq-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'eq-0-1');
        $q->prts['eq'] = new stack_potentialresponse_tree('eq',
                    '', true, 0.3333333, null, array($node));

        $feedbackvars = new stack_cas_keyval('v1 = first(listofvars(ans1)); ftm = setify(map(rhs,solve(ans1,v1)))');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta1');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node0->add_branch(0, '=', 0, '', 1, '', 'sol-0-0');
        $node0->add_branch(1, '=', 1, '', 3, '', 'sol-0-1');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta2');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node1->add_branch(0, '=', 0, '', 2, '', 'sol-1-0');
        $node1->add_branch(1, '=', 1, '', 3, '', 'sol-1-1');

        $sans = new stack_cas_casstring('ans2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ftm');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node2->add_branch(0, '=', 0, '', -1, '', 'sol-2-0');
        $node2->add_branch(1, '=', 1, '', -1,
                'You have correctly solved the equation you have entered in part 1. Please try both parts again!', 'sol-2-1');

        $sans = new stack_cas_casstring('ans2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('tas');
        $tans->get_valid('t');
        $node3 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node3->add_branch(0, '=', 0, '', -1, '', 'sol-3-0');
        $node3->add_branch(1, '=', 1, '', -1, '', 'sol-3-1');

        $q->prts['sol'] = new stack_potentialresponse_tree('sol',
                    '', true, 0.3333333, $feedbackvars->get_session(),
                    array($node0, $node1, $node2, $node3));

        $sans = new stack_cas_casstring('ans3');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('rn');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, '', -1, 'Not correct.', 'short-0-0');
        $node->add_branch(1, '=', 1, '', -1, '', 'short-0-1');
        $q->prts['short'] = new stack_potentialresponse_tree('short',
                            '', true, 0.3333333, null, array($node));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test7.xml file.
     */
    public static function make_stack_question_test7() {
        $q = self::make_a_stack_question();

        $q->name = 'test-7';
        $q->questionvariables = "l1 = 1+(-1)^rand(1)*rand(6); " .
                                "l2 = l1+(-1)^rand(1)*(1+rand(4)); " .
                                "c1 = -1*(l1+l2); c2 = l1*l2; " .
                                "ode = 'diff(y(t),t,2)+c1*'diff(y(t),t)+c2*y(t); " .
                                "ta = A*e^(l1*t)+B*e^(l2*t)";
        $q->questiontext = '<p>Find the general solution to \[ @ode@ =0. \]</p>
                            <p>$y(t)=$ [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:Result]]';
        $q->questionnote = '@ta@';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta', array('boxWidth' => 15));

        $feedbackvars = new stack_cas_keyval('sa1 = subst(y(t)=ans1,ode); ' .
                                             'sa2 = ev(sa1,nouns); ' .
                                             'sa3 = fullratsimp(expand(sa2)); ' .
                                             'l = delete(t,listofvars(ans1)); ' .
                                             'lv = length(l); ' .
                                             'b1 = ev(ans1,t=0,fullratsimp); ' .
                                             'b2 = ev(ans1,t=1,fullratsimp); ' .
                                             'm = float(if not(equal(b2,0)) then fullratsimp(b1/b2) else 0)',
                                             null, 0, 't');

        $sans = new stack_cas_casstring('sa3');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node0->add_branch(0, '=', 0, '', -1, '<p>Your answer should satisfy the differential equation.
                In fact, when we substitute your expression into the differential equation we get</p>
                \[ @sa1@ =0, \]
                <p>evaluating the derivatives we have</p>
                \[ @sa2@ =0 \]
                <p>This simplifies to</p>
                \[ @sa3@ = 0,\]
                <p>so you must have done something wrong.</p>', 'Fails to satisfy DE');
        $node0->add_branch(1, '=', 1, '', 1, '', 'Result-0-T');

        $sans = new stack_cas_casstring('lv');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('2');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node1->add_branch(0, '=', 0.75, '', -1, '<p>You should have a general solution, which
                includes unknown constants. Your answer satisfies the differential equation,
                but does not have the correct number of unknown constants.</p>', 'Insufficient constants');
        $node1->add_branch(1, '=', 1, '', 2, '', 'Result-1-T');

        $sans = new stack_cas_casstring('numberp(m)');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('true');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node2->add_branch(0, '=', 1, '', -1, '', 'Result-2-F');
        $node2->add_branch(1, '=', 0, '', -1,
                'Your general solution should be a sum of two linearly independent components, but is not.',
                'Not two lin ind parts');

        $q->prts['Result'] = new stack_potentialresponse_tree('Result', '',
                true, 1, $feedbackvars->get_session(), array($node0, $node1, $node2));

        return $q;
    }


    /**
     * @return qtype_stack_question the question from the test8.xml file.
     */
    public static function make_stack_question_test8() {
        $q = self::make_a_stack_question();

        $q->name = 'test-8';
        $q->questionvariables = "n = rand(2)+3; " .
                                "p = rand(3)+2; " .
                                "ta = setify(makelist(p*%e^(2*%pi*%i*k/n),k,1,n))";
        $q->questiontext = '<p>Find all the complex solutions of the equation \[ z^@n@=@p^n@.\]
                            Enter your answer as a set of numbers.
                            [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:ans]]';
        $q->questionnote = '@ta@';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta',
                        array('boxWidth' => 20, 'syntaxHint' => '{?,?,...,?}'));

        $feedbackvars = new stack_cas_keyval('a1 = listify(ans1);' .
                                             'a1 = maplist(lambda([x],x^n-p^n),a1);' .
                                             'a1 = setify(a1)');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node0->add_branch(0, '=', 0, '', 1, '', 'ans-0-F');
        $node0->add_branch(1, '=', 1, '', -1, '', 'ans-0-T');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('{p}');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node1->add_branch(0, '=', 0, '', 2, '', 'ans-1-F');
        $node1->add_branch(1, '=', 0, '', -1,
                '<p>There are more answers that just the single real number.
                 Please consider complex solutions to this problem!</p>', 'ans-1-T');

        $sans = new stack_cas_casstring('a1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('{0}');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node2->add_branch(0, '=', 0, '', -1, '', 'ans-2-F');
        $node2->add_branch(1, '=', 0, '', -1,
                'All your answers satisfy the equation. But, you have missed some of the solutions.',
                'ans-2-T');

        $q->prts['ans'] = new stack_potentialresponse_tree('ans', '',
                true, 1, null, array($node0, $node1, $node2));

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test9.xml file.
     */
    public static function make_stack_question_test9() {
        $q = self::make_a_stack_question();

        $q->name = 'test-9';
        $q->questionvariables = 'b = rand([-2,-3,-4,2,3,4]); ta1 = b; ta2 = rand([-2,-3,-4,2,3,4]); ' .
                'a = b*ta2; p = a*x+b; f = lambda([x],if (x<0) then p else ta1*exp(ta2*x))';
        $q->questiontext = '<p>Let $f(x)$ be a real function defined on the interval $[-1,1]$ by the following formula.</p>
                            \[
                            f(x) = \left\{ \begin{array}{ll}
                            @p@ & \mbox{if }x<0, \\
                            a_1 e^{a_2\ x} & \mbox{if }x\geq 0.
                            \end{array}
                            \right.
                            \]
                            <p>Find the values of the parameters so that $f$ is continuous and differentiable of order $1$.
                            $a_1=[[input:ans1]]
                            $a_2=[[input:ans2]]</p>
                            [[validation:ans1]][[validation:ans2]]';

        $q->specificfeedback = '[[feedback:prt1]]';

        $q->generalfeedback  = '<p>We would like the function to be continuous at $x=0$, so we would like
                                \[ @p@=a_1e^{a_2\ x}\]
                                when evaluated at $x=0$. That is to say we want
                                \[ @b@ = a_1 \]
                                If we differentiate both sides of the above equation with respect to $x$ we
                                may match up the gradients. This gives us
                                \[ @a@=a_1a_2.\]
                                Solving this gives $a_2 = @a/b@$.</p>
                                <p>Hence the full answer is
                                \[
                                f(x) = \left\{ \begin{array}{ll}
                                @p@ & \mbox{if }x<0, \\
                                @ta1@ e^{@ta2@ x} & \mbox{if }x\geq 0.
                                \end{array}
                                \right.
                                \]</p>
                                <p>We can sketch the graph of this function as follows.
                                @plot(f(x),[x,-1,1])@</p>';

        $q->questionnote = '\[ a_1=@ta1@,\ a_2=@ta2@.\]';

        $q->inputs['ans1'] = stack_input_factory::make(
                                    'algebraic', 'ans1', 'ta1', array('boxWidth' => 4));
        $q->inputs['ans2'] = stack_input_factory::make(
                                    'algebraic', 'ans2', 'ta2', array('boxWidth' => 4));

        $feedbackvars = new stack_cas_keyval('g = lambda([x],if (x<0) then p else ans1*exp(ans2*x))');

        $sans = new stack_cas_casstring('[ans1,ans2]');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('[ta1,ta2]');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'Int', 'x');
        $node->add_branch(0, '=', 0, '', -1,
                'Compare your answer with the correct one @plot([f(x),g(x)],[x,-1,1])@', 'prt1-1-F');
        $node->add_branch(1, '=', 1, '', -1, '', 'prt1-1-T');
        $q->prts['prt1'] = new stack_potentialresponse_tree('prt1', '', true, 1, null, array($node));

        return $q;
    }
}
