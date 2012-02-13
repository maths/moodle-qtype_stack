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
 * Unit tests for STACK_AnsTest_General_CAS.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../anstest.class.php');
require_once(dirname(__FILE__) . '/../at_general_cas.class.php');


/**
 * Unit tests for STACK_AnsTest_General_CAS.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest_General_CAS_test extends UnitTestCase {

    public function test_is_true_for_equivalent_expressions_diff() {
        $at = new STACK_AnsTest_General_CAS('2*x', '2*x', 'ATDiff', true, 'x', null);
        $this->assertTrue($at->doAnsTest());
        $this->assertEqual(1, $at->getATMark());
    }

    public function test_is_false_for_equivalent_expressions_diff() {
        $at = new STACK_AnsTest_General_CAS('x^3/3', '2*x', 'ATDiff', true, 'x', null);
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_false_for_missing_option_diff() {
        $at = new STACK_AnsTest_General_CAS('(x+1)^2', '2*x', 'ATDiff', true, '', null);
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_true_for_equal_expressions_algequiv() {
        $at = new STACK_AnsTest_General_CAS('1', '1', 'ATAlgEquiv');
        $this->assertTrue($at->doAnsTest());
        $this->assertEqual(1, $at->getATMark());
    }
    
    public function test_is_false_for_unequal_expressions_algequiv() {
        $at = new STACK_AnsTest_General_CAS('x^2+2*x-1', '(x+1)^2', 'ATAlgEquiv');
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }
    
    public function test_is_false_for_expressions_with_different_type_algequiv() {
        $at = new STACK_AnsTest_General_CAS('(x+1)^2', '[a,b,c]', 'ATAlgEquiv');
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_true_for_equal_expressions_comass() {
        $at = new STACK_AnsTest_General_CAS('x+y', 'x+y', 'ATEqual_com_ass', false, null, null, 0);
        $this->assertTrue($at->doAnsTest());
        $this->assertEqual(1, $at->getATMark());
    }

    public function test_is_false_for_unequal_expressions_comass() {
        $at = new STACK_AnsTest_General_CAS('x+x', '2*x', 'ATEqual_com_ass', false, null, null, 0);
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_false_for_expressions_with_different_type_comass() {
        $at = new STACK_AnsTest_General_CAS('(x+1)^2', '[a,b,c]', 'ATEqual_com_ass', false, null, null, 0);
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_true_for_equal_expressions_caseq() {
        $at = new STACK_AnsTest_General_CAS('x+y', 'x+y', 'ATCASEqual', false);
        $this->assertTrue($at->doAnsTest());
        $this->assertEqual(1, $at->getATMark());
    }

    public function test_is_false_for_unequal_expressions_caseq() {
        $at = new STACK_AnsTest_General_CAS('(1-x)^2', '(x-1)^2', 'ATCASEqual', false);
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_false_for_expressions_with_different_type_caseq() {
        $at = new STACK_AnsTest_General_CAS('(x+1)^2', '[a,b,c]', 'ATCASEqual', false);
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }

    public function test_is_true_sametype() {
        $at = new STACK_AnsTest_General_CAS('x+1', 'x^3+x', 'ATSameType');
        $this->assertTrue($at->doAnsTest());
        $this->assertEqual(1, $at->getATMark());
    }
    
    public function test_is_false_sametype() {
        $at = new STACK_AnsTest_General_CAS('x^2+2*x-1', '{(x+1)^2}', 'ATSameType');
        $this->assertFalse($at->doAnsTest());
        $this->assertEqual(0, $at->getATMark());
    }
}
