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
 * AST filter that removes floats that have the "e" in them. Will tag
 * the new stars with the 'insertstars' position marker, and adds
 * 'missing_stars' to the answernote.
 *
 * Note that in cases like '1.23e-4' or '5.6E+7' only adds one star and
 * turns that -/+ to an op.
 */
class stack_ast_filter_450_split_floats implements stack_cas_astfilter_exclusion {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $process = function($node) use (&$answernotes) {
            if ($node instanceof MP_Float && $node->raw !== null) {
                $replacement = false;
                if (strpos($node->raw, 'e') !== false) {
                    $parts = explode('e', $node->raw);
                    if (strpos($parts[0], '.') !== false) {
                        $replacement = new MP_Operation('*', new MP_Float(floatval($parts[0]), $parts[0]),
                                new MP_Operation('*', new MP_Identifier('e'), new MP_Integer(intval($parts[1]))));
                    } else {
                        $replacement = new MP_Operation('*', new MP_Integer(intval($parts[0])),
                                new MP_Operation('*', new MP_Identifier('e'), new MP_Integer(intval($parts[1]))));
                    }
                    $replacement->position['insertstars'] = true;
                    if ($parts[1][0] === '-' || $parts[1][0] === '+') {
                        // Forms such as 1e+1...
                        $op = $parts[1][0];
                        $val = abs(intval($parts[1]));
                        $replacement = new MP_Operation($op, new MP_Operation('*', $replacement->lhs,
                                new MP_Identifier('e')), new MP_Integer($val));
                        $replacement->lhs->position['insertstars'] = true;
                    }
                } else if (strpos($node->raw, 'E') !== false) {
                    $parts = explode('E', $node->raw);
                    if (strpos($parts[0], '.') !== false) {
                        $replacement = new MP_Operation('*', new MP_Float(floatval($parts[0]), $parts[0]),
                                new MP_Operation('*', new MP_Identifier('E'), new MP_Integer(intval($parts[1]))));
                    } else {
                        $replacement = new MP_Operation('*', new MP_Integer(intval($parts[0])),
                                new MP_Operation('*', new MP_Identifier('E'), new MP_Integer(intval($parts[1]))));
                    }
                    $replacement->position['insertstars'] = true;
                    if ($parts[1][0] === '-' || $parts[1][0] === '+') {
                        // Forms such as 1.2E-1...
                        $op = $parts[1][0];
                        $val = abs(intval($parts[1]));
                        $replacement = new MP_Operation($op, new MP_Operation('*', $replacement->lhs,
                                new MP_Identifier('E')), new MP_Integer($val));
                        $replacement->lhs->position['insertstars'] = true;
                    }
                }
                if ($replacement !== false) {
                    if (array_search('missing_stars', $answernotes) === false) {
                        $answernotes[] = 'missing_stars';
                    }
                    $node->parentnode->replace($node, $replacement);
                    return false;
                }
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }

    public function conflicts_with(string $otherfiltername): bool {
        if ($otherfiltername === '101_no_floats') {
            return true;
        }
        return false;
    }
}