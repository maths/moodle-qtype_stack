<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External API for AJAX calls to get question info from a library file.
 *
 * @package qtype_stack
 * @copyright 2024 University of Edinburgh
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/type/stack/api/util/StackIframeHolder.php');
require_once($CFG->dirroot . '/question/type/stack/api/util/StackQuestionLoader.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionlibrary.class.php');

use context;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use cache;
use SimpleXMLElement;
use stack_question_library;
use api\util\StackQuestionLoader;
use api\util\StackIframeHolder;

/**
 * External API for AJAX calls.
 */
class library_render extends \external_api {
    /**
     * Returns parameter types for library_render webservice.
     *
     * @return \external_function_parameters Parameters
     */
    public static function render_execute_parameters() {
        return new \external_function_parameters([
            'category' => new \external_value(PARAM_INT, 'Question category where user has edit access'),
            'filepath' => new \external_value(PARAM_RAW, 'File path relative to samplequestions'),
        ]);
    }

    /**
     * Returns result type for library_render webservice.
     *
     * @return \external_single_structure
     */
    public static function render_execute_returns() {
        return new \external_single_structure([
            'questionrender' => new \external_value(PARAM_RAW, 'HTML render of question text'),
            'iframes' => new external_multiple_structure(
                new external_single_structure([
                    'iframeid' => new \external_value(PARAM_RAW, 'Iframe details'),
                    'content' => new \external_value(PARAM_RAW, 'Iframe details'),
                    'targetdivid' => new \external_value(PARAM_RAW, 'Iframe details'),
                    'title' => new \external_value(PARAM_RAW, 'Iframe details'),
                    'scrolling' => new \external_value(PARAM_BOOL, 'Iframe details'),
                    'evil' => new \external_value(PARAM_BOOL, 'Iframe details'),
                ])),
            'questionname' => new \external_value(PARAM_RAW, 'Question name'),
            'questiontext' => new \external_value(PARAM_RAW, 'Original question text'),
            'questionvariables' => new \external_value(PARAM_RAW, 'Question variable definitions'),
            'questiondescription' => new \external_value(PARAM_RAW, 'Question description'),
            'isstack' => new \external_value(PARAM_BOOL, 'Is this a STACK question?'),
        ]);
    }

    /**
     * Returns info from STACK library question for display.
     *
     * @param int $category Question category id for eventual import. We really only
     * care that the user can add into any category at all at this stage.
     * @param string $filepath File path relative to samplequestions.
     * @return array Array of question render, question text, description and question variables.
     */
    public static function render_execute($category, $filepath) {
        global $CFG, $DB;
        StackIframeHolder::$islibrary = true;
        // Check parameters and that user has question add capability in the supplied category.
        $context = $DB->get_field('question_categories', 'contextid', ['id' => $category]);
        $thiscontext = context::instance_by_id($context);
        self::validate_context($thiscontext);
        require_capability('moodle/question:add', $thiscontext);

        // Check if we've already cached the answer.
        $cache = cache::make('qtype_stack', 'librarycache');
        $result = $cache->get($filepath);
        if (!$result) {
            // Get contents of file and run through API question loader to render.
            $qcontents = file_get_contents($CFG->dirroot . '/question/type/stack/samplequestions/' . $filepath);
            try {
                $question = StackQuestionLoader::loadxml($qcontents)['question'];
                $render = static::call_question_render($question);
                $iframes = [];
                foreach (StackIframeHolder::$iframes as $iframe) {
                    $iframes[] = [
                        'iframeid' => $iframe['0'],
                        'content' => $iframe['1'],
                        'targetdivid' => $iframe['2'],
                        'title' => $iframe['3'],
                        'scrolling' => $iframe['4'],
                        'evil' => $iframe['5'],
                    ];
                }
                $result = [
                    'questionrender' => $render,
                    'iframes' => $iframes,
                    'questionname' => $question->name,
                    'questiontext' => $question->questiontext,
                    'questionvariables' => $question->questionvariables,
                    'questiondescription' => $question->questiondescription,
                    'isstack' => true,
                ];
                $cache->set($filepath, $result);
            } catch (\stack_exception $e) {
                // If the question is not a STACK question we can't render it
                // but we still want users to be able to import it.
                if (strpos($e->getMessage(), 'not of type STACK') !== false) {
                    $xmldata = new SimpleXMLElement($qcontents);
                    $questiontext = (string) $xmldata->question->questiontext->text;
                    $questionname = (string) $xmldata->question->name->text;
                    $result = [
                        'questionrender' => '<div class="formulation">' .
                            get_string('stack_library_not_stack', 'qtype_stack') .
                            '<br><br>' . $questiontext . '</div>',
                        'iframes' => [],
                        'questionname' => $questionname,
                        'questiontext' => $questiontext,
                        'questionvariables' => '',
                        'questiondescription' => '',
                        'isstack' => false,
                    ];
                } else {
                    throw $e;
                }
            }
        }
        return $result;
    }

    /**
     * Separate out to mock in unit testing.
     *
     * @param object $question XML of question
     * @return string HTML render of question
     */
    public static function call_question_render($question) {
        return stack_question_library::render_question($question);
    }
}
