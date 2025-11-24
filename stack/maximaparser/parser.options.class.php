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
require_once('decimal.comma.lexer.class.php');
require_once('basen.lexer.class.php');
require_once('autogen/parser-root.php');
require_once('autogen/parser-equivline.php');
require_once('error.interpreter.class.php');

enum StackParserInsertionOption {
    // No insertion attempts.
    case None;
    // Attempt with multiplication star.
    case Stars;
    // Attempt statement end.
    case EndToken;
}

enum StackLexerSeparators {
    // List separator is ',', decimal separator is '.', statements by ';' and '$'.
    case Dot;
    // List separator is ';', decimal separator is ',', statements by '$'.
    case Comma;
}

enum StackParserRule {
    // Normal maxima expressions and possibly multiple statements
    case Root;
    // Equivline singular expressions with some extra notation
    case Equivline;
}


/*
 This class tries to separate the options related to parsing from
 the underlying implementation of the parser. Both to help when changing
 a parser and when documenting the features of those parsers.

 Note that depending on the implementation, these options may affect:
  1. The selection of the grammar to be used.
  2. Separate lexer/parser settings.
  3. Localisation related details.
  4. The filters needed to be applied on the parse result.


 @copyright  2023 Aalto University.
 @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
*/
class stack_parser_options {
    /**
     * This is where we load up the mappings from ./unicode/*.json files.
     */
    public static $defaultunicodemap = null;
    public static $defaultextraletters = null;

    /**
     * Does the parser consider keywords to be case insensitive?
     * `If` == `if`, `true` == `TRUE` etc.
     */
    public $casesensitivekeywords = true;

    /**
     * Is the special `+-` operator active? If not considered as two
     * separate operators.
     */
    public $pm = true;

    /**
     * Do we have overriding localised alternatives for keywords?
     * e.g. `['true' => 'tosi', 'false' => 'epätosi']` or
     * `['let' => stack_string('equiv_LET')]`
     *
     * Those are just alternatives, by default allow the normal ones.
     *
     * TODO: do we need multiple options for the same keyword? If so
     * the lexers will need to know.
     */
    public $locals = [];

    /**
     * Localised syntax, currently not freely mixable.
     *
     * Also currently no support for digit groupping.
     */
    public StackLexerSeparators $separators = StackLexerSeparators::Dot;

    /**
     * Rule, i.e., what are we parsing for? Basically, selects
     * the grammar and affects the output. Original parser supported
     * the values `'Root'` and `'Equivline'`
     */
    public StackParserRule $rule = StackParserRule::Root;


    /**
     * Drop comments. If you do not need comments for example for
     * annotation purposes you might drop them during processing.
     */
    public $dropcomments = true;


    /**
     * Turn on base-N integer literals. i.e. C-style (0b0010, 0777, 0xBEEF)
     * and B453_36 suffix for variable bases.
     *
     * In this mode decimal numbers do not exist and localised decimal
     * separators have no role.
     *
     * No decimal separators, both ',' and ';' are list separators,
     * statements by '$'.
     *
     * A big TODO... Someone should desing a lexer and syntax for supporting
     * base-N decimal numbers mixed in this. Also normal floats...
     */
    public $basen = false;

    /**
     * The parser may try to insert an operator like `*` if it cannot
     * parse the current tokens. Basically, this is the old insert stars
     * corrective parser. It may also choose to insert an end token like `;`
     * to fix similar situations.
     *
     * Use this setting to tell whether you want to use `'*'` or `';'` symbols
     * when trying to fix things or keep this off if you do not want to fix
     * things.
     *
     * Note that these fixes are for raw parse issues, one can also fix other
     * things during filtering.
     */
    public StackParserInsertionOption $tryinsert = StackParserInsertionOption::None;

    /**
     * Do we allow LISP-identifiers? This is a special feature not to
     * be used for student or even author content. Exists here to make
     * certain development tools able to use this same logic.
     *
     * When false `?` is being mapped to `QMCHAR`.
     */
    public $lispids = false;


    /**
     * Mapping of specific unicode characters into specific other chars.
     * In the mapping we basically have two forms:
     *  1. This unicode map of chars that will be converted in lexer
     *     and forgotten.
     *  2. The extra letters set which are left as is but allowed as
     *     non first identifier chars and might get dealt with by AST
     *     filtering later.
     */
    public $unicodemap = [];

    /**
     * Special characters that are supposed to be considered part of
     * identifiers but are not strictly letters. Or just numbers.
     * Basically, this allows us to handle superscripts etc.
     */
    public $extraletters = false;

    /**
     * Construct a basic version.
     */
    public function __construct() {
        // Ensure that the special cases have been initialised.
        $this->locals = [];
        $this->separators = StackLexerSeparators::Dot;
        $this->unicodemap = self::load_unicodemapping();
        $this->extraletters = self::load_extraletters();
    }

    /**
     * Initialises the cached unicode map and returns a copy of it.
     */
    public static function load_unicodemapping(): array {
        if (self::$defaultunicodemap !== null) {
            return self::$defaultunicodemap;
        }
        self::$defaultunicodemap = [];
        foreach (new DirectoryIterator(__DIR__ . '/unicode') as $item) {
            // Skip . and .. and dirs.
            if ($item->isDot() || $item->isDir()) {
                continue;
            }
            $itemname = $item->getFilename();
            // Skip superscripts and subscripts, those are "extraletters".
            if ($itemname === 'subscript-stack.json' || $itemname === 'superscript-stack.json') {
                continue;
            }
            if (strrpos($itemname, '.json') === strlen($itemname) - 5) {
                $contents = file_get_contents($item->getPathName());
                foreach (json_decode($contents, true) as $key => $value) {
                    self::$defaultunicodemap[$key] = $value;
                }
            }
        }
        return self::$defaultunicodemap;
    }

    /**
     * Initialises the extra letters regexp.
     */
    public static function load_extraletters(): string {
        if (self::$defaultextraletters !== null) {
            return self::$defaultextraletters;
        }
        $chars = [];
        foreach (new DirectoryIterator(__DIR__ . '/unicode') as $item) {
            // Skip . and .. and dirs.
            if ($item->isDot() || $item->isDir()) {
                continue;
            }
            $itemname = $item->getFilename();
            // Only pick subscripts and superscripts as the chars for this.
            if (!($itemname === 'subscript-stack.json' || $itemname === 'superscript-stack.json')) {
                continue;
            }
            if (strrpos($itemname, '.json') === strlen($itemname) - 5) {
                $contents = file_get_contents($item->getPathName());
                foreach (json_decode($contents, true) as $key => $value) {
                    $chars[$key] = $key;
                }
            }
        }
        self::$defaultextraletters = '/[' . preg_quote(
            implode('', array_keys($chars))
        ) . ']/u';

        return self::$defaultextraletters;
    }

    /**
     * Gives a default CAS-config, i.e. what one expects keyvals to be given
     * as. No localisation and no unicode mappings.
     * And no fixing through inserts.
     */
    public static function get_cas_config(): stack_parser_options {
        $r = new stack_parser_options();
        $r->unicodemap = [];
        $r->tryinsert = StackParserInsertionOption::None;
        $r->separators = StackLexerSeparators::Dot;
        return $r;
    }

    /**
     * Gives config matching old student input parsing.
     */
    public static function get_old_config(): stack_parser_options {
        $r = new stack_parser_options();
        $r->separators = StackLexerSeparators::Dot;
        $r->tryinsert = StackParserInsertionOption::Stars;
        $r->locals = ['let' => stack_string('equiv_LET')];
        $r->unicodemap = self::load_unicodemapping();
        $r->extraletters = self::load_extraletters();
        return $r;
    }

    /**
     * Gives config matching old student input parsing but for decimal commas
     */
    public static function get_old_config_comma(): stack_parser_options {
        $r = new stack_parser_options();
        $r->separators = StackLexerSeparators::Comma;
        $r->tryinsert = StackParserInsertionOption::Stars;
        $r->locals = ['let' => stack_string('equiv_LET')];
        $r->unicodemap = self::load_unicodemapping();
        $r->extraletters = self::load_extraletters();
        return $r;
    }

    /**
     * Initialises a lexer suitable for these settings.
     */
    public function get_lexer(string $content): stack_maxima_lexer_base {
        if ($this->basen) {
            return new stack_maxima_lexer_basen($content, $this);
        }

        return match ($this->separators) {
            StackLexerSeparators::Dot => new stack_maxima_lexer_base($content, $this),
            StackLexerSeparators::Comma => new stack_maxima_lexer_decimal_comma($content, $this)
        };
    }

    /**
     * Gets a parser matching the settings.
     */
    public function get_parser() {
        return match ($this->rule) {
            StackParserRule::Root => new stack_maxima_parser2_root($this),
            StackParserRule::Equivline => new stack_maxima_parser2_equivline($this)
        };
    }

    /**
     * Gets AST-filters to run on the parser result after parsing, and their
     * settings. Intended for cases where parsing produces items that need
     * post-processing, e.g., base-N integers may need to be rewritten into
     * some wrappings.
     *
     * Returns a list of filter names and filter options.
     */
    public function get_ast_filters(): array {
        $filternames = [];
        if ($this->basen) {
            $filternames[] = '115_lexer_post_process_stackbasen';
        }
        $filtersettings = [];
        return [$filternames, $filtersettings];
    }

    /**
     * Returns reproduction settings for AST->toString().
     * Useful when wanting to use the same decimal separators etc.
     */
    public function get_to_string_settings(): array {
        $r = match ($this->separators) {
            StackLexerSeparators::Dot => [
                'decimal' => '.',
                'listsep' => ',',
                'statementsep' => ';',
            ],
            StackLexerSeparators::Comma => [
                'decimal' => ',',
                'listsep' => ';',
                'statementsep' => '$',
            ]
        };
        if ($this->basen) {
            $r['reverstackbasen'] = true;
        }
        return $r;
    }

    /**
     * Returns error interpreter class matching this config
     * and tuned for student consumption.
     */
    public function get_student_error_interpreter(): stack_parser_error_interpreter {
        $ei = new stack_parser_error_interpreter($this);
        $ei->logicflow = false;
        $ei->positiondata = false;
        return $ei;
    }

    /**
     * Returns error interpreter class matching this config
     * and tuned for author consumption.
     */
    public function get_author_error_interpreter(): stack_parser_error_interpreter {
        $ei = new stack_parser_error_interpreter($this);
        $ei->logicflow = true;
        $ei->positiondata = true;
        return $ei;
    }
}
