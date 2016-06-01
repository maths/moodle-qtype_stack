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
 * Answer test base class.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest {

    /**
     * @var    string
     */
    protected $sanskey;

    /**
     * @var    string
     */
    protected $tanskey;

    /**
     * @var    string
     */
    protected $options;

    /**
     * @var    CasString
     */
    protected $atoption = null;

    /**
     * @var    float
     */
    protected $atmark;

    /**
     * @var    string
     */
    protected $aterror;

    /**
     * @var    bool
     */
    protected $atvalid;

    /**
     * @var    string
     */
    protected $atansnote;

    /**
     * @var    string
     */
    protected $atfeedback;

    /**
     * $var string.  Copies the debug info, e.g. from the CAS session.
     */
    protected $debuginfo;

    /**
     * Constructor
     *
     * @param  string $sanskey
     * @param  string $tanskey
     */
    public function __construct($sans, $tans, $options = null, $casoption = null) {
        $this->sanskey  = $sans;
        $this->tanskey  = $tans;
        if ($options != null) {
            $this->options  = clone $options;
        } else {
            $this->options = null;
        }
        $this->atoption = $casoption;
    }

    /**
     * Acutally perform the test.
     *
     * @return bool
     */
    public function do_test() {
        return null;
    }

    /**
     *
     *
     * @return string
     */
    public function get_at_errors() {
        return $this->aterror;
    }

    /**
     *
     *
     * @return float
     */
    public function get_at_mark() {
        return $this->atmark;
    }

    /**
     *
     *
     * @return bool
     */
    public function get_at_valid() {
        return $this->atvalid;
    }

    /**
     *
     *
     * @return string
     */
    public function get_at_answernote() {
        return $this->atansnote;
    }

    /**
     *
     *
     * @return string
     */
    public function get_at_feedback() {
        return $this->atfeedback;
    }

    /**
     * Returns whether the testops should be processed by the CAS for this AnswerTest
     *
     * @return bool
     */
    public function process_atoptions() {
        return false;
    }

    /**
     * Returns whether the testops are required for this test.
     *
     * @return bool
     * @access public
     */
    public function required_atoptions() {
        return false;
    }

    /**
     * Returns some sensible debug information for testing questions.
     *
     * @return string
     * @access public
     */
    public function get_debuginfo() {
        return $this->debuginfo;
    }
}
