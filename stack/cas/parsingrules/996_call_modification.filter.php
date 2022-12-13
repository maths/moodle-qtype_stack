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
                // The '-operator makes the IDCHECK not work at the correct time,
                // so detect if that op is in the ancestry of this node and skip if so.
                $p = $node;
                while ($p !== null) {
                    if ($p instanceof MP_PrefixOp && $p->op === "'") {
                        return true;
                    }
                    // Also break on assingments.
                    if ($p->parentnode instanceof MP_Operation && $p->parentnode->op === ':') {
                        break;
                    }
                    // And specific types of equations.
                    if ($p->parentnode instanceof MP_Operation && $p->parentnode->op === '=' && $p->parentnode->rhs === $p) {
                        break;
                    }
                    $p = $p->parentnode;
                }
                if ($node->name instanceof MP_Atom && ($node->name->value === self::IDCHECK ||
                    $node->name->value === self::EXPCHECK || $node->name->value === 'lambda')) {
                    // No checks for the checks themselves. They are protected using other means.
                    // Also lambdas are something that need to be dealt using other means.
                    return true;
                }

                // If we have a complex mapping, i.e. a map function with
                // the identifier coming from something else we rewrite it
                // so that we can check the identifier using normal logic.
                // For example apply(foo(),[...]) => block([_tmp],_tmp:foo(),apply(_tmp),[...])).
                if ($node->name instanceof MP_Atom && isset($mapfuns[$node->name->value])
                    && count($node->arguments) > 0 && !($node->arguments[0] instanceof MP_Atom)) {
                    $replacement = new MP_FunctionCall(new MP_Identifier('block'),
                        [
                            new MP_List([new MP_Identifier('_tmp_996')]),
                            new MP_Operation(':', new MP_Identifier('_tmp_996'), $node->arguments[0])
                        ]);
                    $node->arguments[0]->position['call-id'] = true;
                    $replacement->position['ev-check'] = true;
                    $replacement->name->position['ev-check'] = true;
                    $node->arguments[0] = new MP_Identifier('_tmp_996');
                    $node->arguments[0]->position['ev-check'] = true;
                    $node->parentnode->replace($node, $replacement);
                    $replacement->arguments[] = $node;
                    return false;
                }

                $namecheck = new MP_FunctionCall(new MP_Identifier(self::IDCHECK), [$node->name]);

                if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name === $node) {
                    // This case has been handled when that parent was handled.
                    return true;
                }

                // The order of these ifs is critical, we build up the checks
                // so that no basic check gets lost due to more advanced ones
                // doing more conplex things. The advanced cases assume that
                // the simpler ones have been done already.
                if (!($node->parentnode instanceof MP_Group) ||
                    $node->parentnode->items[0]->toString() !== $namecheck->toString()) {
                    $replacement = new MP_Group([$namecheck, $node]);
                    if ($node->parentnode instanceof MP_PrefixOp ||
                        ($node->parentnode instanceof MP_FunctionCall &&
                            $node->parentnode->name instanceof MP_Atom && $node->parentnode->name->value === self::EXPCHECK)) {
                        // Pattern %_E(subst(...)) => (%_C(subst),%_E(subst(...))).
                        // Pattern 'f(x) => (%_C(f),'f(x)).
                        // This needs to be indempotent.
                        if (!($node->parentnode->parentnode instanceof MP_Group &&
                            $node->parentnode->parentnode->items[0]->toString() === $replacement->items[0]->toString())) {
                            $replacement->items[1] = $node->parentnode;
                            $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                            return false;
                        }
                    } else {
                        // Except when we have block() and block(local()...).
                        if ($node->name instanceof MP_Identifier && ($node->name->value === 'block' ||
                            ($node->name->value === 'local' && $node->parentnode instanceof MP_FunctionCall &&
                                $node->parentnode->name instanceof MP_Identifier && $node->parentnode->name->value === 'block' &&
                                $node->parentnode->arguments[0] === $node))) {
                            return true;
                        }

                        // In the case of calling function/indexing results such as f(x)(y) => (%_C(f),%_C(f(x)),f(x)(y)).
                        if ($node->name instanceof MP_FunctionCall) {
                            $replacement = [$node];
                            $i = null;
                            if ($node instanceof MP_FunctionCall) {
                                $i = $node->name;
                            } else if ($node instanceof MP_Indexing) {
                                $i = $node->target;
                            }
                            while ($i !== null) {
                                array_unshift($replacement, new MP_FunctionCall(new MP_Identifier(self::IDCHECK), [$i]));
                                if ($i instanceof MP_FunctionCall) {
                                    $i = $i->name;
                                } else if ($i instanceof MP_Indexing) {
                                    $i = $i->target;
                                } else {
                                    $i = null;
                                }
                            }
                            $replacement = new MP_Group($replacement);
                            // Has the full set already been done.
                            if ($node->parentnode instanceof MP_Group &&
                                count($node->parentnode->items) === count($replacement->items) &&
                                $node->parentnode->toString() === $replacement->toString()) {
                                return true;
                            }
                        }
                        // The previous one generates a pattern (...,%_C(f(x)),%_C(f(x)(y)),...)
                        // In this situation it is necessary to stop rewriting the call inside the check.
                        if ($node instanceof MP_FunctionCall && $node->parentnode instanceof MP_FunctionCall &&
                            $node->parentnode->name instanceof MP_Identifier &&
                            $node->parentnode->name->value === self::IDCHECK &&
                            $node->parentnode->parentnode instanceof MP_Group) {
                            $i = array_search($node->parentnode, $node->parentnode->parentnode->items, true);
                            if ($i >= 0 && $node->parentnode->parentnode->items[$i - 1]->toString() ===
                                (new MP_FunctionCall(new MP_Identifier(self::IDCHECK), [$node->name]))->toString()) {
                                return true;
                            }
                        }

                        // In the case of a pattern f(x) => (%_C(f),f(x)).
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    }
                }
                if ($node->name instanceof MP_Atom && $node->name->value === 'ev' && count($node->arguments) > 0) {
                    if (!($node->arguments[0] instanceof MP_FunctionCall) || !($node->arguments[0]->name instanceof MP_Atom) ||
                        $node->arguments[0]->name->value !== self::EXPCHECK) {
                        // In the case of a pattern ev(foo, ...) => ev(%_E(foo),...).
                        $node->replace($node->arguments[0], new MP_FunctionCall(new MP_Identifier(self::EXPCHECK),
                            [$node->arguments[0]]));
                        return false;
                    }
                    return true;
                }
                if ($node->name instanceof MP_Atom && ($node->name->value === 'subst' || $node->name->value === 'at')) {
                    // Change subst(...) => %_E(subst(...)), always even when we check again at eval time.
                    if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom &&
                        $node->parentnode->name->value === self::EXPCHECK) {
                        return true;
                    }
                    $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier(self::EXPCHECK), [$node]));
                    return false;
                }
                if ($node->name instanceof MP_Atom && ($node->name->value === 'apply' || isset($mapfuns[$node->name->value]))) {
                    // @codingStandardsIgnoreStart
                    // In the case of a pattern apply(foo,...) => (%_C(foo),%_C(apply),apply(foo,...)).
                    // @codingStandardsIgnoreEnd
                    $check = new MP_FunctionCall(new MP_Identifier(self::IDCHECK), [$node->arguments[0]]);
                    if (isset($node->parentnode->items) && $node->parentnode->items[1]->toString() !== $check->toString()) {
                        $node->parentnode->items = array_merge([$node->parentnode->items[0], $check],
                            array_slice($node->parentnode->items, 1));
                        return false;
                    }
                }
            }
            if ($node instanceof MP_PrefixOp && $node->op === "''" && !($node->rhs instanceof MP_FunctionCall &&
                $node->rhs->name instanceof MP_Atom && $node->rhs->name->value === self::EXPCHECK)) {
                // Pattern ''x => ''%_E(x).
                $node->replace($node->rhs, new MP_FunctionCall(new MP_Identifier(self::EXPCHECK), [$node->rhs]));
                return false;
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        while (!$ast->callbackRecurse($process)) { }
        // @codingStandardsIgnoreEnd

        return $ast;
    }
}
