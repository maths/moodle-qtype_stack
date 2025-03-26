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

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_ast_filter_908_parsons_decode_state_for_display implements stack_cas_astfilter {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $strings = function($node) use (&$answernotes, &$errors) {
            // We validate the node to check that it is a string that represents a Parson's state.
            // This is not strictly required as it is prevented by `$node instanceof MP_String`, but it is an additional safety
            // measure to ensure we do not dehash other strings.
            if ($node instanceof MP_String && stack_utils::validate_parsons_string($node->value)) {
                $node->value = stack_utils::unhash_parsons_string($node->value);
            }

            return true;
        };

        $ast->callbackRecurse($strings);
        return $ast;
    }
}
