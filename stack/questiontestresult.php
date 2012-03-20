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
 * Holds the results of one {@link stack_question_test).
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/questiontestresult.php');


/**
 * Holds the results of one {@link stack_question_test).
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_question_test_result {
    /**
     * @var stack_question_test the test case that this is the results for.
     */
    public $testcase;

    /**
     * @var array input name => the displayed value of that input.
     */
     public $inputvalues;

    /**
     * @var array input name => the input statues. One of the stack_input::STATUS_... constants.
     */
     public $inputstatuses;

     /**
      * @var array prt name => stack_potentialresponse_tree_state object
      */
     public $actualresults;

    /**
     * Constructor
     * @param stack_question_test $testcase the testcase this is the results for.
     */
    public function __construct(stack_question_test $testcase) {
        $this->testcase = $testcase;
    }

    /**
     * Set the part of the results data that describes the state of one of the inputs.
     * @param string $inputname the input name.
     * @param string $displayvalue the displayed version of the value that was input.
     * @param string $status one of the stack_input::STATUS_... constants.
     */
    public function set_input_state($inputname, $displayvalue, $status) {
        $this->inputvalues[$inputname]   = $displayvalue;
        $this->inputstatuses[$inputname] = $status;
    }

    public function set_prt_result($prtname, stack_potentialresponse_tree_state $actualresult) {
        $this->actualresults[$prtname] = $actualresult;
    }

    /**
     * @return array input name => object with fields ->input, ->display and ->status.
     */
    public function get_input_states() {
        $states = array();

        foreach ($this->inputvalues as $inputname => $inputvalue) {
            $state = new stdClass();
            $state->input = $this->testcase->get_input($inputname);
            $state->display = $inputvalue;
            $state->status = $this->inputstatuses[$inputname];
            $states[$inputname] = $state;
        }

        return $states;
    }

    /**
     * @return bool whether the test passed successfully.
     */
    public function passed() {
        // TODO
        return true;
    }
}
