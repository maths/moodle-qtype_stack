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

// We select the implementation of the parser, depending on mbstring.
if (function_exists('mb_ereg')) {
    require_once(__DIR__ . '/autogen/parser.mbstring.php');
} else {
    require_once(__DIR__ . '/autogen/parser.native.php');
}
// Also needs stack_string().
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

class maxima_parser_utils {

    // Parses a string of Maxima code to an AST tree for use elsewhere.
    public static function parse(string $code, string $parserule = 'Root'): MP_Node {
        static $cache = array();
        if ($parserule === 'Root' && isset($cache[$code])) {
            return clone $cache[$code];
        }

        $parser = new MP_Parser();
        $ast = $parser->parse($code, array('startRule' => $parserule,
                                           'letToken' => stack_string('equiv_LET')));
        if ($parserule === 'Root') {
            $cache[$code] = clone $ast;
        }
        return $ast;
    }

    // Takes a raw tree and the matching source code and remaps the positions from char to line:linechar
    // use when you need to have pretty printted position data.
    public static function position_remap(MP_Node $ast, string $code, array $limits = null) {
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
                // If fixing has happened lets hide the fixed string to the result.
                // Might be useful for the editor to have a way of placing those
                // semicolons...
                // Again lets abuse the position array.
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
        if (core_text::substr($code, $i - 1, 2) === '/*') {
            $i--;
        }
        while ($i > 1 && self::is_whitespace(core_text::substr($code, $i - 1, 1))) {
            $i--;
        }
        return core_text::substr($code, 0, $i) . ';' . core_text::substr($code, $i);
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
                }
            }
            return true;
        };
        $ast->callbackRecurse($recursion);

        return $output;
    }

}
