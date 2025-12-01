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

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

/**
 * Old corrective parser style pre-processing of student input before parse
 * attempts. Includes some `@@Is@@` and `@@IS@@` rewrites to apply stars where
 * normal logic might not place them, e.g, `) (` -> `)@@Is@@(`. Not all of old
 * behaviour is present here, some has been transferred to the actual parser,
 * the ones here are inconvenient to implement as parser rules, or represent
 * stricter limitation than the parser would require.
 *
 * @package    qtype_stack
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_maxima_student_preparser {
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    private static $symbols = null;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    private static $letters = null;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    private static $safespacepatterns = [
        ' or ' => 'STACKOR', ' and ' => 'STACKAND', 'not ' => 'STACKNOT', 'nounnot ' => 'STACKNOUNNOT',
        ' nor ' => 'STACKNOR', ' nand ' => 'STACKNAND', ' xor ' => 'STACKXOR', ' xnor ' => 'STACKXNOR',
        ' implies ' => 'STACKIMPLIES', ' nounor ' => 'STACKNOUNOR', ' nounand ' => 'STACKNOUNAND',
    // TO-DO: we really need to think about keywords and whether we allow
    // them for students in the first case. Of course none of these requires
    // spaces you can easily write 'if(foo)then(blaah)else(if(bar)then(zoo))'.
    // Well 'else if' is the exception...
        ' else if ' => '%%STACKELSEIF%%', ' if ' => '%%STACKIF%%',
        ':if ' => ':%%STACKIF%%', ' then ' => '%%STACKTHEN%%',
        ' else ' => '%%STACKELSE%%',
    ];

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function preparse(
        string $string,
        array &$errors,
        array &$answernote,
        stack_parser_options $parseroptions
    ): ?string {

        if (self::$symbols === null) {
            self::$symbols = json_decode(file_get_contents(__DIR__ . '/unicode/symbols-stack.json'), true);
            self::$letters = json_decode(file_get_contents(__DIR__ . '/unicode/letters-stack.json'), true);
        }

        $stringles = trim(stack_utils::eliminate_strings($string));

        $stringles = trim($stringles);

        // Replace some known unicode symbols with their equivalent in ASCII.
        $stringles = str_replace(array_keys(self::$symbols), array_values(self::$symbols), $stringles);
        $stringles = str_replace(array_keys(self::$letters), array_values(self::$letters), $stringles);

        $stringles = preg_replace('!\s+!', ' ', $stringles);

        // Check for invalid chars at this point as they may prove to be difficult to
        // handle latter, also strings are safe already.
        $allowedcharsregex = '~[^' . preg_quote(
            // @codingStandardsIgnoreStart
            // We do really want a backtick here.
            '0123456789,./\%#&{}[]()$@!"\'?`^~*_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM:;=><|: -', '~'
            // @codingStandardsIgnoreEnd
        ) . ']~u';

        $matches = [];
        // Check for permitted characters.
        if (preg_match_all($allowedcharsregex, $stringles, $matches)) {
            $invalidchars = [];
            $replaceablechars = [];
            foreach ($matches as $match) {
                $badchar = $match[0];
                if (!array_key_exists($badchar, $invalidchars)) {
                    switch ($badchar) {
                        case "\n":
                            $invalidchars[$badchar] = "\\n";
                            break;
                        case "\r":
                            $invalidchars[$badchar] = "\\r";
                            break;
                        case "\t":
                            $invalidchars[$badchar] = "\\t";
                            break;
                        case "\v":
                            $invalidchars[$badchar] = "\\v";
                            break;
                        case "\e":
                            $invalidchars[$badchar] = "\\e";
                            break;
                        case "\f":
                            $invalidchars[$badchar] = "\\f";
                            break;
                        default:
                            $invalidchars[$badchar] = $badchar;
                    }
                }
                $answernote[] = 'forbiddenChar';
            }
            $errors[] = stack_string('stackCas_forbiddenChar', ['char' => implode(", ", array_unique($invalidchars))]);
            foreach ($replaceablechars as $bad => $good) {
                $errors[] = stack_string('stackCas_useinsteadChar', ['bad' => $bad, 'char' => $good]);
            }
            return null;
        }

        if (strpos($stringles, "'") !== false) {
            $errors[] = stack_string('stackCas_apostrophe');
            $answernote[] = 'apostrophe';
        }

        if (strpos($stringles, ' ') !== false) {
            // Special cases: allow students to type in expressions such as "x>1 and x<4".
            foreach (self::$safespacepatterns as $key => $pat) {
                $stringles = str_replace($key, $pat, $stringles);
            }

            if ($parseroptions->rule === StackParserRule::Equivline) {
                $stringles = str_replace('let ', '%%STACKLET%%', $stringles);
                if (stack_string('equiv_LET') !== 'let') {
                    $stringles = str_replace(stack_string('equiv_LET') . ' ', '%%STACKLETT%%', $stringles);
                }
            }

            // NOTE: this pattern "fixes" certain valid things like calling
            // the result of a group, but as this is only applied to student
            // input and especially that example is something we do not want
            // it should not be an issue.
            $pat = '/([A-Za-z0-9_\)]+)[ ]([A-Za-z0-9_\(]+)/';
            $fixedspace = false;
            while (preg_match($pat, $stringles)) {
                $fixedspace = true;
                $stringles = preg_replace($pat, "\${1}@@Is@@\${2}", $stringles);
            }

            // Reverse safe spaces.
            foreach (self::$safespacepatterns as $key => $pat) {
                $stringles = str_replace($pat, $key, $stringles);
            }

            if ($parseroptions->rule === StackParserRule::Equivline) {
                $stringles = str_replace('%%STACKLET%%', 'let ', $stringles);
                if (stack_string('equiv_LET') !== 'let') {
                    $stringles = str_replace('%%STACKLETT%%', stack_string('equiv_LET') . ' ', $stringles);
                }
            }

            if ($fixedspace && array_search('spaces', $answernote) === false) {
                $answernote[] = 'spaces';
            }
        }

        $string = self::strings_replace($stringles, $string);

        return $string;
    }



    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function strings_replace($stringles, $original) {
        $strings = stack_utils::all_substring_strings($original);
        if (count($strings) > 0) {
            $split = explode('""', $stringles);
            $stringbuilder = [];
            $i = 0;
            foreach ($strings as $string) {
                $stringbuilder[] = $split[$i];
                $stringbuilder[] = $string;
                $i++;
            }
            $stringbuilder[] = $split[$i];
            $stringles = implode('"', $stringbuilder);
        }
        return $stringles;
    }
}
