<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Answer test base class.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_AnsTest {
    // Attributes
    /**
     * @var    string
     */
    protected $sAnsKey;

    /**
     * @var    string
     */
    protected $tAnsKey;

    /**
     * @var    array
     */
    protected $preCalculated;

    /**
     * @var    int
     */
    protected $seed;

    /**
     * @var    CasString
     */
    protected $ATOption = NULL;

    /**
     * @var    string
     */
    protected $casFunction = NULL;

    /**
     * @var    bool
     */
    protected $ATResult = NULL;

    /**
     * @var    float
     */
    protected $ATMark;

    /**
     * @var    string
     */
    protected $ATError;

    /**
     * @var    bool
     */
    protected $ATValid;

    /**
     * @var    string
     */
    protected $ATAnsNote;

    /**
     * @var    string
     */
    protected $ATFeedback;


    /**
     * Should the test ops be processed in the CAS. By default no.
     *
     * @var  bool
     * @access protected
     */
    protected $CASProcessTestOps = false;

    // Operations
    /**
     * Constructor
     *
     * @param  string $sAnsKey
     * @param  string $tAnsKey
     * @param  array $preCalculated
     * @param  int $seed
     * @param  CasString $casOption
     */
    public function __construct($sAnsKey, $tAnsKey, $STACK_CAS_Maxima_Preferences, $casOption = NULL) {
        $this->sAnsKey = $sAnsKey;
        $this->tAnsKey = $tAnsKey;
        $this->ATOption = $casOption;
        $this->STACK_CAS_Maxima_Preferences = $STACK_CAS_Maxima_Preferences;
        $this->CASProcessTestOps = false;
    }

    /**
     * Acutally perform the test.
     *
     * @return bool
     */
    public function doAnsTest() {
        return NULL;
    }

    /**
     *
     *
     * @return string
     */
    public function getATErrors() {
        return $this->ATError;
    }

    /**
     *
     *
     * @return float
     */
    public function getATMark() {
        return $this->ATMark;
    }

    /**
     *
     *
     * @return bool
     */
    public function getATValid() {
        return $this->ATValid;
    }

    /**
     *
     *
     * @return string
     */
    public function getATAnsNote() {
        return $this->ATAnsNote;
    }

    /**
     *
     *
     * @return string
     */
    public function getATFeedback() {
        //AT feedback needs to be run through the translator
        if (!empty($this->ATFeedback)) {
            $translated = STACK_Translator::translate("$this->ATFeedback");
        } else {
            $translated = '';
        }
        return $translated;
    }

    /**
     * Returns whether the testops should be processed by the CAS for this AnswerTest
     * Returns true if the Testops should be processed.
     *
     * @return bool
     */
    public function processTestOps() {
        return $this->CASProcessTestOps;
    }
}
