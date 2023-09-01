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

require_once(__DIR__ . '/parser.options.class.php');

/**
 * This is the base of all lexers that we use. All of them produce these
 * same tokens and all of them take in the same optiosn class as an argument
 * for the constructor.
 */

class stack_maxima_token {
    /**
     * The major class of the token.
     *  - KEYWORD for `while` etc. Including localised terms.
     *  - ID for any identifier.
     *  - INT for anything that is a integer or base-whatever.
     *  - FLOAT is a float
     *  - BOOL for anything that could be interpreted as `true/false` including
     *    localised terms.
     *  - STRING for `"strings"`. Note that this unescapes it.
     *  - SYMBOL for operators and parens. These include Unicode translations.
     *  - WS for whitespace.
     *  - COMMENT for comments.
     *  - LIST_SEP for ',' or whichever has been selected.
     *  - END_TOKEN for statement separators ';' and '$' or locale dependent ones.
     *  - ERROR for any error imaginable.
     *  - LISP_ID is special case that may be enabled.
     */
    public $type = 'ID'; 

    /**
     * The content of the string, the identifier, the error message etc.
     */
    public $value = '';

    /**
     * Line number of start of token, first line is 1.
     */
    public $line = -1;

    /**
     * Column number of start of token, first column is 1.
     */
    public $column = -1;

    /**
     * Position in stream, i.e. the Unicode character position from start.
     * First char is at 0. 
     */
    public $position = -1;

    /**
     * Length in (unicode) chars.
     */
    public $length = -1;

    /**
     * Note meant for specially generated tokens. I.e. why a star was inserted.
     * In the case of "ERROR" and non terminated things this may contain the value.
     * In the case of identifiers, booleans or keywords may contain the original 
     * form before translation.
     */
    public $note = null;

    public function __construct(string $type, string $value, int $line, int $column, int $position, int $length,string $note = null) {
        $this->type = $type;
        $this->value = $value;
        $this->line = $line;
        $this->column = $column;
        $this->position = $position;
        $this->length = $length;
        $this->note = $note;
    }

    public function __toString() {
        return '[ ' . str_pad(mb_strimwidth($this->value, 0, 15, '...'), 15) . ', ' .  str_pad(mb_strimwidth($this->type, 0, 15, '...'), 15) . ' ,' . $this->line . ':' . $this->column . ':' . $this->position . ',' . $this->length . ',' . mb_strimwidth($this->note, 0, 10, '...') . ']';
    }
}

/**
 * The base lexer implementation for CAS-syntax without any localisation
 * or base-n features. To extend replace the `get_next_token()` logic.
 */
class stack_maxima_base_lexer {
    /* Some common char classes. */
    public static $WS = '~\s+~u';
    public static $DIGITS = ['0' => true, '1' => true, '2' => true, 
                             '3' => true, '4' => true, '5' => true,
                             '6' => true, '7' => true, '8' => true,
                             '9' => true];
    public static $LETTER = '/\pL/iu';

    /* The options easilly accessible to all inheriting lexers. */
    public $options;

    /* The content we are working on. */
    private $buffer = null;

    /* Stuff we have in output buffer. */
    public $outputbuffer = [];

    /* Copy of the original input. */
    public $original = '';

    /* Some counters. */
    public $line = 1;
    public $linechar = 0;
    public $char = 0;

    public function __construct(string $src, stack_parser_options $options) {
        $this->original = $src;
        $this->options = $options;

        // Input buffer. Reverse so that we can pop instead of shift.
        $this->buffer = array_reverse(preg_split('//u', $src, -1, PREG_SPLIT_NO_EMPTY));
        // Output buffer. To deal with virtual tokens like inserted stars.
        $this->outputbuffer = [];

        $this->line = 1;
        $this->linechar = 0;
        $this->char = 0;
    }

    /**
     * Certain unicode translations may happen if asked for.
     * Note that typically we do not do those if we are inside strings etc.
     * Also once translated we do not revert that translation if a char gets returned.
     */
    public function popc(bool $ucconvert = true) {
        if (count($this->buffer) > 0) {
            $c = array_pop($this->buffer);
            $this->char++;
            if ($c === "\n") {
                $this->line = 1 + $this->line;
                $this->linechar = 0;
            } else {
                $this->linechar = 1 + $this->linechar;
            }
            if ($ucconvert && isset($this->options->unicodemap[$c])) {
                $tmp = $this->options->unicodemap[$c];
                if (mb_strlen($tmp) === 1) {
                    // Most of these should be single letter replacements.
                    return $tmp;
                } else {
                    // But if not things get messy for the character positions.
                    // No protection for definition loops, just don't do them.
                    foreach (array_reverse(preg_split('//u', $tmp, -1, PREG_SPLIT_NO_EMPTY)) as $char) {
                        $this->buffer[] = $char;
                    }
                    return array_pop($this->buffer);
                }
            }

            return $c;
        }
        return null;
    }

    public function pushc($chr): void {
        if ($chr === null) {
            return;
        }
        if ($chr === "\n") {
            $this->line--;
            // The linechar here is irrelevant.
        } else {
            $this->linechar--;
        }
        $this->char--;
        $this->buffer[] = $chr;
    }

    /**
     * Reset to the start of input string, used for testing.
     */
    public function reset(): void {
        // Input buffer. Reverse so that we can pop instead of shift.
        $this->buffer = array_reverse(preg_split('//u', $this->original, -1, PREG_SPLIT_NO_EMPTY));
        // Output buffer. To deal with virtual tokens like inserted stars.
        $this->outputbuffer = [];

        $this->line = 1;
        $this->linechar = 0;
        $this->char = 0;   
    }

    /**
     * Returns the next token from the stream, or null for end of stream.
     */
    public function get_next_token(): ?stack_maxima_token {
        // If some action has added something to the buffer.
        if (count($this->outputbuffer) > 0) {
            return array_shift($this->outputbuffer);
        }

        $c1 = $this->popc();
        // End of stream.
        if ($c1 === null) {
            return null;
        }

        // Start with the easy cases.
        $token = new stack_maxima_token('SYMBOL', $c1, $this->line, $this->linechar, $this->char, 1);

        switch ($c1) {
            case ',':
                $token->type = 'LIST_SEP';
                return $token;
            case ';':
            case '$':
                $token->type = 'END_TOKEN';
                return $token;
            case '-':
            case '(':
            case ')':
            case '[':
            case ']':
            case '{':
            case '}':
            case '~':
            case '=':
            case '|':
                // The simplest of symbols.
                return $token;
            case '>':
            case '<':
                // Maybe longer.
                $c2 = $this->popc();
                if ($c2 === '=') {
                    $token->value = $c1 . $c2;
                    $token->length = 2;
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case '*':
            case '^':
            case '!':
            case "'":
                // Potenttially doubling ones.
                $c2 = $this->popc();
                if ($c2 === $c1) {
                    $token->value = $c1 . $c2;
                    $token->length = 2;
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case '+':
                // We might support the '+-' operator.
                if ($this->options->pm) {
                    $c2 = $this->popc();
                    if ($c2 === '-') {
                        $token->value = $c1 . $c2;
                        $token->length = 2;
                    } else {
                        $this->pushc($c2);
                    }
                }
                return $token;
            case ':':
                // Various types of assignements.
                $c2 = $this->popc();
                if ($c2 === ':') {
                    $token->value = $c1 . $c2;
                    $token->length = 2;
                    $c3 = $this->popc();
                    if ($c3 === '=') {
                        $token->value = $c1 . $c2 . $c3;
                        $token->length = 3;
                    } else {
                        $this->pushc($c3);
                    }
                } else if ($c2 === '=') {
                    $token->value = $c1 . $c2;
                    $token->length = 2;
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case '?':
                // The operator in its various forms as well as the id.
                $c2 = $this->popc();
                if ($c2 === '?') {
                    $token->value = $c1 . $c2;
                    $token->length = 2;
                    $c3 = $this->popc();
                    if ($c3 === ' ') {
                        $token->value = $c1 . $c2 . $c3;
                        $token->length = 3;
                    } else {
                        $this->pushc($c3);
                    }
                } else if ($c2 === ' ') {
                    $token->value = $c1 . $c2;
                    $token->length = 2;
                } else if ($this->options->lispids && $c2 !== null && ($c2 === '\\' || '%' === $c2 || '_' === $c2 || preg_match(self::$LETTER, $c2) === 1)) {
                    // Search for LISP_ID
                    $token2 = new stack_maxima_token('LISP_ID', $c2, $this->line, $this->linechar, $this->char, 1);
                    if ($c2 === '\\') {
                        $c3 = $this->popc();    
                        if ($c3 !== null) {
                            $token2->value .= $c3;
                            $token2->length = $token2->length + 1;
                        }
                    }
                    $c3 = $this->popc();
                    while ($c3 !== null && ($c3 === '\\' || '%' === $c3 || '_' === $c3 || isset(self::$DIGITS[$c3]) || preg_match(self::$LETTER, $c3) === 1)) {
                        $token2->value .= $c3;
                        $token2->length = $token2->length + 1;
                        if ($c3 === '\\') {
                            $c3 = $this->popc();
                            if ($c3 !== null) {
                                $token2->value .= $c3;
                                $token2->length = $token2->length + 1;
                            }
                        }
                        $c3 = $this->popc();
                    }
                    $this->pushc($c3);
                    if (mb_strlen($token2->value) > 0) {
                        // LISP_IDs are passed as two separate tokens, the `?` symbol and the ID are separate and the latter needs to wait in the buffer for its turn.
                        $this->outputbuffer[] = $token2;
                    }
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case '#':
                // That other plus-minus.
                $c2 = $this->popc();
                if ($c2 === 'p') {
                    $c3 = $this->popc();
                    if ($c3 === 'm') {
                        $c4 = $this->popc();
                        if ($c4 === '#') {
                            $token->value = '#pm#';
                            $token->length = 4;
                        } else {
                            $this->pushc($c4);
                        }
                    } else {
                        $this->pushc($c3);
                    }
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case ' ':
            case "\n":
            case "\t":
                return $this->eat_whitespace($c1, $this->line, $this->linechar);
            case '"':
                return $this->eat_string();
            case '/':
                // Maybe it's a div, maybe it's a comment.
                $c2 = $this->popc();
                if ($c2 === '*') {
                    return $this->eat_comment();
                } else {
                    $this->pushc($c2);
                }
                return $token;
        }

        // Then the more complex things.

        // First numbers.
        if ($c1 === '.' || isset(self::$DIGITS[$c1])) {
            // For now don't unicode translate within numbers.
            $c2 = $this->popc(false);
            $content = $c1;

            $numbermode = 'pre-dot';

            if ($c1 === '.' && !isset(self::$DIGITS[$c2])) {
                // The dot was just a dot.
                $this->pushc($c2);
                return $token; // Token type still symbol and $c1 the value.
            } else if ($c1 === '.') {
                // We continue with digits.
                $numbermode = 'post-dot';
                $content .= $c2;
            } else if (isset(self::$DIGITS[$c2])) {
                // We continue with digits.
                $content .= $c2;
            } else {
                // Digit followed by something else.
                switch ($c2) {
                    case '.':
                        $numbermode = 'post-dot';
                        $content .= $c2;
                        break;
                    case 'e':
                    case 'E':
                        $c3 = $this->popc(false);
                        if ($c3 === '-' || $c3 === '+') {
                            $c4 = $this->popc(false);
                            if (isset(self::$DIGITS[$c4])) {
                                $numbermode = 'exponent';
                                $content .= $c2 . $c3 . $c4;
                            } else {
                                // Not a valid start for exponent. Return what we have.
                                $this->pushc($c4);
                                $this->pushc($c3);
                                $this->pushc($c2);
                                $token->value = $content;
                                if (mb_strpos($content, '.') !== false) {
                                    $token->type = 'FLOAT';
                                } else {
                                    $token->type = 'INT';
                                }
                                $token->length = mb_strlen($content);
                                return $token;    
                            }
                        } else if (isset(self::$DIGITS[$c3])) {
                            $numbermode = 'exponent';
                            $content .= $c2 . $c3;
                        } else {
                            // The case of a single digit integer. No sensible exponent.
                            $this->pushc($c3);
                            $this->pushc($c2);
                            $token->type = 'INT';
                            $token->length = mb_strlen($content);
                            return $token;    
                        }
                        break;
                    default:
                        // The case of a single digit integer.
                        $this->pushc($c2);
                        $token->type = 'INT';
                        $token->length = mb_strlen($content);
                        return $token;
                }
            }

            // Continue for more digits.
            while (true) {
                $c = $this->popc(false);
                if ($c === null) {
                    // End of stream.
                    if ($numbermode === 'pre-dot') {
                        $token->type = 'INT';
                    } else {
                        $token->type = 'FLOAT';
                    }
                    $token->value = $content;
                    $token->length = mb_strlen($content);
                    return $token;
                }

                if (isset(self::$DIGITS[$c])) {
                    $content .= $c;
                    $token->length = $token->length + 1;
                } else if ($c === 'e' || $c === 'E') {
                    if ($numbermode === 'exponent') {
                        // Cannot have more exponents.
                        $token->type = 'FLOAT';
                        $token->value = $content;
                        $this->pushc($c);
                        $token->length = mb_strlen($content);
                        return $token;
                    } else {
                        // Starting exponent needs to be a digit or + or -.
                        $c3 = $this->popc(false);
                        if (isset(self::$DIGITS[$c3])) {
                            $numbermode = 'exponent';
                            $content .= $c . $c3;
                        } else if ($c3 === '-' || $c3 === '+') {
                            $c4 = $this->popc();
                            if (isset(self::$DIGITS[$c4])) {
                                $numbermode = 'exponent';
                                $content .= $c . $c3 . $c4;
                            } else {
                                // Not a valid start for exponent. Return what we have.
                                $this->pushc($c4);
                                $this->pushc($c3);
                                $this->pushc($c);
                                $token->value = $content;
                                if (mb_strpos($content, '.') !== false) {
                                    $token->type = 'FLOAT';
                                } else {
                                    $token->type = 'INT';
                                }
                                $token->length = mb_strlen($content);
                                return $token;    
                            }
                        } else {
                            // Not a valid start for exponent. Return what we have.
                            $this->pushc($c3);
                            $this->pushc($c);
                            $token->value = $content;
                            $token->length = mb_strlen($content);
                            if (mb_strpos($content, '.') !== false) {
                                $token->type = 'FLOAT';
                            } else {
                                $token->type = 'INT';
                            }
                            return $token;
                        }
                    }
                } else if ($c === '.') {
                    if ($numbermode === 'pre-dot') {
                        $content .= $c;
                        $numbermode = 'post-dot';
                    } else {
                        // Can't have more stop here.
                        $this->pushc($c);
                        $token->value = $content;
                        $token->type = 'FLOAT';
                        $token->length = mb_strlen($content);
                        return $token;
                    }
                } else {
                    $this->pushc($c);
                    $token->value = $content;
                    if ($numbermode === 'pre-dot') {
                        $token->type = 'INT';
                    } else {
                        $token->type = 'FLOAT';
                    }
                    $token->length = mb_strlen($content);
                    return $token;
                }

            }

        }

        // Then identifiers.
        if ($c1 === '%' || $c1 === '_' || preg_match(self::$LETTER, $c1) === 1) {
            $token->type = 'ID';

            while (true) {
                // $c1 has had unicode translation, so it means that no identifier
                // may start with those chars.
                $c = $this->popc(false);
                if ($c === null) {
                    break;
                }
                if ($c === '%' || $c === '_' || preg_match(self::$LETTER, $c) === 1 || isset(self::$DIGITS[$c])) {
                    $token->value .= $c;
                    $token->length = $token->length + 1;
                } else if ($this->options->extraletters !== false && preg_match($this->options->extraletters, $c) === 1) {
                    $token->value .= $c;
                    $token->length = $token->length + 1;
                } else {
                    $this->pushc($c);
                    break;
                }
            }


            // Check if this would have a special meaning, i.e. 
            // is a keyword or boolean also translations.
            return $this->kwidentify($token);
        }

        // More complex whitespace.
        if (preg_match(self::$WS, $c1) === 1) {
            return $this->eat_whitespace($c1, $this->line, $this->linechar);
        }


        // No idea what that was?
        $token->type = 'ERROR';
        $token->note = $token->value;
        $token->value = 'Unexpected character.';
        return $token;
    }

    /**
     * The parser side might return a token when generating a missing 
     * star or semicolon. It will take the token back very soon, but 
     * for now lets help it and hold onto the token.
     */
    public function return_token(stack_maxima_token $tok) {
        if ($tok !== null) {
            array_unshift($this->outputbuffer, $tok);
        }
    }

    /**
     * General implementation for handling comments. Assume previous
     * two chars are '/*' and position is therefore offset by two.
     */
    public function eat_comment(): stack_maxima_token {
        $token = new stack_maxima_token('COMMENT', '', $this->line, $this->linechar - 2, $this->char - 2, 2);

        $content = '';
        while (true) {
            $c1 = $this->popc(false);
            if ($c1 === '*') {
                $c2 = $this->popc(false);
                if ($c2 === null) {
                    $content = $content . $c1;
                    $token->length = $token->length + 1;
                    // Simpler to return to the buffer.
                    $this->pushc($c2);
                } else if ($c2 === '/') {
                    $token->length = $token->length + 2;
                    break;
                } else {
                    $content = $content . $c1 . $c2;
                    $token->length = $token->length + 2;
                }
            } else if ($c1 === null) {
                // So input ended mid comment.
                $token->type = 'ERROR';
                $token->value = 'COMMENT NOT TERMINATED';
                $token->note = $content;
                return $token;
            } else {
                $content = $content . $c1;
                $token->length = $token->length + 1;
            }
        }
        $token->value = $content;
        return $token;
    }

    /**
     * General implementation for strings, assume previous char is '"'
     * and position therefore offset by 1.
     */
    public function eat_string(): stack_maxima_token {
        $token = new stack_maxima_token('STRING', '', $this->line, $this->linechar - 1, $this->char - 1, 1);

        $content = '';
        while (true) {
            $c1 = $this->popc(false);
            if ($c1 === '"') {
                $token->length = $token->length + 1;
                break;
            } else if ($c1 === '\\') {
                $c2 = $this->popc(false);
                $token->length = $token->length + 1;
                if ($c2 !== null) {
                    $content = $content . $c2;
                    $token->length = $token->length + 1;
                }
            } else if ($c1 === null) {
                // So input ended mid comment.
                $token->type = 'ERROR';
                $token->value = 'STRING NOT TERMINATED';
                $token->note = $content;
                return $token;
            } else {
                $content = $content . $c1;
                $token->length = $token->length + 1;
            }
        }

        $token->value = $content;
        return $token;
    }

    /**
     * General implementation for eating whitespace, to start this give
     * the initial position and whitespace. As returning e.g. "\n" through
     * the buffer is not simple.
     */
    public function eat_whitespace(string $initial, int $initialline, int $initialcolumn): stack_maxima_token {
        $token = new stack_maxima_token('WS', '', $initialline, $initialcolumn, $this->char - 1, 1);

        $content = $initial;
        while (true) {
            $c1 = $this->popc(false);
            if ($c1 === null) {
                break;
            } else if ($c1 === ' ' || preg_match(self::$WS, $c1) === 1) {
                // While ' ' is indeed part of the latter, it is so common
                // that avoiding having to call that later is worth it.
                $content = $content . $c1;
            } else {
                $this->pushc($c1);
                break;
            }
        }

        $token->value = $content;
        $token->length = mb_strlen($content);
        return $token;
    }

    /**
     * Identifies whether an identifier token is an identifer or keyword or something else.
     * If you are doing base-n you will probably need to extend this.
     * Returns a token that may have been modified.
     */
    public function kwidentify(stack_maxima_token $token): stack_maxima_token {
        $t = $token->value;
        // Remember the original form.
        $token->note = $t;
        $token->length = mb_strlen($t);
        if (!$this->options->casesensitivekeywords) {
            $t = mb_strtolower($t);
        }
        // Localised one?
        if ($this->options->locals !== null) {
            foreach ($this->options->locals as $orig => $local) {
                if ($this->options->casesensitivekeywords) {
                    if ($token->value === $local) {
                        $token->note = $token->value;
                        $token->value = $orig;
                        break;
                    }
                } else {
                    if ($t === mb_strtolower($local)) {
                        $token->note = $token->value;
                        $token->value = $orig;
                        break;   
                    }
                }
            }
        }

        // Convert all key things to lower case.
        if (!$this->options->casesensitivekeywords) {
            switch($t) {
                case 'not':
                case 'nounnot':
                case '%not':
                case '%and':
                case '%or':
                case 'and':
                case 'or':
                case 'nouneq':
                case 'nounadd':
                case 'nounand':
                case 'nounor':
                case 'nounsub':
                case 'nounpow':
                case 'noundiv':
                case 'nand':
                case 'nor':
                case 'implies':
                case 'xor':
                case 'xnor':
                case 'blankmult':
                case 'if':
                case 'then':
                case 'elseif':
                case 'else':
                case 'do':
                case 'for':
                case 'from':
                case 'step':
                case 'next':
                case 'in':
                case 'thru':
                case 'while':
                case 'unless':
                    $token->value = $t;
            }
        }

        // Special cases.
        if ($token->value === 'true' || $token->value === 'false') {
            $token->type = 'BOOL';
            $token->value = $t === 'true';
            return $token;
        }

        switch ($t) {
            case 'not':
                // Not is a special case, it needs to care abotu following whitespace.
                $tc = $this->popc();
                $token->type = 'SYMBOL';
                if ($tc === ' ') {
                    $token->value = $t . ' ';
                    return $token;
                }
                $this->pushc($tc);
                return $token;
            case 'nounnot':
            case '%not':
            case '%and':
            case '%or':
            case 'and':
            case 'or':
            case 'nouneq':
            case 'nounadd':
            case 'nounand':
            case 'nounor':
            case 'nounsub':
            case 'nounpow':
            case 'noundiv':
            case 'nand':
            case 'nor':
            case 'implies':
            case 'xor':
            case 'xnor':
            case 'UNARY_RECIP':
            case 'unary_recip':
            case 'blankmult':
                $token->type = 'SYMBOL';
                return $token;
            case 'if':
            case 'then':
            case 'elseif':
            case 'else':
            case 'do':
            case 'for':
            case 'from':
            case 'step':
            case 'next':
            case 'in':
            case 'thru':
            case 'while':
            case 'unless':
                $token->type = 'KEYWORD';
                return $token;
        }

        // Let it be an identifier.
        $token->type = 'ID';
        return $token;
    }

}