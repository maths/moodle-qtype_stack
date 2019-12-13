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

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/filter.interface.php');
require_once(__DIR__ . '/../../utils.class.php');
require_once(__DIR__ . '/201_sig_figs_validation.filter.php');

/**
 * AST filter that examines the decimal places of the leftmost
 * integer or float.
 * Can be tuned to check for desired number of digits.
 */
class stack_ast_filter_202_decimal_places_validation implements stack_cas_astfilter_parametric {
    // Min and max are integer or null, null or values less than
    // 1 signify that there is no limit in the given direction.
    private $min = 3;
    private $max = 3;

    public function set_filter_parameters(array $parameters) {
        $this->min = $parameters['min'];
        $this->max = $parameters['max'];
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
           $root = $ast;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        if ($root instanceof MP_Statement) {
            $root = $root->statement;
        }
        $node = stack_ast_filter_201_sig_figs_validation::get_leftmost_int_or_float($ast);
        if ($node === null) {
            $root->position['invalid'] = true;
            if ($this->min !== null && $this->min > 0) {
                $errors[] = stack_string('numericalinputmindp', $this->min);
            } else {
                $errors[] = stack_string('numericalinputmaxdp', $this->max);
            }
        } else {
            // Hmm. where did stack_utils::decimal_places go?
            // Well this is simpler to do like this.
            $raw = strtolower($node->toString());
            $raw = ltrim($raw, '-'); // Just in case.
            $raw = ltrim($raw, '+');
            $raw = explode('e', $raw)[0];

            $post = '';
            if (strpos($raw, '.') !== false) {
                $post = explode('.', $raw)[1];
            }
            if ($this->min !== null && $this->min > 0) {
                if (strlen($post) < $this->min) {
                    $node->position['invalid'] = true;
                    $errors[] = stack_string('numericalinputmindp', $this->min);
                }
            }
            if ($this->max !== null && $this->max > 0) {
                if (strlen($post) > $this->max) {
                    $node->position['invalid'] = true;
                    $errors[] = stack_string('numericalinputmaxdp', $this->max);
                }
            }
        }
        return $ast;
    }
}

