<?php
// https://github.com/ilifau/assStackQuestion
// https://github.com/ilifau/assStackQuestion/blob/master-ilias52/classes/utils/class.assStackQuestionInitialization.php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

define('MOODLE_INTERNAL', true);

require_once(__DIR__ . '../../../../config.php');
require_once(__DIR__ . '../../../../question/type/questionbase.php');
require_once(__DIR__ . '../../../../question/behaviour/behaviourbase.php');

//require_once(__DIR__ . '/stack/input/factory.class.php');
//require_once(__DIR__ . '/stack/cas/keyval.class.php');
//require_once(__DIR__ . '/stack/cas/castext.class.php');
require_once(__DIR__ . '/question.php');
require_once(__DIR__ . '/api.php');

$api = new qtype_stack_api();

//$questionxml = file_get_contents('samplequestions/odd-even.xml');
$questionxml = file_get_contents('samplequestions/test_3_matrix.xml');
//$questionxml = file_get_contents('samplequestions/test_1_basic_integral.xml');

$question = $api->initialise_question_from_xml($questionxml);
$question->initialise_question_from_seed();
//print_r($question);


$options = new stdClass();
$options->readonly = false;
$options->feedback = true;

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

