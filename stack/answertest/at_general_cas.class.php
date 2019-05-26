<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk//
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
 * General answer test which connects to the CAS - prevents duplicate code.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_answertest_general_cas extends stack_anstest {

    /**
     * $var bool Are options processed by the CAS.
     */
    private $processcasoptions;

    /**
     * $var bool Are options required for this test.
     */
    private $requiredoptions;

    /**
     * $var bool If this variable is set to true or false we override the
     *      simplification options in the CAS variables.
     */
    private $simp;

    /**
     * @param  string $sans
     * @param  string $tans
     * @param  string $casoption
     */
    public function __construct($sans, $tans, $casfunction, $processcasoptions = false,
            $atoption = null, $options = null, $simp = false, $requiredoptions = false) {
        parent::__construct($sans, $tans, $options, $atoption);

        if (!is_bool($processcasoptions)) {
            throw new stack_exception('stack_answertest_general_cas: processcasoptions, must be Boolean.');
        }

        if (!is_bool($requiredoptions)) {
            throw new stack_exception('stack_answertest_general_cas: requiredoptions, must be Boolean.');
        }

        $this->casfunction       = $casfunction;
        $this->atname            = $casfunction;
        $this->processcasoptions = $processcasoptions;
        $this->requiredoptions   = $requiredoptions;
        $this->simp              = (bool) $simp;
    }

    /**
     *
     *
     * @return bool
     * @access public
     */
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

        if ($this->processcasoptions) {
            if (null == $this->atoption or '' == $this->atoption) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => stack_string("AT_MissingOptions")));
                $this->atansnote    = 'STACKERROR_OPTION.';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            } else {
                // Validate with teacher privileges, strict syntax & no automatically adding stars.
                $ct  = stack_ast_container::make_from_teacher_source($this->atoption, '', new stack_cas_security());

                if (!$ct->get_valid()) {
                    $this->aterror      = 'TEST_FAILED';
                    $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                    $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $ct->get_errors()));
                    $this->atansnote    = 'STACKERROR_OPTION.';
                    $this->atmark       = 0;
                    $this->atvalid      = false;
                    return null;
                }
            }
        }

        // Sort out options.
        if (null === $this->options) {
            $this->options = new stack_options();
        }
        if (!(null === $this->simp)) {
            $this->options->set_option('simplify', $this->simp);
        }

        $sa = clone $this->sanskey;
        $sa->set_key('STACKSA');
        $ta = clone $this->tanskey;
        $sa->set_key('STACKTA');
        $ops = stack_ast_container::make_from_teacher_source('STACKOP:true', '', new stack_cas_security());
        $result = stack_ast_container::make_from_teacher_source("result:{$this->casfunction}(STACKSA,STACKTA)", '',
            new stack_cas_security());
        $op = $this->atoption->get_inputform();
        if (!(!$this->processcasoptions || trim($op === ''))) {
            $ops = clone $this->atoption;
            $ops->set_key('STACKOP');
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

        if ($this->processcasoptions && trim($op) !== '') {
            if ('' != $ops->get_errors() || !$ops->get_valid()) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $ops->get_errors()));
                $this->atansnote    = $this->casfunction.'_STACKERROR_Opt.';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
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

        $this->atansnote  = str_replace("\n", '', trim($unpacked['answernote']));

        // Convert the Maxima string 'true' to PHP true.
        // Actually in the AST parsing we already have a bool.
        if ($unpacked['result']) {
            $this->atmark = 1;
        } else {
            $this->atmark = 0;
        }
        $this->atfeedback = $unpacked['feedback'];
        $this->atvalid    = $unpacked['valid'];
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
            $r['answernote'] = $result->items[2]->value;
            $r['feedback'] = stack_maxima_translate($result->items[3]->value);
        }
        return $r;
    }


    public function process_atoptions() {
        return $this->processcasoptions;
    }

    public function required_atoptions() {
        return $this->requiredoptions;
    }

    public function get_debuginfo() {
        return $this->debuginfo;
    }

    /**
     * Validates the options, when needed.
     *
     * @return (bool, string)
     * @access public
     */
    public function validate_atoptions($opt) {
        if ($this->processcasoptions) {
            $cs = stack_ast_container::make_from_teacher_source($opt, '', new stack_cas_security());
            return array($cs->get_valid(), $cs->get_errors());
        }
        return array(true, '');
    }
}
