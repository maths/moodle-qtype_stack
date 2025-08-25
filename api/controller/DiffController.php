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

/**
 * This script a request to diff a question with the defaults.
 *
 * @package    qtype_stack
 * @copyright  2023 RWTH Aachen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace api\controller;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../dtos/StackRenderResponse.php');
require_once(__DIR__ . '/../util/StackQuestionLoader.php');

use api\dtos\StackRenderResponse;
use api\util\StackQuestionLoader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class DiffController {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function __invoke(Request $request, Response $response, array $args): Response {
        // TO-DO: Validate.
        $data = $request->getParsedBody();

        $renderresponse = new StackRenderResponse();
        $diff = StackQuestionLoader::detect_differences($data["questionDefinition"]);
        $renderresponse->diff = $diff;

        $response->getBody()->write(json_encode($renderresponse));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
