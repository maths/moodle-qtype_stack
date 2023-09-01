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

require_once(__DIR__ . '/autogen/parser-root.php');
require_once(__DIR__ . '/autogen/parser-equivline.php');
// Also needs stack_string().
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/MP_classes.php');
require_once(__DIR__ . '/parser.options.class.php');

class maxima_parser_utils {


    public static function parse_po(string $code, stack_parser_options $options, array &$notes = []): MP_Node | null {

        // Quick exit.
        if ($code === '') {
            return null;
        }

        $lexer = $options->get_lexer($code);

        // Parser selection.
        $parsed = null;
        if ($options->primaryrule === 'Root') {
            $parsed = stack_maxima_parser2_root::parse($lexer, $options->tryinsert, !$options->dropcomments, $notes);
        } else if ($options->primaryrule === 'Equivline') {
            $parsed = stack_maxima_parser2_equivline::parse($lexer, $options->tryinsert, !$options->dropcomments, $notes);
        }
        if ($parsed !== null && $options->primaryrule === 'Root') {
            // Less quick exit.
            if (count($parsed->items) === 0) {
                return null;
            }
        }

        return $parsed;
    }


    /**
     * Parses a string of Maxima code to an AST tree for use elsewhere.
     *
     * @param string $code the Maxima code to parse.
     * @param string $parserule the parse rule to start with.
     * @param bool $allowpm whether to parse +-.
     * @return MP_Node the AST.
     */
    public static function parse(string $code, string $parserule = 'Root', bool $allowpm = true): MP_Node | null {

        $po = stack_parser_options::get_old_config();
        $po->primaryrule = $parserule;
        $po->pm = $allowpm;

        return self::parse_po($code, $po);
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
        if ($cachekey && $muccachelimit && strlen($code) >= $muccachelimit && mb_strpos($code, 'stack_include') === false) {
            $cache = cache::make('qtype_stack', 'parsercache');
            $ast = $cache->get($cachekey);
            if ($ast) {
                return $ast;
            }
        }

        /* TODO. Restore caching, make the options generate a sensible 
           key prefix for the code. */

        if ($cache && mb_strpos($code, 'stack_include') === false) {
            $cache->set($cachekey, $ast);
        }
        return null;
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
        $po = stack_parser_options::get_cas_config();
        $po->tryinsert = ';';

        try {
            return self::parse_po($str, $po);
        } catch (stack_parser_exception $e) {
            return $e;
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
                    ($node->name->value === 'stack_include' || $node->name->value === 'stack_include_contrib')) {
                    // Now the first requirement for this is that this must be a top level item
                    // in this statement, this statement may not have flags or anythign else.
                    if ($node->parentnode instanceof MP_Statement) {
                        if (count($node->arguments) === 1 && $node->arguments[0] instanceof MP_String) {
                            if ($node->parentnode->flags === null || count($node->parentnode->flags) === 0) {
                                // Count them to give numbered errors, do not give the included address, ever.
                                $includecount = $includecount + 1;
                                $srccode = '1';
                                // Various repeated validation steps may lead to multiple fetches for a single
                                // request, let's not do those, let's save some bandwith for those that share
                                // such stuff.
                                $remoteurl = $node->arguments[0]->value;
                                if ($node->name->value === 'stack_include_contrib') {
                                    $remoteurl = 'https://raw.githubusercontent.com/maths/moodle-qtype_stack/' .
                                        'master/stack/maxima/contrib/' . $remoteurl;
                                }
                                if (isset($remotes[$remoteurl])) {
                                    $srccode = $remotes[$remoteurl];
                                } else {
                                    $srccode = file_get_contents($remoteurl);
                                    $remotes[$remoteurl] = $srccode;
                                }
                                if ($srccode === false) {
                                    // Do not give the address in the output.
                                    $errors[] = 'stack_include or stack_include_contrib, could not retrieve: ' . $remoteurl;
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
     * Try to translate a parser exception to a senible error message.
     */
    public static function translate_exception(stack_parser_exception $e, array &$errors, array &$answernotes): void {
        if ($e->received !== null) {
            // Define the common position infromation fragment.
            $at = stack_string('parser_position', ['line' => $e->received->line, 'column' => $e->received->column]);
            if ($e->received->line === 1) {
                // Most input only has a single line so why mention the line.
                $at = stack_string('parser_position_first_line', ['column' => $e->received->column]);
            }
            // Not yet at the end of the src, or lexer level end error.
            if ($e->received->type === 'ERROR') {
                if ($e->received->value === 'Unexpected character.') {
                    // "Was not expecting CHAR at AT."
                    $errors[] = stack_string('parser_unexpected_char', ['at' => $at, 'char' => $e->received->note]);
                    if ($e->received->note === "\\") {
                        $answernotes[] = 'illegalcaschars';
                    } else {
                        $answernotes[] = 'forbiddenChar';    
                    }
                    
                    return;
                } else if ($e->received->value === 'COMMENT NOT TERMINATED') {
                    // "Comment starting at AT was never closed."
                    $errors[] = stack_string('parser_open_comment', ['at' => $at]);
                    return;
                } else if ($e->received->value === 'STRING NOT TERMINATED') {
                    // "String literal starting at AT was never closed."
                    $errors[] = stack_string('parser_open_string', ['at' => $at]);
                    return;
                }
            }
            if ($e->previous !== null && $e->received->type === 'SYMBOL' && $e->previous->type === 'SYMBOL') {
                if ($e->previous->value === '=' && ($e->received->value === '<') || $e->received->value === '>') {
                    $answernotes[] = 'backward_inequalities';
                    $errors[] = stack_string('stackCas_backward_inequalities', ['cmd' => '=' . $e->received->value]);
                    return;
                }

            }
            if ($e->received->type === 'SYMBOL') {
                $errors[] = stack_string('parser_did_not_expect', ['token' => $e->received->value, 'at' => $at]);
                if ($e->received->value === ')' || $e->received->value === '}' || $e->received->value === ']') {
                    $answernotes[] = 'missingLeftBracket';    
                } else {
                    $answernotes[] = 'spuriousop';
                }
                return;
            }
            if ($e->received->type === 'LIST_SEP') {
                $errors[] = stack_string('parser_did_not_expect', ['token' => $e->received->value, 'at' => $at]);
                return;
            }
            if ($e->received->type === 'KEYWORD' || $e->received->type === 'ID' || $e->received->type === 'BOOL') {
                $errors[] = stack_string('parser_did_not_expect', ['token' => $e->received->note, 'at' => $at]);
                return;
            }
            if ($e->received->type === 'INT' && $e->previous->type === 'LIST_SEP') {
                // TODO: This error needs localisation.
                $answernotes[] = 'unencapsulated_comma';
                $errors[] = stack_string('stackCas_unencpsulated_comma');
                return;
            }
        } else {
            // TODO: maybe backtrack matching parens etc...
            $last = null;
            if ($e->previous->type === 'LIST_SEP') {
                $last = $e->previous->value;
            }
            if ($e->previous->type === 'SYMBOL') {
                $last = $e->previous->value;
                if (mb_strpos('/+*^#~=,_&`;:$-.<>', $last) !== false) {
                    $answernotes[] = 'finalChar';
                }
            }
            if ($e->previous->type === 'KEYWORD' || $e->previous->type === 'ID' || $e->previous->type === 'BOOL') {
                $last = $e->previous->note;
            }
            if ($e->previous->type === 'INT' || $e->previous->type === 'FLOAT') {
                $last = $e->previous->value;
            }
            if ($last !== null) {
                $errors[] = stack_string('parser_cannot_end_with', ['endswith' => $last]);
                return;
            }
        }
        print_r($e);
        $answernotes[] = 'ParseError';
    }
}
