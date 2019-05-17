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
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');


class stack_ast_container {

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

    public static function make_ast_container_from_student_source(string $raw, string $context,
            stack_cas_security $securitymodel, array $filters_to_apply,
            string $grammar = 'Root'): stack_ast_container {

        $errors = array();
        $answernotes = array();
        $parseroptions = array('startRule' => $grammar,
                               'letToken' => stack_string('equiv_LET'));

        // Use the corective parser as this comes from the student.
        $ast = maxima_corrective_parser::parse($raw, $errors, $answernotes, $parseroptions);

        // Get the filter pipeline. Even if we would not use it in case of 
        // ast = null, we still want to check that the request is valid.
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline($filters_to_apply, true);

        if ($ast !== null) {
            $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);
        } 
        // It is now ready to be created.
        $astc = new self;
        //new stack_ast_container($ast, 's', $context, $securitymodel, $errors, $answernotes);
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

    public static function make_ast_container_from_teacher_source(string $raw, string $context,
            stack_cas_security $securitymodel): stack_ast_container {
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
            // And that comes from the strict filter later
        }

        // Get the filter pipeline. Now we only want the core filtters and 
        // append the strict syntax check to the end.
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(array("999_strict"), true);

        if ($ast !== null) {
            $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);
        } 

        // It is now ready to be created.
        return new stack_ast_container($ast, 't', $context, $securitymodel, 
                                           $errors, $answernotes);
    }

    public static function make_ast_container_from_teacher_ast(MP_Statement $ast, string $context,
            stack_cas_security $securitymodel): stack_ast_container {
        // This function is intended to be used when dealing with keyvals, 
        // as there one already has an AST representing multiple casstring 
        // and can just split it to pieces.

        $errors = array();
        $answernotes = array();
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(array("999_strict"), true);
        $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);
        return new stack_cas_casstring_new($ast, 't', $context, $securitymodel, 
                                           $errors, $answernotes);
    }

    /**
       * The parsetree representing this ast after all modifications.
     */
    private $ast;

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
     * NOTE this really should be in a seprate subclass type, like those that do not generate
     * output... ALSO NOTE that castext2 no longer uses these at all so need is somewhat unknown.
     * @array of additional CAS strings which are conditions when the main expression can
     * be evaluated.  I.e. this encapsulates restrictions on the domain of the main value.
     * Same format as the $value string, and not designed to be read by end users.
     */
    private $conditions;

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
            throw new stack_exception('stack_cas_casstring: source, must be "s" or "t" only.');
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
            $has_invalid = false;
            $findinvalid = function($node)  use(&$has_invalid) {
                if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                    $has_invalid = true;
                    return false;
                }
                return true;
            };
            $this->ast->callbackRecurse($findinvalid, false);

            $this->valid = !$has_invalid;

            // Then do the whole security mess.
            $this->valid = $this->valid  && $this->check_security();
        }

        return $this->valid;
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

    public function get_answernotes($raw = 'implode') {
        if (null === $this->valid) {
            $this->get_valid();
        }
        if ($raw === 'implode') {
            return trim(implode(' | ', array_unique($this->answernotes)));
        }
        return $this->answernotes;
    }

    // This returns the fully filttered AST as it should be inputted were 
    // it inputted perfectly.
    public function get_inputform(): string {
        if ($this->ast) {
            return $this->ast->toString(array('inputform' => true, 'qmchar' => true));
        }
        return '';
    }

    // This returns the form that could be sent to CAS.
    // Note that this will do serious amount of work and could for example tie 
    // in the conditions if needed
    public function get_evaluationform(): string {
        if (false === $this->get_valid()) {
            throw new stack_exception('stack_cas_casstring: tried to get the evalution form of an invalid casstring.');
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
        // If this is an assignment type of an casstring we can return its 
        // target "key".
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

    // NOTE this is the "old" one an can be pruned a bit.
    private function check_security() {

        // First extract things of interest from the tree, i.e. function calls,
        // variable references and operations.
        $ofinterest = array();

        // For certain cases we want to know of commas. For this reason
        // certain structures need to be checked for them.
        $commas = false;
        $extraction = function($node) use (&$ofinterest, &$commas){
            if ($node instanceof MP_Identifier ||
                $node instanceof MP_FunctionCall ||
                $node instanceof MP_Operation ||
                $node instanceof MP_PrefixOp ||
                $node instanceof MP_PostfixOp) {

                $ofinterest[] = $node;
            }
            if (!$commas) {
                if ($node instanceof MP_FunctionCall && count($node->arguments) > 1) {
                    $commas = true;
                } else if (($node instanceof MP_Set || $node instanceof MP_List ||
                            $node instanceof MP_Group) && count($node->items) > 1) {
                    $commas = true;
                } else if ($node instanceof MP_EvaluationFlag) {
                    $commas = true;
                }
            }

            return true;
        };
        $this->ast->callbackRecurse($extraction);

        // Separate the identifiers we meet for latter use. Not the nodes
        // the string identifiers. Key is the value so unique from the start.
        $functionnames = array();
        $writtenvariables = array();
        $variables = array();
        $operators = array();

        // If we had commas in play add them to the operators.
        if ($commas) {
            $operators[','] = true;
        }

        // Now loop over the initially found things of interest. Note that
        // the list may grow as we go forward and unwrap things.
        $i = 0;
        while ($i < count($ofinterest)) {
            $node = $ofinterest[$i];
            $i = $i + 1;

            if ($node instanceof MP_Operation || $node instanceof MP_PrefixOp || $node instanceof MP_PostfixOp) {
                // We could just strip these out in the recurse but maybe we want
                // to check something in the future.
                $operators[$node->op] = true;
            } else if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                $variables[$node->value] = true;
                if ($node->is_being_written_to()) {
                    // This can be used to check if someone tries to redefine
                    // %pi or some other important thing.
                    $writtenvariables[$node->value] = true;
                }
            } else if ($node instanceof MP_FunctionCall) {
                $notsafe = true;
                if ($node->name instanceof MP_Identifier || $node->name instanceof MP_String) {
                    $notsafe = false;
                    $functionnames[$node->name->value] = true;
                    $safemap = false;
                    if ($this->securitymodel->has_feature($node->name->value, 'mapfunction')) {
                        // If it is an apply or map function throw it in for
                        // validation.
                        switch ($node->name->value) {
                            case 'apply':
                            case 'funmake':
                                $safemap = true;

                                // TODO: add errors about applying to wrong types
                                // of things and check them. For the other map
                                // functions to allow more to be done.

                            default:
                                // NOTE: this is a correct virtual form for only
                                // 'apply' and 'funmake' others will need to be
                                // written out as multiplce calls. And are
                                // therefore still unsafe atleast untill we do
                                // the writing out...
                                $virtualfunction = new MP_FunctionCall($node->arguments[0], array_slice($node->arguments, 1));
                                $virtualfunction->position['virtual'] = true;
                                $ofinterest[] = $virtualfunction;
                                break;
                        }
                        if (isset($node->position['virtual']) && !$safemap) {
                            // TODO: localise "Function application through mapping
                            // functions has depth limits as it hides things."
                            $this->errors[] = trim(stack_string('stackCas_deepmap'));
                            $this->answernote[] = 'deepmap';
                            $this->valid = false;
                        }
                    }

                } else if ($node->name instanceof MP_FunctionCall) {
                    $outter = $node->name;
                    if (($outter->name instanceof MP_Identifier || $outter->name instanceof MP_String)
                        && $outter->name->value === 'lambda') {
                        // This is safe, but we will not go out of our way to identify the function from further.
                        $notsafe = false;
                    } else {
                        // Calling the result of a function that is not lambda.
                        $this->errors[] = trim(stack_string('stackCas_callingasfunction',
                                                      array('problem' => stack_maxima_format_casstring($node->toString()))));
                        $this->answernote[] = 'forbiddenWord';
                        $this->valid = false;
                    }
                } else if ($node->name instanceof MP_Group) {
                    $outter = $node->name->items[count($node->name->items) - 1];
                    // We do this due to this (1,(cos,sin))(x) => sin(x).
                    $notsafe = false;
                    $virtualfunction = new MP_FunctionCall($outter, $node->arguments);
                    $virtualfunction->position['virtual'] = true;
                    $ofinterest[] = $virtualfunction;
                } else if ($node->name instanceof MP_Indexing) {
                    if (count($node->name->indices) === 1 && $node->name->target instanceof MP_List) {
                        $ind = -1;
                        if (count($node->name->indices[0]) === 1 && $node->name->indices[0]->items[0] instanceof MP_Integer) {
                            $ind = $node->name->indices[0]->items[0]->value - 1;
                        }
                        if ($ind >= 0 && $ind < count($node->name->target->items)) {
                            // We do this due to this because of examples such as [1,(cos,sin)][2](x) => sin(x).
                            $notsafe = false;
                            $virtualfunction = new MP_FunctionCall($node->name->target->items[$ind], $node->arguments);
                            $virtualfunction->position['virtual'] = true;
                            $ofinterest[] = $virtualfunction;
                        } else {
                            $notsafe = false;
                            foreach ($node->name->target->items as $id) {
                                $virtualfunction = new MP_FunctionCall($id, $node->arguments);
                                $virtualfunction->position['virtual'] = true;
                                $ofinterest[] = $virtualfunction;
                            }
                        }
                    }
                }
                if ($notsafe) {
                    // As in not safe identification of the function to be called.
                    $this->errors[] = trim(stack_string('stackCas_applyingnonobviousfunction',
                                                  array('problem' => $node->toString())));
                    $this->answernote[] = 'forbiddenWord';
                    $this->valid = false;
                }
            }
        }

        // Go through operators.
        foreach (array_keys($operators) as $op) {
            // First handle certain fixed special rules for ops.
            if ($op === '?' || $op === '?? ' || $op === '? ') {
                $this->errors[] = trim(stack_string('stackCas_qmarkoperators'));
                $this->answernote[] = 'qmark';
                $this->valid = false;
            } else if ($this->source === 's' && ($op === "'" || $op === "''")) {
                $this->errors[] = trim(stack_string('stackCas_apostrophe'));
                $this->answernote[] = 'apostrophe';
                $this->valid = false;
            } else if (!$this->securitymodel->is_allowed_as_operator($this->source, $op)) {
                $this->errors[] = trim(stack_string('stackCas_forbiddenOperator',
                        array('forbid' => stack_maxima_format_casstring($op))));
                $this->answernote[] = 'forbiddenOp';
                $this->valid = false;
            }
        }

        // Go through function calls.
        foreach (array_keys($functionnames) as $name) {
            // Special feedback for 'In' != 'ln' depends on the allow status of
            // 'In' that is why it is here.
            $vars = $this->securitymodel->get_case_variants($name, 'function');

            if ($this->source === 's' && $name === 'In' && !$this->securitymodel->is_allowed_word($name, 'function')) {
                $this->errors[] = trim(stack_string('stackCas_badLogIn'));
                $this->answernote[] = 'stackCas_badLogIn';
                $this->valid = false;
            } else if ($this->source === 's' && count($vars) > 0 && array_search($name, $vars) === false) {
                // Case sensitivity issues.
                $this->errors[] = trim(stack_string('stackCas_unknownFunctionCase',
                    array('forbid' => stack_maxima_format_casstring($name),
                          'lower' => stack_maxima_format_casstring(implode(', ', $vars)))));
                $this->answernote[] = 'unknownFunctionCase';
                $this->valid = false;
            } else if (!$this->securitymodel->is_allowed_to_call($this->source, $name)) {
                $this->errors[] = trim(stack_string('stackCas_forbiddenFunction',
                        array('forbid' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'forbiddenFunction';
                $this->valid = false;
            }
        }

        // Check for constants.
        foreach (array_keys($writtenvariables) as $name) {
            if ($this->securitymodel->has_feature($name, 'constant')) {
                // TODO: decide if we set this as validity issue, might break
                // materials where the constants redefined do not affect things.
                $this->errors[] = trim(stack_string('stackCas_redefinitionOfConstant',
                        array('constant' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'writingToConstant';
                $this->valid = false;
            }
            // Other checks happen at the $variables loop. These are all members of that.
        }

        if ($this->source === 's') {
            $emptyfungroup = array();
            $checkemptyfungroup = function($node) use (&$emptyfungroup) {
                // A function call with no arguments.
                if ($node instanceof MP_FunctionCall && count($node->arguments) === 0 ) {
                    $emptyfungroup[] = $node;
                }
                // A "group", programatic groups.
                if ($node instanceof MP_Group && count($node->items) === 0 ) {
                    $emptyfungroup[] = $node;
                }
                return true;
            };
            $this->ast->callbackRecurse($checkemptyfungroup);
            if (count($emptyfungroup) > 0) {
                $this->errors[] = trim(stack_string('stackCas_forbiddenWord',
                            array('forbid' => stack_maxima_format_casstring('()'))));
                $this->answernote[] = 'emptyParens';
                $this->valid = false;
            }
        }

        /*
         * The rules of student identifiers are as follows, applies to whole
         * identifier or its subparts:
         *   Phase 1:
         *   if forbidden identifier in security-map then false else
         *   if present in forbidden words or contains such then false else
         *   if strlen() == 1 then true else
         *   if author used key then false else
         *   if strlen() > 2 and in allowed words then true else
         *   if strlen() > 2 and in security-map then true else
         *   if ends with a number then true else false
         *  Phase 2:
         *   if phase 1 = false then false else
         *   if units and not unit name and is unit case variant then false else
         *   if not (know or in security-map) and case variant in security-map then false else
         *   true
         */
  
        // Check for variables.
        foreach (array_keys($variables) as $name) {
            // Check for operators like 'and' if they appear as variables
            // things have gone wrong.
            if ($this->securitymodel->has_feature($name, 'operator')) {
                $this->errors[] = trim(stack_string('stackCas_operatorAsVariable',
                    array('op' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'operatorPlacement';
                $this->valid = false;
                continue;
            }

            if (isset($this->context['units']) && $this->context['units']) {
                // Check for unit synonyms. Ignore if specifically allowed.
                list ($fndsynonym, $answernote, $synonymerr) = stack_cas_casstring_units::find_units_synonyms($name);
                if ($this->source == 's' && $fndsynonym && !$this->securitymodel->is_allowed_word($name)) {
                    $this->errors[] = trim($synonymerr);
                    $this->answernote[] = $answernote;
                    $this->valid = false;
                    continue;
                }
                $err = stack_cas_casstring_units::check_units_case($name);
                if ($err) {
                    // We have spotted a case sensitivity problem in the units.
                    $this->errors[] = trim($err);
                    $this->answernote[] = 'unknownUnitsCase';
                    $this->valid = false;
                    continue;
                }
            }

            if ($this->securitymodel->has_feature($name, 'globalyforbiddenvariable')) {
                // Very bad!
                $this->errors[] = trim(stack_string('stackCas_forbiddenWord',
                    array('forbid' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'forbiddenWord';
                $this->valid = false;
                continue;
            }

            // TODO: Did I understand the split by underscores right?
            // Could we do that split on the PHP side to ensure security
            // covering any possible construction of function calls?
            $keys = array($name => true);
            // If the whole thing is allowed no need to split it down.
            if ($this->source === 's' && !$this->securitymodel->is_allowed_to_read($this->source, $name)) {
                $keys = array();
                foreach (explode("_", $name) as $kw) {
                    $keys[$kw] = true;
                }
            }
            foreach (array_keys($keys) as $n) {
                if (!$this->securitymodel->is_allowed_to_read($this->source, $n)) {
                    if ($this->source === 't') {
                        $this->errors[] = trim(stack_string('stackCas_forbiddenWord',
                            array('forbid' => stack_maxima_format_casstring($n))));
                        $this->answernote[] = 'forbiddenWord';
                        $this->valid = false;
                    } else {
                        $vars = $this->securitymodel->get_case_variants($n, 'variable');
                        if (count($vars) > 0 && array_search($n, $vars) === false) {
                            $this->errors[] = trim(stack_string('stackCas_unknownVariableCase',
                                array('forbid' => stack_maxima_format_casstring($n),
                                'lower' => stack_maxima_format_casstring(
                                    implode(', ', $vars)))));
                            $this->answernote[] = 'unknownVariableCase';
                            $this->valid = false;
                        } else {
                            $this->errors[] = trim(stack_string('stackCas_forbiddenVariable',
                                array('forbid' => stack_maxima_format_casstring($n))));
                            $this->answernote[] = 'forbiddenVariable';
                            $this->valid = false;
                        }
                    }
                } else if (strlen($n) > 1) {
                    // We still need to try for case variants.
                    if ($this->source === 's') {
                        $vars = $this->securitymodel->get_case_variants($n, 'variable');
                        if (count($vars) > 0 && array_search($n, $vars) === false) {
                            $this->errors[] = trim(stack_string('stackCas_unknownVariableCase',
                                array('forbid' => stack_maxima_format_casstring($n),
                                'lower' => stack_maxima_format_casstring(
                                    implode(', ', $vars)))));
                            $this->answernote[] = 'unknownVariableCase';
                            $this->valid = false;
                        }
                    }
                }
            }
        }
        return true;
    }

}