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

namespace qtype_stack\output;

/**
 * Mobile output class for question type stack.
 *
 * @package qtype_stack
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    public static function stack_view() {
        global $CFG;
        return [
            'templates' => [[
                'id' => 'main',
                'html' => file_get_contents($CFG->dirroot . '/question/type/stack/mobile/stack.html'),
            ], ],
            'javascript' => file_get_contents($CFG->dirroot . '/question/type/stack/mobile/stack.min.js'),
        ];
    }
}
