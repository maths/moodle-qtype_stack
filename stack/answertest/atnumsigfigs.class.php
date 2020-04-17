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


require_once(__DIR__ . '/../cas/cassession2.class.php');

/**
 * Significant figure answer test.
 *
 * @copyright  2016 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest_atnumsigfigs extends stack_anstest {

    public function __construct(stack_ast_container $sans, stack_ast_container $tans, string $atname,
            $atoption, $options) {
        parent::__construct($sans, $tans, $options, $atoption);

        $this->casfunction       = 'AT'. $atname;
        $this->atname            = $atname;
        $this->simp              = stack_ans_test_controller::simp($atname);
    }

    public function do_test() {
        if ('' == trim($this->sanskey->ast_to_string())) {
            $this->aterror      = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptySA")));
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptySA")));
            $this->atansnote    = $this->casfunction.'TEST_FAILED-Empty SA.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' == trim($this->tanskey->ast_to_string())) {
            $this->aterror      = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptyTA")));
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_EmptyTA")));
            $this->atansnote    = $this->casfunction.'TEST_FAILED-Empty TA.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // These tests must have an option at this point.
        if (!$this->atoption->get_valid()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $this->atoption->get_errors()));
            $this->atansnote    = 'STACKERROR_OPTION.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Sort out teacher's options for this test.
        $atopt = trim($this->atoption->get_inputform(true));
        $requiredsigfigs = $atopt;
        $requiredaccuracy = $atopt;
        if (substr($atopt, 0, 1) == '[') {
            $opts = substr(substr($atopt, 0 , -1), 1);
            $opts = explode(',', $opts);
            $requiredsigfigs = trim($opts[0]);
            $requiredaccuracy = trim($opts[1]);
        }
        // The evaluated values are better. And we need them.
        if ($this->atoption->is_evaluated()) {
            if ($this->atoption->is_int(true)) {
                $requiredsigfigs = $this->atoption->get_value(true);
                $requiredaccuracy = $this->atoption->get_value(true);
                $atopt = $requiredsigfigs;
            } else if ($this->atoption->is_list(true)) {
                $requiredsigfigs = $this->atoption->get_list_element(0, true)->toString();
                $requiredaccuracy = $this->atoption->get_list_element(1, true)->toString();
                $atopt = "[$requiredsigfigs,$requiredaccuracy]";
            }
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
        }

        if (!(is_numeric($requiredaccuracy) && is_numeric($requiredsigfigs))) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atansnote    = 'STACKERROR_OPTION.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if (null == $atopt or '' == $atopt or 0 === $atopt or $requiredsigfigs <= 0
                or $requiredaccuracy < 0) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_MissingOptions")));
            $this->atansnote    = 'STACKERROR_OPTION.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Use PHP to establish that the range of significant figures from the student's expression
        // contains the number of significant figures specified by the teacher.
        $r = $this->sanskey->get_decimal_digits(false);

        if ($strictsigfigs) {
            $this->atmark = 0;
            if ($r['lowerbound'] == $atopt) {
                $this->atmark = 1;
            } else if ($r['lowerbound'] <= $atopt && $atopt <= $r['upperbound']) {
                $this->atansnote .= $this->casfunction.'_WithinRange. ';
            }
        } else if ($condoneextrasigfigs) {
            // Rounding the student's answer now happens in maxima.
            if ($requiredsigfigs <= $r['lowerbound']) {
                $withinrange = true;
                $this->atmark = 1;
            } else {
                $this->atansnote .= $this->casfunction.'_WrongDigits. ';
                $this->atfeedback = stack_string('ATNumSigFigs_WrongDigits', '');
                $this->atmark = 0;
                $withinrange = false;
            }
        } else {
            if ($requiredsigfigs == $r['lowerbound']) {
                $withinrange = true;
                $this->atmark = 1;
            } else if ($r['lowerbound'] <= $requiredsigfigs && $requiredsigfigs <= $r['upperbound']) {
                $this->atansnote .= $this->casfunction.'_WithinRange. ';
                $withinrange = true;
                $this->atmark = 1;
            } else {
                $this->atansnote = $this->casfunction.'_WrongDigits. ';
                $this->atfeedback .= stack_string('ATNumSigFigs_WrongDigits', '');
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

        // New values could be based on previously evaluated ones.
        $sa = null;
        if ($this->sanskey->is_correctly_evaluated()) {
            $sa = stack_ast_container::make_from_teacher_source($this->sanskey->get_value());
        } else {
            $sa = clone $this->sanskey;
        }
        $sa->set_key('STACKSA');
        $ta = null;
        if ($this->tanskey->is_correctly_evaluated()) {
            $ta = stack_ast_container::make_from_teacher_source($this->tanskey->get_value());
        } else {
            $ta = clone $this->tanskey;
        }
        $ta->set_key('STACKTA');
        $ops = stack_ast_container::make_from_teacher_source('STACKOP:true', '', new stack_cas_security());
        $result = stack_ast_container::make_from_teacher_source("result:{$this->casfunction}(STACKSA,STACKTA)", '',
        new stack_cas_security());
        if (stack_ans_test_controller::process_atoptions($this->atname)) {
            // We must not clone the original atoptions as it might not have variables evaluated.
            $ops = stack_ast_container::make_from_teacher_source('STACKOP:' . $atopt . ',simp', '', new stack_cas_security());
            $result = stack_ast_container::make_from_teacher_source("result:{$this->casfunction}(STACKSA,STACKTA,STACKOP)", '',
            new stack_cas_security());
        }
        $session = new stack_cas_session2(array($sa, $ta, $ops, $result), $this->options, 0);
        if ($session->get_valid()) {
            $session->instantiate();
        }
        $this->debuginfo = $session->get_debuginfo();

        if ('' != $sa->get_errors() || !$sa->get_valid()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $sa->get_errors()));
            $this->atansnote    = $this->casfunction.'_STACKERROR_SAns.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $ta->get_errors() || !$ta->get_valid()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $ta->get_errors()));
            $this->atansnote    = $this->casfunction.'_STACKERROR_TAns.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $ops->get_errors() || !$ops->get_valid()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $ops->get_errors()));
            $this->atansnote    = $this->casfunction.'_STACKERROR_Opt.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        $unpacked = $this->unpack_result($result->get_evaluated());

        if ('' != $result->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            if ('' != trim($unpacked['feedback'])) {
                $this->atfeedback = $unpacked['feedback'];
            } else {
                $this->atfeedback = stack_string('TEST_FAILED', array('errors' => $result->get_errors()));
            }
            $this->atansnote    = trim($unpacked['answernote']);
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Convert the Maxima string 'true' to PHP true.
        if ($unpacked['result'] && $withinrange) {
            $this->atmark = 1;
        } else {
            $this->atmark = 0;
        }

        $this->atvalid     = $unpacked['valid'];
        $this->atansnote  .= str_replace("\n", '', trim($unpacked['answernote']));
        $this->atfeedback .= $unpacked['feedback'];
        $caserrormsgs = array('ATNumSigFigs_STACKERROR_SAns.', 'ATNumSigFigs_STACKERROR_TAns.',
            'ATNumSigFigs_STACKERROR_opt.', 'ATNumSigFigs_NotDecimal.', 'ATUnits_SA_not_expression.',
            'ATUnits_SA_no_units.', 'ATUnits_SA_only_units.');
        if (in_array(trim($unpacked['answernote']), $caserrormsgs)) {
            $this->atansnote  = trim($unpacked['answernote']);
            $this->atfeedback = $unpacked['feedback'];
            $this->atvalid = false;
        }

        if ($this->atmark) {
            return true;
        } else {
            return false;
        }
    }

    private function unpack_result(MP_Node $result): array {
        $r = array('valid' => false, 'result' => 'unknown', 'answernote' => '', 'feedback' => '');

        if ($result instanceof MP_Root) {
            $result = $result->items[0];
        }
        if ($result instanceof MP_Statement) {
            $result = $result->statement;
        }
        if ($result instanceof MP_List) {
            $r['valid'] = $result->items[0]->value;
            $r['result'] = $result->items[1]->value;
            if ($result->items[2] instanceof MP_String) {
                $r['answernote'] = $result->items[2]->value;
            } else if ($result->items[2] instanceof MP_List) {
                // This is an odd case... We really should not have differing types.
                $r['answernote'] = $result->items[2]->toString();
            }
            $r['feedback'] = stack_maxima_translate($result->items[3]->value);
            if (strrpos($r['feedback'], '!NEWLINE!') === mb_strlen($r['feedback']) - 9) {
                $r['feedback'] = trim(mb_substr($r['feedback'], 0, -9));
            }
        }
        return $r;
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
