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
 * AST filter that replaces unicode letters with the ASCII equivalent.
 * Note, this filter is currently not used.
 */
class stack_ast_filter_150_replace_unicode_letters implements stack_cas_astfilter {

    public static $ssmap = null;

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        if (self::$ssmap === null) {
            self::$ssmap = json_decode(file_get_contents(__DIR__ . '/../../maximaparser/unicode/letters-stack.json'), true);
        }

        $process = function($node) use (&$errors, &$answernotes) {
            if ($node instanceof MP_Identifier && !(isset($node->position['invalid']) && $node->position['invalid'])) {
                $node->value = str_replace(array_keys(self::$ssmap), array_values(self::$ssmap), $node->value);
            }
            return true;
        };

        $ast->callbackRecurse($process);
        return $ast;
    }
}
