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

require_once(__DIR__ . '/../config.php');

$then = microtime(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    // Parse JSON input.
    $input = parseinput();

    // Validate input data.
    validate_converter($input);

    $xml = $input['xml'];

    // Create defaults object.
    $defaults = new qtype_stack_api_yaml_defaults($input['defaults'] ?? null);
    $export = new qtype_stack_api_export($xml, $defaults);
    // Export question as yaml string.
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