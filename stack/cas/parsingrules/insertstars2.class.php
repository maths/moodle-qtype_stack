<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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
defined('MOODLE_INTERNAL')|| die();
require_once(__DIR__ . '/insertstars0.class.php');

class stack_parser_logic_insertstars2 extends stack_parser_logic_insertstars0 {

    public function __construct($insertstars = true, $fixspaces = false) {
        // Stars but not spaces. Single char vars.
        parent::__construct($insertstars, $fixspaces);
        $this->insertstars = $insertstars;
        $this->fixspaces = $fixspaces;
    }

    public function post($ast, &$valid, &$errors, &$answernote, $syntax, $safevars, $safefunctions) {
        $process = function($node) use (&$valid, &$errors, &$answernote, $syntax, $safevars, $safefunctions) {
            if ($node instanceof MP_Identifier && !($node->parentnode instanceof MP_FunctionCall)) {
                // Cannot split further.
                if (core_text::strlen($node->value) === 1) {
                    return true;
                }

                // Do not touch variables that are safe. e.g. unit names.
                if (isset($safevars[$node->value])) {
                    return true;
                }

                // Skip the very special identifiers for log-candy. These will be reconstructed
                // as function calls elsewhere.
                if ($node->value === 'log10' || core_text::substr($node->value, 0, 4) === 'log_') {
                    return true;
                }

                // Split the whole identifier to single chars.
                $temp = new MP_Identifier('rhs');
                $replacement = new MP_Operation('*', new MP_Identifier(core_text::substr($node->value, 0, 1)), $temp);
                $iter = $replacement;
                $i = 1;
                for ($i = 1; $i < core_text::strlen($node->value) - 1; $i++) {
                    $t = new MP_Identifier(core_text::substr($node->value, $i, 1));
                    if (ctype_digit($t->value)) {
                        $t = new MP_Integer((int)$t->value);
                    }
                    $iter->replace($temp, new MP_Operation('*', $t, $temp));
                    $iter = $iter->rhs;
                }
                $t = new MP_Identifier(core_text::substr($node->value, $i, 1));
                if (ctype_digit($t->value)) {
                    $t = new MP_Integer((int)$t->value);
                }
                $iter->replace($temp, $t);
                $node->parentnode->replace($node, $replacement);
                $answernote[] = 'missing_stars';
                return false;
            }
            // TODO: Do we do this also for function names? All of them? Is even log10 safe?

            return true;
        };

        while ($ast->callbackRecurse($process) !== true) {
        }
    }
}