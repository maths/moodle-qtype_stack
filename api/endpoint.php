<?php

error_reporting(E_NONE);
ini_set('display_errors', 0);

require_once(__DIR__ . '/libs/tools.php');
require_once(__DIR__ . '/libs/export.php');
require_once(__DIR__ . '/libs/validate.php');
require_once(__DIR__ . '/../config.php');

require_once(__DIR__ . '/api.php');
require_once(__DIR__ . '/libs/yaml_defaults.php');
require_once(__DIR__ . '/libs/yaml.php');

function processRequest() {
    $then = microtime(true);

    $api = new qtype_stack_api();
    $parsed = validateData(parseInput());

    $question_yaml = trim($parsed['question']);
    $defaults = new qtype_stack_api_yaml_defaults($parsed['defaults']);
    if ($question_yaml[0] === '<') {
        $export = new qtype_stack_api_export($question_yaml, $defaults);
        $question_yaml = $export->YAML();
    }

    $importer = new qtype_stack_api_yaml($question_yaml, $defaults);
    $data = $importer->get_question();
    $question = $api->initialise_question($data);
    // Make this a definite number, to fix the random numbers.
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
    $api_then = microtime(true);

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
    $json['request_time'] = $now-$then;
    $json['api_time'] = $now-$api_then;

    printData($json);
}

try {
    processRequest();
}
catch(Exception $e) {

    printError('Exception '. $e->getMessage());
}