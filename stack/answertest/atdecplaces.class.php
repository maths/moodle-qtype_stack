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

//
// Decimal places answer tests.
//
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//
class stack_anstest_atdecplaces extends stack_anstest {

    protected $atname = 'NumDecPlaces';

    protected $casfunction = 'ATDecimalPlaces';

    public function do_test() {
        $this->atmark = 1;
        $anotes = array();

        $commands = array($this->sanskey, $this->tanskey, $this->atoption);
        foreach ($commands as $com) {
            if (!$com->get_valid()) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $com->get_errors()));
                $this->atansnote    = 'ATNumDecPlaces_STACKERROR_Option.';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
        }

        // In real questions, these are evaluated but in test cases they may not be.
        // The old "get value" obscured this distinction.
        $atestops = $this->atoption->get_evaluationform();
        if ($this->atoption->is_evaluated()) {
            $atestops = $this->atoption->get_value();
        }

        if (!$this->atoption->get_valid() || !ctype_digit($atestops) || $atestops <= 0) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('ATNumDecPlaces_OptNotInt', array('opt' => $atestops));
            $this->atansnote    = 'ATNumDecPlaces_STACKERROR_Option.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // Check that the first expression is a floating point number,
        // with the right number of decimal places.
        if ($this->sanskey->is_float() || $this->sanskey->is_int()) {
            // All good.
            $r = $this->sanskey->get_decimal_digits();

        } else if ($this->sanskey->is_correctly_evaluated() &&
                ($this->sanskey->is_float(true) || $this->sanskey->is_int(true))) {
            // This is not great, but it happens when the answer test is applied
            // to a feedback variable, rather than a raw input. E.g. if someone
            // has done sansmin: min(sans1, sans2) in a quadratic question.
            $r = $this->sanskey->get_decimal_digits(true);
            // TODO Should we set an answer note, or similar, in this situation?

        } else {
            $this->atfeedback   = stack_string('ATNumDecPlaces_Float');
            $this->atansnote    = 'ATNumDecPlaces_SA_Not_num.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ($atestops != $r['decimalplaces'] ) {
            $this->atfeedback  .= stack_string('ATNumDecPlaces_Wrong_DPs');
            $anotes[]           = 'ATNumDecPlaces_Wrong_DPs';
            $this->atmark       = 0;
        } else {
            $anotes[]           = 'ATNumDecPlaces_Correct';
        }

        $sans = $this->sanskey->get_inputform(true, 1);
        if ($this->sanskey->is_evaluated()) {
            $sans = $this->sanskey->get_value();
        }
        $tans = $this->tanskey->get_inputform(true, 1);
        if ($this->tanskey->is_evaluated()) {
            $tans = $this->tanskey->get_value();
        }
        // Check that the two numbers evaluate to the same value.
        $cascommands = array();
        $cascommands['caschat2'] = "ev({$atestops},simp)";
        $cascommands['caschat0'] = "ev(float(round(10^caschat2*{$sans})/10^caschat2),simp)";
        $cascommands['caschat1'] = "ev(float(round(10^caschat2*remove_displaydp({$tans}))/10^caschat2),simp)";
        $cascommands['caschat3'] = "ev(second(ATAlgEquiv(caschat0,caschat1)),simp)";
        $cascommands['caschat4'] = "floatnump({$sans})";

        $cts = array();
        $strings = array();
        foreach ($cascommands as $key => $com) {
            $cs = stack_ast_container::make_from_teacher_source($key . ':' . $com, '', new stack_cas_security());
            $cts[] = $cs;
            $strings[$key] = $cs;
        }
        $session = new stack_cas_session2($cts, null, 0);
        if ($session->get_valid()) {
            $session->instantiate();
        }

        if ('' != $strings['caschat0']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $strings['caschat0']->get_errors()));
            $anotes[]           = 'ATNumDecPlaces_STACKERROR_SAns';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $strings['caschat1']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $strings['caschat1']->get_errors()));
            $anotes[]           = 'ATNumDecPlaces_STACKERROR_TAns';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $strings['caschat2']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $strings['caschat2']->get_errors()));
            $anotes[]           = 'ATNumDecPlaces_STACKERROR_Options';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ($strings['caschat3']->get_value() == 'true') {
            // Note, we only want the mark to *stay* at 1.
            $this->atmark *= 1;
            $anotes[]      = 'ATNumDecPlaces_Equiv';
        } else {
            $this->atmark = 0;
            $anotes[]     = 'ATNumDecPlaces_Not_equiv';
        }

        if ($strings['caschat4']->get_errors() == 'false') {
            $this->atmark = 0;
            $this->atfeedback  = stack_string('ATNumDecPlaces_NoDP');
            $anotes            = array('ATNumDecPlaces_NoDP');
        }

        $this->atansnote = implode('. ', $anotes).'.';
        if ($this->atmark) {
            return true;
        }
        return false;
    }

    /**
     * Validates the options, when needed.
     *
     * @return (bool, errors)
     * @access public
     */
    public function validate_atoptions($opt) {
        if ($opt == '') {
            return array(false, stack_string('ATNumDecPlaces_OptNotInt', array('opt' => $opt)));
        }
        return array(true, '');
    }
}
