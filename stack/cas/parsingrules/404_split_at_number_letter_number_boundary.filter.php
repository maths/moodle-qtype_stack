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
 * AST filter that splits variables at number-letter boundaries and
 * at letter-number. e.g. ab3c4 => ab*3*c*4.
 *
 * We don't want to split up x_1 though.
 *
 * Tags the stars and adds 'missing_stars' answernote.
 */
class stack_ast_filter_404_split_at_number_letter_number_boundary implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) use (&$answernotes) {
            if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                // First find the boundaries.
                $splits = [];
                // Type of previous character.
                // This will be true if alpha, false if numeric and null otherwise, e.g. an underscore.
                $alpha = false;
                if (ctype_alpha(mb_substr($node->value, 0, 1))) {
                    $alpha = true;
                }
                $last = 0;
                for ($i = 1; $i < mb_strlen($node->value); $i++) {
                    $now = null;
                    if (ctype_alpha(mb_substr($node->value, $i, 1))) {
                        $now = true;
                    }
                    if (ctype_digit(mb_substr($node->value, $i, 1))) {
                        $now = false;
                    }
                    if (!($alpha === null) && !($now === null) && !($now === $alpha)) {
                        // Don't split at % signs.
                        if (!(mb_substr($node->value, $last, 1) === "%")) {
                            $splits[] = mb_substr($node->value, $last, $i - $last);
                            $last = $i;
                        }
                    }
                    $alpha = $now;
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
