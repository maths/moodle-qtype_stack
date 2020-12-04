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
 * AST filter that check security. Note that this is a parametric filter.
 */
class stack_ast_filter_998_security implements stack_cas_astfilter_parametric {

    private $source = 's';

    public function set_filter_parameters(array $parameters) {
        $this->source = isset($parameters['security']) ? $parameters['security'] : 's';
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $valid = true;

        // First extract things of interest from the tree, i.e. function calls,
        // variable references and operations.
        $ofinterest = array();

        // If this is a student sourced thing, it may include a teacher
        // identifier in the form of the assignment to input-variable.
        // That assignement operation is also protected from forbidding
        // assignment operations.
        $protected = false;
        if ($this->source === 's') {
            $root = $ast;
            if ($root instanceof MP_Root) {
                $root = $root->items[0];
            }
            if ($root instanceof MP_Statement) {
                $root = $root->statement;
            }
            if ($root instanceof MP_Operation && $root->op === ':') {
                $protected = $root;
            }
        }

        // For certain cases we want to know of commas. For this reason
        // certain structures need to be checked for them.
        $commas = false;
        $parenthesis = false;
        $brackets = false;
        $braces = false;
        $evflags = false;
        $extraction = function($node) use (&$ofinterest, &$commas, &$parenthesis, &$brackets, &$braces, &$evflags, $protected){
            if ($node instanceof MP_Identifier ||
                $node instanceof MP_FunctionCall ||
                $node instanceof MP_Operation ||
                $node instanceof MP_PrefixOp ||
                $node instanceof MP_PostfixOp) {
                if ($protected === false || ($node !== $protected && ($node->parentnode !== $protected ||
                        $node->parentnode->lhs !== $node))) {
                    $ofinterest[] = $node;
                }
            }
            if (!$parenthesis && ($node instanceof MP_FunctionCall || $node instanceof MP_Group)) {
                $parenthesis = true;
            }
            if (!$braces && $node instanceof MP_Set) {
                $braces = true;
            }
            if (!$brackets && $node instanceof MP_List) {
                $brackets = true;
            }

            if (!$commas) {
                if ($node instanceof MP_FunctionCall && count($node->arguments) > 1) {
                    $commas = true;
                } else if (($node instanceof MP_Set || $node instanceof MP_List ||
                            $node instanceof MP_Group) && count($node->items) > 1) {
                    $commas = true;
                } else if ($node instanceof MP_EvaluationFlag) {
                    $commas = true;
                    $evflags = true;
                }
            }

            return true;
        };
        // We can actually skip the invalid portions as in most
        // cases those are wrongly used identifiers and it makes little
        // sense to whine about "sin" being used as a variable if we
        // already noted how it should be used.
        $ast->callbackRecurse($extraction, true);

        // Students may not use evaluation flags.
        if ($this->source === 's' && $evflags === true) {
            $valid = false;
            $answernotes[] = 'unencapsulated_comma';
            $errors[] = stack_string('stackCas_unencpsulated_comma');
        }

        // Separate the identifiers we meet for latter use. Not the nodes
        // the string identifiers. Key is the value so unique from the start.
        $functionnames = array();
        $writtenvariables = array();
        $variables = array();
        $operators = array();

        // If we had commas in play add them to the operators.
        if ($commas) {
            $operators[','] = true;
        }
        // Same for the paired ones.
        if ($parenthesis) {
            $operators['('] = true;
            $operators[')'] = true;
        }
        if ($brackets) {
            $operators['['] = true;
            $operators[']'] = true;
        }
        if ($braces) {
            $operators['{'] = true;
            $operators['}'] = true;
        }

        // Now loop over the initially found things of interest. Note that
        // the list may grow as we go forward and unwrap things.
        $i = 0;
        while ($i < count($ofinterest)) {
            $node = $ofinterest[$i];
            $i = $i + 1;

            if ($node instanceof MP_Operation || $node instanceof MP_PrefixOp || $node instanceof MP_PostfixOp) {
                // We could just strip these out in the recurse but maybe we want
                // to check something in the future.
                $operators[$node->op] = true;
            } else if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                $variables[$node->value] = true;
                if ($node->is_being_written_to()) {
                    // This can be used to check if someone tries to redefine
                    // %pi or some other important thing.
                    $writtenvariables[$node->value] = true;
                }
            } else if ($node instanceof MP_FunctionCall) {
                $notsafe = true;
                if ($node->name instanceof MP_Identifier || $node->name instanceof MP_String) {
                    $notsafe = false;
                    $functionnames[$node->name->value] = true;
                    $safemap = false;
                    if ($identifierrules->has_feature($node->name->value, 'mapfunction')) {
                        // If it is an apply or map function throw it in for
                        // validation.
                        switch ($node->name->value) {
                            case 'apply':
                            case 'funmake':
                                $safemap = true;

                                // TODO: add errors about applying to wrong types
                                // of things and check them. For the other map
                                // functions to allow more to be done.

                            default:
                                // NOTE: this is a correct virtual form for only
                                // 'apply' and 'funmake' others will need to be
                                // written out as multiplce calls. And are
                                // therefore still unsafe atleast untill we do
                                // the writing out...
                                $fname = $node->arguments[0];
                                if ($fname instanceof MP_PrefixOp && $fname->op === "'") {
                                    $fname = $fname->rhs;
                                }
                                $virtualfunction = new MP_FunctionCall($fname, array_slice($node->arguments, 1));
                                $virtualfunction->position['virtual'] = true;
                                $ofinterest[] = $virtualfunction;
                                break;
                        }
                        if (isset($node->position['virtual']) && !$safemap) {
                            // TODO: localise "Function application through mapping
                            // functions has depth limits as it hides things".
                            $errors[] = trim(stack_string('stackCas_deepmap'));
                            if (array_search('deepmap', $answernotes) === false) {
                                $answernotes[] = 'deepmap';
                            }
                            $valid = false;
                        }
                    }

                    // The sublist case.
                    if ($identifierrules->has_feature($node->name->value, 'argumentasfunction')) {
                        foreach (stack_cas_security::get_feature($node->name->value, 'argumentasfunction') as $ind) {
                            $virtualfunction = new MP_FunctionCall($node->arguments[$ind], array($node->arguments[0]));
                            $virtualfunction->position['virtual'] = true;
                            $ofinterest[] = $virtualfunction;
                        }
                    }

                } else if ($node->name instanceof MP_FunctionCall) {
                    $outter = $node->name;
                    if (($outter->name instanceof MP_Identifier || $outter->name instanceof MP_String)
                        && $outter->name->value === 'lambda') {
                        // This is safe, but we will not go out of our way to identify the function from further.
                        $notsafe = false;
                    } else if (($outter->name instanceof MP_Identifier || $outter->name instanceof MP_String)
                            && $outter->name->value === 'rand'
                            && count($outter->arguments) === 1
                            && $outter->arguments[0] instanceof MP_List) {
                        // @codingStandardsIgnoreStart
                        // Something like rand(["-","+"]) or rand(["cos","sin"]) applied to something.
                        // @codingStandardsIgnoreEnd
                        $notsafe = false;
                        foreach ($outter->arguments[0]->items as $name) {
                            // Name can be whatever the iteration will react to unsuitable things on the later loops.
                            $virtualfunction = new MP_FunctionCall($name, $node->arguments);
                            $virtualfunction->position['virtual'] = true;
                            $ofinterest[] = $virtualfunction;
                        }
                    } else {
                        // Calling the result of a function that is not lambda.
                        $errors[] = trim(stack_string('stackCas_callingasfunction',
                                                      array('problem' => stack_maxima_format_casstring($node->toString()))));
                        if (array_search('forbiddenWord', $answernotes) === false) {
                            $answernotes[] = 'forbiddenWord';
                        }
                        $valid = false;
                    }
                } else if ($node->name instanceof MP_Group) {
                    $outter = $node->name->items[count($node->name->items) - 1];
                    // We do this due to this (1,(cos,sin))(x) => sin(x).
                    $notsafe = false;
                    $virtualfunction = new MP_FunctionCall($outter, $node->arguments);
                    $virtualfunction->position['virtual'] = true;
                    $ofinterest[] = $virtualfunction;
                } else if ($node->name instanceof MP_Indexing) {
                    if (count($node->name->indices) === 1 && $node->name->target instanceof MP_List) {
                        $ind = -1;
                        if (count($node->name->indices[0]) === 1 && $node->name->indices[0]->items[0] instanceof MP_Integer) {
                            $ind = $node->name->indices[0]->items[0]->value - 1;
                        }
                        if ($ind >= 0 && $ind < count($node->name->target->items)) {
                            // We do this due to this because of examples such as [1,(cos,sin)][2](x) => sin(x).
                            $notsafe = false;
                            $virtualfunction = new MP_FunctionCall($node->name->target->items[$ind], $node->arguments);
                            $virtualfunction->position['virtual'] = true;
                            $ofinterest[] = $virtualfunction;
                        } else {
                            $notsafe = false;
                            foreach ($node->name->target->items as $id) {
                                $virtualfunction = new MP_FunctionCall($id, $node->arguments);
                                $virtualfunction->position['virtual'] = true;
                                $ofinterest[] = $virtualfunction;
                            }
                        }
                    }
                }
                if ($notsafe) {
                    // As in not safe identification of the function to be called.
                    $errors[] = trim(stack_string('stackCas_applyingnonobviousfunction',
                                                  array('problem' => $node->toString())));
                    if (array_search('forbiddenWord', $answernotes) === false) {
                        $answernotes[] = 'forbiddenWord';
                    }
                    $valid = false;
                }
            }
        }

        // Go through operators.
        foreach (array_keys($operators) as $op) {
            // First handle certain fixed special rules for ops.
            if ($op === '?' || $op === '?? ' || $op === '? ') {
                $errors[] = trim(stack_string('stackCas_qmarkoperators'));
                if (array_search('qmark', $answernotes) === false) {
                    $answernotes[] = 'qmark';
                }
                $valid = false;
            } else if ($this->source === 's' && ($op === "'" || $op === "''")) {
                $errors[] = trim(stack_string('stackCas_apostrophe'));
                if (array_search('apostrophe', $answernotes) === false) {
                    $answernotes[] = 'apostrophe';
                }
                $valid = false;
            } else if (!$identifierrules->is_allowed_as_operator($this->source, $op)) {
                $errors[] = trim(stack_string('stackCas_forbiddenOperator',
                        array('forbid' => stack_maxima_format_casstring($op))));
                if (array_search('forbiddenOp', $answernotes) === false) {
                    $answernotes[] = 'forbiddenOp';
                }
                $valid = false;
            }
        }

        // Go through function calls.
        foreach (array_keys($functionnames) as $name) {
            // Special feedback for 'In' != 'ln' depends on the allow status of
            // 'In' that is why it is here.
            $vars = $identifierrules->get_case_variants($name, 'function');

            if ($this->source === 's' && $name === 'In' && !$identifierrules->is_allowed_word($name, 'function')) {
                $errors[] = trim(stack_string('stackCas_badLogIn'));
                if (array_search('stackCas_badLogIn', $answernotes) === false) {
                    $answernotes[] = 'stackCas_badLogIn';
                }
                $valid = false;
            } else if ($this->source === 's' && count($vars) > 0 && array_search($name, $vars) === false) {
                // Case sensitivity issues.
                $errors[] = trim(stack_string('stackCas_unknownFunctionCase',
                    array('forbid' => stack_maxima_format_casstring($name),
                          'lower' => stack_maxima_format_casstring(implode(', ', $vars)))));
                if (array_search('unknownFunctionCase', $answernotes) === false) {
                    $answernotes[] = 'unknownFunctionCase';
                }
                $valid = false;
            } else if (!$identifierrules->is_allowed_to_call($this->source, $name)) {
                $errors[] = trim(stack_string('stackCas_forbiddenFunction',
                        array('forbid' => stack_maxima_format_casstring($name))));
                if (array_search('forbiddenFunction', $answernotes) === false) {
                    $answernotes[] = 'forbiddenFunction';
                }
                $valid = false;
            }
        }

        // Check for constants.
        foreach (array_keys($writtenvariables) as $name) {
            if ($identifierrules->has_feature($name, 'constant')) {
                // TODO: decide if we set this as validity issue, might break
                // materials where the constants redefined do not affect things.
                $errors[] = trim(stack_string('stackCas_redefinitionOfConstant',
                        array('constant' => stack_maxima_format_casstring($name))));
                if (array_search('writingToConstant', $answernotes) === false) {
                    $answernotes[] = 'writingToConstant';
                }
                $valid = false;
            }
            // Other checks happen at the $variables loop. These are all members of that.
        }

        if ($this->source === 's') {
            $emptyfungroup = array();
            $checkemptyfungroup = function($node) use (&$emptyfungroup) {
                // A function call with no arguments.
                if ($node instanceof MP_FunctionCall && count($node->arguments) === 0 ) {
                    $emptyfungroup[] = $node;
                }
                // A "group", programatic groups.
                if ($node instanceof MP_Group && count($node->items) === 0 ) {
                    $emptyfungroup[] = $node;
                }
                return true;
            };
            $ast->callbackRecurse($checkemptyfungroup);
            if (count($emptyfungroup) > 0) {
                $errors[] = trim(stack_string('stackCas_forbiddenWord',
                            array('forbid' => stack_maxima_format_casstring('()'))));
                if (array_search('emptyParens', $answernotes) === false) {
                    $answernotes[] = 'emptyParens';
                }
                $valid = false;
            }
        }

        /*
         * The rules of student identifiers are as follows, applies to whole
         * identifier or its subparts:
         *   Phase 1:
         *   if forbidden identifier in security-map then false else
         *   if present in forbidden words or contains such then false else
         *   if strlen() == 1 then true else
         *   if author used key then false else
         *   if strlen() > 2 and in allowed words then true else
         *   if strlen() > 2 and in security-map then true else
         *   if ends with a number then true else false
         *  Phase 2:
         *   if phase 1 = false then false else
         *   if units and not unit name and is unit case variant then false else
         *   if not (know or in security-map) and case variant in security-map then false else
         *   true
         */

        // Check for variables.
        foreach (array_keys($variables) as $name) {
            // Check for operators like 'and' if they appear as variables
            // things have gone wrong.
            if ($identifierrules->has_feature($name, 'operator')) {
                $errors[] = trim(stack_string('stackCas_operatorAsVariable',
                    array('op' => stack_maxima_format_casstring($name))));
                if (array_search('operatorPlacement', $answernotes) === false) {
                    $answernotes[] = 'operatorPlacement';
                }
                $valid = false;
                continue;
            }
            // For now apply only for students.
            if ($this->source === 's' && $identifierrules->get_units() === true &&
                    !$identifierrules->is_allowed_word($name, 'variable')) {
                // Check for unit synonyms. Ignore if specifically allowed.
                list ($fndsynonym, $answernote, $synonymerr) = stack_cas_casstring_units::find_units_synonyms($name);
                if ($answernote !== '' && array_search($answernote, $answernotes) === false) {
                    $answernotes[] = $answernote;
                }
                if ($this->source == 's' && $fndsynonym && !$identifierrules->is_allowed_word($name)) {
                    $errors[] = trim($synonymerr);
                    $valid = false;
                    continue;
                }
                $err = stack_cas_casstring_units::check_units_case($name);
                if ($err) {
                    // We have spotted a case sensitivity problem in the units.
                    $errors[] = trim($err);
                    if (array_search('unknownUnitsCase', $answernotes) === false) {
                        $answernotes[] = 'unknownUnitsCase';
                    }
                    $valid = false;
                    continue;
                }
            }

            if ($identifierrules->has_feature($name, 'globalyforbiddenvariable')) {
                // Very bad!
                $errors[] = trim(stack_string('stackCas_forbiddenWord',
                    array('forbid' => stack_maxima_format_casstring($name))));
                if (array_search('forbiddenWord', $answernotes) === false) {
                    $answernotes[] = 'forbiddenWord';
                }
                $valid = false;
                continue;
            }

            // TODO: Did I understand the split by underscores right?
            // Could we do that split on the PHP side to ensure security
            // covering any possible construction of function calls?
            $keys = array($name => true);
            // If the whole thing is allowed no need to split it down.
            if ($this->source === 's' && !$identifierrules->is_allowed_to_read($this->source, $name)) {
                $keys = array();
                foreach (explode("_", $name) as $kw) {
                    $keys[$kw] = true;
                }
            }
            foreach (array_keys($keys) as $n) {
                // We also allow function-identifiers that are allowed...
                if (!($identifierrules->is_allowed_to_read($this->source, $n) ||
                        ($name !== $n && $identifierrules->is_allowed_to_call($this->source, $n)))) {
                    if ($this->source === 't') {
                        $errors[] = trim(stack_string('stackCas_forbiddenWord',
                            array('forbid' => stack_maxima_format_casstring($n))));
                        if (array_search('forbiddenWord', $answernotes) === false) {
                            $answernotes[] = 'forbiddenWord';
                        }
                        $valid = false;
                    } else {
                        $vars = $identifierrules->get_case_variants($n, 'variable');
                        if (count($vars) > 0 && array_search($n, $vars) === false) {
                            $errors[] = trim(stack_string('stackCas_unknownVariableCase',
                                array('forbid' => stack_maxima_format_casstring($n),
                                'lower' => stack_maxima_format_casstring(
                                    implode(', ', $vars)))));
                            if (array_search('unknownVariableCase', $answernotes) === false) {
                                $answernotes[] = 'unknownVariableCase';
                            }
                            $valid = false;
                        } else {
                            $errors[] = trim(stack_string('stackCas_forbiddenVariable',
                                array('forbid' => stack_maxima_format_casstring($n))));
                            if (array_search('forbiddenVariable', $answernotes) === false) {
                                $answernotes[] = 'forbiddenVariable';
                            }
                            $valid = false;
                        }
                    }
                } else if (strlen($n) > 1) {
                    // We still need to try for case variants.
                    if ($this->source === 's') {
                        $vars = $identifierrules->get_case_variants($n, 'variable');
                        if (count($vars) > 0 && array_search($n, $vars) === false) {
                            $errors[] = trim(stack_string('stackCas_unknownVariableCase',
                                array('forbid' => stack_maxima_format_casstring($n),
                                'lower' => stack_maxima_format_casstring(
                                    implode(', ', $vars)))));
                            if (array_search('unknownVariableCase', $answernotes) === false) {
                                $answernotes[] = 'unknownVariableCase';
                            }
                            $valid = false;
                        }
                    }
                }
            }
        }

        // If not valid then we paint the whole tree invalid.
        if ($valid === false) {
            $paint = function($node) {
                $node->position['invalid'] = true;
                return true;
            };
            $ast->callbackRecurse($paint);
        }

        return $ast;
    }
}
