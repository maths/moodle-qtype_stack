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

use TypeError;
use castext2_evaluatable;
use qtype_stack_testcase;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');

// Unit tests for {@link stack_cas_text}.

// Note there is no stack_cas_text class in castext2, in castext2
// castext is a CAS-statement like any other and the focus is on
// generating/compiling it from the source castext-code and then
// post-processing the response from CAS to turn it to a string.
// The closest to the original is the castext2_evaluatable class.

/**
 * @group qtype_stack
 */
class castext_exception_test extends qtype_stack_testcase {

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_exception_1() {
        $this->expectException(TypeError::class);
        $at1 = castext2_evaluatable::make_from_source([], null);
    }

    /**
     * @covers \qtype_stack\castext2_evaluatable::make_from_source
     */
    public function test_exception_2() {
        $this->expectException(TypeError::class);
        $at1 = castext2_evaluatable::make_from_source("Hello world", [1]);
    }
}
