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
 * Unit tests for STACK_InteractionElement.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../interactionelement.class.php');
require_once(dirname(__FILE__) . '/../utils.class.php');


/**
 * Unit tests for STACK_InteractionElement.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_InteractionElement_test extends UnitTestCase {

    public function test_constructor() {
        $el = new STACK_InteractionElement('label', 'raw answer',
                STACK_InteractionElement::STATE_NEW, 'cas answer');
        $this->assertEqual('label', $el->label);
        $this->assertEqual('raw answer', $el->getRawAns());
        $this->assertEqual(STACK_InteractionElement::STATE_NEW, $el->getStatus());
        $this->assertEqual($el->casValue, 'cas answer');
        $this->assertNull($el->displayValue);
    }

    public function test_constructor_defaults() {
        $el = new STACK_InteractionElement('label', 'raw answer');
        $this->assertEqual(STACK_InteractionElement::STATE_UNDEFINED, $el->getStatus());
        $this->assertNull($el->casValue, 'cas answer');
    }

    public function test_constructor_get_raw_ans() {
        $el = new STACK_InteractionElement('label', 'raw answer;');
        $this->assertEqual('raw answer', $el->getRawAns());

        $el = new STACK_InteractionElement('label', 'raw answer:');
        $this->assertEqual('raw answer', $el->getRawAns());

        $el = new STACK_InteractionElement('label', 'raw answer$');
        $this->assertEqual('raw answer', $el->getRawAns());
    }

    public function test_allowed_state_transitions() {
        $el = new STACK_InteractionElement('label', 'raw answer');
        $this->assertEqual(STACK_InteractionElement::STATE_UNDEFINED, $el->getStatus());

        $this->assertTrue($el->setStatus(STACK_InteractionElement::STATE_NEW));
        $this->assertEqual(STACK_InteractionElement::STATE_NEW, $el->getStatus());

        $this->assertFalse($el->setStatus(STACK_InteractionElement::STATE_SCORED));
        $this->assertEqual(STACK_InteractionElement::STATE_NEW, $el->getStatus());

        $this->assertTrue($el->setStatus(STACK_InteractionElement::STATE_INVALID));
        $this->assertEqual(STACK_InteractionElement::STATE_INVALID, $el->getStatus());
    }
}
