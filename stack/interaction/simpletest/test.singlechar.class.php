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
 * Unit tests for the stack_interaction_singlechar class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../controller.class.php');


/**
 * Unit tests for stack_interaction_singlechar.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_interaction_singlechar_test extends UnitTestCase {

    public function test_get_xhtml_blank() {
        $el = stack_interaction_controller::make_element('singleChar', 'ans1', null);
        $this->assertEqual('<input type="text" name="question__ans1" size="1" maxlength="1" value="" />',
                $el->get_xhtml('', 'question__ans1', false));
    }

    public function test_get_xhtml_pre_filled() {
        $el = stack_interaction_controller::make_element('singleChar', 'test', null);
        $this->assertEqual('<input type="text" name="question__ans1" size="1" maxlength="1" value="Y" />',
                $el->get_xhtml('Y', 'question__ans1', false));
    }

    public function test_get_xhtml_disabled() {
        $el = stack_interaction_controller::make_element('singleChar', 'input', null);
        $this->assertEqual('<input type="text" name="question__stack1" size="1" maxlength="1" value="a" readonly="readonly" />',
                $el->get_xhtml('a', 'question__stack1', true));
    }
}
