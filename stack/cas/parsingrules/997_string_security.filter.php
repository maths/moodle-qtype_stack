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
 * AST filter that rewrites any strings present in the input by disabling
 * certain chars that would allow script injection.
 *
 * Will not warn about changes, will just break stuff to keep it safe.
 * 
 * Not to be used with author sourced content.
 */
class stack_ast_filter_997_string_security implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) {
            if ($node instanceof MP_String) {
                // Students may not input strings containing specific LaTeX
                // i.e. no math-modes due to us being unable to decide if
                // it is safe.
                $node->value = str_replace('\\[', '\\&#8203;[', $node->value);
                $node->value = str_replace('\\]', '\\&#8203;]', $node->value);
                $node->value = str_replace('\\(', '\\&#8203;(', $node->value);
                $node->value = str_replace('\\)', '\\&#8203;)', $node->value);
                $node->value = str_replace('$$', '$&#8203;$', $node->value);
                // Also any script tags need to be disabled.
                $node->value = str_ireplace('<script', '&lt;&#8203;script', $node->value);
                $node->value = str_ireplace('</script>', '&lt;&#8203;/script&gt;', $node->value);
                $node->value = str_ireplace('<iframe', '&lt;&#8203;iframe', $node->value);
                $node->value = str_ireplace('</iframe>', '&lt;&#8203;/iframe&gt;', $node->value);
            }
            return true;
        };
        $ast->callbackRecurse($process);

        return $ast;
    }
}