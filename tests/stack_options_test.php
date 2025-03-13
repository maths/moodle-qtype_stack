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

use qtype_stack_testcase;
use stack_exception;
use stack_options;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/options.class.php');

/**
 * Unit tests for stack_options.
 *
 * @package    qtype_stack
 * @copyright 2012 The University of Birmingham.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_options
 */
final class stack_options_test extends qtype_stack_testcase {

    public function test_set_exception_1(): void {

        $opts = new stack_options();
        $this->expectException(stack_exception::class);
        $opts->set_option('nonoption', false);
    }

    public function test_set_exception_2(): void {

        $opts = new stack_options();
        $this->expectException(stack_exception::class);
        $opts->set_option('floats', 0);
    }

    public function test_set_exception_3(): void {

        $opts = new stack_options();
        $this->expectException(stack_exception::class);
        $opts->set_option('floats', null);
    }

    public function test_set_exception_4(): void {

        $opts = new stack_options();
        $this->expectException(stack_exception::class);
        $opts->set_option('display', false);
    }

    public function test_set_exception_5(): void {

        $opts = new stack_options();
        $this->expectException(stack_exception::class);
        $opts->set_option('display', 'latex');
    }
}
