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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
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

class TestController {
    /**
     * @throws \stack_exception
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        // TO-DO: Validate.
        $data = $request->getParsedBody();

        list('question' => $question, 'testcases' => $testcases) = StackQuestionLoader::loadxml($data["questionDefinition"], true);
        StackSeedHelper::initialize_seed($question, $data["seed"]);

        $question->initialise_question_from_seed();
        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        $testresponse = new StackTestResponse();
        $testresponse->name = $question->name;

        if (!empty($question->runtimeerrors)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
        }

        $upgradeerrors = $question->validate_against_stackversion(null);
        if ($upgradeerrors != '') {
            $testresponse->isupgradeerror = true;
            $testresponse->results = [
                'noseed' => [
                    'passes' => null,
                    'fails' => null,
                    'messages' => [$upgradeerrors],
                ]
            ];
        }

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

       if (empty($question->deployedseeds)) {
            try {
                $testresponse->results = [
                    'noseed' => $this->qtype_stack_test_question($question, $testcases, null)
                ];
            } catch (stack_exception $e) {
                $testresponse->results = [
                    'noseed' => [
                        'passes' => null,
                        'fails' => null,
                        'messages' => stack_string('errors') . ' : ' . $e,
                    ]
                ];
            }
        } else {
            foreach ($question->deployedseeds as $seed) {
                // TO-DO Skipped test for when there's no test?
                try {
                    $testresponse->results[$seed] = $this->qtype_stack_test_question($question, $testcases, $seed);
                } catch (stack_exception $e) {
                    $testresponse->results[$seed] = [
                        'passes' => null,
                        'fails' => null,
                        'messages' => stack_string('errors') . ' : ' . $e,
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
     * @param \qtype_stack_question $question the question to test.
     * @param int|null $seed if we want to force a particular version.
     * @return array with two elements:
     *              bool true if the tests passed, else false.
     *              sring message summarising the number of passes and fails.
     */
    public function qtype_stack_test_question($question, $testcases, $seed = null) {
        flush(); // Force output to prevent timeouts and to make progress clear.
        gc_collect_cycles(); // Because PHP's default memory management is rubbish.

        if (!is_null($seed)) {
            $question->seed = (int) $seed;
        }
        $emptytestcase = true;
        // Execute the tests.
        $passes = 0;
        $fails = 0;

        foreach ($testcases as $testcase) {
            $results = new \stack_question_test_result($testcase);
            $results->set_questionpenalty($question->penalty);
            $response = \stack_question_test::compute_response($question, $testcase->inputs);
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
            foreach ($testcase->expectedresults as $prtname => $expected) {
                if (implode(' | ', $expected->answernotes) !== 'NULL') {
                    $emptytestcase = false;
                }
                $result = $question->get_prt_result($prtname, $response, true);
                $feedback = $result->get_feedback();
                $feedback = format_text(\stack_maths::process_display_castext($feedback),
                        FORMAT_HTML, ['noclean' => true, 'para' => false]);

                $result->override_feedback($feedback);
                $results->set_prt_result($prtname, $result);
                $results->emptytestcase = $emptytestcase;
            }
            if ($results->passed()) {
                $passes += 1;
            } else {
                $fails += 1;
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

        flush(); // Force output to prevent timeouts and to make progress clear.

        return [
            'passes' => $passes,
            'fails' => $fails,
            'messages' => $message,
        ];
    }
}
