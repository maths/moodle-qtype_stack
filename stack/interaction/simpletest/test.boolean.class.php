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
 * Unit tests for the STACK_Input_Boolean class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../controller.class.php');


/**
 * Unit tests for STACK_Input_Boolean_test.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_Boolean_test extends UnitTestCase {

    protected function expected_choices() {
        return array(
            STACK_Input_Boolean::F => stack_string('false'),
            STACK_Input_Boolean::T => stack_string('true'),
            STACK_Input_Boolean::NA => stack_string('notanswered'),
        );
    }

    public function test_getXHTML_not_answered() {
        $el = STACK_Input_Controller::make_element('boolean', 'ans1');
        $this->assert(new ContainsSelectExpectation('ans1', $this->expected_choices(),
                STACK_Input_Boolean::NA), $el->getXHTML(false));
    }

    public function test_getXHTML_true() {
        $el = STACK_Input_Controller::make_element('boolean', 'ans2');
        $el->setDefault(STACK_Input_Boolean::T);
        $this->assert(new ContainsSelectExpectation('ans2', $this->expected_choices(),
                STACK_Input_Boolean::T), $el->getXHTML(false));
    }

    public function test_getXHTML_false() {
        $el = STACK_Input_Controller::make_element('boolean', 'ans3');
        $el->setDefault(STACK_Input_Boolean::F);
        $this->assert(new ContainsSelectExpectation('ans3', $this->expected_choices(),
                STACK_Input_Boolean::F), $el->getXHTML(false));
    }

    public function test_getXHTML_disabled() {
        $el = STACK_Input_Controller::make_element('boolean', 'input');
        $this->assert(new ContainsSelectExpectation('input', $this->expected_choices(),
                STACK_Input_Boolean::NA, false), $el->getXHTML(true));
    }
}
