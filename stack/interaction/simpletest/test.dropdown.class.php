<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
 * Unit tests for the STACK_Input_DropDownList class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../controller.class.php');


/**
 * Unit tests for STACK_Input_DropDownList.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_DropDownList_test extends UnitTestCase {

    protected function expected_choices($choices) {
        $expected = array('' => stack_string('notanswered'));
        foreach ($choices as $choice) {
            $expected[$choice] = $choice;
        }
        return $expected;
    }

    public function test_getXHTML_not_answered() {
        $choices = array('x', 'y', 'z');
        $el = STACK_Input_Controller::make_element('dropDown', 'ans1', NULL, NULL, NULL, NULL,
                array('ddl_values' => implode(',', $choices)));

        $this->assert(new ContainsSelectExpectation('ans1',
                $this->expected_choices($choices), ''), $el->getXHTML(false));
    }

    public function test_getXHTML_true() {
        $choices = array('x', 'y', 'z');
        $el = STACK_Input_Controller::make_element('dropDown', 'ans2', NULL, NULL, NULL, NULL,
                array('ddl_values' => implode(',', $choices)));
        $el->setDefault('y');
        $this->assert(new ContainsSelectExpectation('ans2',
                $this->expected_choices($choices), 'y'), $el->getXHTML(false));
    }

    public function test_getXHTML_disabled() {
        $choices = array('x > 1', 'x = 1', 'x < 1');
        $el = STACK_Input_Controller::make_element('dropDown', 'tricky', NULL, NULL, NULL, NULL,
                array('ddl_values' => implode(',', $choices)));

        $this->assert(new ContainsSelectExpectation('tricky',
                $this->expected_choices($choices), '', false), $el->getXHTML(true));
    }

    public function test_getXHTML_empty() {
        $el = STACK_Input_Controller::make_element('dropDown', 'oops');
        $this->assertEqual(stack_string('ddl_empty'), $el->getXHTML(false));
    }
}
