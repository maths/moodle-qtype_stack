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

/**
 * AST filter that identifies a particular family of function names
 * and marks them invalid.
 */
class stack_ast_filter_020_no_arc implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        // As these are invalid they do not exist in the security-map.
        $selectednames = array('arcsin' => 'asin', 'arccos' => 'acos',
             'arctan' => 'atan', 'arcsec' => 'asec',
             'arccot' => 'acot', 'arccsc' => 'acsc',
             'arcsinh' => 'asinh', 'arccosh' => 'acosh',
             'arctanh' => 'atanh', 'arcsech' => 'asech',
             'arccoth' => 'acoth', 'arccsch' => 'acsch',
             'arccosec' => 'acsc');

        $process = function($node) use (&$errors, &$answernotes, $selectednames) {
            if ($node instanceof MP_Functioncall &&
                $node->name instanceof MP_Identifier) {
                if (array_key_exists($node->name->value, $selectednames)) {
                    $node->position['invalid'] = true;

                    $errors[] = stack_string('stackCas_triginv',
                        array('badinv' => stack_maxima_format_casstring($node->name->value),
                              'goodinv' => stack_maxima_format_casstring($selectednames[$node->name->value])));
                    if (array_search('triginv', $answernotes) === false) {
                        $answernotes[] = 'triginv';
                    }
                }
            }
            return true;
        };

        $ast->callbackRecurse($process);
        return $ast;
    }
}