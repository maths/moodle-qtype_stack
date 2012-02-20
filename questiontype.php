<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Question type class for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');


/**
 * Stack question type class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack extends question_type {

    public function make_question($questiondata) {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');
        require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/unittest/simpletestlib.php');
        require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');
        $q = test_question_maker::make_question('stack', $questiondata->questiontext);
        $q->id = $questiondata->id;
        $q->category = $questiondata->category;
        return $q;
    }
}
