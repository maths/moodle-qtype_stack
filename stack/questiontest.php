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
 * Holds the data defining one question test.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/questiontestresult.php');


/**
 * One question test.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_question_test {
    /**
     * @var array input name => value to be entered.
     */
    public $inputs;

    /**
     * @var array prt name => stack_potentialresponse_tree_state object
     */
    public $expectedresults;

    /**
     * Constructor
     * @param array $inputs input name => value to enter.
     */
    public function __construct($inputs) {
        $this->inputs = $inputs;
    }

    /**
     * Set the expected result for one of the PRTs.
     * @param string $prtname which PRT.
     * @param stack_potentialresponse_tree_state $expectedresult the expected result
     *      for this PRT. Only the mark, penalty and answernote fields are used.
     */
    public function add_expected_result($prtname, stack_potentialresponse_tree_state $expectedresult) {
        $this->expectedresults[$prtname] = $expectedresult;
    }

    /**
     * Run this test against a particular question.
     * @param question_usage_by_activity $quba the useage to use when running the test.
     * @param qtype_stack_question $question the question to test.
     * @param int $seed the random seed to use.
     * @return stack_question_test_result the test results.
     */
    public function test_question(question_usage_by_activity $quba, qtype_stack_question $question, $seed) {
        $response = array();
        foreach ($this->inputs as $name => $value) {
            $response[$name] = $value;
            $response[$name . '_val'] = $value;
        }

        $slot = $quba->add_question($question, $question->defaultmark);
        $quba->start_question($slot, $seed);

        $quba->process_action($slot, $response);

        $results = new stack_question_test_result($this);
        foreach ($this->inputs as $inputname => $notused) {
            $inputstate = $question->get_input_state($inputname, $response);
            $results->set_input_state($inputname,
                    $inputstate->contentsinterpreted, $inputstate->status);
        }

        foreach ($this->expectedresults as $prtname => $expectedresult) {
            $result = $question->get_prt_result($prtname, $response);
            $results->set_prt_result($prtname, new stack_potentialresponse_tree_state(
                    '', $result['feedback'], explode(' | ', $result['answernote']),
                    $result['valid'], $result['score'], $result['penalty']));
        }

        return $results;
    }

    /**
     * @param string $inputname the name of one of the inputs.
     * @return string the value to be entered into that input.
     */
    public function get_input($inputname) {
        return $this->inputs[$inputname];
    }
}
