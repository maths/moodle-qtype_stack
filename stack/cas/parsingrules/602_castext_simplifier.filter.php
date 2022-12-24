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
 * AST filter that simplifies compiled CASText, the aim is to merge
 * as much as possible down to as simple as possible parts.
 *
 * Note that we intentionally do not simplify too much inside JSXGraphs.
 * This is due to the MecLib library tending to load large blobs and
 * it would be silly to join them with small modifications and end up
 * storing largely similar content during the static string extraction.
 */
class stack_ast_filter_602_castext_simplifier implements stack_cas_astfilter {


    // Is a node of the form ["%root",...].
    private static function is_castext($node) {
        if ($node instanceof MP_List && count($node->items) > 0 && $node->items[0] instanceof MP_String) {
            return $node->items[0]->value === "%root";
        }
        return false;
    }


    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $process = function($node) use (&$answernotes, &$errors) {
            if (isset($node->position['castext']) && $node->position['castext']) {
                if ($node instanceof MP_FunctionCall) {
                    if ($node->name instanceof MP_Identifier && $node->name->value === 'sconcat') {
                        if (count($node->arguments) == 0) {
                            $n = new MP_String("");
                            $n->position['castext'] = true;
                            $node->parentnode->replace($node, $n);
                            return false;
                        }

                        if (count($node->arguments) == 1 && $node->arguments[0] instanceof MP_String) {
                            $node->parentnode->replace($node, $node->arguments[0]);
                            return false;
                        }

                        if (count($node->arguments) > 1) {
                            $newargs = castext2_parser_utils::string_list_reduce($node->arguments, false);
                            if (count($newargs) < count($node->arguments)) {
                                if (count($newargs) === 1 && $newargs[0] instanceof MP_String) {
                                    $n = $newargs[0];
                                    $n->position['castext'] = true;
                                    $node->parentnode->replace($node, $n);
                                    return false;
                                }
                                $node->arguments = $newargs;
                                foreach ($node->arguments as $arg) {
                                    $arg->position['castext'] = true;
                                }
                                return false;
                            }
                        }
                    } else if ($node->name instanceof MP_Identifier &&
                        $node->name->value === 'simplode' && count($node->arguments) == 1 &&
                        $node->arguments[0] instanceof MP_List) {

                        if (count($node->arguments[0]->items) == 0) {
                            $n = new MP_String("");
                            $n->position['castext'] = true;
                            $node->parentnode->replace($node, $n);
                            return false;
                        }

                        if (count($node->arguments[0]->items) == 1 && $node->arguments[0]->items[0] instanceof MP_String) {
                            $node->parentnode->replace($node, $node->arguments[0]->items[0]);
                            return false;
                        }

                        if (count($node->arguments[0]->items) > 1) {
                            $newargs = castext2_parser_utils::string_list_reduce($node->arguments[0]->items, false);
                            if (count($newargs) < count($node->arguments[0]->items)) {
                                if (count($newargs) === 1 && $newargs[0] instanceof MP_String) {
                                    $n = $newargs[0];
                                    $n->position['castext'] = true;
                                    $node->parentnode->replace($node, $n);
                                    return false;
                                }
                                $node->arguments[0]->items = $newargs;
                                foreach ($node->arguments[0]->items as $arg) {
                                    $arg->position['castext'] = true;
                                }
                                return false;
                            }

                        }
                    } else if ($node->name instanceof MP_Identifier && $node->name->value === 'castext_concat' &&
                        count($node->arguments) === 2) {
                        // We could have concatenations that can be pre-evaluated.
                        if ($node->arguments[0] instanceof MP_String && $node->arguments[1] instanceof MP_String) {
                            $n = new MP_String($node->arguments[0]->value . $node->arguments[1]->value);
                            $n->position['castext'] = true;
                            $node->parentnode->replace($node, $n);
                            return false;
                        }
                        $simplifywrapper = false;
                        $locals = [];
                        $root = [new MP_String("%root")];
                        $a = $node->arguments[0];
                        if ($a instanceof MP_String) {
                            $root[] = $a;
                            $a = null;
                        } else if ($a instanceof MP_FunctionCall && $a->name instanceof MP_Identifier) {
                            if ($a->name->value === 'castext_simplify') {
                                $simplifywrapper = true;
                                $a = $a->arguments[0];
                            }
                        }
                        if ($a !== null) {
                            if (self::is_castext($a) || $a instanceof MP_String) {
                                $root[] = $a;
                                $a = null;
                            } else if ($a instanceof MP_FunctionCall && $a->name instanceof MP_Identifier &&
                                $a->name->value === 'block' && count($a->arguments) === 2) {
                                if ($a->arguments[0] instanceof MP_FunctionCall &&
                                    $a->arguments[0]->name instanceof MP_Identifier &&
                                    $a->arguments[0]->name->value === 'local') {
                                    foreach ($a->arguments[0]->arguments as $arg) {
                                        $locals[$arg->value] = $arg;
                                    }
                                    $root[] = $a->arguments[1];
                                    $a = null;
                                }

                            } else if ($a instanceof MP_FunctionCall && $a->name instanceof MP_Identifier &&
                                $a->name->value === 'sconcat') {
                                $root = array_merge($root, $a->arguments);
                                $a = null;
                            }
                        }
                        $b = $node->arguments[1];
                        if ($b instanceof MP_String) {
                            $root[] = $b;
                            $b = null;
                        } else if ($b instanceof MP_FunctionCall && $b->name instanceof MP_Identifier) {
                            if ($b->name->value === 'castext_simplify') {
                                $simplifywrapper = true;
                                $b = $b->arguments[0];
                            }
                        }
                        if ($b !== null) {
                            if (self::is_castext($b) || $b instanceof MP_String) {
                                $root[] = $b;
                                $b = null;
                            } else if ($b instanceof MP_FunctionCall && $b->name instanceof MP_Identifier &&
                                $b->name->value === 'block' && count($b->arguments) === 2) {
                                // Note these block+local constructs only appea at the root-block level.
                                if ($b->arguments[0] instanceof MP_FunctionCall &&
                                    $b->arguments[0]->name instanceof MP_Identifier &&
                                    $b->arguments[0]->name->value === 'local') {
                                    foreach ($b->arguments[0]->arguments as $arg) {
                                        $locals[$arg->value] = $arg;
                                    }
                                    $root[] = $b->arguments[1];
                                    $b = null;
                                }
                            } else if ($b instanceof MP_FunctionCall && $b->name instanceof MP_Identifier &&
                                $b->name->value === 'sconcat') {
                                $root = array_merge($root, $b->arguments);
                                $b = null;
                            }
                        }

                        if ($a == null && $b == null) {
                            // Both extracted.
                            $replacement = new MP_List($root);
                            $replacement->position['castext'] = true;
                            if (count($locals) > 0) {
                                $replacement = new MP_FunctionCall(new MP_Identifier('block'),
                                    [new MP_FunctionCall(new MP_Identifier('local'), array_values($locals)), $replacement]);
                                $replacement->position['castext'] = true;
                            }
                            if ($simplifywrapper) {
                                $replacement = new MP_FunctionCall(new MP_Identifier('castext_simplify'), [$replacement]);
                                $replacement->position['castext'] = true;
                            }
                            $node->parentnode->replace($node, $replacement);
                        }
                    } else if ($node->name instanceof MP_Identifier && $node->name->value === 'castext_simplify' &&
                        count($node->arguments) == 1) {
                        if ($node->arguments[0] instanceof MP_String) {
                            $node->parentnode->replace($node, $node->arguments[0]);
                            return false;
                        }
                    }
                }
                // We may also have simplified everything out. Or down to strings that can be joined.
                if ($node instanceof MP_List && count($node->items) > 1 &&
                    $node->items[0] instanceof MP_String && (
                        $node->items[0]->value === '%root' ||
                        $node->items[0]->value === 'demarkdown' ||
                        $node->items[0]->value === 'demoodle' ||
                        $node->items[0]->value === 'htmlformat')) {
                    if ($node->items[0]->value === '%root' && count($node->items) === 2 && $node->items[1] instanceof MP_String) {
                        // A concatenation of a single string, can be removed. If %root.
                        $node->parentnode->replace($node, $node->items[1]);
                        return false;
                    }

                    $newitems = castext2_parser_utils::string_list_reduce($node->items, true);
                    if (count($newitems) < count($node->items)) {
                        if (count($newitems) === 2 && $node->items[0]->value === '%root') {
                            // The second term is something evaluating to
                            // string. If %root.
                            $node->parentnode->replace($node, $newitems[1]);
                            $newitems[1]->position['castext'] = true;
                            return false;
                        }

                        $node->items = $newitems;
                        foreach ($node->items as $item) {
                            $item->position['castext'] = true;
                        }
                        return false;
                    }
                }

                // Eliminate extra format declarations and render static content in other formats.
                if ($node instanceof MP_List && count($node->items) >= 2 && $node->items[0] instanceof MP_String &&
                    ($node->items[0]->value === 'demoodle' || $node->items[0]->value === 'demarkdown' ||
                            $node->items[0]->value === 'htmlformat')) {
                    // Same for Moodle auto-format.
                    $good = true;
                    $same = false;
                    $p = $node->parentnode;
                    while ($p !== null) {
                        if ($p instanceof MP_List && count($p->items) > 0 && $p->items[0] instanceof MP_String &&
                            ($p->items[0]->value === 'demoodle' || $p->items[0]->value === 'demarkdown' ||
                                    $p->items[0]->value === 'htmlformat' || $p->items[0]->value === 'jsxgraph' ||
                                    $p->items[0]->value === 'textdownload')) {
                            // That or above is somethign one needs to update if we add new format tuning blocks.
                            $good = false;
                            if ($p->items[0]->value === $node->items[0]->value) {
                                $same = true;
                            }
                            if ($node->items[0]->value === 'htmlformat' && ($p->items[0]->value === 'jsxgraph' ||
                                    $p->items[0]->value === 'textdownload')) {
                                // JSXGraph and textdownload are blocks that enforce specific formats.
                                $same = true;
                            }
                            break;
                        }
                        $p = $p->parentnode;
                    }
                    if ($p === null && $good && $node->items[0]->value === 'htmlformat') {
                        // The root format if not defined is htmlformat. So we can stop defining it.
                        $same = true;
                    }

                    // Static ones can be replaced if we don't have complex wrapping.
                    if ($good && $node->items[0]->value === 'demoodle' &&
                            $node->items[1] instanceof MP_String && count($node->items) === 2) {
                        $params = [$node->items[0]->value, $node->items[1]->value];
                        $proc = new stack_cas_castext2_demoodle([]);
                        $n = new MP_String($proc->postprocess($params));
                        $n->position['castext'] = true;
                        $node->parentnode->replace($node, $n);
                        return false;
                    }
                    if ($good && $node->items[0]->value === 'htmlformat' && $node->items[1]
                            instanceof MP_String && count($node->items) === 2) {
                        $node->parentnode->replace($node, $node->items[1]);
                        return false;
                    }
                    if ($good && $node->items[0]->value === 'demarkdown' && $node->items[1]
                            instanceof MP_String && count($node->items) === 2) {
                        $params = [$node->items[0]->value, $node->items[1]->value];
                        $proc = new stack_cas_castext2_demarkdown([]);
                        $n = new MP_String($proc->postprocess($params));
                        $n->position['castext'] = true;
                        $node->parentnode->replace($node, $n);
                        return false;
                    }

                    // If the context is of the same format we do not need to define the format.
                    if ($same) {
                        if ($node->parentnode instanceof MP_List) {
                            for ($i = 1; $i < count($node->items); $i++) {
                                $node->parentnode->insertChild($node->items[$i], $node);
                            }
                            $node->parentnode->removeChild($node);
                            return false;
                        }
                    }
                }
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
