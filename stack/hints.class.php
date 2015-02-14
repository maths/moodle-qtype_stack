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
 * Hints for Stack.
 *
 * @copyright  2013 University of Loughborough
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('utils.class.php');

class stack_hints {

    private static $availablehints  = array('greek_alphabet', 'alg_inequalities',
            'alg_indices', 'alg_logarithms', 'alg_quadratic_formula',
            'alg_partial_fractions', 'trig_degrees_radians', 'trig_standard_values',
            'trig_standard_identities', 'hyp_functions', 'hyp_identities', 
            'hyp_inverse_functions', 'calc_diff_standard_derivatives', 'calc_diff_linearity_rule',
            'calc_product_rule', 'calc_quotient_rule', 'calc_chain_rule', 'calc_rules',
            'calc_int_standard_integrals', 'calc_int_linearity_rule', 'calc_int_methods_substitution',
            'calc_int_methods_parts'
        );

    /**
     * Static class. You cannot create instances.
     */
    private function __construct() {
        throw new stack_exception('stack_hints: you cannot create instances of this class.');
    }

    public static function check_bookends($text) {
        return true;
        // TODO:  this fails to match.  stack_utils::check_bookends($text, '[[hint:', ']]');
        // This can be done when we incorporate this into Matti's parser.
    }

    public static function check_hints_exist($text) {
        preg_match_all('|\[\[hint:(.*)\]\]|U', $text, $htmlmatch);
        $unknown = array();
        foreach ($htmlmatch[1] as $val) {
            if (!in_array($val, self::$availablehints)) {
                $unknown[] = $val;
            }
        }
        return $unknown;
    }

    public static function replace_hints($text) {
        preg_match_all('|\[\[hint:(.*)\]\]|U', $text, $htmlmatch);
        foreach ($htmlmatch[1] as $val) {
            $sr = '[[hint:'.$val.']]';
            if (in_array($val, self::$availablehints)) {
                    $rep = '<div class="secondaryFeedback"><h3 class="secondaryFeedback">' .
                            stack_string($val.'_name') . '</h3>' . stack_string($val . '_fact') . '</div>';
            } else {
                $rep = stack_string('stack_hint_missing', $val);
            }
            $text = str_replace($sr, $rep, $text);
        }
        return $text;
    }

    public static function display_all_hints() {
        $str = '';
        foreach (self::$availablehints as $hint) {
            $str .= '<div class="secondaryFeedback"><h3 class="secondaryFeedback">' .
                    stack_string($hint.'_name') . ' (<tt>'.$hint.'</tt>)</h3>' . stack_string($hint . '_fact') . '</div>';
        }
        return $str;
    }
}
