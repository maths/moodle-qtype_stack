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
 * AST filter that identifies a specific use case related to trig functions
 * and spaces.
 */
class stack_ast_filter_030_no_trig_space implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $selectednames = stack_cas_security::get_all_with_feature('trigfun');

        $process = function($node) use (&$errors, &$answernotes, $selectednames) {
            if ($node instanceof MP_Identifier &&
                !$node->is_function_name() &&
                $node->parentnode instanceof MP_Operation &&
                $node->parentnode->lhs === $node &&
                $node->parentnode->op === '*' &&
                isset($node->parentnode->position['fixspaces'])) {
                if (array_key_exists($node->value, $selectednames)) {
                    $errors[] = stack_string('stackCas_trigspace',
                            ['trig' => stack_maxima_format_casstring($node->value.'(...)')]);
                    if (array_search('trigspace', $answernotes) === false) {
                        $answernotes[] = 'trigspace';
                    }
                    $node->parentnode->position['invalid'] = true;
                    // TODO: handle the case where we are not the lhs of the shared op.
                }
            }
            return true;
        };
        $ast->callbackRecurse($process);
        return $ast;
    }
}
