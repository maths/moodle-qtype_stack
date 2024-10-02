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

// This script handles the running of question tests for a supplied question
//
// @copyright  2024 University of Edinburgh
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\controller;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../dtos/StackTestResponse.php');
require_once(__DIR__ . '/../util/StackQuestionLoader.php');
require_once(__DIR__ . '/../util/StackSeedHelper.php');
require_once(__DIR__ . '/../../stack/questiontestresult.php');

use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use api\dtos\StackTestResponse;

/**
 * Handles the running of question tests for a supplied question
 *
 * Based heavily on bulktester.class.php and questiontest.php but they rely on
 * Moodle context and create HTML. Here we're just getting test results and
 * leaving display for the front end.
 */
class TestController {
    /**
     * @throws \stack_exception
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        // TO-DO: Validate.
        $data = $request->getParsedBody();

        list('question' => $question, 'testcases' => $testcases) = StackQuestionLoader::loadxml($data["questionDefinition"], true);
        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        $testresponse = new StackTestResponse();
        $testresponse->name = $question->name;

        // We want to flag if the question is missing general feedback, has deployed seeds,
        // has random variants and/or has tests. It's up to the front end to decide what to do with that info.
        if (trim($question->generalfeedback) !== '') {
            $testresponse->isgeneralfeedback = true;
        }

        if (!empty($question->deployedseeds)) {
            $testresponse->isdeployedseeds = true;
        }

        if ($question->has_random_variants()) {
            $testresponse->israndomvariants = true;
        }

        if ($testcases) {
            $testresponse->istests = true;
        }

        // If the question uses random variants but has no deployed seeds we can't even initialise
        // the question so return response.
        if ($testresponse->israndomvariants && !$testresponse->isdeployedseeds) {
            $testresponse->results = [];
            $response->getBody()->write(json_encode($testresponse));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            StackSeedHelper::initialize_seed($question, $data["seed"]);
            $question->initialise_question_from_seed();
        }

        // Check for upgrade errors and return response immediately if so.
        // Errors will be listed in overall response messages.
        $dummycontext = new \stdClass(); // Required for unit tests.
        $dummycontext->id = 0;
        $upgradeerrors = $question->validate_against_stackversion($dummycontext);
        if ($upgradeerrors != '') {
            $testresponse->isupgradeerror = true;
            $testresponse->messages = $upgradeerrors;
            $testresponse->results = [];
            $response->getBody()->write(json_encode($testresponse));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // Create test results for each deployed seed. If no random variants, then use 'noseed' as
        // array index.
        if (empty($question->deployedseeds)) {
            try {
                $testresponse->results = [
                    'noseed' => $this->qtype_stack_test_question($question, $testcases, null),
                ];
            } catch (\stack_exception $e) {
                $testresponse->results['noseed'] = [
                        'passes' => null,
                        'fails' => null,
                        'messages' => stack_string('errors') . ' : ' . $e,
                        'outcomes' => null,
                    ];
            }
        } else {
            foreach ($question->deployedseeds as $seed) {
                try {
                    $testresponse->results[$seed] = $this->qtype_stack_test_question($question, $testcases, $seed);
                } catch (\stack_exception $e) {
                    $testresponse->results[$seed] = [
                        'passes' => null,
                        'fails' => null,
                        'messages' => stack_string('errors') . ' : ' . $e,
                        'outcomes' => null,
                    ];
                }
            }
        }
        $response->getBody()->write(json_encode($testresponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Run the tests for one variant of one question and display the results.
     *
     * @param object $question the question to test.
     * @param array $testcases the questions tests.
     * @param int|null $seed if we want to force a particular version.
     * @return array with elements:
     *              int passes - number of tests passed.
     *              int fails - number of tests failed.
     *              string messages - error messages.
     *              array outcomes - detailed info on the outcomes of the test.
     *                  (See stack_question_test_result->passed_with_reasons.
     *                   TO-DO Is it worth creating a class for this?)
     */
    public function qtype_stack_test_question($question, $testcases, $seed = null) {
        if (!is_null($seed)) {
            $question->seed = (int) $seed;
        }
        // Execute the tests for each seed.
        // Return number of passed tests, number of failed tests, any error messages
        // and outcomes - an array of summaries of the test results.
        $passes = 0;
        $fails = 0;
        $message = '';
        $outcomes = [];
        $question->options->set_option('decimals', '.');
        foreach ($testcases as $testcase) {
            $response = \stack_question_test::compute_response($question, $testcase->inputs);
            $results = $testcase->process_results($question, $response);
            $summary = $results->passed_with_reasons();
            $outcomes[$testcase->testcase] = $summary;
            if ($summary['passed']) {
                $passes += 1;
            } else {
                $fails += 1;
                $message .= $summary['reason'];
            }
        }

        // If we don't have any tests, check to see if the model answers give a score of 1.
        if (count($testcases) === 0 && count($question->prts) > 0) {
            $inputs = [];
            foreach ($question->inputs as $inputname => $input) {
                $inputs[$inputname] = $input->get_teacher_answer_testcase();
            }
            $qtest = new \stack_question_test(stack_string('autotestcase'), $inputs);
            $response = \stack_question_test::compute_response($question, $inputs);

            foreach ($question->prts as $prtname => $prt) {
                $result = $question->get_prt_result($prtname, $response, false);
                // We could just check if score === 1 at this point but by creating
                // a test and running it we get the full outcomes in the same
                // format as above.
                $answernotes = $result->get_answernotes();
                $answernote = [end($answernotes)];
                $qtest->add_expected_result($prtname, new \stack_potentialresponse_tree_state(
                    1, true, 1, 0, '', $answernote));
            }
            $results = $qtest->process_results($question, $response);
            $summary = $results->passed_with_reasons();
            $outcomes[$qtest->testcase] = $summary;
            if ($summary['passed']) {
                $passes = 1;
            } else {
                $fails = 1;
                $message = stack_string('defaulttestfail');
            }
        }

        $generalfeedback = $question->get_generalfeedback_castext();

        $generalfeedback->get_rendered($question->castextprocessor);
        if ($generalfeedback->get_errors() != '') {
            $s = stack_string('stackInstall_testsuite_errors') . '  ' .
                stack_string('generalfeedback') . ': ' . $generalfeedback->get_errors();
            $message .= $s;
        }

        if (!empty($question->runtimeerrors)) {
            $s = stack_string('stackInstall_testsuite_errors') . ' ' .
                implode(' ', array_keys($question->runtimeerrors));
            $message .= $s;
        }

        return [
            'passes' => $passes,
            'fails' => $fails,
            'messages' => $message,
            'outcomes' => $outcomes,
        ];
    }
}
