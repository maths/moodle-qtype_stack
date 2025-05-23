<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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
 * This script checks display of subscript elements.
 *
 * @package    qtype_stack
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_subscripts_test_data {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const RAWINPUT   = 0; // What a student might type.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const MAXIMA     = 1; // Correct maxima syntax, as extended by STACK.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const MAXIMASIMP = 2; // Correct maxima syntax, as extended by STACK, with simp:true.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const TEX        = 3; // TeX output from stack_disp.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const TEXSIMP    = 4; // TeX output from stack_disp with simp:true.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const NOTES      = 5;

    /**
     * Raw data should be in the following form.
     * Input, as a raw (but syntactically valid, string.
     * Maxima representation
     * TeX string
     */
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected static $rawdata = [
        ['Delta*v_x', 'Delta*v_x', '!', '\Delta\,{v}_{x}', '!'],
        ['Delta*v_x0', 'Delta*v_x0', '!', '\Delta\,{v}_{x_{0}}', '!'],
        ['Delta*v_xi', 'Delta*v_xi', '!', '\Delta\,{v}_{\xi}', '!', 'xi is a Greek letter...'],
        ['v_0', 'v_0', '!', '{v}_{0}', '!'],
        ['M_1', 'M_1', '!', '{M}_{1}', '!'],
        ['Mz_23', 'Mz_23', '!', '{{\it Mz}}_{23}', '!'],
        ['v_y0', 'v_y0', '!', '{v}_{y_{0}}', '!'],
        ['v_x0^2', 'v_x0^2', '!', '{{v}_{x_{0}}}^2', '!'],
        ['v_1^2', 'v_1^2', '!', '{{v}_{1}}^2', '!'],
        ['v_s', 'v_s', '!', '{v}_{s}', '!'],
        ['m_a', 'm_a', '!', '{m}_{a}', '!'],
        ['a_x', 'a_x', '!', '{a}_{x}', '!'],
        ['a_cm', 'a_cm', '!', '{a}_{{\it cm}}', '!', 'Do we mind about Roman typeface here for units?'],
        ['texsub(a,1*x)', 'texsub(a,1*x)', 'texsub(a,x)', '{a}_{1\,x}', '{a}_{x}'],
        ['texsub(F,1*x)', 'texsub(F,1*x)', 'texsub(F,x)', '{F}_{1\,x}', '{F}_{x}'],
        [
            'texsub(F,sequence(1,2))', 'texsub(F,sequence(1,2))', 'texsub(F,sequence(1,2))',
            '{F}_{1, 2}', '{F}_{1, 2}',
        ],
        [
            'F_1-2', 'F_1-2', '!', '{F}_{1}-2', '!',
            'How do we bind into a single subscript?  We need a display function',
        ],
        [
            'texsub(F,1-2)', 'texsub(F,1-2)', 'texsub(F,-1)', '{F}_{1-2}', '{F}_{-1}',
            'How do we bind into a single subscript?  We need a display function',
        ],
        ['P_min', 'P_min', '!', '{P}_{{\it min}}', '!'],
        ['P_max', 'P_max', '!', '{P}_{{\it max}}', '!'],
        ['F_max', 'F_max', '!', '{F}_{{\it max}}', '!'],
        [
            'F_net', 'F_net', '!', '{F}_{{\it net}}', '!',
            'The function net is not known to Maxima.  In this case it has been added, ' .
                'but in general studnets are only permitted to add known tokens with two letters.',
        ],
        ['omega_a', 'omega_a', '!', '{\omega}_{a}', '!'],
        ['omega_0', 'omega_0', '!', '{\omega}_{0}', '!'],
        ['omega_0^2', 'omega_0^2', '!', '{{\omega}_{0}}^2', '!'],
        ['r_1', 'r_1', '!', '{r}_{1}', '!'],
        ['r_1^2', 'r_1^2', '!', '{{r}_{1}}^2', '!'],
        [
            'r_01', 'r_01', '!', '{r}_{1}', '!',
            'Multiplication by zero here removes the leading zero.',
        ],
        ['texsub(r,0*1)', 'texsub(r,0*1)', 'texsub(r,0)', '{r}_{0\,1}', '{r}_{0}'],
        [
            'r1', 'r1', '!', 'r_{1}', '!',
            'Maxima displays atoms with tailing numbers using subscripts.  This is not algebraically equivalent to r_1.',
        ],
        ['ab1', 'ab1', '!', '{\it ab}_{1}', '!'],
        ['Theta1', 'Theta1', '!', '\Theta_{1}', '!'],
        ['Theta_1', 'Theta_1', '!', '{\Theta}_{1}', '!'],
        ['cos(Theta_1)', 'cos(Theta_1)', '!', '\cos \left( {\Theta}_{1} \right)', '!'],
        ['sin(Theta_1)', 'sin(Theta_1)', '!', '\sin \left( {\Theta}_{1} \right)', '!'],
        ['U_E,a', 'invalid', '', '', '!', 'We do not accept unencapsulated commas.'],
        ['T_1/2', 'T_1/2', '!', '\frac{{T}_{1}}{2}', '!', 'Again, we need to use texsub.'],
        ['texsub(T,1/2)', 'texsub(T,1/2)', '!', '{T}_{\frac{1}{2}}', '!'],
        ['a_b_c', 'a_b_c', '!', '{{a}_{b}}_{c}', '!'],
        // The underscore can appear within atoms, but it cannot be used as an operator here.
        // We might later create a student input context in which the underscore is an operator.
        // In core Maxima we can't because this used in too many function names.
        ['(a_b)_c', 'invalid', '', '', '!', 'Test associativity'],
        ['a_(b_c)', 'a_(b_c)', '!', '{\it a\_}\left({b}_{c}\right)', '!'],
        ['texsub(texsub(a,b),c)', 'texsub(texsub(a,b),c)', '!', '{{a}_{b}}_{c}', '!'],
        ['texsub(a,texsub(b,c))', 'texsub(a,texsub(b,c))', '!', '{a}_{{b}_{c}}', '!'],
        ['a_theta1', 'a_theta1', '!', '{a}_{\theta_{1}}', '!'],
        [
            'a[1]', 'a[1]', '!', 'a_{1}', '!',
            'Elements of arrays are displayed by subscripts as well.',
        ],
        ['a[theta]', 'a[theta]', '!', 'a_{\theta}', '!'],
        ['theta[1]', 'theta[1]', '!', '\theta_{1}', '!'],
        ['theta[a]', 'theta[a]', '!', '\theta_{a}', '!'],
        ['theta[n,m]', 'theta[n,m]', '!', '\theta_{n,m}', '!'],
        // Changes in v4.3.
        ['a_1x', 'a_1x', '!', '{a}_{\text{1x}}', '!'],
        ['F_1x', 'F_1x', '!', '{F}_{\text{1x}}', '!'],
    ];

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected static $rawdatalegacy = [
        ['Delta*v_x', 'Delta*v_x', '!', '\Delta\,{v}_{x}', '!'],
        ['Delta*v_x0', 'Delta*v_x0', '!', '\Delta\,{v}_{{\it x_0}}', '!'],
        ['Delta*v_xi', 'Delta*v_xi', '!', '\Delta\,{v}_{\xi}', '!', 'xi is a Greek letter...'],
        ['v_0', 'v_0', '!', '{v}_{0}', '!'],
        ['v_y0', 'v_y0', '!', '{v}_{{\it y_0}}', '!'],
        ['v_x0^2', 'v_x0^2', '!', '{{v}_{{\it x_0}}}^2', '!'],
        ['v_1^2', 'v_1^2', '!', '{{v}_{1}}^2', '!'],
        ['v_s', 'v_s', '!', '{v}_{s}', '!'],
        ['m_a', 'm_a', '!', '{m}_{a}', '!'],
        ['a_x', 'a_x', '!', '{a}_{x}', '!'],
        ['texsub(a,1*x)', 'texsub(a,1*x)', 'texsub(a,x)', '{a}_{1\,x}', '{a}_{x}'],
        ['a_cm', 'a_cm', '!', '{a}_{{\it cm}}', '!', 'Do we mind about Roman typeface here for units?'],
        ['texsub(F,1*x)', 'texsub(F,1*x)', 'texsub(F,x)', '{F}_{1\,x}', '{F}_{x}'],
        [
            'F_1-2', 'F_1-2', '!', '{F}_{1}-2', '!',
            'How do we bind into a single subscript?  We need a display function',
        ],
        [
            'texsub(F,1-2)', 'texsub(F,1-2)', 'texsub(F,-1)', '{F}_{1-2}', '{F}_{-1}',
            'How do we bind into a single subscript?  We need a display function',
        ],
        ['P_min', 'P_min', '!', '{P}_{{\it min}}', '!'],
        ['P_max', 'P_max', '!', '{P}_{{\it max}}', '!'],
        ['F_max', 'F_max', '!', '{F}_{{\it max}}', '!'],
        [
            'F_net', 'F_net', '!', '{F}_{{\it net}}', '!',
            'The function net is not known to Maxima.  In this case it has been added, ' .
            'but in general studnets are only permitted to add known tokens with two letters.',
        ],
        ['omega_a', 'omega_a', '!', '{\omega}_{a}', '!'],
        ['omega_0', 'omega_0', '!', '{\omega}_{0}', '!'],
        ['omega_0^2', 'omega_0^2', '!', '{{\omega}_{0}}^2', '!'],
        ['r_1', 'r_1', '!', '{r}_{1}', '!'],
        ['r_1^2', 'r_1^2', '!', '{{r}_{1}}^2', '!'],
        [
            'r_01', 'r_01', '!', '{r}_{1}', '!',
            'Multiplication by zero here removes the leading zero.',
        ],
        ['texsub(r,0*1)', 'texsub(r,0*1)', 'texsub(r,0)', '{r}_{0\,1}', '{r}_{0}'],
        [
            'r1', 'r1', '!', '{\it r_1}', '!',
            'Maxima displays atoms with tailing numbers using subscripts.  This is not algebraically equivalent to r_1.',
        ],
        ['ab1', 'ab1', '!', '{\it ab_1}', '!'],
        ['Theta1', 'Theta1', '!', '{\it Theta_1}', '!'],
        ['Theta_1', 'Theta_1', '!', '{\Theta}_{1}', '!'],
        ['cos(Theta_1)', 'cos(Theta_1)', '!', '\cos \left( {\Theta}_{1} \right)', '!'],
        ['sin(Theta_1)', 'sin(Theta_1)', '!', '\sin \left( {\Theta}_{1} \right)', '!'],
        ['U_E,a', 'invalid', '', '', '!', 'We do not accept unencapsulated commas.'],
        ['T_1/2', 'T_1/2', '!', '\frac{{T}_{1}}{2}', '!', 'Again, we need to use texsub'],
        ['texsub(T,1/2)', 'texsub(T,1/2)', '!', '{T}_{\frac{1}{2}}', '!'],
        ['a_b_c', 'a_b_c', '!', '{{a}_{b}}_{c}', '!'],
        // The underscore can appear within atoms, but it cannot be used as an operator here.
        // We might later create a student input context in which the underscore is an operator.
        // In core Maxima we can't because this used in too many function names.
        ['(a_b)_c', 'invalid', '', '', '!', 'Test associativity'],
        ['a_(b_c)', 'a_(b_c)', '!', '{\it a\_}\left({b}_{c}\right)', '!'],
        ['texsub(texsub(a,b),c)', 'texsub(texsub(a,b),c)', '!', '{{a}_{b}}_{c}', '!'],
        ['texsub(a,texsub(b,c))', 'texsub(a,texsub(b,c))', '!', '{a}_{{b}_{c}}', '!'],
        ['a_theta1', 'a_theta1', '!', '{a}_{{\it theta_1}}', '!'],
        [
            'a[1]', 'a[1]', '!', 'a_{1}', '!',
            'Elements of arrays are displayed by subscripts as well.',
        ],
        ['a[theta]', 'a[theta]', '!', 'a_{\theta}', '!'],
        ['theta[1]', 'theta[1]', '!', '\theta_{1}', '!'],
        ['theta[a]', 'theta[a]', '!', '\theta_{a}', '!'],
        ['theta[n,m]', 'theta[n,m]', '!', '\theta_{n,m}', '!'],
        // Changes in v4.3.
        ['a_1x', 'a_1x', '!', '{a}_{\text{1x}}', '!'],
        ['F_1x', 'F_1x', '!', '{F}_{\text{1x}}', '!'],
    ];

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function get_raw_test_data_legacy() {
        return self::$rawdatalegacy;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function test_from_raw($data) {
        $test = new stdClass();
        $test->rawinput      = $data[self::RAWINPUT];
        $test->maxima        = $data[self::MAXIMA];
        $test->maximasimp    = $data[self::MAXIMASIMP];
        $test->tex           = $data[self::TEX];
        $test->texsimp       = $data[self::TEXSIMP];
        $test->notes         = '';
        if (array_key_exists(self::NOTES, $data)) {
            $test->notes = $data[self::NOTES];
        }
        $test->valid         = '';
        $test->value         = '';
        $test->display       = '';
        $test->errors        = '';
        return $test;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function run_test($test, $simp) {
        $sec = new stack_cas_security();

        $cs = ['p:'.$test->rawinput];
        foreach ($cs as $s) {
            $cs = stack_ast_container::make_from_student_source($s, 'subscripts_fixtures', $sec);
            $cs->get_valid();
            $s1[] = $cs;
        }
        $options = new stack_options();
        $options->set_option('simplify', $simp);
        $options->set_option('multiplicationsign', 'none');

        $at1 = new stack_cas_session2($s1, $options, 0);
        if ($at1->get_valid()) {
            $at1->instantiate();
        }

        $cs = $s1[0];
        $test->valid = $cs->get_valid();
        $test->value = '';
        $test->display = '';
        if ($cs->is_correctly_evaluated()) {
            $test->value = $cs->get_value();
            $test->display = $cs->get_display();
        }
        $test->errors = $cs->get_errors();
        return($test);
    }
}
