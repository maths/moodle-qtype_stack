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
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script lets the user import questions from sample questions
 *
 * @copyright  2024 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/api/util/StackQuestionLoader.php');
require_once(__DIR__ . '/api/util/StackSeedHelper.php');
require_once(__DIR__ . '/api/util/StackPlotReplacer.php');

use api\util\StackQuestionLoader;
use api\util\StackSeedHelper;
use api\util\StackPlotReplacer;


// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/questionlibrary.php', $urlparams);
$title = stack_string('stack_library', format_string($question->name));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
require_login();
// $q = file_get_contents('samplequestions/Algebra-Refresher/1-Combinations-of-arithmetic-operations/AlgMap-1-1.xml');
$q = file_get_contents('samplequestions/JSXGraph-behat.xml');
//$q = file_get_contents('samplequestions/plottest.xml');
$question = StackQuestionLoader::loadxml($q)['question'];
StackSeedHelper::initialize_seed($question, null);

// Handle Pluginfiles.
$storeprefix = uniqid();
StackPlotReplacer::persist_plugin_files($question, $storeprefix);

$question->initialise_question_from_seed();

$question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

if (!empty($question->runtimeerrors)) {
    // The question has not been instantiated successfully, at this level it is likely
    // a failure at compilation and that means invalid teacher code.
    throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
}

$translate = new \stack_multilang();
// This is a hack, that restores the filter regex to the exact one used in moodle.
// The modifications done by the stack team prevent the filter funcitonality from working correctly.
$translate->search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang")' .
                        '{2}\s*>.*?<\/span>)(\s*<span(\s+lang="[a-zA-Z0-9_-]+"' .
                        '|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';
$language = current_language();

$plots = [];

$questiontext = $translate->filter(
    \stack_maths::process_display_castext(
        $question->questiontextinstantiated->get_rendered(
            $question->castextprocessor
        )
    ),
    $language
);

StackPlotReplacer::replace_plots($plots, $renderresponse->questionrender, "render", $storeprefix);
$formatoptions = new stdClass();
$formatoptions->noclean = true;
$formatoptions->para = false;
$questiontext = format_text($questiontext, HTML_FORMAT, $formatoptions);
$inputs = [];
foreach ($question->inputs as $name => $input) {
    $tavalue = $question->get_ta_for_input($name);
    $fieldname = 'stack_temp_' . $name;
    $state = $question->get_input_state($name, []);
    $render = $input->render($state, $fieldname, true, [$tavalue]);
    StackPlotReplacer::replace_plots($plots, $render, "answer-".$name, $storeprefix);
    $questiontext = str_replace("[[input:{$name}]]",
        $render,
        $questiontext);
    $questiontext = str_replace("[[validation:{$name}]]",
        '',
        $questiontext);
}

echo '<div class="formulation">' . $questiontext . '</div>';

// Finish output.
echo $OUTPUT->footer();
