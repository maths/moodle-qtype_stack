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

/**
 * This script provides test cases for the numerical rounding tests.
 *
 *
 * @copyright  2016 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_numbers_test_data {

    // In this text digits are 1-9 and 0 is not a digit.
    // array("string", lower, upper, decimal places, dispvalue, err).
    protected static $rawdata = [

        ["0", 1, 1, 0, '"~a"', '0', ''], // Decision: zero has one significant digit.
        ["0.0", 1, 1, 1, '"~,1f"', '0.0', ''], // Decision: 0.0 has one significant digit.
        ["0.00", 2, 2, 2, '"~,2f"', '0.00', ''],
        ["00.00", 2, 2, 2, '"~,2f"', '0.00', ''],
        ["0.000", 3, 3, 3, '"~,3f"', '0.000', ''],
        ["0.0001", 1, 1, 4, '"~,4f"', '0.0001', ''], // Leading zeros are insignificant.
        ["0.0010", 2, 2, 4, '"~,4f"', '0.0010', ''],
        ["100.0", 4, 4, 1, '"~,1f"', '100.0', ''], // Existence of a significant zero (or digit) changes.
        ["100.", 3, 3, 0, '"~a"', '100', ''],
        ["00120", 2, 3, 0, '"~a"', '120', ''],
        ["00.120", 3, 3, 3, '"~,3f"', '0.120', ''],
        ["1.001", 4, 4, 3, '"~,3f"', '1.001', ''],
        ["2.000", 4, 4, 3, '"~,3f"', '2.000', ''],
        ["1234", 4, 4, 0, '"~a"', '1234', ''],
        ["123.4", 4, 4, 1, '"~,1f"', '123.4', ''],
        ["2000", 1, 4, 0, '"~a"', '2000', ''],
        ["10000", 1, 5, 0, '"~a"', '10000', ''],
        ["2001", 4, 4, 0, '"~a"', '2001', ''],
        ["0.01030", 4, 4, 5, '"~,5f"', '0.01030', ''],
        // Unary signs.
        ["+334.3", 4, 4, 1, '"~,1f"', '334.3', ''],
        ["-0.00", 2, 2, 2, '"~,2f"', '0.00', ''],
        ["-12.00", 4, 4, 2, '"~,2f"', '-12.00', ''],
        ["-121000", 3, 6, 0, '"~a"', '-121000', ''],
        ["-303.30003", 8, 8, 5, '"~,5f"', '-303.30003', ''],
        // Brackets should be stripped off.

        ["(-12.00)", 4, 4, 2, '"~,2f"', '-12.00', ''],
        ["--(-12.00)", 4, 4, 2, '"~,2f"', '-12.00', ''],
        ["(00.00)", 2, 2, 2, '"~,2f"', '0.00', ''],
        // Unary minus should be stripped off.
        ["-(12.000)", 5, 5, 3, '"~,3f"', '-12.000', ''],
        // Deal with expressions.  This is now evaluated.
        ["1/-12.00", 1, 1, 0, '"~a"', '0', ''],
        // TODO: more tests with expressions.  Requires changes to test setup.
        // These now throw errors.
        ["e+4.3^k", 2, 2, 1, '"~,1f"', '%e+4.3^k', 'dispdp requires a real number argument.'],
        ["e+4.3e21^k", 2, 2, 1, '"~,1e"', '%e+4.3^k', 'dispdp requires a real number argument.'],
    ];

    // Use the format array("string", lower, upper, decimal places).
    protected static $rawdatautils = [

            // Scientific notation.
            ["4.320e-3", 4, 4, 3, '"~,3e"'], // After a digit, zeros after the decimal separator are always significant.
            // If no digits before a zero that zero is not significant even after the decimal separator.
            ["0.020e3", 2, 2, 3, '"~,1e"'],
            ["1.00e3", 3, 3, 2, '"~,2e"'],
            ["10.0e1", 3, 3, 1, '"~,2e"'],
            ["0.020E3", 2, 2, 3, '"~,1e"'],
            ["1.00E3", 3, 3, 2, '"~,2e"'],
            ["10.0E1", 3, 3, 1, '"~,2e"'],
            // We insist the input only has one numerical multiplier that we act on and that is the first thing in the string.
            ["52435*mg", 5, 5, 0, '"~a"'],
            ["-12.00*m", 4, 4, 2, '"~,2f"'],
            ["-(12.00*m)", 4, 4, 2, '"~,2f"'],
            // Here we know that there are 3 significant figures but can't be sure about that trailing zero.
            ["1030*m/s", 3, 4, 0, '"~a"'],
            ["1.23*4", 3, 3, 2, '"~,2f"'],
            ["4*3.21", 1, 1, 0, '"~a"'],
            ["50*3.21", 1, 2, 0, '"~a"'],
            ["3434...34*34", 4, 4, 0, '"~a"'],
    ];

    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    public static function get_raw_test_data_utils() {
        return self::$rawdatautils;
    }
}
