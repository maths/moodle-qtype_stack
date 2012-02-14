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
 * General answer test which connects to the CAS - prevents duplicate code.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest_General_CAS extends STACK_AnsTest {

    /**
     * 
     * The name of the cas function this answer test uses.
     */
    private $casfunction;
    /**
     * 
     * Are options required.
     */
    private $requirecasoptions;
    /**
     * 
     * If this variable is set to true or false we override the simplification options in the CAS variables.
     */
    private $simp;    /**
     *
     *
     * @param  string $sans
     * @param  string $tans
     * @param  string $casoption
     * @access public
     */
    public function __construct($sans, $tans, $casfunction, $requirecasoptions=false, $casoption = null, $options=null, $simp=null) {
        parent::__construct($sans, $tans, $options, $casoption);
        
        if (!is_bool($requirecasoptions)) {
            throw new Exception('STACK_AnsTest_General_CAS: requirecasoptions, must be Boolean.');
        }
        
        if (!(null===$options || is_a($options, 'stack_options'))) {
            throw new Exception('STACK_AnsTest_General_CAS: options must be stack_options or null.');
        }
        
        $this->casfunction       = $casfunction;
        $this->requirecasoptions = $requirecasoptions;
        $this->simp              = $simp;
    }

    /**
     *
     *
     * @return bool
     * @access public
     */
    public function doAnsTest() {
        if ($this->requirecasoptions) {
            if (null == $this->ATOption or '' == $this->ATOption) {
                $this->ATError      = 'TEST_FAILED';
                $this->ATFeedback   =  stack_string("TEST_FAILED").stack_string("AT_MissingOptions");
                $this->ATAnsNote    = 'STACKERROR_OPTION';
                $this->ATMark       = 0;
                return null;
            } else {
                $ct  = new stack_cas_casstring($this->ATOption, 't', true, true); //validate with teacher privileges, strict syntax & no automatically adding stars.

                if (!$ct->get_valid()) {
                    $this->ATError      = 'TEST_FAILED';
                    $this->ATFeedback   = stack_string("TEST_FAILED");
                    $errors = $ct->get_errors();
                    $this->ATFeedback  .= stack_string('AT_InvalidOptions', array("errors" => $errors));
                    $this->ATAnsNote    = 'STACKERROR_OPTION';
                    $this->ATMark       = 0;
                    return null;
                }
            }
            $atopt = $this->ATOption;
            $ta   = "[$this->tAnsKey,$atopt]";
        } else {
            $ta = $this->tAnsKey;
        }

        // Sort out options
        if (null === $this->options) {
            $this->options = new stack_options();
        }
        if (!(null===$this->simp)) {
            $this->options->set_option('simplify', $this->simp);
        }
        
        $mconn = new stack_cas_maxima_connector($this->options);
        $result = $mconn->maxima_answer_test($this->sAnsKey, $ta, $this->casfunction);

        $this->ATError    = $result['error'];
        $this->ATAnsNote  = $result['answernote'];
        $this->ATMark     = $result['result'];
        $this->ATFeedback = $result['feedback'];
        
        if (1==$this->ATMark) {
            return true;
        } else {
            return false;
        }
    }
}

