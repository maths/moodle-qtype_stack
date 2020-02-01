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
require_once(__DIR__ . '/../../utils.class.php');

/**
 * AST filter that rewrites floats as display functions to ensure
 * that the representation of them does not change when going through
 * Maxima. Essentially this:
 *
 *  1.32    => dispdp(1.32, 2)
 *  0.00001 => dispdp(0.00001, 5)
 *  0.04e7  => displaysci(0.04, 2, 7)
 *
 * This filter will eliminate the 'e' vs 'E' issue by making all values
 * so inputted to be displayed as powers.
 *
 * Note that applying this filter on ASTs going for evaluation makes no
 * sense. This is for representation.
 */
class stack_ast_filter_910_inert_float_for_display implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $floats = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_Float) {
                $raw = strtolower($node->raw);
                if ($node->parentnode instanceof MP_FunctionCall &&
                        $node->parentnode->name instanceof MP_Identifier &&
                        ($node->parentnode->name->value === 'dispdp' ||
                        $node->parentnode->name->value === 'displaysci')) {
                    // Don't break things just fixed.
                    return true;
                }
                $dp = 0;
                if (strpos($raw, '.') !== false) {
                    $parts = explode('.', $raw);
                    $dp = strlen(explode('e', $parts[1])[0]);
                }
                $replacement = null;
                if (strpos($raw, 'e') === false) {
                    $replacement = new MP_FunctionCall(new MP_Identifier('dispdp'), [$node, new MP_Integer($dp, '' . $dp)]);
                } else {
                    $p = intval(explode('e', $raw)[1]);
                    if ($p < 0) {
                        $p = new MP_PrefixOp('-', new MP_Integer(-$p, '' . (-$p)));
                    } else {
                        $p = new MP_Integer($p, '' . $p);
                    }
                    $m = explode('e', $raw)[0];
                    if (strpos($m, '.') === false) {
                        $m = new MP_Integer(intval($m), $m);
                    } else {
                        $m = new MP_Float(floatval($m), $m);
                    }
                    $replacement = new MP_FunctionCall(new MP_Identifier('displaysci'), [$m, new MP_Integer($dp, '' . $dp), $p]);
                }

                $node->parentnode->replace($node, $replacement);
                return false;
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($floats) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}