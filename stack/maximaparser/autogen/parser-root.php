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

////////////////////////////////////////////////////////////////////
// THIS FILE HAS BEEN GENERATED, DO NOT EDIT, EDIT THE GENERATOR. //
////////////////////////////////////////////////////////////////////
/*
 @copyright  2025 Matti Harjula, Aalto University.
 @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
*/

require_once(__DIR__ . '/../MP_classes.php');
require_once(__DIR__ . '/../lexer.base.class.php');
require_once(__DIR__ . '/../parser.common.classes.php');
require_once(__DIR__ . '/../parser.options.class.php');


class stack_maxima_parser2_root {
    private stack_maxima_parser_table_holder $tables;
    private stack_parser_options $options;

    // The selection of the correct reduce logic could be done
    // with a switch or a match, but then one would need to 
    // actually check for the value. Instead, we use an array:
    //  - Here rule-number maps to a tuple of number of items
    //    to extract from the stack and a function to give those to
    private static $reducemap = [
				[1,0],
				[0,1],
				[1,2],
				[3,3],
				[4,4],
				[0,5],
				[1,6],
				[3,7],
				[5,8],
				[5,8],
				[0,5],
				[3,11],
				[3,12],
				[3,13],
				[2,14],
				[0,5],
				[3,16],
				[0,5],
				[1,0],
				[1,19],
				[1,20],
				[1,21],
				[1,0],
				[1,23],
				[1,24],
				[2,25],
				[1,0],
				[1,0],
				[1,0],
				[2,29],
				[2,14],
				[0,5],
				[2,14],
				[1,0],
				[1,0],
				[1,0],
				[5,36],
				[2,37],
				[5,38],
				[0,39],
				[3,40],
				[2,14],
				[0,5],
				[2,43],
				[2,43],
				[2,43],
				[2,43],
				[2,43],
				[2,43],
				[2,43],
				[2,43],
				[1,0],
				[1,0],
				[1,0],
				[1,0],
				[1,0],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,56],
				[2,68],
				[2,68],
				[3,70],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,102],
				[2,56],
				[2,56],
				[3,71],
				[3,71],
				[3,71],
				[3,71],
				[3,109],
				[3,110],
				[2,56],
				[3,71],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,113],
				[3,71],
				[3,71],
				[3,71],
				[3,71]
			];

    public function __construct(stack_parser_options $options) {
        $this->options = $options;
        $this->tables = stack_maxima_parser_table_holder::get_for_grammar('lalr-root.json');
    }

    /**
     * Attempts to parse whatever is left in the lexer. 
     * 
     * Should insertions be enabled and lead to notes being generated
     * will add them to the given array.
     * 
     * May throw a stack_maxima_parser_exception
     */
    public function parse(stack_maxima_lexer_base $lexer, array &$notes = []): ?MP_Node {
        // Collect comments here, for injection to statement-lists.
        // Should the we collect them at all depends on the options.
        $commentdump = [];
        $collectcomments = !$this->options->dropcomments;

        // Insertion of extra tokens might care if we have seen whitespace.
        $whitespaceseen = false;

        // Track previous token.
        $previous = null;

        // The parser stack starts from state 0, i.e., the "Start" rule.
        $stack = [0];
        $shifted = true; // Starting without a token.
        $t = null; // The raw token.
        $T = null; // The symbolic token. e.g. NUM.

        // For KEYWORD -> ID remapping we need to have revert capability.
        // Basically, if someone uses a keyword like `in` or `%not` in
        // variable role we do as follows:
        //  - If an action would exist at that place for an ID.
        //  - We clone the current stack and save it
        //  - We also clone that token.
        //  - If the next token finds an action all is fine
        //    and we clean these.
        //  - If not then we revert and consider that token as an ID.
        $kreverttoken = null;
        $krevertstack = null;
        $krevertreset = 0;

        while (true) {
            if ($shifted) {
                $previous = $t;
                $t = $lexer->get_next_token();
                while ($t !== null && ($t->type === StackMaximaTokenType::WhiteSpace || $t->type === StackMaximaTokenType::Comment)) {
                    if ($t->type === StackMaximaTokenType::WhiteSpace) {
                        $whitespaceseen = true;
                    } else if ($collectcomments && $t->type == StackMaximaTokenType::Comment) {
                        $c = new MP_Comment($t->value, []);
                        $c->set_position_from_parser_token($t);
                        $commentdump[] = $c;
                    }
                    $t = $lexer->get_next_token();
                }
                if ($t === null) {
                    // End of stream.
                    $T = "END OF FILE";
                } else {
                    switch ($t->type) {
                        case StackMaximaTokenType::Symbol:
                            if ($t->value === '@@Is@@' && array_search('fixspaces', $notes) === false) {
                                // Some specific notation of the pre-parser side.
                                if (array_search('spaces', $notes) === false) {
                                    $notes[] = 'spaces';
                                }
                            } else if ($t->value === '@@IS@@' && array_search('insertstars', $notes) === false) {
                                if (array_search('missing_stars', $notes) === false) {
                                    $notes[] = 'missing_stars';
                                }
                            } else if ($t->value === '^' || $t->value === '^^' || $t->value === '**') {
                                // Some operator precendence cases are difficult.
                                $next = $lexer->get_next_token();
                                $lookahead = [];
                                while ($next !== null && ($next->type === StackMaximaTokenType::WhiteSpace || $next->type === StackMaximaTokenType::Comment)) {
                                    if ($next->type === StackMaximaTokenType::Comment) {
                                        if ($collectcomments) {
                                            $c = new MP_Comment($t->value, []);
                                            $c->set_position_from_parser_token($t);
                                            $commentdump[] = $c;
                                        }
                                    } else {
                                        $lookahead[] = $next;
                                    }
                                    $next = $lexer->get_next_token();
                                }
                                if ($next !== null && $next->type === StackMaximaTokenType::Symbol && ($next->value === '-' || $next->value === '+' || $next->value === '+-' || $next->value === '#pm#')) {
                                    $T = ' ' . $t->value . $next->value;
                                    $t = $t->merge($next);
                                    break;
                                } else {
                                    if ($next !== null) {
                                        $lexer->return_token($next);
                                    }
                                    while (count($lookahead) > 0) {
                                        $lexer->return_token(array_pop($lookahead));
                                    }
                                }
                            }
                        case StackMaximaTokenType::Keyword:
                            $T = $t->value;
                            break;
                        case StackMaximaTokenType::IdAtom:
                            $T = 'ID';
                            break;
                        case StackMaximaTokenType::IntAtom:
                            $T = 'INT';
                            break;
                        case StackMaximaTokenType::FloatAtom:
                            $T = 'FLOAT';
                            break;
                        case StackMaximaTokenType::BoolAtom:
                            $T = 'BOOL';
                            break;
                        case StackMaximaTokenType::StringAtom:
                            $T = 'STRING';
                            break;
                        case StackMaximaTokenType::ListSeparator:
                            $T = 'LIST SEP';
                            break;
                        case StackMaximaTokenType::EndToken:
                            $T = 'END TOKEN';
                            break;
                        case StackMaximaTokenType::LispIdentifier:
                            $T = 'LISP ID';
                            break;
                        case StackMaximaTokenType::Error:
                        default:
                            throw new stack_maxima_parser_exception(
                                'Lexer side error',
                                [],
                                $t,
                                $lexer->original,
                                $previous,
                                array_filter($stack, 'stack_maxima_parser_exception_partial_filter')
                            );
                    }
                }
                $shifted = false;
            }

            // Not checking if the top of the stack is a state number
            // it simply must be.
            $currentstate = $stack[count($stack) - 1];

            $action = $this->tables->get_action($currentstate, $T);

            // The revert case for KEYWORD -> ID.
            if ($action !== null && $t !== null && $t->type === StackMaximaTokenType::Keyword) {
                if ($this->tables->get_action($currentstate, 'ID') !== null) {
                    $kreverttoken = $t;
                    $krevertstack = array_merge([],$stack);
                    $krevertreset = 3;
                }
            }

            if ($krevertreset == 1) {
                // We cannot revert too far back. Currently only one step.
                $krevertstack = null;
                $kreverttoken = null;
            }

            if ($action === null) {
                if ($this->options->tryinsert === StackParserInsertionOption::Stars) {
                    if ($this->tables->get_action($currentstate, '*') !== null) {
                        $nt = new stack_maxima_lexer_token(new stack_maxima_lexer_char('*', $t->startline, $t->startcolumn, $t->startchar));
                        $nt->type = StackMaximaTokenType::Symbol;
                        if ($whitespaceseen) {
                            $nt->note = 'inserted with whitespace';
                            if (array_search('spaces', $notes) === false) {
                                $notes[] = 'spaces';
                            }
                        } else {
                            $nt->note = 'inserted without whitespace';
                            if (array_search('missing_stars', $notes) === false) {
                                $notes[] = 'missing_stars';
                            }
                        }
                        $lexer->return_token($t);
                        $t = $nt;
                        $T = '*';
                        $action = $this->tables->get_action($currentstate, $T);
                    }
                } else if ($this->options->tryinsert === StackParserInsertionOption::EndToken) {
                    if ($this->tables->get_action($currentstate, 'END TOKEN') !== null) {
                        $nt = new stack_maxima_lexer_token(new stack_maxima_lexer_char(';', $t->startline, $t->startcolumn, $t->startchar));
                        $nt->type = StackMaximaTokenType::EndToken;
                        $lexer->return_token($t);
                        $t = $nt;
                        $T = 'END TOKEN';
                        $action = $this->tables->get_action($currentstate, $T);
                    }
                }
            }

            // The KEYWORD -> ID case.
            if ($action === null && $kreverttoken !== null) {
                // Go back one step.
                if ($t !== null) {
                    $lexer->return_token($t);
                }
                $t = $kreverttoken;
                $t->type = StackMaximaTokenType::IdAtom;
                $T = 'ID';
                $stack = $krevertstack;
                $currentstate = $stack[count($stack) - 1];
                $action = $this->tables->get_action($currentstate, $T);
                $kreverttoken = null;
                $krevertstack = [];
            }

            if ($action === null) {
                throw new stack_maxima_parser_exception(
                    'No action available',
                    $this->tables->get_expected($currentstate),
                    $t,
                    $lexer->original,
                    $previous,
                    array_filter($stack, 'stack_maxima_parser_exception_partial_filter')
                );
            }
            $krevertreset = $krevertreset - 1;

            if (count($action) === 1) {
                // A shift.
                $stack[] = $t;
                $stack[] = $action[0];
                $shifted = true;
            } else {
                // Reduce.
                [$rule, $nt_name, $nt_id] = $action;

                // Logic.
                [$numargs, $funnum] = self::$reducemap[$rule];
                
                $args = [];
                while ($numargs > 0) {
                    $numargs--;
                    array_pop($stack); // Drop a state number.
                    $args[] = array_pop($stack);
                }
                // Now we could reverse this array, or we could simply write
                // the function arguments in a different order, we choose the latter.
                $reduced = call_user_func_array([$this, 'r'.$funnum], $args);

                // If we just reduced the start rule then that is it.
                if ($nt_name === 'Start') {
                    if (count($commentdump) > 0 && $reduced instanceof MP_Root) {
                        $interleaved = [];
                        $commenttoassign = array_shift($commentdump);
                        foreach ($reduced->items as $item) {
                            while ($commenttoassign !== null &&
                                $commenttoassign->position['start'] < $item->position['start']) {
                                $interleaved[] = $commenttoassign;
                                $commenttoassign = array_shift($commentdump);
                            }
                            while ($commenttoassign !== null &&
                                $commenttoassign->position['start'] < $item->position['end']) {
                                $item->internalcomments[] = $commenttoassign;
                                $commenttoassign = array_shift($commentdump);
                            }
                            $interleaved[] = $item;
                        } 
                        foreach ($commentdump as $commenttoassign) {
                            $interleaved[] = $commenttoassign;
                        }
                        $reduced->items = $interleaved;
                    }

                    // To work with previous logic, return null...
                    if ($reduced instanceof MP_Root && count($reduced->items) === 0) {
                        return(null);
                    }

                    return $reduced;
                }

                // Otherwise
                $topstate = $stack[count($stack) - 1];
                $stack[] = $reduced;
                $next = $this->tables->get_goto($topstate, $nt_id);
                if ($next === null) {
                    throw new stack_maxima_parser_exception(
                        "No goto for state $topstate nonterminal $nt_id = $nt_name",
                        [],
                        $t,
                        $lexer->original,
                        $previous,
                        array_filter($stack, 'stack_maxima_parser_exception_partial_filter')
                    );
                } else {
                    $stack[] = $next;
                }

                // After reduce the last whitespace was inside something.
                $whitespaceseen = false;
            }
        }
    }

    /**
	 * Reduce logic for rules:
	 * 0: Start ->   Root
	 * 18: Statement ->   TopOp
	 * 22: Term ->   CallOrIndex?
	 * 26: IndexableOrCallable ->   List
	 * 27: IndexableOrCallable ->   Set
	 * 28: IndexableOrCallable ->   Group
	 * 33: Term ->   Flow
	 * 34: Flow ->   IfBase
	 * 35: Flow ->   Loop
	 * 51: TopOp ->   OpInfix
	 * 52: TopOp ->   OpSuffix
	 * 53: TopOp ->   OpPrefix
	 * 54: TopOp ->   Term
	 * 55: TopOp ->   Abs
	 */
	private function r0($term0) {
		$term = $term0;
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 1: Root ->   END OF FILE
	 */
	private function r1() {
		$term = new MP_Root([]);
		$term->position = ['start'=>1,'end'=>1,'start-column'=>1,'start-line'=>1,'end-column'=>1,'end-line'=>1];
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 2: Root ->   StatementList
	 */
	private function r2($term0) {
		$term = new MP_Root($term0);
		$term->set_position_from_nodes($term0[0], $term0[count($term0) - 1]);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 3: StatementList ->   Statement  EvalFlags  StatementListN
	 */
	private function r3($term2, $term1, $term0) {
		$term = array_merge([new MP_Statement($term0, $term1)], $term2);
		if (count($term1) > 0) {
			$term[0]->set_position_from_nodes($term0, $term1[count($term1)-1]);
		} else {
			$term[0]->set_position_from_node($term0);
		}
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 4: StatementListN ->   END TOKEN  Statement  EvalFlags  StatementListN
	 */
	private function r4($term3, $term2, $term1, $term0) {
		$term = array_merge([new MP_Statement($term1, $term2)], $term3);
		if (count($term2) > 0) {
			$term[0]->set_position_from_nodes($term1, $term2[count($term2)-1]);
		} else {
			$term[0]->set_position_from_node($term1);
		}
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 5: StatementListN ->   END OF FILE
	 * 10: EvalFlags ->   END OF FILE
	 * 15: StatementNullList ->   END OF FILE
	 * 17: TermList ->   END OF FILE
	 * 31: ListsOrGroups ->   END OF FILE
	 * 42: LoopBits ->   END OF FILE
	 */
	private function r5() {
		$term = [];
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 6: StatementListN ->   END TOKEN
	 */
	private function r6($term0) {
		$term = [];
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 7: EvalFlags ->   LIST SEP  ID  EvalFlags
	 */
	private function r7($term2, $term1, $term0) {
		$term = array_merge([new MP_EvaluationFlag(new MP_Identifier($term1->value), new MP_Boolean(true))], $term2);
		$term[0]->name->set_position_from_parser_token($term1);
		$term[0]->set_position_from_parser_token($term1);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 8: EvalFlags ->   LIST SEP  ID  :  Statement  EvalFlags
	 * 9: EvalFlags ->   LIST SEP  ID  =  Statement  EvalFlags
	 */
	private function r8($term4, $term3, $term2, $term1, $term0) {
		$term = array_merge([new MP_EvaluationFlag(new MP_Identifier($term1->value), $term3)], $term4);
		$term[0]->name->set_position_from_parser_token($term1);
		$term[0]->set_position_from_nodes($term[0]->name, $term3);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 11: List ->   [  StatementNullList  ]
	 */
	private function r11($term2, $term1, $term0) {
		$term = new MP_List($term1);
		$term->set_position_from_parser_tokens($term0, $term2);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 12: Set ->   {  StatementNullList  }
	 */
	private function r12($term2, $term1, $term0) {
		$term = new MP_Set($term1);
		$term->set_position_from_parser_tokens($term0, $term2);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 13: Group ->   (  StatementNullList  )
	 */
	private function r13($term2, $term1, $term0) {
		$term = new MP_Group($term1);
		$term->set_position_from_parser_tokens($term0, $term2);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 14: StatementNullList ->   Statement  TermList
	 * 30: ListsOrGroups ->   List  ListsOrGroups
	 * 32: ListsOrGroups ->   Group  ListsOrGroups
	 * 41: LoopBits ->   LoopBit  LoopBits
	 */
	private function r14($term1, $term0) {
		$term = array_merge([$term0], $term1);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 16: TermList ->   LIST SEP  Statement  TermList
	 */
	private function r16($term2, $term1, $term0) {
		$term = array_merge([$term1], $term2);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 19: Term ->   BOOL
	 */
	private function r19($term0) {
		$term = new MP_Boolean($term0->value === 'true');
		$term->set_position_from_parser_token($term0);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 20: Term ->   INT
	 */
	private function r20($term0) {
		$term = new MP_Integer($term0->value, $term0->value);
		$term->set_position_from_parser_token($term0);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 21: Term ->   FLOAT
	 */
	private function r21($term0) {
		$term = new MP_Float($term0->value, $term0->value);
		$term->set_position_from_parser_token($term0);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 23: IndexableOrCallable ->   STRING
	 */
	private function r23($term0) {
		$term = new MP_String($term0->value);
		$term->set_position_from_parser_token($term0);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 24: IndexableOrCallable ->   ID
	 */
	private function r24($term0) {
		$term = new MP_Identifier($term0->value);
		$term->set_position_from_parser_token($term0);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 25: IndexableOrCallable ->   ?  LISP ID
	 */
	private function r25($term1, $term0) {
		$term = new MP_Identifier($term0->value . $term1->value);
		$term->set_position_from_parser_tokens($term0, $term1);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 29: CallOrIndex? ->   IndexableOrCallable  ListsOrGroups
	 */
	private function r29($term1, $term0) {
		$term = $term0;
		while (count($term1) > 0) {
			$item = array_shift($term1);
			if ($item instanceof MP_List) {
				$term = new MP_Indexing($term, [$item]);
			} else if ($item instanceof MP_Group) {
				$term = new MP_FunctionCall($term, $item->items);
			}
			$term->set_position_from_nodes($term0, $item);
		}
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 36: IfBase ->   if  Statement  then  Statement  IfTail
	 */
	private function r36($term4, $term3, $term2, $term1, $term0) {
		$term = new MP_If(array_merge([$term1], $term4[0]), array_merge([$term3], $term4[1]));
		$endposition = count($term->branches) > 0 ? $term->branches[count($term->branches)-1] : $term3;
		$term->set_position_from_token_node($term0, $endposition);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 37: IfTail ->   else  Statement
	 */
	private function r37($term1, $term0) {
		$term = [[],[$term1]];
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 38: IfTail ->   elseif  Statement  then  Statement  IfTail
	 */
	private function r38($term4, $term3, $term2, $term1, $term0) {
		$term = [array_merge([$term1], $term4[0]), array_merge([$term3], $term4[1])];
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 39: IfTail ->   END OF FILE
	 */
	private function r39() {
		$term = [[],[]];
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 40: Loop ->   LoopBits  do  Statement
	 */
	private function r40($term2, $term1, $term0) {
		$term = new MP_Loop($term2, $term0);
		if (count($term0) > 0) {
			$term->set_position_from_nodes($term0[0], $term2);
		} else {
			$term->set_position_from_token_node($term1, $term2);
		}
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 43: LoopBit ->   for  Statement
	 * 44: LoopBit ->   from  Statement
	 * 45: LoopBit ->   step  Statement
	 * 46: LoopBit ->   next  Statement
	 * 47: LoopBit ->   in  Statement
	 * 48: LoopBit ->   thru  Statement
	 * 49: LoopBit ->   while  Statement
	 * 50: LoopBit ->   unless  Statement
	 */
	private function r43($term1, $term0) {
		$term = new MP_LoopBit($term0->value, $term1);
		$term->set_position_from_token_node($term0, $term1);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 56: OpPrefix ->   -  TopOp
	 * 57: OpPrefix ->   +  TopOp
	 * 58: OpPrefix ->   +-  TopOp
	 * 59: OpPrefix ->   #pm#  TopOp
	 * 60: OpPrefix ->   ''  TopOp
	 * 61: OpPrefix ->   '  TopOp
	 * 62: OpPrefix ->   not  TopOp
	 * 63: OpPrefix ->   not   TopOp
	 * 64: OpPrefix ->   ??   TopOp
	 * 65: OpPrefix ->   ?   TopOp
	 * 66: OpPrefix ->   ?  TopOp
	 * 67: OpPrefix ->   UNARY_RECIP  TopOp
	 * 103: OpPrefix ->   nounnot  TopOp
	 * 104: OpPrefix ->   %not  TopOp
	 * 111: OpPrefix ->   nounnot   TopOp
	 */
	private function r56($term1, $term0) {
		$term = new MP_PrefixOp($term0->value, $term1);
		$term->set_position_from_token_node($term0, $term1);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 68: OpSuffix ->   TopOp  !
	 * 69: OpSuffix ->   TopOp  !!
	 */
	private function r68($term1, $term0) {
		$term = new MP_PostfixOp($term1->value, $term0);
		$term->set_position_from_node_token($term0, $term1);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 70: OpInfix ->   TopOp  *  TopOp
	 */
	private function r70($term2, $term1, $term0) {
		$term = new MP_Operation($term1->value, $term0, $term2);
		$term->set_position_from_nodes($term0, $term2);
		if ($term1->note !== null) {
			$term->position[$term1->note === 'inserted with whitespace' ? 'fixspaces' : 'insertstars'] = true;
		}
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 71: OpInfix ->   TopOp  #pm#  TopOp
	 * 72: OpInfix ->   TopOp  **  TopOp
	 * 73: OpInfix ->   TopOp  ^^  TopOp
	 * 74: OpInfix ->   TopOp  ^  TopOp
	 * 75: OpInfix ->   TopOp  .  TopOp
	 * 76: OpInfix ->   TopOp  #  TopOp
	 * 77: OpInfix ->   TopOp  /  TopOp
	 * 78: OpInfix ->   TopOp  -  TopOp
	 * 79: OpInfix ->   TopOp  +  TopOp
	 * 80: OpInfix ->   TopOp  +-  TopOp
	 * 81: OpInfix ->   TopOp  and  TopOp
	 * 82: OpInfix ->   TopOp  or  TopOp
	 * 83: OpInfix ->   TopOp  nounand  TopOp
	 * 84: OpInfix ->   TopOp  nounor  TopOp
	 * 85: OpInfix ->   TopOp  ::=  TopOp
	 * 86: OpInfix ->   TopOp  :=  TopOp
	 * 87: OpInfix ->   TopOp  ::  TopOp
	 * 88: OpInfix ->   TopOp  :  TopOp
	 * 89: OpInfix ->   TopOp  <=  TopOp
	 * 90: OpInfix ->   TopOp  <  TopOp
	 * 91: OpInfix ->   TopOp  >=  TopOp
	 * 92: OpInfix ->   TopOp  >  TopOp
	 * 93: OpInfix ->   TopOp  =  TopOp
	 * 94: OpInfix ->   TopOp  ~  TopOp
	 * 95: OpInfix ->   TopOp  nounadd  TopOp
	 * 96: OpInfix ->   TopOp  nounsub  TopOp
	 * 97: OpInfix ->   TopOp  nounpow  TopOp
	 * 98: OpInfix ->   TopOp  noundiv  TopOp
	 * 99: OpInfix ->   TopOp  blankmult  TopOp
	 * 100: OpInfix ->   TopOp  STACKpmOPT  TopOp
	 * 101: OpInfix ->   TopOp  nouneq  TopOp
	 * 105: OpInfix ->   TopOp  %and  TopOp
	 * 106: OpInfix ->   TopOp  %or  TopOp
	 * 107: OpInfix ->   TopOp  nounmul  TopOp
	 * 108: OpInfix ->   TopOp  @  TopOp
	 * 112: OpInfix ->   TopOp  implies  TopOp
	 * 125: OpInfix ->   TopOp  xor  TopOp
	 * 126: OpInfix ->   TopOp  xnor  TopOp
	 * 127: OpInfix ->   TopOp  nor  TopOp
	 * 128: OpInfix ->   TopOp  nand  TopOp
	 */
	private function r71($term2, $term1, $term0) {
		$term = new MP_Operation($term1->value, $term0, $term2);
		$term->set_position_from_nodes($term0, $term2);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 102: Abs ->   |  TopOp  |
	 */
	private function r102($term2, $term1, $term0) {
		$term = new MP_FunctionCall(new MP_Identifier('abs'), [$term1]);
		$term->set_position_from_parser_tokens($term0, $term2);
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 109: OpInfix ->   TopOp  @@Is@@  TopOp
	 */
	private function r109($term2, $term1, $term0) {
		$term = new MP_Operation('*', $term0, $term2);
		$term->set_position_from_nodes($term0, $term2);
		$term->position['fixspaces'] = true;
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 110: OpInfix ->   TopOp  @@IS@@  TopOp
	 */
	private function r110($term2, $term1, $term0) {
		$term = new MP_Operation('*', $term0, $term2);
		$term->set_position_from_nodes($term0, $term2);
		$term->position['insertstars'] = true;
	
		return $term;
	}
	
	/**
	 * Reduce logic for rules:
	 * 113: OpInfix ->   TopOp   ^-  TopOp
	 * 114: OpInfix ->   TopOp   ^+  TopOp
	 * 115: OpInfix ->   TopOp   ^+-  TopOp
	 * 116: OpInfix ->   TopOp   ^#pm#  TopOp
	 * 117: OpInfix ->   TopOp   **-  TopOp
	 * 118: OpInfix ->   TopOp   **+  TopOp
	 * 119: OpInfix ->   TopOp   **+-  TopOp
	 * 120: OpInfix ->   TopOp   **#pm#  TopOp
	 * 121: OpInfix ->   TopOp   ^^-  TopOp
	 * 122: OpInfix ->   TopOp   ^^+  TopOp
	 * 123: OpInfix ->   TopOp   ^^+-  TopOp
	 * 124: OpInfix ->   TopOp   ^^#pm#  TopOp
	 */
	private function r113($term2, $term1, $term0) {
		list($op1, $op2) = $term1->split();
		$term = new MP_Operation($op1->value, $term0, new MP_PrefixOp($op2->value, $term2));
		$term->set_position_from_nodes($term0, $term2);
		$term->rhs->set_position_from_token_node($op2, $term2);
	
		return $term;
	}
}