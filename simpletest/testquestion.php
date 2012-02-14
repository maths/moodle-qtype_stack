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
 * Unit tests for the OU multiple response question class.
 *
 * @package    qtype
 * @subpackage oumultiresponse
 * @copyright 2008 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


/**
 * Unit tests for (some of) question/type/oumultiresponse/questiontype.php.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qtype_stack_question_test extends UnitTestCase {

    public function test_get_expected_data() {
        $q = test_question_maker::make_question('stack');
        $this->assertEqual(array('ans1' => PARAM_RAW), $q->get_expected_data());
    }
}
