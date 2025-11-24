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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2018 Aalto University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(__DIR__ . '/parser.options.class.php');
// Also needs stack_string().
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../../vle_specific.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/MP_classes.php');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
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

        $po = stack_parser_options::get_old_config();
        $po->pm = $allowpm;
        if ($parserule === 'Equivline') {
            $po->rule = StackParserRule::Equivline;
        }

        $cachekey = '';
        if ($parserule === 'Root') {
            $cachekey = ($allowpm ? '|PM|' : '|noPM|') . stack_string('equiv_LET') . '|' . $code;
        } else {
            $cachekey = '';
        }

        if ($cachekey !== '' && isset($cache[$cachekey])) {
            return clone $cache[$cachekey];
        }

        $ast = self::do_parse($code, $po, $cachekey);

        if ($cachekey && mb_strpos($code, 'stack_include') === false) {
            $cache[$cachekey] = clone $ast;
        }

        return $ast;
    }

    /**
     * Helper used by the previous method.
     *
     * @param string $code the Maxima code to parse.
     * @param stack_parser_options $parseoptions the options.
     * @param bool $allowpm whether to parse +-.
     * @return MP_Node the AST.
     */
    protected static function do_parse(string $code, stack_parser_options $parseoptions, string $cachekey): MP_Node {
        /* TODO: restore the cache or not.
        $muccachelimit = get_config('qtype_stack', 'parsercacheinputlength');

        $cache = null;
        if ($cachekey !== '' && $muccachelimit && strlen($code) >= $muccachelimit && mb_strpos($code, 'stack_include') === false) {
            $cache = cache::make('qtype_stack', 'parsercache');
            $ast = $cache->get($cachekey);
            if ($ast) {
                return $ast;
            }
        }
        */

        $parser = $parseoptions->get_parser();
        $ast = $parser->parse($parseoptions->get_lexer($code));

        /*
        if ($cachekey !== '' && $cache && mb_strpos($code, 'stack_include') === false) {
            $cache->set($cachekey, $ast);
        }
        */
        return $ast;
    }


    // Takes a raw tree and the matching source code and remaps the positions from char to line:linechar
    // use when you need to have pretty printed position data.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function position_remap(MP_Node $ast, string $code, ?array $limits = null) {
        if ($limits === null) {
            $limits = [];
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
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
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
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function strip_comments(MP_Root $ast) {
        // For now comments exist only at the top level and there are no "inline"
        // comments within statements, hopefully at some point we can go further.
        $nitems = [];
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
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function parse_and_insert_missing_semicolons($str, $lastfix = -1) {
        // Note this function is largely pointless now that the parser
        // has the semicolon insertion mode. In particular that `$lastfix`
        // argument of the old iterative fixer.
        // However, the next function is much simpler to keep as is when
        // we have this here...
        try {
            $po = stack_parser_options::get_cas_config();
            $po->tryinsert = StackParserInsertionOption::EndToken;
            $ast = self::do_parse($str, $po, $str);

            return $ast;
        } catch (stack_maxima_parser_exception $e) {
            return $e;
        }
    }

    // Will generate a singular AST with position remaps and inlined included statements.
    // Generates errors if inclusions within inclusions or inclusions in unexpected places.
    // Returns either the AST or some form of an exception.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function parse_and_insert_missing_semicolons_with_includes($str) {
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
            $include = function ($node) use (&$includecount, &$errors) {
                if (
                    $node instanceof MP_FunctionCall && $node->name instanceof MP_Atom &&
                    ($node->name->value === 'stack_include' || $node->name->value === 'stack_include_contrib')
                ) {
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
                                    $remoteurl = 'contrib://' . $remoteurl;
                                }
                                $srccode = stack_fetch_included_content($remoteurl);
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

            // TO-DO: wrap those errors into something more readable.
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
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
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
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    private static function is_whitespace($mbc) {
        // So ctype_space does not handle those fancy unicode spaces...
        // There are more than these but we add things as we meet them.
        if (ctype_space($mbc)) {
            return true;
        }
        $num = ord($mbc);
        if (
            $num === 160 || $num === 8287 || $num < 33
            || ($num > 8191 && $num < 8208) || ($num > 8231 && $num < 8240)
        ) {
            return true;
        }
        return false;
    }

    // Tool to extract information about which variables are being used and how.
    // In a given parsed section of code. Updates a given usage list so that use
    // for example in going through a PRT tree is convenient.
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function variable_usage_finder($ast, $output = []) {
        if (!array_key_exists('read', $output)) {
            $output['read'] = [];
        }
        if (!array_key_exists('write', $output)) {
            $output['write'] = [];
        }
        if (!array_key_exists('calls', $output)) {
            $output['calls'] = [];
        }
        if (!array_key_exists('declares', $output)) {
            $output['declares'] = [];
        }
        $recursion = function ($node) use (&$output) {
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
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
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
            'Tried to convert something not fully evaluated to PHP object.'
        );
    }
}
