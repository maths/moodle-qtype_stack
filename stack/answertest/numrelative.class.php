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
 * Numerical test - relative difference
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest_NumRelative extends STACK_AnsTest {

    /**
     *
     *
     * @param  string $sans
     * @param  string $tans
     * @param  string $casoption
     * @access public
     */
    public function __construct($sans, $tans, $options=null, $casoption = null) {
        parent::__construct($sans, $tans, $options, $casoption);
    }

    /**
     *
     *
     * @return bool
     * @access public
     */
    public function doAnsTest() {

        if (trim($this->ATOption) == '') {
            $atest_ops = '0.05';
        } else {
            $atest_ops = $this->ATOption;
        }

        $commands = array($this->sAnsKey, $this->tAnsKey, $atest_ops);
        foreach ($commands as $com) {
            $cs = new stack_cas_casstring($com, 't', true, false);
            if (!$cs->get_valid()) {
                $this->ATError      = 'TEST_FAILED';
                $this->ATFeedback   = ' stack_trans("TEST_FAILED"); ';
                $this->ATAnsNote    = 'TEST_FAILED';
                return null;
            }
        }

        $cascommands = array();
        $cascommands[] = "caschat0:ev(float($this->sAnsKey),simp)";
        $cascommands[] = "caschat1:ev(float($this->tAnsKey),simp)";
        $cascommands[] = "caschat2:ev({$atest_ops},simp)";
        $cascommands[] = "caschat3:ev(abs(float({$this->sAnsKey}-{$this->tAnsKey})),simp)";
        $cascommands[] = "caschat4:ev(abs(float({$this->tAnsKey}*{$atest_ops})),simp)";

        $cts = array();
        foreach ($cascommands as $com) {
            $cts[] = new stack_cas_casstring($com, 't', true, false);
        }
        $session = new stack_cas_session($cts, null, null, 't', true, false);
        $session->instantiate();

        if (''!=$session->get_errors_key('caschat0')) {
            $this->ATError      = 'TEST_FAILED';
            $this->ATFeedback   = ' stack_trans("TEST_FAILED"); ';
            $this->ATAnsNote    = 'NumRelative_STACKERROR_SAns';
            return null;
        }

        if (''!=$session->get_errors_key('caschat1')) {
            $this->ATError      = 'TEST_FAILED';
            $this->ATFeedback   = ' stack_trans("TEST_FAILED"); ';
            $this->ATAnsNote    = 'NumRelative_STACKERROR_TAns';
            return null;
        }

        if (''!=$session->get_errors_key('caschat2')) {
            $this->ATError      = 'TEST_FAILED';
            $this->ATFeedback   = ' stack_trans("TEST_FAILED"); ';
            $this->ATFeedback  .= ' stack_trans("AT_InvalidOptions","'.$session->get_errors_key('caschat2').'"); ';
            $this->ATAnsNote    = 'NumRelative_STACKERROR_Options';
            return null;
        }

        $flsa = $session->get_value_key('caschat3');
        $flta = $session->get_value_key('caschat4');
        $this->ATAnsNote = " |sa-ta|={$flsa}<={$flta}=tol*ta";
        
        if ($flsa <= $flta) {
            $this->ATMark = 1;
            return true;
        } else {
            $this->ATMark = 0;
            return false;
        }

    }

}

