<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
class STACK_AnsTest_NumAbsolute extends STACK_AnsTest {

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

        if(trim($this->ATOption) == '') {
            $atest_ops = '0.05';
        } else {
            $atest_ops = $this->ATOption;
        }

        $commands = array($this->sAnsKey, $this->tAnsKey, $atest_ops);
        foreach ($commands as $com) {
            $cs = new STACK_CAS_CasString($com, 't', true, false);
            if (!$cs->Get_valid()) {
                $this->ATError      = 'TEST_FAILED';
                $this->ATFeedback   = ' STACK_Legacy::trans("TEST_FAILED"); ';
                $this->ATAnsNote    = 'TEST_FAILED';
                return null;
            }
        }

        $casCommands = array();
        $casCommands[] = "caschat0:ev(float($this->sAnsKey),simp)";
        $casCommands[] = "caschat1:ev(float($this->tAnsKey),simp)";
        $casCommands[] = "caschat2:ev({$atest_ops},simp)";
        $casCommands[] = "caschat3:ev(abs(float({$this->sAnsKey}-{$this->tAnsKey})),simp)";
        $casCommands[] = "caschat4:ev(abs(float({$atest_ops})),simp)";

        $cts = array();
        foreach ($casCommands as $com)
        {
            $cts[] = new STACK_CAS_CasString($com, 't', true, false);
        }
        $session = new STACK_CAS_CasSession($cts, null, null, 't', true, false);
        $session -> instantiate();


        if (''!=$session->Get_errors_key('caschat0')) {
            $this->ATError      = 'TEST_FAILED';
            $this->ATFeedback   = ' STACK_Legacy::trans("TEST_FAILED"); ';
            $this->ATAnsNote    = 'NumAbsolute_STACKERROR_SAns';
            return null;
        }

        if (''!=$session->Get_errors_key('caschat1')) {
            $this->ATError      = 'TEST_FAILED';
            $this->ATFeedback   = ' STACK_Legacy::trans("TEST_FAILED"); ';
            $this->ATAnsNote    = 'NumAbsolute_STACKERROR_TAns';
            return null;
        }

        if (''!=$session->Get_errors_key('caschat2')) {
            $this->ATError      = 'TEST_FAILED';
            $this->ATFeedback   = ' STACK_Legacy::trans("TEST_FAILED"); ';
            $this->ATFeedback  .= ' STACK_Legacy::trans("AT_InvalidOptions","'.$session->Get_errors_key('caschat2').'"); ';
            $this->ATAnsNote    = 'NumAbsolute_STACKERROR_Options';
            return null;
        }

        $flsa = $session->Get_value_key('caschat3');
        $flta = $session->Get_value_key('caschat4');
        $this->ATAnsNote = " |sa-ta|={$flsa}<={$flta}=tol";

        if($flsa <= $flta) {
            $this->ATMark = 1;
            return true;
        } else {
            $this->ATMark = 0;
            return false;
        }
    
    }

}

