<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

/**
 * Regular expression answer-test.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest_atregexp extends stack_anstest {

    public function do_test() {
        if ($this->atoption == null) {
            $this->aterror = 'TEST_FAILED';
            $this->atfeedback = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atansnote = 'ATRegEx_STACKERROR_Option.';
            $this->atmark = 0;
            $this->atvalid = false;
            return null;

        } else {
            $this->atvalid = true;
            if (preg_match($this->atoption, $this->sanskey, $pattern)) {
                $this->atmark = 1;
                $this->atansnote = ' Pattern matched: '.$pattern[0].'.';
                return true;
            } else {
                $this->atmark = 0;
                return false;
            }
        }
    }

    public function process_atoptions() {
        return false;
    }

    public function required_atoptions() {
        return true;
    }

    public function validate_atoptions($opt) {
        return array(true, '');
    }

    protected function get_casfunction() {
        return 'ATRegEx';
    }
}
