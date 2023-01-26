<?php

namespace api\controller;

use api\dtos\StackValidationResponse;
use api\util\StackQuestionLoader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ValidationController
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

        $validationResponse = new StackValidationResponse();

        if(!array_key_exists($data["inputName"], $question->inputs)) throw new \stack_exception('invalid input name');

        $validationResponse->Validation =
            $question->inputs[$data["inputName"]]->replace_validation_tags(
                $question->get_input_state(
                    $data["inputName"],
                    $data["answers"]
                ),
                $data["inputName"],
                "[[validation:{$data["inputName"]}]]"
            );

        $response->getBody()->write(json_encode($validationResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
