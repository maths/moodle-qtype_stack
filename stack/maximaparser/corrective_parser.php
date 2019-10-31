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

// We select the implementation of the parser, depending on mbstring.
if (function_exists('mb_ereg')) {
    require_once(__DIR__ . '/autogen/parser.mbstring.php');
} else {
    require_once(__DIR__ . '/autogen/parser.native.php');
}
// Also needs stack_string().
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

require_once(__DIR__ . '/MP_classes.php');

// A Maxima parser wrapper that tries to insert missing stars to statements
// to make them parseable.
//
// Once we have an ast we filter further to handle extended syntax and more
// complex star insertion.
//
// @copyright  2019 Aalto University
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
class maxima_corrective_parser {

    // Returns an AST if possible.
    public static function parse(string $string, array &$errors, array &$answernote, array $parseroptions) {
        static $safespacepatterns = array(
         ' or ' => 'STACKOR', ' and ' => 'STACKAND', 'not ' => 'STACKNOT',
         ' nounor ' => 'STACKNOUNOR', ' nounand ' => 'STACKNOUNAND',
         // TODO: we really need to think about keywords and whether we allow
         // them for students in the first case. Of course none of these requires
         // spaces you can easily write 'if(foo)then(blaah)else(if(bar)then(zoo))'.
         // Well 'else if' is the exception...
         ' else if ' => '%%STACKELSEIF%%', ' if ' => '%%STACKIF%%',
         ':if ' => ':%%STACKIF%%', ' then ' => '%%STACKTHEN%%',
         ' else ' => '%%STACKELSE%%');

        // These will store certain errors if the parsing is impossible.
        $err1 = false;
        $err2 = false;

        $stringles = trim(stack_utils::eliminate_strings($string));
        // Hide ?-chars as those can do many things.
        $stringles = str_replace('?', 'QMCHAR', $stringles);

        // Safely wrap "let" statements.
        $fixlet = false;
        $langlet = '';
        if (isset($parseroptions['startRule']) && $parseroptions['startRule'] === 'Equivline') {
            $langlet = strtolower(stack_string('equiv_LET')) . ' ';
            if (strtolower(substr($stringles, 0, strlen($langlet))) === $langlet) {
                $stringles = substr($stringles, strlen($langlet));
                $fixlet = true;
            } else if (strtolower(substr($stringles, 0, strlen('let '))) === 'let ') {
                // In the case of localisable grammar we alway support the original form.
                // But may represent it as other during latter processing.
                $stringles = substr($stringles, strlen($langlet));
                $fixlet = true;
            }
        }

        // Check for invalid chars at this point as they may prove to be difficult to
        // handle latter, also strings are safe already.
        // Special case to allow "pi" through here.  TODO: more systematic way to add in allowed characters.
        $allowedcharsregex = '~[^' . preg_quote(json_decode('"\u03C0"') .
            '0123456789,./\%&{}[]()$@!"\'?`^~*_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM:;=><|: -', '~'
            ) . ']~u';
        $matches = array();
        // Check for permitted characters.
        if (preg_match_all($allowedcharsregex, $stringles, $matches)) {
            $invalidchars = array();
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
            }
            $errors[] = stack_string('stackCas_forbiddenChar', array( 'char' => implode(", ", array_unique($invalidchars))));
            $answernote[] = 'forbiddenChar';
            return null;
        }

        // Missing stars patterns to fix.
        // NOTE: These patterns take into account floats, if the logic wants to
        // kill floats it can do it later after the parsing.
        $starpatterns   = array("/(\))([0-9A-Za-z])/");    // E.g. )a, or )3. But not underscores )_.
        $starpatterns[] = "/([^0-9A-Za-z_][0-9]+)([A-DF-Za-df-z_]+|[eE][^\+\-0-9]+)/"; // +3z(, -2ee+ not *4e-2 or /1e3
        $starpatterns[] = "/^([\+\-]?[0-9]+)([A-DF-Za-df-z_]+|[eE][^\+\-0-9]+)/"; // Same but start of line.
        $starpatterns[] = "/([^0-9A-Za-z_][0-9]+)(\()/"; // Pattern such as -124().
        $starpatterns[] = "/^([\+\-]?[0-9]+)(\()/"; // Same but start of line.
        $starpatterns[] = "/([^0-9A-Za-z_][0-9]+[\.]?[0-9]*[eE][\+\-]?[0-9]+)(\()/"; // Pattern such as -124.4e-3().
        $starpatterns[] = "/^([\+\-]?[0-9]+[\.]?[0-9]*[eE][\+\-]?[0-9]+)(\()/"; // Same but start of line.

        $missingstar    = false;
        $missingstring  = '';

        foreach ($starpatterns as $pat) {
            while (preg_match($pat, $stringles)) {
                $missingstar = true;
                $stringles = preg_replace($pat, "\${1}*%%IS\${2}", $stringles);
            }
        }

        if (false !== $missingstar) {
            // Just so that we do not add this for each star.
            $answernote[] = 'missing_stars';
        }

        // Spaces to stars.
        $stringles = trim($stringles);
        $stringles = preg_replace('!\s+!', ' ', $stringles);

        if (strpos($stringles, ' ') !== false) {
            // Special cases: allow students to type in expressions such as "x>1 and x<4".
            foreach ($safespacepatterns as $key => $pat) {
                $stringles = str_replace($key, $pat, $stringles);
            }

            // NOTE: this pattern "fixes" certain valid things like calling
            // the result of a group, but as this is only applied to student
            // input and especially that example is something we do not want
            // it should not be an issue.
            $pat = "/([A-Za-z0-9_\)]+)[ ]([A-Za-z0-9_\(]+)/";
            $fixedspace = false;
            while (preg_match($pat, $stringles)) {
                $fixedspace = true;
                $stringles = preg_replace($pat, "\${1}*%%Is\${2}", $stringles);
            }

            // Reverse safe spaces.
            foreach ($safespacepatterns as $key => $pat) {
                $stringles = str_replace($pat, $key, $stringles);
            }

            if ($fixedspace) {
                $answernote[] = 'spaces';
            }
        }

        $string = self::strings_replace($stringles, $string);

        $ast = null;

        if ($string === '') {
            return null;
        }

        if ($fixlet) {
            $string = $langlet . $string;
        }
        try {
            $parser = new MP_Parser();
            $ast = $parser->parse($string, $parseroptions);
        } catch (SyntaxError $e) {
            self::handle_parse_error($e, $string, $errors, $answernote);
            return null;
        }

        // Once parsed check if we added stars and tag them.
        $processmarkkers = function($node) {
            // Map the insertted stars.
            if ($node instanceof MP_Operation && $node->op === '*' && !(
                isset($node->position['insertstars']) ||
                isset($node->position['fixspaces']))) {
                $rhs = $node->leftmostofright();
                if ($rhs instanceof MP_Identifier &&
                    core_text::substr($rhs->value, 0, 4) === '%%IS') {
                    $node->position['insertstars'] = true;
                    $rhs->value = core_text::substr($rhs->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_Identifier &&
                    core_text::substr($rhs->value, 0, 4) === '%%Is') {
                    $node->position['fixspaces'] = true;
                    $rhs->value = core_text::substr($rhs->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_FunctionCall && $rhs->name instanceof MP_Identifier &&
                    core_text::substr($rhs->name->value, 0, 4) === '%%IS') {
                    $node->position['insertstars'] = true;
                    $rhs->name->value = core_text::substr($rhs->name->value, 4);
                    if ($rhs->name->value === '') {
                        $node->replace($rhs, new MP_Group($rhs->arguments));
                    }
                    return false;
                }
                if ($rhs instanceof MP_FunctionCall && $rhs->name instanceof MP_Identifier &&
                    core_text::substr($rhs->name->value, 0, 4) === '%%Is') {
                    $node->position['fixspaces'] = true;
                    $rhs->name->value = core_text::substr($rhs->name->value, 4);
                    if ($rhs->name->value === '') {
                        $node->replace($rhs, new MP_Group($rhs->arguments));
                    }
                    return false;
                }
                if ($rhs instanceof MP_Indexing && $rhs->target instanceof MP_Identifier &&
                    core_text::substr($rhs->target->value, 0, 4) === '%%IS') {
                    $node->position['insertstars'] = true;
                    $rhs->target->value = core_text::substr($rhs->target->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_Indexing && $rhs->target instanceof MP_Identifier &&
                    core_text::substr($rhs->target->value, 0, 4) === '%%Is') {
                    $node->position['fixspaces'] = true;
                    $rhs->target->value = core_text::substr($rhs->target->value, 4);
                    return false;
                }
                // Then deep trees.
                $rhs = $node->leftmostofright();
                if ($rhs instanceof MP_Identifier &&
                    core_text::substr($rhs->value, 0, 4) === '%%IS') {
                    $node->position['insertstars'] = true;
                    $rhs->value = core_text::substr($rhs->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_Identifier &&
                    core_text::substr($rhs->value, 0, 4) === '%%Is') {
                    $node->position['fixspaces'] = true;
                    $rhs->value = core_text::substr($rhs->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_FunctionCall && $rhs->name instanceof MP_Identifier &&
                    core_text::substr($rhs->name->value, 0, 4) === '%%IS') {
                    $node->position['insertstars'] = true;
                    $rhs->name->value = core_text::substr($rhs->name->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_FunctionCall && $rhs->name instanceof MP_Identifier &&
                    core_text::substr($rhs->name->value, 0, 4) === '%%Is') {
                    $node->position['fixspaces'] = true;
                    $rhs->name->value = core_text::substr($rhs->name->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_Indexing && $rhs->target instanceof MP_Identifier &&
                    core_text::substr($rhs->target->value, 0, 4) === '%%IS') {
                    $node->position['insertstars'] = true;
                    $rhs->target->value = core_text::substr($rhs->target->value, 4);
                    return false;
                }
                if ($rhs instanceof MP_Indexing && $rhs->target instanceof MP_Identifier &&
                    core_text::substr($rhs->target->value, 0, 4) === '%%Is') {
                    $node->position['fixspaces'] = true;
                    $rhs->target->value = core_text::substr($rhs->target->value, 4);
                    return false;
                }

            }
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Identifier &&
                $node->name->value === '') {
                // The previous one may have caused trouble.
                $node->parentnode->replace($node, new MP_Group($node->arguments));
                return false;
            }

            // Note that %%IS is used in the pre-parser to mark implied multiplications.
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Identifier &&
                    core_text::substr($node->name->value, 0, 4) === '%%IS') {
                $node->name->value = core_text::substr($node->name->value, 4);
                $node->parentnode->position['insertstars'] = true;
                if ($node->name->value === '') {
                    $node->parentnode->replace($node, new MP_Group($node->arguments));
                } else if (ctype_digit($node->name->value)) {
                    $newop = new MP_Operation('*', new MP_Integer(intval($node->name->value), new MP_Group($node->arguments)));
                    $newop->position['insertstars'] = true;
                    $node->parentnode->replace($node, $newop);
                }
                return false;
            } else if ($node instanceof MP_Identifier && core_text::substr($node->value, 0, 4) === '%%IS') {
                $node->value = core_text::substr($node->value, 4);
                $node->parentnode->position['insertstars'] = true;
                if (ctype_digit($node->value)) {
                    $node->parentnode->replace($node, new MP_Integer(intval($node->value)));
                }
                return false;
            }

            // And %%Is that is used for pre-parser fixed spaces.
            if ($node instanceof MP_FunctionCall && $node->name instanceof MP_Identifier &&
                    core_text::substr($node->name->value, 0, 4) === '%%Is') {
                $node->name->value = core_text::substr($node->name->value, 4);
                if ($node->name->value === '') {
                    $node->parentnode->position['fixspaces'] = true;
                    $node->parentnode->replace($node, new MP_Group($node->arguments));
                } else if (ctype_digit($node->name->value)) {
                    $newop = new MP_Operation('*', new MP_Integer(intval($node->name->value),
                            new MP_Group($node->arguments)));
                    // This one is tricky, as it is basically an insertstars in non insertstars context.
                    // Might require a special case upstream and maybe set to invalid...
                    $newop->position['fixspaces'] = true;
                    $node->parentnode->position['fixspaces'] = true;
                    $node->parentnode->replace($node, $newop);
                }
                return false;
            } else if ($node instanceof MP_Identifier && core_text::substr($node->value, 0, 4) === '%%Is') {
                $node->value = core_text::substr($node->value, 4);
                $node->parentnode->position['fixspaces'] = true;
                if (ctype_digit($node->value)) {
                    $node->parentnode->replace($node, new MP_Integer(intval($node->value)));
                }
                return false;
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($processmarkkers) !== true) {
        }
        // @codingStandardsIgnoreEnd

        return $ast;
    }

    public static function handle_parse_error($exception, $string, &$errors, &$answernote) {
        // @codingStandardsIgnoreStart
        // We also disallow backticks.
        static $disallowedfinalchars = '/+*^#~=,_&`;:$-.<>';
        // @codingStandardsIgnoreEnd

        /**
         * @var all the characters permitted in responses.
         * Note, these are used in regular expression ranges, so - must be at the end, and ^ may not be first.
         */
        // @codingStandardsIgnoreStart
        static $allowedchars =
        '0123456789,./\%&{}[]()$@!"\'?`^~*_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM:=><|: -';
        // @codingStandardsIgnoreEnd

        $foundchar = $exception->found;
        $previouschar = null;
        $nextchar = null;

        if ($exception->grammarOffset >= 1) {
            $previouschar = core_text::substr($string, $exception->grammarOffset - 1, 1);
        }
        if ($exception->grammarOffset < (core_text::strlen($string) - 1)) {
            $nextchar = core_text::substr($string, $exception->grammarOffset + 1, 1);
        }

        // Some common output processing.
        $original = $string;
        $string = str_replace('*%%IS', '*', $string);
        $string = str_replace('*%%Is', '*', $string);
        $string = str_replace('QMCHAR', '?', $string);

        // Only permit the following characters to be sent to the CAS.
        $allowedcharsregex = '~[^' . preg_quote($allowedchars, '~') . ']~u';
        // Check for permitted characters.
        if (preg_match_all($allowedcharsregex, $string, $matches)) {
            $invalidchars = array();
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
            }
            $errors[] = stack_string('stackCas_forbiddenChar', array( 'char' => implode(", ", array_unique($invalidchars))));
            $answernote[] = 'forbiddenChar';
            return;
        }

        if ($foundchar === '(' || $foundchar === ')' || $previouschar === '(' || $previouschar === ')' || $foundchar === null) {
            $stringles = stack_utils::eliminate_strings($string);
            $inline = stack_utils::check_bookends($stringles, '(', ')');
            if ($inline === 'left') {
                $answernote[] = 'missingLeftBracket';
                $errors[] = stack_string('stackCas_missingLeftBracket',
                    array('bracket' => '(', 'cmd' => stack_maxima_format_casstring($string)));
                return;
            } else if ($inline === 'right') {
                $answernote[] = 'missingRightBracket';
                $errors[] = stack_string('stackCas_missingRightBracket',
                  array('bracket' => ')', 'cmd' => stack_maxima_format_casstring($string)));
                return;
            }
        }
        if ($foundchar === '[' || $foundchar === ']' || $previouschar === '[' || $previouschar === ']' || $foundchar === null) {
            $stringles = stack_utils::eliminate_strings($string);
            $inline = stack_utils::check_bookends($stringles, '[', ']');
            if ($inline === 'left') {
                $answernote[] = 'missingLeftBracket';
                $errors[] = stack_string('stackCas_missingLeftBracket',
                    array('bracket' => '[', 'cmd' => stack_maxima_format_casstring($string)));
                return;
            } else if ($inline === 'right') {
                $answernote[] = 'missingRightBracket';
                $errors[] = stack_string('stackCas_missingRightBracket',
                    array('bracket' => ']', 'cmd' => stack_maxima_format_casstring($string)));
                return;
            }
        }
        if ($foundchar === '{' || $foundchar === '}' || $previouschar === '{' || $previouschar === '}' || $foundchar === null) {
            $stringles = stack_utils::eliminate_strings($string);
            $inline = stack_utils::check_bookends($stringles, '{', '}');
            if ($inline === 'left') {
                $answernote[] = 'missingLeftBracket';
                $errors[] = stack_string('stackCas_missingLeftBracket',
                    array('bracket' => '{', 'cmd' => stack_maxima_format_casstring($string)));
                return;
            } else if ($inline === 'right') {
                $answernote[] = 'missingRightBracket';
                $errors[] = stack_string('stackCas_missingRightBracket',
                    array('bracket' => '}', 'cmd' => stack_maxima_format_casstring($string)));
                return;
            }
        }

        if ($previouschar === '=' && ($foundchar === '<' || $foundchar === '>')) {
            $a = array();
            if ($foundchar === '<') {
                $a['cmd'] = stack_maxima_format_casstring('=<');
            } else {
                $a['cmd'] = stack_maxima_format_casstring('=>');
            }
            $errors[] = stack_string('stackCas_backward_inequalities', $a);
            $answernote[] = 'backward_inequalities';
        } else if ($foundchar === '=' && ($nextchar === '<' || $nextchar === '>')) {
            $a = array();
            if ($nextchar === '<') {
                $a['cmd'] = stack_maxima_format_casstring('=<');
            } else {
                $a['cmd'] = stack_maxima_format_casstring('=>');
            }
            $errors[] = stack_string('stackCas_backward_inequalities', $a);
            $answernote[] = 'backward_inequalities';
        } else if ($foundchar === "'") {
            $errors[] = stack_string('stackCas_apostrophe');
            $answernote[] = 'apostrophe';
        } else if (($foundchar === '/' && $nextchar === '*') || ($foundchar === '*' && $previouschar === '/')) {
            $a = array('cmd' => stack_maxima_format_casstring('/*'));
            $errors[] = stack_string('stackCas_spuriousop', $a);
            $answernote[] = 'spuriousop';
        } else if ($foundchar === '=' && $nextchar === '=' && $previouschar === '=') {
            $a = array('cmd' => stack_maxima_format_casstring('==='));
            $errors[] = stack_string('stackCas_spuriousop', $a);
            $answernote[] = 'spuriousop';
        } else if ($foundchar === '=' && ($nextchar === '=' || $previouschar === '=')) {
            $a = array('cmd' => stack_maxima_format_casstring('=='));
            $errors[] = stack_string('stackCas_spuriousop', $a);
            $answernote[] = 'spuriousop';
        } else if ($foundchar === '&') {
            $a = array('cmd' => stack_maxima_format_casstring('&'));
            $errors[] = stack_string('stackCas_spuriousop', $a);
            $answernote[] = 'spuriousop';
        } else if ($foundchar === '|') {
            $a = array('cmd' => stack_maxima_format_casstring('|'));
            $errors[] = stack_string('stackCas_spuriousop', $a);
            $answernote[] = 'spuriousop';
        } else if (($foundchar === '>' && $previouschar === '<') || ($foundchar === '<' && $nextchar === '>')) {
            $a = array('cmd' => stack_maxima_format_casstring('<>'));
            $errors[] = stack_string('stackCas_spuriousop', $a);
            $answernote[] = 'spuriousop';

        } else if (ctype_alpha($foundchar) && ctype_digit($previouschar)) {
            $a = array('cmd' => stack_maxima_format_casstring(core_text::substr($string, 0, $exception->grammarOffset) .
                    '<font color="red">*</font>' . core_text::substr($string, $exception->grammarOffset)));
            $answernote[] = 'missing_stars';
        } else if ($foundchar === ',' || (ctype_digit($foundchar) && $previouschar === ',')) {
            $errors[] = stack_string('stackCas_unencpsulated_comma');
            $answernote[] = 'unencapsulated_comma';
        } else if ($foundchar === '\\') {
            $errors[] = stack_string('illegalcaschars');
            $answernote[] = 'illegalcaschars';
        } else if ($previouschar === ' ') {
            $cmds = trim(core_text::substr($original, 0, $exception->grammarOffset - 1));
            $cmds .= '<font color="red">_</font>';
            $cmds .= core_text::substr($original, $exception->grammarOffset);
            $cmds = str_replace('*%%IS', '*', $cmds);
            $cmds = str_replace('*%%Is', '<font color="red">_</font>', $cmds);
            $answernote[] = 'spaces';
            $errors[] = stack_string('stackCas_spaces', array('expr' => stack_maxima_format_casstring($cmds)));
        } else if ($foundchar === ':' && (strpos($string, ':lisp') !== false)) {
            $errors[] = stack_string('stackCas_forbiddenWord',
                    array('forbid' => stack_maxima_format_casstring('lisp')));
            $answernote[] = 'forbiddenWord';
        } else if (count($exception->expected) === 6 &&
                   $exception->expected[0]['type'] === 'literal' && $exception->expected[0]['value'] === ',' &&
                   $exception->expected[1]['type'] === 'literal' && $exception->expected[1]['value'] === ':' &&
                   $exception->expected[2]['type'] === 'literal' && $exception->expected[2]['value'] === ';' &&
                   $exception->expected[3]['type'] === 'literal' && $exception->expected[3]['value'] === '=' &&
                   $exception->expected[4]['type'] === 'end' &&
                   $exception->expected[5]['type'] === 'other' && $exception->expected[5]['description'] === 'whitespace') {
            // This is a sensitive check matching the expectations of the parser....
            // This is extra special, if we have an unencpsulated comma we might be parsing for an evaluation
            // flag but not find the assingment of flag value...
            $errors[] = stack_string('stackCas_unencpsulated_comma');
            $answernote[] = 'unencapsulated_comma';
        } else if ($nextchar === null && ($foundchar !== null && core_text::strpos($disallowedfinalchars, $foundchar) !== false)) {
            $a = array();
            $a['char'] = $foundchar;
            $a['cmd']  = stack_maxima_format_casstring($string);
            $errors[] = stack_string('stackCas_finalChar', $a);
            $answernote[] = 'finalChar';
        } else if ($foundchar === null && ($previouschar !== null &&
                core_text::strpos($disallowedfinalchars, $previouschar) !== false)) {
            $a = array();
            $a['char'] = $previouschar;
            $a['cmd']  = stack_maxima_format_casstring($string);
            $errors[] = stack_string('stackCas_finalChar', $a);
            $answernote[] = 'finalChar';
        } else if ($foundchar === '!' && ($previouschar === null ||
                !(ctype_alpha($previouschar) || ctype_digit($previouschar) || $previouschar === ')' || $previouschar === ']'))) {
            // TODO: Localise... "Operator X without a valid target. Needs something in front of it."
            $a = array('op' => stack_maxima_format_casstring('!'));
            $errors[] = stack_string('stackCas_badpostfixop', $a);
            $answernote[] = 'badpostfixop';
        } else if (core_text::strpos($disallowedfinalchars, core_text::substr(trim($string), -1)) !== false) {
            $a = array();
            $a['char'] = core_text::substr(trim($original), -1);
            $a['cmd']  = stack_maxima_format_casstring($string);
            $errors[] = stack_string('stackCas_finalChar', $a);
            $answernote[] = 'finalChar';
        } else if (($foundchar === '}' || $foundchar === ']' || $foundchar === ')') &&
                core_text::strpos($disallowedfinalchars, $previouschar) !== false) {
            $a = array();
            $a['char'] = $previouschar;
            $a['cmd']  = stack_maxima_format_casstring($string);
            $errors[] = stack_string('stackCas_finalChar', $a);
            $answernote[] = 'finalChar';
        } else if ($previouschar === '"') {
            $errors[] = stack_string('stackCas_MissingString');
            $answernote[] = 'MissingString';
        } else {
            $errors[] = $exception->getMessage();
            $answernote[] = 'ParseError';
        }

    }

    public static function strings_replace($stringles, $original) {
        $strings = stack_utils::all_substring_strings($original);
        if (count($strings) > 0) {
            $split = explode('""', $stringles);
            $stringbuilder = array();
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