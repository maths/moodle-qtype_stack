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
 * AST filter that rewrites calls to functions to be checked at runtime.
 */
class stack_ast_filter_996_call_modification implements stack_cas_astfilter {

    // The name of the function that checks identifiers.
    const IDCHECK = '%_C';

    // The name of the function that checks expressions.
    const EXPCHECK = '%_E';

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $mapfuns = stack_cas_security::get_all_with_feature('mapfunction');
        $process = function($node) use ($mapfuns) {
            if ($node instanceof MP_Functioncall && !$node->is_definition()) {
                if ($node->name instanceof MP_Atom && ($node->name->value === self::IDCHECK || $node->name->value === self::EXPCHECK || $node->name->value === 'lambda')) {
                    // No checks for the checks themselves. They are protected using other means.
                    // Also lambdas are something that need to be dealt using other means.
                    return true;
                }
                $namecheck = new MP_FunctionCall(new MP_Identifier(self::IDCHECK), [$node->name]);
                // The order of these ifs is critical, we build up the checks
                // so that no basic check gets lost due to more advanced ones
                // doing more conplex things. The advanced cases assume that 
                // the simpler ones have been done already.
                if (!($node->parentnode instanceof MP_Group) || $node->parentnode->items[0]->toString() !== $namecheck->toString()) {
                    $replacement = new MP_Group([$namecheck, $node]);
                    if ($node->parentnode instanceof MP_PrefixOp || ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom && $node->parentnode->name->value === self::EXPCHECK)) {
                        // %_E(subst(...)) => (%_C(subst),%_E(subst(...)))
                        // 'f(x) => (%_C(f),'f(x))
                        // This needs to be indempotent.
                        if ($node->parentnode->parentnode instanceof MP_Group && $node->parentnode->parentnode->items[0]->toString() === $replacement->items[0]->toString()) {

                        } else {
                            $replacement->items[1] = $node->parentnode;
                            $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                            return false;
                        }
                    } else {
                        // f(x) => (%_C(f),f(x))
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    }
                }
                if ($node->name instanceof MP_Atom && $node->name->value === 'ev' && count($node->arguments) > 0) {
                    if (!($node->arguments[0] instanceof MP_FunctionCall) || !($node->arguments[0]->name instanceof MP_Atom) ||
                        $node->arguments[0]->name->value !== self::EXPCHECK) {
                        // ev(foo, ...) => ev(%_E(foo),...)
                        $node->replace($node->arguments[0], new MP_FunctionCall(new MP_Identifier(self::EXPCHECK), [$node->arguments[0]]));
                        return false;
                    }
                    return true;
                }
                if ($node->name instanceof MP_Atom && ($node->name->value === 'subst' || $node->name->value === 'at')) {
                    // subst(...) => %_E(subst(...)), always even when
                    // we check again at eval time.
                    if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom && $node->parentnode->name->value === self::EXPCHECK) {
                        return true;
                    }
                    $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier(self::EXPCHECK), [$node]));
                    return false;
                }
                if ($node->name instanceof MP_Atom && ($node->name->value === 'apply' || isset($mapfuns[$node->name->value]))) {
                    // E.g. apply(foo,...) => (%_C(foo),%_C(apply),apply(foo,...)).
                    $check = new MP_FunctionCall(new MP_Identifier(self::IDCHECK), [$node->arguments[0]]);
                    if ($node->parentnode->items[1]->toString() !== $check->toString()) {
                        $node->parentnode->items = array_merge([$node->parentnode->items[0], $check], array_slice($node->parentnode->items, 1));
                        return false;
                    }
                }
            }
            if ($node instanceof MP_PrefixOp && $node->op === "''" && !($node->rhs instanceof MP_FunctionCall && $node->rhs->name instanceof MP_Atom && $node->rhs->name->value === self::EXPCHECK)) {
                // ''x => ''%_E(x)
                $node->replace($node->rhs, new MP_FunctionCall(new MP_Identifier(self::EXPCHECK), [$node->rhs]));
                return false;
            }
            return true;
        };
        while (!$ast->callbackRecurse($process)) { }

        return $ast;
    }
}
