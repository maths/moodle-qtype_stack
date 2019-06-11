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
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.


require_once(__DIR__ . '/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/cassecurity.class.php');
require_once(__DIR__ . '/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');

class stack_ast_container_silent implements cas_evaluatable {

    /**
     * The parsetree representing this ast after all modifications.
     */
    protected $ast;

    /**
     * The source of this ast. As used for security considerations.
     */
    protected $source = 's';

    /**
     * Cached result of the validity check.
     */
    protected $valid = null;

    /**
     * Errors collected from various sources of validation.
     */
    protected $errors = array();

    /**
     * Answernotes collected from various sources of validation.
     */
    protected $answernotes = array();

    /**
     * Feedback collected from various sources of validation and processing.
     */
    protected $feedback = array();

    /**
     * The backreference to the location in the question model from which this
     * ast comes from. e.g., '/questionvariables' or '/prt/0/node/2/tans'.
     * more specific location data i.e. character position data is in the AST.
     */
    protected $context;

    /**
     * The cassecurity settings applied to this question.
     */
    protected $securitymodel;

    /**
     * Some AST-containers have keys but are still to be used like they had 
     * none. This is somewhat more complex behaviour connected to the new
     * cassession only returning the values of last statements with a given 
     * key.
     */
    protected $keyless = false;

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
            array $filteroptions = array(), string $grammar = 'Root') {

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
        $astc = new static;
        $astc->ast = $ast;
        $astc->source = 's';
        $astc->context = $context;
        $astc->securitymodel = $securitymodel;
        $astc->errors = $errors;
        $astc->answernotes = $answernotes;
        $astc->valid = null;
        $astc->feedback = array();
        return $astc;
    }

    public static function make_from_teacher_source(string $raw, string $context='',
            stack_cas_security $securitymodel=null) {
        // If you wonder why the security model is in play for teachers it
        // is here to bring in the information on whether units are constants
        // or not and thus affect the teachers ability to write into them.
        $errors = array();
        $answernotes = array();
        $parseroptions = array('startRule' => 'Root',
                               'letToken' => stack_string('equiv_LET'));

        if ($securitymodel === null) {
            $securitymodel = new stack_cas_security();
        }

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
        $astc = new static;
        $astc->ast = $ast;
        $astc->source = 't';
        $astc->context = $context;
        $astc->securitymodel = $securitymodel;
        $astc->errors = $errors;
        $astc->answernotes = $answernotes;
        $astc->valid = null;
        $astc->feedback = array();
        return $astc;
    }

    public static function make_from_teacher_ast(MP_Statement $ast, string $context,
            stack_cas_security $securitymodel) {
        // This function is intended to be used when dealing with keyvals,
        // as there one already has an AST representing multiple casstring
        // and can just split it to pieces.

        $errors = array();
        $answernotes = array();
        $filteroptions = array('998_security' => array('security' => 't'));
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(array('998_security', '999_strict'), $filteroptions, true);
        $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);

        $astc = new static;
        $astc->ast = $ast;
        $astc->source = 't';
        $astc->context = $context;
        $astc->securitymodel = $securitymodel;
        $astc->errors = $errors;
        $astc->answernotes = $answernotes;
        $astc->valid = null;
        $astc->feedback = array();
        return $astc;
    }



    protected function __constructor($ast, string $source, string $context,
                                   stack_cas_security $securitymodel,
                                   array $errors, array $answernotes) {

        $this->ast = $ast;
        $this->source = $source;
        $this->context = $context;
        $this->securitymodel = $securitymodel;
        $this->errors = $errors;
        $this->answernotes = $answernotes;
        $this->valid = null;
        $this->feedback = array();
        $this->keyless = false;

        if (!('s' === $source || 't' === $source)) {
            throw new stack_exception('stack_ast_container: source, must be "s" or "t" only.');
        }
    }

    public function set_keyless(bool $key=true) {
        $this->keyless = $key;
    }


    // Functions required by cas_evaluatable.
    public function get_valid(): bool {
        if ($this->valid === null) {
            if ($this->ast === null) {
                // In case parsing was impossible we store the errors in this class.
                $this->valid = false;
                return false;
            }

            // First check if the AST contains something marked as invalid.
            $hasinvalid = false;
            $findinvalid = function($node) use(&$hasinvalid) {
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

    public function set_cas_status(array $errors, array $answernotes, array $feedback) {
        // Here we have a slightly difficult situation, as the new
        // session collects real errors through different means than
        // the old they are truly separate from just printed out
        // things, the latter list is for those.
        // And in the former one we also need to handle the old way
        // of catching some key errors as answernotes.
        if (count($errors) > 0) {
            $this->errors = array_merge($this->errors, $errors);
            foreach ($errors as $value) {
                if ($value !== '' && $value !== null) {
                    $this->decode_maxima_errors($value);
                    $this->valid = false;
                }
            }
        }
        // TODO: should we not have somethign more logical than 
        // decode_maxima_errors handle the notes? They are not coming mixed
        // from the CAS? Same with errors and feedback.
        if (count($answernotes) > 0) {
            foreach ($answernotes as $value) {
                if ($value !== '' && $value !== null) {
                    $this->decode_maxima_errors($value);
                }
            }
        }
        if (count($feedback) > 0) {
            foreach ($feedback as $value) {
                if ($value !== '' && $value !== null) {
                    $this->feedback[] = $value;
                }
            }
        }
    }

    public function get_securitymodel(): stack_cas_security {
        return $this->securitymodel;
    }

    public function get_source_context(): string {
        return $this->context;
    }

    public function get_key(): string {
        if ($this->keyless === true) {
            return '';
        }

        $root = $this->ast;
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            } else {
                return '';
            }
        }
        if ($root instanceof MP_Statement) {
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation && $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            return $root->lhs->value;
        }

        return '';
    }


    // General accessors.
    public function get_errors($raw = 'implode') {
        if (null === $this->valid) {
            $this->get_valid();
        }
        if ($raw === 'implode') {
            return implode(' ', array_unique($this->errors));
        }
        return $this->errors;
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

    public function get_variable_usage(array &$updatearray = array()): array {
        // Find out which identifiers are being written to and which are being red from.
        // Simply go through the AST if it exists.
        if ($this->ast !== null) {
            $updatearray = maxima_parser_utils::variable_usage_finder($this->ast, $updatearray);
        }
        return $updatearray;
    }

    public function get_feedback($raw = 'implode') {
        if (null === $this->valid) {
            $this->get_valid();
        }
        if ($raw === 'implode') {
            $r = trim(implode(' | ', array_unique($this->feedback)));
            if (strrpos($r, '!NEWLINE!') === core_text::strlen($r) - 9) {
                $r = trim(core_text::substr($r, 0, -9));
                return $r;
            }
        }
        return $this->feedback;
    }

    // TODO: for whatever reason there was an unused function set_feedback, this
    // is for the same use case but now we store feedback in arrays so we add 
    // stuff... If feedback is purely CAS sourced remove this function.
    public function add_feedback(string $feedback) {
        $this->feedback[] = $feedback;
    }

    /**
     *  This function decodes the error generated by Maxima into meaningful notes.
     *  */
    public function decode_maxima_errors(string $error) {
        $searchstrings = array('DivisionZero', 'CommaError', 'Illegal_floats', 'Lowest_Terms', 'SA_not_matrix',
                'SA_not_list', 'SA_not_equation', 'SA_not_inequality', 'SA_not_set', 'SA_not_expression',
                'Units_SA_excess_units', 'Units_SA_no_units', 'Units_SA_only_units', 'Units_SA_bad_units',
                'Units_SA_errorbounds_invalid', 'Variable_function', 'Bad_assignment');
        $foundone = false;
        foreach ($searchstrings as $s) {
            if (false !== strpos($error, $s)) {
                if (array_search($s, $this->answernotes) === false) {
                    $this->answernotes[] = $s;
                }
                $foundone = true;
            }
        }
        if (!$foundone) {
            if (array_search('CASError: ' . $error, $this->answernotes) === false) {
                $this->answernotes[] = 'CASError: ' . $error;
            }
        }
    }

    /**
     * Handle some concatenations in error messages.
     */
    public function __toString() {
        return $this->get_evaluationform();
    }

    /**
     * Cloning is complex when we have object references.
     */
    public function __clone() {
        if ($this->ast !== null) {
            $this->ast = clone $this->ast;
        }
        if ($this->securitymodel !== null) {
            $this->securitymodel = clone $this->securitymodel;
        }
    }


    /**
     * Basic type checks, for checking if the expression is just one
     * object (ignoring content) of a given type.
     */
    public function is_int(bool $evaluated=false): bool {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return false;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        // For integers and floats we need to deal with prefix ops.
        if ($root instanceof MP_PrefixOp && 
            ($root->op === '-' || $root->op === '+')) {
            $root = $root->rhs;
        }
        if ($root instanceof MP_Integer) {
            return true;
        }
        return false;
    }

    public function is_float(bool $evaluated=false): bool {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return false;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        // For integers and floats we need to deal with prefix ops.
        if ($root instanceof MP_PrefixOp && 
            ($root->op === '-' || $root->op === '+')) {
            $root = $root->rhs;
        }
        if ($root instanceof MP_Float) {
            return true;
        }
        return false;
    }

    // exception of the bool value style, we return the length of
    // the list or -1 if not a list.
    public function is_list(bool $evaluated=false): int {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return -1;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        if ($root instanceof MP_List) {
            return count($root->items);
        }
        return -1;
    }

    public function is_string(bool $evaluated=false): bool {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return false;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        if ($root instanceof MP_String) {
            return true;
        }
        return false;
    }

    public function is_set(bool $evaluated=false): bool {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return false;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        if ($root instanceof MP_Set) {
            return true;
        }
        return false;
    }

    public function is_matrix(bool $evaluated=false): bool {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return false;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        if ($root instanceof MP_Functioncall &&
            $root->name instanceof MP_Identifier &&
            $root->name->value === 'matrix') {
            return true;
        }
        return false;
    }

    // Do not call this unless you are dealing with a list.
    // TODO: ?MP_Node for return type.
    public function get_list_element(int $index, bool $evaluated=false) {
        $root = $this->ast;
        if ($evaluated) {
            $root = $this->get_evaluated();
        }
        if ($root instanceof MP_Root) {
            if (array_key_exists(0, $root->items)) {
                $root = $root->items[0];
            }
        }
        if ($root instanceof MP_Statement) {
            if (count($root->flags) > 0) {
                // No matter what it is if there are flags its not pure anything.
                return null;
            }
            $root = $root->statement;
        }
        if ($root instanceof MP_Operation &&
            $root->op === ':' &&
            $root->lhs instanceof MP_Identifier) {
            $root = $root->rhs;
        }
        return $root->items[$index];
    }
}