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
    const DEFAULT_CORRECT_FEEDBACK          = '<p>Correct answer, well done.</p>';
    const DEFAULT_PARTIALLYCORRECT_FEEDBACK = '<p>Your answer is partially correct.</p>';
    const DEFAULT_INCORRECT_FEEDBACK        = '<p>Incorrect answer.</p>';

    public function get_test_questions() {
        return array(
            'test0', // One input, one PRT, not randomised. (1 + 1 = 2.)
            'test1', // One input, one PRT, randomised. (Integrate (v - a) ^ n, a, n small random ints.)
            'test2', // Two inputs, one PRT, not randomises. (Expand (x - 2)(x - 3).)
            'test3', // Four inputs, four PRTs, not randomised. (Even and odd functions.)
            'test3_penalty0_1', // Four inputs, four PRTs, not randomised. (Even and odd functions.)
            'test4', // One input, one PRT, not randomised, has a plot. (What is the equation of this graph? x^2.)
            'test5', // Three inputs, three PRTs, one with 4 nodes, randomised. (Three steps, rectangle side length from area.)
            // 'test6', // Test of the matrix input type. Not currently supported.
            'test7', // 1 input, 1 PRT with 3 nodes. Solving a diff equation, with intersting feedback.
            'test8', // 1 input, 1 PRT with 3 nodes. Roots of unity. Input has a syntax hint.
            'test9', // 2 inputs, 1 PRT, randomised, worked solution with CAS & plot. Make function continuous.
            'test_boolean', // 2 inputs, 1 PRT, randomised, worked solution with CAS & plot. Make function continuous.
            'divide',       // One input, one PRT, tests 1 / ans1 - useful for testing CAS errors like divide by 0.
            'numsigfigs',   // One input, one PRT, tests 1 / ans1 - uses the NumSigFigs test.
            '1input2prts',  // Contrived example with one input, 2 prts, all feedback in the specific feedback area.
            'information',  // Neither inputs nor PRTs.
            'survey',       // Inputs, but no PRTs.
            'single_char_vars', // Tests the insertion of * symbols between letter names.
            'runtime_prt_err' // This generates an error in the PRT at runtime.  With and without guard clause.
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
        $q->penalty = 0.1; // The default.

        $q->prtcorrect = self::DEFAULT_CORRECT_FEEDBACK;;
        $q->prtcorrectformat = FORMAT_HTML;
        $q->prtpartiallycorrect = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $q->prtpartiallycorrectformat = FORMAT_HTML;
        $q->prtincorrect = self::DEFAULT_INCORRECT_FEEDBACK;
        $q->prtincorrectformat = FORMAT_HTML;
        $q->generalfeedback = '';
        $q->variantsselectionseed = '';

        $q->inputs = array();
        $q->prts = array();

        $q->options = new stack_options();
        $q->questionnote = '';

        return $q;
    }

    /**
     * @return qtype_stack_question a very elementary question.
     */
    public static function make_stack_question_test0() {
        $q = self::make_a_stack_question();

        $q->name = 'test-0';
        $q->questionvariables = 'a:1+1;';
        $q->questiontext = 'What is @a@? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '2', array('boxWidth' => 5));

        $q->options->questionsimplify = 0;

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'EqualComAss');
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-T');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', false, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test1.xml file.
     */
    public static function make_stack_question_test1() {
        $q = self::make_a_stack_question();

        $q->name = 'test-1';
        $q->questionvariables = 'n : rand(5)+3; a : rand(5)+3; v : x; p : (v-a)^n; ta : (v-a)^(n+1)/(n+1); ta1 : ta';
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
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta+c',
                array('boxWidth' => 20, 'forbidWords' => 'int, [[BASIC-ALGEBRA]]', 'allowWords' => 'popup, boo, Sin'));

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'Int', 'x');
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'PotResTree_1-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'PotResTree_1-0-1');
        $q->prts['PotResTree_1'] = new stack_potentialresponse_tree('PotResTree_1', '', true, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test2.xml file.
     */
    public static function make_stack_question_test2() {
        $q = self::make_a_stack_question();

        $q->name = 'test-2';
        $q->questionvariables = 'orderless(y,x); a:3; f(x):=x^2; b:f(a); ta:y+x';
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
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'PotResTree_1-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'PotResTree_1-0-1');
        $q->prts['PotResTree_1'] = new stack_potentialresponse_tree('PotResTree_1', '', true, 1, null, array($node), 0);

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
        $q->specificfeedback = '';
        $q->penalty = 0.4;

        $q->inputs['ans1'] = stack_input_factory::make('algebraic', 'ans1', 'x^3',
                        array('boxWidth' => 15, 'strictSyntax' => true, 'lowestTerms' => false, 'sameType' => false));
        $q->inputs['ans2'] = stack_input_factory::make('algebraic', 'ans2', 'x^4',
                        array('boxWidth' => 15, 'strictSyntax' => true, 'lowestTerms' => false, 'sameType' => false));
        $q->inputs['ans3'] = stack_input_factory::make('algebraic', 'ans3', '0',
                        array('boxWidth' => 15, 'strictSyntax' => true, 'lowestTerms' => false, 'sameType' => false));
        $q->inputs['ans4'] = stack_input_factory::make('boolean', 'ans4', 'true');

        $feedbackvars = new stack_cas_keyval('sa:subst(x=-x,ans1)+ans1', null, null, 't');
        $sans = new stack_cas_casstring('sa');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1,
                'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa@ \neq 0.\]', FORMAT_HTML, 'odd-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'odd-0-1');
        $q->prts['odd']     = new stack_potentialresponse_tree('odd',
                '', true, 0.25, $feedbackvars->get_session(), array($node), 0);

        $feedbackvars = new stack_cas_keyval('sa:subst(x=-x,ans2)-ans2', null, null, 't');
        $sans = new stack_cas_casstring('sa');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1,
                'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa@ \neq 0.\]', FORMAT_HTML, 'even-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'even-0-1');
        $q->prts['even'] = new stack_potentialresponse_tree('even',
                '', true, 0.25, $feedbackvars->get_session(), array($node), 0);

        $feedbackvars = new stack_cas_keyval('sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3)');

        $sans = new stack_cas_casstring('sa1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node0->add_branch(0, '=', 0, $q->penalty, 1,
                'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa1@ \neq 0.\]', FORMAT_HTML, 'oddeven-0-0');
        $node0->add_branch(1, '=', 0.5, $q->penalty, 1, '', FORMAT_HTML, 'oddeven-0-1');

        $sans = new stack_cas_casstring('sa2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node1->add_branch(0, '+', 0, $q->penalty, -1,
                'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa2@ \neq 0.\]', FORMAT_HTML, 'oddeven-1-0');
        $node1->add_branch(1, '+', 0.5, $q->penalty, -1, '', FORMAT_HTML, 'oddeven-1-1');

        $q->prts['oddeven'] = new stack_potentialresponse_tree('oddeven',
                '', true, 0.25, $feedbackvars->get_session(), array($node0, $node1), 0);

        $sans = new stack_cas_casstring('ans4');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('true');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, 1, -1, '', FORMAT_HTML, 'unique-0-0');
        $node->add_branch(1, '=', 1, 1, -1, '', FORMAT_HTML, 'unique-0-1');
        $q->prts['unique']  = new stack_potentialresponse_tree('unique',
                '', true, 0.25, null, array($node), 0);

        $q->hints = array(
            new question_hint(1, 'Hint 1', FORMAT_HTML),
            new question_hint(2, 'Hint 2', FORMAT_HTML),
        );

        $q->deployedseeds = array();

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test3.xml file.
     */
    public static function make_stack_question_test3_penalty0_1() {
        $q = self::make_a_stack_question();

        $q->name = 'test-3,penalty-0.1';
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

        $feedbackvars = new stack_cas_keyval('sa:subst(x=-x,ans1)+ans1', null, null, 't');
        $sans = new stack_cas_casstring('sa');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1,
                'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa@ \neq 0.\]', FORMAT_HTML, 'odd-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'odd-0-1');
        $q->prts['odd']     = new stack_potentialresponse_tree('odd',
                '', true, 0.25, $feedbackvars->get_session(), array($node), 0);

        $feedbackvars = new stack_cas_keyval('sa:subst(x=-x,ans2)-ans2', null, null, 't');
        $sans = new stack_cas_casstring('sa');
        $tans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1,
                'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa@ \neq 0.\]', FORMAT_HTML, 'odd-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'odd-0-1');
        $q->prts['even']    = new stack_potentialresponse_tree('even',
                '', true, 0.25, $feedbackvars->get_session(), array($node), 0);

        $feedbackvars = new stack_cas_keyval('sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3)');

        $sans = new stack_cas_casstring('sa1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node0->add_branch(0, '=', 0, $q->penalty, 1,
                'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa1@ \neq 0.\]', FORMAT_HTML, 'oddeven-0-0');
        $node0->add_branch(1, '=', 0.5, $q->penalty, 1, '', FORMAT_HTML, 'oddeven-0-1');

        $sans = new stack_cas_casstring('sa2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node1->add_branch(0, '+', 0, $q->penalty, -1,
                'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa2@ \neq 0.\]', FORMAT_HTML, 'oddeven-1-0');
        $node1->add_branch(1, '+', 0.5, $q->penalty, -1, '', FORMAT_HTML, 'EVEN');

        $q->prts['oddeven'] = new stack_potentialresponse_tree('oddeven',
                '', true, 0.25, $feedbackvars->get_session(), array($node0, $node1), 0);

        $sans = new stack_cas_casstring('ans4');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('true');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'unique-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'unique-0-1');
        $q->prts['unique']  = new stack_potentialresponse_tree('unique',
                '', true, 0.25, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test4.xml file.
     */
    public static function make_stack_question_test4() {
        $q = self::make_a_stack_question();

        $q->name = 'test-4';
        $q->questionvariables = 'p : x^2';
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
        $node->add_branch(0, '=', 0, $q->penalty, -1,
                'Your answer and my answer are plotted below. Look they are different! @plot([p,ans1],[x,-2,2])@',
                FORMAT_HTML, 'plots-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'plots-0-1');
        $q->prts['plots'] = new stack_potentialresponse_tree('plots',
                '', true, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test5.xml file.
     */
    public static function make_stack_question_test5() {
        $q = self::make_a_stack_question();

        $q->name = 'test-5';
        $q->questionvariables = 'rn : -1*(rand(4)+1); rp : 8+rand(6); ar : rn*rp; sg : rn+rp; ' .
                'ta1 : x*(x+sg)=-ar; ta2 : x*(x-sg)=-ar; tas : setify(map(rhs,solve(ta1,x)))';
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
        $node->add_branch(0, '=', 0, $q->penalty, -1, 'Not correct.', FORMAT_HTML, 'eq-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'eq-0-1');
        $q->prts['eq'] = new stack_potentialresponse_tree('eq',
                    '', true, 0.3333333, null, array($node), 0);

        $feedbackvars = new stack_cas_keyval('v1 : first(listofvars(ans1)); ftm : setify(map(rhs,solve(ans1,v1)))');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta1');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node0->add_branch(0, '=', 0, $q->penalty, 1, '', FORMAT_HTML, 'sol-0-0');
        $node0->add_branch(1, '=', 1, $q->penalty, 3, '', FORMAT_HTML, 'sol-0-1');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta2');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'SubstEquiv', null);
        $node1->add_branch(0, '=', 0, $q->penalty, 2, '', FORMAT_HTML, 'sol-1-0');
        $node1->add_branch(1, '=', 1, $q->penalty, 3, '', FORMAT_HTML, 'sol-1-1');

        $sans = new stack_cas_casstring('ans2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ftm');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node2->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'sol-2-0');
        $node2->add_branch(1, '=', 1, $q->penalty, -1,
                'You have correctly solved the equation you have entered in part 1. Please try both parts again!',
                FORMAT_HTML, 'sol-2-1');

        $sans = new stack_cas_casstring('ans2');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('tas');
        $tans->get_valid('t');
        $node3 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node3->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'sol-3-0');
        $node3->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'sol-3-1');

        $q->prts['sol'] = new stack_potentialresponse_tree('sol',
                    '', true, 0.3333333, $feedbackvars->get_session(),
                    array($node0, $node1, $node2, $node3), 0);

        $sans = new stack_cas_casstring('ans3');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('rn');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1, 'Not correct.', FORMAT_HTML, 'short-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'short-0-1');
        $q->prts['short'] = new stack_potentialresponse_tree('short',
                            '', true, 0.3333333, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question in which the expected answer in the true/false input is generated from the question variables...
     */
    public static function make_stack_question_test_boolean() {
        $q = self::make_a_stack_question();

        $q->name = 'test-boolean';
        $q->questionvariables = 'ta:true;';
        $q->questiontext = 'What is @ta@? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make('boolean', 'ans1', 'ta');

        $q->options->questionsimplify = 1;

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', null);
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-T');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', true, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test7.xml file.
     */
    public static function make_stack_question_test7() {
        $q = self::make_a_stack_question();

        $q->name = 'test-7';
        $q->questionvariables = "l1 : 1+(-1)^rand(1)*rand(6); " .
                                "l2 : l1+(-1)^rand(1)*(1+rand(4)); " .
                                "c1 : -1*(l1+l2); c2 = l1*l2; " .
                                "ode : 'diff(y(t),t,2)+c1*'diff(y(t),t)+c2*y(t); " .
                                "ta : A*e^(l1*t)+B*e^(l2*t)";
        $q->questiontext = '<p>Find the general solution to \[ @ode@ =0. \]</p>
                            <p>$y(t)=$ [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:Result]]';
        $q->questionnote = '@ta@';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta', array('boxWidth' => 15));

        $feedbackvars = new stack_cas_keyval('sa1 : subst(y(t)=ans1,ode); ' .
                                             'sa2 : ev(sa1,nouns); ' .
                                             'sa3 : fullratsimp(expand(sa2)); ' .
                                             'l : delete(t,listofvars(ans1)); ' .
                                             'lv : length(l); ' .
                                             'b1 : ev(ans1,t=0,fullratsimp); ' .
                                             'b2 : ev(ans1,t=1,fullratsimp); ' .
                                             'm : float(if not(equal(b2,0)) then fullratsimp(b1/b2) else 0)',
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
                <p>so you must have done something wrong.</p>', FORMAT_HTML, 'Fails to satisfy DE');
        $node0->add_branch(1, '=', 1, '', 1, '', FORMAT_HTML, 'Result-0-T');

        $sans = new stack_cas_casstring('lv');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('2');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node1->add_branch(0, '=', 0.75, $q->penalty, -1, '<p>You should have a general solution, which
                includes unknown constants. Your answer satisfies the differential equation,
                but does not have the correct number of unknown constants.</p>', FORMAT_HTML, 'Insufficient constants');
        $node1->add_branch(1, '=', 1, $q->penalty, 2, '', FORMAT_HTML, 'Result-1-T');

        $sans = new stack_cas_casstring('numberp(m)');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('true');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node2->add_branch(0, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'Result-2-F');
        $node2->add_branch(1, '=', 0, $q->penalty, -1,
                'Your general solution should be a sum of two linearly independent components, but is not.', FORMAT_HTML,
                'Not two lin ind parts');

        $q->prts['Result'] = new stack_potentialresponse_tree('Result', '',
                true, 1, $feedbackvars->get_session(), array($node0, $node1, $node2), 0);

        return $q;
    }


    /**
     * @return qtype_stack_question the question from the test8.xml file.
     */
    public static function make_stack_question_test8() {
        $q = self::make_a_stack_question();

        $q->name = 'test-8';
        $q->questionvariables = "n : rand(2)+3; " .
                                "p : rand(3)+2; " .
                                "ta : setify(makelist(p*%e^(2*%pi*%i*k/n),k,1,n))";
        $q->questiontext = '<p>Find all the complex solutions of the equation \[ z^@n@=@p^n@.\]
                            Enter your answer as a set of numbers.
                            [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:ans]]';
        $q->questionnote = '@ta@';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta',
                        array('boxWidth' => 20, 'syntaxHint' => '{?,?,...,?}'));

        $feedbackvars = new stack_cas_keyval('a1 : listify(ans1);' .
                                             'a1 : maplist(lambda([x],x^n-p^n),a1);' .
                                             'a1 : setify(a1)');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('ta');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '');
        $node0->add_branch(0, '=', 0, $q->penalty, 1, '', FORMAT_HTML, 'ans-0-F');
        $node0->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'ans-0-T');

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('{p}');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node1->add_branch(0, '=', 0, $q->penalty, 2, '', FORMAT_HTML, 'ans-1-F');
        $node1->add_branch(1, '=', 0, $q->penalty, -1,
                '<p>There are more answers that just the single real number.
                 Please consider complex solutions to this problem!</p>', FORMAT_HTML, 'ans-1-T');

        $sans = new stack_cas_casstring('a1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('{0}');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node2->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'ans-2-F');
        $node2->add_branch(1, '=', 0, $q->penalty, -1,
                'All your answers satisfy the equation. But, you have missed some of the solutions.', FORMAT_HTML,
                'ans-2-T');

        $q->prts['ans'] = new stack_potentialresponse_tree('ans', '',
                true, 1, null, array($node0, $node1, $node2), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test9.xml file.
     */
    public static function make_stack_question_test9() {
        $q = self::make_a_stack_question();

        $q->name = 'test-9';
        $q->questionvariables = 'b : rand([-2,-3,-4,2,3,4]); ta1 : b; ta2 : rand([-2,-3,-4,2,3,4]); ' .
                'a : b*ta2; p : a*x+b; f : lambda([x],if (x<0) then p else ta1*exp(ta2*x))';
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

        $feedbackvars = new stack_cas_keyval('g : lambda([x],if (x<0) then p else ans1*exp(ans2*x))');

        $sans = new stack_cas_casstring('[ans1,ans2]');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('[ta1,ta2]');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'Int', 'x');
        $node->add_branch(0, '=', 0, $q->penalty, -1,
                'Compare your answer with the correct one @plot([f(x),g(x)],[x,-1,1])@', FORMAT_HTML, 'prt1-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'prt1-1-T');
        $q->prts['prt1'] = new stack_potentialresponse_tree('prt1', '', true, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_divide() {
        $q = self::make_a_stack_question();

        $q->name = 'divide';
        $q->questiontext = '<p>Give me \(x\) such that \[1/x = 2\]</p>
                            <p>\(x = \) [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:prt1]]';
        $q->penalty = 0.3333333;

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '1/2', array('boxWidth' => 5));

        $sans = new stack_cas_casstring('1/ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('2');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '3');
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'prt1-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'prt1-1-T');
        $q->prts['prt1'] = new stack_potentialresponse_tree('prt1', '', false, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using a numerical precision answertest.
     */
    public static function make_stack_question_numsigfigs() {
        $q = self::make_a_stack_question();

        $q->name = 'test-numsigfigs';
        $q->questionvariables = '';
        $q->questiontext = 'Please round $\pi$ to three significant figures. [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.1; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '3.14', array('boxWidth' => 5, 'forbidFloats' => false));

        $q->options->questionsimplify = 0;

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('3.14');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'NumSigFigs', '3');
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-T');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', false, 1, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test1.xml file.
     */
    public static function make_stack_question_1input2prts() {
        $q = self::make_a_stack_question();

        $q->name = '1input2prts';
        $q->questionvariables = '';
        $q->questiontext = 'Enter a multiple of 6: [[input:ans1]]
                            [[validation:ans1]]';
        $q->generalfeedback = '';

        $q->specificfeedback = '[[feedback:prt1]] [[feedback:prt2]]';
        $q->penalty = 0.1;

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', '6', array('boxWidth' => 15));

        $sans = new stack_cas_casstring('mod(ans1,2)');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv');
        $node->add_branch(0, '=', 0, $q->penalty, -1, 'Your answer is not even.', FORMAT_HTML, 'prt1-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'prt1-0-1');
        $q->prts['prt1'] = new stack_potentialresponse_tree('prt1', '', true, 0.5, null, array($node), 0);

        $sans = new stack_cas_casstring('mod(ans1,3)');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('0');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv');
        $node->add_branch(0, '=', 0, $q->penalty, -1, 'Your answer is not divisible by three.', FORMAT_HTML, 'prt2-0-0');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'prt2-0-1');
        $q->prts['prt2'] = new stack_potentialresponse_tree('prt1', '', true, 0.5, null, array($node), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question a information item, rather than a question.
     */
    public static function make_stack_question_information() {
        $q = self::make_a_stack_question();

        $q->name = 'Information item';
        $q->questionvariables = 'a:7;';
        $q->questiontext = '\[a = @a@\].';
        $q->generalfeedback = 'The end.';

        $q->specificfeedback = '';
        $q->defaultmark = 0;
        $q->length = 0;

        return $q;
    }

    /**
     * @return qtype_stack_question a 'survey' item. Inputs, but no grading.
     */
    public static function make_stack_question_survey() {
        $q = self::make_a_stack_question();

        $q->name = 'Survey';
        $q->questionvariables = '';
        $q->questiontext = 'What is your favourite equation? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '';
        $q->defaultmark = 0;

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '2', array('boxWidth' => 15, 'sameType' => false));

        return $q;
    }

    /**
     * @return qtype_stack_question a very elementary question.
     */
    public static function make_stack_question_single_char_vars() {
        $q = self::make_a_stack_question();

        $q->name = 'single_char_vars';
        $q->questionvariables = 'a:sin(x*y);';
        $q->questiontext = 'What is @a@? [[input:ans1]]
                               [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                    'algebraic', 'ans1', '2', array('boxWidth' => 5, 'insertStars' => 2, 'strictSyntax' => false));

        $q->options->questionsimplify = 0;

        $sans = new stack_cas_casstring('ans1');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('sin(x*y)');
        $tans->get_valid('t');
        $node = new stack_potentialresponse_node($sans, $tans, 'EqualComAss');
        $node->add_branch(0, '=', 0, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-F');
        $node->add_branch(1, '=', 1, $q->penalty, -1, '', FORMAT_HTML, 'firsttree-1-T');
        $q->prts['firsttree'] = new stack_potentialresponse_tree('firsttree', '', false, 1, null, array($node), 0);

        return $q;
    }

    public static function make_stack_question_runtime_prt_err() {
        $q = self::make_a_stack_question();

        $q->name = 'runtime_prt_err';
        $q->questionvariables = "";
        $q->questiontext = '<p>Give an example of a system of equations with a unique solution.</p>' .
            '<p>[[input:ans1]] [[validation:ans1]]</p>';

        $q->specificfeedback = '[[feedback:Result]]';
        $q->questionnote = '';

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '[x+y=1,x-y=1]', array('boxWidth' => 25));

        $feedbackvars = new stack_cas_keyval('', null, 0, 't');

        $sans = new stack_cas_casstring('all_listp(equationp,ans1)');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('true');
        $tans->get_valid('t');
        $node0 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node0->add_branch(0, '=', 0, '', -1, 'Your answer should be a list of equations!', FORMAT_HTML, 'Result-0-F');
        $node0->add_branch(1, '=', 0, '', 1, 'Your answer is a list of equations.', FORMAT_HTML, 'Result-0-T');

        $sans = new stack_cas_casstring('solve(ans1,listofvars(ans1))');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('[]');
        $tans->get_valid('t');
        $node1 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node1->add_branch(0, '=', 0, $q->penalty, -1, 'Your equations have no solution!', FORMAT_HTML, 'Result-1-F');
        $node1->add_branch(1, '=', 0, $q->penalty, 2, 'You have some solutions!', FORMAT_HTML, 'Result-1-T');

        $sans = new stack_cas_casstring('length(solve(ans1,listofvars(ans1)))');
        $sans->get_valid('t');
        $tans = new stack_cas_casstring('1');
        $tans->get_valid('t');
        $node2 = new stack_potentialresponse_node($sans, $tans, 'AlgEquiv', '', true);
        $node2->add_branch(0, '=', 0, $q->penalty, -1, 'You should have only one solution.', FORMAT_HTML, 'Result-2-F');
        $node2->add_branch(1, '=', 1, $q->penalty, -1,
                'Good, you have one solution.', FORMAT_HTML, 'Result-2-T');

        $q->prts['Result'] = new stack_potentialresponse_tree('Result', '',
                true, 1, $feedbackvars->get_session(), array($node0, $node1, $node2), 0);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function get_stack_question_data_test0() {
        question_bank::load_question_definition_classes('stack');
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'stack';
        $qdata->name = 'test-0';
        $qdata->questiontext = 'What is $1+1$? [[input:ans1]]
                                [[validation:ans1]]';
        $qdata->generalfeedback = '';

        $qdata->options = new stdClass();
        $qdata->options->id                        = 0;
        $qdata->options->questionvariables         = '';
        $qdata->options->specificfeedback          = '[[feedback:firsttree]]';
        $qdata->options->specificfeedbackformat    = FORMAT_HTML;
        $qdata->options->questionnote              = '';
        $qdata->options->questionsimplify          = 1;
        $qdata->options->assumepositive            = 0;
        $qdata->options->prtcorrect                = self::DEFAULT_CORRECT_FEEDBACK;
        $qdata->options->prtcorrectformat          = FORMAT_HTML;
        $qdata->options->prtpartiallycorrect       = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->prtpartiallycorrectformat = FORMAT_HTML;
        $qdata->options->prtincorrect              = self::DEFAULT_INCORRECT_FEEDBACK;
        $qdata->options->prtincorrectformat        = FORMAT_HTML;
        $qdata->options->multiplicationsign        = 'dot';
        $qdata->options->sqrtsign                  = 1;
        $qdata->options->complexno                 = 'i';
        $qdata->options->inversetrig               = 'cos-1';
        $qdata->options->matrixparens              = '[';
        $qdata->options->variantsselectionseed     = '';

        $input = new stdClass();
        $input->name               = 'ans1';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = '2';
        $input->boxsize            = 5;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans1'] = $input;

        $prt = new stdClass();
        $prt->name              = 'firsttree';
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'firsttree';
        $node->nodename            = '0';
        $node->answertest          = 'EqualComAss';
        $node->sans                = 'ans1';
        $node->tans                = '2';
        $node->testoptions         = '';
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 0;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'firsttree-1-T';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'firsttree-1-F';
        $node->falsefeedback       = '';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['firsttree'] = $prt;

        $qdata->deployedseeds = array('12345');

        $qtest = new stack_question_test(array('ans1' => '2'));
        $qtest->add_expected_result('firsttree', new stack_potentialresponse_tree_state(
                1, true, 1, 0, '', array('firsttree-1-T')));
        $qdata->testcases[1] = $qtest;

        return $qdata;
    }

    /**
     * @return qtype_stack_question the question from the test3.xml file.
     */
    public static function get_stack_question_data_test3() {
        question_bank::load_question_definition_classes('stack');
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->contextid = context_system::instance()->id;
        $qdata->qtype = 'stack';
        $qdata->name = 'test-3';
        $qdata->questiontext = '<p>1. Give an example of an odd function by typing
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
        $qdata->generalfeedback = '';
        $qdata->penalty = 0.4;

        $qdata->options = new stdClass();
        $qdata->options->id                        = 0;
        $qdata->options->questionvariables         = '';
        $qdata->options->specificfeedback          = '';
        $qdata->options->specificfeedbackformat    = FORMAT_HTML;
        $qdata->options->questionnote              = '';
        $qdata->options->questionsimplify          = 1;
        $qdata->options->assumepositive            = 0;
        $qdata->options->markmode                  = 'penalty';
        $qdata->options->prtcorrect                = self::DEFAULT_CORRECT_FEEDBACK;
        $qdata->options->prtcorrectformat          = FORMAT_HTML;
        $qdata->options->prtpartiallycorrect       = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->prtpartiallycorrectformat = FORMAT_HTML;
        $qdata->options->prtincorrect              = self::DEFAULT_INCORRECT_FEEDBACK;
        $qdata->options->prtincorrectformat        = FORMAT_HTML;
        $qdata->options->multiplicationsign        = 'dot';
        $qdata->options->sqrtsign                  = 1;
        $qdata->options->complexno                 = 'i';
        $qdata->options->inversetrig               = 'cos-1';
        $qdata->options->matrixparens              = '[';
        $qdata->options->variantsselectionseed     = '';

        $input = new stdClass();
        $input->name               = 'ans1';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = 'x^3';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans1'] = $input;

        $input = new stdClass();
        $input->name               = 'ans2';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = 'x^4';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans2'] = $input;

        $input = new stdClass();
        $input->name               = 'ans3';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = '0';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans3'] = $input;

        $input = new stdClass();
        $input->name               = 'ans4';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'boolean';
        $input->tans               = 'true';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 0;
        $input->showvalidation     = 0;
        $input->options            = '';
        $qdata->inputs['ans4'] = $input;

        $prt = new stdClass();
        $prt->name              = 'odd';
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans1)+ans1';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'odd';
        $node->nodename            = '0';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'odd-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'odd-0-0';
        $node->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa@ \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['odd'] = $prt;

        $prt = new stdClass();
        $prt->name              = 'even';
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans2)-ans2';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'even';
        $node->nodename            = '0';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'even-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'even-0-0';
        $node->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa@ \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['even'] = $prt;

        $prt = new stdClass();
        $prt->name              = 'oddeven';
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackvariables = 'sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3)';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'oddeven';
        $node->nodename            = '0';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa1';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 0.5;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = 1;
        $node->trueanswernote      = 'oddeven-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = 1;
        $node->falseanswernote     = 'oddeven-0-0';
        $node->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)=@sa1@ \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'oddeven';
        $node->nodename            = '1';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa2';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '+';
        $node->truescore           = 0.5;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'oddeven-1-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '+';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'oddeven-1-0';
        $node->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)=@sa2@ \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['1'] = $node;
        $qdata->prts['oddeven'] = $prt;

        $prt = new stdClass();
        $prt->name              = 'unique';
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackvariables = null;
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'unique';
        $node->nodename            = '0';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'ans4';
        $node->tans                = 'true';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 1;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'unique-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 1;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'unique-0-0';
        $node->falsefeedback       = '';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['unique'] = $prt;

        $qdata->deployedseeds = array();
        $qdata->testcases = array();

        $qdata->hints = array(
            1 => new question_hint(1, 'Hint 1', FORMAT_HTML),
            2 => new question_hint(2, 'Hint 2', FORMAT_HTML),
        );

        return $qdata;
    }
}
