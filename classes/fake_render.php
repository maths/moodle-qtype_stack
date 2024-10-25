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
 * Fake library_render class for use in unit tests.
 * Putting it elsewhere causes various problems.
 *
 * @package qtype_stack
 * @copyright 2024 University of Edinburgh
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

/**
 * Mock the question render call in library_render
 * as causes a timeout otherwise due to webservice testing setup.
 * Render is tested seperately in questionlibrary_test.php.
 */
class fake_render extends library_render {
    public static function call_question_render($question) {
        return '<p>Hello World</p>';
    }
}
