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
 * AST filter that marks everything that has been fixed by fixing
 * spaces as invalid.
 */
class stack_ast_filter_990_no_fixing_spaces implements stack_cas_astfilter_exclusion {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $spaces = false;

        $check = function($node) use (&$spaces) {
            if (isset($node->position['fixspaces'])) {
                $spaces = true;
                $node->position['invalid'] = true;
            }
            return true;
        };

        $ast->callbackRecurse($check, false);

        // Now that those have been checked and invalidated. Let's write custom errors.
        if ($spaces === true) {
            $missingstring = $ast->toString(
                    ['fixspaces_as_red_spaces' => true, 'qmchar' => true, 'inputform' => true]);
            if ($ast instanceof MP_Root) {
                $missingstring = mb_substr($missingstring, 0, -2);
            }
            $a = [];
            $a['expr']  = stack_maxima_format_casstring($missingstring);
            array_unshift($errors, stack_string('stackCas_spaces', $a));
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
