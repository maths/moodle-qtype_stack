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
 * AST filter that renames `help` if used anywhere to `%stackhelp`.
 * We don't put underscores in this atom in case we later try to split subscripts.
 * This is done because `help` does interesting things in Maxima and we
 * want to allow one to redefine it to do something else.
 *
 * E.g. target bespoke validation or texput-rules.
 */
class stack_ast_filter_040_help_rename implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) {
            if ($node instanceof MP_Identifier && $node->value === stack_string('stack_help')) {
                $node->value = '%stackhelp';
                return false;
            } else if ($node instanceof MP_String && $node->value === stack_string('stack_help') &&
                    $node->parentnode instanceof MP_FunctionCall &&
                    $node->parentnode->name->toString() === 'texput' &&
                    $node->parentnode->arguments[0] === $node) {
                // Special case for those that use "strings" with texput.
                $node->value = '%stackhelp';
                return false;
            }
            return true;
        };

        while ($ast->callbackRecurse($process, true) !== true) {}
        return $ast;
    }
}
