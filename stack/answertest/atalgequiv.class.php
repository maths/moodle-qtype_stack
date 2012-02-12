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
 * Algebraic equivalence answer test
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest_AlgEquiv extends STACK_AnsTest {

    /**
     * constant
     * The name of the cas function this answer test uses.
     */
    const CASFUNCTION = 'ATAlgEquiv';
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
        $mconn = new stack_cas_maxima_connector($this->options);
        $result = $mconn->maxima_answer_test($this->sAnsKey, $this->tAnsKey, self::CASFUNCTION);

        $this->ATMark     = $result['result'];
        $this->ATAnsNote  = $result['answernote'];
        $this->ATFeedback = $result['feedback'];
        $this->ATError    = $result['error'];

        if (1==$this->ATMark) {
            return true;
        } else {
            return false;
        }
    }

}

