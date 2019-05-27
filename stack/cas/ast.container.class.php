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


class stack_ast_container extends stack_ast_container_silent implements cas_latex_extractor, cas_value_extractor{

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
     * @var string Only gets set by an answertest.
     */
    private $feedback;

    /**
     * @var string Used by the testing setup only.
     */
    private $testclean;

    /**
     * AST value coming back from CAS
     */
    private $evaluated;

    /**
     * LaTeX value coming back from CAS
     */
    private $latex;

    protected function __constructor($ast, string $source, string $context,
                                   stack_cas_security $securitymodel,
                                   array $errors, array $answernotes) {

        parent::__construct($ast, $source, $context, $securitymodel,
                            $errors, $answernotes);
    }


    public function add_errors($err) {
        if ('' == trim($err)) {
            return false;
        } else {
            $this->errors[] = $err;
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

        $fltfmt = stack_utils::decimal_digits($starredanswer);
        $fltfmt = $fltfmt['fltfmt'];

        $tans = $this->validationcontext['tans'];
        $validationmethod = $this->validationcontext['validationmethod'];

        $vcmd = 'stack_validate(['.$starredanswer.'], '.$lowestterms.','.$tans.')';
        if ($validationmethod == 'typeless') {
            // Note, we don't pass in the teacher's as this option is ignored by the typeless validation.
            $vcmd = 'stack_validate_typeless(['.$starredanswer.'], '.$lowestterms.', false,'.$fltfmt.')';
        }
        if ($validationmethod == 'numerical') {
            // TODO: What happens here what are the argumentsm is that the correct function?
            $vcmd = 'stack_validate_typeless(['.$starredanswer.'],
            '.$forbidfloats.', '.$lowestterms.', false,'.$fltfmt.')';
        }
        if ($validationmethod == 'equiv') {
            $vcmd = 'stack_validate_typeless(['.$starredanswer.'], '.$lowestterms.', true,'.$fltfmt.')';
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

    // This returns the fully filttered AST as it should be inputted were
    // it inputted perfectly.
    public function get_inputform(bool $keyless=false): string {
        if ($this->ast) {
            if ($keyless === true && $this->get_key() !== '') {
                $root = $this->ast;
                if ($root instanceof MP_Root) {
                    $root = $root->items[0];
                }
                if ($root instanceof MP_Statement) {
                    $root = $root->statement;
                }
                if ($root instanceof MP_Operation && $root->op === ':' &&
                    $root->lhs instanceof MP_Identifier) {
                    return $root->rhs->toString(array('inputform' => true, 'qmchar' => true, 'nosemicolon' => true));
                }
            }
            return $this->ast->toString(array('inputform' => true, 'qmchar' => true, 'nosemicolon' => true));
        }
        return '';
    }

    public function set_cas_evaluated_value(MP_Node $ast) {
        $this->evaluated = $ast;
    }

    public function set_cas_latex_value(string $latex) {
        $this->latex = $latex;
    }

    public function get_evaluated(): MP_Node {
        return $this->evaluated;
    }

    public function get_latex(): string {
        return $this->latex;
    }

    // If we "CAS validate" this string, then we need to set various options.
    // If the teacher's answer is null then we use typeless validation, otherwise we check type.
    public function set_cas_validation_context($vname, $lowestterms, $tans, $validationmethod, $simp) {

        if (!($validationmethod == 'checktype' || $validationmethod == 'typeless' || $validationmethod == 'units'
                || $validationmethod == 'unitsnegpow' || $validationmethod == 'equiv' || $validationmethod == 'numerical')) {
                    throw new stack_exception('stack_ast_container: validationmethod must one of "checktype", "typeless", ' .
                        '"units" or "unitsnegpow" or "equiv" or "numerical", but received "'.$validationmethod.'".');
        }
        $this->validationcontext = array(
            'vname'            => $vname,
            'lowestterms'      => $lowestterms,
            'tans'             => $tans,
            'validationmethod' => $validationmethod,
            'simp'             => $simp
        );
    }

    public function get_cas_validation_context() {
        return $this->validationcontext;
    }

    public function get_value() {
        if (null === $this->evaluated) {
            throw new stack_exception('stack_ast_container: tried to get the value form of an unevaluated casstring.');
        }
        $root = $this->evaluated;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        $casstring = '';
        if ($this->source === 's') {
            $casstring = $root->toString(array('nounify' => true, 'dealias' => true));
        } else {
            $casstring = $root->toString(array('dealias' => true));
            if ($root instanceof MP_Statement &&
                $root->flags !== null && count($root->flags) > 0) {
                // This makes it possible to write, when authoring, evaluation flags
                // like in maxima without wrapping in ev() yourself.
                $casstring = 'ev(' . $casstring . ')';
            }
        }

        return $casstring;
    }

    public function get_dispvalue() {
        if ($this->evaluated) {
            return $this->evaluated->toString(array('inputform' => true, 'qmchar' => true, 'nosemicolon' => true));
        }
        return '';
    }

    public function get_display() {
        return $this->latex;
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

    public function get_feedback() {
        return $this->feedback;
    }

    public function set_feedback($val) {
        $this->feedback = $val;
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
        }

        // Otherwise set a key.
        $ast = $this->ast;
        $ast = new MP_Operation(':', new MP_Identifier($key), $ast);
        $this->ast = $ast;
    }

    /**
     * Replace the ast, with a human readable value, so we can test equality cleanly and dump values.
     */
    public function test_clean() {
        if ($this->ast) {
            $this->testclean = $this->ast->toString(array('nosemicolon' => true));
        }
        $this->ast = null;
        return true;
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
}