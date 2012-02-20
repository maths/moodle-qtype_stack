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
        return array('test1', 'test0', 'test2', 'test3', 'test4', 'test5', 'test6',
                'test7', 'test8', 'test9');
    }

    /**
     * @return qtype_stack_question the question from the test0.xml file.
     */
    public static function make_stack_question_test0() {
        question_bank::load_question_definition_classes('stack');
        $q = new qtype_stack_question();

        test_question_maker::initialise_a_question($q);

        $q->name = 'Stack question: test0';
        $q->questionvariables = '';
        $q->questiontext = 'What is $1+1$? #ans1#
                           <IEfeedback>ans1</IEfeedback>
                           <PRTfeedback>firsttree</PRTfeedback>';
        $q->generalfeedback = '';
        $q->qtype = question_bank::get_qtype('stack');

        $q->interactions = array();
        $q->interactions['ans1'] = stack_interaction_controller::make_element(
                'algebraic', 'ans1', 5, null);

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

        $q->name = 'Stack question: test0';
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
                        'algebraic', 'ans1', 20, null);

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
}