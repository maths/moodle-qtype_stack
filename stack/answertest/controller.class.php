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
require_once(dirname(__FILE__) . '/anstest.class.php');
require_once(dirname(__FILE__) . '/../cas/connector.class.php');
require_once(dirname(__FILE__) . '/../cas/casstring.class.php');
require_once(dirname(__FILE__) . '/../cas/cassession.class.php');

class STACK_AnsTestController {
    // Attributes
    /**
     *
     *
     * @var    array(string)
     * @access private
     */
    private static $avaliable_ans_tests;

    /**
    * The answertest object that the functions call
    * @var AnsTest
    * @access private
    */
    private $at;

    // Operations

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
        $this->avaliableAnsTests = array('AlgEquiv'=>stack_string("stackOptions_AnsTest_values_AlgEquiv"),
              'Equal_Com_Ass'=> stack_string("stackOptions_AnsTest_values_Equal_com_ass"),
              'CasEqual'     => stack_string("stackOptions_AnsTest_values_CASEqual"),
              'SameType'     => stack_string("stackOptions_AnsTest_values_SameType"),
              'SubstEquiv'   => stack_string("stackOptions_AnsTest_values_SubstEquiv"),
              'SysEquiv'     => stack_string("stackOptions_AnsTest_values_SysEquiv"),              
              'Expanded'     => stack_string("stackOptions_AnsTest_values_Expanded"),
              'FacForm'      => stack_string("stackOptions_AnsTest_values_FacForm"),
              'SingleFrac'   => stack_string("stackOptions_AnsTest_values_SingleFrac"),
              'PartFrac'     => stack_string("stackOptions_AnsTest_values_PartFrac"),
              'CompSquare'   => stack_string("stackOptions_AnsTest_values_CompSquare"),
              'GT'           => stack_string("stackOptions_AnsTest_values_Num_GT"),
              'GTE'          => stack_string("stackOptions_AnsTest_values_Num_GTE"),
              'NumAbsolute'  => stack_string("stackOptions_AnsTest_values_Num_tol_absolute"),
              'NumRelative'  => stack_string("stackOptions_AnsTest_values_Num_tol_relative"),
              'NumSigFigs'   => stack_string("stackOptions_AnsTest_values_Num_sig_figs"),
              'LowestTerms'  => stack_string("stackOptions_AnsTest_values_Num_LowestTerms"),
              'Diff'         => stack_string("stackOptions_AnsTest_values_Diff"),
              'Int'          => stack_string("stackOptions_AnsTest_values_Int"),
              'String'       => stack_string("stackOptions_AnsTest_values_String"),
              'StringSloppy' => stack_string("stackOptions_AnsTest_values_StringSloppy"),
              'RegExp'       => stack_string("stackOptions_AnsTest_values_RegExp"),
              );
        //echo "<br>In Anstest controller: $AnsTest<br>";
        switch($anstest) {
            case 'AlgEquiv':
                require_once(dirname(__FILE__) . '/atalgequiv.class.php');
                $this->at = new STACK_AnsTest_AlgEquiv($sans, $tans, $options, $casoption);
                break;

            case 'Equal_Com_Ass':
                require_once(dirname(__FILE__) . '/equalcomass.class.php');
                $this->at = new STACK_AnsTest_EqualComAss($sans, $tans, $options, $casoption);
                break;

            case 'CasEqual':
                require_once(dirname(__FILE__) . '/casequal.class.php');
                $this->at = new STACK_AnsTest_CasEqual($sans, $tans, $options, $casoption);
                break;

            case 'SameType':
                require_once(dirname(__FILE__) . '/sametype.class.php');
                $this->at = new STACK_AnsTest_SameType($sans, $tans, $options, $casoption);
                break;

            case 'SubstEquiv':
                require_once(dirname(__FILE__) . '/substequiv.class.php');
                $this->at = new STACK_AnsTest_SubstEquiv($sans, $tans, $options, $casoption);
                break;

            case 'Expanded':
                require_once(dirname(__FILE__) . '/expanded.class.php');
                $this->at = new STACK_AnsTest_Expanded($sans, $tans, $options, $casoption);
                break;

            case 'FacForm':
                require_once(dirname(__FILE__) . '/facform.class.php');
                $this->at = new STACK_AnsTest_FacForm($sans, $tans, $options, $casoption);
                break;

            case 'SingleFrac':
                require_once(dirname(__FILE__) . '/singlefrac.class.php');
                $this->at = new STACK_AnsTest_SingleFrac($sans, $tans, $options, $casoption);
                break;

            case 'PartFrac':
                require_once(dirname(__FILE__) . '/partfrac.class.php');
                $this->at = new STACK_AnsTest_PartFrac($sans, $tans, $options, $casoption);
                break;

            case 'CompSquare':
                require_once(dirname(__FILE__) . '/compsquare.class.php');
                $this->at = new STACK_AnsTest_CompSquare($sans, $tans, $options, $casoption);
                break;

            case 'String':
                require_once(dirname(__FILE__) . '/atstring.class.php');
                $this->at = new STACK_AnsTest_ATString($sans, $tans, $options, $casoption);
                break;

            case 'StringSloppy':
                require_once(dirname(__FILE__) . '/stringsloppy.class.php');
                $this->at = new STACK_AnsTest_StringSloppy($sans, $tans, $options, $casoption);
                break;

            case 'RegExp':
                require_once(dirname(__FILE__) . '/atregexp.class.php');
                $this->at = new STACK_AnsTest_ATRegExp($sans, $tans, $options, $casoption);
                break;

            case 'Diff':
                require_once(dirname(__FILE__) . '/diff.class.php');
                $this->at = new STACK_AnsTest_Diff($sans, $tans, $options, $casoption);
                break;

            case 'Int':
                require_once(dirname(__FILE__) . '/int.class.php');
                $this->at = new STACK_AnsTest_Int($sans, $tans, $options, $casoption);
                break;

            case 'GT':
                require_once(dirname(__FILE__) . '/greaterthan.class.php');
                $this->at = new STACK_AnsTest_GreaterThan($sans, $tans, $options, $casoption);
                break;

            case 'GTE':
                require_once(dirname(__FILE__) . '/greaterthanequal.class.php');
                $this->at = new STACK_AnsTest_GreaterThanEqual($sans, $tans, $options, $casoption);
                break;

            case 'NumAbsolute':
                require_once(dirname(__FILE__) . '/numabsolute.class.php');
                $this->at = new STACK_AnsTest_NumAbsolute($sans, $tans, $options, $casoption);
                break;

            case 'NumRelative':
                require_once(dirname(__FILE__) . '/numrelative.class.php');
                $this->at = new STACK_AnsTest_NumRelative($sans, $tans, $options, $casoption);
                break;

            case 'NumSigFigs':
                require_once(dirname(__FILE__) . '/numsigfigs.class.php');
                $this->at = new STACK_AnsTest_NumSigFigs($sans, $tans, $options, $casoption);
                break;

            case 'LowestTerms':
                require_once(dirname(__FILE__) . '/lowestterms.class.php');
                $this->at = new STACK_AnsTest_LowestTerms($sans, $tans, $options, $casoption);
                break;

            case 'SysEquiv':
                require_once(dirname(__FILE__) . '/sysequiv.class.php');
                $this->at = new STACK_AnsTest_SysEquiv($sans, $tans, $options, $casoption);
                break;
        }

    }


    /**
     *
     *
     * @return bool
     * @access public
     */
    public function doAnsTest() {
        $result = $this->at->doAnsTest();
        return $result;
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getATErrors() {
        return $this->at->getATErrors();
    }

    /**
     *
     *
     * @return float
     * @access public
     */
    public function getATMark() {
        return $this->at->getATMark();
    }

    /**
     *
     *
     * @return bool
     * @access public
     */
    public function getATValid() {
        return $this->at->getATValid();
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getATAnsNote() {
        return $this->at->getATAnsNote();
    }


    /**
     *
     *
     * @return string
     * @access public
     */
    public function getATFeedback() {
        $rawfeedback = $this->at->getATFeedback();

        if (strpos($rawfeedback, 'stack_trans') === false) {
            return $this->at->getATFeedback();
        } else {
            //echo "<br />Raw string:<pre>$rawfeedback</pre>";
            $rawfeedback = str_replace('[[', '', $rawfeedback);
            $rawfeedback = str_replace(']]', '', $rawfeedback);
            $rawfeedback = str_replace('\n', '', $rawfeedback);
            $rawfeedback = str_replace('\\', '\\\\', $rawfeedback);
            $rawfeedback = str_replace('$', '\$', $rawfeedback);
            $rawfeedback = str_replace('!quot!','"', $rawfeedback);
            //echo "<br />Subs string:<pre>$rawfeedback</pre>";

            ob_start();
            eval($rawfeedback);
            $translated = ob_get_contents();
            ob_end_clean();

            if ('' != trim($translated)) {
              $translated .= " \n\n";
            }

            return $translated;
        }
    }

    /**
     *
     *
     * @return ErrorLog
     * @access public
     */
    public function getErrorLog() {
        return $this->at->getErrorLog();
    }

    /**
     *
     *
     * @return string
     * @access public
     */
    public function getErrors() {
        return $this->at->getErrors();
    }

    /**
     *
     *
     * @return array(string)
     * @access public
     */
    public function getAvaliableAnsTests() {
        return $this->avaliableAnsTests;
    }

    /**
    * Returns a list of available answertests
    * @access public
    * @return string xhtml
    *
    */
    public function get_edit_dropdown($current, $name='') {

        $widget = "<select name=\"$name\">";
        foreach ($this->avaliableAnsTests as $label => $localName) {
        //answertests have been localised, this displays the correct name
            if ($label == $current) {
                $widget .= "<option value=\"$label\" selected>$localName</option>";
            } else {
                $widget .= "<option value=\"$label\">$localName</option>";
            }
        }
        $widget .= '</select>';
        return $widget;
    }


    /**
    * Returns whether the testops should be processed by the CAS for this AnswerTest
    * Returns true if the Testops should be processed.
    *
    * @return bool
    * @access public
    */
    public function processTestOps() {
        return $this->at->processTestOps();
    }

}

