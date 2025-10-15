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
    const TEXPLAIN   = 5; // TeX output with tex_plain_atoms:true.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Constant
    const NOTES      = 6;

    /**
     * Raw data should be in the following form.
     * Input, as a raw (but syntactically valid, string.
     * Maxima representation
     * TeX string
     */
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected static $rawdata = [
        // These are 'easy' examples, no numbers/Greek letters/etc in base & subscript.
        ['v_s', 'v_s', '!', '{v}_{s}', '!', '!'],
        // Numbers in subscripts, no letters (but superscripts possible).
        ['v_0', 'v_0', '!', '{v}_{0}', '!', '!'],
        ['Mz_23', 'Mz_23', '!', '{{\it Mz}}_{23}', '!', '!'],
        [
            'r_01', 'r_01', '!', '{r}_{1}', '!', '!',
            'Multiplication by zero here removes the leading zero.',
        ],
        ['v_1^2', 'v_1^2', '!', '{{v}_{1}}^2', '!', '!'],
        [
            'v01', 'v01', '!', 'v_{1}', '!', '{\it v01}',
            'Note, Maxima (and STACK) miss out the "0" in the normal tex display.  A long-standing issue.',
        ],
        // Numbers and letters in subscripts, with non-Greek letters (superscripts possible).
        ['v_y0', 'v_y0', '!', '{v}_{y_{0}}', '!', '{v}_{{\it y0}}'],
        ['v_x0^2', 'v_x0^2', '!', '{{v}_{x_{0}}}^2', '!', '{{v}_{{\it x0}}}^2'],
        ['Delta*v_x0', 'Delta*v_x0', '!', '\Delta\,{v}_{x_{0}}', '!', '\Delta\,{v}_{{\it x0}}'],
        ['a_1x', 'a_1x', '!', '{a}_{\text{1x}}', '!', '!'],
        // Numbers and letters in subscripts, with Greek letters.
        ['a_theta1', 'a_theta1', '!', '{a}_{\theta_{1}}', '!', '{a}_{{\it theta1}}'],
        ['Delta*v_xi', 'Delta*v_xi', '!', '\Delta\,{v}_{\xi}', '!', '!', 'xi is a Greek letter...'],
        // Greek letters in the main element.
        ['omega_a', 'omega_a', '!', '{\omega}_{a}', '!', '!'],
        ['omega_0', 'omega_0', '!', '{\omega}_{0}', '!', '!'],
        ['omega_0^2', 'omega_0^2', '!', '{{\omega}_{0}}^2', '!', '!'],
        // Functions in subscripts, units in subscripts, functions present.
        ['P_min', 'P_min', '!', '{P}_{{\it min}}', '!', '!'],
        ['P_max', 'P_max', '!', '{P}_{{\it max}}', '!', '!'],
        [
            'F_net', 'F_net', '!', '{F}_{{\it net}}', '!', '!',
            'The function net is not known to Maxima.  In this case it has been added, ' .
            'but in general students are only permitted to add known tokens with two letters.',
        ],
        ['a_cm', 'a_cm', '!', '{a}_{{\it cm}}', '!', '!', 'Do we mind about Roman typeface here for units?'],
        ['cos(Theta_1)', 'cos(Theta_1)', '!', '\cos \left( {\Theta}_{1} \right)', '!', '!'],
        ['sin(Theta_1)', 'sin(Theta_1)', '!', '\sin \left( {\Theta}_{1} \right)', '!', '!'],
        // Multiple-subscripts.
        ['a_b_c', 'a_b_c', '!', '{{a}_{b}}_{c}', '!', '!'],
        // The underscore can appear within atoms, but it cannot be used as an operator here.
        // We might later create a student input context in which the underscore is an operator.
        // In core Maxima we can't because this used in too many function names.
        ['(a_b)_c', 'invalid', '', '', '!', '!', 'Test associativity'],
        ['a_(b_c)', 'a_(b_c)', '!', '{\it a\_}\left({b}_{c}\right)', '!', '!'],
        // Array-notation.
        ['v[0]', 'v[0]', '!', 'v_{0}', '!', '!'],
        // By default, Maxima drops the leading zeros when it creates subscripts.
        ['a[theta]', 'a[theta]', '!', 'a_{\theta}', '!', '!'],
        ['v[1,2]', 'v[1,2]', '!', 'v_{1,2}', '!', '!'],
        //        ['a[theta]', 'a[theta]', '!', 'a_{\theta}', '!', '!'],//repetition
        ['theta[1]', 'theta[1]', '!', '\theta_{1}', '!', '!'],
        ['theta[a]', 'theta[a]', '!', '\theta_{a}', '!', '!'],
        ['theta[n,m]', 'theta[n,m]', '!', '\theta_{n,m}', '!', '!'],
        ['v[theta]', 'v[theta]', '!', 'v_{\theta}', '!', '!'],
        // Misc.
        [
            'F_1-2', 'F_1-2', '!', '{F}_{1}-2', '!', '!',
            'How do we bind into a single subscript?  We need a display function',
        ],
        ['U_E,a', 'invalid', '', '', '!', '!', 'We do not accept unencapsulated commas.'],
        ['T_1/2', 'T_1/2', '!', '\frac{{T}_{1}}{2}', '!', '!', 'Again, we need to use texsub.'],
        // Use of STACK's texsub.
        ['texsub(a,1*x)', 'texsub(a,1*x)', 'texsub(a,x)', '{a}_{1\,x}', '{a}_{x}', '{a}_{x}'],
        [
            'texsub(F,sequence(1,2))', 'texsub(F,sequence(1,2))', 'texsub(F,sequence(1,2))',
            '{F}_{1, 2}', '{F}_{1, 2}', '{F}_{1, 2}',
        ],
        [
            'texsub(F,1-2)', 'texsub(F,1-2)', 'texsub(F,-1)', '{F}_{1-2}', '{F}_{-1}', '{F}_{-1}',
            'How do we bind into a single subscript?  We need a display function',
        ],
        ['texsub(r,0*1)', 'texsub(r,0*1)', 'texsub(r,0)', '{r}_{0\,1}', '{r}_{0}', '{r}_{0}'],
        ['texsub(texsub(a,b),c)', 'texsub(texsub(a,b),c)', '!', '{{a}_{b}}_{c}', '!', '!'],
        ['texsub(a,texsub(b,c))', 'texsub(a,texsub(b,c))', '!', '{a}_{{b}_{c}}', '!', '!'],
        ['texsub(T,1/2)', 'texsub(T,1/2)', '!', '{T}_{\frac{1}{2}}', '!', '!'],
        // Subscripts 'inserted' by software when tailing numbers are present,
        // (possibly with Greek letters in the main element).
        ['v0', 'v0', '!', 'v_{0}', '!', '{\it v0}'],
        [
            'v01', 'v01', '!', 'v_{1}', '!', '{\it v01}',
            'Note, Maxima (and STACK) miss out the "0" in the normal tex display.  A long-standing issue.',
        ],
        ['ab1', 'ab1', '!', '{\it ab}_{1}', '!', '{\it ab1}'],
        ['ab001', 'ab001', '!', '{\it ab}_{1}', '!', '{\it ab001}'],
        ['Theta1', 'Theta1', '!', '\Theta_{1}', '!', '{\it Theta1}'],
        ['Theta01', 'Theta01', '!', '\Theta_{1}', '!', '{\it Theta01}'],
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
        $test->texplain      = $data[self::TEXPLAIN];
        $test->notes         = '';
        if (array_key_exists(self::NOTES, $data)) {
            $test->notes = $data[self::NOTES];
        }
        $test->valid         = '';
        $test->value         = '';
        $test->display       = '';
        $test->plaindisplay  = '';
        $test->errors        = '';
        return $test;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function run_test($test, $simp) {
        $sec = new stack_cas_security();

        $cs = [];
        $cs[] = 'p:'.$test->rawinput;
        $cs[] = 'tex_plain_atoms:true';
        $cs[] = 'q:'.$test->rawinput;
        foreach ($cs as $s) {
            $cs = stack_ast_container::make_from_student_source($s, 'subscripts_fixtures', $sec);
            $cs->get_valid();
            $s1[] = $cs;
        }
        $options = new stack_options();
        $options->set_option('simplify', $simp);
        $options->set_option('multiplicationsign', 'space');

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
        $cs = $s1[2];
        $test->plaindisplay = '';
        if ($cs->is_correctly_evaluated()) {
            $test->plaindisplay = $cs->get_display();
        }
        return($test);
    }
}
