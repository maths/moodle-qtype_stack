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
 * AST filter that prevents calling function returns.
 */
class stack_ast_filter_no_calling_function_returns_43 implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $hasany = false;

        $process = function($node) use (&$hasany) {
            if ($node instanceof MP_FunctionCall && !$node->name instanceof MP_Identifier) {
                $hasany = true;
                return false;
            }
            return true;
        };

        $ast->callbackRecurse($process);
        if ($hasany) {
            $answernotes[] = 'calling_function_returns';
        }
        return $ast;
    }
}