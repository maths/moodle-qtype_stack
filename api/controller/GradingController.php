<?php

namespace api\controller;

use api\dtos\StackGradingResponse;
use api\util\StackPlotReplacer;
use api\util\StackQuestionLoader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GradingController
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        //TODO: Validate
        $data = $request->getParsedBody();

        //Load Functions emulating Moodle
        require_once(__DIR__ . '/../emulation/MoodleEmulation.php');

        $question = StackQuestionLoader::loadXML($data["questionDefinition"]);

        if($question->has_random_variants()) {
            //If the specified seed is not in the deployed variant list, abort
            if(!in_array($data["seed"], $question->deployedseeds)) {
                throw new \Exception('The requested seed is not included in the deployed variants');
            }
            $question->seed = $data["seed"];
        } else {
            $question->seed = -1;
        }

        $question->initialise_question_from_seed();

        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        if (!empty($question->runtimeerrors)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
        }

        $plots = [];
        $filePrefix = uniqid();
        $gradingResponse = new StackGradingResponse();

        $scores = array();
        foreach ($question->prts as $index => $prt) {
            $result = $question->get_prt_result($index, $data['answers'], true);

            $feedback = $result->get_feedback();

            $scores[$index] = $result->get_score();

            //TODO: Invalid/Incomplete inputs?

            if($prt->get_feedbackstyle() === 1) {
                $feedback = $this->standard_prt_feedback($question, $result) . $feedback;
            }

            $gradingResponse->Prts[$index] = \stack_maths::process_display_castext($feedback);
            array_push($plots, ...StackPlotReplacer::replace_plots($gradingResponse->Prts[$index], $filePrefix));
        }

        $score = 0;
        $weights = $question->get_parts_and_weights();
        foreach ($weights as $prt => $weight) {
            $score += $weights[$prt] * $scores[$prt];
        }

        $gradingResponse->Score = $score;
        $gradingResponse->SpecificFeedback = $question->specificfeedbackinstantiated->get_rendered($question->castextprocessor);
        array_push($plots, ...StackPlotReplacer::replace_plots($gradingResponse->SpecificFeedback, $filePrefix));

        $gradingResponse->GradingAssets = $plots;

        $response->getBody()->write(json_encode($gradingResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function standard_prt_feedback(\qtype_stack_question $question, \prt_evaluatable $result)
    {
        if(!empty($result->get_errors())) {
            return '';
        }

        $field = '';
        if ($result->get_score() < 0.000001) {
            $field = 'prtincorrectinstantiated';
        } else if ($result->get_score() > 0.999999) {
            $field = 'prtcorrectinstantiated';
        } else {
            $field = 'prtpartiallycorrectinstantiated';
        }

        if ($question->$field) {
            return \stack_maths::process_display_castext($question->$field->get_rendered($question->castextprocessor));
        }

        return '';
    }

}
