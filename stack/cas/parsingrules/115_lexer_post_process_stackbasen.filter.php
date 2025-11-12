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
 * Post processing for the base-N lexer, turns special integer literals
 * to function calls. Basically, `0xBEEF` -> `stackbasen("0xBEEF","C",16)`,
 * `Zzz_36` -> `stackbasen("Zzz_36","S",36)`...
 * 
 * @package    qtype_stack
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/filter.interface.php');

/**
 * AST filter that converts base-N syntax.
 */
class stack_ast_filter_115_lexer_post_process_stackbasen implements stack_cas_astfilter {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $checkfloats = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Integer && is_string($node->value)) {
                $raw = new MP_String($node->value);
                $base = null;
                $mode = null;
                $match = [];
                if (preg_match('/[0-9a-zA-Z]+_([1-9][0-9]*)/', $node->value, $match) === 1) {
                    $mode = new MP_String('S');
                    $base = new MP_Integer($match[1]);
                } else if (preg_match('/^0([xb0-6])[0-9a-zA-Z]+$/', $node->value, $match) === 1) {
                    $mode = new MP_String('C');
                    if ($match[1] === 'x') {
                        $base = new MP_Integer('16');
                    } else if ($match[1] === 'b') {
                        $base = new MP_Integer('2');
                    } else {
                        $base = new MP_Integer('8');
                    }
                }
                if ($base !== null) {
                    $node->parentnode->replace($node, new MP_Functioncall(new MP_Identifier('stackbasen'), [$raw, $mode, $base]));
                    // Even though we change the tree we do not need to return
                    // false as we are only touching leaves and the same filter
                    // does nto need to see the result.
                }
            }
            return true;
        };

        $ast->callbackRecurse($checkfloats);
        return $ast;
    }
}
