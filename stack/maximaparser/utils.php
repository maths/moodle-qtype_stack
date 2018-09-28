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

// @copyright  2018 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
require_once("maximaParser.php");

class maxima_parser_utils {

    // Parses a string of Maxima code to an AST tree for use elsewhere.
    public static function parse(string $code): MP_Root {
            $parser = new MP_Parser();
            return $parser->parse($code);
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
}
