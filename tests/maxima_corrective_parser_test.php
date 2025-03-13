<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

namespace qtype_stack;

use maxima_corrective_parser_test_data;
use qtype_stack_testcase;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for verious AST filters.
 *
 * @package    qtype_stack
 * @copyright  2019 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../stack/maximaparser/corrective_parser.php');

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/fixtures/maximacorrectiveparser.class.php');

/**
 * Add description here.
 * @group qtype_stack
 * @covers \maxima_corrective_parser
 */
final class maxima_corrective_parser_test extends qtype_stack_testcase {

    /**
     * Add description
     * @codingStandardsIgnoreStart
     * Provider in another class/file throws false code check error.
     * @dataProvider maxima_corrective_parser_test_data::get_raw_test_data
     * @codingStandardsIgnoreEnd
     */
    public function test_maxima_corrective_parser(): void {

        $test = maxima_corrective_parser_test_data::test_from_raw(func_get_args());
        $result = maxima_corrective_parser_test_data::run_test($test);

        $this->assertEquals($test->expectedstr, $result->resultstr);
        $this->assertEquals($test->expectnotes, $result->notes);
        $this->assertEquals($test->expecterrs, $result->errors);
    }

}
