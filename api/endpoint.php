<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require_once(__DIR__ . '/config.php');

require_once(__DIR__ . '/api/api.php');
require_once(__DIR__ . '/question.php');

$api = new qtype_stack_api();

$request = $_POST

switch($request['type']) {
  case 'question_html':
    $response = get_question($request);
    break;
  case 'validate':
    $response = validate($request);
    break;
  default:
    $response = 'Request is invalid';
}

function get_question($request) {
  $questionxml = $request['question'];
  $question = $api->initialise_question_from_xml($questionxml);
  $options = $request['options'];
  question->seed = 10384;

  $question->initialise_question_from_seed();

  // Control the display of feedback, and whether students can change their answer.
  $options = new stdClass();
  $options->readonly = $options['readonly'];
  $options->feedback = $options['feedback'];
  $options->score = $options['score'];
  // Show a worked solution?
  $options->generalfeedback = $options['generalfeedback'];

  return $api->formulation_and_controls($question, null, $options, '');
}

function validate($request) {
  $questionxml = $request['question'];
  $question = $api->initialise_question_from_xml($questionxml);
  $response = $request['response'];
  $options = $request['options'];
  question->seed = 10384;

  $question->initialise_question_from_seed();

  // Control the display of feedback, and whether students can change their answer.
  $options = new stdClass();
  $options->readonly = $options['readonly'];
  $options->feedback = $options['feedback'];
  $options->score = $options['score'];
  // Show a worked solution?
  $options->generalfeedback = $options['generalfeedback'];

  return $api->formulation_and_controls($question, $response, $options, '');
}

