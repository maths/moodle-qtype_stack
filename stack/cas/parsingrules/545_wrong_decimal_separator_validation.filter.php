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
 * AST filter to make sure dot is not used between numbers.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/filter.interface.php');
require_once(__DIR__ . '/../../utils.class.php');

/**
 * AST filter to make sure dot is not used between numbers.
 * This is a sign students have used "." instead of ",".
 *
 * This filter needs to come before 910_inert_float_for_display to make it easy to decide if we have floats.
 */
class stack_ast_filter_545_wrong_decimal_separator_validation implements stack_cas_astfilter {

    /*
     * This function decides if we have something which looks like a number.
     */
    private function looks_like_number($ast) {
        if ($ast instanceof MP_Integer) {
            return true;
        }
        if ($ast instanceof MP_Float) {
            return true;
        }
        return false;
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $process = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Operation && $node->op === ".") {
                // We need to check both integers and floats, as in sets of numbers we have {1.2,3}.
                // For simplicity, this check needs to be done before any dispdp functions are applied.
                if ($this->looks_like_number($node->lhs) && $this->looks_like_number($node->rhs)) {
                    $errors[] = stack_string('stackCas_decimal_usedcomma');
                    $answernotes[] = 'forbiddenCharDecimal';
                    $node->position['invalid'] = true;
                    return false;
                }
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        $ast->callbackRecurse($process, true);
        return $ast;
        // @codingStandardsIgnoreEnd
    }
}

