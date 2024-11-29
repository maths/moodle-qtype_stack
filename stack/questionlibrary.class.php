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
 * @package   qtype_stack
 * @copyright 2024 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
 defined('MOODLE_INTERNAL') || die();

 require_once(__DIR__ . '../../api/util/StackSeedHelper.php');
 require_once(__DIR__ . '../../api/util/StackPlotReplacer.php');

 use api\util\StackSeedHelper;
 use api\util\StackPlotReplacer;
/**
 * Functions required to display the STACK question library
 * @package   qtype_stack
 */
class stack_question_library {
    /** @var int increments unique folder ids */
    public static $dircount = 1;

    /**
     * Summary of render_question
     * @param object Moodle XML of question
     * @throws \stack_exception
     * @return string HTML render of question text
     */
    public static function render_question(object $question): string {
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
            $question->questiontextinstantiated->apply_placeholder_holder(
                \stack_maths::process_display_castext(
                    $question->questiontextinstantiated->get_rendered(
                        $question->castextprocessor
                    )
                )
            ),
            $language
        );

        StackPlotReplacer::replace_plots($plots, $questiontext, 'render', $storeprefix);
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $formatoptions->allowid = true;
        $questiontext = format_text($questiontext, FORMAT_HTML, $formatoptions);

        foreach ($question->inputs as $name => $input) {
            $tavalue = $question->get_ta_for_input($name);
            $fieldname = 'stack_temp_' . $name;
            $state = $question->get_input_state($name, []);
            $render = $input->render($state, $fieldname, false, [$tavalue]);
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

    /**
     * Gets the structure of folders and files within a given directory
     * See questionfolder.mustache for output and usage.
     * @param string directory within samplequestions to be examined
     * @return object StdClass Representation of the file system
     */
    public static function get_file_list(string $dir): object {
        $files = glob($dir);
        $results = new stdClass();
        $labels = explode('/', $dir);
        $results->label = $labels[count($labels) - 2];
        $results->divid = 'stack-library-folder-' . self::$dircount;
        self::$dircount++;
        $results->children = [];
        $results->isdirectory = 1;
        foreach ($files as $path) {
            if (!is_dir($path)) {
                if (!strpos($path, 'gitsync_category.xml')) {
                    $childless = new StdClass();
                    // Get the path relative to the samplequestions folder.
                    $pathfromsq = str_replace('samplequestions/', '', $path);
                    $pathfromsq = str_replace('../', '', $pathfromsq);
                    $childless->path = $pathfromsq;
                    $labels = explode('/', $path);
                    $childless->label = end($labels);
                    $childless->isdirectory = 0;
                    $results->children[] = $childless;
                }
            } else {
                $results->children[] = self::get_file_list($path . '/*');
            }
        }

        return $results;
    }
}
