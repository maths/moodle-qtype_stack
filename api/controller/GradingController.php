<?php

namespace api\controller;

use api\dtos\StackGradingResponse;
use api\util\StackPlotReplacer;
use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GradingController
{
    /**
     * @throws \stack_exception
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        //TODO: Validate
        $data = $request->getParsedBody();

        //Load Functions emulating Moodle
        require_once(__DIR__ . '/../emulation/MoodleEmulation.php');

        $question = StackQuestionLoader::loadXML($data["questionDefinition"]);

        StackSeedHelper::initializeSeed($question, $data["seed"]);

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
        $translate->search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)(\s*<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';
        $language = current_language();

        // If an input explicitly allows empty answers, and the response data dosnt contain a value for the input, set the input value to an empty string
        foreach ($question->inputs as $name => $input) {
            if($input->get_extra_option('allowempty') && !array_key_exists($name, $data['answers'])) {
                $data['answers'][$name] = '';
            }
        }

        $plots = [];
        $filePrefix = uniqid();
        $gradingResponse = new StackGradingResponse();
        $gradingResponse->isGradable = true;

        $scores = array();
        foreach ($question->prts as $index => $prt) {
            $result = $question->get_prt_result($index, $data['answers'], true);

            //If not all inputs required for the prt have been filled out, or the prt evaluation caused an error, we abort the grading, and indicate that this input state is not gradable
            if($result->get_errors() || !$question->has_necessary_prt_inputs($prt, $data['answers'], true)) {
                $gradingResponse = new StackGradingResponse();
                $gradingResponse->isGradable = false;

                $response->getBody()->write(json_encode($gradingResponse));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $feedbackStyle = $prt->get_feedbackstyle();

            $feedback = $result->get_feedback();
            $standardfeedback = $this->standard_prt_feedback($question, $result, $feedbackStyle);

            $overallFeedback = match ($feedbackStyle) {
                //Formative
                0 => $feedback,
                //Standard
                1, 2 => $standardfeedback . $feedback,
                //Compact
                // Symbolic
                3 => $standardfeedback,
                // Invalid
                default => "Invalid Feedback style"
            };


            $scores[$index] = $result->get_score();

            $gradingResponse->Prts[$index] = $translate->filter(
                \stack_maths::process_display_castext($overallFeedback),
                $language
            );
            array_push($plots, ...StackPlotReplacer::replace_plots($gradingResponse->Prts[$index], $filePrefix));
        }

        $score = 0;
        $weights = $question->get_parts_and_weights();
        foreach ($weights as $prt => $weight) {
            $score += $weights[$prt] * $scores[$prt];
        }

        $gradingResponse->Score = $score;
        $gradingResponse->SpecificFeedback = $translate->filter(
            $question->specificfeedbackinstantiated->get_rendered($question->castextprocessor),
            $language
        );
        array_push($plots, ...StackPlotReplacer::replace_plots($gradingResponse->SpecificFeedback, $filePrefix));

        $gradingResponse->GradingAssets = $plots;

        $response->getBody()->write(json_encode($gradingResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function standard_prt_feedback(\qtype_stack_question $question, \prt_evaluatable $result, $feedbackStyle)
    {
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
        if ($feedbackStyle === 2 || $feedbackStyle === 3) {
            $s = get_string('symbolicprt' . $class . 'feedback', 'qtype_stack');
            return \html_writer::tag('span', $s, array('class' => $class));
        }

        if ($question->$field) {
            return \html_writer::tag('div',
                \stack_maths::process_display_castext($question->$field->get_rendered($question->castextprocessor)),
                array('class' => $class)
            );
        }

        return '';
    }

}
