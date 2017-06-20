<?php
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
// Do we display feedback and a score for each part (in a multi-part question)?
$options->feedback = true;
$options->score = true;

$attempt = $_POST;
$result = $api->formulation_and_controls($question, $attempt, $options, '');

//echo "<pre>";
//print_r($result);
//echo "</pre>";

echo "<html>\n<head>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_CHTML'></script>";
echo "</head>\n";
echo "<body>\n";
echo "\n\n<form action=\"minimal.php\" method=\"post\">\n";
echo $result->questiontext;
echo "\n\n<br/>\n<input type=\"submit\" value=\"Check\">\n";
$score = $result->score * $result->defaultmark;
echo "<p>Your mark for this attempt is ".$score.".</p>";
echo "\n</form>\n\n";

echo "<p><hr /></p>\n\n";
echo "<h2>Worked solution:</h2>\n";
echo $result->generalfeedback;

echo "<p><hr /></p>\n\n";
echo "<h2>Correct answers:</h2>\n";
echo $result->formatcorrectresponse;

echo "<p><hr /></p>\n\n";
echo "<h2>Attempt summary information (for stats purposes)</h2>\n";
echo "<h3>Inputs:</h3>\n";
echo "<pre>" . $result->summariseresponse . "</pre>\n\n";
echo "<h3>Response trees:</h3>\n";
echo "<pre>" . $result->answernotes; "</pre>\n\n";

echo "</body>\n</html>";

