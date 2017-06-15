<?php
// https://github.com/ilifau/assStackQuestion
// https://github.com/ilifau/assStackQuestion/blob/master-ilias52/classes/utils/class.assStackQuestionInitialization.php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require_once(__DIR__ . '/config.php');

require_once(__DIR__ . '/api/api.php');
require_once(__DIR__ . '/question.php');

$api = new qtype_stack_api();

// Run this command once at install time to compile Maxima on your machine.
//$api->install();

// Choose one of the XML files in the samplequestions directory.
$questionxml = file_get_contents('samplequestions/odd-even.xml');
//$questionxml = file_get_contents('samplequestions/test_3_matrix.xml');
//$questionxml = file_get_contents('samplequestions/test_1_basic_integral.xml');

$question = $api->initialise_question_from_xml($questionxml);
// Make this a definite number, to fix the random numbers.
$question->seed = 10384;
//print_r($question);

$question->initialise_question_from_seed();

// Control the display of feedback, and whether students can change their answer.
$options = new stdClass();
$options->readonly = false;
$options->feedback = true;
$options->score = true;
// Show a worked solution?
$options->generalfeedback = true;

$response = $_POST;
//print_r($response);

echo "<html>\n<head>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_CHTML'></script>";
echo "</head>\n";
echo "<body>\n";
echo "\n\n<form action=\"minimal.php\" method=\"post\">\n";
echo $api->formulation_and_controls($question, $response, $options, '');
echo "\n\n<br/>\n<input type=\"submit\" value=\"Check\">\n";
echo "\n</form>\n\n";
echo "</body>\n</html>";

