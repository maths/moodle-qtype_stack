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

    protected $atname = 'String';

    public function do_test() {
        $this->atvalid = true;
        $sa = '';
        if ($this->sanskey->get_valid()) {
            $sa = $this->sanskey->get_inputform(true, 1);
            if ($this->sanskey->is_correctly_evaluated()) {
                $sa = $this->sanskey->get_value();
            }
        } else {
            $this->atansnote    = $this->casfunction.'TEST_FAILED:Invalid SA.';
        }
        $ta = '';
        if ($this->tanskey->get_valid()) {
            $ta = $this->tanskey->get_inputform(true, 1);
            if ($this->tanskey->is_correctly_evaluated()) {
                $ta = $this->tanskey->get_value();
            }
        } else {
            $this->atansnote    = $this->casfunction.'TEST_FAILED:Invalid TA.';
        }

        if (trim($sa) == trim($ta)) {
            $this->atmark = 1;
            return true;

        } else {
            $this->atmark = 0;
            return false;
        }
    }

    public function validate_atoptions($opt) {
        return array(true, '');
    }

    protected function get_casfunction() {
        return 'ATString';
    }
}
