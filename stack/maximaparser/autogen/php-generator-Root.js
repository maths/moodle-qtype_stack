/*
 * This script generates the PHP parser from a grammar definition.
 * parser as well as various debug details.
 *
 * This will execute the step:
 *  'numbered-grammar.json' => 'parser.php'
 *
 * Note that that 'parser.php' will only function with the matching
 * 'lalr.json'.
 *
 * @copyright  2023 Matti Harjula, Aalto University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

const fs = require('fs');
fs.readFile('numbered-grammar-Root.json', 'utf8', function(err, data) {
    var numbered = JSON.parse(data);
    // Some indentation for the generated code.
    var indent = '    ';
    var indent2 = indent + indent;
    var indent3 = indent2 + indent;
    var indent4 = indent2 + indent2;
    var indent5 = indent2 + indent3;
    var indent6 = indent3 + indent3;
    var indent7 = indent4 + indent3;
    var indent8 = indent4 + indent4;

    var code = `<?php
// THIS FILE HAS BEEN GENERATED, DO NOT EDIT, EDIT THE GENERATOR.
/*
 @copyright  2023 Matti Harjula, Aalto University.
 @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
*/

require_once(__DIR__ . '/../MP_classes.php');

class stack_parser_exception extends Exception {
    // What tokens were expected.
    public $expected = null;
    // What was seen, the full token.
    public $received = null;
    // Where it was seen if known.
    public $position = null;
    // The full original code.
    public $original = null;
    // The previous token.
    public $previous = null;
    // Partial results, i.e. all the MP-objects we can recover 
    // from the parsers stack. Might let one to give some context.
    public $partial = null;

    public function __construct($message, $expected, $received, $position, $original, $previous, $partial) {
        parent::__construct($message . ' Expected' . json_encode($expected) . ' received ' . json_encode($received));
        $this->expected = $expected;
        $this->received = $received;
        $this->position = $position;
        $this->original = $original;
        $this->previous = $previous;
        $this->partial = $partial;
    }

    public function __toString() {
        return 'Expected [' . implode(',', $this->expected) . '] received ' . json_encode($this->received) . ' at ' . json_encode($this->position);
    }
}

/**
 * A predicate for certain filter operations. 
 */
function is_mp_object($x): bool {
    return $x instanceof MP_Node;
}

class stack_maxima_parser2_root {

    private static $table = null;
    private static $goto = null;
    private static $dict = null;

    // Some debug features for development.
    // TODO: remove to save on checks.
    public static $debug = false;

    /**
     * The parse function takes a Lexer that produces the tokens.
     * 
     *
     * It can be told if it should insert stars or semi-colons
     * it cannot insert both. Note that the lexer also inserts stars 
     * especially in cases like "2x => 2*x".
     *
     * Finally it can be told to collect comments from the input stream or
     * just throw them away.
     * 
     * Returns an MP_Node or a parse error, wrap in something that 
     * catches those
     */
    public static function parse($lexer, $insert = false, $collectcomments = true, array &$notes = []) {        // First check if we have the table loaded.
        if (self::$table === null) {
            $raw = file_get_contents(__DIR__ . '/lalr-Root.json');
            $raw = json_decode($raw, true);
            self::$table = $raw['table'];
            self::$goto = $raw['goto'];
            self::$dict = array_flip($raw['dict']);
        }

        // Shorter.
        $goto = self::$goto;
        $table = self::$table;

        // Collect comments here, for injection to statement-lists.
        $commentdump = [];

        // Insertion of extra tokens might care if we have seen whitespace.
        $whitespaceseen = false;

        // Track previous token.
        $previous = null;

        // Start with the parser stack at state 0.
        $stack = [0];
        $shifted = true;
        $t = null; // The raw token.
        $T = null; // The symbolic token. e.g. NUM.
        while (true) {
            if ($shifted) {
                $previous = $t;
                $t = $lexer->get_next_token();
                while ($t !== null && ($t->type == 'WS' || $t->type == 'COMMENT')) {
                    if ($t->type === 'WS') {
                        $whitespaceseen = true;
                    }
                    if ($collectcomments === true && $t->type == 'COMMENT') {
                        $c = new MP_Comment($t->value, []);
                        $c->position['start'] = $t->position;
                        $c->position['start-row'] = $t->line;
                        $c->position['start-col'] = $t->column;
                        $c->position['end'] = 4 + mb_strlen($t->value) + $t->position;
                        $commentdump[] = $c;
                    }
                    $t = $lexer->get_next_token();
                }
                if ($t === null) {
                    // This is a magic char signaling the end of stream.
                    $T = "\u0000";
                } else if ($t->type == 'KEYWORD' || $t->type == 'SYMBOL') {
                    $T = $t->value;
                } else {
                    $T = $t->type;
                }
                $shifted = false;
                if (self::$debug) {
                    echo(json_encode($t) . "\n");
                }
            }

            // If insertion required try it.
            if (!isset($table[$stack[count($stack)-1]][$T])) {
                // TODO: maybe we should forbid keywords as identifiers and force string wrapping opnames?
                if ($t !== null && ($t->type === 'SYMBOL' || $t->type === 'KEYWORD') && isset($table[$stack[count($stack)-1]]['ID'])) {
                    // Sometimes it is possible to interpret symbols as identifiers.
                    $c = substr($t->value, 0, 1);
                    if ($c === '%' || $c === '_' || (preg_match('/\\pL/iu', $c) === 1)) {
                        $t->type = 'ID';
                        $T = 'ID';
                        if (self::$debug) {
                            echo("SYMBOL/KEYWORD->ID\n");
                        }
                    }
                } 
                if (!isset($table[$stack[count($stack)-1]][$T]) && (($insert === '*' && isset($table[$stack[count($stack)-1]]['*'])) || ($insert === ';' && isset($table[$stack[count($stack)-1]]['END_TOKEN'])))) {
                    // Only support these two and only insert if possible.
                    $lexer->return_token($t);
                    $T = $insert;
                    $t = new stack_maxima_token('SYMBOL', $insert, -1, -1, -1, mb_strlen($insert));
                    if ($whitespaceseen) {
                        $t->note = 'inserted with whitespace';
                        if (array_search('spaces', $notes) === false) {
                            $notes[] = 'spaces';
                        }
                    } else {
                        $t->note = 'inserted without whitespace';
                        if (array_search('missing_stars', $notes) === false) {
                            $notes[] = 'missing_stars';
                        }
                    }
                    if (array_search($insert, $lexer->options->statementendtokens) !== false) {
                        $t->type = 'END_TOKEN';
                        $T = 'END_TOKEN';
                    }
                    $whitespaceseen = false;
                    if (self::$debug) {
                        echo("return and insert " . json_encode($t) . "\n");
                    }
                } 
                if (!isset($table[$stack[count($stack)-1]][$T])) {
                    // Error got $t, was expecting these...
                    throw new stack_parser_exception('Unexpected token.', array_keys($table[$stack[count($stack)-1]]), $t, $t !== null ? ['row' => $t->line, 'char' => $t->column, 'position' => $t->position] : null, $lexer->original, $previous, array_filter($stack, 'is_mp_object'));
                }
            }

            $action = $table[$stack[count($stack)-1]][$T];

            if ($action[0] === 0) {
                // Do a shift.
                $stack[] = $t;
                $stack[] = $action[1];
                $shifted = true;
            } else {
                // Time for reduce.
                $rule = $action[1];
                $tokens = [];

                if ($action[2] > 0) {
                    // This may confuse you, read into the handling of the stack in LALR parsing.
                    $tmp = array_slice($stack, -$action[2]*2);
                    array_walk($tmp, function($value, $key) use (&$tokens) {
                        if ($key % 2 === 0) {
                            $tokens[] = $value;
                        }
                    });                    
                    $stack = array_slice($stack, 0, -$action[2]*2);
                }

                // Reduce to this var.
                $term = null;

                // Turn the tokens array into shorter variables.
                $term0 = array_shift($tokens);
                $term1 = array_shift($tokens);
                $term2 = array_shift($tokens);
                $term3 = array_shift($tokens);
                $term4 = array_shift($tokens); // We don't currently have a grammar of longer definition.

                switch ($rule) {
`;


    // Keep track of the handled so that we can simplify the tracking and selection of rules.
    var zeroindent = indent4;

    // Similars.
    var codes = {};
    var names = {};
    numbered.forEach((rule) => {
        if ('php' in rule) {
            if (rule['php'] in codes) {
                codes[rule['php']].push(rule['num']);
            } else {
                codes[rule['php']] = [rule['num']];
            }
            names[rule['num']] = rule['left'];
        }
    });


    Object.keys(codes).forEach((tcode) => {
        let nums = codes[tcode];
        for (const num of nums) {
            code += indent4 + indent + 'case ' + num + ': // ' + names[num] + '.\n';
        }
        let c = tcode.split("\n");
        let t = [];
        for (const r of c) {
            if (r.startsWith(">>>>")) {
                t.push(indent4 + indent2 + indent4 + r.substring(4));
            } else if (r.startsWith(">>>")) {
                t.push(indent4 + indent2 + indent3 + r.substring(3));
            } else if (r.startsWith(">>")) {
                t.push(indent4 + indent2 + indent2 + r.substring(2));
            } else if (r.startsWith(">")) {
                t.push(indent4 + indent2 + indent + r.substring(1));
            } else {
                t.push(indent4 + indent2 + r);
            }
        }
        code += t.join("\n");
        code += "\n" + indent4 + indent2 + "break;\n";
    });




    code += `
                    

                    default:
                        return ['error', 'unknown rule in reduce'];
                }

                // Push the reduced on back into stack.
                $stack[] = $term;

                // If we reached the start rule end here.
                if ($action[3] === 'Start') {
                    // The result should be on the top of the stack.
                    $root = $stack[1];
                    if ($collectcomments === true) {
                        // For now we simply append the comments withotu any
                        // sensible interleaving. One can always doe extra work
                        // to find the MP-objects that have positions that cover
                        // those comments and move them to correct places.
                        $root->items = array_merge($root->items, $commentdump);
                    }
                    return $root;
                }
                
                // Where to next?
                $stack[] = $goto[$stack[count($stack)-2]][$action[3]];

                // After reduce we need to track whitespace again.
                $whitespaceseen = false;
            }
        }

        // The result should be on the top of the stack.
        $root = end($stack);

        if ($collectcomments) {
            // For now we simply append the comments withotu any
            // sensible interleaving. One can always doe extra work
            // to find the MP-objects that have positions that cover
            // those comments and move them to correct places.
            $root->items = array_merge($root->items, $commentdump);
        }

        return $root;
`;


    // Close the parse function
    code += indent + "}\n"

    // Close the class.
    code += "\n}\n";

    // Dump it out.
    fs.writeFile('parser-root.php', code, (err) => {});
});