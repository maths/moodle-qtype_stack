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
require_once(__DIR__ . '/../../maximaparser/utils.php');

/**
 * AST filter that splits function calls to implied variable names
 * from the same AST.
 *
 * f(x(x+f(1))) => f(x*(x+f(1)))
 *
 * Tags the stars and adds 'missing_stars' answernote.
 */
class stack_ast_filter_406_split_implied_variable_names implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $usage = maxima_parser_utils::variable_usage_finder($ast);

        $process = function($node) use (&$answernotes, $usage) {
            if ($node instanceof MP_FunctionCall &&
                $node->name instanceof MP_Identifier) {
                // Is it something that has also been used as variable?
                if (array_key_exists($node->name->value, $usage['read']) ||
                    array_key_exists($node->name->value, $usage['write'])) {
                    $nop = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                    $nop->position['insertstars'] = true;
                    $node->parentnode->replace($node, $nop);
                    if (array_search('missing_stars', $answernotes) === false) {
                        $answernotes[] = 'missing_stars';
                    }
                    if (array_search('Variable_function', $answernotes) === false) {
                        $answernotes[] = 'Variable_function';
                    }
                    return false;
                }
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process, true) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}