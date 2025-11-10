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

require_once('parser.common.classes.php');
require_once('parser.options.class.php');

/**
 * A configurable handler for parser exceptions. Intended to adjust
 * the error messages for student/author use and to keep track of
 * localised syntax. 
 */
class stack_parser_error_interpreter {
    /**
     * Whether to list logic-flow items in expected input listings.
     * 
     * Students don't need to see those they are a bit too much extra. 
     */
    public bool $logicflow = false;

    /**
     * Whether to add position related details.
     * 
     * Students don't need to see those and for students the positioning
     * would be off due to preparsing. 
     */
    public bool $positiondata = false;

    /**
     * Localised syntax and other options the parser might have.
     */
    public stack_parser_options $parseroptions;

    public function __construct(stack_parser_options $options) {
        $this->parseroptions = $options;
    }

    /**
     * Takes a parser exception and tries to make it sensible for the end user.
     * 
     * If an exception is seen then the parsing has failed, but why?
     */
    public function interprete(stack_maxima_parser_exception $exception, array &$errors = [], array &$answernote = []): string {
        $groupped = $this->group_expectations($exception->expected);

        // Lexer errors
        if ($exception->received !== null && $exception->received->type === StackMaximaTokenType::Error) {
            switch ($exception->received->value) {
                case 'STRING NOT TERMINATED':
                    $answernote[] = 'OpenString';
                    $errors[] = stack_string('stackCas_OpenString');
                    break;
                case 'COMMENT NOT TERMINATED':
                    if ($this->logicflow) {
                        $answernote[] = 'OpenComment';
                        $errors[] = stack_string('stackCas_OpenComment');
                    } else {
                        $a = ['cmd' => stack_maxima_format_casstring('/*')];
                        $errors[] = stack_string('stackCas_spuriousop', $a);
                        $answernote[] = 'spuriousop';
                    }
                    break;
                case 'LEXER LEVEL FORBIDDEN TOKEN':
                case 'UNEXPECTED CHARACTER':
                    switch ($exception->received->note) {
                        case '\\':
                            $errors[] = stack_string('illegalcaschars');
                            $answernote[] = 'illegalcaschars';
                            break;
                        case '&':
                            $a = ['cmd' => stack_maxima_format_casstring('&')];
                            $errors[] = stack_string('stackCas_spuriousop', $a);
                            $answernote[] = 'spuriousop';
                            break;
                        case '%':
                        case '%%':
                            $a = ['cmd' => stack_maxima_format_casstring($exception->received->note)];
                            $errors[] = stack_string('stackCas_spuriousop', $a);
                            $answernote[] = 'spuriousop';
                            break;
                        default:
                            $answernote[] = 'forbiddenChar_parserError';
                            $errors[] = stack_string('stackCas_forbiddenChar', ['char' => $exception->received->note]);
                            break;
                    }
                    break;
            }
        }

        // Unexpected stop?
        if ($exception->received === null && array_search('END_OF_INPUT', $groupped) === false) {
            // Was not expecting the input to end.
            // Check various cases and leave room for a general one.
            $explained = false;

            // Check if we have open parenthesis in the partial stack?
            // Note that any previously matched parens in the stack have
            // already been closed to MP-class objects.
            foreach (array_reverse($exception->partial) as $item) {
                if ($item instanceof stack_maxima_lexer_token &&
                    $item->type === StackMaximaTokenType::Symbol) {
                    switch ($item->value) {
                        case '(':
                            $answernote[] = 'missingRightBracket';
                            $errors[] = stack_string('stackCas_missingRightBracket',
                            ['bracket' => ')', 'cmd' => stack_maxima_format_casstring($exception->original)]);
                            $explained = true;
                            break;
                        case '[':
                            $answernote[] = 'missingRightBracket';
                            $errors[] = stack_string('stackCas_missingRightBracket',
                            ['bracket' => ']', 'cmd' => stack_maxima_format_casstring($exception->original)]);
                            $explained = true;
                            break;
                        case '{':
                            $answernote[] = 'missingRightBracket';
                            $errors[] = stack_string('stackCas_missingRightBracket',
                            ['bracket' => '}', 'cmd' => stack_maxima_format_casstring($exception->original)]);
                            $explained = true;
                            break;
                    }
                }
            }

            if (!$explained) {
                if (strlen($exception->previous->value) === 1) {
                    $a['char'] = $exception->previous->value;
                    $a['cmd']  = stack_maxima_format_casstring($exception->original);
                    $errors[] = stack_string('stackCas_finalChar', $a);
                    $answernote[] = 'finalChar';
                } else {
                    $a['token'] = $exception->previous->value;
                    $a['cmd']  = stack_maxima_format_casstring($exception->original);
                    $errors[] = stack_string('stackCas_finalToken', $a);
                    $answernote[] = 'finalToken';
                }
            }
        }

        // Odd pairs?
        if ($exception->previous !== null && $exception->received !== null
            && $exception->previous->type === StackMaximaTokenType::Symbol
            && $exception->received->type === StackMaximaTokenType::Symbol) {
            if ($exception->previous->value === '='
                && ($exception->received->value === '<'
                    || $exception->received->value === '>')) {
                $a = [];
                $a['cmd'] = stack_maxima_format_casstring('=' . $exception->received->value);
                $errors[] = stack_string('stackCas_backward_inequalities', $a);
                $answernote[] = 'backward_inequalities';
            } else if ($exception->previous->value === '='
                && $exception->received->value === '=') {
                $a = ['cmd' => stack_maxima_format_casstring('==')];
                $errors[] = stack_string('stackCas_spuriousop', $a);
                $answernote[] = 'spuriousop';
            } else if ($exception->previous->value === '<'
                && $exception->received->value === '>') {
                $a = ['cmd' => stack_maxima_format_casstring('<>')];
                $errors[] = stack_string('stackCas_spuriousop', $a);
                $answernote[] = 'spuriousop';
            }
        }

        // Unexpected tokens?
        if ($exception->received !== null) {
            if ($exception->received->type === StackMaximaTokenType::Symbol) {
                switch ($exception->received->value) {
                    case ')':
                    case ']':
                    case '}':
                        // Three options:
                        //  A) there is no matching opening
                        //  B) the previous unmatched opening is of different type
                        //  C) unsuitable place e.g. `(1+)`
                        $opens = [];
                        foreach (array_reverse($exception->partial) as $item) {
                            if ($item instanceof stack_maxima_lexer_token &&
                                $item->type === StackMaximaTokenType::Symbol) {
                                switch ($item->value) {
                                    case '(':
                                    case '[':
                                    case '{':
                                        $opens[] = $item->value;
                                        // Note. The closing ones will never
                                        // appear here, all grammar rules
                                        // containing them terminate to them
                                        // and thus no tokens are present in
                                        // partial results.
                                }
                            }
                        }
                        $matching = '(';
                        if ($exception->received->value === ']') {
                            $matching = '[';
                        } else if ($exception->received->value === '}') {
                            $matching = '{';
                        }
                        if (count($opens) === 0) {
                            // Pure A.
                            $answernote[] = 'missingLeftBracket';
                            $errors[] = stack_string('stackCas_missingLeftBracket',
                                ['bracket' => $matching, 'cmd' => stack_maxima_format_casstring($exception->original)]);
                        } else if (end($opens) === $matching) {
                            // This is C.
                            $answernote[] = 'prematureRightBracket';
                            $errors[] = stack_string('stackCas_prematureRightBracket',
                                ['bracket' => $matching, 'cmd' => stack_maxima_format_casstring($exception->original)]);
                        } else {
                            // This is B or A.
                            // If we don't have matching in the opens then A.
                            if (array_search($matching, $opens) === false) {
                                $answernote[] = 'missingLeftBracket';
                                $errors[] = stack_string('stackCas_missingLeftBracket',
                                    ['bracket' => $matching, 'cmd' => stack_maxima_format_casstring($exception->original)]);
                            } else {
                                $answernote[] = 'missmatchedRightBracket';
                                $errors[] = stack_string('stackCas_missmatchedRightBracket',
                                ['bracket' => $matching, 'expected' => end($opens), 'cmd' => stack_maxima_format_casstring($exception->original)]);
                            }
                        }
                        break;
                    case ':':
                        if (strpos($exception->original, ':lisp') !== false) {
                            $errors[] = stack_string('stackCas_forbiddenWord',
                    ['forbid' => stack_maxima_format_casstring('lisp')]);
                            $answernote[] = 'forbiddenWord';
                        }
                        break;
                    case '|':
                        $a = ['cmd' => stack_maxima_format_casstring('|')];
                        $errors[] = stack_string('stackCas_spuriousop', $a);
                        $answernote[] = 'spuriousop';
                        break;
                    case '!':
                        $a = ['op' => stack_maxima_format_casstring('!')];
                        $errors[] = stack_string('stackCas_badpostfixop', $a);
                        $answernote[] = 'badpostfixop';
                        break;
                }
            } else if (($exception->received->type === StackMaximaTokenType::ListSeparator) || ($exception->previous !== null && $exception->previous->type === StackMaximaTokenType::ListSeparator)) {
                if ($this->parseroptions->separators === StackLexerSeparators::Dot) {
                    $errors[] = stack_string('stackCas_unencpsulated_comma');
                } else {
                    $errors[] = stack_string('stackCas_unencpsulated_semicolon');
                }
                $answernote[] = 'unencapsulated_comma';
            }

        }

        // The final token is a problem?
        if ($exception->received !== null && count($errors) === 0
            && strlen(rtrim($exception->original)) === $exception->received->endchar) {
            if ($exception->received->type === StackMaximaTokenType::Symbol) {
                if (strlen($exception->received->value) === 1) {
                    $a['char'] = $exception->received->value;
                    $a['cmd']  = stack_maxima_format_casstring($exception->original);
                    $errors[] = stack_string('stackCas_finalChar', $a);
                    $answernote[] = 'finalChar';
                } else {
                    $a['token'] = $exception->received->value;
                    $a['cmd']  = stack_maxima_format_casstring($exception->original);
                    $errors[] = stack_string('stackCas_finalToken', $a);
                    $answernote[] = 'finalToken';
                }
            }
        }


        if ($exception->getMessage() === 'No action available' && count($errors) === 0) {
            // General unexpected input token.
            $errors[] = 'Expected "' . implode('", "', $groupped) . '", received "' . $exception->received . '".';
            $answernote[] = 'ParseError';
        }

        $err = implode(', ', $errors);

        if ($this->positiondata && ($exception->received !== null || $exception->previous !== null)) {
            $line = null;
            $col = null;
            if ($exception->received !== null) {
                $line = $exception->received->startline;
                $col = $exception->received->startcolumn;
            } else {
                $line = $exception->previous->endline;
                $col = $exception->previous->endcolumn;
            }
            $err .= ' (' . stack_string('stackCas_errorpos',
                            ['line' => $line, 'col' => $col]) . ')';
        }

        return $err;
    }

    /**
     * Combines the expected tokens list to simpler categories.
     */
    public function group_expectations(?array $expected): array {
        if ($expected === null) {
            return [];
        }
        // First literals or atoms.
        $not_atom_pred = function ($tok): bool {
            return !match ($tok) {
                'ID' => true,
                'BOOL' => true,
                'STRING' => true,
                'INT' => true,
                'FLOAT' => true,
                default => false
            };
        };
        $workb = $expected;
        $worka = array_filter($workb, $not_atom_pred);
        if (count($worka) !== count($workb)) {
            $worka[] = 'ATOM';
            $workb = $worka;
        }

        // Prefix operators
        $not_prefix_op = function ($tok): bool {
            return !match ($tok) {
                '? ' => true,
                '?? ' => true,
                '?' => true,
                "'" => true,
                "''" => true,
                '-' => true,
                '+' => true,
                '+-' => true,
                '#pm#' => true,
                'STACKpmOPT' => true,
                'not' => true,
                'not ' => true,
                'nounnot' => true,
                'nounnot ' => true,
                '%not' => true,
                'UNARY_RECIP' => true,
                default => false
            };
        };
        $worka = array_filter($workb, $not_prefix_op);
        if (count($worka) !== count($workb)) {
            $worka[] = 'PREFIX_OP';
            $workb = $worka;
        }

        // Prefix operators
        $not_infix_op = function ($tok): bool {
            return !match ($tok) {
                '^' => true,
                '^^' => true,
                'nounpow' => true,
                '.' => true,
                '*' => true,
                '**' => true,
                'nounmul' => true,
                'blankmult' => true,
                '/' => true,
                'noundiv' => true,
                '-' => true,
                'nounsub' => true,
                'nounadd' => true,
                '+' => true,
                '+-' => true,
                '#pm#' => true,
                'STACKpmOPT' => true,
                'or' => true,
                '%or' => true,
                'nounor' => true,
                'and' => true,
                '%and' => true,
                'nounand' => true,
                '@@Is@@' => true,
                '@@IS@@' => true,
                // The following 12 are parser level synthetic tokens
                // they are not emitted by the lexer and only exist to
                // deal with a particular precedence problem.
                ' ^-' => true,
                ' ^+' => true,
                ' ^+-' => true,
                ' ^#pm#' => true,
                ' ^^-' => true,
                ' ^^+' => true,
                ' ^^+-' => true,
                ' ^^#pm#' => true,
                ' **-' => true,
                ' **+' => true,
                ' **+-' => true,
                ' **#pm#' => true,
                default => false
            };
        };
        $worka = array_filter($workb, $not_infix_op);
        if (count($worka) !== count($workb)) {
            $worka[] = 'INFIX_OP';
            $workb = $worka;
        }

        // Relation operators
        $not_relation_op = function ($tok): bool {
            return !match ($tok) {
                '=' => true,
                'nouneq' => true,
                ':' => true,
                ':=' => true,
                '::=' => true,
                '::' => true,
                '~' => true,
                '#' => true,
                '<' => true,
                '<=' => true,
                '>' => true,
                '>=' => true,
                '%or' => true,
                'nounor' => true,
                'and' => true,
                '%and' => true,
                'nounand' => true,
                default => false
            };
        };
        $worka = array_filter($workb, $not_relation_op);
        if (count($worka) !== count($workb)) {
            $worka[] = 'RELATION_OP';
            $workb = $worka;
        }

        // Suffix operators
        $not_suffix_op = function ($tok): bool {
            return !match ($tok) {
                '!' => true,
                '!!' => true,
                default => false
            };
        };
        $worka = array_filter($workb, $not_suffix_op);
        if (count($worka) !== count($workb)) {
            $worka[] = 'SUFFIX_OP';
            $workb = $worka;
        }

        // Program flow
        $not_program_flow = function ($tok): bool {
            return !match ($tok) {
                'if' => true,
                'next' => true,
                'in' => true,
                'then' => true,
                'thru' => true,
                'from' => true,
                'unless' => true,
                'step' => true,
                'elseif' => true,
                'for' => true,
                'do' => true,
                'else' => true,
                'while' => true,
                default => false
            };
        };
        $worka = array_filter($workb, $not_program_flow);
        if (count($worka) !== count($workb)) {
            if ($this->logicflow) {
                $worka[] = 'PROGRAM_FLOW';
            }
            $workb = $worka;
        }

        // End of stream.
        $not_end_of_stream = function ($tok): bool {
            return !match ($tok) {
                "END OF FILE" => true,
                default => false
            };
        };
        $worka = array_filter($workb, $not_end_of_stream);
        if (count($worka) !== count($workb)) {
            if ($this->logicflow) {
                $worka[] = 'END_OF_INPUT';
            }
            $workb = $worka;
        }

        sort($worka);
        return array_values($worka);
    }
}