<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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
 * This script runs the answers tests and verifies the results.
 *
 * This serves two purposes. First, it verifies that the answer tests are working
 * correctly, and second it serves to document the expected behaviour of answer
 * tests, which is useful for learning how they work.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class maxima_corrective_parser_test_data {

    const RAWINPUT    = 0; // What a student might type.
    const PARSED      = 1; // What we expect to get from maxima corrective parser.
    const EXPECTNOTES = 2; // Expected array of answer notes.
    const EXPECTERRS  = 3; // Expected array of errors.

    protected static $rawdata = [
        ['2x', '2*x', [0 => 'missing_stars'], []],
        ['sin(x)a', 'sin(x)*a', [0 => 'missing_stars'], []],
        ['-12(7x+1)', '-12*(7*x+1)', [0 => 'missing_stars'], []],
        ['(5x+1)3', '(5*x+1)*3', [0 => 'missing_stars'], []],
            // The following does not insert a * here, because this is legitimate maxima and may be forbidden
            // separately in students' input.
        ['sin(x)(a+b)', 'sin(x)(a+b)', [], []],
        ['2 x', '2*x', [0 => 'spaces'], []],
        ['sin(x) a', 'sin(x)*a', [0 => 'spaces'], []],
        ['-12 (7 x+1)', '-12*(7*x+1)', [0 => 'spaces'], []],
        ['x%3', 'x%3', [], []],
        [
            '1+3x^2+7 x', '1+3*x^2+7*x', [0 => 'missing_stars', 1 => 'spaces'],
            [],
        ],
        [
            "f'(x)+1", null, [0 => 'apostrophe'],
            [0 => 'Apostrophes are not permitted in responses.'],
        ],
        ['x>1 and x<4', 'x > 1 and x < 4', [], []],
        [
            'x=>1 and x<4', null, [0 => 'backward_inequalities'],
            [
                0 => 'Non-strict inequalities e.g. <span class="filter_mathjaxloader_equation">' .
                            '<span class="nolink">\( \leq \)</span></span> or ' .
                            '<span class="filter_mathjaxloader_equation"><span class="nolink">\( \geq \)</span></span>' .
                            ' must be entered as <= or >=.  You have <span class="stacksyntaxexample">=></span> ' .
                            'in your expression, which is backwards.',
            ],
        ],
        [
            'x>1 and x<>4', null, [0 => 'spuriousop'],
            [0 => 'Unknown operator: <span class="stacksyntaxexample"><></span>.'],
        ],
        [
            'x^2+2*x==1', null, [0 => 'spuriousop'],
            [0 => 'Unknown operator: <span class="stacksyntaxexample">==</span>.'],
        ],
        [
            'x|y', null, [0 => 'spuriousop'],
            [0 => 'Unknown operator: <span class="stacksyntaxexample">|</span>.'],
        ],
        [
            'x=1,2', null, [0 => 'unencapsulated_comma'],
            [
                0 => 'A comma in your expression appears in a strange way.  ' .
                            'Commas are used to separate items in lists, sets etc.  You need to use a decimal point, ' .
                            'not a comma, in floating point numbers.',
            ],
        ],
        [
            'x^', null, [0 => 'finalChar'],
            [0 => '\'^\' is an invalid final character in <span class="stacksyntaxexample">x^</span>'],
        ],
        [
            '2+!4*x', null, [0 => 'badpostfixop'],
            [0 => 'You have a bad "postfix" operator in your expression.'],
        ],
        ['/* Comment */', '/* Comment */', [], []],
        [
            '/* Open comment', null, [0 => 'spaces', 1 => 'spuriousop'],
            [0 => 'Unknown operator: <span class="stacksyntaxexample">/*</span>.'],
        ],
    ];

    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    public static function test_from_raw($data) {
        $test = new stdClass();
        $test->rawinput      = $data[self::RAWINPUT];
        $test->expectedstr = null;
        if ($data[self::PARSED] !== null) {
            $test->expectedstr   = $data[self::PARSED] . ";\n";
        }
        $test->expectnotes = $data[self::EXPECTNOTES];
        $test->expecterrs  = $data[self::EXPECTERRS];
        return $test;
    }

    public static function run_test($test) {
        $notes      = [];
        $errors     = [];

        $ast         = maxima_corrective_parser::parse($test->rawinput, $errors, $notes, []);

        // We don't always get an ast, to check if it is not null.
        $test->resultstr = null;
        if ($ast) {
            $test->resultstr = $ast->toString();
        }
        $test->errors = $errors;
        $test->notes  = $notes;
        return($test);
    }
}
