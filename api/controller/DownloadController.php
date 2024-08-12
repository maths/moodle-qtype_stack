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

// This script handles file download from file download blocks.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\controller;
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../../stack/cas/castext2/castext2_static_replacer.class.php');

use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DownloadController {
    /**
     * @throws \stack_exception
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        // TO-DO: Validate.
        $data = $request->getParsedBody();
        $name = $data['filename'];
        $tdid = $data['fileid'];

        $question = StackQuestionLoader::loadxml($data["questionDefinition"])['question'];

        StackSeedHelper::initialize_seed($question, $data["seed"]);

        $question->initialise_question_from_seed();

        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        if (!empty($question->runtimeerrors)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
        }

        $question->get_cached('units');

        if (!isset($question->compiledcache['castext-td-' . $tdid])) {
            header('HTTP/1.0 404 Not Found');
            header('Content-Type: text/plain;charset=UTF-8');
            echo 'No such textdownload object in this question';
            die();
        }

        $ct = \castext2_evaluatable::make_from_compiled($question->compiledcache['castext-td-' .
            $tdid], $name, new \castext2_static_replacer($question->get_cached('static-castext-strings')));

        // Get the context from the question.
        $ses = new \stack_cas_session2([], $question->options, $question->seed);
        $question->add_question_vars_to_session($ses);

        $ses->add_statement($ct);

        // Is it valid?
        if (!$ses->get_valid()) {
            header('HTTP/1.0 500 Internal Server Error');
            header('Content-Type: text/plain;charset=UTF-8');
            echo 'Unknown issue related to the generation of this data.';
            die();
        }

        // Render it.
        $ses->instantiate();
        $content = $ct->get_rendered();
        $this->set_headers($name);
        echo($content);
        return $response;
    }

    /**
     * Separate out setting of headers for mocking as part of unit tests.
     *
     * @param string $name
     * @return void
     */
    public function set_headers($name) {
        header('HTTP/1.0 200 OK');
        header("Content-Disposition: attachment; filename=\"$name\"");
        if (strripos($name, '.csv') === strlen($name) - 4) {
            header('Content-Type: text/csv;charset=UTF-8');
        } else {
            header('Content-Type: text/plain;charset=UTF-8');
        }
    }

}
