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
require_once(__DIR__ . '/../dtos/StackValidationResponse.php');
require_once(__DIR__ . '/../util/StackQuestionLoader.php');
require_once(__DIR__ . '/../util/StackSeedHelper.php');

use api\util\StackIframeHolder;
use api\dtos\StackValidationResponse;
use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ValidationController {
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

        $validationresponse = new StackValidationResponse();

        if (!array_key_exists($data["inputName"], $question->inputs)) {
            throw new \stack_exception('invalid input name');
        }

        $validationresponse->validation =
            $question->inputs[$data["inputName"]]->render_validation(
                $question->get_input_state(
                    $data["inputName"],
                    $data["answers"]
                ),
                $data["inputName"],
                null,
            );

        $validationresponse->iframes = StackIframeHolder::$iframes;
        $response->getBody()->write(json_encode($validationresponse));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
