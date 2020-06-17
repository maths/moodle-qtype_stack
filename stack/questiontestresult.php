<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

defined('MOODLE_INTERNAL') || die();

// Holds the results of one {@link stack_question_test).
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

class stack_question_test_result {
    /**
     * @var stack_question_test the test case that this is the results for.
     */
    public $testcase;

    /**
     * @var array input name => actual value put into this input.
     */
    public $inputvalues;

    /**
     * @var array input name => modified value of this input.
     */
    public $inputvaluesmodified;

    /**
     * @var array input name => the displayed value of that input.
     */
    public $inputdisplayed;

    /**
     * @var array input name => any errors created by invalid input.
     */
    public $inputerrors;

     /**
      * @var array input name => the input statues. One of the stack_input::STATUS_... constants.
      */
    public $inputstatuses;

     /**
      * @var array prt name => stack_potentialresponse_tree_state object
      */
    public $actualresults;

     /**
      * @var array prt name => debuginfo
      */
    public $debuginfo;

    /**
     * @var float Store the question penalty to check defaults.
     */
    public $questionpenalty;

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
     * @param string $inputbalue the value of this input.
     * @param string $displayvalue the displayed version of the value that was input.
     * @param string $status one of the stack_input::STATUS_... constants.
     */
    public function set_input_state($inputname, $inputvalue, $inputmodified, $displayvalue, $status, $error) {
        $this->inputvalues[$inputname]         = $inputvalue;
        $this->inputvaluesmodified[$inputname] = $inputmodified;
        $this->inputdisplayed[$inputname]      = $displayvalue;
        $this->inputstatuses[$inputname]       = $status;
        $this->inputerrors[$inputname]         = $error;
    }

    public function set_prt_result($prtname, stack_potentialresponse_tree_state $actualresult) {
        $this->actualresults[$prtname] = $actualresult;
    }

    public function set_questionpenalty($penalty) {
        $this->questionpenalty = $penalty;
    }

    /**
     * @return array input name => object with fields ->input, ->display and ->status.
     */
    public function get_input_states() {
        $states = array();

        foreach ($this->inputvalues as $inputname => $inputvalue) {
            $state = new stdClass();
            $state->rawinput = $this->testcase->get_input($inputname);
            $state->input = $inputvalue;
            $state->modified = $this->inputvaluesmodified[$inputname];
            $state->display = $this->inputdisplayed[$inputname];
            $state->status = $this->inputstatuses[$inputname];
            $state->errors = $this->inputerrors[$inputname];
            $states[$inputname] = $state;
        }

        return $states;
    }

    /**
     * @return array input name => object with fields ->mark, ->expectedmark,
     *      ->penalty, ->expectedpenalty, ->answernote, ->expectedanswernote,
     *      ->feedback and ->testoutcome.
     */
    public function get_prt_states() {
        $states = array();

        foreach ($this->testcase->expectedresults as $prtname => $expectedresult) {
            $expectedanswernote = $expectedresult->answernotes;

            $state = new stdClass();
            $state->expectedscore = $expectedresult->score;
            $state->expectedpenalty = $expectedresult->penalty;
            $state->expectedanswernote = reset($expectedanswernote);

            if (array_key_exists($prtname, $this->actualresults)) {
                $actualresult = $this->actualresults[$prtname];
                $state->score = $actualresult->score;
                $state->penalty = $actualresult->penalty;
                $state->answernote = implode(' | ', $actualresult->answernotes);
                $state->trace = implode("\n", $actualresult->trace);
                $state->feedback = $actualresult->feedback;
                $state->debuginfo = $actualresult->debuginfo;
            } else {
                $state->score = '';
                $state->penalty = '';
                $state->answernote = '';
                $state->feedback = '';
                $state->debuginfo = '';
            }

            $state->testoutcome = true;
            $reason = array();
            if (is_null($state->expectedscore) != is_null($state->score) ||
                    abs($state->expectedscore - $state->score) > 10E-6) {
                $state->testoutcome = false;
                $reason[] = stack_string('score');
            }
            // If the expected penalty is null then we use the question default penalty.
            $penalty = $state->expectedpenalty;
            if (is_null($state->expectedpenalty)) {
                $penalty = $this->questionpenalty;
            }
            // If we have a "NULL" expected answer note we just ignore what happens to penalties here.
            if ('NULL' !== $state->expectedanswernote) {
                if (is_null($state->penalty) ||
                        abs($penalty - $state->penalty) > 10E-6) {
                    $state->testoutcome = false;
                    $reason[] = stack_string('penalty');
                }
            }
            if (!$this->test_answer_note($state->expectedanswernote, $actualresult->answernotes)) {
                $state->testoutcome = false;
                $reason[] = stack_string('answernote');
            }
            if (empty($reason)) {
                $state->reason = '';
            } else {
                $state->reason = ' ('.implode(', ', $reason).')';
            }

            $states[$prtname] = $state;
        }

        return $states;
    }

    /**
     * Test that the expected and actual answer notes match, to the level we can test.
     * @param string $expected the expected final answer note.
     * @param array $actual the actual answer notes returend.
     * @return bool whether the answer notes match sufficiently.
     */
    protected function test_answer_note($expected, $actual) {
        $lastactual = array_pop($actual);
        if ('NULL' == $expected) {
            return '' == trim($lastactual);
        }
        return trim($lastactual) == trim($expected);
    }

    /**
     * @return bool whether the test passed successfully.
     */
    public function passed() {
        foreach ($this->get_prt_states() as $state) {
            if (!$state->testoutcome) {
                return false;
            }
        }
        return true;
    }
}
