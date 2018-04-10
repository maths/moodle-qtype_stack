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

/**
 * Significant figure answer test.
 *
 * @copyright  2016 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest_atnumsigfigs extends stack_anstest {

    public function __construct($sans, $tans, $options, $atoption, $casfunction, $simp) {
        parent::__construct($sans, $tans, $options, $atoption);

        $this->casfunction = $casfunction;
        $this->simp        = (bool) $simp;
    }

    public function do_test() {
        if ('' == trim($this->sanskey)) {
            $this->aterror      = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptySA")));
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptySA")));
            $this->atansnote    = $this->casfunction.'TEST_FAILED:Empty SA.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' == trim($this->tanskey)) {
            $this->aterror      = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptyTA")));
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptyTA")));
            $this->atansnote    = $this->casfunction.'TEST_FAILED:Empty TA.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Sort out teacher's options for this test.
        $atopt = trim($this->atoption);
        $requiredsigfigs = $atopt;
        $requiredaccuracy = $atopt;
        if (substr($atopt, 0, 1) == '[') {
            $opts = substr(substr($atopt, 0 , -1), 1);
            $opts = explode(',', $opts);
            $requiredsigfigs = trim($opts[0]);
            $requiredaccuracy = trim($opts[1]);
        }
        $strictsigfigs = false;
        $condoneextrasigfigs = false;
        $numaccuracy   = true;
        if ('ATSigFigsStrict' == $this->casfunction) {
            $strictsigfigs = true;
            $numaccuracy   = false;
        }
        if ($requiredaccuracy == 0) {
            $numaccuracy   = false;
        }
        if ('ATNumSigFigs' == $this->casfunction && $requiredaccuracy == -1) {
            $condoneextrasigfigs = true;
            $requiredaccuracy = $requiredsigfigs;
            // Change the options going into the CAS.
            $atopt = "[$requiredsigfigs,$requiredsigfigs]";
        }

        if (null == $atopt or '' == $atopt or 0 === $atopt or $requiredsigfigs <= 0
                or $requiredaccuracy < 0 or !ctype_digit($requiredsigfigs) or !ctype_digit($requiredaccuracy)) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_MissingOptions")));
            $this->atansnote    = 'STACKERROR_OPTION.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        } else {
            // Validate with teacher privileges, strict syntax & no automatically adding stars.
            $ct  = new stack_cas_casstring($this->atoption);

            if (!$ct->get_valid('t', true, 1)) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $ct->get_errors()));
                $this->atansnote    = 'STACKERROR_OPTION.';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
        }

        // Use PHP to establish that the range of significant figures from the student's expression
        // contains the number of significant figures specified by the teacher.
        $r = stack_utils::decimal_digits($this->sanskey);

        if ($strictsigfigs) {
            $this->atmark = 0;
            if ($r['lowerbound'] == $this->atoption) {
                $this->atmark = 1;
            } else if ($r['lowerbound'] <= $this->atoption && $this->atoption <= $r['upperbound']) {
                $this->atansnote    = $this->casfunction.'_WithinRange. ';
            }
        } else if ($condoneextrasigfigs) {
            // Round the student's answer.
            $this->sanskey = 'significantfigures('.$this->sanskey.','.$requiredsigfigs.')';
            if ($requiredsigfigs <= $r['lowerbound']) {
                $withinrange = true;
                $this->atmark = 1;
            } else {
                $this->atansnote = $this->casfunction.'_WrongDigits. ';
                // Note, we combine with feedback from the CAS, so we set up a situation which can be combined with
                // other CAS-generated feedback here.
                $this->atfeedback = "stack_trans('ATNumSigFigs_WrongDigits');";
                $this->atmark = 0;
                $withinrange = false;
            }
        } else {
            if ($requiredsigfigs == $r['lowerbound']) {
                $withinrange = true;
                $this->atmark = 1;
            } else if ($r['lowerbound'] <= $requiredsigfigs && $requiredsigfigs <= $r['upperbound']) {
                $this->atansnote = $this->casfunction.'_WithinRange. ';
                $withinrange = true;
                $this->atmark = 1;
            } else {
                $this->atansnote = $this->casfunction.'_WrongDigits. ';
                // Note, we combine with feedback from the CAS, so we set up a situation which can be combined with
                // other CAS-generated feedback here.
                $this->atfeedback = "stack_trans('ATNumSigFigs_WrongDigits');";
                $this->atmark = 0;
                $withinrange = false;
            }
        }

        // Do we need to establish numerical precision with a CAS call?
        if (!$numaccuracy) {
            if ($this->atmark) {
                return true;
            } else {
                return false;
            }
        }

        // Sort out options for the CAS session.
        if (null === $this->options) {
            $this->options = new stack_options();
        }
        $this->options->set_option('simplify', $this->simp);

        $cascommands = array();
        $cascommands[] = "STACKSA:$this->sanskey";
        $cascommands[] = "STACKTA:$this->tanskey";
        $cascommands[] = "STACKOP:$atopt";
        $cascommands[] = "result:StackReturn({$this->casfunction}(STACKSA,STACKTA,STACKOP))";

        $cts = array();
        foreach ($cascommands as $com) {
            $cs    = new stack_cas_casstring($com);
            $cs->get_valid('t', true, 0);
            $cts[] = $cs;
        }

        $session = new stack_cas_session($cts, $this->options, 0);
        $session->instantiate();

        $this->debuginfo = $session->get_debuginfo();
        if ('' != $session->get_errors_key('STACKSA')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('STACKSA')));
            $this->atansnote    = $this->casfunction.'_STACKERROR_SAns.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $session->get_errors_key('STACKTA')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('STACKTA')));
            $this->atansnote    = $this->casfunction.'_STACKERROR_TAns.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $session->get_errors_key('STACKOP')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('STACKOP')));
            $this->atansnote    = $this->casfunction.'_STACKERROR_Opt.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        $sessionvars = $session->get_session();
        $result = $sessionvars[3];

        if ('' != $result->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            if ('' != trim($result->get_feedback())) {
                $this->atfeedback .= $result->get_feedback();
            } else {
                $this->atfeedback = stack_string('TEST_FAILED', array('errors' => $result->get_errors()));
            }
            $this->atansnote    = trim($result->get_answernote());
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Convert the Maxima string 'true' to PHP true.
        if ('true' == $result->get_value() && $withinrange) {
            $this->atmark = 1;
        } else {
            $this->atmark = 0;
        }

        $this->atvalid     = $result->get_valid();
        $this->atansnote  .= trim($result->get_answernote());
        $this->atfeedback .= $result->get_feedback();
        $caserrormsgs = array('ATNumSigFigs_NotDecimal.', 'ATUnits_SA_not_expression.',
            'ATUnits_SA_no_units.', 'ATUnits_SA_only_units.');
        if (in_array(trim($result->get_answernote()), $caserrormsgs)) {
            $this->atansnote  = trim($result->get_answernote());
            $this->atfeedback = $result->get_feedback();
            $this->atvalid = false;
        }

        if ($this->atmark) {
            return true;
        } else {
            return false;
        }
    }

    public function process_atoptions() {
        return true;
    }

    public function required_atoptions() {
        return true;
    }

    /**
     * Validates the options, when needed.
     *
     * @return (bool, errors)
     * @access public
     */
    public function validate_atoptions($opt) {
        return array(true, '');
    }
}
