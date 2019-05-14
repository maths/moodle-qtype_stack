<?php


require_once(__DIR__ . '/filter.interface.php');
require_once(__DIR__ . '/../../maximaparser/utils.php');

/**
 * AST filter that handles the logarithm base syntax-extension.
 *
 * e.g. log_10(x) => lg(x,10), log_y+x(z) => lg(z,y+x)
 *
 * Also maps log10(x) => lg(x,10)
 *
 * Will add 'logsubs' answernote if triggered.
 */
class stack_ast_log_candy_002 implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $process = function($node) use (&$errors, &$answernotes) {
            if ($node instanceof MP_Functioncall && $node->name instanceof MP_Identifier) {
                // If we are already a function call and the name fits then this might be easy.
                if ($node->name->value === 'log10') {
                    $node->name->value = 'lg';
                    // The special replace of FunctionCalls appends an argument.
                    $node->replace(-1, new MP_Integer(10));
                    $answernotes[] = 'logsubs';
                    return false;
                }
                if ($node->name->value === 'log_') {
                    // This is a problem case. Lets assume we are dealing with:
                    //  log_(baseexpression)...(x) => lg(x,(baseexpression)...)
                    // As we cannot be dealing with an empty base. So lets eat that.
                    $arguments = array(); // Should be only one.
                    foreach ($node->arguments as $arg) {
                        $arguments[] = $arg->toString();
                    }
                    $newnode = new MP_Identifier('log_(' . implode(',', $arguments) . ')');
                    $node->parentnode->replace($node, $newnode);
                    // We generate answernotes and errors if this thing eats the whole 
                    // expression not before.
                    return false;
                }
                if (core_text::substr($node->name->value, 0, 4) === 'log_') {
                    // Now we have something of the form 'log_xyz(y)' we will simply turn it 
                    // to 'lg(y,xyz)' by parsing 'xyz'. We do not need to care about any rules
                    // when parsing it as it will be a pure statement and will be parseable.
                    $argument = core_text::substr($node->name->value, 4);
                    // This will unfortunately lose all the inforamtion about insertted stars 
                    // but that is hardly an issue.
                    $parsed = maxima_parser_utils::parse($argument, 'Root');
                    // There will be only one statement and it is a statement.
                    $parsed = $parsed->items[0]->statement;

                    // Then we rewrite things.
                    $node->name->value = 'lg';
                    // The special replace of FunctionCalls appends an argument.
                    $node->replace(-1, $parsed);
                    $answernotes[] = 'logsubs';
                    return false;
                }
            } else if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                // The more problematic case is dealing with variables that float around,
                // they must be tied to groups so that they can form functions, but we can
                // end up in a situation where the variable starting with 'log_' eats all
                // and has nothing to tie into, this would then be an error.
                // We also need to know the difference between automatically inserted stars
                // and real stars.

                if ($node->value === 'log_' && $node->parentnode instanceof MP_Operation &&
                    $node->parentnode->lhs === $node) {
                    // If the situation is 'log_*(x)' we have an syntax error unless that
                    // * comes from insert stars of some sort i.e. spaces...
                    if ($node->parentnode->rhs instanceof MP_Group && $node->parentnode->op === '*') {
                        if (isset($node->parentnode->position['fixspaces'])) {
                            // Return the situation to an already solved one.
                            $node->parentnode->parentnode->replace($node->parentnode, 
                                new MP_Functioncall($node, $node->parentnode->rhs->items));
                            return false;
                        } else {
                            $node->parentnode->position['invalid'] = true;
                            // TODO: localise, maybe include the erroneous portion.
                            $errors[] = 'Logarithm without a base...';
                            return false;
                        }
                    } else {
                        // For any other operation this is a fail. e.g. 'log_^3x(x)'
                        $node->parentnode->position['invalid'] = true;
                        // TODO: localise, maybe include the erroneous portion.
                        $errors[] = 'Logarithm syntax missing left hand side of operattion...';
                        return false;
                    }
                } 
                if (core_text::substr($node->value, 0, 4) === 'log_' &&
                    $node->parentnode instanceof MP_Operation &&
                    $node->parentnode->lhs === $node) {
                    // log_x^3 eats that op, but if log_x*(x) then it depends on 
                    // where that star came from
                    if ($node->parentnode->rhs instanceof MP_Group && $node->parentnode->op === '*') {
                        // If it is a generated star then interpret as a function call.
                        if (isset($node->parentnode->position['insertstars']) ||
                            isset($node->parentnode->position['fixspaces'])) {
                            // Return the situation to an already solved one.
                            $node->parentnode->parentnode->replace($node->parentnode, 
                                new MP_Functioncall($node, $node->parentnode->rhs->items));
                            return false;
                        }
                    }
                    $newname = $node->value;
                    if (stack_cas_security::get_feature($node->parentnode->op, 'spacesurroundedop') !== null) {
                        $newname .= ' ' . $node->parentnode->op . ' ';
                    } else {
                        $newname .= $node->parentnode->op;
                    }
                    // Some variation about how to deal.
                    if ($node->parentnode->rhs instanceof MP_Functioncall) {
                        $newname .= $node->parentnode->rhs->name->toString();
                        $node->parentnode->parentnode->replace($node->parentnode, 
                            new MP_Functioncall(new MP_Identifier($newname), $node->parentnode->rhs->arguments));   
                        return false;
                    }
                    if ($node->parentnode->rhs instanceof MP_Operation) {
                        // There is a very specific subgroup that causes trouble:
                        if ($node->parentnode->rhs->op === '*' && 
                            isset($node->parentnode->rhs->position['insertstars']) &&
                            $node->parentnode->rhs->rhs instanceof MP_Group &&
                            $node->parentnode->rhs->lhs instanceof MP_Atom) {
                            // Basically these are created by the insert stars logic
                            // and have slightly od order of precedence in most cases.
                            $newname .= $node->parentnode->rhs->lhs->toString();
                            $nf = new MP_Functioncall(new MP_Identifier($newname),
                                $node->parentnode->rhs->rhs->items);
                            $node->parentnode->parentnode->replace($node->parentnode, $nf);
                            return false;
                        }


                        // Insert into the subtree on the right. Elevate the subtree.
                        $rhs = $node->parentnode->leftmostofright();
                        if ($rhs instanceof MP_Atom) {
                            $newname .= $rhs->toString();
                            $node->value = $newname;
                            $rhs->parentnode->replace($rhs, $node);
                            $node->parentnode->parentnode->replace($node->parentnode, $node->parentnode->rhs);  
                            return false;
                        } else if ($rhs instanceof MP_Functioncall) {
                            $newname .= $rhs->name->toString();
                            $rhs->parentnode->replace($rhs, new MP_Functioncall(new MP_Identifier($newname), $rhs->arguments));
                            $node->parentnode->parentnode->replace($node->parentnode, $node->parentnode->rhs);  
                            return false;
                        } 
                    }
                    if ($node->parentnode->rhs instanceof MP_Atom) {
                        $newname .= $node->parentnode->rhs->toString();
                        $node->parentnode->parentnode->replace($node->parentnode, 
                            new MP_Identifier($newname));   
                        return false;
                    }
                }


                if (core_text::substr($node->value, 0, 4) === 'log_' && (
                    $node->parentnode instanceof MP_List ||
                    $node->parentnode instanceof MP_Set ||
                    $node->parentnode instanceof MP_Group ||
                    $node->parentnode instanceof MP_Statement ||
                    $node->parentnode instanceof MP_Functioncall)) {
                    // We have ended up in a situation where there is nothing to eat.
                    $node->parentnode->position['invalid'] = true;
                    // TODO: localise, maybe include the erroneous portion.
                    $errors[] = 'Logarithm without an argument...';
                    return false;
                }
            }
            return true;
        };

        // This filter does massive tree modifications that are beyond the capabilities
        // of the normal graph updating so we need to activate that updating manually
        // between steps. That updating ensures that parentnode links i.e. backreferences 
        // are valid but it does not do it unless absolutely necessary. And now it is.
        $ast->callbackRecurse(null);
        while ($ast->callbackRecurse($process, true) !== true) {
            $ast->callbackRecurse(null);
        }
        
        return $ast;
    }
}