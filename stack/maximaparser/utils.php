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
require_once(__DIR__ . '/MP_classes.php');

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
        if ($limits === null) {
            $limits = array();
            foreach (explode("\n", $code) as $line) {
                $limits[] = strlen($line) + 1;
            }
        }

        $trg = isset($ast->position['start']) ? $ast->position['start'] : 0;
        $c = 1;
        $l = 0;
        $count = 0;
        foreach ($limits as $ll) {
            $count += $ll;
            $l++;
            if ($trg < $count) {
                $count -= $ll;
                $c = $trg - $count;
                break;
            }
        }
        $c += 1;
        $trg = isset($ast->position['end']) ? isset($ast->position['end']) : $trg + mb_strlen($ast->toString());
        $ast->position['start'] = "$l:$c";
        $c = 1;
        $l = 0;
        $count = 0;
        foreach ($limits as $ll) {
            $count += $ll;
            $l++;
            if ($trg < $count) {
                $count -= $ll;
                $c = $trg - $count;
                break;
            }
        }
        $c += 1;
        $ast->position['end'] = "$l:$c";
        foreach ($ast->getChildren() as $node) {
            self::position_remap($node, $code, $limits);
        }

        return $ast;
    }

    // This one removes comments before parsing.
    // For those cases where you just must check for some chars.
    public static function remove_comments(string $src): string {
        $chars = preg_split('//u', $src, -1, PREG_SPLIT_NO_EMPTY);

        $r = '';

        $instring = false;
        $incomment = false;
        $lastslash = false;
        $cc = count($chars);
        for ($i = 0; $i < $cc; $i++) {
            $c = $chars[$i];
            if ($instring) {
                if ($c === "\\") {
                    $lastslash = !$lastslash;
                } else if ($c === '"' && !$lastslash) {
                    $instring = false;
                }
            } else if ($incomment) {
                if ($c === '*' && $i + 1 < $cc && $chars[$i + 1] === '/') {
                    $i++;
                    $incomment = false;
                    continue;
                }
            } else {
                if ($c === '/' && $i + 1 < $cc && $chars[$i + 1] === '*') {
                    $i++;
                    $incomment = true;
                    continue;
                } else if ($c === '"') {
                    $instring = false;
                }
            }
            if (!$incomment) {
                $r .= $c;
            }
        }
        // If we have a hanging comment we need return the original
        // string so that the full parser can trigger correct errors.
        // Note that this means that we don't remove any comments if
        // even one of them is faulty, but that should not matter as
        // things will break with syntax errors no matter what we do.
        if ($incomment) {
            return $src;
        }

        return $r;
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

    // Will generate a singular AST with position remaps and inlined included statements.
    // Generates errors if inclusions within inclusions or inclusions in unexpected places.
    // Returns either the AST or some form of an exception.
    public static function parse_and_insert_missing_semicolons_with_includes($str) {
        static $remotes = [];

        $root = self::parse_and_insert_missing_semicolons($str);
        if ($root instanceof MP_Root) {
            if (isset($root->position['fixedsemicolons'])) {
                $root = self::position_remap($root, $root->position['fixedsemicolons']);
            } else {
                $root = self::position_remap($root, $str);
            }
            // Ok now seek for the inclusions if any are there.
            $includecount = 0;
            $errors = [];
            $include = function($node) use (&$includecount, &$errors, &$remotes) {
                if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom &&
                    $node->name->value === 'stack_include') {
                    // Now the first requirement for this is that this must be a top level item
                    // in this statement, this statement may not have flags or anythign else.
                    if ($node->parentnode instanceof MP_Statement) {
                        if (count($node->arguments) === 1 && $node->arguments[0] instanceof MP_String) {
                            if ($node->parentnode->flags === null || count($node->parentnode->flags) === 0) {
                                // Count them to give numbered errors, do not give the included address, ever.
                                $includecount = $includecount + 1;
                                $srccode = '1';
                                // Various repeated validation steps may lead to multiple fetches for a single
                                // request, lets not do those, lets save some bandwith for those that share
                                // such stuff.
                                if (isset($remotes[$node->arguments[0]->value])) {
                                    $srccode = $remotes[$node->arguments[0]->value];
                                } else {
                                    $srccode = file_get_contents($node->arguments[0]->value);
                                    $remotes[$node->arguments[0]->value] = $srccode;
                                }
                                if ($srccode === false) {
                                    // Do not give the address in the output.
                                    $errors[] = 'stack_include, could not retrieve: #' . $includecount;
                                    $node->name->value = 'failed_stack_include';
                                    $node->position['invalid'] = true;
                                    return true;
                                }
                                $src = self::parse_and_insert_missing_semicolons($srccode);
                                if ($src instanceof MP_Root) {
                                    // For completeness sake check for the existence of includes.
                                    $usage = self::variable_usage_finder($src);
                                    if (isset($usage['calls']) && isset($usage['calls']['stack_include'])) {
                                        // Why not is simply because we do not want to deal with cycles or
                                        // trying to keep track of the error messages. It is better to guide
                                        // towards less deep hierarchys of included content. And if someone
                                        // wants to do deppeer includes they may just give an url to a serverside
                                        // include using system, which is a fine way for buildign a package management
                                        // system for thes sorts of things.
                                        $errors[] = 'stack_include, include includes includes, we do not allow that: #' .
                                            $includecount;
                                        $node->name->value = 'failed_stack_include';
                                        $node->position['invalid'] = true;
                                        return true;
                                    }

                                    if (isset($src->position['fixedsemicolons'])) {
                                        $src = self::position_remap($src, $src->position['fixedsemicolons']);
                                    } else {
                                        $src = self::position_remap($src, $srccode);
                                    }
                                    // Simply remove the include statement and inject the parsed ones
                                    // in its place, tag the statements with a source detail to help error tracking.
                                    $replacement = [];
                                    foreach ($node->parentnode->parentnode->items as $i) {
                                        if ($i === $node->parentnode) {
                                            foreach ($src->items as $item) {
                                                $item->position['included-from'] = 'inclusion #' . $includecount;
                                                $item->position['included-src'] = $node->arguments[0]->value;
                                                $item->parentnode = $node->parentnode->parentnode;
                                                $replacement[] = $item;
                                            }
                                        } else {
                                            $replacement[] = $i;
                                        }
                                    }
                                    // This is the root node, which has the comments and statements as its items.
                                    // We do include even the comments, maybe in the future they include annotations.
                                    $node->parentnode->parentnode->items = $replacement;
                                    return false;
                                } else {
                                    $node->name->value = 'failed_stack_include';
                                    $errors[] = 'stack_include, a parse error inside the include #' . $includecount .
                                        ': ' . $src->getMessage();
                                    return false;
                                }
                            } else {
                                $node->name->value = 'failed_stack_include';
                                // This is mainly because I am lazy, but it does make the handling of the include
                                // side error reportting quite a lot simpler.
                                $errors[] = 'stack_include-statements may not have evaluation-flags.';
                                return true;
                            }
                        } else {
                            $node->name->value = 'failed_stack_include';
                            $errors[] = 'stack_include must have one and only one static string value as its argument.';
                            return true;
                        }
                    } else {
                        // End this ones processing.
                        $node->name->value = 'failed_stack_include';
                        $errors[] = 'stack_include must not be wrapped in any complex processing, ' .
                            'it must be a top-level statement.';
                        return true;
                    }
                }
                return true;
            };
            // @codingStandardsIgnoreStart
            while ($root->callbackRecurse($include) !== true) {}
            // @codingStandardsIgnoreEnd

            // TODO: wrap those errors into something more readable.
            if (count($errors) > 0) {
                // Returning an exception because we already either return an excpetion or the root node, so why
                // have even more types in play.
                return new stack_exception(implode(',', $errors));
            }
            return $root;
        } else {
            // Even the first level was bad.
            return $root;
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

    // Turns MP_Nodes to raw PHP objects like strings/numbers arrays...
    // Note that this identifies stackmaps by default.
    // Also after this has done its thing you will not be able to separate strings from identifiers.
    // Intended for processing complex return values from CAS using PHP methods.
    public static function mp_to_php(
        MP_Node $in,
        bool $stackmaps = true
    ) {
        if ($in instanceof MP_Atom) {
            return $in->value;
        }
        if ($in instanceof MP_Root) {
            return self::mp_to_php($in->items[0]);
        }
        if ($in instanceof MP_Statement) {
            return self::mp_to_php($in->statement);
        }
        if ($in instanceof MP_Set || ($in instanceof MP_List && !$stackmaps)) {
            $r = [];
            foreach ($in->items as $item) {
                $r[] = self::mp_to_php($item);
            }
            return $r;
        }
        if ($in instanceof MP_List) {
            $r = [];
            foreach ($in->items as $item) {
                $r[] = self::mp_to_php($item);
            }
            if (count($r) > 0 && $r[0] === 'stack_map') {
                $m = [];
                for ($i = 1; $i < count($r); $i++) {
                    $m[$r[$i][0]] = $r[$i][1];
                }
                return $m;
            } else {
                return $r;
            }
        }

        throw new stack_exception(
            'Tried to convert something not fully evaluated to PHP object.');
    }

    /**
     * Takes an array of ASTs and an array of identifier keyed lists of replacements and
     * replaces said identifiers including operators in the targets one at a time
     * in the order given in a separate list, if the list is not given will replace in
     * the order the replacements were given.
     * Returns all distinct results in a toString keyed array of ASTs.
     *
     * This does the substitution like `subst` not like `ev`.
     */
    public static function substitute_in_sequence(array $asts, array $substs, $targetedkeys = null): array {
        if ($targetedkeys !== null && !is_array($targetedkeys)) {
            // When you want to replace all but the one.
            $tmp = array_keys($substs);
            $i = array_search($targetedkeys, $tmp, true);
            if ($i !== false) {
                unset($tmp[$i]);
            }
            $targetedkeys = $tmp;
        }
        if ($targetedkeys === null) {
            // For when you want to replace all and don't bother with telling what is all.
            $targetedkeys = array_keys($substs);
        }

        if (count($targetedkeys) === 0) {
            // The recursion is over.
            return $asts;
        }
        $trg = null;
        $value = null;
        $opmode = false;
        $replacetrivial = function($node) use (&$trg, &$value, &$opmode) {
            $replacepaint = false;
            $i = $node;
            while ($i !== null && !$replacepaint) {
                if (isset($i->position['replace paint']) && property_exists($node, 'value')
                        && isset($i->position['replace paint'][$node->value])) {
                    $replacepaint = true;
                }
                $i = $i->parentnode;
            }
            if ($node instanceof MP_Identifier && $node->value === $trg && !$replacepaint) {
                // Never replace function name or indexing target with an integer or a float.
                if ($value instanceof MP_Integer || $value instanceof MP_Float) {
                    if ($node->is_function_name()
                            || ($node->parentnode instanceof MP_Indexing && $node->parentnode->target === $node)) {
                        return true;
                    }
                }
                if ($node->parentnode instanceof MP_List && $node->parentnode->parentnode instanceof MP_FunctionCall
                        && $node->parentnode->parentnode->name instanceof MP_Atom
                        && $node->parentnode->parentnode->name->value === 'lambda') {
                    // Do not touch these.
                    return true;
                }

                if ($opmode && !$node->is_function_name()) {
                    return true;
                }
                if ($node->parentnode instanceof MP_Operation && $node->parentnode->op === ':'
                        && $node->parentnode->lhs === $node) {
                    return true; // Not for direct assings.
                }
                if ($node->parentnode instanceof MP_List && $node->parentnode->parentnode instanceof MP_Operation
                        && $node->parentnode->parentnode->op === ':'
                        && $node->parentnode->parentnode->lhs === $node->parentnode) {
                    return true; // Not for multi assings.
                }

                // Solve needs the arguments after the first to be protected.
                if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom
                        && $node->parentnode->name->value === 'solve') {
                    if ($node->parentnode->arguments[0] !== $node) {
                        return true;
                    }
                }
                // Algsys needs the list contents protected.
                if ($node->parentnode instanceof MP_List && $node->parentnode->parentnode !== null
                        && $node->parentnode->parentnode instanceof MP_FunctionCall
                        && $node->parentnode->parentnode->name instanceof MP_Atom
                        && $node->parentnode->parentnode->name->value === 'algsys') {
                    if ($node->argument_of('algsys') === 1) {
                        return true;
                    }
                }
                // We very specially do not replace the LHS of `=`-op if it is in the first argument of a subst.
                if ($node->parentnode instanceof MP_Operation && $node->parentnode->op === '='
                        && $node->parentnode->lhs === $node) {
                    if ($node->parentnode->parentnode instanceof MP_FunctionCall
                            && $node->parentnode->parentnode->arguments[0] === $node->parentnode
                            && $node->parentnode->parentnode->name instanceof MP_Atom
                            && $node->parentnode->parentnode->name->value === 'subst') {
                        // No list case.
                        return true;
                    }
                    if ($node->parentnode->parentnode instanceof MP_List
                            && $node->parentnode->parentnode->parentnode instanceof MP_FunctionCall
                            && $node->parentnode->parentnode->parentnode->arguments[0] === $node->parentnode->parentnode
                            && $node->parentnode->parentnode->parentnode->name instanceof MP_Atom
                            && $node->parentnode->parentnode->parentnode->name->value === 'subst') {
                        // The list case.
                        return true;
                    }
                }
                // The target of substitution in the three arg case.
                if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom
                        && $node->parentnode->name->value === 'subst'
                        && count($node->parentnode->arguments) === 3
                        && $node->parentnode->arguments[1] === $node) {
                    return true;
                }
                // Similarily for `:` and `=` in the case of ev and not the first argument.
                if ($node->parentnode instanceof MP_Operation && ($node->parentnode->op === '=' || $node->parentnode->op === ':')
                        && $node->parentnode->lhs === $node) {
                    $i = $node->argument_of('ev');
                    if ($i !== false && $i > 0) {
                        return true;
                    }
                }
                // The second argument of at is also secure.
                if ($node->parentnode instanceof MP_Operation && ($node->parentnode->op === '=' || $node->parentnode->op === ':')
                        && $node->parentnode->lhs === $node) {
                    $i = $node->argument_of('at');
                    if ($i !== false && $i === 1) {
                        return true;
                    }
                }
                if ($node->is_function_name() && !($value instanceof MP_Atom)) {
                    // No replacing function names with complex expressions here.
                    return true;
                }
                // Return true after replace so that we do repeat the loop.
                $v = clone $value;
                $v->position['replace paint'] = true;
                $node->parentnode->replace($node, $v);
                return true;
            }
            if ($opmode && $node instanceof MP_Operation && $node->op === $trg) {
                $node->op = $value->value; // It must be a string...
            }
            if ($opmode && $node instanceof MP_String && $node->value === $trg) {
                // If doing operator replacement we need to do things differently.
                $node->value = $value->value;
            }
            return true;
        };

        $trg = array_shift($targetedkeys);
        $opmode = (new stack_cas_security())->has_feature($trg, 'operator');
        $out = [];
        $gotany = false;

        foreach ($substs[$trg] as $val) {
            $value = $val;
            if (is_integer($val)) {
                continue;
            }
            $gotany = true;
            foreach ($asts as $src) {
                if (mb_strpos($src->toString(), $trg) !== false) {
                    $tmp = new MP_Group([clone $src]);
                    $tmp->callbackRecurse($replacetrivial);
                    $out[$tmp->items[0]->toString()] = $tmp->items[0];
                } else {
                    $out[$src->toString()] = $src;
                }
            }
        }
        if (!$gotany) {
            $out = $asts;
        }
        $out = self::substitute_in_sequence($out, $substs, $targetedkeys);

        return $out;
    }

    /**
     * `ev` style substitution. Works with only singular values i.e. do not give this
     * options for the values. Will replace identifiers present as keys in the substs
     * array with the values from that array as long as no parent-node of the identifier
     * has been replaced with the same identifier. Note this does not do operator replacements.
     *
     * Returns a cloned version of the input with the replacements.
     */
    public static function substitute_in_parallel(MP_Node $ast, array $substs): MP_Node {
        $tmp = new MP_Group([clone $ast]);

        // We use the position array to paint the nodes with the replacement marker.
        $replace = function($node) use ($substs) {
            if ($node instanceof MP_Identifier && isset($subst[$node->value])) {
                // Never replace function name or indexing target with an integer or a float.
                if ($subst[$node->value] instanceof MP_Integer || $subst[$node->value] instanceof MP_Float) {
                    if ($node->is_function_name()
                            || ($node->parentnode instanceof MP_Indexing && $node->parentnode->target === $node)) {
                        return true;
                    }
                }
                if ($node->parentnode instanceof MP_List && $node->parentnode->parentnode instanceof MP_FunctionCall
                        && $node->parentnode->parentnode->name instanceof MP_Atom
                        && $node->parentnode->parentnode->name->value === 'lambda') {
                    // Do not touch these.
                    return true;
                }
                // First skip the ones that should not be modified.
                if ($node->is_being_written_to()) {
                    return true;
                }
                // Solve needs the arguments after the first to be protected.
                if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom
                        && ($node->parentnode->name->value === 'solve')) {
                    if ($node->parentnode->arguments[0] !== $node) {
                        return true;
                    }
                }
                // Algsys needs the list contents protected.
                if ($node->parentnode instanceof MP_List && $node->parentnode->parentnode !== null
                        && $node->parentnode->parentnode instanceof MP_FunctionCall
                        && $node->parentnode->parentnode->name instanceof MP_Atom
                        && $node->parentnode->parentnode->name->value === 'algsys') {
                    if ($node->argument_of('algsys') === 1) {
                        return true;
                    }
                }
                // We very specially do not replace the LHS of `=`-op if it is in the first argument of a subst.
                if ($node->parentnode instanceof MP_Operation && $node->parentnode->op === '='
                        && $node->parentnode->lhs === $node) {
                    if ($node->parentnode->parentnode instanceof MP_FunctionCall
                            && $node->parentnode->parentnode->arguments[0] === $node->parentnode
                            && $node->parentnode->parentnode->name instanceof MP_Atom
                            && $node->parentnode->parentnode->name->value === 'subst') {
                        // No list case.
                        return true;
                    }
                    if ($node->parentnode->parentnode instanceof MP_List
                            && $node->parentnode->parentnode->parentnode instanceof MP_FunctionCall
                            && $node->parentnode->parentnode->parentnode->arguments[0] === $node->parentnode->parentnode
                            && $node->parentnode->parentnode->parentnode->name instanceof MP_Atom
                            && $node->parentnode->parentnode->parentnode->name->value === 'subst') {
                        // The list case.
                        return true;
                    }
                }
                // The target of substitution in the three arg case.
                if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom
                        && $node->parentnode->name->value === 'subst' && count($node->parentnode->arguments) === 3
                        && $node->parentnode->arguments[1] === $node) {
                    return true;
                }
                // Similarily for `:` and `=` in the case of ev and not the first argument.
                if ($node->parentnode instanceof MP_Operation && ($node->parentnode->op === '=' || $node->parentnode->op === ':')
                        && $node->parentnode->lhs === $node) {
                    $i = $node->argument_of('ev');
                    if ($i !== false && $i > 0) {
                        return true;
                    }
                }
                // The second argument of at is also secure.
                if ($node->parentnode instanceof MP_Operation && ($node->parentnode->op === '=' || $node->parentnode->op === ':')
                        && $node->parentnode->lhs === $node) {
                    $i = $node->argument_of('at');
                    if ($i !== false && $i === 1) {
                        return true;
                    }
                }

                // Now check that we are not part of a replaced subtree.
                $i = $node;
                $good = true;
                while ($i !== null && $good) {
                    if (isset($i->position['replace paint']) && isset($i->position['replace paint'][$node->value])) {
                        $good = false;
                    }
                    $i = $i->parentnode;
                }

                if ($good) {
                    $repl = $substs[$node->value];
                    // Tag the replaced one.
                    $repl->position = ['replace paint' => [$node->value => true]];
                    $node->parentnode->replace($node, $repl);
                    return false;
                }
            }

            return true;
        };
        // @codingStandardsIgnoreStart
        while (!$tmp->callbackRecurse($replace)) {};
        // @codingStandardsIgnoreEnd

        return $tmp->items[0];
    }

    // Does blind replacement with no care about usage context.
    public static function id_replace(MP_Node $ast, array $ids): MP_Node {
        $replace = function($node) use(&$ids) {
            if ($node instanceof MP_Identifier && isset($ids[$node->value])) {
                $node->value = $ids[$node->value];
            }
            return true;
        };
        $tmp = new MP_Group([clone $ast]);
        $tmp->callbackRecurse($replace);
        return $tmp->items[0];
    }

    // Tool to build an estimate of the identifiers and their types and values
    // for future insert-stars logic. The map will describe the values as follows,
    // there will be an array of the values it receives the array will include:
    // 1. -1 if the value is a custom function definition.
    // 2. ASTs of all the values assigned.
    // 3. will be empty if no assings happen but is still used.
    // 4. -2 might be predefined in the given list to signal an input indentifier.
    // 5. -3 signals something that might have a value from unclear substitutions and thus the type would be unknown.
    // 6. -4 if the identifier is ever called as a function.
    // For insert-stars purposes if the identifier has count=1 items in the array and
    // the only key present is -1 then the identifier is a function defined in
    // the question and should be used as such.

    // Includes a timeout logic that will stop exploring after a time if we time out
    // a special key '% TIMEOUT %' will have a value.
    public static function identify_identifier_values($ast, $expand=[], $timeout=20): array {
        $timelimit = microtime(true) + $timeout;
        $output = array_merge($expand, []);
        $sec = new stack_cas_security(); // For access to the type map.

        if ($timelimit < microtime(true)) {
            $output['% TIMEOUT %'] = true;
            return $output;
        }

        // First rewrite evaluation-flags to evs. And pick up all the statements.
        $workset1 = [];
        $seek1 = function($node) use(&$workset1, &$output) {
            if ($node instanceof MP_Statement) {
                if ($node->flags !== null && count($node->flags) > 0) {
                    $flags = [];
                    foreach ($node->flags as $flag) {
                        $flags[] = new MP_Operation(':', clone $flag->name, clone $flag->value);
                    }
                    $v = null;
                    // Now ev-flags apply to the rhs of an assingment but not the left.
                    if ($node->statement instanceof MP_Operation && ($node->op === ':' || $node->op === ':=')) {
                        $v = new MP_Operation($node->op, clone $node->lhs, new MP_FunctionCall(new MP_Identifier('ev'),
                                array_merge([clone $node->rhs], $flags)));
                    } else {
                        $v = new MP_FunctionCall(new MP_Identifier('ev'), array_merge([clone $node->statement], $flags));
                    }

                    $workset1[$v->toString()] = $v;
                } else {
                    // Only uniques so that repeated definitions do not cause issues.
                    $workset1[$node->statement->toString()] = clone $node->statement;
                }
            }
            if ($node instanceof MP_Operation && $node->op === ':=') {
                if ($node->lhs instanceof MP_FunctionCall) {
                    if (!isset($output[$node->lhs->name->toString()])) {
                        $output[$node->lhs->name->toString()] = [-1 => -1];
                    } else {
                        $output[$node->lhs->name->toString()][-1] = -1;
                    }
                }
            }
            if ($node instanceof MP_Identifier && $node->is_function_name()) {
                if (!isset($output[$node->value])) {
                    $output[$node->value] = [-4 => -4];
                } else {
                    $output[$node->value][-4] = -4;
                }
            }
            return true;
        };
        $ast->callbackRecurse($seek1);

        if (count($workset1) === 0) {
            // If we have nothing then exit.
            return $expand;
        }

        // Some rewrites are done with intermediate symbols.
        // These symbols will be removed from the output.
        // These are also used to disconnect block-variables and function arguments.
        $fakesym = 1;
        $fakes = []; // Keep track of the assigned ones.
        while (isset($output['%fake' . $fakesym])) {
            $fakesym = $fakesym + 1;
        }
        $funargs = [];

        // Turn lambdas into functions, for now don't bother with argument mapping if it is dynamic.
        // Also do the block-local and argument disconnect.
        $workset2 = [];
        $lambdas = function($node) use(&$output, &$fakesym, &$fakes, &$workset2, &$funargs) {
            if ($node instanceof MP_Operation && $node->op === ':' && $node->rhs instanceof MP_FunctionCall
                    && $node->rhs->name instanceof MP_Atom && $node->rhs->name->value === 'lambda'
                    && $node->lhs instanceof MP_Identifier) {
                if (!isset($output[$node->lhs->value])) {
                    $output[$node->lhs->value] = [-1 => -1];
                } else {
                    $output[$node->lhs->value][-1] = -1;
                }
                $r = new MP_Operation(':=', new MP_FunctionCall($node->lhs, []),
                        new MP_Group(array_slice($node->rhs->arguments, 1)));
                if ($node->rhs->arguments[0] instanceof MP_List) {
                    $r->lhs->arguments = $node->rhs->arguments[0]->items;
                }
                $node->parentnode->replace($node, $r);
                return false;
            }
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom && $node->name->value === 'block') {
                // Do this for the innermost block.
                $types = $node->type_count();
                if ($types['funs']['block'] > 1) {
                    return true;
                }
                if (isset($types['funs']['lamda'])) {
                    return true;
                }
                $vars = [];
                if (count($node->arguments) > 0 && $node->arguments[0] instanceof MP_List) {
                    foreach ($node->arguments[0]->items as $arg) {
                        while (isset($output['%fake' . $fakesym])) {
                            $fakesym = $fakesym + 1;
                        }
                        $vars[$arg->toString()] = '%fake' . $fakesym;
                        $fakes[] = '%fake' . $fakesym;
                        $output['%fake' . $fakesym] = [];
                    }
                } else if (count($node->arguments) > 0 && $node->arguments[0] instanceof MP_FunctionCall
                        && $node->arguments[0]->name instanceof MP_Atom && $node->arguments[0]->name->value === 'local') {
                    foreach ($node->arguments[0]->arguments as $arg) {
                        while (isset($output['%fake' . $fakesym])) {
                            $fakesym = $fakesym + 1;
                        }
                        $vars[$arg->toString()] = '%fake' . $fakesym;
                        $fakes[] = '%fake' . $fakesym;
                        $output['%fake' . $fakesym] = [];
                    }
                }
                $repl = null;
                if (count($vars) > 0) {
                    $repl = new MP_Group(array_slice($node->arguments, 1));
                    $repl = self::id_replace($repl, $vars);
                } else {
                    $repl = new MP_Group($node->arguments);
                }
                $node->parentnode->replace($node, $repl);
                return false;
            }
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom && $node->name->value === 'lambda') {
                // Do this for the innermost lambda.
                $types = $node->type_count();
                if ($types['funs']['lambda'] > 1) {
                    return true;
                }
                if (isset($types['funs']['block'])) {
                    return true;
                }
                $vars = [];
                $args = [];
                $offset = 1;

                // The first one must always be a list.
                if ($node->arguments[0] instanceof MP_List) {
                    foreach ($node->arguments[0]->items as $arg) {
                        while (isset($output['%fake' . $fakesym])) {
                            $fakesym = $fakesym + 1;
                        }
                        $vars[$arg->toString()] = '%fake' . $fakesym;
                        $args[$arg->toString()] = new MP_Identifier('%fake' . $fakesym);
                        $fakes[] = '%fake' . $fakesym;
                        $output['%fake' . $fakesym] = [];
                    }
                }
                if (count($node->arguments) > 2 && $node->arguments[1] instanceof MP_FunctionCall
                        && $node->arguments[1]->name instanceof MP_Atom && $node->arguments[1]->name->value === 'local') {
                    $offset = 2;
                    foreach ($node->arguments[1]->arguments as $arg) {
                        while (isset($output['%fake' . $fakesym])) {
                            $fakesym = $fakesym + 1;
                        }
                        $vars[$arg->toString()] = '%fake' . $fakesym;
                        $fakes[] = '%fake' . $fakesym;
                        $output['%fake' . $fakesym] = [];
                    }
                }

                // Fake fun.
                while (isset($output['%fake' . $fakesym])) {
                    $fakesym = $fakesym + 1;
                }
                $fakes[] = '%fake' . $fakesym;
                $output['%fake' . $fakesym] = [-1 => -1];

                $repl = new MP_Group(array_slice($node->arguments, $offset));
                $repl = self::id_replace($repl, $vars);

                $funargs['%fake' . $fakesym] = [];
                foreach ($args as $arg) {
                    $funargs['%fake' . $fakesym][] = $arg->toString();
                }

                $fun = new MP_Operation(':=',
                        new MP_FunctionCall(new MP_Identifier('%fake' . $fakesym), array_values($args)), $repl);
                $fun->position['disconnected'] = true;
                $workset2[$fun->toString()] = $fun;

                $node->parentnode->replace($node, new MP_Identifier('%fake' . $fakesym));
                return false;
            }
            // Then normal functions.
            if ($node instanceof MP_Operation && $node->op === ':=' && !isset($node->position['disconnected'])
                    && $node->lhs instanceof MP_FunctionCall) {
                // Only after these have been handled.
                $types = $node->type_count();
                if (isset($types['funs']['block']) || isset($types['funs']['lambda'])) {
                    return true;
                }
                $vars = [];
                foreach ($node->lhs->arguments as $arg) {
                    while (isset($output['%fake' . $fakesym])) {
                        $fakesym = $fakesym + 1;
                    }
                    $vars[$arg->toString()] = '%fake' . $fakesym;
                    $fakes[] = '%fake' . $fakesym;
                    $output['%fake' . $fakesym] = [];
                }
                $fname = $node->lhs->name->toString();
                $funargs[$fname] = array_values($vars);

                $repl = self::id_replace($node, $vars);
                // Make sure we will never need to deal with this again.
                $repl->position['disconnected'] = true;
                $node->parentnode->replace($node, $repl);
                return false;
            }
            return true;
        };

        // Local function args. i,e. pick up the values that these functions are called with.
        // Also build the importance mapping.
        $usedinops = [];
        $localfun = function($node) use (&$output, &$funargs, &$usedasfun, &$usedinops) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom
                    && !isset($node->position['arg extracted'])
                    && isset($funargs[$node->name->value])
                    && !$node->is_definition()) {
                $node->position['arg extracted'] = true;

                foreach ($funargs[$node->name->value] as $i => $name) {
                    if (!isset($output[$name])) {
                        $output[$name] = [];
                    }
                    if (isset($node->arguments[$i])) {
                        $output[$name][$node->arguments[$i]->toString()] = clone $node->arguments[$i];
                    }
                }
            }
            if ($node instanceof MP_Operation && $node->lhs instanceof MP_Identifier && $node->rhs instanceof MP_Identifier) {
                if (!isset($usedinops[$node->lhs->value])) {
                    $usedinops[$node->lhs->value] = [];
                }
                $usedinops[$node->lhs->value][$node->rhs->value] = true;
            }

            return true;
        };

        // Do initial simplification, write open static bits.
        // Replace sub-structures with a placeholder functions. Try to cut down the number of
        // nodes in the AST as much as possible, but not so much that side effects get lost.
        $mergelimit = 50;
        // Mergelimit controls the size of an expression that will be merged down to SCE
        // it also controls some rules like indexing evaluation.
        $open1 = function($node) use (&$sec, &$mergelimit) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom) {
                if ($node->name->value === 'ascii') {
                    // This is a relic from the pre UTF days and for castext1.
                    if ($node->arguments[0] instanceof MP_Integer && $node->arguments[0]->value !== null &&
                        // Yes, not including 0 or 127 intenttionally.
                        $node->arguments[0]->value > 0 && $node->arguments[0]->value < 127) {
                        $node->parentnode->replace($node, new MP_String(chr($node->arguments[0]->value)));
                        return false;
                    }
                } else if ($node->name->value === 'append') {
                    // Identify types, we do not check the dimensions here, we could but we do not.
                    $float = false;
                    $expression = false;
                    $vars = [];
                    $allgood = true;
                    foreach ($node->arguments as $arg) {
                        if ($arg instanceof MP_FunctionCall && $arg->name instanceof MP_Identifier) {
                            if ($arg->name->value === 'stack_float_list') {
                                $float = true;
                            } else if ($arg->name->value === 'stack_expression_list') {
                                $expression = true;
                                $vars = array_merge($vars, $arg->arguments);
                            } else if ($arg->name->value !== 'stack_integer_list') {
                                $allgood = false;
                                break;
                            }
                        } else {
                            $allgood = false;
                            break;
                        }
                    }
                    if ($allgood) {
                        $r = new MP_FunctionCall(new MP_Identifier('stack_integer_list'), []);
                        if ($float) {
                            $r->name->value = 'stack_float_list';
                        }
                        if ($expression) {
                            $r->name->value = 'stack_expression_list';
                            $vs = [];
                            foreach ($vars as $value) {
                                // Unique these just in case.
                                $vs[$value->value] = $value;
                            }
                            $r->arguments = array_values($vs);
                        }
                        $node->parentnode->replace($node, $r);
                        return false;
                    }
                } else if ($node->name->value === 'ev') {
                    // We cannot map ev to substs or otherway around so don't try that.
                    // They have different logics. Actually a subst is a nested set of evs,
                    // but an ev cannot be a subst in all cases.
                    $terms = []; // The possible assingments that matter to us.
                    foreach (array_slice($node->arguments, 1) as $arg) {
                        if (!($arg instanceof MP_Operation) || !($arg->op === ':' || $arg->op === '=')) {
                            // If the type is not visible at this point, lets push it forward.
                            if (!($arg instanceof MP_Identifier)) {
                                $terms[] = $arg;
                            } else if (!$sec->has_feature($arg->value, 'evflag')) {
                                // The normal evaluation flags can be ignored.
                                $terms[] = $arg;
                            }
                        } else if (!($arg->rhs instanceof MP_Boolean)) {
                            $terms[] = $arg;
                        }
                    }

                    if (count($node->arguments) === 1 || count($terms) === 0) {
                        // This is a typical use case.
                        $node->parentnode->replace($node, $node->arguments[0]);
                        return false;
                    }
                } else if ($node->name->value === 'subst') {
                    // Subst can be mapped to evs, note though that we only do that here for
                    // The list or direct relation case if we see the expected thing and
                    // the three argument case as we see what we do in those cases, others
                    // stay 'unevaluated' for now.
                    if (count($node->arguments) === 3) {
                        $r = new MP_FunctionCall(new MP_Identifier('ev'), [$node->arguments[2],
                            new MP_Operation('=', $node->arguments[1], $node->arguments[0])]);
                        $node->parentnode->replace($node, $r);
                        return false;
                    } else if (count($node->arguments) === 2) {
                        if ($node->arguments[0] instanceof MP_Operation && $node->arguments[0]->op === '='
                                && $node->arguments[0]->lhs instanceof MP_Identifier) {
                            $r = new MP_FunctionCall(new MP_Identifier('ev'), [$node->arguments[1], $node->arguments[0]]);
                            $node->parentnode->replace($node, $r);
                            return false;
                        } else if ($node->arguments[0] instanceof MP_List && count($node->arguments[0]->items) === 0) {
                            // This is for recursions sake.
                            $node->parentnode->replace($node, $node->arguments[1]);
                            return false;
                        } else if ($node->arguments[0] instanceof MP_List
                                && $node->arguments[0]->items[0] instanceof MP_Operation
                                && $node->arguments[0]->items[0]->op === '='
                                && $node->arguments[0]->items[0]->lhs instanceof MP_Identifier) {
                            // We extract the last substitution out to be executed last.
                            $t = array_pop($node->arguments[0]->items);
                            $r = new MP_FunctionCall(new MP_Identifier('ev'), [$node, $t]);
                            $node->parentnode->replace($node, $r);
                            return false;
                        }
                    }
                } else if ($node->name->value === 'matrix') {
                    // Identify types, we do not check the dimensions here, we could but we do not.
                    $float = false;
                    $expression = false;
                    $vars = [];
                    $allgood = true;
                    foreach ($node->arguments as $arg) {
                        if ($arg instanceof MP_FunctionCall && $arg->name instanceof MP_Identifier) {
                            if ($arg->name->value === 'stack_float_list') {
                                $float = true;
                            } else if ($arg->name->value === 'stack_expression_list') {
                                $expression = true;
                                $vars = array_merge($vars, $arg->arguments);
                            } else if ($arg->name->value !== 'stack_integer_list') {
                                $allgood = false;
                                break;
                            }
                        } else {
                            $allgood = false;
                            break;
                        }
                    }
                    if ($allgood) {
                        $r = new MP_FunctionCall(new MP_Identifier('stack_integer_matrix'), []);
                        if ($float) {
                            $r->name->value = 'stack_float_matrix';
                        }
                        if ($expression) {
                            $r->name->value = 'stack_expression_matrix';
                            $vs = [];
                            foreach ($vars as $value) {
                                // Unique these just in case.
                                $vs[$value->value] = $value;
                            }
                            $r->arguments = array_values($vs);
                        }
                        $node->parentnode->replace($node, $r);
                        return false;
                    }
                } else if ($node->name->value === 'setify') {
                    if ($node->arguments[0] instanceof MP_FunctionCall && $node->arguments[0]->name instanceof MP_Identifier) {
                        switch ($node->arguments[0]->name->value) {
                            case 'stack_float_list':
                            case 'stack_integer_list':
                            case 'stack_expression_list':
                                $node->arguments[0]->name->value = str_replace('_list', '_set', $node->arguments[0]->name->value);
                                $node->parentnode->replace($node, $node->arguments[0]);
                                return false;
                        }
                    } else if ($node->arguments[0] instanceof MP_List) {
                        // Ignore uniquenes.
                        $nl = new MP_Set($node->arguments[0]->items);
                        $node->parentnode->replace($node, $nl);
                        return false;
                    }
                } else if ($node->name->value === 'listify') {
                    if ($node->arguments[0] instanceof MP_FunctionCall && $node->arguments[0]->name instanceof MP_Identifier) {
                        switch ($node->arguments[0]->name->value) {
                            case 'stack_float_set':
                            case 'stack_integer_set':
                            case 'stack_expression_set':
                                $node->arguments[0]->name->value = str_replace('_set', '_list', $node->arguments[0]->name->value);
                                $node->parentnode->replace($node, $node->arguments[0]);
                                return false;
                        }
                    } else if ($node->arguments[0] instanceof MP_Set) {
                        $nl = new MP_List($node->arguments[0]->items);
                        $node->parentnode->replace($node, $nl);
                        return false;
                    }
                } else if ($node->name->value === 'rand') {
                    // Note we might want to flag the result as random in some way.
                    // Not doing that now but maybe in the future.
                    if ($node->arguments[0] instanceof MP_FunctionCall && $node->arguments[0]->name instanceof MP_Identifier) {
                        switch ($node->arguments[0]->name->value) {
                            case 'stack_float_list':
                                $node->parentnode->replace($node, new MP_Float(null, null));
                                return false;
                            case 'stack_integer_list':
                                $node->parentnode->replace($node, new MP_Integer(null, null));
                                return false;
                            case 'stack_expression_list':
                                $node->arguments[0]->name = 'stack_complex_expression';
                                $node->parentnode->replace($node, $node->arguments[0]);
                                return false;
                            case 'stack_integer_matrix':
                            case 'stack_float_matrix':
                            case 'stack_expression_matrix':
                                $node->parentnode->replace($node, $node->arguments[0]);
                                return false;
                        }
                    } else if ($node->arguments[0] instanceof MP_Integer) {
                        $node->parentnode->replace($node, new MP_Integer(null, null));
                        return false;
                    } else if ($node->arguments[0] instanceof MP_Float) {
                        $node->parentnode->replace($node, new MP_Float(null, null));
                        return false;
                    }
                } else if ($node->name->value === 'rand_with_step') {
                    if ($node->arguments[0] instanceof MP_Integer && $node->arguments[2] instanceof MP_Integer) {
                        $node->parentnode->replace($node, new MP_Integer(null, null));
                        return false;
                    } else if ($node->arguments[0] instanceof MP_Float || $node->arguments[2] instanceof MP_Float) {
                        $node->parentnode->replace($node, new MP_Float(null, null));
                        return false;
                    }
                } else if ($node->name->value === 'reverse' && count($node->arguments) === 1) {
                    if ($node->arguments[0] instanceof MP_Operation && $node->arguments[0]->op === '=') {
                        // Funny little trick related to this.
                        $tmp = $node->arguments[0]->rhs;
                        $node->arguments[0]->rhs = $node->arguments[0]->lhs;
                        $node->arguments[0]->lhs = $tmp;
                        $node->parentnode->replace($node, $node->arguments[0]);
                        return false;
                    }
                } else if ($node->name->value === 'length' && isset($node->arguments[0])) {
                    if ($node->arguments[0] instanceof MP_FunctionCall && $node->arguments[0]->name instanceof MP_Identifier) {
                        switch ($node->arguments[0]->name->value) {
                            case 'stack_float_list':
                            case 'stack_integer_list':
                            case 'stack_expression_list':
                                $node->parentnode->replace($node, new MP_Integer(null, null));
                                return false;
                        }
                    } else if ($node->arguments[0] instanceof MP_List) {
                        $node->parentnode->replace($node, new MP_Integer(count($node->arguments[0]->items), null));
                        return false;
                    }
                } else if ($node->name->value === 'delete') {
                    // Just ignore the deletion. If the term is not complex.
                    $types = $node->arguments[0]->type_count();
                    if (!isset($types['ops'][':']) && !isset($types['funs']['ev'])
                            && !isset($types['funs']['subst']) && !isset($types['funs']['solve'])
                            && !isset($types['funs']['at'])) {
                        $node->parentnode->replace($node, $node->arguments[1]);
                        return false;
                    }
                } else if ($node->name->value === 'sconcat') {
                    $strings = [];
                    $allgood = true;
                    foreach ($node->arguments as $arg) {
                        if ($arg instanceof MP_String) {
                            $strings[] = $arg->value;
                        } else {
                            $allgood = false;
                            break;
                        }
                    }
                    if ($allgood) {
                        $node->parentnode->replace($node, new MP_String(implode('', $strings)));
                        return false;
                    }
                } else if ($node->name->value === 'stack_complex_expression'
                        && $node->parentnode instanceof MP_FunctionCall
                        && $node->parentnode->name->toString() === 'stack_complex_expression') {
                    // These can be merged together, do unique at the same time.
                    $newargs = [];
                    foreach ($node->parentnode->arguments as $arg) {
                        if ($arg !== $node) {
                            $newargs[$arg->toString()] = $arg;
                        }
                    }
                    foreach ($node->arguments as $arg) {
                        $newargs[$arg->toString()] = $arg;
                    }
                    $node->parentnode->arguments = array_values($newargs);
                    return false;
                } else if ($node->name->value === 'makelist' && count($node->arguments) >= 4) {
                    if ($node->arguments[2] instanceof MP_Integer
                            && $node->arguments[3] instanceof MP_Integer
                            && $node->arguments[0]->toString() === $node->arguments[1]->toString()) {
                        $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier('stack_integer_list'), []));
                        return false;
                    }
                }
            }
            if (($node instanceof MP_List && (!($node->parentnode instanceof MP_Indexing)
                    || $node->parentnode->target === $node)) || $node instanceof MP_Set) {
                // This aims to cut down rands and matrices present in the AST.
                // Note that while stack_expression_list is a thing we do not create
                // them here.
                $types = $node->type_count();
                if (!isset($types['MP_Identifier']) && !isset($types['MP_String']) && !isset($types['MP_FunctionCall'])
                        && !(isset($types['ops']) && isset($types['ops'][':']))) {
                    if (!isset($types['MP_Float'])) {
                        if ($node instanceof MP_List) {
                            $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier('stack_integer_list'), []));
                        } else {
                            $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier('stack_integer_set'), []));
                        }
                        return false;
                    } else {
                        if ($node instanceof MP_List) {
                            $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier('stack_float_list'), []));
                        } else {
                            $node->parentnode->replace($node, new MP_FunctionCall(new MP_Identifier('stack_float_set'), []));
                        }
                        return false;
                    }
                }
            }
            if ($node instanceof MP_PrefixOp && ($node->rhs instanceof MP_Integer || $node->rhs instanceof MP_Float)
                    && $node->op === '-') {
                if ($node->rhs->value !== null) {
                    $node->rhs->value = -$node->rhs->value;
                    if ($node->rhs->raw !== null) {
                        $node->rhs->raw = '-' . $node->rhs->raw;
                    }
                }
                $node->parentnode->replace($node, $node->rhs);
                return false;
            }
            if ($node instanceof MP_Operation && ($node->rhs instanceof MP_Integer || $node->rhs instanceof MP_Float)
                    && ($node->lhs instanceof MP_Integer || $node->lhs instanceof MP_Float)) {
                if ($node->rhs instanceof MP_Integer && $node->lhs instanceof MP_Integer) {
                    $val = null;
                    if ($node->rhs->value !== null && $node->lhs->value !== null) {
                        $b = $node->rhs->value;
                        $a = $node->lhs->value;
                        switch ($node->op) {
                            case '-':
                                $val = $a - $b;
                                break;
                            case '*':
                                $val = $a * $b;
                                break;
                            case '+':
                                $val = $a + $b;
                                break;
                            case '^':
                                $val = $a ** $b;
                                break;
                        }
                    }

                    $node->parentnode->replace($node, new MP_Integer($val, null));
                } else {
                    $node->parentnode->replace($node, new MP_Float(null, null));
                }
                return false;
            }
            // Matrix multiplication is a very common place to merge all sorts of things.
            if ($node instanceof MP_Operation && $node->op === '.' && $node->lhs instanceof MP_FunctionCall) {
                $node->parentnode->replace($node, self::to_sce($node, $sec));
                return false;
            }

            // There two make sure that serialised previous values can be reused.
            if ($node instanceof MP_Identifier && $node->value === 'stack_unknown_integer') {
                $node->parentnode->replace($node, new MP_Integer(null, null));
                return false;
            }
            if ($node instanceof MP_Identifier && $node->value === 'stack_unknown_float') {
                $node->parentnode->replace($node, new MP_Float(null, null));
                return false;
            }
            if ($node instanceof MP_Indexing && count($node->indices) === 1
                    && !($node->parentnode instanceof MP_Operation && $node->parentnode->lhs === $node &&
                            $node->parentnode->op === ':')) {
                $indexl = $node->indices[0];
                $i = null;
                $r = null;
                $c = null;
                if ($indexl instanceof MP_List && count($indexl->items) === 1) {
                    if ($indexl->items[0] instanceof MP_Integer) {
                        $i = $indexl->items[0]->value;
                    }
                    if ($i !== null && $i > 0 && $node->target instanceof MP_List) {
                        $i = $i - 1;
                        if ($i < count($node->target->items)) {
                            $node->parentnode->replace($node, $node->target->items[$i]);
                            return false;
                        }
                    } else if ($i !== null && $i >= 0 && $node->target instanceof MP_String) {
                        // Someone randomised operators using a string from which to pick them.
                        // Note that this only works after variable substitution.
                        if ($i < mb_strlen($node->target->value)) {
                            $node->parentnode->replace($node, new MP_String(mb_substr($node->target->value, $i, 1)));
                            return false;
                        }
                    } else if ($i === null && $node->target instanceof MP_List) {
                        // The list might consist of only equivalent terms.
                        $terms = [];
                        foreach ($node->target->items as $term) {
                            $terms[$term->toString()] = $term;
                        }
                        if (count($terms) === 1) {
                            $node->parentnode->replace($node, array_pop($terms));
                            return false;
                        }
                    }
                    if ($mergelimit < 30) {
                        if ($i === null) {
                            $sce = self::to_sce($node, $sec);
                            if ($sce->toString() !== $node->toString()) {
                                    $node->parentnode->replace($node, $sce);
                                return false;
                            }
                        } else {
                            $node->parentnode->replace($node, $node->target);
                            return false;
                        }
                    }
                } else if ($indexl instanceof MP_List && count($indexl->items) === 2) {
                    if ($indexl->items[0] instanceof MP_Integer) {
                        $r = $indexl->items[0]->value;
                    }
                    if ($indexl->items[1] instanceof MP_Integer) {
                        $c = $indexl->items[1]->value;
                    }
                    if (!($r === null || $c === null || $c < 1 || $r < 1)) {
                        if ($node->target instanceof MP_FunctionCall
                                && $node->target->name instanceof MP_Atom
                                && $node->target->name->value === 'matrix' && count($node->target->arguments) >= $r) {
                            if ($node->target->arguments[$r - 1] instanceof MP_List
                                    && count($node->target->arguments[$r - 1]->items) >= $c) {
                                $node->parentnode->replace($node, $node->target->arguments[$r - 1]->items[$c - 1]);
                                return false;
                            }
                        }
                    }
                    if ($mergelimit < 30) {
                        $sce = self::to_sce($node, $sec);
                        if ($sce->toString() !== $node->toString()) {
                            $node->parentnode->replace($node, $sce);
                            return false;
                        }
                    }
                }
            } else if ($node instanceof MP_Indexing) {
                $node->parentnode->replace($node, self::to_sce($node, $sec));
                return false;
            }

            // Special constants.
            if ($node instanceof MP_Identifier && ($node->value === 'pi' || $node->value === '%pi'
                    || $node->value === '%e' || $node->value === '%phi'
                    || $node->value === 'inf' || $node->value === 'minf' || $node->value === 'infinity'
                    || $node->value === '%gamma')) {
                $node->parentnode->replace($node, new MP_Float(null, null));
                return false;
            }

            // Double unary minus. These are generated by tracing through examples of odd and even functions.
            if ($node instanceof MP_PrefixOp && $node->op === '-' && $node->rhs instanceof MP_PrefixOp && $node->rhs->op === '-') {
                $node->parentnode->replace($node, $node->rhs->rhs);
                return false;
            }
            // Ignore all evaluation ops.
            if ($node instanceof MP_PrefixOp && $node->op === "'") {
                $node->parentnode->replace($node, $node->rhs);
                return false;
            }

            // Clean up some extra groupings. Saves on storage size.
            if ($node instanceof MP_Group && count($node->items) === 1 && (
                $node->parentnode instanceof MP_Group ||
                $node->parentnode instanceof MP_Set ||
                $node->parentnode instanceof MP_List ||
                ($node->parentnode instanceof MP_FunctionCall && $node !== $node->parentnode->name)
                )) {
                $node->parentnode->replace($node, $node->items[0]);
                return false;
            }
            if ($node instanceof MP_Group && count($node->items) === 1 && (
                $node->items[0] instanceof MP_FunctionCall ||
                $node->items[0] instanceof MP_Atom ||
                $node->items[0] instanceof MP_List ||
                $node->items[0] instanceof MP_Set)) {
                $node->parentnode->replace($node, $node->items[0]);
                return false;
            }

            // Common patterns to remove to save space. If the simplification tuning is not the last thing in a group.
            if ($node instanceof MP_Group && count($node->items) > 1) {
                $i = null;
                foreach ($node->items as $k => $item) {
                    if ($item instanceof MP_Operation && $item->op === ':' && $item->lhs instanceof MP_Identifier
                        && $item->lhs->value === 'simp' && $item->rhs instanceof MP_Boolean) {
                        $i = $k;
                        break;
                    }
                }
                if ($i !== null && $i + 1 < count($node->items)) {
                    array_splice($node->items, $i, 1);
                    return false;
                }
            }

            // Arithmetic with complex expressions.
            if ($node instanceof MP_Operation && $node->op !== '=' && $node->op !== ':=' && $node->op !== ':') {
                if ($node->rhs instanceof MP_FunctionCall && $node->rhs->name instanceof MP_Atom &&
                    ($node->rhs->name->value === 'stack_complex_expression' || $node->rhs->name->value === 'stack_complex_unknown')
                        && $node->lhs instanceof MP_FunctionCall && $node->lhs->name instanceof MP_Atom
                        && ($node->lhs->name->value === 'stack_complex_expression'
                                || $node->lhs->name->value === 'stack_complex_unknown')) {
                    $terms = [];
                    // Unique them.
                    foreach ($node->rhs->arguments as $value) {
                        $terms[$value->toString()] = $value;
                    }
                    foreach ($node->lhs->arguments as $value) {
                        $terms[$value->toString()] = $value;
                    }
                    ksort($terms);
                    $node->rhs->name->value = 'stack_complex_expression';
                    $r = new MP_FunctionCall($node->rhs->name, array_values($terms));
                    $node->parentnode->replace($node, $r);
                    return false;
                }

            }
            if ($node instanceof MP_Operation && $node->op !== '=' && $node->op !== ':=' && $node->op !== ':') {
                if (($node->rhs instanceof MP_FunctionCall && $node->rhs->name instanceof MP_Atom
                        && ($node->rhs->name->value === 'stack_complex_expression'
                                || $node->rhs->name->value === 'stack_complex_unknown')
                        || $node->lhs instanceof MP_FunctionCall && $node->lhs->name instanceof MP_Atom
                        && ($node->lhs->name->value === 'stack_complex_expression'
                                || $node->lhs->name->value === 'stack_complex_unknown'))
                        && ($node->rhs instanceof MP_Atom || $node->lhs instanceof MP_Atom)) {
                    $terms = [];
                    // Unique them.
                    if ($node->rhs instanceof MP_Atom) {
                        if ($node->rhs instanceof MP_String || $node->rhs instanceof MP_Identifier) {
                            $terms[$node->rhs->toString()] = $node->rhs;
                        }
                    } else {
                        foreach ($node->rhs->arguments as $value) {
                            $terms[$value->toString()] = $value;
                        }
                    }
                    if ($node->lhs instanceof MP_Atom) {
                        if ($node->lhs instanceof MP_String || $node->lhs instanceof MP_Identifier) {
                            $terms[$node->lhs->toString()] = $node->lhs;
                        }
                    } else {
                        foreach ($node->lhs->arguments as $value) {
                            $terms[$value->toString()] = $value;
                        }
                    }
                    ksort($terms);
                    $r = new MP_FunctionCall(new MP_Identifier('stack_complex_expression'), array_values($terms));
                    $node->parentnode->replace($node, $r);
                    return false;
                }
            }

            // Assumptions about single argument functions apply.
            if ($node instanceof MP_FunctionCall && count($node->arguments) === 1 &&
                $node->arguments[0] instanceof MP_FunctionCall &&
                $node->arguments[0]->name instanceof MP_Atom &&
                ($node->arguments[0]->name->value === 'stack_complex_expression' ||
                 $node->arguments[0]->name->value === 'stack_complex_unknown')) {
                // The complex part can include its wrapper.
                $node->parentnode->replace($node, $node->arguments[0]);
                return false;
            }
            if ($node instanceof MP_FunctionCall && count($node->arguments) === 1 &&
                !($node->name instanceof MP_Atom &&
                  $node->name->value === 'stack_complex_expression') &&
                $node->arguments[0] instanceof MP_Atom && $node->arguments[0]->value === null) {
                $r = new MP_FunctionCall(new MP_Identifier('stack_complex_expression'), []);
                $node->parentnode->replace($node, $r);
                return false;
            }

            // Check that a complex expression is still clean.
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom
                    && $node->name->value === 'stack_complex_expression') {
                $clean = true;
                foreach ($node->arguments as $arg) {
                    if ($arg instanceof MP_PrefixOp || $arg instanceof MP_Integer || $arg instanceof MP_Float
                            || ($arg instanceof MP_FunctionCall && $arg->name->toString() === 'stack_complex_expression')) {
                        $clean = false;
                        break;
                    }
                }
                if (!$clean) {
                    // Combine and sort the terms.
                    $sce = self::to_sce($node, $sec);
                    $node->parentnode->replace($node, $sce);
                    return false;
                }
            }

            // Catch all large, this will lead to missing some nuances.
            if (!isset($node->position['open1 groupped'])) {
                $types = $node->type_count();
                if (!isset($types['MP_Statement']) && $types['totalnodes'] > $mergelimit
                        && count($types['vars']) + 1 < $mergelimit && !isset($types['ops'][':'])
                        && !isset($types['ops']['=']) && !isset($types['funs']['subst'])
                        && !isset($types['funs']['ev']) && !isset($types['funs']['solve'])
                        && !isset($types['funs']['algsys']) && !isset($types['funs']['at'])) {
                    $ids = [];
                    foreach ($types['vars'] as $key => $value) {
                        $ids[] = new MP_Identifier($key);
                    }
                    $r = new MP_FunctionCall(new MP_Identifier('stack_complex_expression'), $ids);
                    $r->position['open1 groupped'] = true;
                    $node->parentnode->replace($node, $r);
                    return false;
                }
            }

            return true;
        };

        // A special thing happening only after the first writing open step.
        $randexpand = function($node) use (&$sec, &$output, &$fakesym, &$fakes, &$usedinops) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom && $node->name->value === 'rand') {
                if ($node->arguments[0] instanceof MP_List && count($node->arguments[0]->items) > 0) {
                    $needsexpansion = false;
                    // There are types of lists present in these rands that we really do not need
                    // to branch on. So lets check if this is one of the types we might want to expand on.
                    $types = $node->arguments[0]->type_count();
                    foreach ($types['ids'] as $id) {
                        if (isset($usedinops[$id]) || (isset($output[$id]) && isset($output[$id][-4]))) {
                            $needsexpansion = true;
                            break;
                        }
                    }
                    if (!$needsexpansion) {
                        // Some special cases need to be handled, e.g. the rand between sets of params.
                        foreach ($node->arguments[0]->items as $item) {
                            if ($item instanceof MP_List) {
                                $needsexpansion = true;
                                break;
                            }
                        }
                    }

                    if ($needsexpansion) {
                        // What this does is it writes open the variants, this exists to deal with
                        // cases where we have randomisation between sets of parameters.
                        while (isset($output['%fake' . $fakesym])) {
                            $fakesym = $fakesym + 1;
                        }
                        $fakes[] = '%fake' . $fakesym;
                        $output['%fake' . $fakesym] = [];
                        foreach ($node->arguments[0]->items as $item) {
                            $output['%fake' . $fakesym][$item->toString()] = $item;
                        }
                        $node->parentnode->replace($node, new MP_Identifier('%fake' . $fakesym));
                        return false;
                    }
                }
            }
            return true;
        };

        // Remove basic ops by scalars they have no effect on the type of the result.
        $scalarelimination = function($node) {
            if ($node instanceof MP_Operation && ($node->op === '*' || $node->op === '-' || $node->op === '+')) {
                if ($node->lhs instanceof MP_Integer) {
                    $node->parentnode->replace($node, $node->rhs);
                    return false;
                } else if ($node->rhs instanceof MP_Integer) {
                    $node->parentnode->replace($node, $node->lhs);
                    return false;
                } else if ($node->lhs instanceof MP_Float) {
                    $node->parentnode->replace($node, $node->rhs);
                    return false;
                } else if ($node->rhs instanceof MP_Float) {
                    $node->parentnode->replace($node, $node->lhs);
                    return false;
                }
            }
            return true;
        };

        foreach ($workset1 as $value) {
            $tmp = new MP_Group([$value]);
            // @codingStandardsIgnoreStart
            while (!$tmp->callbackRecurse($lambdas)) {};
            while (!$tmp->callbackRecurse($localfun)) {};
            while (!$tmp->callbackRecurse($open1)) {};
            while (!$tmp->callbackRecurse($randexpand)) {};
            while (!$tmp->callbackRecurse($open1)) {};
            // @codingStandardsIgnoreEnd

            if ($tmp->items[0] instanceof MP_Operation && $tmp->items[0]->op === ':'
                    && $tmp->items[0]->lhs instanceof MP_Identifier
                    && $tmp->items[0]->rhs instanceof MP_Atom) {
                // Start building the output. By picking atoms out of the set.
                $output[$tmp->items[0]->lhs->value][$tmp->items[0]->rhs->toString()] = clone $tmp->items[0]->rhs;
            } else if ($tmp->items[0] instanceof MP_Operation && $tmp->items[0]->op === ':'
                    && $tmp->items[0]->lhs instanceof MP_Identifier) {
                // If not an atom it might still be an expression free of flow-control and function-calls.
                $types = $tmp->items[0]->type_count();
                if (!$types['has control flow'] && !isset($types['MP_FunctionCall']) && $types['ops'][':'] === 1) {
                    $output[$tmp->items[0]->lhs->value][$tmp->items[0]->rhs->toString()] = clone $tmp->items[0]->rhs;
                } else {
                    $workset2[$tmp->items[0]->toString()] = $tmp->items[0];
                }
            } else {
                $workset2[$tmp->items[0]->toString()] = $tmp->items[0];
            }
        }

        if ($timelimit < microtime(true)) {
            $output['% TIMEOUT %'] = true;
            return $output;
        }

        unset($workset1); // No need for this anymore.

        // Some things skip logic...
        if (count($workset2) > 0) {
            $ws2 = [];
            foreach ($workset2 as $value) {
                $tmp = new MP_Group([$value]);
                // @codingStandardsIgnoreStart
                while (!$tmp->callbackRecurse($localfun)) {};
                // @codingStandardsIgnoreEnd
                $ws2[$tmp->items[0]->toString()] = $tmp->items[0];
            }
            $workset2 = $ws2;
        }

        // Do some cross assingments in the context.
        $output = self::mergeclasses($output, [$open1, $scalarelimination], $sec);

        // The rand expand may have moved some substs, evs or solves to the output prematurely so bring them back.
        foreach ($output as $key => $values) {
            $vb = [];
            foreach ($values as $k => $value) {
                if (is_integer($value)) {
                    $vb[$k] = $value;
                    continue;
                }
                $types = $value->type_count();
                if ($types['has control flow'] || isset($types['funs']['solve']) || isset($types['funs']['algsys'])
                        || isset($types['funs']['ev'])
                        || isset($types['funs']['subst']) || isset($types['funs']['at']) || isset($types['ops'][':'])) {
                    $t = new MP_Operation(':', new MP_Identifier($key), $value);
                    $workset2[$t->toString()] = $t;
                } else {
                    $vb[$k] = $value;
                }
            }
            $output[$key] = $vb;
        }

        if ($timelimit < microtime(true)) {
            $output['% TIMEOUT %'] = true;
            return $output;
        }

        // Reduce program-flow statements down to simpler things.
        $pfreduce = function($node) use(&$out, &$sec) {
            // We apply first to the innermost items.
            if ($node instanceof MP_If || $node instanceof MP_Loop) {
                $types = $node->type_count();
                $c = 0;
                if (isset($types['MP_If'])) {
                    $c = $c + $types['MP_If'];
                }
                if (isset($types['MP_Loop'])) {
                    $c = $c + $types['MP_Loop'];
                }
                if ($c > 1) {
                    return true;
                }
                // So we are the only flow thing in the subtree this is good.
                // Extract all used variables and functions called.
                // Also spot all assingments present and construct values for them.
                $vars = [];
                $strings = [];
                $funs = [];
                $assings = [];
                $seek2 = function($n) use (&$vars, &$funs, &$assings, &$sec) {
                    if ($n instanceof MP_FunctionCall) {
                        if (!($n->name instanceof MP_Atom)
                                || !$sec->has_feature($n->name->value, 'built-in') || $n->name->value === 'ev'
                                || $n->name->value === 'at' || $n->name->value === 'subst') {
                            $funs[$n->toString()] = clone $n;
                        }
                    } else if ($n instanceof MP_Identifier && !$n->is_function_name()) {
                        $vars[$n->toString()] = clone $n;
                    } else if ($n instanceof MP_String) {
                        $strings[$n->value] = clone $n;
                    } else if ($n instanceof MP_Operation && $n->op === ':') {
                        $assings[$n->toString()] = clone $n;
                    }
                    return true;
                };
                $node->callbackRecurse($seek2);
                $r = new MP_FunctionCall(new MP_Identifier('stack_complex_expression'),
                        array_merge(array_values($vars), array_values($funs), array_values($strings)));
                // Intentionally not cloning that.
                $node->parentnode->replace($node, $r);
                foreach ($assings as $value) {
                    if ($value->rhs instanceof MP_Integer || $value->rhs instanceof MP_Float || $value->rhs instanceof MP_String) {
                        $out[] = $value;
                    } else {
                        // The value might be much simpler but we are not playing around.
                        $out[] = new MP_Operation(':', $value->lhs, $r);
                    }
                }
                return false;
            }
            return true;
        };

        $pickassings = function($node) use(&$output, &$sec) {
            if ($node instanceof MP_Operation && $node->op === ':'
                    && !($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom
                            && $node->parentnode->name->value === 'ev')) {
                // Ensure that this is the innermost assing.
                $types = $node->type_count();
                if ($types['ops'][':'] > 1) {
                    return true;
                }

                if ($node->lhs instanceof MP_Identifier) {
                    if (!isset($output[$node->lhs->value])) {
                        $output[$node->lhs->value] = [];
                    }
                    $output[$node->lhs->value][$node->rhs->toString()] = $node->rhs;
                    $node->parentnode->replace($node, $node->lhs);
                    return false;
                } else if ($node->lhs instanceof MP_List) {
                    $sce = self::to_sce($node->rhs, $sec);
                    $i = 0;
                    $group = [];
                    if ($node->rhs instanceof MP_List) {
                        foreach ($node->lhs->items as $key) {
                            if ($key instanceof MP_Identifier) {
                                $output[$key->toString()][$sce->toString()] = clone $node->rhs->items[$i];
                            } else {
                                $group[] = new MP_Operation(':', $key, clone $node->rhs->items[$i]);
                            }
                            $i = $i + 1;
                        }
                        $group[] = $node->rhs;
                        if (count($group) === 1) {
                            $node->parentnode->replace($node, $group[0]);
                        } else {
                            $node->parentnode->replace($node, new MP_Group($group));
                        }
                        return false;
                    } else {
                        foreach ($node->lhs->items as $key) {
                            if ($key instanceof MP_Identifier) {
                                $output[$key->toString()][$sce->toString()] = clone $sce;
                            } else {
                                $group[] = new MP_Operation(':', $key, clone $sce);
                            }
                            $i = $i + 1;
                        }
                        $group[] = $sce;
                        if (count($group) === 1) {
                            $node->parentnode->replace($node, $group[0]);
                        } else {
                            $node->parentnode->replace($node, new MP_Group($group));
                        }
                        return false;
                    }
                } else if ($node->lhs instanceof MP_Indexing && count($node->lhs->indices) === 1) {
                    if ($node->lhs->target instanceof MP_Atom && $node->lhs->indices[0] instanceof MP_List) {
                        if (count($node->lhs->indices[0]->items) === 1) {
                            $i = null;
                            if ($node->lhs->indices[0]->items[0] instanceof MP_Integer) {
                                $i = $node->lhs->indices[0]->items[0]->value;
                            }
                            if ($i === null || $i > 20 || $i <= 0) {
                                $sce = self::to_sce($node->rhs, $sec);
                                if (!isset($output[$node->lhs->target->value])) {
                                    $output[$node->lhs->target->value] = [];
                                }
                                $output[$node->lhs->target->value][$sce->toString()] = $sce;
                                $node->parentnode->replace($node, clone $sce);
                            } else {
                                $list = [];
                                for ($j = 0; $j < $i - 1; $j++) {
                                    $list[] = new MP_Integer(null);
                                }
                                $list[] = clone $node->rhs;
                                $list = new MP_List($list);
                                $output[$node->lhs->target->value][$list->toString()] = $list;
                                $node->parentnode->replace($node, clone $node->rhs);
                            }
                            return false;
                        } else if (count($node->lhs->indices[0]->items) === 2) {
                            $r = null;
                            $c = null;
                            if ($node->lhs->indices[0]->items[0] instanceof MP_Integer) {
                                $r = $node->lhs->indices[0]->items[0]->value;
                            }
                            if ($node->lhs->indices[0]->items[1] instanceof MP_Integer) {
                                $c = $node->lhs->indices[0]->items[1]->value;
                            }
                            if ($r === null || $c === null || ($r * $c) > 25 || $r <= 0 || $c <= 0) {
                                $sce = self::to_sce($node->rhs, $sec);
                                if (!isset($output[$node->lhs->target->value])) {
                                    $output[$node->lhs->target->value] = [];
                                }
                                $output[$node->lhs->target->value][$sce->toString()] = $sce;
                                $node->parentnode->replace($node, clone $sce);
                            } else {
                                $rows = [];
                                for ($j = 0; $j < $r - 1; $j++) {
                                    $list = [];
                                    if ($r === $j + 1) {
                                        for ($k = 0; $k < $c - 1; $k++) {
                                            $list[] = new MP_Integer(null);
                                        }
                                        $list[] = $node->rhs;
                                    }
                                    $rows[] = new MP_List($list);
                                }
                                $m = new MP_FunctionCall(new MP_Identifier('matrix'), $rows);
                                $output[$node->lhs->target->value][$m->toString()] = $m;
                                $node->parentnode->replace($node, clone $node->rhs);
                            }
                            return false;
                        } else {
                            // Larger dimension...
                            $sce = self::to_sce($node->rhs, $sec);
                            $output[$node->lhs->target->value][$sce->toString()] = $sce;
                            $node->parentnode->replace($node, clone $node->rhs);
                            return false;
                        }
                    }
                }
            }
            return true;
        };

        $workset3 = [];
        foreach ($workset2 as $value) {
            $tmp = new MP_Group([$value]);
            $out = [];
            // @codingStandardsIgnoreStart
            while (!$tmp->callbackRecurse($pfreduce)) {};
            while (!$tmp->callbackRecurse($open1)) {};
            while (!$tmp->callbackRecurse($pickassings)) {};
            // @codingStandardsIgnoreEnd

            if (count($out) > 0) {
                foreach ($out as $item) {
                    if ($item instanceof MP_Operation && $item->op === ':'
                            && $item->lhs instanceof MP_Identifier && $item->rhs instanceof MP_Atom) {
                        // Start building the output. By picking atoms out of the set.
                        $output[$item->lhs->value][$item->rhs->toString()] = clone $item->rhs;
                    } else {
                        $workset3[$item->toString()] = $item;
                    }
                }
            }
            if ($tmp->items[0] instanceof MP_Operation && $tmp->items[0]->op === ':'
                    && $tmp->items[0]->lhs instanceof MP_Identifier && $tmp->items[0]->rhs instanceof MP_Atom) {
                // Again remove the simplest ones from the set.
                $output[$tmp->items[0]->lhs->value][$tmp->items[0]->rhs->toString()] = clone $tmp->items[0]->rhs;
            } else if ($tmp->items[0] instanceof MP_Operation && $tmp->items[0]->op === ':'
                            && $tmp->items[0]->lhs instanceof MP_Identifier) {
                // If not an atom it might still be an expression free of flow-control and function-calls.
                $types = $tmp->items[0]->type_count();
                if (!$types['has control flow'] && !isset($types['MP_FunctionCall']) && $types['ops'][':'] === 1) {
                    $output[$tmp->items[0]->lhs->value][$tmp->items[0]->rhs->toString()] = clone $tmp->items[0]->rhs;
                } else {
                    $workset3[$tmp->items[0]->toString()] = $tmp->items[0];
                }
            } else if (!($tmp->items[0] instanceof MP_Identifier)) {
                $workset3[$tmp->items[0]->toString()] = $tmp->items[0];
            }
        }

        if ($timelimit < microtime(true)) {
            $output['% TIMEOUT %'] = true;
            return $output;
        }

        $mergelimit = 30;
        unset($workset2);

        // Some rewrites may have moved more substs and other interesting objects to the ouput in advance.
        foreach ($output as $key => $values) {
            $vb = [];
            foreach ($values as $k => $value) {
                if (is_integer($value)) {
                    $vb[$k] = $value;
                    continue;
                }
                $types = $value->type_count();
                if ($types['has control flow'] || isset($types['funs']['solve']) || isset($types['funs']['algsys'])
                        || isset($types['funs']['ev'])
                        || isset($types['funs']['subst']) || isset($types['funs']['at'])) {
                    $t = new MP_Operation(':', new MP_Identifier($key), $value);
                    $workset3[$t->toString()] = $t;
                } else {
                    $vb[$k] = $value;
                }
            }
            $output[$key] = $vb;
        }

        // Do some cross assingments in the context.
        $output = self::mergeclasses($output, [$open1, $scalarelimination], $sec);

        $solve = function ($node) use(&$sec) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom) {
                if ($node->name->value === 'solve') {
                    // Solve returns lists of equalities. We want to see it as such a list.
                    // There are three different basic call-forms.
                    if (count($node->arguments) === 1) {
                        // The single variable in an expression with only one variable.
                        $usage = self::variable_usage_finder(new MP_Group([$node->arguments[0]]));
                        $repl = new MP_List([]);
                        if (count($usage['read']) > 0) {
                            $repl->items[] = new MP_Operation('=', new MP_Identifier(array_keys($usage['read'])[0]),
                                    new MP_FunctionCall(new MP_Identifier('stack_complex_unknown'), []));
                        }
                        $node->parentnode->replace($node, $repl);
                        return false;
                    } else if (count($node->arguments) > 1) {
                        // Then the single or multivariate cases with declared identifiers.
                        $repl = new MP_List([]);
                        if ($node->arguments[1] instanceof MP_Identifier) {
                            $repl->items[] = new MP_Operation('=', clone $node->arguments[1],
                                    new MP_FunctionCall(new MP_Identifier('stack_complex_unknown'), []));
                        } else if ($node->arguments[1] instanceof MP_List) {
                            foreach ($node->arguments[1]->items as $value) {
                                $repl->items[] = new MP_Operation('=', clone $value,
                                        new MP_FunctionCall(new MP_Identifier('stack_complex_unknown'), []));
                            }
                            if (count($node->arguments[1]->items) > 1) {
                                // In the multivariate case we need to wrap the list.
                                $repl = new MP_List([$repl]);
                            }
                        }
                        $node->parentnode->replace($node, $repl);
                        return false;
                    }
                }
                if ($node->name->value === 'algsys') {
                    // In the case of algsys we need to make all the identifiers of the second argument
                    // tied to the first argument.
                    $sce = self::to_sce($node->arguments[0], $sec);
                    $usage = self::variable_usage_finder(new MP_Group([$node->arguments[1]]));
                    $repl = new MP_List([]);
                    foreach ($usage['read'] as $k => $v) {
                        $repl->items[] = new MP_Operation('=', new MP_Identifier($k), clone $sce);
                    }
                    $node->parentnode->replace($node, $repl);
                    return false;
                }
            }
            return true;
        };

        // Workset 3 the phase where we handle the special substitutions
        // before substitutions to substs. Currently only `solve` is a thing belonging to this.
        $workset4 = [];
        foreach ($workset3 as $value) {
            $types = $value->type_count();
            if (isset($types['funs']['solve']) || isset($types['funs']['algsys'])) {
                $rs = self::substitute_in_sequence([$value->toString() => new MP_Group([$value])], $output);
                foreach ($rs as $val) {
                    // @codingStandardsIgnoreStart
                    while (!$val->callbackRecurse($pfreduce)) {};
                    while (!$val->callbackRecurse($open1)) {};
                    while (!$val->callbackRecurse($solve)) {};
                    while (!$val->callbackRecurse($pickassings)) {};
                    while (!$val->callbackRecurse($open1)) {};
                    // @codingStandardsIgnoreEnd

                    $workset4[$val->items[0]->toString()] = $val->items[0];
                }
            } else if (isset($types['funs']['subst']) || isset($types['funs']['ev']) || isset($types['funs']['at'])) {
                $workset4[$value->toString()] = $value;
            }
        }
        unset($workset3);

        // Workset 4 should mainly contain statements that include subst or ev
        // the arguments of these functions are to be fully expanded before the final processing.

        $output = self::mergeclasses($output, [$open1], $sec, true);

        $evsandsubsts = function($node) use (&$output, &$sec) {
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Atom) {
                if ($node->name->value === 'subst' || $node->name->value === 'ev' || $node->name->value === 'at') {
                    // First ensure that this is the innermost one.
                    $types = $node->type_count();
                    $c = 0;
                    if (isset($types['funs']['subst'])) {
                        $c = $c + $types['funs']['subst'];
                    }
                    if (isset($types['funs']['ev'])) {
                        $c = $c + $types['funs']['ev'];
                    }
                    if (isset($types['funs']['at'])) {
                        $c = $c + $types['funs']['at'];
                    }
                    if ($c > 1) {
                        return true;
                    }

                    // Ok now handle the different types of substs.
                    if ($node->name->value === 'ev') {
                        $repl = [];
                        foreach (array_slice($node->arguments, 1) as $arg) {
                            if ($arg instanceof MP_Operation && ($arg->op === '=' || $arg->op === ':')
                                    && $arg->lhs instanceof MP_Identifier) {
                                $repl[$arg->lhs->value] = clone $arg->rhs;
                            } // TODO: is this else condition reachable? If so tag everything as unknown.
                        }
                        foreach ($repl as $key => $value) {
                            if (!isset($output[$key])) {
                                $output[$key] = [];
                            }
                            $output[$key][$value->toString()] = $value;
                        }
                        $r = self::substitute_in_parallel($node->arguments[0], $repl);
                        $node->parentnode->replace($node, $r);
                        return false;
                    } else if ($node->name->value === 'at') {
                        $repl = [];
                        $unknown = true;
                        if ($node->arguments[1] instanceof MP_Operation && ($node->arguments[1]->op === '='
                                || $node->arguments[1]->op === ':') && $node->arguments[1]->lhs instanceof MP_Identifier) {
                            $repl[$node->arguments[1]->lhs->value] = clone $node->arguments[1]->rhs;
                            $unknown = false;
                        } else if ($node->arguments[1] instanceof MP_List) {
                            $unknown = false;
                            foreach ($node->arguments[1]->items as $arg) {
                                if ($arg instanceof MP_Operation && ($arg->op === '=' || $arg->op === ':')
                                        && $arg->lhs instanceof MP_Identifier) {
                                    $repl[$arg->lhs->value] = clone $arg->rhs;
                                } else {
                                    $unknown = true;
                                }
                            }
                        }
                        foreach ($repl as $key => $value) {
                            if (!isset($output[$key])) {
                                $output[$key] = [];
                            }
                            $output[$key][$value->toString()] = $value;
                        }
                        if ($unknown) {
                            $types = $node->arguments[0]->type_count();
                            foreach (array_keys($types['ids']) as $id) {
                                if (!isset($repl[$id]) && $id !== 'stack_cyclic') {
                                    if (!isset($output[$id])) {
                                        $output[$id] = [];
                                    }
                                    $output[$id][-3] = -3;
                                }
                            }
                        }
                        $r = self::substitute_in_parallel($node->arguments[0], $repl);
                        $node->parentnode->replace($node, $r);
                        return false;
                    } else if ($node->name->value === 'subst') {
                        if (count($node->arguments) === 3) {
                            $repl = [$node->arguments[1]->toString() => [$node->arguments[0]]];
                            foreach ($repl as $key => $values) {
                                if ($sec->has_feature($key, 'operator')) {
                                    continue;
                                }
                                if (!isset($output[$key])) {
                                    $output[$key] = [];
                                }
                                foreach ($values as $value) {
                                    $output[$key][$value->toString()] = $value;
                                }
                            }
                            $r = self::substitute_in_sequence([$node->arguments[0]], $repl);
                            $node->parentnode->replace($node, array_pop($r));
                            return false;
                        } else if (count($node->arguments) === 2) {
                            $repl = [];
                            $unknown = true;
                            if ($node->arguments[0] instanceof MP_List) {
                                if (count($node->arguments[0]->items) === 0) {
                                    $unknown = false;
                                } else {
                                    $unknown = false;
                                    foreach ($node->arguments[0]->items as $eq) {
                                        if ($eq instanceof MP_Operation && $eq->op === '='
                                                && ($eq->lhs instanceof MP_Identifier || $eq->lhs instanceof MP_String)) {
                                            $repl[$eq->lhs->value] = [clone $eq->rhs];
                                        } else {
                                            $unknown = true;
                                        }
                                    }
                                }
                            } else if ($node->arguments[0] instanceof MP_Operation
                                    && $node->arguments[0]->op === '='
                                    && ($node->arguments[0]->lhs instanceof MP_Identifier
                                            || $node->arguments[0]->lhs instanceof MP_String)) {
                                $repl[$node->arguments[0]->lhs->value] = [clone $node->arguments[0]->rhs];
                                $unknown = false;
                            }
                            foreach ($repl as $key => $values) {
                                if ($sec->has_feature($key, 'operator')) {
                                    continue;
                                }
                                if (!isset($output[$key])) {
                                    $output[$key] = [];
                                }
                                foreach ($values as $value) {
                                    $output[$key][$value->toString()] = $value;
                                }
                            }
                            if ($unknown) {
                                $types = $node->arguments[1]->type_count();
                                foreach (array_keys($types['ids']) as $id) {
                                    if (!isset($repl[$id]) && $id !== 'stack_cyclic') {
                                        if (!isset($output[$id])) {
                                            $output[$id] = [];
                                        }
                                        $output[$id][-3] = -3;
                                    }
                                }
                            }
                            $r = self::substitute_in_sequence([$node->arguments[1]], $repl);
                            $node->parentnode->replace($node, array_pop($r));
                            return false;
                        }
                    }
                }
            }
            return true;
        };

        foreach ($workset4 as $value) {
            $types = $value->type_count();
            if (isset($types['funs']['subst']) || isset($types['funs']['ev']) || isset($types['funs']['at'])) {
                $rs = self::substitute_in_sequence([$value->toString() => new MP_Group([$value])], $output);
                foreach ($rs as $val) {

                    if (is_integer($val)) {
                        continue;
                    }
                    // @codingStandardsIgnoreStart
                    while (!$val->callbackRecurse($pfreduce)) {};
                    while (!$val->callbackRecurse($open1)) {};
                    while (!$val->callbackRecurse($solve)) {};
                    while (!$val->callbackRecurse($evsandsubsts)) {};
                    while (!$val->callbackRecurse($open1)) {};
                    while (!$val->callbackRecurse($pickassings)) {};
                    // @codingStandardsIgnoreEnd
                }
                $output = self::mergeclasses($output, [$open1], $sec, true);
                if ($timelimit < microtime(true)) {
                    $output['% TIMEOUT %'] = true;
                    return $output;
                }
            }
        }

        $mergelimit = 20;

        // Do some cross assingments in the context.
        $output = self::mergeclasses($output, [$open1], $sec, true);

        // Drop the fakes.
        foreach ($fakes as $key) {
            if (isset($output[$key])) {
                unset($output[$key]);
            }
        }

        return $output;
    }

    /* Common merge actions for type struct fixing. */
    private static function mergeclasses(array $data, array $funcs, stack_cas_security $sec, bool $merge = false): array {
        $output = $data;

        // Always make sure that the cyclic ref is never populated.
        if (isset($output['stack_cyclic'])) {
            unset($output['stack_cyclic']);
        }

        foreach ($output as $key => $values) {
            if (isset($values[$key])) {
                unset($values[$key]); // Clean self-references.
            }
            $values2 = [];
            $values1 = [];
            foreach ($values as $k => $value) {
                if (is_integer($value)) {
                    $values2[$value] = $value;
                } else {
                    $values1[$k] = $value;
                }
            }
            $cc = array_merge([], $output);
            $cc[$key] = ['stack_cyclic' => new MP_Identifier('stack_cyclic')];
            $values = self::substitute_in_sequence($values1, $cc);
            $values3 = [];
            $intmerge = [];
            $listmerge = [];
            $floatmerge = [];
            $sce = [];
            foreach ($values as $value) {
                if (is_integer($value)) {
                    continue;
                }
                $tmp = new MP_Group([$value]);
                foreach ($funcs as $fun) {
                    // @codingStandardsIgnoreStart
                    while (!$tmp->callbackRecurse($fun)) {};
                    // @codingStandardsIgnoreEnd
                }
                if ($tmp->items[0] instanceof MP_Integer) {
                    $intmerge[$tmp->items[0]->toString()] = $tmp->items[0];
                } else if ($tmp->items[0] instanceof MP_Float) {
                    $floatmerge[$tmp->items[0]->toString()] = $tmp->items[0];
                } else if ($tmp->items[0] instanceof MP_FunctionCall
                        && $tmp->items[0]->name instanceof MP_Atom
                        && $tmp->items[0]->name->value === 'stack_complex_expression') {
                    $sce[$tmp->items[0]->toString()] = $tmp->items[0];
                } else if ($tmp->items[0] instanceof MP_List) {
                    $listmerge[$tmp->items[0]->toString()] = $tmp->items[0];
                } else {
                    $values3[$tmp->items[0]->toString()] = $tmp->items[0];
                }
            }
            // Execute datatype specific merges.
            if (count($listmerge) > 5) {
                $tmp = new MP_Group(array_values($listmerge));
                $tmp = self::to_sce($tmp, $sec);
                $sce[$tmp->toString()] = $tmp;
            } else {
                foreach ($listmerge as $k => $v) {
                    $values3[$k] = $v;
                }
            }

            if (count($intmerge) === 1 && count($sce) === 0) {
                $int = array_pop($intmerge);
                $values3[$int->toString()] = $int;
            } else if (count($intmerge) > 1 && count($sce) === 0) {
                $i = new MP_Integer(null);
                $values3[$i->toString()] = $i;
            }
            if (count($floatmerge) === 1 && count($sce) === 0) {
                $float = array_pop($floatmerge);
                $values3[$float->toString()] = $float;
            } else if (count($floatmerge) > 1 && count($sce) === 0) {
                $f = new MP_Float(null, '');
                $values3[$f->toString()] = $f;
            }
            if (count($sce) === 1) {
                $sce = array_pop($sce);
                $values3[$sce->toString()] = $sce;
            } else if (count($sce) > 1) {
                $terms = [];
                foreach ($sce as $sc) {
                    foreach ($sc->arguments as $arg) {
                        if ($arg instanceof MP_Atom) {
                            if ($arg instanceof MP_Identifier || $arg instanceof MP_String) {
                                $terms[$arg->toString()] = $arg;
                            }
                        } else {
                            $terms[$arg->toString()] = $arg;
                        }
                    }
                }
                ksort($terms);
                $sce = new MP_FunctionCall(new MP_Identifier('stack_complex_expression'), array_values($terms));
                $values3[$sce->toString()] = $sce;
            }
            if (isset($values3[$key])) {
                unset($values3[$key]); // Clean self-references.
            }

            // Keep the integers.
            foreach ($values2 as $value) {
                $values3[$value] = $value;
            }
            $output[$key] = $values3;
        }
        if ($merge) {
            foreach ($output as $key => $values) {
                $values2 = [];
                $values1 = [];
                $values3 = [];
                foreach ($values as $k => $value) {
                    if (is_integer($value)) {
                        $values2[$value] = $value;
                    } else if (!($value instanceof MP_Identifier)) {
                        $types = $value->type_count();
                        if (!isset($types['ops']['=']) && !isset($types['ops'][':'])) {
                            $values1[$k] = $value;
                        } else {
                            $values3[$k] = $value;
                        }
                    } else {
                        $values3[$k] = $value;
                    }
                }
                if (count($values1) > 1) {
                    $tmp = new MP_Group(array_values($values1));
                    $tmp = self::to_sce($tmp, $sec);

                    $tmp = new MP_Group([$tmp]);
                    foreach ($funcs as $fun) {
                        // @codingStandardsIgnoreStart
                        while (!$tmp->callbackRecurse($fun)) {};
                        // @codingStandardsIgnoreEnd
                    }
                    $values3[$tmp->items[0]->toString()] = $tmp->items[0];
                } else if (count($values1) > 0) {
                    foreach ($values1 as $k => $value) {
                        $values3[$k] = $value;
                    }
                }

                // Keep the integers.
                foreach ($values2 as $value) {
                    $values3[$value] = $value;
                }
                $output[$key] = $values3;
            }
        }

        return $output;
    }

    /* Turn a subtree to `stack_complex_expression`. */
    private static function to_sce(MP_Node $ast, stack_cas_security $sec): MP_Node {
        $terms = [];
        $seek2 = function($n) use (&$terms, &$sec) {
            if ($n instanceof MP_FunctionCall) {
                if (!($n->name instanceof MP_Atom)
                        || !$sec->has_feature($n->name->value, 'built-in')
                        || $n->name->value === 'subst' || $n->name->value === 'ev' || $n->name->value === 'at') {
                    $terms[$n->toString()] = clone $n;
                }
            } else if ($n instanceof MP_Identifier && !$n->is_function_name()) {
                $terms[$n->toString()] = clone $n;
            } else if ($n instanceof MP_String) {
                $terms[$n->toString()] = clone $n;
            } else if ($n instanceof MP_Operation && $n->op === ':') {
                $terms[$n->toString()] = clone $n;
            }
            return true;
        };
        (new MP_Group([clone $ast]))->callbackRecurse($seek2);
        ksort($terms);
        return new MP_FunctionCall(new MP_Identifier('stack_complex_expression'), array_values($terms));
    }
}
