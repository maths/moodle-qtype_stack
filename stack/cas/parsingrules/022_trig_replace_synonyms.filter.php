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
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/filter.interface.php');

/**
 * AST filter that replaces arccos with acos and so on.
 */
class stack_ast_filter_022_trig_replace_synonyms implements stack_cas_astfilter {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        // As these are invalid they do not exist in the security-map.
        $selectednames = [
            'arcsin' => 'asin', 'arccos' => 'acos',
            'arctan' => 'atan', 'arcsec' => 'asec',
            'arccot' => 'acot', 'arccsc' => 'acsc',
            'arcsinh' => 'asinh', 'arccosh' => 'acosh',
            'arctanh' => 'atanh', 'arcsech' => 'asech',
            'arccoth' => 'acoth', 'arccsch' => 'acsch',
            'arccosec' => 'acsc',
            'arsinh' => 'asinh', 'arcosh' => 'acosh',
            'artanh' => 'atanh', 'arsech' => 'asech',
            'arcoth' => 'acoth', 'arcsch' => 'acsch',
        ];

        $process = function($node) use (&$errors, &$answernotes, $selectednames) {
            if ($node instanceof MP_Functioncall &&
                $node->name instanceof MP_Identifier) {
                if (array_key_exists($node->name->value, $selectednames)) {
                    $node->name->value = $selectednames[$node->name->value];

                    if (array_search('triginv', $answernotes) === false) {
                        $answernotes[] = 'triginv';
                    }

                    return true;
                }
            }
            return true;
        };

        $ast->callbackRecurse($process);
        return $ast;
    }
}
