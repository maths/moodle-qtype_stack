<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Stack question definition class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/stack/interaction/controller.class.php');
require_once(dirname(__FILE__) . '/stack/cas/keyval.class.php');
require_once(dirname(__FILE__) . '/stack/cas/castext.class.php');
require_once(dirname(__FILE__) . '/stack/potentialresponsetree.class.php');


/**
 * Represents a Stack question.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question extends question_graded_automatically {

    /**
     * @var array STACK specific: string name as it appears in the question text => stack_interaction_element
     */
    public $interactions;

    /**
     * @var string STACK specific: variables, as authored by the teacher.
     */
    public $questionvariables;

    /**
     * @var array stack_potentialresponse_tree STACK specific: respones tree number => ...
     */
    public $prts;

    /**
     * @var stack_options STACK specific: question-level options.
     */
    public $options;

    public function start_attempt(question_attempt_step $step, $variant) {

        $seed = time();
        $step->set_qt_var('_seed', $seed);

        $questionvars = new stack_cas_keyval($this->questionvariables);
        $qtext = new stack_cas_text($this->questiontext, $questionvars->get_session(), $seed, 't', false, true);
        //TODO error trapping if a question version breaks things.
        $step->set_qt_var('_questiontext', $qtext->get_display_castext());
        $step->set_qt_var('_session', $qtext->get_session());
    }

    public function apply_attempt_state(question_attempt_step $step) {
        // TODO
    }

    public function get_expected_data() {
        $expected = array();
        foreach ($this->interactions as $name => $ie) {
            $expected[$name] = PARAM_RAW;
        }
        return $expected;
    }

    public function summarise_response(array $response) {
        return ''; // TODO
    }

    public function get_correct_response() {
        return null; // TODO can we implement this?
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return false; // TODO
    }

    public function is_complete_response(array $response) {
        return false; // TODO
    }

    public function is_gradable_response(array $response) {
        return false; // TODO
    }

    public function get_validation_error(array $response) {
        return ''; // TODO
    }

    public function grade_response(array $response) {
        return array(0, question_state::$gradedwrong);
    }

    /**
     * @return int the number of vaiants that this question has.
     */
    public function get_num_variants() {
        return 1; //TODO
    }

}
