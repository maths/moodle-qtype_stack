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
require_once(__DIR__ . '/../util/StackIframeHolder.php');
require_once(__DIR__ . '/../dtos/StackGradingResponse.php');
require_once(__DIR__ . '/../util/StackPlotReplacer.php');
require_once(__DIR__ . '/../util/StackQuestionLoader.php');
require_once(__DIR__ . '/../util/StackSeedHelper.php');

use api\dtos\StackGradingResponse;
use api\util\StackIframeHolder;
use api\util\StackPlotReplacer;
use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GradingController {
    /**
     * @throws \stack_exception
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        // TO-DO: Validate.
        $data = $request->getParsedBody();

        $question = StackQuestionLoader::loadxml($data["questionDefinition"])['question'];

        StackSeedHelper::initialize_seed($question, $data["seed"]);

        $question->initialise_question_from_seed();

        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        if (!empty($question->runtimeerrors)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
        }

        $translate = new \stack_multilang();
        // This is a hack, that restores the filter regex to the exact one used in moodle.
        // The modifications done by the stack team prevent the filter funcitonality from working correctly.
        $translate->search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang")' .
                             '{2}\s*>.*?<\/span>)(\s*<span(\s+lang="[a-zA-Z0-9_-]+"' .
                             '|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';
        $language = current_language();

        // If an input explicitly allows empty answers, and the response data doesn't
        // contain a value for the input, set the input value to an empty string.
        foreach ($question->inputs as $name => $input) {
            if ($input->get_extra_option('allowempty') && !array_key_exists($name, $data['answers'])) {
                $data['answers'][$name] = '';
            }
        }

        $plots = [];
        $storeprefix = uniqid();
        $gradingresponse = new StackGradingResponse();
        $gradingresponse->isgradable = true;

        $scores = [];
        foreach ($question->prts as $index => $prt) {
            $result = $question->get_prt_result($index, $data['answers'], true);

            // If not all inputs required for the prt have been filled out,
            // or the prt evaluation caused an error, we abort the grading,
            // and indicate that this input state is not gradable.
            if ($result->get_errors() || !$question->has_necessary_prt_inputs($prt, $data['answers'], true)) {
                $gradingresponse = new StackGradingResponse();
                $gradingresponse->isgradable = false;

                $response->getBody()->write(json_encode($gradingresponse));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $feedbackstyle = $prt->get_feedbackstyle();

            $feedback = $result->apply_placeholder_holder($result->get_feedback());
            $standardfeedback = $this->standard_prt_feedback($question, $result, $feedbackstyle);

            switch ($feedbackstyle) {
                // Formative.
                case 0:
                    $overallfeedback = $feedback;
                    break;
                // Standard.
                case 1:
                case 2:
                    $overallfeedback = $standardfeedback . $feedback;
                    break;
                // Compact.
                // Symbolic.
                case 3:
                    $overallfeedback = $standardfeedback;
                    break;
                // Invalid.
                default:
                    $overallfeedback = "Invalid Feedback style";
                    break;
            }

            $scores[$index] = $result->get_score();

            $gradingresponse->prts[$index] = $translate->filter(
                \stack_maths::process_display_castext($overallfeedback),
                $language
            );
            StackPlotReplacer::replace_plots($plots, $gradingresponse->prts[$index], "prt-".$index, $storeprefix);
        }

        $weights = $question->get_parts_and_weights();
        $scores['total'] = 0;
        foreach ($weights as $prt => $weight) {
            $prtscore = $weights[$prt] * $scores[$prt];
            $scores['total'] += $prtscore;
        }
        $weights['total'] = $question->defaultmark;

        $gradingresponse->score = $scores['total'];
        $gradingresponse->scores = $scores;
        $gradingresponse->scoreweights = $weights;
        $gradingresponse->specificfeedback = $translate->filter(
            $question->specificfeedbackinstantiated->apply_placeholder_holder(
                $question->specificfeedbackinstantiated->get_rendered($question->castextprocessor)
            ),
            $language
        );
        StackPlotReplacer::replace_plots($plots, $gradingresponse->specificfeedback, "specificfeedback", $storeprefix);

        $gradingresponse->gradingassets = (object) $plots;

        $gradingresponse->responsesummary = $question->summarise_response($data['answers']);
        $gradingresponse->iframes = StackIframeHolder::$iframes;
        $response->getBody()->write(json_encode($gradingresponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function standard_prt_feedback(\qtype_stack_question $question, \prt_evaluatable $result, $feedbackstyle) {
        $class = '';
        if ($result->get_score() < 0.000001) {
            $class = 'incorrect';
        } else if ($result->get_score() > 0.999999) {
            $class = 'correct';
        } else {
            $class = 'partiallycorrect';
        }

        $field = 'prt' . $class . 'instantiated';

        // Compact and symbolic only.
        if ($feedbackstyle === 2 || $feedbackstyle === 3) {
            $s = get_string('symbolicprt' . $class . 'feedback', 'qtype_stack');
            return \html_writer::tag('span', $s, ['class' => $class]);
        }

        if ($question->$field) {
            return \html_writer::tag('div',
                \stack_maths::process_display_castext(
                    $question->$field->apply_placeholder_holder($question->$field->get_rendered($question->castextprocessor))
                ),
                ['class' => $class]
            );
        }

        return '';
    }

}
