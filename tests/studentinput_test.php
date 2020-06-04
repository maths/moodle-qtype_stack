<?php
// This file is part of STACK - http://stack.maths.ed.ac.uk//
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/answertest/controller.class.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/fixtures/inputfixtures.class.php');

// Add in all the tests from studentinput.php into the unit testing framework.
// These are exposed to users as documentation and the Travis integration should also run all the tests.
//
// @copyright 2016 The University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_studentinput_testcase extends qtype_stack_testcase {

    /**
     * @dataProvider stack_inputvalidation_test_data::get_raw_test_data
     */
    public function test_studentinput() {
        $test = stack_inputvalidation_test_data::test_from_raw(func_get_args(), 'typeless');
        $result = stack_inputvalidation_test_data::run_test($test);

        $this->assert_equals_ignore_spaces_and_e($result->display, $result->casdisplay);
        $this->assertEquals($result->ansnotes, $result->casnotes);
        $this->assertTrue($result->passed);
    }

    /**
     * @dataProvider stack_inputvalidation_test_data::get_raw_test_data_units
     */
    public function test_studentinput_units() {
        $test = stack_inputvalidation_test_data::test_from_raw(func_get_args(), 'units');
        $result = stack_inputvalidation_test_data::run_test($test);

        $this->assert_equals_ignore_spaces_and_e($result->display, $result->casdisplay);
        $this->assertEquals($result->ansnotes, $result->casnotes);
        $this->assertTrue($result->passed);
    }
}
