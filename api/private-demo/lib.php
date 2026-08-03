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
 * Shared helpers for the private STACK API demo frontend.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

define('STACK_PRIVATE_DEMO_ROOT', realpath(__DIR__ . '/../..'));
define(
    'STACK_PRIVATE_DEMO_LIBRARY_ROOT',
    realpath(STACK_PRIVATE_DEMO_ROOT . '/samplequestions/' . trim(getenv('STACK_PRIVATE_DEMO_LIBRARY') ?: 'stacklibrary', '/'))
);
define('STACK_PRIVATE_DEMO_CORS_ROOT', realpath(STACK_PRIVATE_DEMO_ROOT . '/corsscripts'));
define('STACK_PRIVATE_DEMO_MANIFEST', __DIR__ . '/assets/question-manifest.json');
define('STACK_PRIVATE_DEMO_API_BASE', rtrim(getenv('STACK_PRIVATE_DEMO_API_URL') ?: 'http://api', '/'));

/**
 * HTTP error for the private demo frontend.
 */
class stack_private_demo_http_exception extends RuntimeException {
    /**
     * Get the HTTP status.
     *
     * @return int HTTP status.
     */
    public function get_status() {
        return $this->getCode() ?: 500;
    }
}

/**
 * Raise an HTTP error.
 *
 * @param string $message Error message.
 * @param int $status HTTP status.
 * @throws stack_private_demo_http_exception
 */
function stack_private_demo_error($message, $status = 400) {
    throw new stack_private_demo_http_exception($message, $status);
}

/**
 * Write a JSON response.
 *
 * @param object $response Response.
 * @param mixed $data Response data.
 * @param int $status HTTP status.
 * @return object Response.
 */
function stack_private_demo_json_response($response, $data, $status = 200) {
    $json = json_encode($data, JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        stack_private_demo_error('Could not encode JSON response.', 500);
    }

    $response->getBody()->write($json);
    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json;charset=UTF-8');
}

/**
 * Write a text response.
 *
 * @param object $response Response.
 * @param string $text Response text.
 * @param int $status HTTP status.
 * @return object Response.
 */
function stack_private_demo_text_response($response, $text, $status = 200) {
    $response->getBody()->write($text);
    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'text/plain;charset=UTF-8');
}

/**
 * Render a local PHP template into a response.
 *
 * @param object $response Response.
 * @param string $template Template path.
 * @param array $data Template data.
 * @return object Response.
 */
function stack_private_demo_template_response($response, $template, $data = []) {
    $queryparams = $data['queryparams'] ?? null;

    ob_start();
    require($template);
    $response->getBody()->write(ob_get_clean());
    return $response->withHeader('Content-Type', 'text/html;charset=UTF-8');
}

/**
 * Resolve a relative path beneath a configured root directory.
 *
 * @param string|false $root Root directory.
 * @param string $relativepath Relative path.
 * @return string|false Resolved file path, or false if unavailable.
 */
function stack_private_demo_resolve_file($root, $relativepath) {
    if (!is_string($root) || $root === '' || !is_dir($root) || !is_string($relativepath) || $relativepath === '') {
        return false;
    }

    $fullpath = realpath($root . DIRECTORY_SEPARATOR . $relativepath);
    if ($fullpath === false || !str_starts_with($fullpath, $root . DIRECTORY_SEPARATOR) || !is_file($fullpath)) {
        return false;
    }

    return $fullpath;
}

/**
 * Load the question manifest checked into the repository.
 *
 * @return array Manifest.
 */
function stack_private_demo_manifest() {
    if (!file_exists(STACK_PRIVATE_DEMO_MANIFEST)) {
        stack_private_demo_error('Question manifest is not available.', 500);
    }

    $manifest = json_decode(file_get_contents(STACK_PRIVATE_DEMO_MANIFEST), true);
    if (!is_array($manifest) || !isset($manifest['questions']) || !is_array($manifest['questions'])) {
        stack_private_demo_error('Question manifest is invalid.', 500);
    }

    return $manifest;
}

/**
 * Return the public catalogue exposed to the browser.
 *
 * @return array Catalogue.
 */
function stack_private_demo_catalogue() {
    $questions = array_values(stack_private_demo_manifest()['questions']);
    usort($questions, function ($left, $right) {
        return [$left['category'], $left['name']] <=> [$right['category'], $right['name']];
    });

    return array_map(function ($question) {
        return [
            'questionId' => $question['id'],
            'name' => $question['name'],
            'filename' => $question['filename'],
            'category' => $question['category'],
        ];
    }, $questions);
}

/**
 * Decode a JSON request body.
 *
 * @param object $request Request.
 * @return array Request data.
 */
function stack_private_demo_request_json($request) {
    $data = json_decode((string) $request->getBody(), true);
    if (!is_array($data)) {
        stack_private_demo_error('Expected a JSON object.');
    }
    if (array_key_exists('questionDefinition', $data)) {
        stack_private_demo_error('questionDefinition is not accepted by this demo.');
    }

    return $data;
}

/**
 * Get exactly one question reference from request data.
 *
 * @param array $data Request data.
 * @return array Question reference.
 */
function stack_private_demo_question_reference($data) {
    $questionid = $data['questionId'] ?? null;
    $questionid = is_string($questionid) ? trim($questionid) : null;
    $questionid = $questionid === '' ? null : $questionid;

    $questionpath = $data['questionPath'] ?? null;
    $questionpath = is_string($questionpath) ? trim($questionpath) : null;
    $questionpath = $questionpath === '' ? null : $questionpath;

    if (($questionid === null && $questionpath === null) || ($questionid !== null && $questionpath !== null)) {
        stack_private_demo_error('Exactly one of questionId or questionPath is required.');
    }

    if ($questionid !== null) {
        return ['questionId' => $questionid];
    }
    return ['questionPath' => $questionpath];
}

/**
 * Resolve a question id to a local XML question definition.
 *
 * @param string $questionid Opaque question id.
 * @return string XML question definition.
 */
function stack_private_demo_question_definition($questionid) {
    $manifest = stack_private_demo_manifest();
    if (!isset($manifest['questions'][$questionid])) {
        stack_private_demo_error('Unknown question id.', 404);
    }

    return stack_private_demo_question_definition_from_path($manifest['questions'][$questionid]['path'], true);
}

/**
 * Resolve a question reference to a local XML question definition.
 *
 * @param array $reference Question reference.
 * @return string XML question definition.
 */
function stack_private_demo_question_definition_from_reference($reference) {
    return isset($reference['questionId']) ?
        stack_private_demo_question_definition($reference['questionId']) :
        stack_private_demo_question_definition_from_path($reference['questionPath']);
}

/**
 * Get the seed sequence requested for an embedded question.
 *
 * @param array $data Query parameters.
 * @param SimpleXMLElement $quiz Quiz XML.
 * @return int[]|null Seed sequence, or null if no seed control was requested.
 */
function stack_private_demo_embed_seed_sequence($data, $quiz) {
    if (empty($data['seeds']) || !is_string($data['seeds'])) {
        return null;
    }

    $seeds = trim($data['seeds']);
    if (strtolower($seeds) === 'all') {
        return stack_private_demo_deployed_seeds_from_xml($quiz);
    }

    return array_map('intval', preg_split('/\s*,\s*/', $seeds, -1, PREG_SPLIT_NO_EMPTY));
}

/**
 * Read deployed seeds from question XML.
 *
 * @param SimpleXMLElement $quiz .
 * @return int[] Deployed seeds.
 */
function stack_private_demo_deployed_seeds_from_xml($quiz) {
    $seeds = [];
    foreach ($quiz->question[0]->deployedseed as $seed) {
        $seeds[] = (int) $seed;
    }
    return $seeds;
}

/**
 * Resolve a question-library-relative path to a local XML question definition.
 *
 * @param string $relativepath Relative path under the configured question library.
 * @param bool $manifestpath Whether the path came from the generated manifest.
 * @return string XML question definition.
 */
function stack_private_demo_question_definition_from_path($relativepath, $manifestpath = false) {
    if (
        !is_string($relativepath) ||
        $relativepath === '' ||
        str_starts_with($relativepath, '/') ||
        strtolower(pathinfo($relativepath, PATHINFO_EXTENSION)) !== 'xml'
    ) {
        stack_private_demo_error($manifestpath ? 'Invalid question path in manifest.' : 'Invalid question path.', 400);
    }

    $fullpath = stack_private_demo_resolve_file(STACK_PRIVATE_DEMO_LIBRARY_ROOT, $relativepath);
    if ($fullpath === false) {
        stack_private_demo_error('Question file is not available.', $manifestpath ? 500 : 404);
    }

    $xml = file_get_contents($fullpath);
    if ($xml === false) {
        stack_private_demo_error('Question file could not be read.', 500);
    }

    return $xml;
}

/**
 * Build an API request from a browser request.
 *
 * @param array $data Browser request.
 * @param string $route API route.
 * @return array API request.
 */
function stack_private_demo_api_payload($data, $route) {
    $reference = stack_private_demo_question_reference($data);
    $payload = $data;
    unset($payload['questionId']);
    unset($payload['questionPath']);
    $payload['questionDefinition'] = stack_private_demo_question_definition_from_reference($reference);

    if ($route === 'render') {
        unset($payload['answers']);
    }

    return $payload;
}

/**
 * Proxy a private STACK API container response.
 *
 * @param object $response Response.
 * @param string $url API URL.
 * @param array $options HTTP stream options.
 * @param string $defaulttype Default content type.
 * @param string $errormessage Message to show if the request fails.
 * @param bool $forwardlength Whether to forward the content length header.
 * @return object Response.
 */
function stack_private_demo_proxy_api($response, $url, $options, $defaulttype, $errormessage, $forwardlength = false) {
    $context = stream_context_create(['http' => $options + [
        'ignore_errors' => true,
        'timeout' => 60,
    ]]);
    $body = file_get_contents($url, false, $context);
    if ($body === false) {
        stack_private_demo_error($errormessage, 502);
    }

    $status = 200;
    $contenttype = $defaulttype;
    $length = '';
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+([0-9]{3})\s/', $header, $matches)) {
            $status = (int) $matches[1];
            continue;
        }
        if (stripos($header, 'Content-Type:') === 0) {
            $contenttype = trim(substr($header, strlen('Content-Type:')));
            continue;
        }
        if (stripos($header, 'Content-Length:') === 0) {
            $length = trim(substr($header, strlen('Content-Length:')));
        }
    }

    $response = $response
        ->withStatus($status)
        ->withHeader('Content-Type', $contenttype);

    if ($forwardlength && $length !== '') {
        $response = $response->withHeader('Content-Length', $length);
    }

    $response->getBody()->write($body);
    return $response;
}

/**
 * Proxy a JSON POST to the private STACK API container.
 *
 * @param object $request Request.
 * @param object $response Response.
 * @param string $route API route.
 * @param array $payload API payload.
 * @return object Response.
 */
function stack_private_demo_proxy_json($request, $response, $route, $payload) {
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        stack_private_demo_error('Could not encode API request.', 500);
    }

    $headers = ['Content-Type: application/json'];
    $language = $request->getHeaderLine('Accept-Language');
    if ($language !== '') {
        $headers[] = 'Accept-Language: ' . $language;
    }

    return stack_private_demo_proxy_api(
        $response,
        STACK_PRIVATE_DEMO_API_BASE . '/' . $route,
        [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $json,
        ],
        'application/json',
        'Private API request failed.'
    );
}

/**
 * Proxy a generated plot/static question asset from the private API container.
 *
 * @param object $response Response.
 * @param string $filename Asset filename.
 * @return object Response.
 */
function stack_private_demo_proxy_plot($response, $filename) {
    $filename = basename(urldecode($filename));
    if ($filename === '' || $filename === '.' || $filename === '..') {
        stack_private_demo_error('Invalid asset name.');
    }

    return stack_private_demo_proxy_api(
        $response,
        STACK_PRIVATE_DEMO_API_BASE . '/plot.php/' . rawurlencode($filename),
        ['method' => 'GET'],
        'application/octet-stream',
        'Private API asset request failed.',
        true
    );
}

/**
 * Serve a static helper asset with CORS headers for iframe content.
 *
 * @param object $request Request.
 * @param object $response Response.
 * @param string $asset Relative asset path.
 * @return object Response.
 */
function stack_private_demo_cors_asset($request, $response, $asset) {
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', '*');

    if ($request->getMethod() === 'OPTIONS') {
        return $response
            ->withStatus(204)
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    $fullpath = stack_private_demo_resolve_file(STACK_PRIVATE_DEMO_CORS_ROOT, ltrim(urldecode($asset), '/'));
    if ($fullpath === false) {
        return stack_private_demo_text_response($response, 'No such script here.', 404);
    }

    $types = [
        'css' => 'text/css;charset=UTF-8',
        'js' => 'text/javascript;charset=UTF-8',
        'map' => 'application/json;charset=UTF-8',
        'json' => 'application/json;charset=UTF-8',
    ];

    $response->getBody()->write(file_get_contents($fullpath));
    return $response
        ->withHeader('Content-Type', $types[strtolower(pathinfo($fullpath, PATHINFO_EXTENSION))] ??
            'application/octet-stream')
        ->withHeader('Cache-Control', 'public, max-age=31104000, immutable');
}
