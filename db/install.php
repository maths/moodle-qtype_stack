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
 * Stack question type installation code.
 *
 * @package    qtype_stack
 * @copyright  2012 Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_qtype_stack_install() {
    global $CFG;

    // Define stackmaximaversion config parameter
    if (!preg_match('~\[ STACK-Maxima started, library version (\d{10}) \]~',
            file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
        throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
    }
    set_config('stackmaximaversion', $matches[1], 'qtype_stack');
}
