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
require_once('lexer.base.class.php');

/**
 * Variant of the base lexer using decimal commas and semicolons as
 * list separators. Dollars for end tokens.
 *
 * @package    qtype_stack
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
 */
class stack_maxima_lexer_decimal_comma extends stack_maxima_lexer_base {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
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
            case ';':
                $token->type = StackMaximaTokenType::ListSeparator;
                return $token;
            case '$':
                // Note we do not care about any $options related to these
                // in this base lexer.
                $token->type = StackMaximaTokenType::EndToken;
                return $token;
            case '.':
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
                    if ($c3->c === '=') {
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
                    if ($c3 === null) {
                        ;
                    } else if ($c3->c === ' ') {
                        $token->value .= $c3->c;
                        $token->set_end_position($c3);
                    } else {
                        $this->pushc($c3);
                        $token->set_end_position($c2);
                    }
                } else if ($c2->c === ' ') {
                    $token->value .= $c2->c;
                    $token->set_end_position($c2);
                } else if (
                        $c2 !== null
                        && ($c2->c === '\\' || '%' === $c2->c || '_' === $c2->c || preg_match(self::$LETTER, $c2->c) === 1)
                ) {
                    // Search for LISP_ID.
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
                    while (
                            $c3 !== null
                            && ($c3->c === '\\' || '%' === $c3->c || '_' === $c3->c || isset(self::$DIGITS[$c3->c])
                            || preg_match(self::$LETTER, $c3->c) === 1)
                    ) {
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
                        // LISP_IDs are passed as two separate tokens, the `?` symbol and the ID are separate
                        // and the latter needs to wait in the buffer for its turn.
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

        if ($c1->c === ',' || isset(self::$DIGITS[$c1->c])) {
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

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function eat_number(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        $numbermode = 'pre-dot';
        $last = null;
        if ($token->value === ',') {
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
            } else if ($c1->c === ',') {
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
            $token->value = str_replace(',', '.', $token->value);
        }
        return $token;
    }
}
