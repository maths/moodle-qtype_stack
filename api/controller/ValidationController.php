<?php

namespace api\controller;

use api\dtos\StackValidationResponse;
use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ValidationController
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

        $validationResponse = new StackValidationResponse();

        if(!array_key_exists($data["inputName"], $question->inputs)) throw new \stack_exception('invalid input name');

        $validationResponse->Validation =
            $question->inputs[$data["inputName"]]->render_validation(
                $question->get_input_state(
                    $data["inputName"],
                    $data["answers"]
                ),
                $data["inputName"]
            );

        $response->getBody()->write(json_encode($validationResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
