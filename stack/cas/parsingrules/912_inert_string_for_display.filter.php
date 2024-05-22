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
 * AST filter that rewrites Strings so that they are safe to display through
 * LaTeX rendering.
 *
 * Note That Maxima turns "strings" to \text{ strings } and MathJax etc will
 * do something with that as will the HTML rendering of the browser and we want
 * both to keep the input as it was.
 *
 * That \mbox causes all sorts of issues and it would be better if we could make
 * the Maxima side logic produce properly escaped \text instead. But for now we
 * target some very specific cases separately.
 */
class stack_ast_filter_912_inert_string_for_display implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $strings = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_String) {
                // Were not using htmlspecialchars because the &-?&amp; subsitution messes everything up.
                $node->value = str_ireplace("'", '&apos;', $node->value);
                $node->value = str_ireplace('"', '&quot;', $node->value);
                $node->value = str_ireplace('>', '&gt;', $node->value);
                $node->value = str_ireplace('<', '&lt;', $node->value);
            }
            return true;
        };

        $ast->callbackRecurse($strings);
        return $ast;
    }
}
