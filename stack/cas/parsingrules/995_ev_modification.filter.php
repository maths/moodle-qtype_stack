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
require_once(__DIR__ . '/996_call_modification.filter.php');

/**
 * AST filter that rewrites calls to ev in such a way that they can deal
 * with the security system. Also rewrites evaluation flags if they are
 * in play.
 */
class stack_ast_filter_995_ev_modification implements stack_cas_astfilter_parametric {

    // Whether to rewrite evaluation flags. Don't do for students.
    private $flags = false;

    public function set_filter_parameters(array $parameters) {
        $this->flags = isset($parameters['flags']) ? $parameters['flags'] : false;
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) {
            // If not student input, turn all evaluation flags to ev-calls.
            // Do it now before other ev logic gets executed.
            if ($this->flags && $node instanceof MP_Statement &&
                $node->flags !== null && count($node->flags) > 0) {
                $fun = new MP_FunctionCall(new MP_Identifier('ev'), [$node->statement]);
                if ($node->statement instanceof MP_Operation && $node->statement->op === ':') {
                    $fun->arguments[0] = $node->statement->rhs;
                    $node->statement->rhs = $fun;
                } else {
                    $node->statement = $fun;
                }
                foreach ($node->flags as $flag) {
                    $fun->arguments[] = new MP_Operation('=', $flag->name, $flag->value);
                }
                $node->flags = [];
                return false;
            }
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom &&
                $node->name->value === 'ev') {
                $payload = $node->arguments[0];
                $tc = $payload->type_count();
                // Do not touch these, indempotent behaviour required.
                if (isset($tc['funs'][stack_ast_filter_996_call_modification::EXPCHECK])) {
                    unset($tc['funs'][stack_ast_filter_996_call_modification::EXPCHECK]);
                }
                if (count($tc['funs']) > 0) {
                    // Complex `ev`. As in iss824.

                    $simp = null;
                    foreach ($node->arguments as $arg) {
                        if ($arg !== $payload) {
                            if ($arg instanceof MP_Identifier && $arg->value === 'simp') {
                                $simp = new MP_Boolean(true);
                            } else if ($arg instanceof MP_Operation && ($arg->op === ':' || $arg->op === '=')
                                       && $arg->lhs instanceof MP_Identifier && $arg->lhs->value === 'simp') {
                                $simp = clone $arg->rhs;
                            }
                        }
                    }

                    $replacement = null;
                    if ($simp === null) {
                        $replacement = new MP_FunctionCall(new MP_Identifier('block'),
                            [
                                new MP_List([new MP_Identifier('%_sev_e')]),
                                new MP_Operation(':', new MP_Identifier('%_sev_e'), $payload),
                                $node]);
                    } else {
                        $replacement = new MP_FunctionCall(new MP_Identifier('block'),
                            [
                                new MP_List([new MP_Identifier('%_sev_e'), new MP_Identifier('%_sev_s')]),
                                new MP_Operation(':', new MP_Identifier('%_sev_s'), new MP_Identifier('simp')),
                                new MP_Operation(':', new MP_Identifier('simp'), $simp),
                                new MP_Operation(':', new MP_Identifier('%_sev_e'), $payload),
                                new MP_Operation(':', new MP_Identifier('simp'), new MP_Identifier('%_sev_s')),
                                $node]);
                    }
                    $node->replace($payload, new MP_Identifier('%_sev_e'));
                    $node->parentnode->replace($node, $replacement);
                    return false;
                }
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while (!$ast->callbackRecurse($process)) { }
        // @codingStandardsIgnoreEnd

        return $ast;
    }
}
