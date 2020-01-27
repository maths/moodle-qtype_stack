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
 * AST filter that prevents the use of any programming logic.
 * Just `if` and `do`-keywords, note that one can use some functions
 * to do the same.
 */
class stack_ast_filter_106_no_control_flow implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $checkfloats = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_If ||
                $node instanceof MP_Loop ||
                $node instanceof MP_LoopBit) {
                $node->position['invalid'] = true;
                if (array_search('Illegal_control_flow', $answernotes) === false) {
                    $answernotes[] = 'Illegal_control_flow';
                    $errors[] = stack_string('Illegal_control_flow');
                }
            }
            return true;
        };

        $ast->callbackRecurse($checkfloats);
        return $ast;
    }
}
