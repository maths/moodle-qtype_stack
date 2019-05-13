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

// CAS strings and related functions.
//
// @copyright  2012 University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');
require_once(__DIR__ . '/casstring.units.class.php');
require_once(__DIR__ . '/cassecurity.class.php');
require_once(__DIR__ . '/parsingrules/factory.class.php');


class stack_cas_casstring {

    /** @var string as typed in by the user.
     *  This should not be changed by the system, so will contains things like ?, which are otherwise not permitted.
     */
    private $rawcasstring;

    /** @var string as modified by the validation.
     *       This string is suitable to be sent directly to Maxima.
     *       It will not contain ?, but these things will be replaced by tokens such as QMCHAR.
     */
    private $casstring;

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
     * @array of additional CAS strings which are conditions when the main expression can
     * be evaluated.  I.e. this encapsulates restrictions on the domain of the main value.
     * Same format as the $value string, and not designed to be read by end users.
     */
    private $conditions;

    /**
     * @var string how to display the CAS string, e.g. LaTeX. Only gets set
     *              after the casstring has been processed by the CAS, and the
     *              CAS function is an answertest.
     */
    private $feedback;

    /** @var bool if the string has passed validation. */
    private $valid = null;

    /** @var string */
    private $key;

    /** @var array Set the value of various contexts, e.g. whether the string has scientific units. */
    private $contexts = array();

    /** @var array any error messages to display to the user. */
    private $errors;

    /**
     * @var array Records logical information about the string, used for statistical
     *             anaysis of students' answers and recording types of error.
     */
    private $answernote;

    /**
     * If this casstring is an input about to be validated, then we need to store some information here.
     */
    private $validationcontext = null;

    /**
     * @var MPNode the parse tree presentation of this CAS string. Public for debug access...
     */
    public $ast = null;

    /**
     * Upper case Greek letters are allowed.
     */
    private static $greekupper = array(
        'Alpha' => true, 'Beta' => true, 'Gamma' => true, 'Delta' => true, 'Epsilon' => true,
        'Zeta' => true, 'Eta' => true, 'Theta' => true, 'Iota' => true, 'Kappa' => true, 'Lambda' => true,
        'Mu' => true, 'Nu' => true, 'Xi' => true, 'Omicron' => true, 'Pi' => true, 'Rho' => true,
        'Sigma' => true, 'Tau' => true, 'Upsilon' => true, 'Phi' => true, 'Chi' => true, 'Psi' => true,
        'Omega' => true);

    /**
     * @var all the characters permitted in responses.
     * Note, these are used in regular expression ranges, so - must be at the end, and ^ may not be first.
     */
    // @codingStandardsIgnoreStart
    private static $allowedchars =
            '0123456789,./\%&{}[]()$@!"\'?`^~*_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM:=><|: -';
    // @codingStandardsIgnoreEnd

    /**
     * @var all the permitted which are not allowed to be the final character.
     * Note, these are used in regular expression ranges, so - must be at the end, and ^ may not be first.
     */
    // @codingStandardsIgnoreStart
    private static $disallowedfinalchars = '/+*^#~=,_&`;:$-.<>';
    // @codingStandardsIgnoreEnd

    /**
     * @var all the permitted patterns in which spaces occur.  Simple find and replace.
     */
    private static $spacepatterns = array(
             ' or ' => 'STACKOR', ' and ' => 'STACKAND', 'not ' => 'STACKNOT',
             ' nounor ' => 'STACKNOUNOR', ' nounand ' => 'STACKNOUNAND');

    public function __debugInfo() {
        // For various reasons we do not want to print out certain things like the AST.
        // Make sure that if you add new fields you add them also here.
        return array(
            'rawcasstring' => $this->rawcasstring,
            'casstring' => $this->casstring,
            'value' => $this->value,
            'dispvalue' => $this->dispvalue,
            'conditions' => $this->conditions,
            'feedback' => $this->feedback,
            'valid' => $this->valid,
            'key' => $this->key,
            'contexts' => $this->contexts,
            'errors' => $this->errors,
            'answernote' => $this->answernote,
            'validationcontext' => $this->validationcontext
        );
    }


    public function __construct($rawstring, $conditions = null, $ast = null) {
        // If null the validation will need to parse, in case of keyval just give the statement from bulk parsing.
        // Also means that potenttial unparseable insert starts magick will need to be done.
        $this->ast            = &$ast;
        $this->rawcasstring   = $rawstring;
        $this->answernote     = array();
        $this->errors         = array();
        // If null then the validate command has not yet been run.
        $this->valid          = null;

        $this->contexts = array('units' => false, 'equivline' => false);

        if (!is_string($this->rawcasstring)) {
            throw new stack_exception('stack_cas_casstring: rawstring must be a string.');
        }
        $this->rawcasstring = $rawstring;
        if (!($conditions === null || is_array($conditions))) {
            throw new stack_exception('stack_cas_casstring: conditions must be null or an array.');
        }

        if ($conditions !== null && count($conditions) != 0) {
            $this->conditions   = $conditions;
        } else {
            $this->conditions   = array();
        }
    }

    /*********************************************************/
    /* Validation functions                                  */
    /*********************************************************/

    /* We may need to use this function more than once to validate with different options.
     * $security must either be 's' for student, or 't' for teacher.
     * $syntax is whether we enforce a "strict syntax".
     * $insertstars is whether we actually put stars into the places we expect them to go.
     *              0 - don't insert stars
     *              1 - insert stars
     *              2 - assume single letter variables only.
     * $secrules describes the allowed and forbidden identifiers and operators.
     */
    private function validate($security='s', $syntax=true, $insertstars=0, $secrules=null) {

        if (!('s' === $security || 't' === $security)) {
            throw new stack_exception('stack_cas_casstring: security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new stack_exception('stack_cas_casstring: syntax, must be Boolean.');
        }

        if (!is_int($insertstars)) {
            throw new stack_exception('stack_cas_casstring: insertstars, must be an integer.');
        }

        if ($secrules === null || $secrules === '') {
            $secrules = new stack_cas_security();
        }

        // Ensure that the security rules use the same units setting as this string.
        // NOTE: will probably cause debug nightmares until the units setting
        // gets a better defintion logic. It is not a setting that should be
        // distributed it should be a question level setting.
        $secrules->set_units($this->contexts['units']);

        $this->valid     = true;
        $this->casstring = $this->rawcasstring;

        // CAS strings must be non-empty.
        if (trim($this->casstring) == '') {
            $this->answernote[] = 'empty';
            $this->valid = false;
            return false;
        }

        // Now then do we already have validly parsed AST? if not what do we need to do to get one?
        // If we have then this is most certainly coming from keyval and $security better be 't'.
        if ($this->ast === null) {
            if ($security === 't') {
                try {
                    // Without logic_nouns_sort we need to deal with QMCHAR manually here.
                    $protected = $this->casstring;
                    if (core_text::strpos($this->casstring, '"')) {
                        $stringles = trim(stack_utils::eliminate_strings($this->casstring));
                        $stringles = str_replace('?', 'QMCHAR', $stringles);
                        $strings = stack_utils::all_substring_strings($this->casstring);
                        $string = $this->casstring;
                        if (count($strings) > 0) {
                            $split = explode('""', $stringles);
                            $stringbuilder = array();
                            $i = 0;
                            foreach ($strings as $string) {
                                $stringbuilder[] = $split[$i];
                                $stringbuilder[] = $string;
                                $i++;
                            }
                            $stringbuilder[] = $split[$i];
                            $protected = implode('"', $stringbuilder);
                        }
                    } else {
                        $protected = str_replace('?', 'QMCHAR', $this->casstring);
                    }

                    $this->ast = maxima_parser_utils::parse($protected);
                } catch (SyntaxError $e) {
                    $this->valid = false;
                    $this->teacher_parse_errors($e);
                    return false;
                }
            } else {
                $logic = stack_parsingrule_factory::get_parsingrule($insertstars);
                $parserule = 'Root';
                if ($this->contexts['equivline']) {
                    $parserule = 'Equivline';
                }
                $this->ast = $logic->parse($this->casstring, $this->valid, $this->errors, $this->answernote, $syntax,
                        $parserule, $this->contexts['units']);
                if ($this->ast === null) {
                    $this->valid = false;
                    return false;
                }
            }
        }

        // Check that we have only one statement. Should not have comments either.
        if ($this->ast instanceof MP_Root && count($this->ast->items) > 1) {
            // We either have comments, semicolons or even dollars in play.
            $comments = 0;
            foreach ($this->ast->items as $node) {
                if ($node instanceof MP_Comment) {
                    $comments++;
                }
            }
            if ((count($this->ast->items) - $comments) > 1) {
                // There are multiple statements not good.
                $this->add_error(stack_string('stackCas_forbiddenChar', array( 'char' => ';')));
                $this->answernote[] = 'forbiddenChar';
                $this->valid = false;
                return false;
            }
        }

        // Extract the key part out of the expression first so that our rules do not act on it.
        $this->key = '';
        $root = $this->ast;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        if ($root instanceof MP_Statement && $root->statement instanceof MP_Operation && $root->statement->op === ':') {
            $this->key = $root->statement->lhs->toString();
            $root->replace($root->statement, $root->statement->rhs);
        }

        // Minimal accuracy matching of mixed use.
        $usages = array('functions' => array(), 'variables' => array());

        // Let's do this in phases, first go through all identifiers. Rewrite things related to them.
        $processidentifiers = function($node) use($security, $secrules, $insertstars) {
            if ($node instanceof MP_Identifier) {
                return $this->process_identifier($node, $security, $secrules, $insertstars);
            }
            return true;
        };

        $processfunctioncalls = function($node) use($security, $syntax, $secrules, $insertstars) {
            if ($node instanceof MP_FunctionCall) {
                return $this->process_functioncall($node, $security, $syntax, $secrules, $insertstars);
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        // We repeat this until all is done. Identifiers first as they may turn into function calls.
        while ($this->ast->callbackRecurse($processidentifiers) !== true) {
            // Do nothing.
        }
        while ($this->ast->callbackRecurse($processfunctioncalls) !== true) {
            // Do nothing.
        }
        // @codingStandardsIgnoreEnd

        // Then the rest.
        $hasfloats = false;
        $mainloop = function($node) use($security, $secrules, $insertstars, &$usages, &$hasfloats) {
            if ($node instanceof MP_Identifier) {
                $this->check_characters($node->value);
                if ($node->is_function_name()) {
                    $usages['functions'][$node->value] = true;
                } else if (!($node->parentnode instanceof MP_Operation && $node->parentnode->op === '=' &&
                        $node->parentnode->lhs === $node)) {
                    $usages['variables'][$node->value] = true;
                }
            } else if ($node instanceof MP_PrefixOp || $node instanceof MP_PostfixOp || $node instanceof MP_Operation) {
                $this->check_operators($node);
            } else if ($node instanceof MP_Comment && $security === 's') {
                $this->valid = false;
                $a = array('cmd' => stack_maxima_format_casstring($op));
                $this->add_error(stack_string('stackCas_spuriousop', $a));
                $this->answernote[] = 'spuriousop';
            } else if ($node instanceof MP_EvaluationFlag && $security === 's') {
                // Some bad commas are parsed correctly as evaluation flags.
                $this->valid = false;
                $this->add_error(stack_string('stackCas_unencpsulated_comma'));
                $this->answernote[] = 'unencpsulated_comma';
            } else if ($node instanceof MP_Float) {
                $hasfloats = true;
            }
            return true;
        };

        $this->ast->callbackRecurse($mainloop);

        foreach ($usages['variables'] as $key => $duh) {
            if (isset($usages['functions'][$key])) {
                $this->answernote[] = 'Variable_function';
                $this->valid = false;
            }
        }

        // Move this check in here?
        // Yes sensible, but we can already pick that from the $mainloop, no need to
        // iterate too often.
        /*
        $floatspresent = false;
        $checkfloats = function($node) use (&$floatspresent){
            if ($node instanceof MP_Float) {
                $floatspresent = true;
                return false;
            }
            return true;
        };
        $this->ast->callbackRecurse($checkfloats);
        */

        // Security check contains various errors related to using functions as
        // variables that have already been covered in earlier checks so it
        // make sense to skip it if we are already invalid.
        if ($this->valid) {
            $this->check_security($security, $secrules);
        }

        // Lastly check certain context validation steps.
        if ($this->validationcontext !== null &&
            $this->validationcontext['forbidfloats'] === true &&
            $hasfloats) {
            $this->valid = false;
            $this->add_error(stack_string('Illegal_floats'));
        }

        $root = $this->ast;
        if ($this->ast instanceof MP_Root) {
            $root = $this->ast->items[0];
        }

        // Infer topmost type.
        $type = 'expression';
        $obj = $root;
        if ($obj instanceof MP_Statement) {
            $obj = $obj->statement;
        }
        if ($obj instanceof MP_Set) {
            $type = 'set';
        } else if ($obj instanceof MP_List) {
            $type = 'list';
        } else if ($obj instanceof MP_FunctionCall &&
              $obj->name instanceof MP_Identifier &&
              $obj->name->value === 'matrix') {
            $type = 'matrix';
        } else if ($obj instanceof MP_Operation && $obj->op === '=') {
            $type = 'equality';
        } else if ($obj instanceof MP_Operation && (
               $obj->op === '<' || $obj->op === '>' ||
               $obj->op === '>=' || $obj->op === '<=' || $obj->op === '#'
        )) {
            $type = 'inequality';
        }

        $this->ast = $root;
        $this->update_casstring($security);

        return $this->valid;
    }

    /* Use the ast to create a casstring. */
    private function update_casstring($security) {
        if ($security === 's') {
            $this->casstring = $this->ast->toString(array('nounify' => true));
        } else if ($security === 't') {
            $this->casstring = $this->ast->toString();
            if ($this->ast instanceof MP_Statement &&
                $this->ast->flags !== null && count($this->ast->flags) > 0) {
                // This makes it possible to write when authoring evaluation flags
                // like in maxima without wrapping in ev() yourself.
                $this->casstring = 'ev(' . $this->ast->toString() . ')';
            }
        }
    }

    private function process_identifier($id, $security, $secrules, $insertstars) {
        static $percentconstants = array('%e' => true, '%pi' => true, '%i' => true, '%j' => true,
                                             '%gamma' => true, '%phi' => true, '%and' => true,
                                             '%or' => true, '%union' => true);

        static $alwaysfunctiontrigs = array('sin' => true, 'cos' => true, 'tan' => true, 'sinh' => true, 'cosh' => true,
            'tanh' => true, 'sec' => true, 'cosec' => true, 'cot' => true, 'csc' => true, 'coth' => true, 'csch' => true,
            'sech' => true);
        static $alwaysfunctiontrigsa = array('asin' => true, 'acos' => true, 'atan' => true, 'asinh' => true,
            'acosh' => true, 'atanh' => true, 'asec' => true, 'acosec' => true, 'acot' => true, 'acsc' => true,
            'acoth' => true, 'acsch' => true, 'asech' => true);
        static $alwaysfunctiontrigsarc = array('arcsin' => true, 'arccos' => true, 'arctan' => true, 'arcsinh' => true,
            'arccosh' => true, 'arctanh' => true, 'arcsec' => true, 'arccosec' => true, 'arccot' => true, 'arccsc' => true,
            'arccoth' => true, 'arccsch' => true, 'arcsech' => true);

        static $alwaysfunctionother = array('log' => true, 'ln' => true, 'lg' => true, 'exp' => true, 'abs' => true,
            'sqrt' => true);

        // The return values here are false for structural changes and true otherwise.
        if ($id instanceof MP_Identifier) {
            $raw = $id->value;
            if (core_text::substr($raw, 0, 1) === '%') {
                // Is this a good constant?
                if (!isset($percentconstants[$raw])) {
                    $this->add_error(stack_string('stackCas_percent',
                        array('expr' => stack_maxima_format_casstring($this->casstring))));
                    $this->answernote[] = 'percent';
                    $this->valid   = false;
                    return true;
                }
            }
            if ($this->contexts['units']) {
                // These could still be in stack_cas_casstring_units, but why do a separate call
                // and we need that strutural change detection here.
                if ($id->value === 'Torr') {
                    $id->value = 'torr';
                    return true;
                } else if ($id->value === 'kgm') {
                    // TODO: Does this really need that '/s' in the original? Or is it due to regexp?
                    // If not just drop the ifs...
                    if ($id->parentnode instanceof MP_Operation &&
                        $id->parentnode->lhs === $id &&
                        $id->parentnode->op === '/') {
                        $operand = $id->parentnode->leftmostofright(); // This is here for /s^2.
                        if ($operand instanceof MP_Identifier && $operand->value === 's') {
                            $id->value = 'kg';
                            $id->parentnode->replace($id, new MP_Operation('*', $id, new MP_Identifier('m')));
                            return false;
                        }
                    }
                }
            }

            // TODO: is the name is a common function e.g. sqrt or sin and it is not a function-name
            // we should warn about it... in cases where we have no op on right...
            if ($id->is_function_name() && isset($alwaysfunctiontrigsarc[$raw])) {
                // Using arcsin(x) is bad go for asin(x).
                // TODO: we could write the whole function arguments and all here...
                // We might even fix/rename this but atleast Matti opposes that.
                // This test should logically be in the process_functioncall side but we already have
                // the identifier lists here...
                $this->add_error(stack_string('stackCas_triginv',
                array('badinv' => stack_maxima_format_casstring($raw),
                        'goodinv' => stack_maxima_format_casstring('a' . core_text::substr($raw, 3)))));
                $this->answernote[] = 'triginv';
                $this->valid = false;
                return true;
            }
            if ($id->parentnode instanceof MP_Indexing && $id->parentnode->target === $id && (isset($alwaysfunctionother[$raw])
                    || isset($alwaysfunctiontrigs[$raw]) || isset($alwaysfunctiontrigsa[$raw]))) {
                // Examples such as sin[x].
                // TODO: other-functions should probably be handled separately with a separate error...
                // TODO: we could write the whole function arguments and all here...
                // We might even fix but atleast Matti opposes that.
                $this->add_error(stack_string('stackCas_trigparens',
                    array('forbid' => stack_maxima_format_casstring($raw.'(x)'))));
                $this->answernote[] = 'trigparens';
                $this->valid = false;
                return true;
            }
            if ($id->parentnode instanceof MP_Operation && (isset($alwaysfunctionother[$raw])
                    || isset($alwaysfunctiontrigs[$raw]) || isset($alwaysfunctiontrigsa[$raw]))) {
                // TODO: other-functions should probably be handled separately with a separate error...
                if ($id->parentnode->lhs === $id) {
                    $op = $id->parentnode->op;
                    $this->valid = false;
                    if ($op === '^') {
                        $this->add_error(stack_string('stackCas_trigexp',
                            array('forbid' => stack_maxima_format_casstring($raw.'^'))));
                        $this->answernote[] = 'trigexp';
                        return true;
                    } else if ($op === '*' || $op === '+' || $op === '-' || $op === '/') {
                        if ($op === '*' && isset($id->parentnode->position['fixspaces'])) {
                            // Note the special case of inserted star on top of an space...
                            $this->add_error(stack_string('stackCas_trigspace',
                                array('trig' => stack_maxima_format_casstring($raw.'(...)'))));
                            $this->answernote[] = 'trigspace';
                            return true;
                        } else {
                            $this->add_error(stack_string('stackCas_trigop',
                                array('trig' => stack_maxima_format_casstring($raw),
                                    'forbid' => stack_maxima_format_casstring($raw.$op))));
                            $this->answernote[] = 'trigop';
                            return true;
                        }
                    } else if ($op === '=') {
                        // Actually you may use evaluation flags and other things to
                        // redefine functions: ev(lg(19),lg=logbasesimp).
                        $this->valid = true;
                        return true;
                    }
                } else {
                    $op = $id->parentnode->operationOnRight();
                    $this->valid = false;
                    if ($op === '*' || $op === '+' || $op === '-' || $op === '/') {
                        $this->add_error(stack_string('stackCas_trigop',
                            array('trig' => stack_maxima_format_casstring($fun),
                                  'forbid' => stack_maxima_format_casstring($fun.$op))));
                        $this->answernote[] = 'trigop';
                        return false;
                    }
                }
            }

            if (!$id->is_function_name() && core_text::substr($raw, 0, 4) === 'log_') {
                if ($id->parentnode instanceof MP_Operation && $id->parentnode->lhs === $id) {
                    // Examples such as log_...*(...).
                    if ($id->parentnode->op === '*' && $id->parentnode->rhs instanceof MP_Group) {
                        $nf = new MP_FunctionCall($id, $id->parentnode->rhs->items);
                        $id->parentnode->parentnode->replace($id->parentnode, $nf);
                        return false;
                    }
                    // Examples such aslog_...+zz(...).
                    if ($id->parentnode->rhs instanceof MP_FunctionCall) {
                        $nf = new MP_FunctionCall(new MP_Identifier($id->value . $id->parentnode->op
                                . $id->parentnode->rhs->name->toString()), $id->parentnode->rhs->arguments);
                        $id->parentnode->parentnode->replace($id->parentnode, $nf);
                        return false;
                    }
                    // Examples such aslog_...-a.
                    if ($id->parentnode->rhs instanceof MP_Atom) {
                        $ni = new MP_Identifier($id->value . $id->parentnode->op . $id->parentnode->rhs->toString());
                        $id->parentnode->parentnode->replace($id->parentnode, $ni);
                        return false;
                    }
                    // Examples such as log_...-a^b.
                    if ($id->parentnode->rhs instanceof MP_Operation && $id->parentnode->rhs->lhs instanceof MP_Atom) {
                        $ni = new MP_Identifier($id->value . $id->parentnode->op . $id->parentnode->rhs->lhs->toString());
                        $id->parentnode->rhs->replace($id->parentnode->rhs->lhs, $ni);
                        $id->parentnode->parentnode->replace($id->parentnode, $id->parentnode->rhs);
                        return false;
                    }

                }
            }

        } else {
            // Other params have been vetted already.
            throw new stack_exception('stack_cas_casstring: process_identifier: called with non identifier');
        }
        return true;
    }

    private function process_functioncall($fc, $security, $syntax, $secrules, $insertstars) {
        // The return values here are false for structural changes and true otherwise.
        if ($fc instanceof MP_FunctionCall) {
            $name = $fc->name;
            if ($name instanceof MP_Identifier) {
                // Known name branch.
                $name = $name->value;
                // Handle some renames.
                if ($name === 'log10') {
                    $fc->name->value = 'log_10';
                    $name = 'log_10';
                }
                if (core_text::substr($name, 0, 4) === 'log_') {
                    $num = core_text::substr($name, 4);
                    if (ctype_digit($num)) {
                        $fc->name->value = 'lg';
                        // Not actually replace this is append.
                        $fc->replace(-1, new MP_Integer((int)$num));
                    } else {
                        $fc->name->value = 'lg';
                        // Now things get difficult, but as we have just plugged terms to that part
                        // of the identifier we can just parse it as a casstring.
                        $operand = new MP_Identifier($num);
                        $cs = new stack_cas_casstring($num);
                        $cs->set_context('units', $this->contexts['units']);
                        if ($cs->get_valid($security, $syntax, $insertstars, $secrules)) {
                            // There are no evaluationflags here.
                            $operand = $cs->ast->statement;
                        } else {
                            if ($cs->ast !== null) {
                                $operand = $cs->ast->statement;
                            }
                            $this->valid = false;
                        }
                        foreach ($cs->answernote as $note) {
                            $this->answernote[] = $note;
                        }
                        foreach ($cs->errors as $err) {
                            $this->errors[] = $err;
                        }

                        // Not actually replace this is append.
                        $fc->replace(-1, $operand);
                    }
                    $this->answernote[] = 'logsubs';
                    return false;
                }
            } else {
                // Unknown name branch.
            }
        } else {
            // Other params have been vetted already.
            throw new stack_exception('stack_cas_casstring: process_functioncall: called with non functioncall');
        }
        return true;
    }

    private function check_characters($string) {
        // We are only checking identifiers now so no need for ops or newlines...
        // TODO: do we need to check? All the chars that go through the parser should work
        // with maxima... although π?

        // Only permit the following characters to be sent to the CAS.
        $allowedcharsregex = '~[^' . preg_quote(self::$allowedchars, '~') . ']~u';
        // Check for permitted characters.
        if (preg_match_all($allowedcharsregex, $string, $matches)) {
            $invalidchars = array();
            foreach ($matches as $match) {
                $badchar = $match[0];
                if (!array_key_exists($badchar, $invalidchars)) {
                    switch ($badchar) {
                        case "\n":
                            $invalidchars[$badchar] = "\\n";
                            break;
                        case "\r":
                            $invalidchars[$badchar] = "\\r";
                            break;
                        case "\t":
                            $invalidchars[$badchar] = "\\t";
                            break;
                        case "\v":
                            $invalidchars[$badchar] = "\\v";
                            break;
                        case "\e":
                            $invalidchars[$badchar] = "\\e";
                            break;
                        case "\f":
                            $invalidchars[$badchar] = "\\f";
                            break;
                        default:
                            $invalidchars[$badchar] = $badchar;
                    }
                }
            }
            $this->add_error(stack_string('stackCas_forbiddenChar', array( 'char' => implode(", ", array_unique($invalidchars)))));
            $this->answernote[] = 'forbiddenChar';
            $this->valid = false;
        }
    }

    private function check_operators($opnode) {
        // This gets tricky as the old one mainly focused to syntax errors...
        // But atleast we have the chained ones still.
        static $ineqs = array('>' => true, '<' => true, '<=' => true, '>=' => true, '=' => true);
        if ($opnode instanceof MP_Operation && isset($ineqs[$opnode->op])) {
            // TODO: This was security 's' in the old system, but that probably was only due to the test failing.
            if ($opnode->lhs instanceof MP_Operation && isset($ineqs[$opnode->lhs->op])
                    || $opnode->rhs instanceof MP_Operation && isset($ineqs[$opnode->rhs->op])) {
                $this->add_error(stack_string('stackCas_chained_inequalities'));
                $this->answernote[] = 'chained_inequalities';
                $this->valid = false;
            }
        }
        // 1..1, essentially a matrix multiplication of float of particular presentation.
        if ($opnode instanceof MP_Operation && $opnode->op === '.') {

            if ($opnode->lhs instanceof MP_Float && $opnode->rhs instanceof MP_Integer &&
                    substr($opnode->lhs->raw, -1, 1) === '.') {
                $this->valid = false;
                $a = array();
                $a['cmd']  = stack_maxima_format_casstring('..');
                $this->add_error(stack_string('stackCas_spuriousop', $a));
                $this->answernote[] = 'spuriousop';
            }

            $operand = $opnode->leftmostofright();
            if ($operand instanceof MP_Float && $operand->raw !== null &&
                    core_text::substr($operand->raw, 0, 1) === '.') {
                $this->valid = false;
                $a = array();
                $a['cmd']  = stack_maxima_format_casstring('..');
                $this->add_error(stack_string('stackCas_spuriousop', $a));
                $this->answernote[] = 'spuriousop';
            }
        }
    }

    /**
     * Check for forbidden CAS commands, based on security level
     *
     * @return bool|string true if passes checks if fails, returns string of forbidden commands
     */
    private function check_security($security, stack_cas_security $secrules) {
        // First extract things of interest from the tree, i.e. functioncalls,
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
                    if ($secrules->has_feature($node->name->value, 'mapfunction')) {
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
                            $this->add_error(stack_string('stackCas_deepmap'));
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
                        $this->add_error(stack_string('stackCas_callingasfunction',
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
                    $this->add_error(stack_string('stackCas_applyingnonobviousfunction',
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
                $this->add_error(stack_string('stackCas_qmarkoperators'));
                $this->answernote[] = 'qmark';
                $this->valid = false;
            } else if ($security === 's' && ($op === "'" || $op === "''")) {
                $this->add_error(stack_string('stackCas_apostrophe'));
                $this->answernote[] = 'apostrophe';
                $this->valid = false;
            } else if (!$secrules->is_allowed_as_operator($security, $op)) {
                $this->add_error(stack_string('stackCas_forbiddenOperator',
                        array('forbid' => stack_maxima_format_casstring($op))));
                $this->answernote[] = 'forbiddenOp';
                $this->valid = false;
            }
        }

        // Go through function calls.
        foreach (array_keys($functionnames) as $name) {
            // Special feedback for 'In' != 'ln' depends on the allow status of
            // 'In' that is why it is here.
            $vars = $secrules->get_case_variants($name, 'function');

            if ($security === 's' && $name === 'In' && !$secrules->is_allowed_word($name, 'function')) {
                $this->add_error(stack_string('stackCas_badLogIn'));
                $this->answernote[] = 'stackCas_badLogIn';
                $this->valid = false;
            } else if ($security === 's' && count($vars) > 0 && array_search($name, $vars) === false) {
                // Case sensitivity issues.
                $this->add_error(stack_string('stackCas_unknownFunctionCase',
                    array('forbid' => stack_maxima_format_casstring($name),
                          'lower' => stack_maxima_format_casstring(implode(', ', $vars)))));
                $this->answernote[] = 'unknownFunctionCase';
                $this->valid = false;
            } else if (!$secrules->is_allowed_to_call($security, $name)) {
                $this->add_error(stack_string('stackCas_forbiddenFunction',
                        array('forbid' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'forbiddenFunction';
                $this->valid = false;
            }
        }

        // Check for constants.
        foreach (array_keys($writtenvariables) as $name) {
            if ($secrules->has_feature($name, 'constant')) {
                // TODO: decide if we set this as validity issue, might break
                // materials where the constants redefined do not affect things.
                $this->add_error(stack_string('stackCas_redefinitionOfConstant',
                        array('constant' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'writingToConstant';
                $this->valid = false;
            }
            // Other checks happen at the $variables loop. These are all members of that.
        }

        if ($security === 's') {
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
                $this->add_error(stack_string('stackCas_forbiddenWord',
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
            if ($secrules->has_feature($name, 'operator')) {
                $this->add_error(stack_string('stackCas_operatorAsVariable',
                    array('op' => stack_maxima_format_casstring($name))));
                $this->answernote[] = 'operatorPlacement';
                $this->valid = false;
                continue;
            }

            if ($this->contexts['units']) {
                // Check for unit synonyms. Ignore if specifically allowed.
                list ($fndsynonym, $answernote, $synonymerr) = stack_cas_casstring_units::find_units_synonyms($name);
                if ($security == 's' && $fndsynonym && !$secrules->is_allowed_word($name)) {
                    $this->add_error($synonymerr);
                    $this->answernote[] = $answernote;
                    $this->valid = false;
                    continue;
                }
                $err = stack_cas_casstring_units::check_units_case($name);
                if ($err) {
                    // We have spotted a case sensitivity problem in the units.
                    $this->add_error($err);
                    $this->answernote[] = 'unknownUnitsCase';
                    $this->valid = false;
                    continue;
                }
            }

            if ($secrules->has_feature($name, 'globalyforbiddenvariable')) {
                // Very bad!
                $this->add_error(stack_string('stackCas_forbiddenWord',
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
            if ($security === 's' && !$secrules->is_allowed_to_read($security, $name)) {
                $keys = array();
                foreach (explode("_", $name) as $kw) {
                    $keys[$kw] = true;
                }
            }
            foreach (array_keys($keys) as $n) {
                if (!$secrules->is_allowed_to_read($security, $n)) {
                    if ($security === 't') {
                        $this->add_error(stack_string('stackCas_forbiddenWord',
                            array('forbid' => stack_maxima_format_casstring($n))));
                        $this->answernote[] = 'forbiddenWord';
                        $this->valid = false;
                    } else {
                        $vars = $secrules->get_case_variants($n, 'variable');
                        if (count($vars) > 0 && array_search($n, $vars) === false) {
                            $this->add_error(stack_string('stackCas_unknownVariableCase',
                                array('forbid' => stack_maxima_format_casstring($n),
                                'lower' => stack_maxima_format_casstring(
                                    implode(', ', $vars)))));
                            $this->answernote[] = 'unknownVariableCase';
                            $this->valid = false;
                        } else {
                            $this->add_error(stack_string('stackCas_forbiddenVariable',
                                array('forbid' => stack_maxima_format_casstring($n))));
                            $this->answernote[] = 'forbiddenVariable';
                            $this->valid = false;
                        }
                    }
                } else if (strlen($n) > 1) {
                    // We still need to try for case variants.
                    if ($security === 's') {
                        $vars = $secrules->get_case_variants($n, 'variable');
                        if (count($vars) > 0 && array_search($n, $vars) === false) {
                            $this->add_error(stack_string('stackCas_unknownVariableCase',
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
    }

    /*********************************************************/
    /* Internal utility functions                            */
    /*********************************************************/

    private function add_error($err) {
        $this->errors[] = trim($err);
    }

    /*********************************************************/
    /* Return and modify information                         */
    /*********************************************************/
    /* Note since iss324 this does not take raw allowords it takes stack_cas_security objects */
    public function get_valid($security = 's', $syntax = true, $insertstars = 0, $secrules = null) {
        if (null === $this->valid) {
            $this->validate($security, $syntax, $insertstars, $secrules);
        }
        return $this->valid;
    }

    public function set_valid($val) {
        $this->valid = $val;
    }

    public function get_errors($raw = 'implode') {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($raw === 'implode') {
            return implode(' ', array_unique($this->errors));
        }
        return $this->errors;
    }

    public function get_raw_casstring() {
        return $this->rawcasstring;
    }

    public function get_casstring() {
        if (null === $this->valid) {
            $this->validate();
        }
        return $this->casstring;
    }

    public function get_key() {
        if (null === $this->valid) {
            $this->validate();
        }
        return $this->key;
    }

    public function get_value() {
        return $this->value;
    }

    public function get_display() {
        return $this->display;
    }

    public function get_dispvalue() {
        return $this->dispvalue;
    }

    public function get_conditions() {
        return $this->conditions;
    }

    public function set_key($key, $appendkey=false) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ('' != $this->key && $appendkey) {
            $this->casstring = $this->key.':'.$this->casstring;
            $this->key = $key;
        } else {
            $this->key = $key;
        }
    }

    public function set_context($context, $val) {
        if (!isset($this->contexts[$context])) {
            throw new stack_exception('Tried to set a casstring context ' . $context . ' which does not exist');
        }
        $this->contexts[$context] = $val;
    }

    /* Traverse the ast, and add/remove all nounable nouns. */
    public function set_nounvalues($direction) {
        if ($this->ast === null) {
            return true;
        }
        $setnounvalues = function($node) use ($direction) {
            if ($node instanceof MP_Operation) {
                $op = $node->op;
                $feat = null;
                if ($direction == 'add') {
                    $feat = stack_cas_security::get_feature($node->op, 'nounoperator');
                }
                if ($direction == 'remove') {
                    $feat = stack_cas_security::get_feature($node->op, 'nounoperatorfor');
                }
                if ($feat !== null) {
                    $node->op = $feat;
                    return false;
                }
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        while ($this->ast->callbackRecurse($setnounvalues) !== true) {
            // Do nothing.
        }
        // @codingStandardsIgnoreEnd
        $this->update_casstring('t');
    }

    public function set_value($val) {
        $this->value = $val;
    }

    public function set_display($val) {
        $this->display = $this->translate_displayed_tex($val);
    }

    public function set_dispvalue($val) {
        $val = str_replace('"!! ', '', $val);
        $val = str_replace(' !!"', '', $val);
        // TODO, we might need to remove nouns here....
        $this->dispvalue = $val;
    }

    public function get_answernote($raw = 'implode') {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($raw === 'implode') {
            return trim(implode(' | ', array_unique($this->answernote)));
        }
        return $this->answernote;
    }

    public function set_answernote($val) {
        $this->answernote[] = $val;
    }

    public function get_feedback() {
        return $this->feedback;
    }

    public function set_feedback($val) {
        $this->feedback = $this->translate_displayed_tex($val);;
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

    // If we "CAS validate" this string, then we need to set various options.
    // If the teacher's answer is null then we use typeless validation, otherwise we check type.
    public function set_cas_validation_context($forbidfloats, $lowestterms, $tans, $validationmethod, $simp) {

        if (!($validationmethod == 'checktype' || $validationmethod == 'typeless' || $validationmethod == 'units'
                || $validationmethod == 'unitsnegpow' || $validationmethod == 'equiv' || $validationmethod == 'numerical')) {
                    throw new stack_exception('stack_cas_casstring: validationmethod must one of "checktype", "typeless", ' .
                            '"units" or "unitsnegpow" or "equiv" or "numerical", but received "'.$validationmethod.'".');
        }

        $this->validationcontext = array(
            'forbidfloats'     => $forbidfloats,
            'lowestterms'      => $lowestterms,
            'tans'             => $tans,
            'validationmethod' => $validationmethod,
            'simp'             => $simp
        );
    }

    public function get_cas_validation_context() {
        return $this->validationcontext;
    }

    /**
     *  Replace the contents of strings to the stringles version.
     */
    private function strings_replace($stringles) {
        // NOTE: This function should not exist, as this should only happen at the end of validate().
        // We still have some error messages that need it.
        $strings = stack_utils::all_substring_strings($this->rawcasstring);
        if (count($strings) > 0) {
            $split = explode('""', $stringles);
            $stringbuilder = array();
            $i = 0;
            foreach ($strings as $string) {
                $stringbuilder[] = $split[$i];
                $stringbuilder[] = $string;
                $i++;
            }
            $stringbuilder[] = $split[$i];
            $stringles = implode('"', $stringbuilder);
        }
        return $stringles;
    }

    /**
     *  This function decodes the error generated by Maxima into meaningful notes.
     *  */
    public function decode_maxima_errors($error) {
        $searchstrings = array('DivisionZero', 'CommaError', 'Illegal_floats', 'Lowest_Terms', 'SA_not_matrix',
                'SA_not_list', 'SA_not_equation', 'SA_not_inequality', 'SA_not_set', 'SA_not_expression',
                'Units_SA_excess_units', 'Units_SA_no_units', 'Units_SA_only_units', 'Units_SA_bad_units',
                'Units_SA_errorbounds_invalid', 'Variable_function', 'Bad_assignment');

        $foundone = false;
        foreach ($searchstrings as $s) {
            if (false !== strpos($error, $s)) {
                $this->set_answernote($s);
                $foundone = true;
            }
        }
        if (!$foundone) {
            $this->set_answernote('CASError: '.$error);
        }
    }

    private function teacher_parse_errors($e) {
        $errs = array();
        $ansnotes = array();
        stack_parser_logic_insertstars0::handle_parse_error($e, $this->rawcasstring,
                                                            $errs, $ansnotes);

        if (count($errs) > 0) {
            foreach ($errs as $err) {
                $this->add_error($err);
            }
        }
        if (count($ansnotes) > 0) {
            foreach ($ansnotes as $note) {
                $this->answernote[] = $note;
            }
        }
        if (count($this->errors) === 0) {
            // Nothing to say yet, lets throw this string through student validation and pick some errors from it.
            $csb = new stack_cas_casstring($this->rawcasstring);
            $csb->get_valid('s', true, 0);
            foreach ($csb->get_errors(false) as $err) {
                $this->add_error($err);
            }
        }
    }

    /**
     * Some of the TeX contains language tags which we need to translate.
     * @param string $str
     */
    private function translate_displayed_tex($str) {
        $dispfix = array('!LEFTSQ!' => '\left[', '!LEFTR!' => '\left(',
                '!RIGHTSQ!' => '\right]', '!RIGHTR!' => '\right)');
        // Need to add this in here also because strings may contain question mark characters.
        foreach ($dispfix as $key => $fix) {
            $str = str_replace($key, $fix, $str);
        }
        $loctags = array('ANDOR', 'SAMEROOTS', 'MISSINGVAR', 'ASSUMEPOSVARS', 'ASSUMEPOSREALVARS', 'LET',
                'AND', 'OR', 'NOT');
        foreach ($loctags as $tag) {
            $str = str_replace('!'.$tag.'!', stack_string('equiv_'.$tag), $str);
        }
        return $str;
    }

    /**
     * Remove the ast, and other clutter, so we can test equality cleanly and dump values.
     */
    public function test_clean() {
        $this->ast = null;
        return true;
    }
}
