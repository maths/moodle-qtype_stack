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

require_once('lexer.base.class.php');

/**
 * Variant of the base lexer interpreting some base-N notations as
 * integers. Does not support decimal-numbers.
 *
 * @package    qtype_stack
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_maxima_lexer_basen extends stack_maxima_lexer_base {
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
            case ',':
                $token->type = StackMaximaTokenType::ListSeparator;
                return $token;
            case '$':
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

        if (isset(self::$DIGITS[$c1->c]) || strpos(self::$ALPHA, $c1->c) !== false) {
            return $this->eat_basen_number($token);
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



    public function eat_basen_number(stack_maxima_lexer_token $token): stack_maxima_lexer_token {
        // So we start from a digit or from alpha and need to figure out:
        // 1. Is it just a base-10 integer?
        // 2. Is it one of the C-cases? Starting with 0.
        // 3. Maybe it is our base_suffix form?
        // 4. Or even just an identifier or keyword?
        // We won't limit the digits 0-9,A-Z,a-z used based on
        // the specified base, that will be left to CAS side logic.
        // 0 padding is supportted, like 0000_2 or 0b0000
        $startswithalpha = !isset(self::$DIGITS[$token->value]);

        $chars = [];
        $mode = 'feed';

        // Basically, if it starts with `0x` or `0b` and has
        // only alpha or digits after those it is base-N
        // Also if otherwise pure alpha or digits and ends
        // with `_N` then it is base-N.
        $c = $this->popc();
        while ($c !== null && $mode === 'feed') {
            if ($c->c === '_') {
                // If we get numbers and a non id char we are happy.
                // Otherwise things depend on whether we have id like things.
                $cn = $this->popc();
                if ($cn === null) {
                    // Ended immediately after _
                    $this->pushc($c);
                    if ($startswithalpha) {
                        foreach ($chars as $ci) {
                            $token->value .= $ci;
                            $token->set_end_position($ci);
                        }
                        // Feeding that _ back.
                        return $this->kwidentify($this->eat_identifier($token));
                    } else {
                        // The maybe octal case. But with that dangling _.
                        $mode = 'revert?';
                        break;
                    }
                }
                $chars[] = $c;
                $mode = 'revert?';
                while ($cn !== null) {
                    if (isset(self::$DIGITS[$cn->c])) {
                        $chars[] = $cn;
                    } else if (
                        $cn->c === '%' || $cn->c === '_' ||  preg_match(self::$LETTER, $cn->c) === 1 ||
                        (($this->options->extraletters !== false && preg_match($this->options->extraletters, $cn->c) === 1))
                    ) {
                        $chars[] = $cn;
                        if ($startswithalpha) {
                            $mode = 'continue-as-id';
                        } else {
                            $mode = 'revert?';
                        }
                        break;
                    } else {
                        $this->pushc($cn);
                        $mode = 'suffix?';
                        break;
                    }
                    $cn = $this->popc();
                }
                break;
            } else if (isset(self::$DIGITS[$c->c]) || (strpos(self::$ALPHA, $c->c) !== false)) {
                $chars[] = $c;
            } else if ($startswithalpha) {
                // So is it a valid char to continue as id?
                if (
                    $c->c === '%' || preg_match(self::$LETTER, $c->c) === 1 ||
                    (($this->options->extraletters !== false && preg_match($this->options->extraletters, $c->c) === 1))
                ) {
                    $chars[] = $c;
                    $mode = 'continue-as-id';
                    break;
                } else {
                    // Was not valid for that we break here.
                    $this->pushc($c);
                }
                break;
            } else {
                $this->pushc($c);
                $mode = 'revert?';
                break;
            }
            $c = $this->popc();
        }
        if ($mode === 'feed') {
            $mode = 'revert?';
        }

        $test = $token->value;
        foreach ($chars as $c) {
            $test .= $c->c;
        }

        if ($mode === 'revert?' || $mode === 'suffix?') {
            // Does it follow any of the patterns?
            if (preg_match('/^[0-9a-zA-Z]+_[1-9][0-9]*$/', $test) === 1) {
                $mode = 'suffix';
            } else if (preg_match('/^0[xb0-6][0-9a-zA-Z]+$/', $test) === 1) {
                $mode = 'C';
            } else if (preg_match('/^[0-9]+$/', $test) === 1) {
                $mode = 'maybeint';
            } else if ($startswithalpha) {
                $mode = 'continue-as-id';
            } else {
                $mode = 'revert';
            }
        }

        if ($mode === 'continue-as-id') {
            $token->value = $test;
            if (count($chars) > 0) {
                $token->set_end_position($chars[count($chars) - 1]);
            }
            return $this->kwidentify($this->eat_identifier($token));
        } else if ($mode === 'C' || $mode === 'suffix' || $mode === 'maybeint') {
            $token->value = $test;
            if (count($chars) > 0) {
                $token->set_end_position($chars[count($chars) - 1]);
            }
            $token->type = StackMaximaTokenType::IntAtom;
            return $token;
        } else {
            // Revert. So it did not start with alpha or 0 and it is not
            // NNA_suffix then we assume NNA -> NN A
            while (count($chars) > 0 && isset(self::$DIGITS[$chars[0]->c])) {
                $c = array_shift($chars);
                $token->value .= $c->c;
                $token->set_end_position($c);
            }
            $chars = array_reverse($chars);
            foreach ($chars as $c) {
                $this->pushc($c);
            }
            $token->type = StackMaximaTokenType::IntAtom;
            return $token;
        }
    }
}
