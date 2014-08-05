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
 * The hints class for STACK.
 *
 * @copyright  2014 Loughborough University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_hints {

    private $text;

    private static $hints = array('greek_alphabet', 'alg_inequalities',
                    'alg_indices', 'alg_logarithms', 'alg_quadratic_formula', 
                    'alg_partial_fractions', 'trig_degrees_radians', 'trig_standard_values',
                    'trig_standard_identities', 'hyp_functions', 'hyp_identities',
                    'hyp_inverse_functions', 'calc_diff_standard_derivatives',
                    'calc_diff_linearity_rule', 'calc_product_rule', 'calc_quotient_rule',
                    'calc_chain_rule', 'calc_rules', 'calc_int_standard_integrals',
                    'calc_int_linearity_rule', 'calc_int_methods_substitution',
                    'calc_int_methods_parts');

    public function __construct($text) {

        if (!is_string($text)) {
            throw new stack_exception('stack_hints: text, must be a string.');
        }

        $this->text = $text;
    }

    public function display() {

        $strin = $this->text;
        if (preg_match_all('|\[hint:(.*)\]|U', $strin, $html_match)) {
            global $CFG;
            $stackurl = $CFG->wwwroot . '/question/type/stack/';
    
            $modal_script= "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js?ver=1.4.3'></script><script type='text/javascript' src='".$stackurl."jquery.simplemodal.1.4.3.min.js'></script>";
            $strin = $modal_script.$strin; // prepend script
            foreach ($html_match[1] as $val) {
                if (false !== array_search($val, self::$hints)) {
                    $sr = '[hint:'.$val.']';
                    $rep = $this->modal_popup(stack_string($val.'_name'),
                            stack_string($val.'_fact'),//body
                            'Hint' //label on button
                    );
                    $strin = str_replace($sr, $rep, $strin);
                }
            }
        }
        return $strin;
    }

}
