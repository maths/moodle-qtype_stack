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
 * AST filter that prevents any function calls.
 */
class stack_ast_filter_no_functions_041 implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $hasany = false;
        $known = stack_cas_security::get_protected_identifiers('function', $identifierrules->get_units());

        $process = function($node) use (&$hasany, &$errors, $known) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Identifier) {
                if (array_key_exists($node->name->value, $known)) {
                    return true;
                }
                $hasany = true;
                // Insert stars into the patten.
                $errors [] = stack_string('stackCas_unknownFunction',
                        array('forbid' => stack_maxima_format_casstring($node->toString())));
                $nop = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                $nop->position['insertstars'] = true;
                $node->parentnode->replace($node, $nop);
                return false;
            }
            return true;
        };

        while ($ast->callbackRecurse($process, true) !== true) {
        }
        if ($hasany) {
            $answernotes[] = 'unknownFunction';
        }
        return $ast;
    }
}