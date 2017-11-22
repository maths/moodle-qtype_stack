<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../config.php');

require_once(__DIR__ . '/libs/export.php');
require_once(__DIR__ . '/libs/yaml_defaults.php');
require_once(__DIR__ . '/libs/tools.php');
require_once(__DIR__ . '/libs/validate.php');

function validate_converter(array $data) {
    if (!array_key_exists('xml', $data)) {
        printError('No XML provided');
    }
}

function process_request() {
    $then = microtime(true);
    $input = parseinput();

    validate_converter($input);

    $xml = $input['xml'];

    $defaults = new qtype_stack_api_yaml_defaults($input['defaults'] ?? null);
    $export = new qtype_stack_api_export($xml, $defaults);
    $yamlstring = $export->yaml();
    $now = microtime(true);

    $response = array(
    'yaml' => $yamlstring,
    'request_time' => $now - $then
    );
    printdata($response);
}

try {
    process_request();
} catch (Exception $e) {
    printError('Exception '. $e->getMessage());
}