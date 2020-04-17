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

defined('MOODLE_INTERNAL') || die();

/**
 * This script provides test cases for the numerical rounding tests.
 *
 *
 * @copyright  2016 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class stack_numbers_test_data {

    // In this text digits are 1-9 and 0 is not a digit.
    // array("string", lower, upper, decimal places, dispvalue, casvalue from dispdp).
    protected static $rawdata = array(
            array("0", 1, 1, 0, '"~a"', '0'), // Decision: zero has one significant digit.
            array("0.0", 1, 1, 1, '"~,1f"', '0.0'), // Decision: 0.0 has one significant digit.
            array("0.00", 2, 2, 2, '"~,2f"', '0.00'),
            array("00.00", 2, 2, 2, '"~,2f"', '0.00'),
            array("0.000", 3, 3, 3, '"~,3f"', '0.000'),
            array("0.0001", 1, 1, 4, '"~,4f"', '0.0001'), // Leading zeros are insignificant.
            array("0.0010", 2, 2, 4, '"~,4f"', '0.0010'),
            array("100.0", 4, 4, 1, '"~,1f"', '100.0'), // Existence of a significant zero (or digit) changes.
            array("100.", 3, 3, 0, '"~a"', '100'),
            array("00120", 2, 3, 0, '"~a"', '120'),
            array("00.120", 3, 3, 3, '"~,3f"', '0.120'),
            array("1.001", 4, 4, 3, '"~,3f"', '1.001'),
            array("2.000", 4, 4, 3, '"~,3f"', '2.000'),
            array("1234", 4, 4, 0, '"~a"', '1234'),
            array("123.4", 4, 4, 1, '"~,1f"', '123.4'),
            array("2000", 1, 4, 0, '"~a"', '2000'),
            array("10000", 1, 5, 0, '"~a"', '10000'),
            array("2001", 4, 4, 0, '"~a"', '2001'),
            array("0.01030", 4, 4, 5, '"~,5f"', '0.01030'),
            // Unary signs.
            array("+334.3", 4, 4, 1, '"~,1f"', '334.3'),
            array("-0.00", 2, 2, 2, '"~,2f"', '0.00'),
            array("-12.00", 4, 4, 2, '"~,2f"', '-12.00'),
            array("-121000", 3, 6, 0, '"~a"', '-121000'),
            array("-303.30003", 8, 8, 5, '"~,5f"', '-303.30003'),
            // Brackets should be stripped off.
            array("(-12.00)", 4, 4, 2, '"~,2f"', '-12.00'),
            array("--(-12.00)", 4, 4, 2, '"~,2f"', '-12.00'),
            array("(00.00)", 2, 2, 2, '"~,2f"', '0.00'),
            // Unary minus should be stripped off.
            array("-(12.000)", 5, 5, 3, '"~,3f"', '-12.000'),
    );

    // Use the format array("string", lower, upper, decimal places).
    protected static $rawdatautils = array(

            // Scientific notation.
            array("4.320e-3", 4, 4, 3, '"~,3e"'), // After a digit, zeros after the decimal separator are always significant.
            // If no digits before a zero that zero is not significant even after the decimal separator.
            array("0.020e3", 2, 2, 3, '"~,1e"'),
            array("1.00e3", 3, 3, 2, '"~,2e"'),
            array("10.0e1", 3, 3, 1, '"~,2e"'),
            array("0.020E3", 2, 2, 3, '"~,1e"'),
            array("1.00E3", 3, 3, 2, '"~,2e"'),
            array("10.0E1", 3, 3, 1, '"~,2e"'),
            // We insist the input only has one numerical multiplier that we act on and that is the first thing in the string.
            array("52435*mg", 5, 5, 0, '"~a"'),
            array("-12.00*m", 4, 4, 2, '"~,2f"'),
            array("-(12.00*m)", 4, 4, 2, '"~,2f"'),
            // Here we know that there are 3 significant figures but can't be sure about that trailing zero.
            array("1030*m/s", 3, 4, 0, '"~a"'),
            array("1.23*4", 3, 3, 2, '"~,2f"'),
            array("4*3.21", 1, 1, 0, '"~a"'),
            array("50*3.21", 1, 2, 0, '"~a"'),
            array("3434...34*34", 4, 4, 0, '"~a"'),
    );

    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    public static function get_raw_test_data_utils() {
        return self::$rawdatautils;
    }
}