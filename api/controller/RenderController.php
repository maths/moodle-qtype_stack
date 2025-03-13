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
require_once(__DIR__ . '/../dtos/StackRenderResponse.php');
require_once(__DIR__ . '/../util/StackPlotReplacer.php');
require_once(__DIR__ . '/../util/StackQuestionLoader.php');
require_once(__DIR__ . '/../util/StackSeedHelper.php');

use api\util\StackIframeHolder;
use api\dtos\StackRenderInput;
use api\dtos\StackRenderResponse;
use api\util\StackPlotReplacer;
use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RenderController {
    /**
     * @throws \stack_exception
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        // TO-DO: Validate.
        $data = $request->getParsedBody();

        $question = StackQuestionLoader::loadxml($data["questionDefinition"])['question'];

        StackSeedHelper::initialize_seed($question, $data["seed"]);

        // Handle Pluginfiles.
        $storeprefix = uniqid();
        StackPlotReplacer::persist_plugin_files($question, $storeprefix);

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

        $renderresponse = new StackRenderResponse();
        $plots = [];

        $renderresponse->questionrender = $translate->filter(
            $question->questiontextinstantiated->apply_placeholder_holder(
                \stack_maths::process_display_castext(
                    $question->questiontextinstantiated->get_rendered(
                        $question->castextprocessor
                    )
                )
            ),
            $language
        );

        StackPlotReplacer::replace_plots($plots, $renderresponse->questionrender, "render", $storeprefix);

        $renderresponse->questionsamplesolutiontext = $translate->filter(
            $question->get_generalfeedback_castext()->apply_placeholder_holder(
                $question->get_generalfeedback_castext()->get_rendered($question->castextprocessor)
            ),
            $language
        );

        StackPlotReplacer::replace_plots($plots, $renderresponse->questionsamplesolutiontext, "samplesolution", $storeprefix);

        $inputs = [];
        foreach ($question->inputs as $name => $input) {
            $apiinput = new StackRenderInput();

            $apiinput->samplesolution = $input->get_api_solution($question->get_ta_for_input($name));
            $apiinput->samplesolutionrender = $input->get_api_solution_render($question->get_ta_render_for_input($name));

            $apiinput->validationtype = $input->get_parameter('showValidation', 1);
            $apiinput->configuration = $input->render_api_data($question->get_ta_for_input($name));

            if (array_key_exists('options', $apiinput->configuration)) {
                foreach ($apiinput->configuration['options'] as $key => &$option) {
                    StackPlotReplacer::replace_plots($plots, $option, "input-".$name."-".$key, $storeprefix);
                }
            }

            $inputs[$name] = $apiinput;

            if ($data['renderInputs']) {
                $tavalue = $question->get_ta_for_input($name);
                $fieldname = $data['renderInputs'] . $name;
                $state = $question->get_input_state($name, []);
                $render = $input->render($state, $fieldname, $data['readOnly'], $tavalue);
                StackPlotReplacer::replace_plots($plots, $render, "answer-".$name, $storeprefix);
            }

            $inputs[$name]->render = $render;
        }

        // Necessary, as php will otherwise encode this as an empty array, instead of an empty object.
        $renderresponse->questioninputs = (object) $inputs;

        $renderresponse->questionassets = (object) $plots;

        $renderresponse->questionseed = $question->seed;
        $renderresponse->questionvariants = $question->deployedseeds;
        $renderresponse->iframes = StackIframeHolder::$iframes;

        $response->getBody()->write(json_encode($renderresponse));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
