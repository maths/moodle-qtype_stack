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
 * Standard Moodle callbacks for qtype_stack
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks file access for matching questions.
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @return bool
 */
function qtype_stack_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_stack', $filearea, $args, $forcedownload);
}

/**
 * Called via pluginfile.php -> question_pluginfile to serve files belonging to
 * a question in a question_attempt when that attempt is in a qtype_stack usage.
 * (i.e. the STACK question dashboard)
 *
 * @package qtype_stack
 * @category files
 * @param stdClass $course course settings object
 * @param stdClass $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param int $qubaid the attempt usage id.
 * @param int $slot the id of a question in this quiz attempt.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function qtype_stack_question_pluginfile($course, $context, $component,
                    $filearea, $qubaid, $slot, $args, $forcedownload, array $options=[]) {

    list($context, $course, $cm) = get_context_info_array($context->id);
    require_login($course, false, $cm);

    $quba = question_engine::load_questions_usage_by_activity($qubaid);

    if (!question_has_capability_on($quba->get_question($slot, false), 'use')) {
        send_file_not_found();
    }

    // We don't check file access as there shouldn't be a restriction on which parts
    // of the question can be shown.

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/{$component}/{$filearea}/{$relativepath}";
    $file = $fs->get_file_by_hash(sha1($fullpath));
    if (!$file || $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

