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
 * Unit tests for the stack_textarea_input class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../factory.class.php');
require_once(dirname(__FILE__) . '/../textarea.class.php');


/**
 * Unit tests for stack_textarea_input.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_textarea_input_test extends UnitTestCase {

    public function test_tokenize() {
        $el = new testable_stack_textarea_input('notused', null);

        $this->assertEqual(array(), $el->tokenize_list(''));

        $this->assertEqual(array(), $el->tokenize_list('[]'));

        $this->assertEqual(array('1'), $el->tokenize_list('[1]'));

        $this->assertEqual(array('1', '2'), $el->tokenize_list('[1,2]'));

        $this->assertEqual(array('1', 'x+y'), $el->tokenize_list('[1,x+y]'));

        $this->assertEqual(array('[1,2]'), $el->tokenize_list('[[1,2]]'));

        $this->assertEqual(array(1, '1/sum([1,3])', 'matrix([1],[2])'), $el->tokenize_list('[1,1/sum([1,3]),matrix([1],[2])]'));
    }

    public function test_get_xhtml_blank() {
        $el = stack_input_factory::make('textArea', 'ans1', null);
        $this->assertEqual('<textarea name="st_ans1" rows="5" cols="20"></textarea>',
                $el->get_xhtml('', 'st_ans1', false));
    }

    public function test_get_xhtml_pre_filled() {
        $el = stack_input_factory::make('textArea', 'test', null);
        $this->assertEqual('<textarea name="st_ans1" rows="5" cols="20">' .
                "1\n1/sum([1,3])\nmatrix([1],[2])\n</textarea>",
                $el->get_xhtml('[1,1/sum([1,3]),matrix([1],[2])]', 'st_ans1', false));
    }

    public function test_get_xhtml_disabled() {
        $el = stack_input_factory::make('textArea', 'input', null);
        $this->assertEqual('<textarea name="st_ans1" rows="5" cols="20" readonly="readonly"></textarea>',
                $el->get_xhtml('', 'st_ans1', true));
    }
}


/**
 * Test helper class that exploses some protected methods.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_stack_textarea_input extends stack_textarea_input {
    public function tokenize_list($in) {
        return parent::tokenize_list($in);
    }
}
