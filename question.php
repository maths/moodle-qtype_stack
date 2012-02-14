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


/**
 * Represents a Stack question.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question extends question_graded_automatically {

    /**
     * @var array string name as it appears in the question text => stack_interaction_element
     */
    public $interactions;

    /**
     * @var array int respones tree number => ...
     */
    public $prts;

    /**
     * @var array question-level options.
     */
    public $options;

    public function start_attempt(question_attempt_step $step, $variant) {
        // TODO
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
}
