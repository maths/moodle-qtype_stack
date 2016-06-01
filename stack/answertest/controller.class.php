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
 * Answer test controller class.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/anstest.class.php');
require_once(__DIR__ . '/at_general_cas.class.php');
require_once(__DIR__ . '/../cas/connector.class.php');
require_once(__DIR__ . '/../cas/casstring.class.php');
require_once(__DIR__ . '/../cas/cassession.class.php');

class stack_ans_test_controller {
    protected static $types = array(
              'AlgEquiv'     => 'stackOptions_AnsTest_values_AlgEquiv',
              'EqualComAss'  => 'stackOptions_AnsTest_values_EqualComAss',
              'CasEqual'     => 'stackOptions_AnsTest_values_CasEqual',
              'SameType'     => 'stackOptions_AnsTest_values_SameType',
              'SubstEquiv'   => 'stackOptions_AnsTest_values_SubstEquiv',
              'SysEquiv'     => 'stackOptions_AnsTest_values_SysEquiv',
              'Expanded'     => 'stackOptions_AnsTest_values_Expanded',
              'FacForm'      => 'stackOptions_AnsTest_values_FacForm',
              'SingleFrac'   => 'stackOptions_AnsTest_values_SingleFrac',
              'PartFrac'     => 'stackOptions_AnsTest_values_PartFrac',
              'CompSquare'   => 'stackOptions_AnsTest_values_CompSquare',
              'GT'           => 'stackOptions_AnsTest_values_GT',
              'GTE'          => 'stackOptions_AnsTest_values_GTE',
              'NumAbsolute'  => 'stackOptions_AnsTest_values_NumAbsolute',
              'NumRelative'  => 'stackOptions_AnsTest_values_NumRelative',
              'NumSigFigs'   => 'stackOptions_AnsTest_values_NumSigFigs',
              'NumDecPlaces' => 'stackOptions_AnsTest_values_NumDecPlaces',
              'Units'        => 'stackOptions_AnsTest_values_Units',
              'UnitsStrict'  => 'stackOptions_AnsTest_values_UnitsStrict',
              'LowestTerms'  => 'stackOptions_AnsTest_values_LowestTerms',
              'Diff'         => 'stackOptions_AnsTest_values_Diff',
              'Int'          => 'stackOptions_AnsTest_values_Int',
              'String'       => 'stackOptions_AnsTest_values_String',
              'StringSloppy' => 'stackOptions_AnsTest_values_StringSloppy',
              'RegExp'       => 'stackOptions_AnsTest_values_RegExp',
              );

    /**
     * The answertest object that the functions call
     * @var string
     * @access private
     */
    private $at;

    // Operations.

    /**
     *
     *
     * @param  string $AnsTest
     * @param  string $sans A CAS string assumed to represent the student's answer.
     * @param  string $tans A CAS string assumed to represent the tecaher's answer.
     * @param  object $options
     * @param  CasString $casoption
     * @access public
     */
    public function __construct($anstest = null, $sans = null, $tans = null, $options = null, $casoption = null) {

        switch($anstest) {
            case 'AlgEquiv':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATAlgEquiv', false, $casoption, $options);
                break;

            case 'EqualComAss':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATEqualComAss', false, $casoption, $options, false);
                break;

            case 'CasEqual':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATCASEqual', false, $casoption, $options, false);
                break;

            case 'SameType':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATSameType', false, $casoption, $options);
                break;

            case 'SubstEquiv':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATSubstEquiv', false, $casoption, $options);
                break;

            case 'Expanded':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATExpanded', false, $casoption, $options);
                break;

            case 'FacForm':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATFacForm', true, $casoption, $options, false, true);
                break;

            case 'SingleFrac':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATSingleFrac', false, $casoption, $options, false);
                break;

            case 'PartFrac':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATPartFrac',
                                    true, $casoption, $options, true, false, true);
                break;

            case 'CompSquare':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATCompSquare',
                                    true, $casoption, $options, true, false, true);
                break;

            case 'String':
                require_once(__DIR__ . '/atstring.class.php');
                $this->at = new stack_anstest_atstring($sans, $tans, $options, $casoption);
                break;

            case 'StringSloppy':
                require_once(__DIR__ . '/stringsloppy.class.php');
                $this->at = new stack_anstest_stringsloppy($sans, $tans, $options, $casoption);
                break;

            case 'RegExp':
                require_once(__DIR__ . '/atregexp.class.php');
                $this->at = new stack_anstest_atregexp($sans, $tans, $options, $casoption);
                break;

            case 'Diff':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATDiff', true, $casoption, $options, false, true);
                break;

            case 'Int':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATInt', true, $casoption, $options, false, true);
                break;

            case 'GT':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATGT', false, $casoption, $options);
                break;

            case 'GTE':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATGTE', false, $casoption, $options);
                break;

            case 'NumAbsolute':
                if (trim($casoption) == '') {
                    $casoption = '0.05';
                }
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATNumAbsolute', true, $casoption, $options, true, true);
                break;

            case 'NumRelative':
                if (trim($casoption) == '') {
                    $casoption = '0.05';
                }
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATNumRelative', true, $casoption, $options, true, true);
                break;

            case 'NumSigFigs':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATNumSigFigs', true, $casoption, $options, true, true);
                break;

            case 'NumDecPlaces':
                require_once(__DIR__ . '/atdecplaces.class.php');
                $this->at = new stack_anstest_atdecplaces($sans, $tans, $options, $casoption);
                break;

            case 'Units':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATUnits', true, $casoption, $options, false, true);
                break;

            case 'UnitsStrict':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATUnitsStrict',
                                    true, $casoption, $options, false, true);
                break;

            case 'LowestTerms':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATLowestTerms', false, $casoption, $options, 0);
                break;

            case 'SysEquiv':
                $this->at = new stack_answertest_general_cas($sans, $tans, 'ATSysEquiv', false, $casoption, $options);
                break;

            default:
                throw new stack_exception('stack_ans_test_controller: called with invalid answer test name: '.$anstest);
        }

    }


    /**
     *
     *
     * @return bool
     * @access public
     */
    public function do_test() {
        $result = $this->at->do_test();
        return $result;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function get_at_errors() {
        return $this->at->get_at_errors();
    }

    /**
     *
     *
     * @return float
     * @access public
     */
    public function get_at_mark() {
        return $this->at->get_at_mark();
    }

    /**
     *
     *
     * @return bool
     * @access public
     */
    public function get_at_valid() {
        return $this->at->get_at_valid();
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function get_at_answernote() {
        return trim($this->at->get_at_answernote());
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function get_at_feedback() {
        return stack_maxima_translate($this->at->get_at_feedback());
    }

    /**
     * @return array the list of available answertest types. An array
     *      answertest internal name => language string key.
     */
    public static function get_available_ans_tests() {
        return self::$types;
    }

    /**
     * Returns whether the testops should be processed by the CAS for this AnswerTest
     * Returns true if the Testops should be processed.
     *
     * @return bool
     * @access public
     */
    public function process_atoptions() {
        return $this->at->process_atoptions();
    }

    /**
     * Returns whether the testops are required for this test.
     *
     * @return bool
     * @access public
     */
    public function required_atoptions() {
        return $this->at->required_atoptions();
    }

    /**
     * Validates the options, when needed.
     *
     * @return bool
     * @access public
     */
    public function validate_atoptions($opt) {
        return $this->at->validate_atoptions($opt);
    }

    /**
     * Pass back CAS debug information for testing.
     *
     * @return string
     * @access public
     */
    public function get_debuginfo() {
        return $this->at->get_debuginfo();
    }
}

