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

//
// Decimal places answer tests.
//
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//
class stack_anstest_atdecplaces extends stack_anstest {

    public function do_test() {
        $this->atmark = 1;
        $anotes = array();

        // Note that in casting to an integer we are lucky here.
        // Non-integer strings get cast to zero, which is invalid anyway....
        $atestops = (int) $this->atoption;
        if (!is_int($atestops) or $atestops <= 0) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('ATNumDecPlaces_OptNotInt', array('opt' => $this->atoption));
            $this->atansnote    = 'ATNumDecPlaces_STACKERROR_Option.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        $commands = array($this->sanskey, $this->tanskey, (string) $this->atoption);
        foreach ($commands as $com) {
            $cs = new stack_cas_casstring($com);
            if (!$cs->get_valid('t', true, 0)) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $cs->get_errors()));
                $this->atansnote    = 'ATNumDecPlaces_STACKERROR_Option.';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
        }

        // Check that the first expression is a floating point number,
        // with the right number of decimal places.
        $r = stack_utils::decimal_digits($this->sanskey);
        if ($atestops != $r['decimalplaces'] ) {
            $this->atfeedback  .= stack_string('ATNumDecPlaces_Wrong_DPs');
            $anotes[]           = 'ATNumDecPlaces_Wrong_DPs';
            $this->atmark       = 0;
        } else {
            $anotes[]           = 'ATNumDecPlaces_Correct';
        }

        // Check that the two numbers evaluate to the same value.
        $cascommands = array();
        $cascommands[] = "caschat2:ev({$this->atoption},simp)";
        $cascommands[] = "caschat0:ev(float(round(10^caschat2*{$this->sanskey})/10^caschat2),simp)";
        $cascommands[] = "caschat1:ev(float(round(10^caschat2*{$this->tanskey})/10^caschat2),simp)";
        $cascommands[] = "caschat3:ev(second(ATAlgEquiv(caschat0,caschat1)),simp)";
        $cascommands[] = "caschat4:floatnump({$this->sanskey})";

        $cts = array();
        foreach ($cascommands as $com) {
            $cs    = new stack_cas_casstring($com);
            $cs->get_valid('t', true, 0);
            $cts[] = $cs;
        }
        $session = new stack_cas_session($cts, null, 0);
        $session->instantiate();

        if ('' != $session->get_errors_key('caschat0')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('caschat0')));
            $anotes[]           = 'ATNumDecPlaces_STACKERROR_SAns';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $session->get_errors_key('caschat1')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $session->get_errors_key('caschat1')));
            $anotes[]           = 'ATNumDecPlaces_STACKERROR_TAns';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $session->get_errors_key('caschat2')) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $session->get_errors_key('caschat2')));
            $anotes[]           = 'ATNumDecPlaces_STACKERROR_Options.';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ($session->get_value_key('caschat3') == 'true') {
            // Note, we only want the mark to *stay* at 1.
            $this->atmark *= 1;
            $anotes[]      = 'ATNumDecPlaces_Equiv';
        } else {
            $this->atmark = 0;
            $anotes[]     = 'ATNumDecPlaces_Not_equiv';
        }

        if ($session->get_value_key('caschat4') == 'false') {
            $this->atmark = 0;
            $this->atfeedback  = stack_string('ATNumDecPlaces_NoDP');
            $anotes             = array('ATNumDecPlaces_NoDP');
        }

        $this->atansnote = implode('. ', $anotes).'.';
        if ($this->atmark) {
            return true;
        }
        return false;
    }

    public function process_atoptions() {
        return true;
    }

    public function required_atoptions() {
        return true;
    }

    protected function get_casfunction() {
        return 'ATDecimalPlaces';
    }

    /**
     * Validates the options, when needed.
     *
     * @return (bool, errors)
     * @access public
     */
    public function validate_atoptions($opt) {
        $atestops = (int) $opt;
        if (!is_int($atestops) or $atestops <= 0) {
            return array(false, stack_string('ATNumDecPlaces_OptNotInt', array('opt' => $opt)));
        }
        return array(true, '');
    }
}
