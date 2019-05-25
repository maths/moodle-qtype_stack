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
 * AST filter that prevents any function calls.
 */
class stack_ast_filter_441_split_unknown_functions implements stack_cas_astfilter_exclusion {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $known = stack_cas_security::get_protected_identifiers('function', $identifierrules->get_units());

        $process = function($node) use (&$hasany, &$errors, $known) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Identifier) {
                if (array_key_exists($node->name->value, $known)) {
                    return true;
                }
                // Insert stars into the pattern.
                $nop = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                $nop->position['insertstars'] = true;
                $node->parentnode->replace($node, $nop);
                return false;
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd

        return $ast;
    }

    public function conflicts_with(string $otherfiltername): bool {
        if ($otherfiltername === '542_no_functions_at_all' ||
            $otherfiltername === '442_split_all_functions') {
            return true;
        }
        return false;
    }
}