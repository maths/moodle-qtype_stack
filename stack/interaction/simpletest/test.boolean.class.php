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
 * Unit tests for the stack_interaction_boolean class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../controller.class.php');
require_once(dirname(__FILE__) . '/../boolean.class.php');


/**
 * Unit tests for stack_interaction_boolean_test.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_interaction_boolean_test extends UnitTestCase {

    protected function expected_choices() {
        return array(
            stack_interaction_boolean::F => stack_string('false'),
            stack_interaction_boolean::T => stack_string('true'),
            stack_interaction_boolean::NA => stack_string('notanswered'),
        );
    }

    public function test_get_xhtml_not_answered() {
        $el = stack_interaction_controller::make_element('boolean', 'ans1', stack_interaction_boolean::T);
        $this->assert(new ContainsSelectExpectation('ans1', $this->expected_choices(),
                stack_interaction_boolean::NA), $el->get_xhtml(stack_interaction_boolean::NA, false));
    }

    public function test_get_xhtml_true() {
        $el = stack_interaction_controller::make_element('boolean', 'ans2', stack_interaction_boolean::T);
        $this->assert(new ContainsSelectExpectation('ans2', $this->expected_choices(),
                stack_interaction_boolean::T), $el->get_xhtml(stack_interaction_boolean::T, false));
    }

    public function test_get_xhtml_false() {
        $el = stack_interaction_controller::make_element('boolean', 'ans3', stack_interaction_boolean::T);
        $this->assert(new ContainsSelectExpectation('ans3', $this->expected_choices(),
                stack_interaction_boolean::F), $el->get_xhtml(stack_interaction_boolean::F, false));
    }

    public function test_get_xhtml_disabled() {
        $el = stack_interaction_controller::make_element('boolean', 'input', stack_interaction_boolean::T);
        $this->assert(new ContainsSelectExpectation('input', $this->expected_choices(),
                stack_interaction_boolean::NA, false), $el->get_xhtml('', true));
    }

    public function test_validate_student_response_true() {
        $el = stack_interaction_controller::make_element('boolean', 'sans1', 'true');
        $cs = $el->validate_student_response('true');
        $this->assertTrue($cs->get_valid());
    }

    public function test_validate_student_response_false() {
        $el = stack_interaction_controller::make_element('boolean', 'sans1', 'true');
        $cs = $el->validate_student_response('false');
        $this->assertTrue($cs->get_valid());
    }

    public function test_validate_student_response_na() {
        $el = stack_interaction_controller::make_element('boolean', 'sans1', 'true');
        $cs = $el->validate_student_response('');
        $this->assertFalse($cs->get_valid());
    }
}
