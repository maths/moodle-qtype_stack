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

defined('MOODLE_INTERNAL') || die();

/*
 @copyright  2018 Aalto University.
 @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
*/

require_once(__DIR__ . '/autogen/parser.mbstring.php');
// Also needs stack_string().
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

class maxima_parser_utils {

    /**
     * Parses a string of Maxima code to an AST tree for use elsewhere.
     *
     * @param string $code the Maxima code to parse.
     * @param string $parserule the parse rule to start with.
     * @param bool $allowpm whether to parse +-.
     * @return MP_Node the AST.
     */
    public static function parse(string $code, string $parserule = 'Root', bool $allowpm = true): MP_Node {
        /** @var MP_Node[] $cache cache of parsed expressions, with keys as below. */
        static $cache = [];

        $parseoptions = [
            'startRule' => $parserule,
            'letToken' => stack_string('equiv_LET'),
            'allowPM' => $allowpm
        ];
        if ($parserule === 'Root') {
            $cachekey = ($allowpm ? '|PM|' : '|noPM|') . $parseoptions['letToken'] . '|' . $code;
        } else {
            $cachekey = '';
        }

        if ($cachekey && isset($cache[$cachekey])) {
            return clone $cache[$cachekey];
        }

        $ast = self::do_parse($code, $parseoptions, $cachekey);

        if ($cachekey) {
            $cache[$cachekey] = clone $ast;
        }

        return $ast;
    }

    /**
     * Helper used by the previous method.
     *
     * @param string $code the Maxima code to parse.
     * @param array $parseoptions the parse rule to start with.
     * @param bool $allowpm whether to parse +-.
     * @return MP_Node the AST.
     */
    protected static function do_parse(string $code, array $parseoptions, string $cachekey): MP_Node {
        $muccachelimit = get_config('qtype_stack', 'parsercacheinputlength');

        $cache = null;
        if ($cachekey && $muccachelimit && strlen($code) >= $muccachelimit) {
            $cache = cache::make('qtype_stack', 'parsercache');
            $ast = $cache->get($cachekey);
            if ($ast) {
                return $ast;
            }
        }

        $parser = new MP_Parser();
        $ast = $parser->parse($code, $parseoptions);

        if ($cache) {
            $cache->set($cachekey, $ast);
        }
        return $ast;
    }


    // Takes a raw tree and the matching source code and remaps the positions from char to line:linechar
    // use when you need to have pretty printed position data.
    public static function position_remap(MP_Node $ast, string $code, array $limits = null) {
        if ($ast instanceof MP_Statement) {
            if ($limits === null) {
                $limits = array();
                foreach (explode("\n", $code) as $line) {
                    $limits[] = strlen($line) + 1;
                }
            }

            $c = $ast->position['start'];
            $l = 1;
            foreach ($limits as $ll) {
                if ($c < $ll) {
                    break;
                } else {
                    $c -= $ll;
                    $l += 1;
                }
            }
            $c += 1;
            $ast->position['start'] = "$l:$c";
            $c = $ast->position['end'];
            $l = 1;
            foreach ($limits as $ll) {
                if ($c < $ll) {
                    break;
                } else {
                    $c -= $ll;
                    $l += 1;
                }
            }
            $c += 1;
            $ast->position['end'] = "$l:$c";
            foreach ($ast->getChildren() as $node) {
                self::position_remap($node, $code, $limits);
            }
        }

        return $ast;
    }

    // Takes a raw tree and drops the comments sections from it.
    public static function strip_comments(MP_Root $ast) {
        // For now comments exist only at the top level and there are no "inline"
        // comments within statements, hopefully at some point we can go further.
        $nitems = array();
        foreach ($ast->items as $node) {
            if ($node instanceof MP_Comment) {
                continue;
            } else {
                $nitems[] = $node;
            }
        }
        if (count($nitems) !== count($ast->items)) {
            $ast->items = $nitems;
        }
        return $ast;
    }

    // Tries to parse a long string of statements and if not imediately valid
    // tries to fix by adding semicolons.
    public static function parse_and_insert_missing_semicolons($str, $lastfix = -1) {
        try {
            $ast = self::parse($str);
            if ($lastfix !== -1) {
                // If fixing has happened let's hide the fixed string to the result.
                // Might be useful for the editor to have a way of placing those
                // semicolons...
                // Again let's abuse the position array.
                $ast->position['fixedsemicolons'] = $str;
            }
            return $ast;
        } catch (SyntaxError $e) {
            if ($lastfix !== $e->grammarOffset && $lastfix + 1 !== $e->grammarOffset) {
                if (substr($str, $e->grammarOffset - 1, 2) === '/*') {
                    $fix = self::previous_non_whitespace($str, $e->grammarOffset - 1);
                } else {
                    $fix = self::previous_non_whitespace($str, $e->grammarOffset);
                }
                // Cut some memory leakage in the recursion here.
                $off = $e->grammarOffset;
                $e = null;

                return self::parse_and_insert_missing_semicolons($fix, $off);
            } else {
                return $e;
            }
        }

    }

    // Function to find suitable place to inject a semicolon to i.e. place into start of whitespace.
    private static function previous_non_whitespace($code, $pos) {
        $i = $pos;
        if (mb_substr($code, $i - 1, 2) === '/*') {
            $i--;
        }
        while ($i > 1 && self::is_whitespace(mb_substr($code, $i - 1, 1))) {
            $i--;
        }
        return mb_substr($code, 0, $i) . ';' . mb_substr($code, $i);
    }

    // Custom rules on what is an is not whitespace.
    private static function is_whitespace($mbc) {
        // So ctype_space does not handle those fancy unicode spaces...
        // There are more than these but we add things as we meet them.
        if (ctype_space($mbc)) {
            return true;
        }
        $num = ord($mbc);
        if ($num === 160 || $num === 8287 || $num < 33
            || ($num > 8191 && $num < 8208) || ($num > 8231 && $num < 8240)) {
            return true;
        }
        return false;
    }

    // Tool to extract information about which variables are being used and how.
    // In a given parsed section of code. Updates a given usage list so that use
    // for example in going through a PRT tree is convenient.
    public static function variable_usage_finder($ast, $output=array()) {
        if (!array_key_exists('read', $output)) {
            $output['read'] = array();
        }
        if (!array_key_exists('write', $output)) {
            $output['write'] = array();
        }
        if (!array_key_exists('calls', $output)) {
            $output['calls'] = array();
        }
        if (!array_key_exists('declares', $output)) {
            $output['declares'] = array();
        }
        $recursion = function($node) use(&$output) {
            // Feel free to expand this to track any other types of usages,
            // like functions and their definitions.
            if ($node instanceof MP_Identifier) {
                if ($node->is_variable_name()) {
                    if ($node->is_being_written_to()) {
                        $output['write'][$node->value] = true;
                    } else {
                        $output['read'][$node->value] = true;
                    }
                } else if (!$node->parentnode->is_definition()) {
                    $output['calls'][$node->value] = true;
                } else {
                    $output['declares'][$node->value] = true;
                }
            }
            return true;
        };
        $ast->callbackRecurse($recursion);

        return $output;
    }

    // Tool to build an estimate of the identifiers and their types and values
    // for future insert-stars logic. The map will describe the values as follows,
    // there will be an array of the values it receives the array will include:
    // 1. -1 if the value is a custom function definition.
    // 2. ASTs of all the values assigned.
    // 3. will be empty if no assings happen but is still used.
    // For insert-stars purposes if the identifier has count=1 items in the array and
    // the only key present is -1 then the identifier is a function defined in
    // the question and should be used as such.
    public static function identify_identifier_values($ast, $expand=[]): array {
        $output = array_merge($expand, []);
        $statement = null;
        // I know this is not well documentted... we'll get to that.
        $sec = new stack_cas_security();
        $recursion = function($node) use(&$output, &$statement, $sec) {
            if ($node instanceof MP_Identifier) {
                if (!isset($output[$node->value])) {
                    $output[$node->value] = [];
                }
            }
            if ($node instanceof MP_Statement) {
                // Track the statement we are in.
                $statement = $node;
                if (!$statement->statement instanceof MP_Operation || ($statement->statement !== ':' && $statement->statement !== ':=')) {
                    if ($statement->flags !== null) {
                        foreach ($statement->flags as $flag) {
                            if ($flag->name instanceof MP_Identifier) {
                                if (!isset($output[$flag->name->value])) {
                                    $output[$flag->name->value] = [];
                                }
                                $str = $flag->value->toString();
                                $output[$flag->name->value][$str] = clone $flag->value;
                            }
                        }
                    }
                }
            }
            if ($node instanceof MP_Operation) {
                if ($node->op === ':' && $node->lhs instanceof MP_Identifier && !(
                    $node->rhs instanceof MP_Operation && $node->rhs->op === ':=')) {
                    $v = clone $node->rhs;
                    if ($statement->flags !== null && count($statement->flags) > 0) {
                        $flags = [];
                        foreach ($statement->flags as $flag) {
                            $flags[] = new MP_Operation(':', clone $flag->name, clone $flag->value);
                        }
                        $v = new MP_FunctionCall(new MP_Identifier('ev'), array_merge([$v], $flags));
                    }
                    $str = $v->toString();
                    if (isset($output[$node->lhs->value])) {
                        $output[$node->lhs->value][$str] = $v;
                    } else {
                        $output[$node->lhs->value] = [$str => $v];
                    }
                }
                if ($node->op === ':=' && $node->lhs instanceof MP_FunctionCall) {
                    $output[$node->lhs->name->value][-1] = -1;
                }
            }
            return true;
        };
        $ast->callbackRecurse($recursion);

        // Now we have the definitions of all the bound variables, lets expand
        // them out to figure out of which unbound variables they are made of.
        // Note that the end result should tell us which are static values.
        $trg = null;
        $val = null;
        $replace = function($node) use(&$trg, &$val) {
            if ($node instanceof MP_Identifier && $node->value === $trg && !$node->is_being_written_to()) {
                $node->parentnode->replace($node, clone $val);
            }
            return true;
        };
        $clean = [];
        $unclean = [];
        $simp = function($node) {
            if ($node instanceof MP_Group && count($node->items) > 1) {
                $node->items = [end($node->items)];
                return false;
            }
            if ($node instanceof MP_Group && count($node->items) == 1 && $node->items[0] instanceof MP_Atom) {
                $node->parentnode->replace($node, $node->items[0]);
                return false;
            }
            return true;
        };
        foreach ($output as $key => $values) {
            $clean[$key] = [];
            if (count($values) === 0) {
                $clean[$key] = $values;
            } else {
                foreach ($values as $str => $value) {
                    if (is_integer($str)) {
                        $clean[$key][$str] = $value;
                    } else {
                        if (!isset($unclean[$key])) {
                            $unclean[$key] = [$str => $value];
                        } else {
                            $unclean[$key][$str] = $value;
                        }
                    }
                }
            }
        }
        // Remove cleans.
        $clear1 = [1];
        while (count($clear1) > 0) {
            $clear1 = [];
            foreach ($unclean as $key => $values) {
                $clear2 = [];
                foreach ($values as $vk => $value) {
                    $usage = self::variable_usage_finder($value);
                    // Now if it uses none of the uncleans it is clean.
                    $good = true;
                    foreach ($usage['read'] as $k => $v) {
                        if ($key !== $k && isset($unclean[$k])) {
                            $good = false;
                            break;
                        }
                    }
                    foreach ($usage['calls'] as $k => $v) {
                        if ($key !== $k && isset($unclean[$k])) {
                            $good = false;
                            break;
                        }
                    }
                    if ($good) {
                        $clear2[] = $vk;
                        $clean[$key][$vk] = $value;
                    }
                }
                foreach ($clear2 as $k) {
                    unset($values[$k]);
                }
                if (count($values) === 0) {
                    $clear1[] = $key;
                }
            }
            foreach ($clear1 as $k) {
                unset($unclean[$k]);
            }
        }

        while (count($unclean) > 0) {
            // Remove cleans.
            $clear1 = [];
            foreach ($unclean as $key => $values) {
                $clear2 = [];
                foreach ($values as $vk => $value) {
                    $usage = self::variable_usage_finder(new MP_Group([$value]));
                    // Now if it uses none of the uncleans it is clean.
                    $good = true;
                    foreach ($usage['read'] as $k => $v) {
                        if ($key !== $k && isset($unclean[$k])) {
                            $good = false;
                            break;
                        }
                    }
                    foreach ($usage['calls'] as $k => $v) {
                        if ($key !== $k && isset($unclean[$k])) {
                            $good = false;
                            break;
                        }
                    }
                    if ($good) {
                        $clear2[] = $vk;
                        $clean[$key][$vk] = $value;
                    }
                }
                foreach ($clear2 as $k) {
                    unset($values[$k]);
                }
                if (count($values) === 0) {
                    $clear1[] = $key;
                }
            }
            foreach ($clear1 as $k) {
                unset($unclean[$k]);
            }
            foreach (array_keys($unclean) as $key) {
                $trg = $key;
                foreach ($unclean as $k => $values) {
                    if ($key !== $k) {
                        $clear1 = [];
                        $adds = [];
                        foreach ($values as $vk => $value) {
                            if (mb_strpos($vk, $key) !== false) {
                                foreach ($unclean[$key] as $alt) {
                                    $tmp = new MP_Group([$alt]);
                                    $val = new MP_Identifier('rec ' . $key);
                                    $tmp->callbackRecurse($replace);
                                    $val = $tmp->items[0];
                                    $tmp = new MP_Group([clone $value]);
                                    $tmp->callbackRecurse($replace);
                                    while (!$tmp->callbackRecurse($simp)){};
                                    if ($vk !== $tmp->items[0]->toString()) {
                                        $clear1[$vk] = $vk;
                                        $adds[$tmp->items[0]->toString()] = $tmp->items[0];
                                    }
                                }
                            }
                        }
                        foreach ($adds as $k3 => $v3) {
                            $unclean[$k][$k3] = $v3;
                        }
                        foreach ($clear1 as $k2) {
                           unset($unclean[$k][$k2]);
                        }
                    }
                }
            }
        }
        // Then substitutions.
        $subst = function($node) use(&$trg, &$val, &$clean, &$replace, &$simp)  {
            if ($node instanceof MP_FunctionCall && ($node->name instanceof MP_Identifier || $node->name instanceof MP_String)) {
                if ($node->name->value === 'ev' && count($node->arguments) >= 2) {
                    $tmp = new MP_Group([$node->arguments[0]]);
                    foreach (array_slice($node->arguments, 1) as $arg) {
                        if ($arg instanceof MP_Operation && ($arg->op === ':' || $arg->op === '=')) {
                            $trg = $arg->lhs->toString();
                            $val = $arg->rhs;
                            $clean[$trg][$val->toString()] = $val;
                            $tmp->callbackRecurse($replace);
                            while (!$tmp->callbackRecurse($simp)){};
                        }
                    }
                    $node->parentnode->replace($node, $tmp->items[0]);
                    return false;
                } else if ($node->name->value === 'subst' && count($node->arguments) >= 3) {
                    $tmp = new MP_Group([$node->arguments[2]]);
                    $trg = $node->arguments[1]->toString();
                    $val = $node->arguments[0];
                    $clean[$trg][$val->toString()] = $val;
                    $tmp->callbackRecurse($replace);
                    while (!$tmp->callbackRecurse($simp)){};
                    $node->parentnode->replace($node, $tmp->items[0]);
                    return false;
                } else if ($node->name->value === 'subst' && count($node->arguments) == 2 && $node->arguments[0] instanceof MP_List) {
                    $node->name->value = 'ev';
                    $node->arguments = array_merge([$node->arguments[1]], $node->arguments[0]->items);
                    return false;
                } else if ($node->name->value === 'subst' && count($node->arguments) == 2 && $node->arguments[0] instanceof MP_Operation) {
                    $node->name->value = 'ev';
                    $node->arguments = [$node->arguments[1], $node->arguments[0]];
                    return false;
                }
            }
            return true;
        };
        $revert = function($node) {
            if ($node instanceof MP_Identifier && strpos($node->value, 'rec ') === 0) {
                $node->value = mb_substr($node->value, 4);
            }
            return true;
        };
        $output = [];
        foreach ($clean as $key => $values) {
            $output1[$key] = [];
            foreach ($values as $kv => $value) {
                if (!is_integer($kv) && (mb_strpos($kv, 'ev(') !== false || mb_strpos($kv, 'subst(') !== false)) {
                    $tmp = new MP_Group([$value]);
                    while (!$tmp->callbackRecurse($subst)){};
                    while (!$tmp->callbackRecurse($simp)){};
                    $tmp->callbackRecurse($revert);
                    $output[$key][$tmp->items[0]->toString()] = $tmp->items[0];
                } else {
                    if ($value instanceof MP_Node) {
                        $tmp = new MP_Group([$value]);
                        $tmp->callbackRecurse($revert);
                        $output[$key][$tmp->items[0]->toString()] = $tmp->items[0];
                    } else {
                        $output[$key][$kv] = $value;
                    }
                }
            }
        }
        // Final filter, if the expression is overly complex we will note it with
        // this. This should be handled as unidentifiable. Note that while we can 
        // generate this parsing it back from the cache is the problem.
        $outputf = [];
        foreach ($output as $key => $values) {
            $outputf[$key] = [];
            foreach ($values as $kv => $value) {
                if (is_integer($kv) || mb_strlen($kv) < 200) {
                    $outputf[$key][$kv] = $value;
                } else {
                    $usage = self::variable_usage_finder(new MP_Group([$value]));
                    $tmp = new MP_FunctionCall(new MP_Identifier('stack_complex_expression'), []);
                    $usage = array_merge($usage['read'], $usage['calls']);
                    ksort($usage);
                    foreach ($usage as $k => $v) {
                        $tmp->arguments[] = new MP_Identifier($k);
                    }
                    $outputf[$key][$tmp->toString()] = $tmp;
                }
            }
        }


        return $outputf;
    }

}
