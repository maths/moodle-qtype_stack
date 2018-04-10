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

require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/mathsoutput/mathsoutput.class.php');

// Unit tests for the OU maths filter output classes replace-dollars
// functionality.
//
// @copyright 2017 Aalto University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_maths_stack_maths_test extends advanced_testcase {

    public function test_replace_dollars_and_abacus() {
        $test0 = 'Test $\frac12$ $$\frac14$$ @1/8@ \(\frac{@a@}{2}\).';
        $expected0 = 'Test \(\frac12\) \[\frac14\] {@1/8@} \(\frac{@a@}{2}\).';
        $this->assertEquals($expected0, stack_maths::replace_dollars($test0));

        $test1 = '@1/8@ {@a@}';
        $expected1 = '{@1/8@} {@a@}';
        $this->assertEquals($expected1, stack_maths::replace_dollars($test1));

        $test2 = '{@1/8@} @a@';
        $expected2 = '{@1/8@} {@a@}';
        $this->assertEquals($expected2, stack_maths::replace_dollars($test2));

        $test3 = '{@1/8@';
        $expected3 = '{{@1/8@}';
        $this->assertEquals($expected3, stack_maths::replace_dollars($test3));

        $test4 = '@1/8@}';
        $expected4 = '{@1/8@}}';
        $this->assertEquals($expected4, stack_maths::replace_dollars($test4));

        $test5 = '{@ 1/8@} @a @ @ b@';
        $expected5 = '{@ 1/8@} {@a@} {@b@}';
        $this->assertEquals($expected5, stack_maths::replace_dollars($test5));

        $test6 = '<p>First write each term over a common denominator \[ @(A*x+B)*D/D+C/D = ((A*x+B)*D+C)/D @\]'
            . ' Then expand out brackets on the top and collect like terms to get \[ @ ev(expand((A*x+B)*D+C),simp)/D@.\]'
            . '&nbsp;</p>';
        $expected6 = '<p>First write each term over a common denominator \[ {@(A*x+B)*D/D+C/D = ((A*x+B)*D+C)/D@}\] '
            . 'Then expand out brackets on the top and collect like terms to get \[ {@ev(expand((A*x+B)*D+C),simp)/D@}.\]'
            . '&nbsp;</p>';
        $this->assertEquals($expected6, stack_maths::replace_dollars($test6));
    }
}

