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
require_once(__DIR__ . '/baselogic.class.php');
require_once(__DIR__ . '/../../maximaparser/MP_classes.php');

// This class is the base of the old insert stars logics 0-5.
class stack_parser_logic_insertstars0 extends stack_parser_logic {
    // These control the logic, if these are false the logic will tag
    // things as invalid if it meets core syntax rules matching these.
    protected $insertstars = false;
    protected $fixspaces = false;

    public function __construct($insertstars = false, $fixspaces = false) {
        $this->insertstars = $insertstars;
        $this->fixspaces = $fixspaces;
    }

    public function parse(&$string, &$valid, &$errors, &$answernote, $syntax, string $parserule = 'Root', bool $units = false) {
        $ast = $this->preparse($string, $valid, $errors, $answernote, $this->insertstars, $this->fixspaces, $parserule);
        // If the parser fails it has already markeed all the correct errors.
        if ($ast === null) {
            return null;
        }
        // Fix the common magic markkers.
        $this->commonpostparse($ast);
        // Give a place to hook things in.
        $this->pre($ast, $valid, $errors, $answernote, $syntax, $units);
        // The common insertstars rules.
        $this->handletree($ast, $valid, $errors, $answernote, $syntax, $units);
        // Give a place to hook things in.
        $this->post($ast, $valid, $errors, $answernote, $syntax, $units);

        // Common stars insertion error.
        if (!$valid || !$this->insertstars) {
            $hasany = false;
            $check = function($node)  use(&$hasany) {
                if ($node instanceof MP_Operation && $node->op === '*' && isset($node->position['insertstars'])) {
                    $hasany = true;
                }
                return true;
            };
            $ast->callbackRecurse($check);
            if ($hasany) {
                if (array_search('missing_stars', $answernote) === false) {
                    $answernote[] = 'missing_stars';
                }
                // As we output the AST as a whole including the MP_Root there will be extra chars at the end. But it might also come form other parsing rules...
                $missingstring = $ast->toString(array('insertstars_as_red' => true, 'qmchar' => true, 'inputform' => true));
                if ($ast instanceof MP_Root) {
                    $missingstring = core_text::substr($missingstring, 0, -2);
                }

                $a = array();
                $a['cmd']  = stack_maxima_format_casstring($missingstring);
                $errors[] = stack_string('stackCas_MissingStars', $a);
            }
        }

        // Common spaces insertion errors.
        if (!$valid || !$this->fixspaces) {
            $hasany = false;
            $checks = function($node)  use(&$hasany) {
                if ($node instanceof MP_Operation && $node->op === '*' && isset($node->position['fixspaces'])) {
                    $hasany = true;
                }
                return true;
            };
            $ast->callbackRecurse($checks);
            if ($hasany) {
                if (array_search('spaces', $answernote) === false) {
                    $answernote[] = 'spaces';
                }
                $missingstring = $ast->toString(
                        array('fixspaces_as_red_spaces' => true, 'qmchar' => true, 'inputform' => true));
                if ($ast instanceof MP_Root) {
                    $missingstring = core_text::substr($missingstring, 0, -2);
                }
                $a = array();
                $a['expr']  = stack_maxima_format_casstring($missingstring);
                $errors[] = stack_string('stackCas_spaces', $a);
            }
        }

        return $ast;
    }

    // Here is a hook to place things if you want to extend.
    public function pre($ast, &$valid, &$errors, &$answernote, $syntax, $units) {
        return;
    }

    // Here is a hook to place things if you want to extend.
    public function post($ast, &$valid, &$errors, &$answernote, $syntax, $units) {
        return;
    }

    // This applies all the common old rules about suitable names and
    // numbers mixed in them. Also certain other old tricks.
    private function handletree($ast, &$valid, &$errors, &$answernote, $syntax, $units) {
        $identifiedsinglelettervariables = array();

        $process = function($node) use (&$valid, $errors, &$answernote, $syntax, $units, &$identifiedsinglelettervariables) {
            if ($node instanceof MP_FunctionCall) {
                // Do not touch functions with names that are safe.
                if (($node->name instanceof MP_Identifier ||
                        $node->name instanceof MP_String) && array_key_exists($node->name->value, stack_cas_security::get_protected_identifiers('function', $units))) {
                    return true;
                }
                // Skip the very special identifiers for log-candy.
                if (($node->name instanceof MP_Identifier ||
                        $node->name instanceof MP_String) && ($node->name->value === 'log10' ||
                        core_text::substr($node->name->value, 0, 4) === 'log_')) {
                    return true;
                }
                // a(x)(y) => a(x)*(y) or (x)(y) => (x)*(y)
                if ($node->name instanceof MP_FunctionCall || $node->name instanceof MP_Group) {
                    $answernote[] = 'missing_stars';
                    if (!$this->insertstars) {
                        $valid = false;
                    }
                    $newop = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                    $newop->position['insertstars'] = true;
                    $node->parentnode->replace($node, $newop);
                    return false;
                }
                if ($node->name instanceof MP_Identifier) {
                    // Students may not have functionnames ending with numbers...
                    if (ctype_digit(core_text::substr($node->name->value, -1))) {
                        $replacement = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                        $answernote[] = 'missing_stars';
                        if (!$this->insertstars) {
                            $valid = false;
                        }
                        $replacement->position['insertstars'] = true;
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    } else if ($node->name->value === 'i') {
                        $replacement = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                        $answernote[] = 'missing_stars';
                        if (!$this->insertstars) {
                            $valid = false;
                        }
                        $replacement->position['insertstars'] = true;
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    } else if (!$syntax && core_text::strlen($node->name->value) === 1 &&
                            isset($identifiedsinglelettervariables[$node->name->value])) {
                        // Single character function names... TODO: what is this!?
                        $replacement = new MP_Operation('*', $node->name, new MP_Group($node->arguments));
                        $answernote[] = 'missing_stars';
                        if (!$this->insertstars) {
                            $valid = false;
                        }
                        $replacement->position['insertstars'] = true;
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    }
                }
            } else if ($node instanceof MP_Identifier && !($node->parentnode instanceof MP_FunctionCall)) {
                // Do not touch variables that are safe. e.g. unit names.
                if (array_key_exists($node->value, stack_cas_security::get_protected_identifiers('variable', $units))) {
                    return true;
                }
                // Skip the very special identifiers for log-candy. These will be reconstructed
                // as fucntion calls elsewhere.
                if ($node->value === 'log10' || core_text::substr($node->value, 0, 4) === 'log_') {
                    return true;
                }
                // E.g. x3 => x*3, we could handle the 2-char case in the latter ones too...
                if (!$syntax && core_text::strlen($node->value) === 2 && ctype_alpha(core_text::substr($node->value, 0, 1)) &&
                        ctype_digit(core_text::substr($node->value, 1, 1))) {
                    // Binding powers will be wrong but we are not evaluating stuff here.
                    $replacement = new MP_Operation('*', new MP_Identifier(core_text::substr($node->value, 0, 1)),
                            new MP_Integer((int) core_text::substr($node->value, 1, 1)));
                    $answernote[] = 'missing_stars';
                    if (!$this->insertstars) {
                        $valid = false;
                    }
                    $replacement->position['insertstars'] = true;
                    $node->parentnode->replace($node, $replacement);
                    return false;
                }
            }
            if ($node instanceof MP_Identifier) {
                // Identify single letter varaible names.
                if (core_text::strlen($node->value) === 1 &&
                        !isset($identifiedsinglelettervariables[$node->value]) && !$node->is_function_name()) {
                    $identifiedsinglelettervariables[$node->value] = true;
                    return false;
                }

                // Skip the very special identifiers for log-candy.
                // These will be reconstructed as fucntion calls elsewhere.
                if ($node->value === 'log10' || core_text::substr($node->value, 0, 4) === 'log_') {
                    return true;
                }

                // Check for a1b2c => a1*b2*c, i.e. shifts from number to letter in the name.
                $splits = array();
                $alpha = true;
                $last = 0;
                for ($i = 1; $i < core_text::strlen($node->value); $i++) {
                    if ($alpha && ctype_digit(core_text::substr($node->value, $i, 1))) {
                        $alpha = false;
                    } else if (!$alpha && ctype_alpha(core_text::substr($node->value, $i, 1))) {
                        $alpha = false;
                        $splits[] = core_text::substr($node->value, $last, $i - $last);
                        $last = $i;
                    }
                }
                $splits[] = core_text::substr($node->value, $last);
                if (count($splits) > 1) {
                    $answernote[] = 'missing_stars';
                    if (!$this->insertstars) {
                        $valid = false;
                    }
                    // Initial bit is turned to multiplication chain. The last one need to check for function call.
                    $temp = new MP_Identifier('rhs');
                    $replacement = new MP_Operation('*', new MP_Identifier($splits[0]), $temp);
                    $replacement->position['insertstars'] = true;
                    $iter = $replacement;
                    $i = 1;
                    for ($i = 1; $i < count($splits) - 1; $i++) {
                        $iter->replace($temp, new MP_Operation('*', new MP_Identifier($splits[$i]), $temp));
                        $iter = $iter->rhs;
                        $iter->position['insertstars'] = true;
                    }
                    if ($node->is_function_name()) {
                        $iter->replace($temp, new MP_FunctionCall(new MP_Identifier($splits[$i]), $node->parentnode->arguments));
                        $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                    } else {
                        $iter->replace($temp, new MP_Identifier($splits[$i]));
                        $node->parentnode->replace($node, $replacement);
                    }
                    return false;
                }
                // xyz12 => xyz*12 but not x_1 => x_*1
                if (!$syntax && ctype_digit(core_text::substr($node->value, -1))) {
                    $i = 0;
                    for ($i = 0; $i < core_text::strlen($node->value); $i++) {
                        if (ctype_digit(core_text::substr($node->value, $i, 1)) &&
                                ctype_alpha(core_text::substr($node->value, $i - 1, 1))) {
                            break;
                        }
                    }
                    if ($i < core_text::strlen($node->value)) {
                        // Note after the "a1b2c" the split should be clean and the remainder is just an integer.
                        $replacement = new MP_Operation('*', new MP_Identifier(core_text::substr($node->value, 0, $i)),
                                new MP_Integer((int) core_text::substr($node->value, $i)));
                        $replacement->position['insertstars'] = true;
                        if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name === $node) {
                            $replacement->rhs = new MP_Operation('*', $replacement->rhs,
                                    new MP_Group($node->parentnode->arguments));
                            $replacement->rhs->position['insertstars'] = true;
                            $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                        } else {
                            $node->parentnode->replace($node, $replacement);
                        }
                        $answernote[] = 'missing_stars';
                        if (!$this->insertstars) {
                            $valid = false;
                        }
                        return false;
                    }
                }
            }
            // Disabled for now. May need additional context to decide whether this gets done.
            if (false && !$syntax && $node instanceof MP_Float && $node->raw !== null) {
                // TODO: When and how does this need to break the floats?
                // This is one odd case to handle but maybe some people want to kill floats like this.
                $replacement = false;
                if (strpos($node->raw, 'e') !== false) {
                    $parts = explode('e', $node->raw);
                    if (strpos($parts[0], '.') !== false) {
                        $replacement = new MP_Operation('*', new MP_Float(floatval($parts[0]), null),
                                new MP_Operation('*', new MP_Identifier('e'), new MP_Integer(intval($parts[1]))));
                    } else {
                        $replacement = new MP_Operation('*', new MP_Integer(intval($parts[0])),
                                new MP_Operation('*', new MP_Identifier('e'), new MP_Integer(intval($parts[1]))));
                    }
                    $replacement->position['insertstars'] = true;
                    if ($parts[1]{0} === '-' || $parts[1]{0} === '+') {
                        // 1e+1...
                        $op = $parts[1]{0};
                        $val = abs(intval($parts[1]));
                        $replacement = new MP_Operation($op, new MP_Operation('*', $replacement->lhs,
                                new MP_Identifier('e')), new MP_Integer($val));
                        $replacement->lhs->position['insertstars'] = true;
                    }
                } else if (strpos($node->raw, 'E') !== false) {
                    $parts = explode('E', $node->raw);
                    if (strpos($parts[0], '.') !== false) {
                        $replacement = new MP_Operation('*', new MP_Float(floatval($parts[0]), null),
                                new MP_Operation('*', new MP_Identifier('E'), new MP_Integer(intval($parts[1]))));
                    } else {
                        $replacement = new MP_Operation('*', new MP_Integer(intval($parts[0])),
                                new MP_Operation('*', new MP_Identifier('E'), new MP_Integer(intval($parts[1]))));
                    }
                    $replacement->position['insertstars'] = true;
                    if ($parts[1]{0} === '-' || $parts[1]{0} === '+') {
                        // 1.2E-1...
                        $op = $parts[1]{0};
                        $val = abs(intval($parts[1]));
                        $replacement = new MP_Operation($op, new MP_Operation('*', $replacement->lhs,
                                new MP_Identifier('E')), new MP_Integer($val));
                        $replacement->lhs->position['insertstars'] = true;
                    }
                }
                if ($replacement !== false) {
                    $answernote[] = 'missing_stars';
                    if (!$this->insertstars) {
                        $valid = false;
                    }
                    $node->parentnode->replace($node, $replacement);
                    return false;
                }
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd

        // There is a chance that the pre-parser added stars that we don't
        // aknowledge yet. Search for them. These cover a relatively rare edge
        // case that probably is not worth it. Essenttially, if the answernote
        // does not containt those specific flags we will never go looking for
        // these.
        if (array_search('missing_stars', $answernote) === false) {
            $hasany = false;
            $check = function($node) use(&$hasany) {
                if ($node instanceof MP_Operation && $node->op === '*' && isset($node->position['insertstars'])) {
                    $hasany = true;
                }
                return true;
            };
            $ast->callbackRecurse($check);
            if ($hasany) {
                $answernote[] = 'missing_stars';
                if (!$this->insertstars) {
                    $valid = false;
                }
            }
        }
        if (array_search('spaces', $answernote) === false) {
            $hasany = false;
            $check = function($node) use(&$hasany) {
                if ($node instanceof MP_Operation && $node->op === '*' && isset($node->position['fixspaces'])) {
                    $hasany = true;
                }
                return true;
            };
            $ast->callbackRecurse($check);
            if ($hasany) {
                $answernote[] = 'spaces';
                if (!$this->fixspaces) {
                    $valid = false;
                }
            }
        }
    }
}
