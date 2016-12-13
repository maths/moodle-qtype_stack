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
 * String answer test
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest_atstring extends stack_anstest {

    public function do_test() {
        $this->atvalid = true;
        if (trim($this->sanskey) == trim($this->tanskey)) {
            $this->atmark = 1;
            return true;

        } else {
            $this->atmark = 0;
            return false;
        }
    }

    public function process_atoptions() {
        return false;
    }

    public function validate_atoptions($opt) {
        return array(true, '');
    }

    protected function get_casfunction() {
        return 'ATString';
    }
}
