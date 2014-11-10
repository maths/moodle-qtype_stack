<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Unit tests for the Moodle TeX filter maths output class.
 *
 * @package   qtype_stack
 * @copyright 2014 Loughborough University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../stack/mathsoutput/mathsoutput.class.php');
require_once(__DIR__ . '/../doc/docslib.php');


/**
 * Unit tests for the maths output base class.
 *
 * @copyright 2014 Loughborough University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_maths_test extends advanced_testcase {

    public function test_no_conversion() {
        $this->assertEquals('What is \[x^2\]?',
                stack_maths::replace_dollars('What is \[x^2\]?'));
    }

    public function test_tex_conversion1() {
        $this->assertEquals('What is \[x^2\]?',
                stack_maths::replace_dollars('What is $$x^2$$?'));
    }

    public function test_tex_conversion2() {
        $this->assertEquals('What is \(x^2\)?',
                stack_maths::replace_dollars('What is $x^2$?'));
    }

    public function test_CAS_conversion() {
        $this->assertEquals('What is {@x^2@}?',
                stack_maths::replace_dollars('What is {@x^2@}?'));
    }

    public function test_CAS_conversion1() {
        $this->assertEquals('What is {@x^2@}?',
                stack_maths::replace_dollars('What is @x^2@?'));
    }

    public function test_CAS_conversion2() {
        $this->assertEquals('What is \( {@x^2@} \)?',
                stack_maths::replace_dollars('What is \( @x^2@ \)?'));
    }

    public function test_CAS_conversion3() {
        $this->assertEquals('{@x^2@} and another one {@sin(x)@}',
                stack_maths::replace_dollars('@x^2@ and another one @sin(x)@'));
    }

    public function test_CAS_conversion4() {
        $this->assertEquals('{@a@}, {@a@}, {@a@}, {@a@}, {@a@}',
                stack_maths::replace_dollars('{@a@}, {@a@}, {@a@}, @a@, {@a@}'));
    }

    public function test_CAS_tex_conversion() {
        $this->assertEquals('\({@x^2@}\) and another one \[{@sin(x)@}+1\]',
            stack_maths::replace_dollars('$@x^2@$ and another one $$@sin(x)@+1$$'));
    }

    public function test_CAS_inside_frac_conversion() {
        $this->assertEquals('\[ \frac{1+x}{@x^2@}\]',
            stack_maths::replace_dollars('\[ \frac{1+x}{@x^2@}\]'));
    }

    public function test_CAS_inside_frac_conversion2() {
        $this->assertEquals('\[ \frac{{@a@}+x}{@x^2@}\]',
            stack_maths::replace_dollars('\[ \frac{@a@+x}{@x^2@}\]'));
    }

    public function test_CAS_inside_frac_conversion3() {
        $this->assertEquals('\(\frac{d}{d{@v@}}u=1\)',
            stack_maths::replace_dollars('\(\frac{d}{d@v@}u=1\)'));
    }

    public function test_CAS_inside_frac_conversion4() {
        $this->assertEquals('\(\frac{d}{d{@v@}}u=1\)',
            stack_maths::replace_dollars('\(\frac{d}{d{@v@}}u=1\)'));
    }
}