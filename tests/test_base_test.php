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
 * Unit tests for test_base.
 *
 * @copyright  2016 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/test_base.php');

/**
 * Unit tests for test_base.
 *
 * @copyright  2016 The Open Unviersity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class qtype_stack_test_base_testcase extends qtype_stack_testcase {

    public function test_prepare_actual_maths() {
        $this->assertEquals('frog', qtype_stack_testcase::prepare_actual_maths('frog'));
        $this->assertEquals('frog', qtype_stack_testcase::prepare_actual_maths(
                '<span class="nolink">frog</span>'));
        $this->assertEquals('frog', qtype_stack_testcase::prepare_actual_maths(
                '<span class="filter_mathjaxloader_equation">frog</span>'));
        $this->assertEquals('frog', qtype_stack_testcase::prepare_actual_maths(
                '<span class="filter_mathjaxloader_equation"><span class="nolink">frog</span></span>'));
        $this->assertEquals("\n\nfrog\n\n", qtype_stack_testcase::prepare_actual_maths(
                "<span class=\"filter_mathjaxloader_equation\">\n<span class=\"nolink\">\nfrog\n</span>\n</span>"));
    }
}
