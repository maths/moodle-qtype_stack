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

// Holds the data defining one question test.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/questiontestresult.php');
require_once(__DIR__ . '/potentialresponsetree.class.php');

class stack_question_test {
    /**
     * @var int|null test-case number, if this is a real test stored in the database, else null.
     */
    public $testcase;

    /**
     * @var array input name => value to be entered.
     */
    public $inputs;

    /**
     * @var array prt name => stack_potentialresponse_tree_state object
     */
    public $expectedresults = array();

    /**
     * Constructor
     * @param array $inputs input name => value to enter.
     * @param int $testcase test-case number, if this is a real test stored in the database.
     */
    public function __construct($inputs, $testcase = null) {
        $this->inputs = $inputs;
        $this->testcase = $testcase;
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

        $slot = $quba->add_question($question, $question->defaultmark);
        $quba->start_question($slot, $seed);

        $response = self::compute_response($question, $this->inputs);
        $quba->process_action($slot, $response);

        $results = new stack_question_test_result($this);
        foreach ($this->inputs as $inputname => $notused) {
            $inputstate = $question->get_input_state($inputname, $response);
            // The _val below is a hack.  Not all inputnames exist explicitly in
            // the response, but the _val does. Some inputs, e.g. matrices have
            // many entries in the response so none match $response[$inputname].
            // Of course, a teacher may have left a test case blank in which case the input isn't there either.
            $inputresponse = '';
            if (array_key_exists($inputname, $response)) {
                $inputresponse = $response[$inputname];
            } else if (array_key_exists($inputname.'_val', $response)) {
                $inputresponse = $response[$inputname.'_val'];
            }
            $results->set_input_state($inputname, $inputresponse,
                    $inputstate->contentsdisplayed, $inputstate->status, $inputstate->errors);
        }

        foreach ($this->expectedresults as $prtname => $expectedresult) {
            $result = $question->get_prt_result($prtname, $response, false);
            $results->set_prt_result($prtname, $result);
        }

        if ($this->testcase) {
            $this->save_result($question, $results);
        }

        return $results;
    }

    /**
     * Create the actual response data. The response data in the test case may
     * include expressions in terms of the question variables.
     * @param qtype_stack_question $question the question - with $question->session initialised.
     * @return array the respones to send to $quba->process_action.
     */
    public static function compute_response(qtype_stack_question $question, $inputs) {
        // If the question has simp:false, then the local options should reflect this.
        // In this case, test constructors (question authors) will need to explicitly simplify their test case constructions.
        $localoptions = clone $question->options;

        // Start with the question variables (note that order matters here).
        $cascontext = new stack_cas_session(null, $localoptions, $question->seed);
        $question->add_question_vars_to_session($cascontext);

        // Turn off simplification - we *always* need test cases to be unsimplified, even if the question option is true.
        $vars = array();
        $cs = new stack_cas_casstring('false');
        $cs->set_key('simp');
        $vars[] = $cs;
        // Now add the expressions we want evaluated.
        foreach ($inputs as $name => $value) {
            if ('' !== $value) {
                $cs = new stack_cas_casstring($value);
                if ($cs->get_valid('t')) {
                    $cs->set_key('testresponse_' . $name);
                    $vars[] = $cs;
                }
            }
        }
        $cascontext->add_vars($vars);
        $cascontext->instantiate();

        $response = array();
        foreach ($inputs as $name => $notused) {
            $computedinput = $cascontext->get_value_key('testresponse_' . $name, true);
            // In the case we start with an invalid input, and hence don't send it to the CAS.
            // We want the response to constitute the raw invalid input.
            // This permits invalid expressions in the inputs, and to compute with valid expressions.
            if ('' == $computedinput) {
                $computedinput = $inputs[$name];
            }
            if (array_key_exists($name, $question->inputs)) {
                $response = array_merge($response, $question->inputs[$name]->maxima_to_response_array($computedinput));
            }
        }
        return $response;
    }

    /**
     * @param string $inputname the name of one of the inputs.
     * @return string the value to be entered into that input.
     */
    public function get_input($inputname) {
        return $this->inputs[$inputname];
    }

    /**
     * Store the outcome of running a test in qtype_stack_qtest_results.
     *
     * @param qtype_stack_question $question the question being tested.
     * @param stack_question_test_result $result the test result.
     */
    protected function save_result(qtype_stack_question $question,
            stack_question_test_result $result) {
        global $DB;

        $existingresult = $DB->get_record('qtype_stack_qtest_results',
                array('questionid' => $question->id, 'testcase' => $this->testcase, 'seed' => $question->seed),
                '*', IGNORE_MISSING);

        if ($existingresult) {
            $existingresult->result = (int) $result->passed();
            $existingresult->timerun = time();
            $DB->update_record('qtype_stack_qtest_results', $existingresult);
        } else {
            $DB->insert_record('qtype_stack_qtest_results', array(
                    'questionid' => $question->id,
                    'testcase' => $this->testcase,
                    'seed' => $question->seed,
                    'result' => $result->passed(),
                    'timerun' => time(),
            ));
        }
    }
}
