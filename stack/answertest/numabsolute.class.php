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
 * Numerical test - absolute difference
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest_numabsolute extends stack_anstest {

    /**
     *
     *
     * @return bool
     * @access public
     */
    public function do_test() {

        if (trim($this->atoption) == '') {
            $atest_ops = '0.05';
        } else {
            $atest_ops = $this->atoption;
        }

        $commands = array($this->sanskey, $this->tanskey, $atest_ops);
        foreach ($commands as $com) {
            $cs = new stack_cas_casstring($com);
            if (!$cs->get_valid('t', true, false)) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $cs->get_errors()));
                $this->atansnote    = 'STACKERROR_OPTION';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
        }

        $cascommands = array();
        $cascommands[] = "caschat0:ev(float($this->sanskey),simp)";
        $cascommands[] = "caschat1:ev(float($this->tanskey),simp)";
        $cascommands[] = "caschat2:ev({$atest_ops},simp)";
        $cascommands[] = "caschat3:ev(abs(float({$this->sanskey}-{$this->tanskey})),simp)";
        $cascommands[] = "caschat4:ev(abs(float({$atest_ops})),simp)";

        $cts = array();
        foreach ($cascommands as $com) {
            $cs    = new stack_cas_casstring($com);
            $cs->validate('t', true, false);
            $cts[] = $cs;
        }
        $session = new stack_cas_session($cts, null, 0);
        $session->instantiate();

        if (''!=$session->get_errors_key('caschat0')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('caschat0')));
            $this->atansnote    = 'NumAbsolute_STACKERROR_SAns';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if (''!=$session->get_errors_key('caschat1')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('caschat1')));
            $this->atansnote    = 'NumAbsolute_STACKERROR_TAns';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if (''!=$session->get_errors_key('caschat2')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $session->get_errors_key('caschat2')));
            $this->atansnote    = 'NumAbsolute_STACKERROR_Options';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        $flsa = $session->get_value_key('caschat3');
        $flta = $session->get_value_key('caschat4');
        $this->atansnote = " |sa-ta|={$flsa}>={$flta}=tol";

        $this->atvalid = true;
        if ($flsa <= $flta) {
            $this->atmark = 1;
            return true;
        } else {
            $this->atmark = 0;
            return false;
        }

    }

    public function process_atoptions() {
        return true;
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
        $cs = new stack_cas_casstring($opt);
        return array($cs->get_valid('t'), $cs->get_errors());
    }
}

