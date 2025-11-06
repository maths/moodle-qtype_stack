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

require_once('parser.options.class.php');

/**
 * A base class for Maxima parser lexers, this base class can be used as
 * a basis for specialised versions that handle base-N etc. this version
 * provides basic unicode character mapping and keyword mapping to 
 * localised versions. Basically, should do everything we supported before.
 * 
 * @package    qtype_stack
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

enum StackMaximaTokenType {
    case Keyword;
    /* Note the naming of these differs from some other language versions 
     * of this family of parsers. This is mainly due to keyword restrictions.
     */
    case IdAtom; 
    case IntAtom;
    case FloatAtom;
    case BoolAtom;
    case StringAtom;
    case Symbol;
    case WhiteSpace;
    case Comment;
    case ListSeparator;
    case EndToken;
    case LispIdentifier;
    case Error;
}


class stack_maxima_lexer_char {
    // A single unicode character
    public String $c;

    // Should this char be the result of multi char unicode replacement,
    // e.g. "\u0391" -> "Alpha" then these map to the original chars position.

    // The line on which it is.
    public int $line;
    // The column on which it is.
    public int $column;
    // The char index in the whole stream.
    public int $char;

    public function __construct(string $c, int $line, int $column, int $char) {
        $this->c = $c;
        $this->line = $line;
        $this->column = $column;
        $this->char = $char;
    }
}


class stack_maxima_lexer_token implements JsonSerializable {
    // The type of this token.
    public StackMaximaTokenType $type;

    // The value, i.e., content of this token after possible localised syntax
    // has been removed.
    public String|int $value;

    // Error message, value before any translations.
    public ?String $note;

    // The line this token starts on.
    public int $startline;

    // The column this token starts on.
    public int $startcolumn;

    // The char this token starts on.
    public int $startchar;

    // The line this token ends on. Inclusive.
    public int $endline;

    // The column this token ends on. Inclusive.
    public int $endcolumn;

    // The char this token ends before. Exclusive.
    public int $endchar;

    // Initialises a token as WhiteSpace and sets the start position to
    // the given char.
    // Initialises the value to that first char.
    public function __construct(stack_maxima_lexer_char $firstchar) {
        $this->type = StackMaximaTokenType::WhiteSpace;
        $this->value = $firstchar->c;
        $this->note = null;

        $this->startchar = $firstchar->char;
        $this->endchar = $firstchar->char + 1;
        $this->startline = $firstchar->line;
        $this->endline = $firstchar->line;
        $this->startcolumn = $firstchar->column;
        $this->endcolumn = $firstchar->column;
    }

    public function set_end_position(stack_maxima_lexer_char $lastchar) {
        $this->endchar = $lastchar->char + 1;
        $this->endline = $lastchar->line;
        $this->endcolumn = $lastchar->column;
    }

    public function jsonSerialize(): mixed {
        $r = [
            'type' => null,
            'value' => $this->value,
            'note' => $this->note,
            'startchar' => $this->startchar,
            'endchar' => $this->endchar,
            'startline' => $this->startline,
            'endline' => $this->endline,
            'startcolumn' => $this->startcolumn,
            'endcolumn' => $this->endcolumn,
        ];
        $r['type'] = match ($this->type) {
            StackMaximaTokenType::Keyword => "KW",
            StackMaximaTokenType::IdAtom => "ID",
            StackMaximaTokenType::IntAtom => "INT",
            StackMaximaTokenType::FloatAtom => "FLOAT",
            StackMaximaTokenType::BoolAtom => "BOOL",
            StackMaximaTokenType::StringAtom => "STRING",
            StackMaximaTokenType::Symbol => "SYMBOL",
            StackMaximaTokenType::WhiteSpace => "WS",
            StackMaximaTokenType::Comment => "COMMENT",
            StackMaximaTokenType::ListSeparator => "LIST_SEP",
            StackMaximaTokenType::EndToken => "END_TOKEN",
            StackMaximaTokenType::LispIdentifier => "LISP_ID",
            StackMaximaTokenType::Error => "ERROR"
        };
        return $r;
    }

    public function __toString(): string {
        return match ($this->type) {
            StackMaximaTokenType::Keyword => $this->value,
            StackMaximaTokenType::IdAtom => $this->value,
            StackMaximaTokenType::IntAtom => $this->value,
            StackMaximaTokenType::FloatAtom => $this->value,
            StackMaximaTokenType::BoolAtom => $this->value,
            StackMaximaTokenType::StringAtom =>  '"' . str_replace('"', '\\"', str_replace('\\', '\\\\', $this->value)) . '"',
            StackMaximaTokenType::Symbol => $this->value,
            StackMaximaTokenType::WhiteSpace => " ",
            StackMaximaTokenType::Comment => '/*' . $this->value . '*/',
            StackMaximaTokenType::ListSeparator => ',',
            StackMaximaTokenType::EndToken => ';',
            StackMaximaTokenType::LispIdentifier => $this->value,
            StackMaximaTokenType::Error => $this->value
        };
    }

    /**
     * Special tool for dealing with `^-` at parser level.
     */
    public function merge(stack_maxima_lexer_token $other): stack_maxima_lexer_token {
        $t = clone $this;
        $t->endchar = $other->endchar;
        $t->endline = $other->endline;
        $t->endcolumn = $other->endcolumn;
        $t->value = $this->value . ' ' . $other->value;
        return $t;
    }
    /**
     * Special tool for dealing with `^-` at parser level.
     */
    public function split(): array {
        $t1 = clone $this;
        $t2 = clone $this;

        $t1->value = explode(' ', $this->value, 2)[0];
        $t2->value = explode(' ', $this->value, 2)[1];

        $t1->endline = $t1->startline;
        $t1->endchar = $t1->startchar + mb_strlen($t1->value);
        $t1->endcolumn = $t1->startcolumn + mb_strlen($t1->value);

        $t2->startline = $t2->endline;
        $t2->startchar = $t2->endchar - mb_strlen($t2->value);
        $t2->startcolumn = $t2->endcolumn - mb_strlen($t2->value);

        return [$t1, $t2];
    }

}


class stack_maxima_lexer_base {
    /* Some common char classes. */
    public static $WS = '~\s+~u';
    public static $DIGITS = ['0' => true, '1' => true, '2' => true,
                             '3' => true, '4' => true, '5' => true,
                             '6' => true, '7' => true, '8' => true,
                             '9' => true];
    public static $NZDIGITS = ['1' => true, '2' => true, '3' => true,
                             '4' => true, '5' => true, '6' => true,
                             '7' => true, '8' => true, '9' => true];
    public static $LETTER = '/\pL/iu';
    public static $ALPHA = 
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /* The options easilly accessible to all inheriting lexers. */
    public $options;

    /* The content we are working on. */
    private $buffer = null;

    /* Stuff we have in output buffer. E.g., returned or injected tokens 
       or lexer producing multiple tokens at once. */
    public $outputbuffer = [];

    /* Copy of the original input. */
    public $original = '';

    public function __construct(string $src, stack_parser_options $options) {
        $this->original = $src;
        $this->options = $options;
        $this->reset();
    }


    /**
     * Certain unicode translations may happen if asked for.
     * Note that typically we do not do those if we are inside strings etc.
     * Also once translated we do not revert that translation if a char gets returned.
     */
    public function popc(bool $ucconvert = true): ?stack_maxima_lexer_char {
        if (count($this->buffer) > 0) {
            $c = array_pop($this->buffer);
            if ($ucconvert && isset($this->options->unicodemap[$c->c])) {
                $tmp = $this->options->unicodemap[$c->c];
                if (mb_strlen($tmp) === 1) {
                    // Most of these should be single letter replacements.
                    return new stack_maxima_lexer_char($tmp, $c->line, $c->column, $c->char);
                } else {
                    // But if not things get messy for the character positions.
                    // No protection for definition loops, just don't do them.
                    foreach (array_reverse(preg_split('//u', $tmp, -1, PREG_SPLIT_NO_EMPTY)) as $char) {
                        $this->buffer[] = new stack_maxima_lexer_char($char, $c->line, $c->column, $c->char);
                    }
                    return array_pop($this->buffer);
                }
            }

            return $c;
        }
        return null;
    }

    public function pushc(stack_maxima_lexer_char $chr): void {
        $this->buffer[] = $chr;
    }

    /**
     * Tries to eat a sequence of fixed chars, if possible returns the last
     * char of that sequence. Otherwise returns null.
     */
    public function eat(array $seq, bool $ucconvert = true): ?stack_maxima_lexer_char {
        $popped = [];
        $cand = null;
        foreach ($seq as $c) {
            $cand = $this->popc($ucconvert);
            if ($cand !== null) {
                array_unshift($popped, $cand);
            }
            if ($cand === null || $cand->c !== $c) {
                foreach ($popped as $cc) {
                    $this->buffer[] = $cc;
                }
                return null;
            }
        }
        return $cand;
    }


    /**
     * For testing reset everything to the first token. 
     */
    public function reset() {
        // Input buffer. Reverse so that we can pop instead of shift.
        $this->buffer = [];
        
        // Output buffer. To deal with virtual tokens like inserted stars.
        $this->outputbuffer = [];

        $line = 1;
        $column = 1;
        $char = 0;
        foreach(preg_split('//u', $this->original, -1, PREG_SPLIT_NO_EMPTY) as $c) {
            $this->buffer[] = new stack_maxima_lexer_char($c, $line, $column, $char);
            $char++;
            if ($c === "\n") {
                $line++;
                $column = 1;
            } else {
                $column++;
            }
        }
        $this->buffer = array_reverse($this->buffer);
    }

    /**
     * Returns the next token from the stream, or null for end of stream.
     */
    public function get_next_token(): ?stack_maxima_lexer_token {
        // If some action has added something to the buffer.
        if (count($this->outputbuffer) > 0) {
            return array_shift($this->outputbuffer);
        }

        $c1 = $this->popc();
        // End of stream.
        if ($c1 === null) {
            return null;
        }

        // Start from this.
        $token = new stack_maxima_lexer_token($c1);

        switch ($c1->c) {
            case ',':
                $token->type = StackMaximaTokenType::ListSeparator;
                return $token;
            case ';':
            case '$':
                // Note we do not care about any $options related to these
                // in this base lexer.
                $token->type = StackMaximaTokenType::EndToken;
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
                $token->type = StackMaximaTokenType::Symbol;
                return $token;
            case '@':
                $token->type = StackMaximaTokenType::Symbol;
                $c4 = $this->eat(['@', 'I', 's', '@', '@']);
                if ($c4 !== null) {
                    $token->value = '@@Is@@';
                    $token->set_end_position($c4);
                } else {
                    $c4 = $this->eat(['@', 'I', 'S', '@', '@']);
                    if ($c4 !== null) {
                        $token->value = '@@IS@@';
                        $token->set_end_position($c4);
                    }    
                }
                return $token;
            case '>':
            case '<':
                // Maybe longer.
                $c2 = $this->popc();
                $token->type = StackMaximaTokenType::Symbol;
                if ($c2 === null) {
                    return $token;
                } else if ($c2->c === '=') {
                    $token->value .= $c2->c;
                    $token->set_end_position($c2);
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
                $token->type = StackMaximaTokenType::Symbol;
                if ($c2 === null) {
                    return $token;
                } else if ($c2->c === $c1->c) {
                    $token->value .= $c2->c;
                    $token->set_end_position($c2);
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case '+':
                // We might support the '+-' operator.
                $token->type = StackMaximaTokenType::Symbol;
                if ($this->options->pm) {
                    $c2 = $this->popc();
                    if ($c2 === null) {
                        return $token;
                    } else if ($c2->c === '-') {
                        $token->value .= $c2->c;
                        $token->set_end_position($c2);
                    } else {
                        $this->pushc($c2);
                    }
                }
                return $token;
            case ':':
                // Various types of assignments.
                $c2 = $this->popc();
                $token->type = StackMaximaTokenType::Symbol;
                if ($c2 === null) {
                    return $token;
                } else if ($c2->c === ':') {
                    $token->value .= $c2->c;
                    $token->set_end_position($c2);
                    $c3 = $this->popc();
                    if ($c3 === null) {

                    } else if ($c3->c === '=') {
                        $token->value .= $c3->c;
                        $token->set_end_position($c3);
                    } else {
                        $this->pushc($c3);
                    }
                } else if ($c2->c === '=') {
                    $token->value .= $c2->c;
                    $token->set_end_position($c2);
                } else {
                    $this->pushc($c2);
                }
                return $token;
            case '?':
                if ($this->options->lispids === false) {
                    $token->value = 'QMCHAR';
                    $token->type = StackMaximaTokenType::IdAtom;
                    return $token;
                }

                // The operator in its various forms as well as the id.
                $c2 = $this->popc();
                $token->type = StackMaximaTokenType::Symbol;
                if ($c2->c === '?') {
                    $token->value .= $c2->c;
                    $c3 = $this->popc();
                    if ($c3->c === ' ') {
                        $token->value .= $c3->c;
                        $token->set_end_position($c3);
                    } else {
                        $this->pushc($c3);
                        $token->set_end_position($c2);
                    }
                } else if ($c2->c === ' ') {
                    $token->value .= $c2->c;
                    $token->set_end_position($c2);
                } else if ($c2 !== null && ($c2->c === '\\' || '%' === $c2->c || '_' === $c2->c || preg_match(self::$LETTER, $c2->c) === 1)) {
                    // Search for LISP_ID
                    $token2 = new stack_maxima_lexer_token($c2);
                    $token2->type = StackMaximaTokenType::LispIdentifier;
                    if ($c2->c === '\\') {
                        $c3 = $this->popc();    
                        if ($c3 !== null) {
                            $token2->value .= $c3->c;
                            $token2->set_end_position($c3);
                        }
                    }
                    $c3 = $this->popc();
                    while ($c3 !== null && ($c3->c === '\\' || '%' === $c3->c || '_' === $c3->c || isset(self::$DIGITS[$c3->c]) || preg_match(self::$LETTER, $c3->c) === 1)) {
                        $token2->value .= $c3->c;
                        if ($c3->c === '\\') {
                            $c4 = $this->popc();
                            if ($c4 !== null) {
                                $token2->value .= $c4->c;
                                $token2->set_end_position($c4);
                            }
                        }
                        $token2->set_end_position($c3);
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
                $token->type = StackMaximaTokenType::Symbol;
                $c4 = $this->eat(['p', 'm', '#']);
                if ($c4 !== null) {
                    $token->value = '#pm#';
                    $token->set_end_position($c4);
                }
                return $token;
            case ' ':
            case "\n":
            case "\t":
                return $this->eat_whitespace($token);
            case '"':
                return $this->eat_string($token);
            case '/':
                // Maybe it's a div, maybe it's a comment.
                $c2 = $this->popc();
                $token->type = StackMaximaTokenType::Symbol;
                if ($c2 === null) {
                    return $token;
                } else if ($c2->c === '*') {
                    return $this->eat_comment($token);
                } else {
                    $this->pushc($c2);
                }
                return $token;
        }

        if ($c1->c === '.' || isset(self::$DIGITS[$c1->c])) {
            return $this->eat_number($token);
        }

        if ($c1->c === '%' || $c1->c === '_' || preg_match(self::$LETTER, $c1->c) === 1) {
            return $this->kwidentify($this->eat_identifier($token));
        }

        if (preg_match(self::$WS, $c1->c) === 1) {
            return $this->eat_whitespace($token);
        }

        // No idea what that was?
        $token->type = StackMaximaTokenType::Error;
        $token->note = $token->value;
        $token->value = 'UNEXPECTED CHARACTER';
        return $token;
    }

    /**
     * The parser side might return a token when generating a missing 
     * star or semicolon. It will take the token back very soon, but 
     * for now lets help it and hold onto the token.
     */
    public function return_token(stack_maxima_lexer_token $tok) {
        if ($tok !== null) {
            array_unshift($this->outputbuffer, $tok);
        }
    }


    /**
     * Continues eating starting from '/*' 
     */
    public function eat_comment(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $token->type = StackMaximaTokenType::Comment;
        $token->value = ''; // Only track the content so throw '/*' out.
        while (true) {
            $c1 = $this->popc(false); // No unicode translation here.
            if ($c1 === null) {
                // So input ended mid comment.
                $token->note = $token->value;
                $token->value = 'COMMENT NOT TERMINATED';
                $token->type = StackMaximaTokenType::Error;
                return $token;
            } else if ($c1->c === '*') {
                $c2 = $this->popc(false);
                if ($c2 === null) {
                    // So input ended mid comment.
                    $token->note = $token->value . '*';
                    $token->value = 'COMMENT NOT TERMINATED';
                    $token->type = StackMaximaTokenType::Error;
                    return $token;
                } else if ($c2->c === '/') {
                    $token->set_end_position($c2);
                    break;
                } else if ($c2->c === '*') {
                    // We need to take a step back.
                    $this->pushc($c2);
                    $token->value .= $c1->c;
                } else {
                    $token->value .= $c1->c . $c2->c;
                }
            } else {
                $token->value .= $c1->c;       
            }
        }
        return $token;
    }

    /**
     * General implementation for strings, assume previous char is '"'.
     * Unescapes the value.
     */
    public function eat_string(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $token->type = StackMaximaTokenType::StringAtom;
        $token->value = ''; // Drop the quotes.
        
        while (true) {
            $c1 = $this->popc(false); // Ignore unicode conversion here.
            if ($c1 === null) {
                // So input ended mid string.
                $token->note = $token->value;
                $token->value = 'STRING NOT TERMINATED';
                $token->type = StackMaximaTokenType::Error;
                return $token;
            } else if ($c1->c === '"') {
                $token->set_end_position($c1);
                break;
            } else if ($c1->c === "\\") {
                $c2 = $this->popc(false);
                if ($c2 !== null) {
                    $token->value .= $c2->c;
                } else {
                    // So input ended mid string.
                    $token->note = $token->value . "\\";
                    $token->value = 'STRING NOT TERMINATED';
                    $token->type = StackMaximaTokenType::Error;
                    return $token;
                }
            } else {
                $token->value .= $c1->c;
            }
        }

        return $token;
    }


    /**
     * General implementation for eating whitespace.
     */
    public function eat_whitespace(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $token->type = StackMaximaTokenType::WhiteSpace;

        $last = null;
        while (true) {
            $c1 = $this->popc(false);
            if ($c1 === null) {
                if ($last !== null) {
                    // If immediate end of stream no need to update 
                    // end position but otherwise.
                    $token->set_end_position($last);
                }
                break;
            } else if ($c1->c === ' ' || preg_match(self::$WS, $c1->c) === 1) {
                $token->value .= $c1->c;
            } else {
                if ($last !== null) {
                   $token->set_end_position($last);
                }
                $this->pushc($c1);
                break;
            }
            $last = $c1;
        }

        return $token;
    }

    /**
     * Base-10 integers and floats, assumes that the start token is 
     * a digit or a decimal dot.
     */
    public function eat_number(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $numbermode = 'pre-dot';
        $last = null;
        if ($token->value === '.') {
            $numbermode = 'post-dot';
        
            // Check for matrix multiplication before a dot-starting float.
            $c1 = $this->popc();
            if ($c1 === null || !isset(self::$DIGITS[$c1->c])) {
                // The token was the dot operator.
                $token->type = StackMaximaTokenType::Symbol;
                if ($c1 !== null) {
                    $this->pushc($c1);
                }
                return $token;
            } else {
                $last = $c1;
                $token->value .= $c1->c;
            }
        }  else if (strpos($token->value, '.')) {
            // We might use this function with more than single char 
            // start token.
            $numbermode = 'post-dot';
        }

        // At this point the $value is ending with a digit.
        while (true) {
            $c1 = $this->popc();
            if ($c1 === null) {
                if ($last !== null) {
                    // If immediate end of stream no need to update 
                    // end position but otherwise.
                    $token->set_end_position($last);
                }
                break;
            } else if ($c1->c === '.') {
                if ($numbermode !== 'pre-dot') {
                    // No second dot nor dots in exponent.
                    $token->set_end_position($last);
                    $this->pushc($c1);
                    break;
                } else {
                    $numbermode = 'post-dot';
                    $token->value .= $c1->c;
                    $last = $c1;
                }
            } else if ($c1->c === 'e' || $c1->c === 'E') {
                if ($numbermode === 'exponent') {
                    // Not an exponent in exponent.
                    $token->set_end_position($last);
                    $this->pushc($c1);
                    break;
                }
                // Is it a start of an exponent?
                $c2 = $this->popc();
                if (isset(self::$DIGITS[$c2->c])) {
                    $numbermode = 'exponent';
                    $token->value .= $c1->c . $c2->c;
                    $last = $c2;
                } else if ($c2->c === '-' || $c2->c === '+') {
                    // Maybe a signed one?
                    $c3 = $this->popc();
                    if (isset(self::$DIGITS[$c3->c])) {
                        $numbermode = 'exponent';
                        $token->value .= $c1->c . $c2->c . $c3->c;
                        $last = $c3;
                    } else {
                        // Was not an exponent.
                        $this->pushc($c3);
                        $this->pushc($c2);
                        $this->pushc($c1);
                        $token->set_end_position($last);
                        break;
                    }
                } else {
                    // Was not an exponent.
                    $this->pushc($c2);
                    $this->pushc($c1);
                    $token->set_end_position($last);
                    break;
                }
            } else if (isset(self::$DIGITS[$c1->c])) {
                $token->value .= $c1->c;
                $last = $c1;
            } else {
                if ($last !== null) {
                    // Else the first was the last.
                    $token->set_end_position($last);
                }
                $this->pushc($c1);
                break;
            }
        }

        if ($numbermode === 'pre-dot') {
            $token->type = StackMaximaTokenType::IntAtom;
            $token->note = $token->value;
            // Now we might have a directly workable value?
            if (('' . intval($token->value)) === $token->value) {
                $token->value = intval($token->value);
            }
        } else {
            $token->type = StackMaximaTokenType::FloatAtom;
            $token->note = $token->value;
        }
        return $token;
    }


    /**
     * General identifier eater, will append all chars it finds acceptable
     * to the starter present in the initial token.
     * 
     * Do send the result to be identified as a keyword or for base-n if 
     * need be. This will not do that automatically.
     */
    public function eat_identifier(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $token->type = StackMaximaTokenType::IdAtom;
        
        $last = null;
        while (true) {
            // Note that the first char may have had unicode converted but the
            // remaining ones will not.
            $c1 = $this->popc(false);
            if ($c1 === null) {
                if ($last !== null) {
                    // If immediate end of stream no need to update 
                    // end position but otherwise.
                    $token->set_end_position($last);
                }
                break;
            } else if ($c1->c === '%' || $c1->c === '_' || preg_match(self::$LETTER, $c1->c) === 1 || isset(self::$DIGITS[$c1->c])) {
                $token->value .= $c1->c;
            } else if ($this->options->extraletters !== false && preg_match($this->options->extraletters, $c1->c) === 1) {
                // Basically some specific chars like superscript numbers 
                // might be considered as part of the identifier, even if not
                // true letters or digits. Those are likely filtered and 
                // converted later.
                $token->value .= $c1->c;
            } else {
                if ($last !== null) {
                    // Else the first was the last.
                    $token->set_end_position($last);
                }
                $this->pushc($c1);
                break;
            }
            $last = $c1;
        }

        return $token;
    }


    /**
     * Identifies whether an identifier token is an identifer or keyword or something else.
     * If you are doing base-n you will probably need to extend this.
     * Returns a token that may have been modified.
     */
    public function kwidentify(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $t = $token->value;
        // Remember the original form.
        $token->note = $t;
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
                case 'nounmul':
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
            $token->type = StackMaximaTokenType::BoolAtom;
            return $token;
        }

        switch ($t) {
            case 'nounnot':
            case 'not':
                // Not is a special case, it needs to care about following whitespace.
                $tc = $this->popc();
                $token->type = StackMaximaTokenType::Keyword;
                if ($tc !== null && $tc->c === ' ') {
                    $token->type = StackMaximaTokenType::Symbol;
                    $token->value = $t . ' ';
                    return $token;
                }
                $this->pushc($tc);
                return $token;
            case 'let':
                if ($this->options->rule !== StackParserRule::Equivline) {
                    // To save the parser from doing the KW->ID thing
                    // only mark "let" as keyword in the grammar that
                    // uses it.
                    $token->type = StackMaximaTokenType::IdAtom;
                    return $token;
                }
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
            case 'nounmul':
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
                $token->type = StackMaximaTokenType::Keyword;
                return $token;
            case '%':
            case '%%':
                $token->note = $token->value;
                $token->value = 'LEXER LEVEL FORBIDDEN TOKEN';
                $token->type = StackMaximaTokenType::Error;
                return $token;
        }

        // Let it be an identifier.
        $token->type = StackMaximaTokenType::IdAtom;
        return $token;
    }
}