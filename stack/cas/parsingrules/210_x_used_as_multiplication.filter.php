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
require_once(__DIR__ . '/../../utils.class.php');

/**
 * AST filter that examines whether we have a pattern like a*x*b which might have arisen from axb, indicating
 * x has been used to indicate multiplication.  Typically 23.2 x 10^b, which is why we look for an identifier x10.
 *
 * Intended originally to be used by the unit input.
 */
class stack_ast_filter_210_x_used_as_multiplication implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) use (&$answernotes, &$errors) {
            // @codingStandardsIgnoreStart
            // The kind of patterns we want are x*(10^? * ?), x*10^?, x*(10^? / ?).
            // ([Op: *] ([Id] x), ([Op: *] ([Op: ^] ([Int] 10)

            // 523.2 x 10^2m
            // ([Root] ([Op: *] ([Float] 523.2), ([Op: *] ([Id] x), ([Op: *] ([Op: ^] ([Int] 10), ([Int] 2)), ([Id] m)))))

            // 523.2 x 10^2Nm
            // ([Root] ([Op: *] ([Float] 523.2), ([Op: *] ([Id] x), ([Op: *] ([Op: ^] ([Int] 10), ([Int] 2)), ([Op: *] ([Id] N), ([Id] m))))))

            // 523.2 x 10^2m/s
            // ([Root] ([Op: *] ([Float] 523.2), ([Op: *] ([Id] x), ([Op: *] ([Op: ^] ([Int] 10), ([Int] 2)), ([Op: /] ([Id] m), ([Id] s))))))
            // @codingStandardsIgnoreEnd

            if ($node instanceof MP_Operation &&
                    $node->op === '*' &&
                    $node->lhs instanceof MP_Identifier && $node->lhs->value === 'x' &&
                    $node->rhs instanceof MP_Operation && ($node->rhs->op === '*' || $node->rhs->op === '/') &&
                    $node->rhs->lhs instanceof MP_Operation && $node->rhs->lhs->op === '^' &&
                    // Don't use the strict === below, as MP_Integer values can be integers.
                    $node->rhs->lhs->lhs instanceof MP_Integer && $node->rhs->lhs->lhs->value == '10'
                    ) {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_x10';
                $errors[] = stack_string('Illegal_x10');
                return false;
            }

            if ($node instanceof MP_Operation &&
                    $node->op === '*' &&
                    $node->lhs instanceof MP_Identifier && $node->lhs->value === 'x' &&
                    $node->rhs instanceof MP_Operation && $node->rhs->op === '^' &&
                    $node->rhs->lhs instanceof MP_Integer && $node->rhs->lhs->value == '10'
                    ) {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_x10';
                $errors[] = stack_string('Illegal_x10');
                return false;
            }

            if (($node instanceof MP_Identifier) && $node->value === 'x10') {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_x10';
                $errors[] = stack_string('Illegal_x10');
                return false;
            }
            return true;
        };

        $ast->callbackRecurse($process);

        return $ast;
    }
}

