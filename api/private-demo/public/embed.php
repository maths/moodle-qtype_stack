<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Embeddable single-question frame for the private demo.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(__DIR__ . '/../lib.php');

$questionid = isset($_GET['questionId']) ? trim($_GET['questionId']) : '';
$questionpath = isset($_GET['questionPath']) ? trim($_GET['questionPath']) : '';

if (($questionid === '' && $questionpath === '') || ($questionid !== '' && $questionpath !== '')) {
    http_response_code(400);
    echo 'Exactly one of questionId or questionPath is required.';
    die();
}
if ($questionid !== '') {
    // Fail early for malformed embed URLs. The API request will still perform
    // the authoritative manifest lookup.
    $xml = stack_private_demo_question_definition($questionid);
}
if ($questionpath !== '') {
    $xml = stack_private_demo_question_definition_from_path($questionpath);
}

$title = 'Practice question';
libxml_use_internal_errors(true);
$quiz = simplexml_load_string($xml);
libxml_clear_errors();
if ($quiz !== false && isset($quiz->question[0]->name->text)) {
    $questionname = trim((string) $quiz->question[0]->name->text);
    if ($questionname !== '') {
        $title = $questionname;
    }
}

$question = [
    'name' => $title,
];
if ($questionid !== '') {
    $question['questionId'] = $questionid;
} else {
    $question['questionPath'] = $questionpath;
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title><?=htmlspecialchars($question['name'])?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/api.css" />
    <style>
      body {
        margin: 0;
        padding: 1rem;
        background: #fff;
      }
      .main-content {
        margin-left: 0;
        padding: 0;
      }
      #stackapi_spinner {
        display: none;
      }
    </style>
    <script
      type="text/javascript"
      src="//cdn.jsdelivr.net/npm/mathjax@3.2.2/es5/tex-mml-chtml.js?config=TeX-AMS-MML_HTMLorMML"
    ></script>
    <script src="assets/stackjsvle.js"></script>
    <script>
      window.stackApiDemoConfig = {
        serverUrl: '/demo/',
        displayType: 'SAMPLE'
      };
      window.stackEmbeddedQuestion = <?=json_encode($question, JSON_UNESCAPED_SLASHES)?>;
    </script>
    <script src="assets/stackshared.js"></script>
    <script src="embed-question.js"></script>
  </head>
  <body>
    <div class="main-content que stack">
      <h2 id="stackapi_name"></h2>
      <div id="errors"></div>
      <div id="stackapi_qtext" style="display: none">
        <div id="output" class="formulation"></div>
        <br>
        <input type="button" onclick="answer()" class="btn btn-primary noninfo" value="Submit Answers"/>
        <input type="button" onclick="toggleAnswer(this)" class="btn btn-primary noninfo" value="Display Correct Answers"/>
        <input id="stackapi_variant" type="button" onclick="advanceVariant()" class="btn btn-primary" value="Next Variant"/>
        <span id="stackapi_spinner" class="spinner-border text-primary align-middle" role="status" style="margin-left: 10px;">
          <span class="sr-only">Loading...</span>
        </span>
        <div id="stackapi_validity" style="color:darkred"></div>
      </div>
      <br>
      <div id="stackapi_combinedfeedback" class="feedback outcome" style="display: none">
        <div id="specificfeedback"></div>
        <div id="generalfeedback"></div>
      </div>
      <div id="stackapi_correct" style="display: none">
        <div class="noninfo">
          <h3>Correct answers:</h3>
          <div id="formatcorrectresponse" class="feedback outcome"></div>
        </div>
      </div>
    </div>
  </body>
</html>
