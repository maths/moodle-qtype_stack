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

    private static $protectedidentifiermap = null;

    public function __construct($insertstars = true, $fixspaces = false) {
        // Stars but not spaces. Single char vars.
        parent::__construct($insertstars, $fixspaces);
        $this->insertstars = $insertstars;
        $this->fixspaces = $fixspaces;
    }

    public function post($ast, &$valid, &$errors, &$answernote, $syntax, $safevars, $safefunctions) {
        if (self::$protectedidentifiermap === null) {
            self::$protectedidentifiermap = array('functions' => array(), 'variables' => array());
            self::$protectedidentifiermap['functions'] = stack_cas_security::get_all_with_feature('function');
            self::$protectedidentifiermap['variables'] = stack_cas_security::get_all_with_feature('variable');
            foreach (stack_cas_security::get_all_with_feature('constant') as $key => $value) {
                self::$protectedidentifiermap['variables'][$key] = $value;
            }
            self::$protectedidentifiermap['variables']['QMCHAR'] = 'QMCHAR';
            usort(self::$protectedidentifiermap['functions'], function (
                string $a,
                string $b
            ) {
                return strlen($a) < strlen($b);
            });
            usort(self::$protectedidentifiermap['variables'], function (
                string $a,
                string $b
            ) {
                return strlen($a) < strlen($b);
            });
            // Now that they are sortted by the length lets remap them so that the array has
            // keys in the same order
            $functions = array();
            $variables = array();
            foreach (self::$protectedidentifiermap['functions'] as $funct) {
                $functions[$funct] = $funct;
            }
            foreach (self::$protectedidentifiermap['variables'] as $var) {
                $variables[$var] = $var;
            }
            self::$protectedidentifiermap['functions'] = $functions;
            self::$protectedidentifiermap['variables'] = $variables;
        }

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

                // If the identifier is a protected one stop here.
                if (array_key_exists($node->value, self::$protectedidentifiermap['variables'])) {
                    return true;
                }

                // If it starts with any know identifier split after that.
                foreach (self::$protectedidentifiermap['variables'] as $safe) {
                    if (core_text::strpos($node->value, $safe) === 0) {
                        $remainder = core_text::substr($node->value, core_text::strlen($safe));
                        if (ctype_digit($remainder)) {
                            $remainder = new MP_Integer($remainder);
                        } else {
                            $remainder = new MP_Identifier($remainder);
                        }
                        $replacement = new MP_Operation('*', new MP_Identifier($safe), $remainder);
                        $node->parentnode->replace($node, $replacement);
                        $answernote[] = 'missing_stars';
                        return false;
                    }
                }

                // If it does not start with a know identifier split the first char.
                $remainder = core_text::substr($node->value, 1);
                if (ctype_digit($remainder)) {
                    $remainder = new MP_Integer($remainder);
                } else {
                    $remainder = new MP_Identifier($remainder);
                }
                $firstchar = core_text::substr($node->value, 0, 1);
                if (ctype_digit($firstchar)) {
                    $firstchar = new MP_Integer($firstchar);
                } else {
                    $firstchar = new MP_Identifier($firstchar);
                }
                $replacement = new MP_Operation('*', $firstchar, $remainder);
                $node->parentnode->replace($node, $replacement);
                $answernote[] = 'missing_stars';
                return false;

                /*
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
                */
            }
            // TODO: Do we do this also for function names? All of them? Is even log10 safe?

            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd

    }
}