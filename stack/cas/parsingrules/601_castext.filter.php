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
 * AST filter that compiles inline CASText2.
 * It also marks anything it sees that looks like CASText2,
 * even manually written CASText2.
 */
class stack_ast_filter_601_castext implements stack_cas_astfilter_parametric {

    // What do we mark as the context of the CASText.
    private $context = 'unknown';
    private $errclass = 'stack_cas_error';

    public function set_filter_parameters(array $parameters) {
        if (isset($parameters['context'])) {
            $this->context = $parameters['context'];
        }
        if (isset($parameters['errclass'])) {
            $this->errclass = $parameters['errclass'];
        }
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $ctx = $this->context;
        $errclass = $this->errclass;

        $process = function($node) use (&$errors, $ctx, $errclass) {
            if ($node instanceof MP_FunctionCall) {
                if ($node->name instanceof MP_Identifier && $node->name->value === 'castext') {
                    if (count($node->arguments) == 1 && !($node->arguments[0] instanceof MP_String)) {
                        $errors[] = new $errclass('castext()-compiler, wrong argument. ' .
                            'Only works with one direct raw string. And possibly a format descriptor.', $ctx);
                        $node->position['invalid'] = true;
                        return true;
                    } else if (count($node->arguments) == 2 && (!($node->arguments[0] instanceof MP_String) ||
                            !($node->arguments[1] instanceof MP_Identifier))) {
                        $errors[] = new $errclass('castext()-compiler, wrong argument. ' .
                            'Only works with one direct raw string. And possibly a format descriptor.', $ctx);
                        $node->position['invalid'] = true;
                        return true;
                    } else if (count($node->arguments) == 0 || count($node->arguments) > 2) {
                        $errors[] = new $errclass('castext()-compiler, wrong argument. ' .
                            'Only works with one direct raw string. And possibly a format descriptor.', $ctx);
                        $node->position['invalid'] = true;
                        return true;
                    }
                    $format = castext2_parser_utils::RAWFORMAT;
                    // Special handling for generating fragments to be injected in Markdown formated contexts.
                    if (count($node->arguments) == 2 && strtolower($node->arguments[1]->value) === 'md') {
                        $format = castext2_parser_utils::MDFORMAT;
                    }
                    $compiled = castext2_parser_utils::compile($node->arguments[0]->value,
                        $format, ['errclass' => $errclass, 'context' => $ctx]);
                    if ($compiled instanceof MP_Root) {
                        $compiled = $compiled->items[0];
                    }
                    if ($compiled instanceof MP_Statement) {
                        $compiled = $compiled->statement;
                    }
                    $compiled->position['castext'] = true;
                    $node->parentnode->replace($node, $compiled);
                    return false;
                } else if ($node->name instanceof MP_Identifier && $node->name->value === 'castext_concat') {
                    $node->position['castext'] = true;
                } else if ($node->name instanceof MP_Identifier && $node->name->value === 'castext_simplify') {
                    $node->position['castext'] = true;
                }
            }
            if ($node instanceof MP_List && count($node->items) > 0) {
                if ($node->items[0] instanceof MP_String && $node->items[0]->value === '%root') {
                    $node->position['castext'] = true;
                }

            } else if (isset($node->parentnode->position['castext'])) {
                $node->position['castext'] = true;
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
