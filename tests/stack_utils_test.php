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
 * Unit tests for stack_utils.
 *
 * @copyright  2012 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/cas/cassession.class.php');


/**
 * Unit tests for stack_utils.
 *
 * @copyright  2012 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_utils_test extends basic_testcase {

    public function test_matching_pairs() {
        $this->assertTrue(stack_utils::check_matching_pairs('Hello $world$!', '$'));
        $this->assertFalse(stack_utils::check_matching_pairs('Hello @world!', '@'));
        $this->assertTrue(stack_utils::check_matching_pairs('', '$'));
    }

    public function test_check_bookends() {
        $this->assertSame('left', stack_utils::check_bookends('x+1)^2', '(', ')'));
        $this->assertSame('right', stack_utils::check_bookends('(x+1', '(', ')'));
        $this->assertSame('left', stack_utils::check_bookends('(y^2+1))', '(', ')'));
        $this->assertSame('left', stack_utils::check_bookends('[sin(x)+1)', '(', ')'));
        $this->assertSame('right', stack_utils::check_bookends('[sin(x)+1)', '[', ']'));
        $this->assertSame(true, stack_utils::check_bookends('x+1', '(', ')'));
        $this->assertSame(true, stack_utils::check_bookends('x+1', '[', ']'));
        $this->assertSame(true, stack_utils::check_bookends('x+1', '{', '}'));
        $this->assertSame(true, stack_utils::check_bookends('(sin(x)+1)', '[', ']'));
        $this->assertSame(true, stack_utils::check_bookends('(sin(x)+1)', '(', ')'));
        $this->assertSame(true, stack_utils::check_bookends('[sin(x)+1)', '{', '}'));
    }

    public function test_check_nested_bookends() {
        $this->assertTrue(stack_utils::check_nested_bookends(''));
        $this->assertTrue(stack_utils::check_nested_bookends('x+1'));
        $this->assertTrue(stack_utils::check_nested_bookends('(sin(x)+1)'));
        $this->assertTrue(stack_utils::check_nested_bookends('[sin(x)+1]'));
        $this->assertTrue(stack_utils::check_nested_bookends('{}[]()'));
        $this->assertTrue(stack_utils::check_nested_bookends('{[()]}'));
        $this->assertTrue(stack_utils::check_nested_bookends('{[()(()[(){}((){})])]}'));

        $this->assertFalse(stack_utils::check_nested_bookends('('));
        $this->assertFalse(stack_utils::check_nested_bookends(')'));
        $this->assertFalse(stack_utils::check_nested_bookends('x+1)'));
        $this->assertFalse(stack_utils::check_nested_bookends('(sin(x+1)'));
        $this->assertFalse(stack_utils::check_nested_bookends('[sin(x]+1)'));
        $this->assertFalse(stack_utils::check_nested_bookends('{}[()'));
        $this->assertFalse(stack_utils::check_nested_bookends('{[()(()[(){}((){})]))]}'));
    }

    public function test_substring_between() {
        $this->assertEquals(array('[hello]', 0, 6), stack_utils::substring_between('[hello] world!', '[', ']'));
        $this->assertEquals(array('[world]', 6, 12), stack_utils::substring_between('hello [world]!', '[', ']'));
        $this->assertEquals(array('[world]', 8, 14), stack_utils::substring_between('[hello] [world]!', '[', ']', 8));
        $this->assertEquals(array('[world]', 8, 14), stack_utils::substring_between('[hello] [world]!', '[', ']', 1));

        $this->assertEquals(array('$hello$', 0, 6), stack_utils::substring_between('$hello$ world!', '$', '$'));
        $this->assertEquals(array('$world$', 6, 12), stack_utils::substring_between('hello $world$!', '$', '$'));
        $this->assertEquals(array('$world$', 8, 14), stack_utils::substring_between('$hello$ $world$!', '$', '$', 8));
        $this->assertEquals(array('$ $', 6, 8), stack_utils::substring_between('$hello$ $world$!', '$', '$', 1));

        $this->assertEquals(array('[he[ll]o]', 0, 8), stack_utils::substring_between('[he[ll]o] world!', '[', ']'));
        $this->assertEquals(array('[[[]w[o]r[[l]d]]]', 6, 22),
                stack_utils::substring_between('hello [[[]w[o]r[[l]d]]]!', '[', ']'));
    }

    public function test_all_substring_between() {
        $this->assertEquals(array(), stack_utils::all_substring_between('hello world!', '[', ']'));
        $this->assertEquals(array('hello'), stack_utils::all_substring_between('[hello] world!', '[', ']'));
        $this->assertEquals(array('hello', 'world'), stack_utils::all_substring_between('[hello] [world]!', '[', ']'));

        $this->assertEquals(array(), stack_utils::all_substring_between('hello world!', '$'));
        $this->assertEquals(array('hello'), stack_utils::all_substring_between('$hello$ world!', '$'));
        $this->assertEquals(array('hello', 'world'), stack_utils::all_substring_between('$hello$ $world$!', '$'));

        // This is current behaviour, but I am not sure it is correct.
        $this->assertEquals(array('hello', 'wor'), stack_utils::all_substring_between('[he[llo] [wor]ld]!', '[', ']'));
    }

    public function test_replace_between() {
        $this->assertEquals('hello world!', stack_utils::replace_between('hello world!', '[', ']', array()));
        $this->assertEquals('[goodbye] world!', stack_utils::replace_between('[hello] world!', '[', ']', array('goodbye')));
        $this->assertEquals('[goodbye] [all]!',
                stack_utils::replace_between('[hello] [world]!', '[', ']', array('goodbye', 'all')));

        $this->assertEquals('hello world!', stack_utils::replace_between('hello world!', '$', '$', array()));
        $this->assertEquals('$goodbye$ world!', stack_utils::replace_between('$hello$ world!', '$', '$', array('goodbye')));
        $this->assertEquals('$goodbye$ $all$!',
                stack_utils::replace_between('$hello$ $world$!', '$', '$', array('goodbye', 'all')));

        $this->setExpectedException('stack_exception');
        $this->assertEquals('goodbye all!', stack_utils::replace_between('$hello$ $world$!', '$', '$', array('1', '2', '3')));
    }

    public function test_underscore() {
        $this->assertEquals('hello_world!', stack_utils::underscore('hello world!'));
        $this->assertEquals('he_he_hello_world_', stack_utils::underscore('he-he-hello world!', array('!')));
    }

    public function test_list_to_array() {
        // Do not recurse over lists.
        $a = array();
        $strin = '';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $a = array();
        $strin = '[]';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $a = array('1');
        $strin = '[1]';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $a = array('1', '2');
        $strin = '[1,2]';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $strin = '[x^2, sin(x)]';
        $a = array('x^2', ' sin(x)');
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $a = array('1', 'x+y');
        $strin = '[1,x+y]';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $a = array('[1,2]');
        $strin = '[[1,2]]';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $strin = '[[1,2,3], {x^2,x^3}]';
        $a = array('[1,2,3]', ' {x^2,x^3}');
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        $a = array('1', '1/sum([1,3])', 'matrix([1],[2])');
        $strin = '[1,1/sum([1,3]),matrix([1],[2])]';
        $this->assertEquals($a, stack_utils::list_to_array($strin, false));

        // Recurse over lists.
        $strin = '[[1,2,3], {x^2,x^3}]';
        $a = array(array('1', '2', '3'), ' {x^2,x^3}');
        $this->assertEquals($a, stack_utils::list_to_array($strin, true));
    }

    public function test_decompose_rename_operation_identity() {
        $this->assertEquals(array(), stack_utils::decompose_rename_operation(
                array('a' => 'a', 'b' => 'b')));
    }

    public function test_decompose_rename_operation_no_overlap() {
        $this->assertEquals(array('a' => 'c', 'b' => 'd'), stack_utils::decompose_rename_operation(
                array('a' => 'c', 'b' => 'd')));
    }

    public function test_decompose_rename_operation_shift() {
        $this->assertSame(array('x3' => 'x4', 'x2' => 'x3', 'x1' => 'x2'), stack_utils::decompose_rename_operation(
                array('x1' => 'x2', 'x2' => 'x3', 'x3' => 'x4')));
    }

    public function test_decompose_rename_operation_simple_swap() {
        $this->assertEquals(array('a' => 'temp1', 'b' => 'a', 'temp1' => 'b'), stack_utils::decompose_rename_operation(
                array('a' => 'b', 'b' => 'a')));
    }

    public function test_decompose_rename_operation_cycle_temp_already_used() {
        $this->assertEquals(array('temp1' => 'temp4', 'temp3' => 'temp1', 'temp2' => 'temp3', 'temp4' => 'temp2'),
                stack_utils::decompose_rename_operation(
                array('temp1' => 'temp2', 'temp2' => 'temp3', 'temp3' => 'temp1')));
    }

    public function test_decompose_rename_operation_complex() {
        $this->assertEquals(array('i' => 'j', 'h' => 'i', 'a' => 'temp1', 'e' => 'a', 'g' => 'e', 'temp1' => 'g',
                'd' => 'temp2', 'f' => 'd', 'temp2' => 'f'), stack_utils::decompose_rename_operation(
                array('a' => 'g', 'b' => 'b', 'd' => 'f', 'd' => 'f', 'e' => 'a', 'f' => 'd', 'g' => 'e', 'h' => 'i', 'i' => 'j')));
    }

    public function test_all_substring_strings() {
        $this->assertEquals(array("test", "testb"), stack_utils::all_substring_strings("stringa:\"test\" and stringb:\"testb\""));
        $this->assertEquals(array("", "\\\""), stack_utils::all_substring_strings("stringa:\"\" and stringb:\"\\\"\""));
    }

    public function test_eliminate_strings() {
        $this->assertEquals('stringa:"" and stringb:""', stack_utils::eliminate_strings("stringa:\"test\" and stringb:\"testb\""));
        $this->assertEquals('stringa:"" and stringb:""', stack_utils::eliminate_strings("stringa:\"\" and stringb:\"\\\"\""));
    }

    public function test_decimal_digits() {
        // In this text digits are 1-9 and 0 is not a digit.
        // array("string", lower, upper, decimal places).
        $tests = array(
            array("0", 1, 1, 0, '"~a"'), // Decision: zero has one significant digit.
            array("0.0", 1, 1, 1, '"~,1f"'), // Decision: 0.0 has one significant digit.
            array("0.00", 2, 2, 2, '"~,2f"'),
            array("00.00", 2, 2, 2, '"~,2f"'),
            array("0.000", 3, 3, 3, '"~,3f"'),
            array("0.0001", 1, 1, 4, '"~,4f"'), // Leading zeros are insignificant.
            array("0.0010", 2, 2, 4, '"~,4f"'),
            array("100.0", 4, 4, 1, '"~,1f"'), // Existence of a significant zero (or digit) changes.
            array("100.", 3, 3, 0, '"~a"'),
            array("00120", 2, 3, 0, '"~a"'),
            array("00.120", 3, 3, 3, '"~,3f"'),
            array("1.001", 4, 4, 3, '"~,3f"'),
            array("2.000", 4, 4, 3, '"~,3f"'),
            array("1234", 4, 4, 0, '"~a"'),
            array("123.4", 4, 4, 1, '"~,1f"'),
            array("2000", 1, 4, 0, '"~a"'),
            array("10000", 1, 5, 0, '"~a"'),
            array("2001", 4, 4, 0, '"~a"'),
            array("0.01030", 4, 4, 5, '"~,5f"'),
            // Scientific notation.
            array("4.320e-3", 4, 4, 3, '"~,3e"'), // After a digit, zeros after the decimal separator are always significant.
            // If no digits before a zero that zero is not significant even after the decimal separator.
            array("0.020e3", 2, 2, 3, '"~,1e"'),
            array("1.00e3", 3, 3, 2, '"~,2e"'),
            array("10.0e1", 3, 3, 1, '"~,2e"'),
            // Unary signs.
            array("+334.3", 4, 4, 1, '"~,1f"'),
            array("-0.00", 2, 2, 2, '"~,2f"'),
            array("-12.00", 4, 4, 2, '"~,2f"'),
            array(" -121000", 3, 6, 0, '"~a"'),
            array("-303.30003", 8, 8, 5, '"~,5f"'),
            // We insist the input only has one numerical multiplier that we act on and that is the first thing in the string.
            array("52435*mg", 5, 5, 0, '"~a"'),
            array("-12.00*m", 4, 4, 2, '"~,2f"'),
            // Here we know that there are 3 significant figures but can't be sure about that trailing zero.
            array("1030*m/s", 3, 4, 0, '"~a"'),
            array("1.23*4", 3, 3, 2, '"~,2f"'),
            array("4*3.21", 1, 1, 0, '"~a"'),
            array("50*3.21", 1, 2, 0, '"~a"'),
            array("3434...34*34", 4, 4, 0, '"~a"'),
        );

        foreach ($tests as $t) {
            $r = stack_utils::decimal_digits($t[0]);
            $this->assertEquals($r['lowerbound'], $t[1]);
            $this->assertEquals($r['upperbound'], $t[2]);
            $this->assertEquals($r['decimalplaces'], $t[3]);
            $this->assertEquals($r['fltfmt'], $t[4]);
        }

    }

    public function test_single_char_vars_2() {

        $testcases = array('ab' => 'a*b',
            'abc' => 'a*b*c',
            'ab*c+a+(b+cd)' => 'a*b*c+a+(b+c*d)',
            'sin(xy)' => 'sin(x*y)',
            'sin(xy)+cos(ab)+c' => 'sin(x*y)+cos(a*b)+c',
            'xe^x' => 'x*e^x',
            'pix' => 'p*i*x',
            '2(xya+3c)' => '2(x*y*a+3c)',
            '2pi+nk' => '2pi+n*k',  // This function does not add the star in 2*pi here.  That is done elsewhere.
            '(ax+1)(ax-1)' => '(a*x+1)(a*x-1)',
            'nx(1+2x)' => 'nx(1+2x)' // Note, two letter function names are permitted.
        );

        foreach ($testcases as $test => $result) {
            $this->assertEquals(stack_utils::make_single_char_vars($test, null, false, 2, ''), $result);
        }

    }

    public function test_single_char_vars_5() {

        $testcases = array('ab' => 'a*b',
            'abc' => 'a*b*c',
            'ab*c+a+(b+cd)' => 'a*b*c+a+(b+c*d)',
            'sin(xy)' => 'sin(x*y)',
            'sin(xy)+cos(ab)+c' => 'sin(x*y)+cos(a*b)+c',
            'xe^x' => 'x*e^x',
            'pix' => 'p*i*x',
            '2(xya+3c)' => '2*(x*y*a+3*c)',
            '2pi+nk' => '2*pi+n*k',
            '(ax+1)(ax-1)' => '(a*x+1)*(a*x-1)',
            'nx(1+2x)' => 'nx(1+2*x)' // Note, two letter function names are permitted.
        );

        foreach ($testcases as $test => $result) {
            $this->assertEquals(stack_utils::make_single_char_vars($test, null, false, 5, ''), $result);
        }

    }
}
