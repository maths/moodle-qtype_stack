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
require_once(__DIR__ . '/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');


class stack_ast_container implements cas_latex_extractor, cas_value_extractor{

    /*
     * NOTES:
     *  1. this does not provide means of storing the results of evaluation.
     *  2. the usage of this class boils down to this:
     *    - ask it to make a casstring for you based on various information
     *    - ask that castring whether it is valid
     *    - check errors and answernotes
     *    - ask for inputform or evaluation form representation
     *    - you can also retrieve the AST but it is not secured and you should
     *      never modify it when taking it from an existing casstring, make
            sure that the AST is ready before you put it in a casstring
     */

    public static function make_from_student_source(string $raw, string $context,
            stack_cas_security $securitymodel, array $filterstoapply = array(), 
            array $filteroptions = array(), string $grammar = 'Root'): stack_ast_container {

        $errors = array();
        $answernotes = array();
        $parseroptions = array('startRule' => $grammar,
                               'letToken' => stack_string('equiv_LET'));

        // Force the security filter to use 's'.
        if (isset($filteroptions['998_security'])) {
            $filteroptions['998_security']['security'] = 's';
        } else {
            $filteroptions['998_security'] = array('security' => 's');
        }
        // If security filter is not included include it.
        if (array_search('998_security', $filterstoapply) === false) {
            $filterstoapply[] = '998_security';
        }

        // Use the corective parser as this comes from the student.
        $ast = maxima_corrective_parser::parse($raw, $errors, $answernotes, $parseroptions);

        // Get the filter pipeline. Even if we would not use it in case of
        // ast = null, we still want to check that the request is valid.
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline($filterstoapply, $filteroptions, true);

        if ($ast !== null) {
            $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);
        }
        // It is now ready to be created.
        $astc = new self;
        $astc->rawcasstring = $raw;
        $astc->ast = $ast;
        $astc->source = 's';
        $astc->context = $context;
        $astc->securitymodel = $securitymodel;
        $astc->errors = $errors;
        $astc->answernotes = $answernotes;
        $astc->conditions = array();
        $astc->valid = null;
        return $astc;
    }

    public static function make_from_teacher_source(string $raw, string $context,
            stack_cas_security $securitymodel, $conditions = array()): stack_ast_container {
        // If you wonder why the security model is in play for teachers it
        // is here to bring in the information on whether units are constants
        // or not and thus affect the teachers ability to write into them.
        $errors = array();
        $answernotes = array();
        $parseroptions = array('startRule' => 'Root',
                               'letToken' => stack_string('equiv_LET'));

        // Use the raw parser if it does not work this is invalid input.
        $ast = null;
        try {
            $ast = maxima_parser_utils::parse($raw);
        } catch (SyntaxError $e) {
            $ast = maxima_corrective_parser::parse($raw, $errors, $answernotes, $parseroptions);
            // All stars that were insertted by that are invalid.
            // And that comes from the strict filter later.
        }

        // As we take no filter options for teachers sourced stuff lets build them from scratch.
        $filteroptions = array('998_security' => array('security' => 't'));

        // Get the filter pipeline. Now we only want the core filtters and
        // append the strict syntax check to the end.
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(array('998_security', '999_strict'), $filteroptions, true);

        if ($ast !== null) {
            $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);
        }

        // It is now ready to be created.
        $astc = new self;
        $astc->rawcasstring = $raw;
        $astc->ast = $ast;
        $astc->source = 't';
        $astc->context = $context;
        $astc->securitymodel = $securitymodel;
        $astc->errors = $errors;
        $astc->answernotes = $answernotes;
        $astc->conditions = $conditions;
        $astc->valid = null;
        return $astc;
    }

    public static function make_from_teacher_ast(MP_Statement $ast, string $raw, string $context,
            stack_cas_security $securitymodel): stack_ast_container {
        // This function is intended to be used when dealing with keyvals,
        // as there one already has an AST representing multiple casstring
        // and can just split it to pieces.

        $errors = array();
        $answernotes = array();
        $filteroptions = array('998_security' => array('security' => 't'));
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(array('998_security', '999_strict'), $filteroptions, true);
        $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);

        $astc = new self;
        $astc->rawcasstring = $raw;
        $astc->ast = $ast;
        $astc->source = 't';
        $astc->context = $context;
        $astc->securitymodel = $securitymodel;
        $astc->errors = $errors;
        $astc->answernotes = $answernotes;
        $astc->conditions = array();
        $astc->valid = null;
        return $astc;
    }

    /** @var string as typed in by the user.
     *  This should not be changed by the system, so will contains things like ?, which are otherwise not permitted.
     */
    private $rawcasstring;

    /**
     * The parsetree representing this ast after all modifications.
     */
    // TODO: refactor the inputs....
    public $ast;

    /**
     * The source of this ast. As used for security considerations.
     */
    private $source = 's';

    /**
     * The backreference to the location in the question model from which this
     * ast comes from. e.g., '/questionvariables' or '/prt/0/node/2/tans'.
     * more specific location data i.e. character position data is in the AST.
     */
    private $context;

    /**
     * If this is an input about to be validated, then we need to store some information here.
     */
    private $validationcontext = null;

    /**
     * The cassecurity settings applied to this question.
     */
    private $securitymodel;

    /**
     * Cached result of the validity check.
     */
    private $valid = null;

    /**
     * Errors collected from various sources of validation.
     */
    private $errors = array();

    /**
     * Answernotes collected from various sources of validation.
     */
    private $answernotes;

    /**
     * @var string Only gets set by an answertest.
     */
    private $feedback;

    /**
     * NOTE this really should be in a seprate subclass type, like those that do not generate
     * output... ALSO NOTE that castext2 no longer uses these at all so need is somewhat unknown.
     * @array of additional CAS strings which are conditions when the main expression can
     * be evaluated.  I.e. this encapsulates restrictions on the domain of the main value.
     * Same format as the $value string, and not designed to be read by end users.
     */
    private $conditions;

    /**
     * @var string the value of the CAS string, in Maxima syntax. Only gets set
     *             after the casstring has been processed by the CAS.
     *             Exactly what Maxima returns, and so suitable to be sent back.
     */
    private $value;

    /**
     * @var string A sanitised version of the value, e.g. with decimal places printed
     *             and stackunits replaced by multiplication.
     *             Used sparingly, e.g. for the teacher's answer, and testing inputs.
     *             Will contain ?, not QMCHAR.
     */
    private $dispvalue;

    /**
     * @var string Displayed for of the value. LaTeX. Only gets set
     *             after the casstring has been processed by the CAS.
     */
    private $display;

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



    private function __constructor($ast, string $source, string $context,
                                   stack_cas_security $securitymodel,
                                   array $errors, array $answernotes, array $conditions = array()) {

        $this->ast = $ast;
        $this->source = $source;
        $this->context = $context;
        $this->securitymodel = $securitymodel;
        $this->errors = $errors;
        $this->answernotes = $answernotes;
        $this->conditions = $conditions;
        $this->valid = null;

        if (!('s' === $source || 't' === $source)) {
            throw new stack_exception('stack_ast_container: source, must be "s" or "t" only.');
        }
    }

    public function get_valid(): bool {
        if ($this->valid === null) {
            if ($this->ast === null) {
                // In case parsing was impossible we store the errors in this class.
                $this->valid = false;
                return false;
            }

            // First check if the AST contains something marked as invalid.
            $hasinvalid = false;
            $findinvalid = function($node)  use(&$hasinvalid) {
                if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                    $hasinvalid = true;
                    return false;
                }
                return true;
            };
            $this->ast->callbackRecurse($findinvalid, false);

            $this->valid = !$hasinvalid;
        }

        return $this->valid;
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

    public function get_errors($raw = 'implode') {
        if (null === $this->valid) {
            $this->get_valid();
        }
        if ($raw === 'implode') {
            return implode(' ', array_unique($this->errors));
        }
        return $this->errors;
    }

    public function get_raw_casstring() {
        return $this->rawcasstring;
    }

    // This returns the fully filttered AST as it should be inputted were
    // it inputted perfectly.
    public function get_inputform(): string {
        if ($this->ast) {
            return $this->ast->toString(array('inputform' => true, 'qmchar' => true, 'nosemicolon' => true));
        }
        return '';
    }

    // This returns the form that could be sent to CAS.
    // Note that this will do serious amount of work and could for example tie
    // in the conditions if needed.
    public function get_evaluationform(): string {
        if (false === $this->get_valid()) {
            throw new stack_exception('stack_ast_container: tried to get the evalution form of an invalid casstring.');
        }
        $root = $this->ast;
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

    public function get_key() {
        // If this is an assignment type we can return its target "key".
        $key = '';
        $root = $this->ast;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        if ($root instanceof MP_Statement && $root->statement instanceof MP_Operation && $root->statement->op === ':') {
            $key = $root->statement->lhs->toString();
            // Note that we do not split the key out of it that is not necessary.
        }
        return $key;
    }


    // cas_evaluatable interfaces.
    public function set_cas_status(array $errors) {
        if (count($errors) > 0) {
            $this->errors = array_merge($this->errors, $errors);
        }
    }
    
    public function get_source_context(): string {
        return $this->context;
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

    public function set_conditions($c) {
        $this->conditions = $c;
    }

    public function get_conditions() {
        return $this->conditions;
    }

    public function set_value($val) {
        $this->value = $val;
    }

    public function get_value() {
        return $this->value;
    }

    public function set_dispvalue($val) {
        $val = str_replace('"!! ', '', $val);
        $val = str_replace(' !!"', '', $val);
        // TODO, we might need to remove nouns here....
        $this->dispvalue = $val;
    }

    public function get_dispvalue() {
        return $this->dispvalue;
    }

    public function set_display($val) {
        $this->display = $val;
    }

    public function get_display() {
        return $this->display;
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
}