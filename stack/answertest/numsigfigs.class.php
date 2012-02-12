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
 * Tests number of significant figures
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest_NumSigFigs extends STACK_AnsTest {

    /**
     * constant
     * The name of the cas function this answer test uses.
     */
    const CASFUNCTION = 'ATNumSigFigs';
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

        $atopt = $this->ATOption;
        // Set a default option
        if ('' == trim($atopt)) {
            $atopt = '3';
        }

        if (null === $this->ATOption) {
                //$this->errorLog->addError('Missing variable in CAS Option field');
                $this->ATError      = 'TEST_FAILED';
                $this->ATFeedback   = ' STACK_Legacy::trans("TEST_FAILED"); STACK_Legacy::trans("AT_MissingOptions");';
                $this->ATAnsNote    = 'STACKERROR_OPTION';
                $this->ATMark       = 0;
                return null;
        } else {
            $ct  = new stack_cas_casstring($atopt, 't', true, true); //validate with teacher privileges, strict syntax & no automatically adding stars.

            if ($ct->get_valid()) {
                $atopt = $this->ATOption;
                $ta   = "[$this->tAnsKey,$atopt]";

                $mconn = new stack_cas_maxima_connector($this->options);
                $result = $mconn->maxima_answer_test($this->sAnsKey, $ta, self::CASFUNCTION);

                $this->ATMark     = $result['result'];
                $this->ATAnsNote  = $result['answernote'];
                $this->ATFeedback = $result['feedback'];
                $this->ATError    = $result['error'];

                if (1==$this->ATMark) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->ATError      = 'TEST_FAILED';
                $errors = $ct->get_errors();
                $this->ATFeedback   = ' STACK_Legacy::trans("TEST_FAILED"); ';
                $this->ATFeedback  .= ' STACK_Legacy::trans("AT_InvalidOptions","'.$errors.' ."); ';
                $this->ATAnsNote    = 'STACKERROR_OPTION';
                return null;
            }
        }
    }
}

