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
 * AST filter that consolidates subscripted atoms in the form M_1 into M1.
 * Consolidating students' input in this way makes it less likley they will be penalised
 * on a technicality.
 *
 * By design this filter only looks for very basic patterns, and ignores double subscripts.
 */
class stack_ast_filter_420_consolidate_subscripts implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $protected = stack_cas_security::get_protected_identifiers('variable', $identifierrules->get_units());

        $process = function($node) use (&$valid, &$errors, &$answernotes, $protected) {
            if ($node instanceof MP_Identifier && !$node->is_function_name()) {

                if (preg_match('/^[a-zA-Z]+_[0-9]+$/', $node->value, $matches)) {
                    $answernotes[] = 'consolidate_subscripts';
                    $node->value = str_replace('_', '', $node->value);
                }
                return true;
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}
