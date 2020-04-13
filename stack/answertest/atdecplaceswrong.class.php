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
class stack_anstest_atdecplaceswrong extends stack_anstest {

    protected $atname = 'NumDecPlacesWrong';

    protected $casfunction = 'ATDecimalPlacesWrong';

    public function do_test() {
        $this->atmark = 1;
        $anotes = array();

        $commands = array($this->sanskey, $this->tanskey, $this->atoption);
        foreach ($commands as $com) {
            if (!$com->get_valid()) {
                $this->aterror      = 'TEST_FAILED';
                $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
                $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $com->get_errors()));
                $this->atansnote    = 'ATNumDecPlacesWrong_STACKERROR_Option.';
                $this->atmark       = 0;
                $this->atvalid      = false;
                return null;
            }
        }

        $atestops = $this->atoption->get_evaluationform();
        if ($this->atoption->is_evaluated()) {
            $atestops = $this->atoption->get_value();
        }

        if (!$this->atoption->get_valid() || !ctype_digit($atestops) || $atestops <= 0) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('ATNumDecPlaces_OptNotInt', array('opt' => $atestops));
            $this->atansnote    = 'ATNumDecPlacesWrong_STACKERROR_Option.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ($this->sanskey->is_float() || $this->sanskey->is_int()) {
            // All good.
            $notused = true;

        } else if ($this->sanskey->is_correctly_evaluated() &&
                ($this->sanskey->is_float(true) || $this->sanskey->is_int())) {
            // This is not great, but it happens when the answer test is applied
            // to a feedback variable, rather than a raw input. E.g. if someone
            // has done sansmin: min(sans1, sans2) in a quadratic question.
            $this->atansnote    = 'ATNumDecPlacesWrong_SA_variable.';

        } else {
            $this->atfeedback   = stack_string('ATNumDecPlaces_Float');
            $this->atansnote    = 'ATNumDecPlacesWrong_SA_Not_num.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
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
        $cascommands['caschat0'] = $sans;
        $cascommands['caschat1'] = 'ev(remove_displaydp('.$tans.'),simp)';
        $cascommands['caschat2'] = "ev({$atestops},simp)";
        $cascommands['caschat3'] = "numberp({$sans})";
        $cascommands['caschat4'] = "numberp(caschat1)";

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
            $anotes[]           = 'ATNumDecPlacesWrong_STACKERROR_SAns';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $strings['caschat1']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => $strings['caschat1']->get_errors()));
            $anotes[]           = 'ATNumDecPlacesWrong_STACKERROR_TAns';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $strings['caschat2']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback   = stack_string('TEST_FAILED', array('errors' => ''));
            $this->atfeedback  .= stack_string('AT_InvalidOptions', array('errors' => $strings['caschat2']->get_errors()));
            $anotes[]           = 'ATNumDecPlacesWrong_STACKERROR_Option';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $strings['caschat3']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback  .= stack_string('TEST_FAILED', array('errors' => $strings['caschat3']->get_errors()));
            $anotes[]           = 'ATNumDecPlacesWrong_ERR_sansnum';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('' != $strings['caschat4']->get_errors()) {
            $this->aterror      = 'TEST_FAILED';
            $this->atfeedback  .= stack_string('TEST_FAILED', array('errors' => $strings['caschat4']->get_errors()));
            $anotes[]           = 'ATNumDecPlacesWrong_ERR_tansnum';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        // These should not throw an error. The test just returns false.
        if ('false' === $strings['caschat3']->get_value()) {
            $anotes[]           = 'ATNumDecPlacesWrong_Sans_Not_Num';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        if ('false' === $strings['caschat4']->get_value()) {
            $anotes[]           = 'ATNumDecPlacesWrong_Tans_Not_Num';
            $this->atansnote    = implode('. ', $anotes).'.';
            $this->atmark       = 0;
            $this->atvalid      = false;
            return null;
        }

        $ndps = $strings['caschat2']->get_value();
        // We use the raw values here to preserve DPs.
        $sa = $this->sanskey;
        $ta = $this->tanskey;

        // Ignore the decimal point by eliminating it.
        $sa = str_replace('.', '', $sa);
        $ta = str_replace('.', '', $ta);

        // Remove any leading zeros.
        $sa = substr($sa, strcspn($sa, '123456789'));
        $ta = substr($ta, strcspn($ta, '123456789'));

        // Add sufficient trailing zeros.
        // This condones any lack of trailing zeros (for this test).
        $sa .= str_repeat('0', (int) $ndps);
        $ta .= str_repeat('0', (int) $ndps);

        $sa = substr($sa, 0, (int) $ndps);
        $ta = substr($ta, 0, (int) $ndps);
        if ($sa == $ta) {
            // Note, we only want the mark to *stay* at 1.
            $this->atmark  = 1;
            $anotes[]      = 'ATNumDecPlacesWrong_Correct';
        } else {
            $this->atmark = 0;
            $anotes[]     = 'ATNumDecPlacesWrong_Wrong';
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
            return array(false, stack_string('ATNumDecPlacesWrong_OptNotInt', array('opt' => $opt)));
        }
        return array(true, '');
    }
}
