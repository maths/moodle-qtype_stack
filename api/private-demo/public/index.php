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
 * Private STACK API demo frontend.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once('../config.php');
require_once(__DIR__ . '../../emulation/MoodleEmulation.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$responsefactory = $app->getResponseFactory();

$app->add(function ($request, $handler) use ($responsefactory) {
    try {
        return $handler->handle($request);
    } catch (stack_private_demo_http_exception $exception) {
        $response = $responsefactory->createResponse();
        if (str_starts_with($request->getUri()->getPath(), '/demo/')) {
            return stack_private_demo_json_response(
                $response,
                ['message' => $exception->getMessage()],
                $exception->get_status()
            );
        }
        return stack_private_demo_text_response($response, $exception->getMessage(), $exception->get_status());
    }
});

// NB: This essentially allows access to all your questions.
// It's used in the current home page with question search.
$app->get('/demo/questions', function ($request, ResponseInterface $response) {
    return stack_private_demo_json_response($response, stack_private_demo_catalogue());
});

foreach (['/cors.php', '/demo/cors.php'] as $route) {
    $app->map(['GET', 'OPTIONS'], $route, function ($request, ResponseInterface $response) {
        $params = $request->getQueryParams();
        return stack_private_demo_cors_asset($request, $response, $params['name'] ?? '');
    });
}

$app->get('/demo/plot.php/{filename:.+}', function ($request, ResponseInterface $response, $args) {
    return stack_private_demo_proxy_plot($response, $args['filename']);
});

$app->post('/demo/{route:render|validate|grade|download|diff}', function ($request, ResponseInterface $response, $args) {
    $route = $args['route'];
    $data = stack_private_demo_request_json($request);
    $payload = stack_private_demo_api_payload($data, $route);
    if ($route === 'diff') {
        $payload = ['questionDefinition' => $payload['questionDefinition']];
    }
    return stack_private_demo_proxy_json($request, $response, $route, $payload);
});

$app->get('/embed', function ($request, ResponseInterface $response) {
    return stack_private_demo_template_response(
        $response,
        __DIR__ . '/../embed.php',
        ['queryparams' => $request->getQueryParams()]
    );
});

foreach (['/', '/index.php'] as $route) {
    $app->get($route, function ($request, ResponseInterface $response) {
        return stack_private_demo_template_response($response, __DIR__ . '/../home.php');
    });
}

$app->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], '/{routes:.+}', function ($request, ResponseInterface $response) {
    if (str_starts_with($request->getUri()->getPath(), '/demo/')) {
        return stack_private_demo_json_response($response, ['message' => 'Not found'], 404);
    }
    return $response
        ->withStatus(302)
        ->withHeader('Location', '/index.php');
});

$app->run();
