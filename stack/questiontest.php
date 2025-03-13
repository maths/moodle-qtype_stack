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

class stack_question_test {
    /**
     * @var string Give each testcase a meaningful description.
     */
    public $description;

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
    public $expectedresults = [];

    /**
     * Constructor
     * @param array $inputs input name => value to enter.
     * @param int $testcase test-case number, if this is a real test stored in the database.
     */
    public function __construct($description, $inputs, $testcase = null) {
        $this->description = $description;
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
     * @param int $questionid The database id of the question to test.
     * @param int $seed the random seed to use.
     * @param context_course $context The course in which this question takes place.
     * @return stack_question_test_result the test results.
     */
    public function test_question($questionid, $seed, $context) {
        // Create a completely clean version of the question usage we will use.
        // Evaluated state is stored in question variables etc.
        $question = question_bank::load_question($questionid);
        // Hard-wire testing to use the decimal point.
        // Teachers must use strict Maxima syntax, including in test case construction.
        // I appreciate teachers will, reasonably, want to test the input mechanism.
        // The added internal complexity here is serious.
        // This complexity includes things like matrix input types which need a valid Maxima expression as the value of the input.
        $question->options->set_option('decimals', '.');
        if (!is_null($seed)) {
            $question->seed = (int) $seed;
        }
        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
        $quba->set_preferred_behaviour('adaptive');
        $slot = $quba->add_question($question, $question->defaultmark);
        $quba->start_question($slot, $seed);

        $response = self::compute_response($question, $this->inputs);
        $quba->process_action($slot, $response);

        $results = $this->process_results($question, $response);

        if ($this->testcase) {
            $this->save_result($question, $results);
        }

        return $results;
    }

    /**
     * Seperate out from Moodle-specific parts of test_question() to allow
     * use in the API
     *
     * @param question_definition $question
     * @param array $response
     * @return stack_question_test_result
     */
    public function process_results($question, $response) {
        // We don't permit completely empty test cases.
        // Completely empty test cases always pass, which is spurious in the bulk test.
        $emptytestcase = true;
        $results = new stack_question_test_result($this);
        $results->set_questionpenalty($question->penalty);
        foreach ($this->inputs as $inputname => $notused) {
            // Check input still exits, could have been deleted in a question.
            if (array_key_exists($inputname, $question->inputs)) {
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
                if ($inputresponse != '') {
                    $emptytestcase = false;
                }
                $results->set_input_state($inputname, $inputresponse, $inputstate->contentsmodified,
                    $inputstate->contentsdisplayed, $inputstate->status, $inputstate->errors);
            }
        }

        foreach ($this->expectedresults as $prtname => $expectedresult) {
            if (implode(' | ', $expectedresult->answernotes) !== 'NULL') {
                $emptytestcase = false;
            }
            $result = $question->get_prt_result($prtname, $response, false);
            // Adapted from renderer.php prt_feedback_display.
            $feedback = $result->get_feedback();
            $feedback = format_text(stack_maths::process_display_castext($feedback),
                    FORMAT_HTML, ['noclean' => true, 'para' => false, 'allowid' => true]);

            $result->override_feedback($feedback);
            $results->set_prt_result($prtname, $result);

        }

        $results->emptytestcase = $emptytestcase;
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
        // In this case, question authors will need to explicitly simplify their test case constructions.
        $localoptions = clone $question->options;

        // Start with the question variables (note that order matters here).
        $cascontext = new stack_cas_session2([], $localoptions, $question->seed);
        $question->add_question_vars_to_session($cascontext);

        // Add the correct answer for all inputs.
        foreach ($question->inputs as $name => $input) {
            $cs = stack_ast_container::make_from_teacher_source($name . ':' . $input->get_teacher_answer(),
                    '', new stack_cas_security());
            $cascontext->add_statement($cs);
        }

        // Turn off simplification - we need test cases to be unsimplified, even if the question option is true.
        $vars = [];
        $cs = stack_ast_container::make_from_teacher_source('simp:false' , '', new stack_cas_security());
        $vars['simp'] = $cs;
        // Now add the expressions we want evaluated.
        foreach ($inputs as $name => $value) {
            // Check the input still exits since it could have been deleted in a question.
            if ('' !== $value && array_key_exists($name, $question->inputs)) {
                $input = $question->inputs[$name];
                if (substr($value, 0, 3) === 'CT:') {
                    $cs = castext2_evaluatable::make_from_source(substr($value, 3), '');
                } else {
                    $val = 'testresponse_' . $name . ':' . $value;
                    // Except if the input simplifies, then so should the generated testcase.
                    // The input will simplify again.
                    // We may need to create test cases which will generate errors, such as makelist.
                    if ($input->get_extra_option('simp')) {
                        $val = 'testresponse_' . $name . ':ev(' . $value .',simp)';
                    }
                    $cs = stack_ast_container::make_from_teacher_source($val , '', new stack_cas_security());
                }
                if ($cs->get_valid() && substr($value, 0, 4) != 'RAW:') {
                    $vars[$name] = $cs;
                }
            }
        }
        $cascontext->add_statements($vars);
        if ($cascontext->get_valid()) {
            $cascontext->instantiate();
        }
        $response = [];
        foreach ($inputs as $name => $value) {
            $var = null;
            $computedinput = '';
            if (array_key_exists($name, $vars)) {
                $var = $vars[$name];
                if (get_class($var) === 'stack_ast_container' && $var->is_correctly_evaluated()) {
                    $computedinput = $var->get_dispvalue();
                }
                if (get_class($var) === 'castext2_evaluatable') {
                    $computedinput = $var->get_rendered();
                }
            }
            // In the case we start with an invalid input, and hence don't send it to the CAS.
            // We want the response to constitute the raw invalid input.
            // This permits invalid expressions in the inputs, and to compute with valid expressions.
            if ('' == $computedinput) {
                $computedinput = $inputs[$name];
            }
            // Use any "raw" test case value.
            if (substr($value, 0, 4) === 'RAW:') {
                $computedinput = substr($value, 4);
            }
            if (array_key_exists($name, $question->inputs)) {
                // Remove things like apostrophies in test case inputs so we don't create an invalid student input.
                // 4.3. changes this.
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
                ['questionid' => $question->id, 'testcase' => $this->testcase, 'seed' => $question->seed],
                '*', IGNORE_MISSING);

        if ($existingresult) {
            $existingresult->result = (int) $result->passed();
            $existingresult->timerun = time();
            $DB->update_record('qtype_stack_qtest_results', $existingresult);
        } else {
            $DB->insert_record('qtype_stack_qtest_results', [
                'questionid' => $question->id,
                'testcase' => $this->testcase,
                'seed' => $question->seed,
                'result' => $result->passed(),
                'timerun' => time(),
            ]);
        }
    }
}
