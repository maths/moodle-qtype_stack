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
 * @copyright  2020 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_answertest_general_cas_preprepare extends stack_answertest_general_cas {

    /**
     * @param  string $sans
     * @param  string $tans
     * @param  string $casoption
     */
    public function __construct(stack_ast_container $sans, stack_ast_container $tans, string $atname,
            $atoption = null, $options = null) {

        if ($sans->get_valid()) {
            $sa = $sans->get_inputform(true, 1);
            // Don't wrap strings twice.
            if (!$sans->is_string()) {
                $sa = '"' . $sa . '"';
            }
            if ($sans->is_correctly_evaluated()) {
                $sa = $sans->get_value();
                if (!$sans->is_string(true)) {
                    $sa = '"' . $sa . '"';
                }
            }
            $sans = stack_ast_container::make_from_teacher_source($sa, '', new stack_cas_security());
        } else {
            $this->atansnote    = $this->casfunction.'TEST_FAILED:Invalid SA.';
        }
        $ta = '';
        if ($tans->get_valid()) {
            $ta = $tans->get_inputform(true, 1);
            if (!$tans->is_string()) {
                $ta = '"' . $ta . '"';
            }
            if ($tans->is_correctly_evaluated()) {
                $ta = $tans->get_value();
                if (!$tans->is_string(true)) {
                    $ta = '"' . $ta . '"';
                }
            }
            $tans = stack_ast_container::make_from_teacher_source($ta, '', new stack_cas_security());
        } else {
            $this->atansnote    = $this->casfunction.'TEST_FAILED:Invalid TA.';
        }

        parent::__construct($sans, $tans, $atname, $options, $atoption);

        $this->casfunction       = 'AT'. $atname;
        $this->atname            = $atname;
        $this->simp              = stack_ans_test_controller::simp($atname);
    }
}
