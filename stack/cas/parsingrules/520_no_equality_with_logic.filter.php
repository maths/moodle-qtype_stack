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
 * AST filter that spots statements like 'x=1 or 2' and 'x=1 and 2'.
 */
class stack_ast_filter_520_no_equality_with_logic implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        // The logic is that if you have a logic operation and one side has
        // an equality operation the other one must also have such.

        $process = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Operation && (($node->op === 'or') ||
                $node->op === 'and') && (($node->lhs instanceof MP_Operation) ||
                $node->rhs instanceof MP_Operation)) {

                $lefteq = $node->lhs instanceof MP_Operation && $node->lhs->op === '=';
                $righteq = $node->rhs instanceof MP_Operation && $node->rhs->op === '=';

                if ($node->op === 'and' && ($righteq !== $lefteq)) {
                    // TODO: maybe point out that it cannot be both at the same time?
                    $node->position['invalid'] = true;
                    if (array_search('Bad_assignment', $answernotes) === false) {
                        $answernotes[] = 'Bad_assignment';
                    }
                } else if ($node->op === 'or' && ($righteq !== $lefteq)) {
                    $node->position['invalid'] = true;
                    if (array_search('Bad_assignment', $answernotes) === false) {
                        $answernotes[] = 'Bad_assignment';
                    }
                }
            }
            return true;
        };

        $ast->callbackRecurse($process, true);

        return $ast;
    }
}