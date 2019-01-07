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

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/libs/tools.php');
require_once(__DIR__ . '/libs/export.php');
require_once(__DIR__ . '/libs/validate.php');

require_once(__DIR__ . '/api.php');
require_once(__DIR__ . '/libs/yaml_defaults.php');
require_once(__DIR__ . '/libs/yaml.php');

require_once(__DIR__ . '/../stack/questiontest.php');

function processrequest() {
    global $PAGE, $CFG;
    $then = microtime(true);

    $api = new qtype_stack_api();
    // Parse input JSON and validate it.
    $parsed = validatedata(parseinput());
    // Control the display of feedback, and whether students can change their answer.
    $options = new stdClass();
    $GLOBALS['OPTIONS'] =& $options;

    $options->readonly = $parsed['readOnly'];
    // Do we display feedback and a score for each part (in a multi-part question)?
    $options->feedback = $parsed['feedback'];
    $options->score = $parsed['score'];
    $options->validate = !$parsed['score'];
    $options->lang = $parsed['lang'];
    $options->debug = $parsed['debug'];

    $questionyaml = trim($parsed['question']);

    $defaults = new qtype_stack_api_yaml_defaults($parsed['defaults']);
    // If question data starts with "<" sign - export it to yaml.
    if ($questionyaml[0] === '<') {
        $export = new qtype_stack_api_export($questionyaml, $defaults);
        $questionyaml = $export->yaml();
    }

    // Import STACK question from yaml string.
    $importer = new qtype_stack_api_yaml($questionyaml, $defaults);
    $data = $importer->get_question($options->lang);
	
    $verifyvar = $parsed['verifyvar'];
	if ( $verifyvar ) { // make questions a simply as possible, we just want verify user answer
		$data["question_html"] = '<p>[[validation:' . $verifyvar . ']]</p>';
		$data["response_trees"] = new stdClass;
		// $data["variables"] = "";
		$data["specific_feedback_html"] = "";
		$data["note"] = "";
		$data["worked_solution_html"] = "";
	}
    $question = $api->initialise_question($data);
    // Make this a definite number, to fix the random numbers.
    $question->seed = $parsed['seed'];

	if ( 0 ) {  // for debug purposes
	   print("\n=====================================================\n");
	   printdata($options);
	   print("\n=====================================================\n");
	   printdata($data);
	   print("\n=====================================================\n");
	   printdata($question);
       return;
	}  // end debug
	
    $question->initialise_question_from_seed();

    $attempt = $parsed['answer'];
    $apithen = microtime(true);

    $res = $api->formulation_and_controls($question, $attempt, $options, $parsed['prefix']);
    // printdata($res);

    // Run question tests.
    // TODO: this is unfinished.  We need to refactor some of this, and from questiontestrun.php to eliminate any duplication in testing.
    // TODO: for now disable testing of the question.
    if (false && array_key_exists('tests', $data)) {
        $testresults = array();
        $seed = 0;

        $questiontests = $data['tests'];
        $test = $questiontests[1];

        $inputs = array();
        foreach ($data['inputs'] as $ikey => $val) {
            if (array_key_exists($ikey, $test)) {
                $inputs[$ikey] = $test[$ikey];
            }
        }
        $qtest = new stack_question_test($inputs);
        $testattempt = $qtest->compute_response($question, $inputs);
        $question->initialise_question_from_seed();
        // TODO: we need to dig inside here, and compare at the potential response tree level.
        $res = $api->formulation_and_controls($question, $testattempt, $options, $parsed['prefix']);
        // Questions don't have to have a response tree.  It could be a survey.
        if (array_key_exists('response_trees', $data)) {
            foreach ($data['response_trees'] as $prtname => $testvals) {
                if (array_key_exists($prtname, $test)) {
                    $qtest->add_expected_result($prtname, new stack_potentialresponse_tree_state(1,
                            true, $test[$prtname]['score'], $test[$prtname]['penalty'], '', array($test[$prtname]['answer_note'])));
                }
            }
        }
    }

	$json = [];
	if ( $verifyvar ) {
		$json = [
			"questiontext" => $res->questiontext
		];
	}
	else {
		// Assemble output.
		$ploturl = $parsed['ploturl'];
		$json = [
			"questiontext" => replace_plots($res->questiontext, $ploturl),
			"score" => $res->score,
			"generalfeedback" => replace_plots($res->generalfeedback, $ploturl),
			"formatcorrectresponse" => replace_plots($res->formatcorrectresponse, $ploturl),
			"summariseresponse" => json_decode($res->summariseresponse),
			"answernotes" => json_decode($res->answernotes)
		];
	}
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
