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
 * AST filter that rewrites `sconcat` calls to `simplode` calls if
 * the argument count is too high. To be used whenever we might
 * have built these calls.
 *
 * The reason for this is that GCL Lisp has had an issue with functions
 * that have large numbers of arguments.
 */
class stack_ast_filter_680_gcl_sconcat implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $simplode = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Functioncall) {
                if ($node->name instanceof MP_Identifier && $node->name->value === 'sconcat') {
                    // The GCL thing. Old used lreduce/sconcat but simplode is probably faster.
                    if (count($node->arguments) > 40) {
                        $replacement = new MP_FunctionCall(new MP_Identifier('simplode'),
                            [new MP_List($node->arguments)]);
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    }
                }
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($simplode) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}
