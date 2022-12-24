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

defined('MOODLE_INTERNAL')|| die();

// Ast container and related functions, which replace "cas strings".
//
// @copyright  2019 University of Aalto.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/cassecurity.class.php');
require_once(__DIR__ . '/ast.container.silent.class.php');
require_once(__DIR__ . '/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');


class stack_ast_container extends stack_ast_container_silent implements cas_latex_extractor,
            cas_value_extractor, cas_display_value_extractor {

    /*
     * NOTES:
     *  1. this does provide means of fetching the results of evaluation if
     *     you do not need them use the silent one.
     *  2. the usage of this class boils down to this:
     *    - ask it to make a casstring for you based on various information
     *    - ask that castring whether it is valid
     *    - check errors and answernotes
     *    - ask for inputform or evaluation form representation
     *    - you can also retrieve the AST but it is not secured and you should
     *      never modify it when taking it from an existing casstring, make
            sure that the AST is ready before you put it in a casstring
     */

    /**
     * If this is an input about to be validated, then we need to store some information here.
     */
    private $validationcontext = null;

    /**
     * AST value coming back from CAS.
     */
    private $evaluated;

    /**
     * LaTeX value coming back from CAS.
     */
    private $latex;

    /**
     * CAS rendered displayvalue.
     */
    private $displayvalue;

    protected function __construct() {
    }

    public function add_errors($err) {
        if ('' == trim($err)) {
            return false;
        } else {
            // Force validation first so that all the errors are in the same form.
            $this->get_valid();
            $this->errors[] = new $this->errclass($err, $this->get_source_context());
            // Old behaviour was to return the combined errors, but apparently it was not used in master?
            // TODO: maybe remove the whole return?
            return $this->get_errors();
        }
    }

    public function get_evaluationform(): string {
        // The common_ast_container provides means of dealing with validation context.
        if ($this->validationcontext === null) {
            return parent::get_evaluationform();
        }

        $starredanswer = parent::get_evaluationform();
        if ($this->validationcontext['lowestterms']) {
            $lowestterms = 'true';
        } else {
            $lowestterms = 'false';
        }

        if ($this->validationcontext['simp']) {
            $starredanswer = 'ev(' . $starredanswer . ',simp)';
        }

        $fltfmt = '"~a"';
        if ($this->ast !== null) {
            $fltfmt = $this->get_decimal_digits();
            $fltfmt = $fltfmt['fltfmt'];
        }

        $tans = $this->validationcontext['tans'];
        if ($tans === null || $tans === '') {
            // If we are here someone has forgotten something.
            $tans = 'und';
        }
        $validationmethod = $this->validationcontext['validationmethod'];

        $checkvars = $this->validationcontext['checkvars'];

        $vcmd = 'stack_validate(['.$starredanswer.'], '.$lowestterms.','.$tans.','.$fltfmt.','.$checkvars.')';
        if ($validationmethod == 'typeless') {
            $vcmd = 'stack_validate_typeless(['.$starredanswer.'], '.$lowestterms.','.$tans.','.
                $fltfmt.','.$checkvars.', false)';
        }
        if ($validationmethod == 'equiv') {
            $vcmd = 'stack_validate_typeless(['.$starredanswer.'], '.$lowestterms.','.$tans.','.
                $fltfmt.','.$checkvars.', true)';
        }
        if ($validationmethod == 'units') {
            // Note, we don't pass in forbidfloats as this option is ignored by the units validation.
            $vcmd = '(make_multsgn("blank"),stack_validate_units(['.$starredanswer.'], ' .
                    $lowestterms.', '.$tans.', "inline", '.$fltfmt.'))';
        }
        if ($validationmethod == 'unitsnegpow') {
            // Note, we don't pass in forbidfloats as this option is ignored by the units validation.
            $vcmd = '(make_multsgn("blank"),stack_validate_units(['.$starredanswer.'], ' .
                    $lowestterms.', '.$tans.', "negpow", '.$fltfmt.'))';
        }
        return $this->validationcontext['vname'] . ':' . $vcmd;
    }

    public function set_cas_evaluated_value(MP_Node $ast) {
        $this->evaluated = $ast;
    }

    public function set_cas_display_value(string $displayvalue) {
        // Maxima displays floats as sting with these tags.
        // The last of the old mess left?
        $displayvalue = str_replace('"!! ', '', $displayvalue);
        $displayvalue = str_replace(' !!"', '', $displayvalue);

        $this->displayvalue = $displayvalue;
    }

    public function set_cas_latex_value(string $latex) {
        $this->latex = stack_maxima_latex_tidy($latex);
    }

    public function get_evaluated(): MP_Node {
        return $this->evaluated;
    }

    public function get_latex(): string {
        return $this->latex;
    }

    public function is_correctly_evaluated(): bool {
        /*
         * In cases where a statement occurs many times, only the last values will be stored.
         * Some of the previous values will therefore be null, creating an exception if we ask for the value.
         */
        if ($this->evaluated === null) {
            return false;
        }
        return $this->isevaluated && $this->valid;
    }

    // If we "CAS validate" this string, then we need to set various options.
    // If the teacher's answer is null then we use typeless validation, otherwise we check type.
    public function set_cas_validation_context($vname, $lowestterms, $tans, $validationmethod, $simp, $checkvars) {

        if (!($validationmethod == 'checktype' || $validationmethod == 'typeless' || $validationmethod == 'units'
                || $validationmethod == 'unitsnegpow' || $validationmethod == 'equiv')) {
                    throw new stack_exception('stack_ast_container: validationmethod must one of "checktype", "typeless", ' .
                        '"units" or "unitsnegpow" or "equiv", but received "'.$validationmethod.'".');
        }
        $this->validationcontext = array(
            'vname'            => $vname,
            'lowestterms'      => $lowestterms,
            'tans'             => $tans,
            'validationmethod' => $validationmethod,
            'simp'             => $simp,
            'checkvars'        => $checkvars,
        );
    }

    public function get_cas_validation_context() {
        return $this->validationcontext;
    }

    public function get_value() {
        if (null === $this->evaluated) {
            throw new stack_exception('stack_ast_container: tried to get the value from of an unevaluated casstring.');
        }
        return $this->ast_to_string($this->evaluated, array('checkinggroup' => true));
    }

    /* This function returns something a teacher might claim a student types in.
     * This means we have to de-parse a lot of things, listed below.
     */
    public function get_dispvalue() {

        /* To create test cases we need the following:
         * (1) we want actual numerical information, such as 0.5000 not displaydp(0.5,4);
         * (2) we don't want noun values (students do not type these in);
         * (3) we want ? characters, and no semicolons.
         * (4) we want +- and not #pm#.
         * (5) ntuples have to be stripped off.
         */

        $dispval = $this->displayvalue;
        /*
         * This function is mostly used for testing.  In the case of an unevaluated expression we do not
         * want to have an exception here, so we degrade from "null" to an empty string.
         */
        if ($dispval === null) {
            $dispval = '';
        }
        $testval = self::make_from_teacher_source($dispval, '', new stack_cas_security());
        $computedinput = $testval->ast->toString(array('nounify' => 0, 'inputform' => true,
                'qmchar' => true, 'pmchar' => 0, 'nosemicolon' => true, 'nontuples' => true));

        return $computedinput;
    }

    public function get_display() {
        if (!$this->is_correctly_evaluated()) {
            throw new stack_exception('stack_ast_container: ' .
                    'tried to get the LaTeX representation from of an unevaluated or invalid casstring.');
        }
        return trim($this->latex);
    }

    /*
     * Used to test the ast within the container.
     */
    public function get_ast_test() {
        if ($this->is_correctly_evaluated()) {
            return $this->evaluated->toString(array('flattree' => true));
        }
        return $this->ast->toString(array('flattree' => true));
    }

    public function get_ast_clone() {
        if ($this->is_correctly_evaluated()) {
            $ast = clone $this->evaluated;
        } else {
            $ast = clone $this->ast;
        }
        if ($ast instanceof MP_Root) {
            $ast = $ast->items[0];
        }
        return $ast;
    }

    /**
     * For when you want to look at the AST after basic filters and
     * don't care about multiple statements or comments.
     */
    public function get_commentles_primary_statement() {
        $commentfilter = stack_parsing_rule_factory::get_by_common_name('901_remove_comments');
        $ast = clone $this->ast;
        $dummya = [];
        $dummyb = [];
        $ast = $commentfilter->filter($ast, $dummyb, $dummya, new stack_cas_security());
        if ($ast instanceof MP_Root) {
            // After removal of comments should be the first.
            $ast = $ast->items[0];
            // The evaluation-flag transformation has already happened.
            $ast = $ast->statement;
        }
        return $ast;
    }

    public function add_answernote($val) {
        $this->answernotes[] = $val;
    }

    public function get_answernote($raw = 'implode') {
        if (null === $this->valid) {
            $this->get_valid();
        }
        if ($raw === 'implode') {
            return trim(implode(' | ', array_unique($this->answernotes)));
        }
        return $this->answernotes;
    }

    /*
     * We sometimes need to modify the ast to set a particular key.
     */
    public function set_key($key) {
        $root = $this->ast;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        if ($root instanceof MP_Statement) {
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation && $root->op === ':' &&
                $root->lhs instanceof MP_Identifier) {
            $root->lhs->value = $key;
            return true;
        }

        // Otherwise set a key.
        $nop = new MP_Operation(':', new MP_Identifier($key), $root);
        $root->parentnode->replace($root, $nop);
    }

    /**
     * Cloning is complex when we have object references.
     */
    public function __clone() {
        parent::__clone();
        if ($this->evaluated) {
            $this->evaluated = clone $this->evaluated;
        }
    }

    /**
     * Identify simplification modifications. For #849.
     * The identified value is not trustworthy and only gives a hint of
     * the last seen mod, this will not work with references or conditionals.
     * Should be good enough for CASText.
     */
    public function identify_simplification_modifications(): array {
        $r = [
            'simp-accessed' => false,
            'simp-modified' => false,
            'last-seen' => null,
            'out-of-ev-write' => false
        ];

        // Ensure depth with a group.
        $ast = new MP_Group([$this->get_commentles_primary_statement()]);

        $seek = function($node) use (&$r) {
            if ($node instanceof MP_Identifier && $node->value === 'simp') {
                $r['simp-accessed'] = true;
                if ($node->parentnode instanceof MP_Operation && ($node->parentnode->op === ':' ||
                        $node->parentnode->op === '=') && $node->parentnode->lhs === $node) {
                    $r['simp-modified'] = true;
                    $val = $node->parentnode->rhs;
                    if ($val instanceof MP_Boolean) {
                        $r['last-seen'] = $val->value;
                    }
                }
                if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name instanceof MP_Atom &&
                        $node->parentnode->name->value === 'ev') {
                    if (array_search($node, $node->parentnode->arguments, true) > 0) {
                        $r['last-seen'] = true;
                    }
                }
                if ($node->parentnode instanceof MP_Operation && $node->parentnode->op === ':' &&
                        $node->parentnode->lhs === $node && !($node->parentnode->parentnode instanceof MP_FunctionCall &&
                        $node->parentnode->parentnode->name instanceof MP_Atom &&
                        $node->parentnode->parentnode->name->value === 'ev')) {
                    // Not perfect but should identify if a modification
                    // is not part of 'ev' definitions. Still false positives
                    // if done within an `ev` that holds `simp` itself.
                    $r['out-of-ev-write'] = true;
                }
            }
            return true;
        };
        $ast->callbackRecurse($seek);

        return $r;
    }
}
