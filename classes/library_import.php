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
 * External API for AJAX calls to import question info from a library file.
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
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionlibrary.class.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/modlib.php');

use context;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use qformat_xml;
use core_question\local\bank\question_edit_contexts;
use mod_quiz\quiz_settings;

/**
 * External API for AJAX calls.
 */
class library_import extends \external_api {
    /**
     * Returns parameter types for library_import webservice.
     *
     * @return \external_function_parameters Parameters
     */
    public static function import_execute_parameters() {
        return new \external_function_parameters([
            'courseid' => new \external_value(PARAM_INT, 'ID of current course.'),
            'category' => new \external_value(PARAM_INT, 'Question category where user has edit access'),
            'filepath' => new \external_value(PARAM_RAW, 'File path relative to samplequestions'),
            'isfolder' => new \external_value(PARAM_BOOL, 'Is import of whole question folder requested?'),
        ]);
    }

    /**
     * Returns result type for library_import webservice.
     *
     * @return \external_multiple_structure Result type
     */
    public static function import_execute_returns() {
        return new \external_multiple_structure(
            new \external_single_structure([
                'success' => new \external_value(PARAM_BOOL, 'Success'),
                'questionid' => new \external_value(PARAM_INT, 'Question id'),
                'filename' => new \external_value(PARAM_TEXT, 'File name'),
                'questionname' => new \external_value(PARAM_TEXT, 'Question name'),
                'isstack' => new \external_value(PARAM_BOOL, 'Is this a stack question?'),
            ])
        );
    }

    /**
     * Imports a question from STACK library.
     *
     * @param int $courseid ID of current course.
     * @param int $category Question category id for import.
     * @param string $filepath File path relative to samplequestions.
     * @return array Question details.
     */
    public static function import_execute($courseid, $category, $filepath, $isfolder) {
        global $CFG, $DB;
        $params = self::validate_parameters(self::import_execute_parameters(), [
            'courseid' => $courseid,
            'category' => $category,
            'filepath' => $filepath,
            'isfolder' => $isfolder,
        ]);
        // Check parameters and permissions.
        $thiscontext = null;
        $qformat = null;
        $thiscategory = $DB->get_record('question_categories', ['id' => $params['category']]);
        $contextid = $thiscategory->contextid;
        $thiscontext = context::instance_by_id($contextid);
        // For quiz import we also double-check course access later.
        self::validate_context($thiscontext);
        require_capability('moodle/question:add', $thiscontext);
        $loadingquiz = false;
        $categories = [];

        if (pathinfo($params['filepath'], PATHINFO_EXTENSION) === 'json'
                    && strrpos($params['filepath'], '_quiz.json') !== false) {
            // We've got a quiz file. Load JSON and instantiate.
            $quizcontents = file_get_contents($CFG->dirroot . '/question/type/stack/samplequestions/' . $params['filepath']);
            $quizdata = json_decode($quizcontents);
            // We have to create the quiz, import the questions and then add the questions to the quiz.
            // Create quiz and its default category. This is now our target category which we add to the quiz data.
            $quizcreate = self::import_quiz($courseid, $quizdata);
            $thiscategory = $quizcreate->defaultcategory;
            $quizdata->quiz->cmid = $quizcreate->cmid;
            $questions = $quizdata->questions;

            // Convert the paths to the question files to be relative to the sample questions folder. Paths in the quiz
            // data file are relative to the location of the quiz data file itself.
            $reldirname = dirname($params['filepath']);
            $files = array_map(function($question) use ($reldirname) {
                return $reldirname . $question->quizfilepath;
            }, $questions);
            // Create an array of the gitsync category files we will also need. Each unique directory
            // we have based on the question files will have a category file.
            foreach ($files as $file) {
                $category = dirname($file) . '/' . 'gitsync_category.xml';
                if (!array_search($category, $categories)) {
                    array_push($categories, $category);
                }
            }
            $loadingquiz = true;
        } else if (!$params['isfolder']) {
            // We're only importing one question. Stick the supplied fielpath in an array.
            $files = [$params['filepath']];
        } else {
            // We're importing a folder.
            // Full path of supplied question.
            $fullpath = $CFG->dirroot . '/question/type/stack/samplequestions/' . $params['filepath'];
            $reldirname = dirname($params['filepath']);
            // List all the files in the same folder.
            $files = scandir(dirname($fullpath));
            // Discard anything which isn't XML. Also discard category files.
            $files = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'xml' && strrpos($file, 'gitsync_category') === false;
            });
            // Convert file names into paths relative to the sample questions folder.
            $files = array_map(function($file) use ($reldirname) {
                return $reldirname . '/' . $file;
            }, $files);
        }
        $response = [];

        $categoryids = [];
        // If we're importing a quiz we need to make sure the required question categories exist.
        foreach ($categories as $category) {
            // We supply the base category for the context and let the import process
            // figure out if it needs to create a new category based on the info in the file.
            $qformat = new qformat_xml();
            $qformat->set_display_progress(false);
            $qformat->setCategory($thiscategory);
            $qformat->setCatfromfile(true);
            $qformat->setFilename($CFG->dirroot . '/question/type/stack/samplequestions/' . $category);
            $qformat->setContextfromfile(false);
            $qformat->setStoponerror(true);
            $contexts = new question_edit_contexts($thiscontext);
            $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));

            // Import.
            if (!$qformat->importpreprocess()) {
                // Import failure writes directly to output. This breaks the response JSON.
                ob_clean();
                continue;
            }

            if (!$qformat->importprocess()) {
                ob_clean();
                continue;
            }
            // In case anything needs to be done after.
            if (!$qformat->importpostprocess()) {
                ob_clean();
                continue;
            }
            // Create an easy way to get the id of the category from the location of a question file.
            $categoryids[dirname($category)] = $qformat->category->id;
        }

        $questionevents = [];
        // Import the questions.
        foreach ($files as $file) {
            $output = new \stdClass();
            $output->success = false;
            $output->filename = basename($file);
            $output->questionid = 0;
            $output->questionname = '';
            $output->isstack = false;

            // Set up import. All files are XML.
            $qformat = new qformat_xml();
            $qformat->set_display_progress(false);
            // If we're loading a quiz, get the correct catgory for the question otherwise always
            // use the category supplied by the user.
            if ($loadingquiz) {
                $currentcategoryid = $categoryids[dirname($file)];
                $currentcategory = $DB->get_record('question_categories', ['id' => $currentcategoryid]);
                $qformat->setCategory($currentcategory);
            } else {
                $qformat->setCategory($thiscategory);
            }
            $qformat->setCatfromfile(false);

            $qformat->setFilename($CFG->dirroot . '/question/type/stack/samplequestions/' . $file);
            $qformat->setContextfromfile(false);
            $qformat->setStoponerror(true);
            $contexts = new question_edit_contexts($thiscontext);
            $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));

            // Import.
            if (!$qformat->importpreprocess()) {
                // Import failure writes directly to output. This breaks the response JSON.
                ob_clean();
                $response[] = $output;
                continue;
            }

            if (!$qformat->importprocess()) {
                ob_clean();
                $response[] = $output;
                continue;
            }
            // In case anything needs to be done after.
            if (!$qformat->importpostprocess()) {
                ob_clean();
                $response[] = $output;
                continue;
            }

            // When loading a quiz we need to record the id of each question in Moodle so we can insert
            // the questions in to the quiz later.
            if ($loadingquiz) {
                foreach ($quizdata->questions as $qindex => $currentq) {
                    if ($reldirname . $currentq->quizfilepath === $file) {
                        break;
                    }
                }
                $quizdata->questions[$qindex]->id = (int) $qformat->questionids[0];
            }

            $output->success = true;
            $output->questionid = $qformat->questionids[0];
            $question = $DB->get_record('question', ['id' => $qformat->questionids[0]], 'id, name, qtype');
            $output->questionname = $question->name;
            $output->isstack = ($question->qtype === 'stack') ? true : false;
            $response[] = $output;
            $eventdata = ['qcategoryid' => $qformat->category->id, 'qcontextid' => $qformat->category->contextid];
            $aleadylisted = false;
            foreach ($questionevents as $currentevent) {
                if (
                    $currentevent['qcategoryid'] === $eventdata['qcategoryid'] &&
                    $currentevent['qcontextid'] === $eventdata['qcontextid']
                ) {
                    $aleadylisted = true;
                    break;
                }
            }
            if (!$aleadylisted) {
                $questionevents[] = $eventdata;
            }
        }

        foreach ($questionevents as $questionevent) {
            // Log import if we've had a success.
            $eventparams = [
                'contextid' => $questionevent['qcontextid'],
                'other' => ['format' => 'xml', 'categoryid' => $questionevent['qcategoryid']],
            ];
            $event = \core\event\questions_imported::create($eventparams);
            $event->trigger();
        }

        if ($loadingquiz) {
            // We need to insert the questions into the quiz.
            self::import_quiz($courseid, $quizdata);
            $output = new \stdClass();
            $output->success = true;
            $output->filename = basename($params['filepath']);
            $output->questionid = $quizdata->quiz->cmid;
            $output->questionname = stack_string('stack_library_quiz_prefix') . ' ' . $quizdata->quiz->name;
            $output->isstack = false;
            $response[] = $output;
        }

        return $response;
    }

    /**
     * Create a quiz in Moodle based on the data from file.
     *
     * First call creates the quiz and returns the course module id. Second call
     * inserts the questions into the quiz.
     *
     * @param int $courseid course id
     * @param object $quizdata Object containing basic quiz info.
     * @return \StdClass
     */
    public static function import_quiz($courseid, $quizdata) {
        global $CFG, $DB;
        $result = new \StdClass();
        // Check user has add capability on the course in case they've tampered with the courseid.
        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        require_capability('moodle/question:add', $coursecontext);

        // Set basic quiz info.
        $moduleinfo = new \stdClass();
        $moduleinfo->name = $quizdata->quiz->name;
        $moduleinfo->modulename = 'quiz';
        $moduleinfo->module = $DB->get_field('modules', 'id', ['name' => 'quiz']);
        $moduleinfo->course = $courseid;
        $moduleinfo->section = 1;
        $moduleinfo->quizpassword = '';
        $moduleinfo->visible = true;
        $moduleinfo->introeditor = [
                                    'text' => $quizdata->quiz->intro,
                                    'format' => (int) $quizdata->quiz->introformat,
                                    'itemid' => 0,
                                    ];
        $moduleinfo->preferredbehaviour = 'deferredfeedback';
        $moduleinfo->grade = $quizdata->quiz->grade;
        $moduleinfo->questionsperpage = (int) $quizdata->quiz->questionsperpage;
        $moduleinfo->shuffleanswers = true;
        $moduleinfo->navmethod = $quizdata->quiz->navmethod;
        $moduleinfo->timeopen = 0;
        $moduleinfo->timeclose = 0;
        $moduleinfo->decimalpoints = 2;
        $moduleinfo->questiondecimalpoints = -1;
        $moduleinfo->grademethod = 1;
        $moduleinfo->graceperiod = 0;
        $moduleinfo->timelimit = 0;
        if (!empty($quizdata->quiz->cmid)) {
            // We're updating a quiz.
            $moduleinfo->coursemodule = (int) $quizdata->quiz->cmid;
            $moduleinfo->cmidnumber = $moduleinfo->coursemodule;
            $module = get_coursemodule_from_id('', $moduleinfo->coursemodule, 0, false, \MUST_EXIST);
            list($module, $moduleinfo) = \update_moduleinfo($module, $moduleinfo, \get_course($courseid));
            $module = get_module_from_cmid($moduleinfo->coursemodule)[0];
        } else {
            // We're creating the quiz.
            $moduleinfo->cmidnumber = '';
            $moduleinfo = \add_moduleinfo($moduleinfo, \get_course($courseid));

            $module = get_module_from_cmid($moduleinfo->coursemodule)[0];
        }
        $result->cmid = $module->cmid;
        // Post-creation updates.
        $reviewchoice = [];
        $reviewchoice['reviewattempt'] = 69888;
        $reviewchoice['reviewcorrectness'] = 4352;
        $reviewchoice['reviewmarks'] = 4352;
        $reviewchoice['reviewspecificfeedback'] = 4352;
        $reviewchoice['reviewgeneralfeedback'] = 4352;
        $reviewchoice['reviewrightanswer'] = 4352;
        $reviewchoice['reviewoverallfeedback'] = 4352;
        $reviewchoice['id'] = $moduleinfo->instance;
        $DB->update_record('quiz', $reviewchoice);

        // Sort questions by slot.
        usort($quizdata->questions, function($a, $b) {
            if ((int) $a->slot > (int) $b->slot) {
                return 1;
            } else if ((int) $a->slot < (int) $b->slot) {
                return -1;
            } else {
                return 0;
            }
        });
        if (!empty($quizdata->quiz->cmid)) {
            // We can only add questions if the quiz already exists.
            foreach ($quizdata->questions as $question) {
                // Double-check user has question access.
                quiz_require_question_use($question->id);
                quiz_add_quiz_question($question->id, $module, (int) $question->page, (float) $question->maxmark);
                if ($question->requireprevious) {
                    $quizcontext = \context_module::instance($result->cmid);
                    $questionbankentryid = $DB->get_field_sql(
                        'SELECT MAX(questionbankentryid) FROM {question_versions} WHERE questionid = :questionid',
                        ['questionid' => $question->id]
                    );
                    $itemid = $DB->get_field('question_references', 'itemid',
                        ['usingcontextid' => $quizcontext->id, 'questionbankentryid' => $questionbankentryid]);
                    $DB->set_field('quiz_slots', 'requireprevious', 1, ['id' => $itemid]);
                }
            }
            if (class_exists('mod_quiz\grade_calculator')) {
                quiz_settings::create($moduleinfo->instance)->get_grade_calculator()->recompute_quiz_sumgrades();
            } else {
                quiz_update_sumgrades($module);
            }
            // NB Must add questions before updating sections.
            foreach ($quizdata->sections as $section) {
                $section->quizid = $moduleinfo->instance;
                $section->firstslot = (int) $section->firstslot;
                // First slot will have been automatically created so we need to overwrite.
                if ($section->firstslot == 1) {
                    $sectionid = $DB->get_field('quiz_sections', 'id',
                        ['quizid' => $moduleinfo->instance, 'firstslot' => 1]);
                    $section->id = $sectionid;
                    $DB->update_record('quiz_sections', $section);
                } else {
                    $sectionid = $DB->insert_record('quiz_sections', $section);
                }
                $slotid = $DB->get_field('quiz_slots', 'id',
                    ['quizid' => $moduleinfo->instance, 'slot' => (int) $section->firstslot]);

                // Log section break created event.
                $event = \mod_quiz\event\section_break_created::create([
                    'context' => $coursecontext,
                    'objectid' => $sectionid,
                    'other' => [
                        'quizid' => $section->quizid,
                        'firstslotnumber' => $section->firstslot,
                        'firstslotid' => $slotid,
                        'title' => $section->heading,
                    ],
                ]);
                $event->trigger();
            }
        } else {
            $quizcontext = \context_module::instance($result->cmid);
            $result->defaultcategory = \question_make_default_categories([$quizcontext]);
        }

        foreach ($quizdata->feedback as $feedback) {
            $feedback->quizid = $moduleinfo->instance;
            $DB->insert_record('quiz_feedback', $feedback);
        }

        return $result;
    }
}
