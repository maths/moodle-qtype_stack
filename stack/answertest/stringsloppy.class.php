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
 * Loose string comparison answer test.
 *
 * This test ignores the case of letters, and whitespace.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest_StringSloppy extends STACK_AnsTest {

    public function do_test() {
        $sa = str_replace(' ', '', strtolower(trim($this->sAnsKey)));
        $sa = str_replace("\n", '', $sa);
        $sa = str_replace("\t", '', $sa);

        $ta = str_replace(' ', '', strtolower(trim($this->tAnsKey)));
        $ta = str_replace("\n", '', $ta);
        $ta = str_replace("\t", '', $ta);

        if ($sa == $ta) {
            $this->ATMark = 1;
            return true;
        } else {
            $this->ATMark = 0;
            return false;
        }
    }
}
 