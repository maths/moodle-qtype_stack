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
 * AST filter that spots inequalities that are chained.
 */
class stack_ast_filter_050_no_chained_inequalities implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $ops = array('<' => '<', '<=' => '<=', '>' => '>', '>=' => '>=',
            '#' => '#', '=' => '=');

        $process = function($node) use (&$valid, &$errors, &$answernotes, $ops) {
            if ($node instanceof MP_Operation && isset($ops[$node->op])) {
                if (($node->lhs instanceof MP_Operation && isset($ops[$node->lhs->op])) ||
                    ($node->rhs instanceof MP_Operation && isset($ops[$node->rhs->op]))) {
                    $node->position['invalid'] = true;
                    $errors[] = stack_string('stackCas_chained_inequalities');
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