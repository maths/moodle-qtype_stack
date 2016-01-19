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
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');

/**
 * Unit tests for stack_dropdown_input.
 *
 * @copyright  2015 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */

class stack_dropdown_input_exception_test extends basic_testcase {

    protected function make_dropdown($parameters = array()) {
        $el = stack_input_factory::make('dropdown', 'ans1', $this->make_ta(), $parameters);
        return $el;
    }

    protected function make_ta() {
        return '[[x+1,true],[x+2,false],[sin(pi*n),false]]';
    }

    public function test_validate_student_response_error() {
        $this->setExpectedException('stack_exception');
        $options = new stack_options();
        $el = $this->make_dropdown();
        $state = $el->validate_student_response(array('ans1' => '4'), $options, '1', null);
    }

    public function test_type_option() {
        $this->setExpectedException('stack_exception');
        $el = $this->make_dropdown(array('options' => 'WHOKNOWS'));
        $el->adapt_to_model_answer($this->make_ta());
    }
}