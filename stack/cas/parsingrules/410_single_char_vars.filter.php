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
 * AST filter that splits variable names into single characters.
 * Or longest known variable names.
 */
class stack_ast_filter_410_single_char_vars implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        // TODO: do we maybe want to use the allowed words here also?
        // although then allowed words should be typed, to get the best results.

        // Get the list/map of protected variable names and constants.
        $protected = stack_cas_security::get_protected_identifiers('variable', $identifierrules->get_units());

        $process = function($node) use (&$valid, &$errors, &$answernotes, $protected) {
            if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                // Cannot split further.
                if (mb_strlen($node->value) === 1) {
                    return true;
                }

                // If the identifier is a protected one stop here.
                if (array_key_exists($node->value, $protected)) {
                    return true;
                }

                // If it starts with any know identifier split after that.
                for ($l = mb_strlen($node->value); $l > 0; $l--) {
                    $prefix = mb_substr($node->value, 0, $l);
                    if (array_key_exists($prefix, $protected)) {
                        // It is the longest prefix, lets split.
                        $remainder = mb_substr($node->value, $l);
                        if (mb_substr($remainder, 0, 1) === '_') {
                            return true;
                        }
                        if (ctype_digit($remainder)) {
                            $remainder = new MP_Integer($remainder);
                        } else {
                            $remainder = new MP_Identifier($remainder);
                        }
                        $replacement = new MP_Operation('*', new MP_Identifier($prefix), $remainder);
                        $replacement->position['insertstars'] = true;
                        $node->parentnode->replace($node, $replacement);
                        if (array_search('missing_stars', $answernotes) === false) {
                            $answernotes[] = 'missing_stars';
                        }
                        return false;
                    }
                }

                // Don't split up subscripts here.
                if (mb_substr($node->value, 0, 1) === '_' || mb_substr($node->value, 1, 1) === '_') {
                    return true;
                }
                // TODO: more subtle case of ab_cd -> a*b_c*d rather than a*b_cd.
                // This is enough for now, and doesn't break Maxima.

                // If it does not start with a known identifier split the first char.
                $remainder = mb_substr($node->value, 1);
                if (ctype_digit($remainder)) {
                    $remainder = new MP_Integer($remainder);
                } else {
                    $remainder = new MP_Identifier($remainder);
                }
                $firstchar = mb_substr($node->value, 0, 1);
                if (ctype_digit($firstchar)) {
                    $firstchar = new MP_Integer($firstchar);
                } else {
                    $firstchar = new MP_Identifier($firstchar);
                }
                $replacement = new MP_Operation('*', $firstchar, $remainder);
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