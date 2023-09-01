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

require_once(__DIR__ . '/../parser.options.class.php');
require_once(__DIR__ . '/../lexer.base.php');


/**
 * This is a parametric lexer which allows tuning of various separators.
 * This version does not include support for base-n nor for lisp-ids.
 */
class stack_maxima_lexer_localised extends stack_maxima_base_lexer {
	private $decimalsep = '.';
	private $decimalgroupings = [];
	private $listsep = ',';
	private $statementseps = [];


    public function __construct(string $src, stack_parser_options $options) {
        parent::__construct($src, $options);
        
        // Simplify the accesing of these.
        $this->decimalsep = $options->decimalseparator;
        $this->listsep = $options->listseparator;
        $this->statementseps = [];
        $this->decimalgroupings = [];

        foreach ($options->statementendtokens as $sep) {
        	$this->statementseps[$sep] = $sep;
        }
        // Note that this might actually contain the list sep or even the matrix 
        // multiplication op.
		foreach ($options->decimalgroupping as $sep) {
        	$this->decimalgroupings[$sep] = $sep;
        }

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
                // The operator in its various forms. For this lexer we do not support those ids.
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

        // Some special cases.
        if ($this->listsep === $c1) {
        	$token->type = 'LIST_SEP';
            return $token;
        } else if (isset($this->statementseps[$c1])) {
        	$token->type = 'END_TOKEN';
            return $token;
        }



        // Then the more complex things.

        // First numbers.
        if ($c1 === $this->decimalsep || isset(self::$DIGITS[$c1])) {
            // For now don't unicode translate within numbers.
            $c2 = $this->popc(false);
            $content = $c1;
            if ($c1 === $this->decimalsep) {
            	// Normalise the number.
            	$content = '.';
            }

            $numbermode = 'pre-dot';

            // If the decimalsep is '.' it might also be an op.
            if ($c1 === '.' && !isset(self::$DIGITS[$c2])) {
                // The dot was just a dot.
                $this->pushc($c2);
                return $token; // Token type still symbol and $c1 the value.
            } else if ($c1 === $this->decimalsep) {
                // We continue with digits.
                $numbermode = 'post-dot';
                $content .= $c2;
                $token->length = $token->length + 1;
            } else if (isset(self::$DIGITS[$c2])) {
                // We continue with digits.
                $content .= $c2;
                $token->length = $token->length + 1;
            } else {
                // Digit followed by something else.
                switch ($c2) {
                    case $this->decimalsep:
                        $numbermode = 'post-dot';
                        $content .= '.';
                        $token->length = $token->length + 1;
                        break;
                    case 'e':
                    case 'E':
                        $c3 = $this->popc(false);
                        if ($c3 === '-' || $c3 === '+') {
                            $c4 = $this->popc(false);
                            if (isset(self::$DIGITS[$c4])) {
                                $numbermode = 'exponent';
                                $content .= $c2 . $c3 . $c4;
                                $token->length = $token->length + 3;
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
                                return $token;    
                            }
                        } else if (isset(self::$DIGITS[$c3])) {
                            $numbermode = 'exponent';
                            $content .= $c2 . $c3;
                            $token->length = $token->length + 2;
                        } else {
                            // The case of a single digit integer. No sensible exponent.
                            $this->pushc($c3);
                            $this->pushc($c2);
                            $token->type = 'INT';
                            return $token;    
                        }
                        break;
                    default:
                    	$c3 = $this->popc();
                    	if (isset($this->decimalgroupings[$c2]) && isset(self::$DIGITS[$c3])) {
                    		// Decimal grouping char. Between two digits.
                    		$content .= $c3;
                    		$token->length = $token->length + 2;
                    	} else {
	                    	// The case of a single digit integer.
	                    	$this->pushc($c3);
	                        $this->pushc($c2);
	                        $token->type = 'INT';
	                        return $token;	
                    	}
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
                        return $token;
                    } else {
                        // Starting exponent needs to be a digit or + or -.
                        $c3 = $this->popc(false);
                        if (isset(self::$DIGITS[$c3])) {
                            $numbermode = 'exponent';
                            $content .= $c . $c3;
                            $token->length = $token->length + 2;
                        } else if ($c3 === '-' || $c3 === '+') {
                            $c4 = $this->popc();
                            if (isset(self::$DIGITS[$c4])) {
                                $numbermode = 'exponent';
                                $content .= $c . $c3 . $c4;
                                $token->length = $token->length + 3;
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
                                return $token;    
                            }
                        } else {
                            // Not a valid start for exponent. Return what we have.
                            $this->pushc($c3);
                            $this->pushc($c);
                            $token->value = $content;
                            if (mb_strpos($content, '.') !== false) {
                                $token->type = 'FLOAT';
                            } else {
                                $token->type = 'INT';
                            }
                            return $token;
                        }
                    }
                } else if ($c === $this->decimalsep) {
                    if ($numbermode === 'pre-dot') {
                        $content .= '.';
                        $token->length = $token->length + 1;
                        $numbermode = 'post-dot';
                    } else {
                        // Can't have more stop here.
                        $this->pushc($c);
                        $token->value = $content;
                        $token->type = 'FLOAT';
                        return $token;
                    }
                } else if (isset($this->decimalgroupings[$c])) {
                	$c2 = $this->popc(false);
                	if (isset(self::$DIGITS[$c2])) {
                		// It was a valid grouping digit.
                		$content .= $c2;
                		$token->length = $token->length + 2;
                	} else {
                		$this->pushc($c2);
                		$this->pushc($c);
	                    $token->value = $content;
	                    if ($numbermode === 'pre-dot') {
	                        $token->type = 'INT';
	                    } else {
	                        $token->type = 'FLOAT';
	                    }
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

        // If not a decimal separator might get throught to this.
        if ($c1 === '.') {
        	// Still symbol from the start.
        	return $token;
        }

        // No idea what that was?
        $token->type = 'ERROR';
        $token->note = $token->value;
        $token->value = 'Unexpected character.';
        return $token;
    }



}