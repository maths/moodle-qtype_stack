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

/**
 * Allows users to view and import questions from a library of samples.
 *
 * @copyright 2024 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

 require_once(__DIR__ . '../../api/util/StackSeedHelper.php');
 require_once(__DIR__ . '../../api/util/StackPlotReplacer.php');

 use api\util\StackSeedHelper;
 use api\util\StackPlotReplacer;
/**
 *
 */
class stack_question_library {

    public static function render_question($question) {
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

        StackPlotReplacer::replace_plots($plots, $questiontext, 'render', $storeprefix);
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $questiontext = format_text($questiontext, HTML_FORMAT, $formatoptions);

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

        return '<div class="formulation">' . $questiontext . '</div>';
    }
    public static function get_file_list($dir, &$results = array()) {
        $files = glob($dir);

        foreach ($files as $path) {
            if (!is_dir($path)) {
                if (!strpos($path, 'gitsync_category.xml')) {
                    $results[] = str_replace('samplequestions/stacklibrary/', '', $path);
                }
            } else {
                self::get_file_list($path . '/*', $results);
            }
        }

        return $results;
    }
}
