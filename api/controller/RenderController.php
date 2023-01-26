<?php

namespace api\controller;

use api\dtos\StackRenderInput;
use api\dtos\StackRenderResponse;
use api\util\StackPlotReplacer;
use api\util\StackQuestionLoader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RenderController
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        //TODO: Validate
        $data = $request->getParsedBody();

        //Load Functions emulating Moodle
        require_once(__DIR__ . '/../emulation/MoodleEmulation.php');

        $question = StackQuestionLoader::loadXML($data["questionDefinition"]);

        if($question->has_random_variants()) {
            //We require the xml to include deployed variants
            if(count($question->deployedseeds) === 0) {
                throw new \Exception(get_string('api_no_deployed_variants', null));
            }

            //If no seed has been specified, use the first deployed variant
            if(!array_key_exists('seed', $data) || !in_array($data["seed"], $question->deployedseeds)) {
                $data["seed"] = $question->deployedseeds[0];
            }

            $question->seed = $data["seed"];
        } else {
            //We just set any seed here, to simplify the handling on the catnip side
            $question->seed = -1;
        }

        //handle Pluginfiles
        $filePrefix = uniqid();
        StackPlotReplacer::persistPluginfiles($question, $filePrefix);

        $question->initialise_question_from_seed();

        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        if (!empty($question->runtimeerrors)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
        }


        $renderResponse = new StackRenderResponse();
        $plots = [];
        $renderResponse->QuestionRender = \stack_maths::process_display_castext($question->questiontextinstantiated->get_rendered($question->castextprocessor));
        array_push($plots, ...StackPlotReplacer::replace_plots($renderResponse->QuestionRender, $filePrefix));

        $renderResponse->QuestionSampleSolutionText = $question->get_generalfeedback_castext()->get_rendered($question->castextprocessor);
        array_push($plots, ...StackPlotReplacer::replace_plots($renderResponse->QuestionSampleSolutionText, $filePrefix));

        $renderResponse->QuestionInputs = array();
        foreach ($question->inputs as $name => $input) {
            $apiInput = new StackRenderInput();


            $apiInput->SampleSolution = $input->getApiSolution($question->get_ta_for_input($name));
            $apiInput->SampleSolutionRender = $input->getApiSolutionRender($question->get_ta_render_for_input($name));

            $apiInput->ValidationType = $input->get_parameter('showValidation', 1);
            $apiInput->Configuration = $input->renderApiData($question->get_ta_for_input($name));

            if(array_key_exists('options', $apiInput->Configuration)) {
                foreach ($apiInput->Configuration['options'] as &$option) {
                    array_push($plots, ...StackPlotReplacer::replace_plots($option, $filePrefix));
                }
            }

            $renderResponse->QuestionInputs[$name] = $apiInput;
        }

        $renderResponse->QuestionAssets = $plots;

        $renderResponse->QuestionSeed = $question->seed;
        $renderResponse->QuestionVariants = $question->deployedseeds;

        $response->getBody()->write(json_encode($renderResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
