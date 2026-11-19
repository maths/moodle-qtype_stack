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
require_once(__DIR__ . '/../../utils.class.php');

/**
 * AST filter that examines whether we have a pattern like a*x*b which might have arisen from axb, indicating
 * x has been used to indicate multiplication.  Typically 23.2 x 10^b, which is why we look for an identifier x10.
 *
 * Intended originally to be used by the unit input.
 */
class stack_ast_filter_210_x_used_as_multiplication implements stack_cas_astfilter {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function ($node) use (&$answernotes, &$errors) {
            // @codingStandardsIgnoreStart
            // The kind of patterns we want are x*(10^? * ?), x*10^?, x*(10^? / ?).
            // Case 1.
            // x*10^a => ([Op: *] ([Id] x), ([Op: ^] ([Int] 10), ([Id] a)))
            //            ------- ........  -------------------

            // Case 2.
            // 523.2 x 10^2m
            // ([Root] ([Op: *] ([Op: *] ([Op: *] ([Float] 523.2), ([Id] x)), ([Op: ^] ([Int] 10), ([Int] 2))), ([Id] m)))
            //                  --------                           ..........  -------------------                        

            // Case 3.
            // 523.2 x 10^2Nm
            // ([Root] ([Op: *] ([Op: *] ([Op: *] ([Op: *] ([Float] 523.2), ([Id] x)), ([Op: ^] ([Int] 10), ([Int] 2))), ([Id] N)), ([Id] m)))
            //                            -------                            ........    ------------------

            // Case 4.
            // 523.2 x 10^2m/s
            // ([Root] ([Op: /] ([Op: *] ([Op: *] ([Op: *] ([Float] 523.2), ([Id] x)), ([Op: ^] ([Int] 10), ([Int] 2))), ([Id] m)), ([Id] s)))
            //                            -------                           .........   ------------------

            // Case 5.
            // 9.81*x*10^2*m/s/s
            // ([Op: /] ([Op: /] ([Op: *] ([Op: *] ([Op: *] ([Float] 9.81), ([Id] x)), ([Op: ^] ([Int] 10), ([Int] 2))), ([Id] m)), ([Id] s)), ([Id] s))
            //                             -------                         ..........  -------------------
            

            // @codingStandardsIgnoreEnd


            // Covers cases 2-5.
            if (
                $node instanceof MP_Operation &&
                    $node->op === '*' &&
                    $node->rhs instanceof MP_Identifier && $node->rhs->value === 'x' &&
                    $node->parentnode instanceof MP_Operation && $node->parentnode->op === '*' &&
                    $node->parentnode->lhs === $node && $node->parentnode->rhs instanceof MP_Operation &&
                    $node->parentnode->rhs->op === '^' &&
                    // Don't use the strict === below, as MP_Integer values can be integers.
                    $node->parentnode->rhs->lhs instanceof MP_Integer && $node->parentnode->rhs->lhs->value == '10'
            ) {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_x10';
                $errors[] = stack_string('Illegal_x10');
                return false;
            }

            // Covers case 1.
            if (
                $node instanceof MP_Operation &&
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
