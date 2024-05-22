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
 * AST filter that marks everything that has been fixed by inserting
 * stars as invalid.
 */
class stack_ast_filter_991_no_fixing_stars implements stack_cas_astfilter_exclusion {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $stars = false;

        $check = function($node) use (&$stars) {
            if (isset($node->position['insertstars'])) {
                $stars = true;
                $node->position['invalid'] = true;
            }
            return true;
        };

        $ast->callbackRecurse($check, false);

        // Now that those have been checked and invalidated. Let's write custom errors.
        if ($stars === true) {
            $missingstring = $ast->toString(['insertstars_as_red' => true, 'qmchar' => true, 'inputform' => true]);
            if ($ast instanceof MP_Root) {
                // If MP_Root then it ads ";\n" to the string after statement.
                $missingstring = mb_substr($missingstring, 0, -2);
            }
            $a = [];
            $a['cmd']  = stack_maxima_format_casstring($missingstring);
            // This is an error worthy of being at the top.
            array_unshift($errors, stack_string('stackCas_MissingStars', $a));
        }

        return $ast;
    }

    public function conflicts_with(string $otherfiltername): bool {
        if ($otherfiltername === '999_strict') {
            return true;
        }
        return false;
    }
}
