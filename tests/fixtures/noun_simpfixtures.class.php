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
 * This script tests CAS functions in noun_simp.mac and verifies the results.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../stack/cas/cassession2.class.php');

/**
 * This script tests CAS functions in noun_simp.mac and verifies the results.
 */
class stack_noun_simp_test_data {

    /**
     * String, as typed into the question variables or sandbox.
     */
    const RAWSTRING     = 0;
    /**
    * Location of the "simp:true" sub-array.
    */
    const SIMPTRUE      = 1;
    /**
     * Location of the "simp:true" sub-array.
     */
    const SIMPFALSE     = 2;

    /**
     * Location of the "simp:true" sub-array.
     */
    const VALUE         = 0;
    /**
     * Location of the "simp:true" sub-array.
     */
    const DISPLAYTEX    = 1;

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected static $rawdata = [
        // Each line has two sub-arrays, the first for simp:true, the second for simp:false.
        ['a nounadd b',
            ['a nounadd b', 'a+b'],
            ['a nounadd b', 'a+b']],
        ['a nounadd -b',
            // Note simp:true does add brackets.
            ['a nounadd -b', 'a+\left(-b\right)'],
            // But the TeX does not print a+-b here.
            ['a nounadd -b', 'a-b']],
        ['-a nounadd a nounadd -b',
            // Note simp:true does add brackets.
            ['-a nounadd a nounadd -b', '-a+a+\left(-b\right)'],
            // But the TeX does not print a+-b here.
            ['-a nounadd a nounadd -b', '-a+a-b']],
        ['a nounmul b',
            ['a nounmul b', 'a\cdot b'],
            ['a nounmul b', 'a\cdot b']],
        ['a nounadd b nounmul c',
            ['a nounadd b nounmul c', 'a+b\cdot c'],
            ['a nounadd b nounmul c', 'a+b\cdot c']],
        ['a nounmul b nounadd c',
            ['a nounmul b nounadd c', 'a\cdot b+c'],
            ['a nounmul b nounadd c', 'a\cdot b+c']],
        // Mix of noun and normal.
        ['a nounadd b nounadd a+b',
            // Note, Maxima's normal simplification rules turn a+b->b+a.
            ['b+(a nounadd b nounadd a)', 'b+\left(a+b+a\right)'],
            ['(a nounadd b nounadd a)+b', 'a+b+a+b']],
        ['a nounmul b nounmul a*b',
            ['(a nounmul b nounmul a)*b', 'a\cdot b\cdot a\cdot b'],
            ['(a nounmul b nounmul a)*b', 'a\cdot b\cdot a\cdot b']],
        ['a nounadd b nounpow (c nounadd d)',
            ['a nounadd b nounpow (c nounadd d)', 'a+b^{c+d}'],
            ['a nounadd b nounpow (c nounadd d)', 'a+b^{c+d}']],
        // Prefix notation.
        ['"nounadd"(a,b)',
            ['a nounadd b', 'a+b'],
            ['a nounadd b', 'a+b']],
        ['"nounadd"(a,b,c)',
            ['a nounadd b nounadd c', 'a+b+c'],
            ['a nounadd b nounadd c', 'a+b+c']],
        ['"nounmul"(a,b)',
            ['a nounmul b', 'a\cdot b'],
            ['a nounmul b', 'a\cdot b']],
        ['"nounmul"(a,b,c)',
            ['a nounmul b nounmul c', 'a\cdot b\cdot c'],
            ['a nounmul b nounmul c', 'a\cdot b\cdot c']],
    ];

    /**
     * Provide all the test data.
     */
    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    /**
     * Create an individual test.
     * @array $data Raw data line.
     * @string $simpvalue Value of Maxima simp variable to use with test.
     */
    public static function test_from_raw($data, $simpvalue) {
        $test = new stdClass();
        $test->rawstring        = $data[self::RAWSTRING];
        $test->simp             = $simpvalue;
        if ($simpvalue === 'simp:true') {
            $simpbranch = $data[self::SIMPTRUE];
        } else if ($simpvalue === 'simp:false') {
            $simpbranch = $data[self::SIMPFALSE];
        }
        $test->expectedvalue    = $simpbranch[self::VALUE];
        $test->expectedtex      = $simpbranch[self::DISPLAYTEX];

        $test->passed           = null;
        $test->errors           = null;
        return $test;
    }

    /**
    * Actually run the test.
    */
    public static function run_test($test) {

        $cs = stack_ast_container::make_from_teacher_source($test->rawstring, '', new stack_cas_security(), []);
        $casvalue = '';
        $castex   = '';
        $passed = true;
        $errors = '';

        // Note, they all should be valid.
        if ($cs->get_valid()) {
            $options = new stack_options();
            $simp = false;
            if ($test->simp === 'simp:true') {
                $simp = true;
            }
            $options->set_option('simplify', $simp);

            $session = new stack_cas_session2([$cs], $options, 0);
            $session->instantiate();

            if ($cs->get_errors() === '') {
                $casvalue = $cs->get_value();
                $castex = $cs->get_display();
            }

            if ($test->expectedvalue !== $casvalue) {
                $passed = false;
                $errors .= 'Value mismatch: ' . $test->expectedvalue . ' !== ' . $casvalue . '. ';
            }
            if ($test->expectedtex !== $castex) {
                $passed = false;
                $errors .= 'Tex mismatch: ' . $test->expectedtex . ' !== ' . $castex . '. ';
            }
        } else {
            throw new stack_exception('noun_simp test case expected a valid expression but got: ', $test->rawstring);
        }

        $test->passed = $passed;
        if (!$passed) {
            $test->errors = $test->simp . ': ' . $errors;
        }
        return $test;
    }
}
