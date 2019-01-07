<head>
      <meta charset="utf-8"/>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/codemirror.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/lint.min.css" />
      <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
          tex2jax: {
            inlineMath: [['$', '$'], ['\\[','\\]'], ['\\(','\\)']],
            displayMath: [['$$', '$$']],
            processEscapes: true,
            skipTags: ["script","noscript","style","textarea","pre","code","button"]
          },
          showMathMenu: false
        })
      </script>
      <script
        type="text/javascript"
        src="//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS_HTML"
      >
      </script>
    </head>
<body>
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

/*
 * The purpose of this file is to provide minimal details of how to use
 * the STACK API.
 * 
 * @copyright  2018 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

require_once('../config.php');
$fields = array();

/* The API expects questions to be in YAML format.
 * Note, you can export questions from an existing Moodle install from the
 * individual question testing page.
 */
$filename = $CFG->wwwroot . '/samplequestions/test_1_integration.yaml';
$fields['question'] = file_get_contents($filename);

/*
 * The prefix is used to name html form elements, to enable larger systems
 * to distinguish these within a page.  Use any reasonable string.
 */
$prefix = 'stackapi_';
$fields['prefix'] = $prefix;

/*
 * The "answer" array contains the raw responses from a student's previous attempt.
 */
$answers = array(
        $prefix . 'ans1' => '(x-6)^4/4'
        );
$fields['answer'] = $answers;

/*
 * The seed for the random number generator.
 * It should be one of the deployed seeds, so the question has already been tested.
 */
$fields['seed'] = 165520961;

/* 
 * Make HTML form elements "read only", to prevent further attempts.
*/
$fields['readOnly'] = 0;

/*
 * Display feedback to the student.
 */
$fields['feedback'] = 1;

/*
 * Defaults provide options for the question type.
 * TODO: include them here.
 */

/*
 * Display a score to the student (a score is always returned by the API), but sometimes the score
 * is emedded within the question itself for individual parts.
 */ 
$fields['score'] = 1;

/*
 * Language to be used.
 * Blank assumes English (sorry).
*/
$fields['lang'] = 'en';

echo "<h1>Minimal input as follows</h1>\n\n";
echo "<pre>\n";
print_r($fields);
echo "</pre>\n\n";

/*
 * Send the request to the API.
 */
$url = $CFG->dataurl . 'api/endpoint.php';
$content = json_encode($fields, true);

echo "<h1>JSON sent:</h1>\n\n";
echo "<textarea cols='120' rows='30' readonly>\n";
print_r($content);
echo "</textarea>\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec($ch);

$parsed = json_decode($result, true);
echo "<h1>Result parsed as follows:</h1>\n\n";
echo "<textarea cols='120' rows='30' readonly>\n";
print_r($parsed);
echo "</textarea>\n\n";

echo "<h1>Returned values</h1>\n\n";
echo "<p>Note, there are other fields in the return value than those printed below.<p>\n\n";

echo "<h2><tt>questiontext</tt> which is the `question`</h2>\n\n";
echo $parsed['questiontext'];

echo "<hr/> \n <h2><tt>generalfeedback</tt> (worked solution)</h2>\n\n";
echo $parsed['generalfeedback'];

echo "<hr/> \n <h2><tt>formatcorrectresponse</tt> (teacher's answer)</h2>\n\n";
echo $parsed['formatcorrectresponse'];

echo "<hr/> \n <h2><tt>summariseresponse</tt></h2>\n\n";
echo "<pre>";
print_r($parsed['summariseresponse']);
echo "</pre>";

echo "<hr/> \n <h2><tt>answernotes</tt></h2>\n\n";
echo "<pre>";
print_r($parsed['answernotes']);
echo "</pre>";

?>
</body>
