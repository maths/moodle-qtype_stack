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

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $mapfuns = stack_cas_security::get_all_with_feature('mapfunction');
        $process = function($node) use ($mapfuns) {
            if ($node instanceof MP_Functioncall && !isset($node->position['997']) && !$node->is_definition()) {
                if ($node->name instanceof MP_Atom && ($node->name->value === 'apply' || isset($mapfuns[$node->name->value]))) {
                    // apply(foo,...) => (_C(foo),_C(apply),apply(foo,...))
                    $replacement = new MP_Group([new MP_FunctionCall(new MP_Identifier('_C'),[$node->arguments[0]]), new MP_FunctionCall(new MP_Identifier('_C'),[$node->name]), $node]);
                    $replacement->items[0]->position['997'] = true;
                    $replacement->items[1]->position['997'] = true;
                    $replacement->items[2]->position['997'] = true;
                    $node->parentnode->replace($node, $replacement);
                    return false;
                } else if ($node->name instanceof MP_Atom && ($node->name->value === 'at' || $node->name->value === 'subst')) {
                    // subst([f=g],g(x)) => (_C(subst),_CE(subst([f=g],g(x))))
                    $replacement = new MP_Group([new MP_FunctionCall(new MP_Identifier('_C'),[$node->name]), new MP_Functioncall(new MP_Identifier('_CE'),[$node])]);
                    $replacement->items[0]->position['997'] = true;
                    $replacement->items[1]->position['997'] = true;
                    $replacement->items[1]->arguments[0]->position['997'] = true;
                    $node->parentnode->replace($node, $replacement);
                    return false;
                } else {
                    // f(x) => (_C(f),f(x))
                    $replacement = new MP_Group([new MP_FunctionCall(new MP_Identifier('_C'),[$node->name]), $node]);
                    $replacement->items[0]->position['997'] = true;
                    
                    // 'f(x) => (_C(f),'f(x))
                    if ($node->parentnode instanceof MP_PrefixOp) {
                        $replacement->items[1] = $node->parentnode;
                        $replacement->items[1]->rhs->position['997'] = true;
                        $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                    } else {
                        $replacement->items[1]->position['997'] = true;
                        $node->parentnode->replace($node, $replacement);
                    }

                    return false;
                }
            }
            return true;
        };
        while (!$ast->callbackRecurse($process)) { }

        return $ast;
    }
}
