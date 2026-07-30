<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Shared helpers for the private STACK API demo frontend.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

define('STACK_PRIVATE_DEMO_ROOT', realpath(__DIR__ . '/../..'));
define('STACK_PRIVATE_DEMO_SAMPLEQUESTIONS_ROOT', realpath(STACK_PRIVATE_DEMO_ROOT . '/samplequestions'));
define(
    'STACK_PRIVATE_DEMO_LIBRARY_ROOT',
    realpath(STACK_PRIVATE_DEMO_ROOT . '/samplequestions/' . trim(getenv('STACK_PRIVATE_DEMO_LIBRARY') ?: 'stacklibrary', '/'))
);
define('STACK_PRIVATE_DEMO_CORS_ROOT', realpath(STACK_PRIVATE_DEMO_ROOT . '/corsscripts'));
define('STACK_PRIVATE_DEMO_MANIFEST', __DIR__ . '/assets/question-manifest.json');
define('STACK_PRIVATE_DEMO_API_BASE', rtrim(getenv('STACK_PRIVATE_DEMO_API_URL') ?: 'http://api', '/'));

/**
 * Send a JSON response and stop execution.
 *
 * @param mixed $data Response data.
 * @param int $status HTTP status.
 */
function stack_private_demo_json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json;charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    die();
}

/**
 * Send an error JSON response and stop execution.
 *
 * @param string $message Error message.
 * @param int $status HTTP status.
 */
function stack_private_demo_error($message, $status = 400) {
    stack_private_demo_json(['message' => $message], $status);
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
    usort($questions, function($left, $right) {
        return [$left['category'], $left['name']] <=> [$right['category'], $right['name']];
    });

    return array_map(function($question) {
        return [
            'questionId' => $question['id'],
            'name' => $question['name'],
            'filename' => $question['filename'],
            'category' => $question['category'],
        ];
    }, $questions);
}

/**
 * Decode the current JSON request body.
 *
 * @return array Request data.
 */
function stack_private_demo_request_json() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        stack_private_demo_error('Expected a JSON object.');
    }

    if (array_key_exists('questionDefinition', $data)) {
        stack_private_demo_error('questionDefinition is not accepted by this demo.');
    }

    return $data;
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
        str_contains($relativepath, '..') ||
        strtolower(pathinfo($relativepath, PATHINFO_EXTENSION)) !== 'xml'
    ) {
        stack_private_demo_error($manifestpath ? 'Invalid question path in manifest.' : 'Invalid question path.', 400);
    }

    $fullpath = realpath(STACK_PRIVATE_DEMO_LIBRARY_ROOT . '/' . $relativepath);
    if ($fullpath === false || !str_starts_with($fullpath, STACK_PRIVATE_DEMO_LIBRARY_ROOT . DIRECTORY_SEPARATOR)) {
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
    $questionid = $data['questionId'] ?? null;
    $questionpath = $data['questionPath'] ?? null;
    if (($questionid === null && $questionpath === null) || ($questionid !== null && $questionpath !== null)) {
        stack_private_demo_error('Exactly one of questionId or questionPath is required.');
    }

    $payload = $data;
    unset($payload['questionId']);
    unset($payload['questionPath']);
    $payload['questionDefinition'] = $questionid !== null ?
        stack_private_demo_question_definition($questionid) :
        stack_private_demo_question_definition_from_path($questionpath);

    if ($route === 'render') {
        unset($payload['answers']);
    }

    return $payload;
}

/**
 * Proxy a JSON POST to the private STACK API container.
 *
 * @param string $route API route.
 * @param array $payload API payload.
 */
function stack_private_demo_proxy_json($route, $payload) {
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        stack_private_demo_error('Could not encode API request.', 500);
    }

    $headers = [
        'Content-Type: application/json',
    ];
    $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($language !== '') {
        $headers[] = 'Accept-Language: ' . $language;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $json,
            'ignore_errors' => true,
            'timeout' => 60,
        ],
    ]);

    $url = STACK_PRIVATE_DEMO_API_BASE . '/' . $route;
    $body = file_get_contents($url, false, $context);
    if ($body === false) {
        stack_private_demo_error('Private API request failed.', 502);
    }

    $status = stack_private_demo_response_status($http_response_header ?? []);
    http_response_code($status);
    header('Content-Type: ' . stack_private_demo_header_value($http_response_header ?? [], 'Content-Type', 'application/json'));
    echo $body;
    die();
}

/**
 * Proxy a generated plot/static question asset from the private API container.
 *
 * @param string $filename Asset filename.
 */
function stack_private_demo_proxy_plot($filename) {
    $filename = basename(urldecode($filename));
    if ($filename === '' || $filename === '.' || $filename === '..') {
        stack_private_demo_error('Invalid asset name.');
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 60,
        ],
    ]);

    $url = STACK_PRIVATE_DEMO_API_BASE . '/plot.php/' . rawurlencode($filename);
    $body = file_get_contents($url, false, $context);
    if ($body === false) {
        stack_private_demo_error('Private API asset request failed.', 502);
    }

    $headers = $http_response_header ?? [];
    http_response_code(stack_private_demo_response_status($headers));
    header('Content-Type: ' . stack_private_demo_header_value($headers, 'Content-Type', 'application/octet-stream'));
    $length = stack_private_demo_header_value($headers, 'Content-Length', '');
    if ($length !== '') {
        header('Content-Length: ' . $length);
    }
    echo $body;
    die();
}

/**
 * Serve a static helper asset with CORS headers for iframe content.
 *
 * @param string $asset Relative asset path.
 */
function stack_private_demo_cors_asset($asset) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('HTTP/1.0 204 No Content');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        die();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        stack_private_demo_error('Method not allowed.', 405);
    }

    $asset = ltrim(urldecode($asset), '/');
    if ($asset === '' || str_contains($asset, '..') || str_starts_with($asset, '/')) {
        http_response_code(404);
        echo 'No such script here.';
        die();
    }

    $fullpath = realpath(STACK_PRIVATE_DEMO_CORS_ROOT . '/' . $asset);
    if (
        $fullpath === false ||
        !str_starts_with($fullpath, STACK_PRIVATE_DEMO_CORS_ROOT . DIRECTORY_SEPARATOR) ||
        !is_file($fullpath)
    ) {
        http_response_code(404);
        echo 'No such script here.';
        die();
    }

    header('Content-Type: ' . stack_private_demo_mimetype($fullpath));
    header('Cache-Control: public, max-age=31104000, immutable');
    readfile($fullpath);
    die();
}

/**
 * Return a MIME type for a public static helper asset.
 *
 * @param string $path Asset path.
 * @return string MIME type.
 */
function stack_private_demo_mimetype($path) {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $types = [
        'css' => 'text/css;charset=UTF-8',
        'js' => 'text/javascript;charset=UTF-8',
        'map' => 'application/json;charset=UTF-8',
        'json' => 'application/json;charset=UTF-8',
    ];

    return $types[$extension] ?? 'application/octet-stream';
}

/**
 * Get the HTTP response status from wrapper response headers.
 *
 * @param array $headers Response headers.
 * @return int HTTP status.
 */
function stack_private_demo_response_status($headers) {
    if (!empty($headers[0]) && preg_match('/\s([0-9]{3})\s/', $headers[0], $matches)) {
        return (int) $matches[1];
    }
    return 200;
}

/**
 * Get a response header value from wrapper response headers.
 *
 * @param array $headers Response headers.
 * @param string $name Header name.
 * @param string $default Default value.
 * @return string Header value.
 */
function stack_private_demo_header_value($headers, $name, $default) {
    foreach ($headers as $header) {
        if (stripos($header, $name . ':') === 0) {
            return trim(substr($header, strlen($name) + 1));
        }
    }
    return $default;
}
