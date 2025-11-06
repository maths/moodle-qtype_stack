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

/**
 * AST filter that spots inequalities that are chained.
 */
class stack_ast_filter_050_no_chained_inequalities implements stack_cas_astfilter {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $ops = [
            '<' => '<', '<=' => '\leq ', '>' => '>', '>=' => '\geq',
            '#' => '\neq ', '=' => '=',
        ];

        $process = function($node) use (&$valid, &$errors, &$answernotes, $ops) {
            if ($node instanceof MP_Operation && isset($ops[$node->op])) {
                if (($node->lhs instanceof MP_Operation && isset($ops[$node->lhs->op])) ||
                    ($node->rhs instanceof MP_Operation && isset($ops[$node->rhs->op]))) {
                    $node->position['invalid'] = true;
                    // The error message only uses the first operator (the need not be the same).
                    // But this is a modest improvement.
                    $errors[] = stack_string('stackCas_chained_inequalities',
                        ['op' => $ops[$node->op]]);
                    if (array_search('chained_inequalities', $answernotes) === false) {
                        $answernotes[] = 'chained_inequalities';
                    }
                }
            }

            return true;
        };

        $ast->callbackRecurse($process, true);
        return $ast;
    }
}
