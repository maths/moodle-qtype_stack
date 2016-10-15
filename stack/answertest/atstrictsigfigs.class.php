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


/**
 * Answer test which establishes strict rules of significant figures.
 *
 * @copyright  2016 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../utils.class.php');

class stack_anstest_atstrictsigfigs extends stack_anstest {

    public function do_test() {
        $this->atmark = 0;
        $anotes = array();

        $commands = array((string) $this->atoption, $this->sanskey);
        foreach ($commands as $com) {
            $cs = new stack_cas_casstring($com);
            if (!$cs->get_valid('t', true, 0)) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $cs->get_errors()));
                $this->atansnote    = 'ATStrictSigFigs_STACKERROR_Option. ';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
        }

        // Check the teacher's answer is a positive integer.
        if (!ctype_digit($this->atoption) || $this->atoption <= 0) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $cs->get_errors()));
            $this->atansnote    = 'ATStrictSigFigs_STACKERROR_Option. ';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Actually perform the test.
        $r = stack_utils::decimal_digits($this->sanskey);

        if ($r['lowerbound'] == $this->atoption) {
            $this->atmark = 1;
        } else if ($r['lowerbound'] <= $this->atoption && $this->atoption <= $r['upperbound']) {
            $this->atansnote    = 'ATStrictSigFigs_WithinRange. ';
        }

        return false;
    }

    public function process_atoptions() {
        return false;
    }

    public function required_atoptions() {
        return true;
    }

    /**
     * Validates the options, when needed.
     *
     * @return (bool, errors)
     * @access public
     */
    public function validate_atoptions($opt) {
        return array(true, '');
    }
}
