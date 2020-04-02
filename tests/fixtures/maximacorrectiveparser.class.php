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

defined('MOODLE_INTERNAL') || die();

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

    protected static $rawdata = array(
            array('2x', '2*x', array(0 => 'missing_stars'), array()),
            array('sin(x)a', 'sin(x)*a', array(0 => 'missing_stars'), array()),
            array('-12(7x+1)', '-12*(7*x+1)', array(0 => 'missing_stars'), array()),
            array('(5x+1)3', '(5*x+1)*3', array(0 => 'missing_stars'), array()),
            // The following does not insert a * here, because this is legitimate maxima and may be forbidden
            // separately in students' input.
            array('sin(x)(a+b)', 'sin(x)(a+b)', array(), array()),
            array('2 x', '2*x', array(0 => 'spaces'), array()),
            array('sin(x) a', 'sin(x)*a', array(0 => 'spaces'), array()),
            array('-12 (7 x+1)', '-12*(7*x+1)', array(0 => 'spaces'), array()),
            array('x%3', 'x%3', array(), array()),
            array('1+3x^2+7 x', '1+3*x^2+7*x', array(0 => 'missing_stars', 1 => 'spaces'),
                    array()),
            array("f'(x)+1", null, array(0 => 'apostrophe'),
                    array(0 => 'Apostrophes are not permitted in responses.')),
            array('x>1 and x<4', 'x > 1 and x < 4', array(), array()),
            array('x=>1 and x<4', null, array(0 => 'backward_inequalities'),
                    array(0 => 'Non-strict inequalities e.g. <span class="filter_mathjaxloader_equation">' .
                            '<span class="nolink">\( \leq \)</span></span> or ' .
                            '<span class="filter_mathjaxloader_equation"><span class="nolink">\( \geq \)</span></span>' .
                            ' must be entered as <= or >=.  You have <span class="stacksyntaxexample">=></span> ' .
                            'in your expression, which is backwards.')),
            array('x>1 and x<>4', null, array(0 => 'spuriousop'),
                    array(0 => 'Unknown operator: <span class="stacksyntaxexample"><></span>.')),
            array('x^2+2*x==1', null, array(0 => 'spuriousop'),
                    array(0 => 'Unknown operator: <span class="stacksyntaxexample">==</span>.')),
            array('x|y', null, array(0 => 'spuriousop'),
                    array(0 => 'Unknown operator: <span class="stacksyntaxexample">|</span>.')),
            array('x=1,2', null, array(0 => 'unencapsulated_comma'),
                    array(0 => 'A comma in your expression appears in a strange way.  ' .
                            'Commas are used to separate items in lists, sets etc.  You need to use a decimal point, ' .
                            'not a comma, in floating point numbers.')),
            array('x^', null, array(0 => 'finalChar'),
                    array(0 => '\'^\' is an invalid final character in <span class="stacksyntaxexample">x^</span>')),
            array('2+!4*x', null, array(0 => 'badpostfixop'),
                    array(0 => 'You have a bad "postfix" operator in your expression.')),
            );

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
        $notes      = array();
        $errors     = array();

        $ast         = maxima_corrective_parser::parse($test->rawinput, $errors, $notes, array());

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