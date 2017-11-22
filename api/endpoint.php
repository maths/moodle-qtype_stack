<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/libs/tools.php');
require_once(__DIR__ . '/libs/export.php');
require_once(__DIR__ . '/libs/validate.php');
require_once(__DIR__ . '/../config.php');

require_once(__DIR__ . '/api.php');
require_once(__DIR__ . '/libs/yaml_defaults.php');
require_once(__DIR__ . '/libs/yaml.php');

function processrequest() {
    $then = microtime(true);

    $api = new qtype_stack_api();
    $parsed = validatedata(parseinput());

    $questionyaml = trim($parsed['question']);

    //$question = $api->initialise_question_from_xml($questionyaml);

    $defaults = new qtype_stack_api_yaml_defaults($parsed['defaults']);
    if ($questionyaml[0] === '<') {
        $export = new qtype_stack_api_export($questionyaml, $defaults);
        $questionyaml = $export->yaml();
    }

    $importer = new qtype_stack_api_yaml($questionyaml, $defaults);
    $data = $importer->get_question();
    $question = $api->initialise_question($data);
    // Make this a definite number, to fix the random numbers.
    //print_r($question);
    $question->seed = $parsed['seed'];

    $question->initialise_question_from_seed();

    // Control the display of feedback, and whether students can change their answer.
    $options = new stdClass();
    $options->readonly = $parsed['readOnly'];
    // Do we display feedback and a score for each part (in a multi-part question)?
    $options->feedback = $parsed['feedback'];
    $options->score = $parsed['score'];
    $options->validate = !$parsed['score'];

    $attempt = $parsed['answer'];
    $apithen = microtime(true);

    $res = $api->formulation_and_controls($question, $attempt, $options, $parsed['prefix']);

    $json = [
        "questiontext" => replace_plots($res->questiontext),
        "score" => $res->score,
        "generalfeedback" => replace_plots($res->generalfeedback),
        "formatcorrectresponse" => replace_plots($res->formatcorrectresponse),
        "summariseresponse" => json_decode($res->summariseresponse),
        "answernotes" => json_decode($res->answernotes)
    ];
    $now = microtime(true);
    $json['request_time'] = $now - $then;
    $json['api_time'] = $now - $apithen;

    printdata($json);
}

try {
    processrequest();
} catch (Exception $e) {
    printError('Exception '. $e->getMessage());
}