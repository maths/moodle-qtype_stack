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

defined('MOODLE_INTERNAL') || die();


// Answer test controller class.
//
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/anstest.class.php');
require_once(__DIR__ . '/at_general_cas.class.php');
require_once(__DIR__ . '/../cas/connector.class.php');
require_once(__DIR__ . '/../cas/ast.container.class.php');

class stack_ans_test_controller {
    protected static $types = [
        'AlgEquiv'             => 'stackOptions_AnsTest_values_AlgEquiv',
        'AlgEquivNouns'        => 'stackOptions_AnsTest_values_AlgEquivNouns',
        'EqualComAss'          => 'stackOptions_AnsTest_values_EqualComAss',
        'EqualComAssRules'     => 'stackOptions_AnsTest_values_EqualComAssRules',
        'CasEqual'             => 'stackOptions_AnsTest_values_CasEqual',
        'SameType'             => 'stackOptions_AnsTest_values_SameType',
        'SubstEquiv'           => 'stackOptions_AnsTest_values_SubstEquiv',
        'SysEquiv'             => 'stackOptions_AnsTest_values_SysEquiv',
        'Sets'                 => 'stackOptions_AnsTest_values_Sets',
        'Expanded'             => 'stackOptions_AnsTest_values_Expanded',
        'FacForm'              => 'stackOptions_AnsTest_values_FacForm',
        'SingleFrac'           => 'stackOptions_AnsTest_values_SingleFrac',
        'PartFrac'             => 'stackOptions_AnsTest_values_PartFrac',
        'CompSquare'           => 'stackOptions_AnsTest_values_CompSquare',
        'PropLogic'            => 'stackOptions_AnsTest_values_PropLogic',
        'Equiv'                => 'stackOptions_AnsTest_values_Equiv',
        'EquivFirst'           => 'stackOptions_AnsTest_values_EquivFirst',
        'GT'                   => 'stackOptions_AnsTest_values_GT',
        'GTE'                  => 'stackOptions_AnsTest_values_GTE',
        'SigFigsStrict'        => 'stackOptions_AnsTest_values_SigFigsStrict',
        'NumAbsolute'          => 'stackOptions_AnsTest_values_NumAbsolute',
        'NumRelative'          => 'stackOptions_AnsTest_values_NumRelative',
        'NumSigFigs'           => 'stackOptions_AnsTest_values_NumSigFigs',
        'NumDecPlaces'         => 'stackOptions_AnsTest_values_NumDecPlaces',
        'NumDecPlacesWrong'    => 'stackOptions_AnsTest_values_NumDecPlacesWrong',
        'Units'                => 'stackOptions_AnsTest_values_UnitsSigFigs',
        'UnitsStrict'          => 'stackOptions_AnsTest_values_UnitsStrictSigFigs',
        'UnitsAbsolute'        => 'stackOptions_AnsTest_values_UnitsAbsolute',
        'UnitsStrictAbsolute'  => 'stackOptions_AnsTest_values_UnitsStrictAbsolute',
        'UnitsRelative'        => 'stackOptions_AnsTest_values_UnitsRelative',
        'UnitsStrictRelative'  => 'stackOptions_AnsTest_values_UnitsStrictRelative',
        'LowestTerms'          => 'stackOptions_AnsTest_values_LowestTerms',
        'Diff'                 => 'stackOptions_AnsTest_values_Diff',
        'Int'                  => 'stackOptions_AnsTest_values_Int',
        'String'               => 'stackOptions_AnsTest_values_String',
        'StringSloppy'         => 'stackOptions_AnsTest_values_StringSloppy',
        'Levenshtein'          => 'stackOptions_AnsTest_values_Levenshtein',
        'SRegExp'              => 'stackOptions_AnsTest_values_SRegExp',
        'Validator'            => 'stackOptions_AnsTest_values_Validator',
    ];

    /*
     * Does this test require options [0] and are these evaluated by the CAS [1] ?
     * In [2] we have the value of simp in the CAS session.
     * Does the test require the raw value of the student's answer as a string [3] ?
     *
     * Note, the options are currently always simplified in the node class.
     */
    protected static $pops = [
        'AlgEquiv'             => [false, false, true, false],
        'AlgEquivNouns'        => [false, false, false, false],
        'EqualComAss'          => [false, false, false, false],
        'EqualComAssRules'     => [true, true, false, false],
        'CasEqual'             => [false, false, false, false],
        'SameType'             => [false, false, true, false],
        'SubstEquiv'           => ['optional', true, true, false],
        'SysEquiv'             => [false, false, true, false],
        'Sets'                 => [false, false, false, false],
        'Expanded'             => [false, false, true, false],
        'FacForm'              => [true, true, false, false],
        'SingleFrac'           => [false, false, false, false],
        'PartFrac'             => [true, true, true, false],
        'CompSquare'           => [true, true, true, false],
        'PropLogic'            => [false, false, true, false],
        'Equiv'                => ['optional', true, false, false],
        'EquivFirst'           => ['optional', true, false, false],
        'GT'                   => [false, false, true, false],
        'GTE'                  => [false, false, true, false],
        'SigFigsStrict'        => [true, true, true, true],
        'NumAbsolute'          => [true, true, true, false],
        'NumRelative'          => [true, true, true, false],
        'NumSigFigs'           => [true, true, false, true],
        'NumDecPlaces'         => [true, true, false, true],
        'NumDecPlacesWrong'    => [true, true, false, false],
        'Units'                => [true, true, false, true],
        'UnitsStrict'          => [true, true, false, true],
        'UnitsAbsolute'        => [true, true, false, false],
        'UnitsStrictAbsolute'  => [true, true, false, false],
        'UnitsRelative'        => [true, true, false, false],
        'UnitsStrictRelative'  => [true, true, false, false],
        'LowestTerms'          => [false, false, false, false],
        'Diff'                 => [true, true, false, false],
        'Int'                  => [true, true, false, false],
        'String'               => [false, false, false, false],
        'StringSloppy'         => [false, false, false, false],
        'Levenshtein'          => [true, true, true, false],
        'SRegExp'              => [false, false, true, false],
        'Validator'            => [true, true, false, false],
    ];

    /**
     * The answertest object that the functions call.
     * @var stack_anstest
     * @access private
     */
    private $at;

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
    public function __construct(string $anstest, stack_ast_container $sans, stack_ast_container $tans, $casoption = null,
            $options = null, $contextsession = []) {

        switch($anstest) {
            case 'AlgEquiv':
            case 'AlgEquivNouns':
            case 'EqualComAss':
            case 'EqualComAssRules':
            case 'CasEqual':
            case 'SameType':
            case 'Sets':
            case 'Expanded':
            case 'FacForm':
            case 'SingleFrac':
            case 'PartFrac':
            case 'CompSquare':
            case 'PropLogic':
            case 'Diff':
            case 'Int':
            case 'GT':
            case 'GTE':
            case 'UnitsAbsolute':
            case 'UnitsStrictAbsolute':
            case 'UnitsRelative':
            case 'UnitsStrictRelative':
            case 'LowestTerms':
            case 'SysEquiv':
            case 'SRegExp':
            case 'NumSigFigs':
            case 'SigFigsStrict':
            case 'Units':
            case 'UnitsStrict':
            case 'NumDecPlaces':
            case 'NumDecPlacesWrong':
            case 'Levenshtein':
            case 'Validator':
                $this->at = new stack_answertest_general_cas($sans, $tans, $anstest, $casoption, $options, $contextsession);
                break;

            case 'SubstEquiv':
                if ($casoption === null || '' == $casoption->ast_to_string() || 'null' == $casoption->ast_to_string()) {
                    $opts = stack_ast_container::make_from_teacher_source('[]', '', new stack_cas_security());
                    $this->at = new stack_answertest_general_cas($sans, $tans, $anstest, $opts, $options, $contextsession);
                } else {
                    $this->at = new stack_answertest_general_cas($sans, $tans, $anstest, $casoption, $options, $contextsession);
                }
                break;

            case 'Equiv':
            case 'EquivFirst':
                if ($casoption === null || '' == $casoption->ast_to_string()) {
                    $opts = stack_ast_container::make_from_teacher_source('null', '', new stack_cas_security());
                    $this->at = new stack_answertest_general_cas($sans, $tans, $anstest, $opts, $options, $contextsession);
                } else {
                    $this->at = new stack_answertest_general_cas($sans, $tans, $anstest, $casoption, $options, $contextsession);
                }
                break;

            case 'NumAbsolute':
            case 'NumRelative':
                if ($casoption === null || !$casoption->get_valid() || '' == $casoption->ast_to_string()) {
                    $casoption = stack_ast_container::make_from_teacher_source('0.05', '', new stack_cas_security());
                }
                $this->at = new stack_answertest_general_cas($sans, $tans, $anstest, $casoption, $options, $contextsession);
                break;

            case 'String':
            case 'StringSloppy':
            case 'RegExp':
                require_once(__DIR__ . '/at_general_cas_preprepare.class.php');
                $this->at = new stack_answertest_general_cas_preprepare($sans, $tans, $anstest, $options, $casoption);
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
        return ($this->at->get_at_feedback());
    }

    /**
     * @return array the list of available answertest types. An array
     *      answertest internal name => language string key.
     */
    public static function get_available_ans_tests() {
        return self::$types;
    }

    /**
     * Returns whether the testops are required for this test.
     *
     * @return bool
     * @access public
     */
    public static function required_atoptions($atest) {
        $op = self::$pops[$atest];
        return $op[0];
    }

    /**
     * Returns a list of the answer tests who do not require test options
     *
     * @return array
     * @access public
     */
    public static function get_ans_tests_without_options() {
        $anstests = [];
        foreach (self::$pops as $key => $value) {
            if ($value[0] === false) {
                $anstests[] = $key;
            }
        }
        return $anstests;
    }

    /**
     * Returns whether the testops should be processed by the CAS for this AnswerTest
     *
     * @return bool
     * @access public
     */
    public static function process_atoptions($atest) {
        $op = self::$pops[$atest];
        return $op[1];
    }

    /**
     * Returns whether the session needs simplification.
     *
     * @return bool
     * @access public
     */
    public static function simp($atest) {
        $op = self::$pops[$atest];
        return $op[2];
    }

    /**
     * Returns whether the test requires the raw input of the student's answer.
     *
     * @return bool
     * @access public
     */
    public static function required_raw($atest) {
        $op = self::$pops[$atest];
        return $op[3];
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

    /**
     * Returns an intelligible trace of an executed answer test.
     *
     * @return string
     * @access public
     */
    public function get_trace($includeresult = true) {
        return $this->at->get_trace($includeresult);
    }
}

