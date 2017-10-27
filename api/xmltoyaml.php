<?php

$then = microtime(true);
error_reporting('E_NONE');
ini_set('display_errors', 0);

require_once(__DIR__ . '/../config.php');

require_once(__DIR__ . '/libs/export.php');
require_once(__DIR__ . '/libs/yaml_defaults.php');
require_once(__DIR__ . '/libs/tools.php');
require_once(__DIR__ . '/libs/validate.php');

function validateConverter(array $data)
{
    if (!array_key_exists('xml', $data)) {
        printError('No XML provided');
    }
}

function process_request()
{
    $input = parseInput();

    validateConverter($input);

    $xml = $input['xml'];

    $defaults = new qtype_stack_api_yaml_defaults($input['defaults']);
    $export = new qtype_stack_api_export($xml, $defaults);
    $yaml_string = $export->YAML();
    $now = microtime(true);

    $response = array(
    'yaml' => $yaml_string,
    'request_time' => $now - $then
    );
    printData($response);
}

try {
    process_request();
}
catch(Exception $e) {
    printError('Exception '. $e->getMessage());
}
