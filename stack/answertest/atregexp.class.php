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
 * Regular expression answer-test.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest_atregexp extends stack_anstest {

    public function do_test() {
        if ($this->ATOption == null) {
            $this->ATError = 'Missing regular expression in CAS Option field';
            $this->ATFeedback = ' stack_trans("TEST_FAILED");';
            $this->ATAnsNote = 'STACKERROR_OPTION_REGEX';
            $this->ATMark = 0;
            $this->ATValid = false;
            return null;

        } else {
            $this->ATValid = true;
            if (preg_match($this->ATOption, $this->sAnsKey, $pattern)) {
                $this->ATMark = 1;
                $this->ATAnsNote = ' Pattern matched: '.$pattern[0];
                return true;
            } else {
                $this->ATMark = 0;
                return false;
            }
        }
    }

    public function process_atoptions() {
        return false;
    }
}
