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
 * AST filter turns all evaluation groups into tuples.
 * Does not change function calls, or arguments of operators, so x/(y+z) is not changed.
 */
class stack_ast_filter_504_insert_tuples_for_groups implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $insettuples = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Group && count($node->items) > 1 && !($node->is_in_operation())) {
                // Guard clause to allow nested tuples, but not other function calls.
                if ($node->parentnode instanceof MP_FunctionCall) {
                    if (!$node->parentnode->name->toString() == 'ntuple') {
                        return false;
                    }
                }
                $nop = new MP_FunctionCall(new MP_Identifier('ntuple'), $node->getChildren());
                $node->parentnode->replace($node, $nop);
                return false;
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($insettuples) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}
