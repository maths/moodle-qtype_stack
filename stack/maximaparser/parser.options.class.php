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

require_once(__DIR__ . '/lexers/cas.php');
require_once(__DIR__ . '/lexers/localised.php');

/*
 This class tries to separate the optiosn related to parsing from
 the underlying implementation of the parser. Both to help when changing
 a parser and when documenting the features of those parsers.

 Note that depending onf the implementation, these options may affect:
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
     * TODO: do we need multiple options for the same keyword?
     */
    public $locals = [];

    /**
     * Localised syntax, these must be carefully selected. The same char
     * may not be in multiple roles and these may also conflict with
     * some operators.
     */
    public $listseparator = ',';
    public $decimalseparator = '.';
    public $decimalgroupping = [];
    public $statementendtokens = [';'];


    /**
     * Primary rule, i.e., what are we parsing for? Basically, selects
     * the grammar and affects the output. Original parser supported
     * the values `'Root'` and `'Equivline'`
     */
    public $primaryrule = 'Root';

    /**
     * Drop comments. If you do not need comments for example for
     * annotation purposes you might drop them during processing.
     */
    public $dropcomments = true;


    /**
     * Support, for base N integers, with various styles.
     * 
     * These require special filtering of the parser result to convert
     * into CAS side objects.
     * 
     * C-style includes binary, octal, and hex modes:
     *  `0b1010`, `01334`, `0xBC32`
     * 
     * BASIC-style includes binary, octal, and hex modes:
     *  `&b1010`, `&o1334`, `&HBC32`
     * 
     * Suffix-style, allows arbitrary base, note that digits are limited to
     * numerals and latin alphabet.
     *  `1010_2`, `1334_8`, `BC32_16`
     * 
     * Note that base 10 integers and floats are still allowed to be mixed in
     * use AST-filters to forbid them.
     */
    public $basenc = false;
    public $basenbasic = false;
    public $basensuffix = false;

    /**
     * The parser my try to insert a bening operator like `*` if it cannot
     * parse the current tokens. Basically, this is the old insert stars
     * corrective parser. It may also choose to insert and end token like `;`
     * to fix similar situations.
     * 
     * Use this setting to tell whether you want to use `'*'` or `';'` symbols
     * when trying to fix things or keep this off if you do not want to fix
     * things.
     * 
     * Note that these fixes are for raw parse issues, one can also fix other
     * things during filtering.
     */
    public $tryinsert = false;

    /**
     * Do we allow LISP-identifiers? This is a special feature not to
     * be used for student or even author content. Exists here to make
     * certain development tools able to use this same logic.
     */
    public $lispids = false;


    /**
     * Mapping of specific unicode characters into specific other chars.
     * In the mappign we basically have two forms:
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
        $this->statementendtokens = [';','$'];
        $this->unicodemap = self::load_unicodemapping();
        $this->extraletters = self::load_extraletters();
    }

    /**
     * Returns a lexer suitable for current options. 
     */
    public function get_lexer(string $input) {
        // Do we need basen support?
        $basen = $this->basenc || $this->basensuffix || $this->basenbasic;

        // Locale specific support?
        $caslocale = $this->listseparator === ',' && 
                     $this->decimalseparator === '.' &&
                     $this->decimalgroupping == [] && (
                        $this->statementendtokens == [';'] ||
                        $this->statementendtokens == [';', '$']
                     );

        // The pure old CAS version.
        if ($caslocale && !$basen) {
            return new stack_maxima_lexer_cas($input, $this);
        }
        // Localised version.
        if (!$caslocale && !$basen) {
            return new stack_maxima_lexer_localised($input, $this);   
        }

        // TODO: port the base-n lexers over.
        throw new stack_exception('No lexer available for these options.');
        return null;
    }


    /**
     * Returns a list of AST-filters related to this particular option set
     * for example psot filtering to turn base-n literals into base-10 or
     * into inert representations.
     */
    public function get_ast_filters(): array {
        return [];
    }

    /**
     * Returns MP-object toString options for tuning presentation to match
     * original parsed form. 
     */
    public function get_toString_options() {
        $r = [];
        if ($this->listseparator !== ',') {
            $r['listseparator'] = $this->listseparator;
        }
        if ($this->decimalseparator !== '.') {
            $r['decimalseparator'] = $this->decimalseparator;
        }
        // For statement separtaor we use the first option... For now.
        if ($this->statementendtokens[0] !== ';') {
            $r['statementseparator'] = $this->statementendtokens[0];
        }
        return $r;
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
            implode('', array_keys($chars))) . ']/u';

        return self::$defaultextraletters;
    }

    /**
     * Gives a default CAS-config, i.e. what one expects keyvals to be given
     * as. No localisation and no unicode mappings. For now no `$` either.
     * And no fixing through inserts.
     */
    public static function get_cas_config(): stack_parser_options {
        $r = new stack_parser_options();
        $r->unicodemap = [];
        $r->statementendtokens = [';'];
        return $r;
    }

    /**
     * Gives config matching old student input parsing.
     */
    public static function get_old_config(): stack_parser_options {
        $r = new stack_parser_options();
        $r->statementendtokens = [';'];
        $r->tryinsert = '*';
        $r->locals = ['let' => stack_string('equiv_LET')];
        $r->unicodemap = self::load_unicodemapping();
        $r->extraletters = self::load_extraletters();
        return $r;
    }    

}