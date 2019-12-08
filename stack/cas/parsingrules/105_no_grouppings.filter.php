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
 * AST filter that prevents the use of parenthesis for wrapping expressions.
 * Basically, `2*(1+x)` has such a group while `sin(x)` is a function call
 * and groups nothing.
 *
 * This is a different thing than 505_no_evaluation_groups.
 */
class stack_ast_filter_105_no_grouppings implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $checkfloats = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Group && (count($node->items) === 1)) {
                $node->position['invalid'] = true;
                if (array_search('Illegal_groupping', $answernotes) === false) {
                    $answernotes[] = 'Illegal_groupping';
                    $errors[] = stack_string('Illegal_groupping');
                }
            }
            return true;
        };

        $ast->callbackRecurse($checkfloats);
        return $ast;
    }
}
