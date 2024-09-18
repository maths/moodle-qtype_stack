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
require_once($CFG->dirroot . '/question/type/stack/api/util/StackQuestionLoader.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionlibrary.class.php');

use context;
use cache;
use stack_question_library;
use api\util\StackQuestionLoader;

/**
 * External API for AJAX calls.
 */
class library_render extends \external_api {
    /**
     * Returns parameter types for library_render function.
     *
     * @return \external_function_parameters Parameters
     */
    public static function library_render_parameters() {
        return new \external_function_parameters([
            'context' => new \external_value(PARAM_INT, 'Context where user has edit access'),
            'filepath' => new \external_value(PARAM_RAW, 'File path relative to samplequestions/stacklibrary'),
        ]);
    }

    /**
     * Returns result type for library_render function.
     *
     * @return \external_description Result type
     */
    public static function library_render_returns() {
        return new \external_single_structure([
            'questionrender' => new \external_value(PARAM_RAW, 'HTML render of question text'),
            'questiontext' => new \external_value(PARAM_RAW, 'Original question text'),
            'questionvariables' => new \external_value(PARAM_RAW, 'Question variable definitions'),
        ]);
    }

    /**
     * Returns info from STACK library question for display.
     *
     * @param int $context
     * @param string $filepath Input name
     * @param mixed $input Input value
     * @return array Array of question render, question text and question variables.
     */
    public static function library_render($context, $filepath) {
        global $CFG;

        $thiscontext = context::instance_by_id($context);
        self::validate_context($thiscontext);
        require_capability('moodle/question:add', $thiscontext);
        $cache = cache::make('qtype_stack', 'librarycache');
        $result = $cache->get($filepath);
        if (!$result) {
            $qcontents = file_get_contents($CFG->dirroot . '/question/type/stack/samplequestions/stacklibrary/' . $filepath);
            $question = StackQuestionLoader::loadxml($qcontents)['question'];
            $render =  stack_question_library::render_question($question);
            $result = [
                'questionrender'   => $render,
                'questiontext'  => $question->questiontext,
                'questionvariables' => $question->questionvariables,
            ];
            $cache->set($filepath, $result);
        }
        return $result;
    }
}
