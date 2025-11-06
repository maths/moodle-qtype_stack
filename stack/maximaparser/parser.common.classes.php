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
require_once('MP_classes.php');

// Used to filter parser stacks to pick only relevant items for exceptions.
// Basically, drop state numbers as those can change and we should not use
// them in errors.
function stack_maxima_parser_exception_partial_filter($item): bool {
    return $item instanceof MP_Node || $item instanceof stack_maxima_lexer_token;
}

// General exception from a parser.
class stack_maxima_parser_exception extends Exception implements JsonSerializable {
    // What tokens were expected. String form.
    public ?array $expected = null;
    // What was seen, the full token.
    public ?stack_maxima_lexer_token $received = null;
    // The full original code.
    public String $original;
    // The previous token.
    public ?stack_maxima_lexer_token $previous = null;
    // Partial results, i.e., the parser stack with tokens and reduced 
    // MP objects. State numbers and other parsing details eliminated.
    public array $partial;

    public function __construct($message, $expected, $received, $original, $previous, $partial) {
        parent::__construct($message);
        $this->expected = $expected;
        $this->received = $received;
        $this->original = $original;
        $this->previous = $previous;
        $this->partial = $partial;
    }

    public function __toString() {
        return 'Expected [' . implode(',', $this->expected) . '] received ' . json_encode($this->received);
    }

    public function jsonSerialize(): mixed {
        return [
            'expected' => $this->expected,
            'received' => $this->received,
            'previous' => $this->previous,
            'partial' => $this->partial,
            'original' => $this->original
        ];
    }
}




/**
 * Holds static copies of variants of the tables and provides common
 * access logic.
 * 
 * Exists to abstract any encoding/compression of the table away from
 * the parser. And naturally to avoid loading the same static data
 * multiple times.
 */
class stack_maxima_parser_table_holder {
    private static $cache = [];
    private $table; // state -> [t_id -> action]
    private $goto; // state -> [nt_id -> state]
    private $rules_to_nonterminals; // rule num -> nt_id
    private $nonterminals; // nt_id -> name
    private $terminals; // name -> nt_id

    public static function get_for_grammar(string $jsonname) {
        if (isset(self::$cache[$jsonname])) {
            return self::$cache[$jsonname];
        }
        $content = file_get_contents(__DIR__ . '/autogen/' . $jsonname);
        $content = json_decode($content, true);
        $r = new stack_maxima_parser_table_holder();
        $r->table = $content['table'];
        $r->goto = $content['goto'];
        $r->rules_to_nonterminals = $content['rules_to_nonterminals'];
        $r->nonterminals = $content['nonterminals'];
        $r->terminals = array_flip($content['terminals']);
        self::$cache[$jsonname] = $r;
        return $r;
    }


    /**
     * Action (Shift/Reduce) based on the current state and token seen.
     * 
     * The action will be retuned as an array as follows:
     *  - if Shift a single element array with [state num]
     *  - if Reduce an array of [rule, nt name, nt id]
     * 
     * If no match is available null will be returned.
     */
    public function get_action(int $state, String $token): ?array {
        if (!isset($this->terminals[$token])) {
            return null; // "let" or other special keyword not present in this grammar.
        }
        $t = $this->terminals[$token];
        if (!isset($this->table[$state]) || !isset($this->table[$state][$t])) {
            return null;
        }
        $encoded = $this->table[$state][$t];
        if ($encoded % 2 == 0) {
            // Even ones are shifts.
            return [$encoded/2];
        } else {
            // Odd ones are reduces.
            $rule = ($encoded - 1)/2;
            $nt_id = $this->rules_to_nonterminals[$rule];
            return [$rule, $this->nonterminals[$nt_id], $nt_id];
        }
    }

    /**
     * Lists expected tokens for a given state, mainly for error generation.
     */
    public function get_expected(int $state): array {
        $r = [];
        // Only flip it when necessary, this happens only during exceptions.
        $flipped = array_flip($this->terminals);
        foreach (array_keys($this->table[$state]) as $tid) {
            $r[] = $flipped[$tid];
        }
        return $r;
    }

    /**
     * Next state based on the current state and the reduced nonterminal.
     * 
     * Null if impossible.
     */
    public function get_goto(int $state, int $nonterminal_id): ?int {
        if (!isset($this->goto[$state]) || !isset($this->goto[$state][$nonterminal_id])) {
            return null;
        }
        return $this->goto[$state][$nonterminal_id];
    }
}