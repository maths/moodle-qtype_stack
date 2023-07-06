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
require_once(__DIR__ . '/../../maximaparser/corrective_parser.php');

/**
 * AST filter that check identifiers for the use of chars that are
 * considered supercript. If found converts the expression into its
 * logical equivalent, i.e.,  `x² -> x^2`
 */
class stack_ast_filter_180_char_based_superscripts implements stack_cas_astfilter {

    public static $ssmap = null;

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        if (self::$ssmap === null) {
            self::$ssmap = json_decode(file_get_contents(__DIR__ . '/../../maximaparser/unicode/superscript-stack.json'), true);
        }

        $process = function($node) use (&$errors, &$answernotes) {
            if ($node instanceof MP_Identifier && !(isset($node->position['invalid']) && $node->position['invalid'])) {
                // Iterate over the name to detect when we move from normal to superscript.
                $norm = true;
                // Split to chars.
                $chars = preg_split('//u', $node->value, -1, PREG_SPLIT_NO_EMPTY);
                // Store the segments as detected.
                $segments = [];
                // Build uopt the current one.
                $current = '';
                foreach ($chars as $chr) {
                    if ($norm) {
                        if (isset(self::$ssmap[$chr])) {
                            $segments[] = $current;
                            $current = self::$ssmap[$chr];
                            $norm = false;
                        } else {
                            $current .= $chr;
                        }
                    } else {
                        if (isset(self::$ssmap[$chr])) {
                            $current .= self::$ssmap[$chr];
                        } else {
                            $segments[] = $current;
                            $current = $chr;
                            $norm = true;
                        }
                    }
                }
                if ($current !== '') {
                    $segments[] = $current;
                }

                // Now if we have segments we need to deal with them.
                if (count($segments) > 1) {
                    // Parts between which we have insert stars.
                    // E.g. x²x²x²x -> x^2*x^2*x^2*x.
                    $parts = [];
                    while (count($segments) > 1) {
                        $base = new MP_Identifier(array_shift($segments));
                        $power = array_shift($segments);

                        $power = maxima_corrective_parser::parse($power, $errors, $answernotes, array('startRule' => 'Root',
                                   'letToken' => stack_string('equiv_LET')));
                        // Should there be something truly unexpected.
                        if ($power === null) {
                            $node->position['invalid'] = true;
                            return false;
                        }

                        // There will be only one statement and it is a statement.
                        $power = $power->items[0]->statement;
                        $pow = new MP_Operation('^', $base, $power);
                        if (array_search('superscriptchars', $answernotes) === false) {
                            $answernotes[] = 'superscriptchars';
                        }
                        $parts[] = $pow;
                    }
                    if (count($segments) > 0) {
                        $parts[] = new MP_Identifier(array_shift($segments));
                    }

                    if (count($parts) === 1) {
                        $node->parentnode->replace($node, $parts[0]);
                    } else {
                        if (array_search('missing_stars', $answernotes) === false) {
                            $answernotes[] = 'missing_stars';
                        }
                        $a = new MP_Operation('*', array_shift($parts), array_shift($parts));
                        $a->position['insertstars'] = true;
                        while (count($parts) > 0) {
                            $a = new MP_Operation('*', $a, array_shift($parts));
                            $a->position['insertstars'] = true;
                        }
                        $node->parentnode->replace($node, $a);
                    }
                    return false;
                }
            }
            return true;
        };

        $ast->callbackRecurse($process);
        return $ast;
    }
}
