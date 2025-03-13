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
 * AST filter that ensures that 'i(x)' will always be split.
 */
class stack_ast_filter_005_i_is_never_a_function implements stack_cas_astfilter {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) use (&$valid, &$errors, &$answernotes) {
            if ($node instanceof MP_Functioncall &&
                $node->name instanceof MP_Identifier &&
                 $node->name->value === 'i') {
                $replacement = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                $replacement->position['insertstars'] = true;
                $node->parentnode->replace($node, $replacement);
                if (array_search('missing_stars', $answernotes) === false) {
                    $answernotes[] = 'missing_stars';
                }
                return false;
            }

            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}
