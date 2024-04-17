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
 * AST filter that checks for very specific characters within
 * identifiers.
 */
class stack_ast_filter_090_special_forbidden_characters implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $warned = [];
        $process = function($node) use (&$warned) {
            if ($node instanceof MP_Identifier) {
                if (mb_strpos($node->value, 'ˆ')) {
                    $node->position['invalid'] = true;
                    $warned['ˆ'] = 'ˆ';
                }
            }

            return true;
        };

        $ast->callbackRecurse($process);

        if (count($warned) > 0) {
            $errors[] = stack_string('stackCas_forbiddenChar', ['char' => implode(", ", array_unique($warned))]);
            $answernotes[] = 'forbiddenChar';
        }

        return $ast;
    }
}
