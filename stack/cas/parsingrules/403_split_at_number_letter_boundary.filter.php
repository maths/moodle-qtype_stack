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
 * AST filter that splits variables at number-letter boundaries not
 * at letter-number. e.g. a2b3c4 => a2*b3*c4.
 *
 * Tags the stars and adds 'missing_stars' answernote.
 */
class stack_ast_filter_403_split_at_number_letter_boundary implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) use (&$answernotes) {
            if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                // First find the boundaries.
                $splits = [];
                $alpha = true;
                $last = 0;
                for ($i = 1; $i < mb_strlen($node->value); $i++) {
                    if ($alpha && ctype_digit(mb_substr($node->value, $i, 1))) {
                        $alpha = false;
                    } else if (!$alpha && ctype_alpha(mb_substr($node->value, $i, 1))) {
                        $alpha = false;
                        $splits[] = mb_substr($node->value, $last, $i - $last);
                        $last = $i;
                    }
                }
                $splits[] = mb_substr($node->value, $last);
                // Then if we have more than one part split to parts.
                if (count($splits) > 1) {
                    if (array_search('missing_stars', $answernotes) === false) {
                        $answernotes[] = 'missing_stars';
                    }
                    // Initial identifier is turned to multiplication chain.
                    $temp = new MP_Identifier('rhs');
                    $replacement = new MP_Operation('*', new MP_Identifier($splits[0]), $temp);
                    $replacement->position['insertstars'] = true;
                    $iter = $replacement;
                    $i = 1;
                    for ($i = 1; $i < count($splits) - 1; $i++) {
                        $iter->replace($temp, new MP_Operation('*', new MP_Identifier($splits[$i]), $temp));
                        $iter = $iter->rhs;
                        $iter->position['insertstars'] = true;
                    }
                    $iter->replace($temp, new MP_Identifier($splits[$i]));
                    $node->parentnode->replace($node, $replacement);
                    return false;
                }
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process, true) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}
