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
 * Unit tests for the STACK_Input_TextArea class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../controller.class.php');
require_once(dirname(__FILE__) . '/../textarea.class.php');


/**
 * Unit tests for STACK_Input_TextArea.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_TextArea_test extends UnitTestCase {

    public function test_tokenize() {
        $el = new testable_STACK_Input_TextArea('notused');

        $this->assertEqual(array(), $el->tokenize_list(''));

        $this->assertEqual(array(), $el->tokenize_list('[]'));

        $this->assertEqual(array('1'), $el->tokenize_list('[1]'));

        $this->assertEqual(array('1', '2'), $el->tokenize_list('[1,2]'));

        $this->assertEqual(array('1','x+y'), $el->tokenize_list('[1,x+y]'));

        $this->assertEqual(array('[1,2]'), $el->tokenize_list('[[1,2]]'));

        $this->assertEqual(array(1, '1/sum([1,3])', 'matrix([1],[2])'), $el->tokenize_list('[1,1/sum([1,3]),matrix([1],[2])]'));
    }

    public function test_getXHTML_blank() {
        $el = STACK_Input_Controller::make_element('textArea', 'ans1', 10, NULL, NULL, 2);
        $this->assertEqual('<textarea name="ans1" rows="2" cols="10"></textarea>',
                $el->getXHTML(false));
    }

    public function test_getXHTML_pre_filled() {
        $el = STACK_Input_Controller::make_element('textArea', 'test');
        $el->setDefault('[1,1/sum([1,3]),matrix([1],[2])]');
        $this->assertEqual('<textarea name="test" rows="4" cols="20">' .
                "1\n1/sum([1,3])\nmatrix([1],[2])\n</textarea>",
                $el->getXHTML(false));
    }

    public function test_getXHTML_disabled() {
        $el = STACK_Input_Controller::make_element('textArea', 'input');
        $this->assertEqual('<textarea name="input" rows="1" cols="5" readonly="readonly"></textarea>',
                $el->getXHTML(true));
    }
}


/**
 * Test helper class that exploses some protected methods.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_STACK_Input_TextArea extends STACK_Input_TextArea {
    public function tokenize_list($in) {
        return parent::tokenize_list($in);
    }
}
