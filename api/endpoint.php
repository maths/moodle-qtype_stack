<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require_once(__DIR__ . '/../config.php');

require_once(__DIR__ . '/api.php');
require_once(__DIR__ . '/../question.php');

function printData($data) {
  header('Content-Type: application/json');
  echo json_encode($data);
}

function printSuccess($data) {
  printData([
    "error" => false,
    "message" => $data
  ]);
}

function printError($message) {
  $res = [
    "error" => true,
    "message" => $message,
  ];
  printData($res);
  die();
}

function validateData($data) {
  // TODO:
  if (!array_key_exists('question', $data)) {
    printError('No question');
  }
}

function parseInput() {
  $data = file_get_contents("php://input");
  $parsed = json_decode($data, true);
  if ($parsed === null) {
    printError('no valid json');
  }
  validateData($parsed);
  return $parsed;
}

$api = new qtype_stack_api();

$parsed = parseInput();

$questionxml = $parsed['question'];

$question = $api->initialise_question_from_xml($questionxml);
// Make this a definite number, to fix the random numbers.
$question->seed = $parsed['seed'];
//print_r($question);

$question->initialise_question_from_seed();

// Control the display of feedback, and whether students can change their answer.
$options = new stdClass();
$options->readonly = $parsed['readOnly'];
// Do we display feedback and a score for each part (in a multi-part question)?
$options->feedback = $parsed['feedback'];
$options->score = $parsed['score'];

$attempt = $parsed['answer'];

$result = $api->formulation_and_controls($question, $attempt, $options, '');

$json = [
  "questiontext" => $res->questiontext,
  "score" => $res->score,
  "generalfeedback" => $res->generalfeedback,
  "formatcorrectresponse" => $res->formatcorrectresponse,
  "summariseresponse" => $res->summariseresponse,
  "answernotes" => $res->answernotes
];

printData($json);