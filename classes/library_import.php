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
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionlibrary.class.php');

use context;
use moodle_exception;
use qformat_xml;
use core_question\local\bank\question_edit_contexts;

/**
 * External API for AJAX calls.
 */
class library_import extends \external_api {
    /**
     * Returns parameter types for library_render function.
     *
     * @return \external_function_parameters Parameters
     */
    public static function library_import_parameters() {
        return new \external_function_parameters([
            'category' => new \external_value(PARAM_INT, 'Question category where user has edit access'),
            'filepath' => new \external_value(PARAM_RAW, 'File path relative to samplequestions/stacklibrary'),
        ]);
    }

    /**
     * Returns result type for library_render function.
     *
     * @return \external_description Result type
     */
    public static function library_import_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Success'),
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
    public static function library_import($category, $filepath) {
        global $CFG, $DB;
        $thiscontext = null;
        $qformat = null;
        $thiscategory = $DB->get_record('question_categories', ['id' => $category]);
        $contextid = $thiscategory->contextid;
        $thiscontext = context::instance_by_id($contextid);
        self::validate_context($thiscontext);
        require_capability('moodle/question:add', $thiscontext);

        $qformat = new qformat_xml();
        $qformat->set_display_progress(false);

        $qformat->setCategory($thiscategory);
        $qformat->setCatfromfile(false);

        $qformat->setFilename($CFG->dirroot . '/question/type/stack/samplequestions/stacklibrary/' . $filepath);
        $qformat->setContextfromfile(false);
        $qformat->setStoponerror(true);
        $contexts = new question_edit_contexts($thiscontext);
        $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
        if (!$qformat->importpreprocess()) {
            throw new moodle_exception('importerror', 'qbank_gitsync', null, $filename);
        }

        if (!$qformat->importprocess()) {
            throw new moodle_exception('importerror', 'qbank_gitsync', null, $filename);
        }
        // In case anything needs to be done after.
        if (!$success = $qformat->importpostprocess()) {
            throw new moodle_exception('importerror', 'qbank_gitsync', null, $filename);
        }

        $response = new \stdClass();
        $response->success = true;

        $eventparams = [
            'contextid' => $qformat->category->contextid,
            'other' => ['format' => 'xml', 'categoryid' => $qformat->category->id],
        ];
        $event = \core\event\questions_imported::create($eventparams);
        $event->trigger();

        return $response;
    }
}