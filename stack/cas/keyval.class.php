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

require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');
require_once(__DIR__ . '/cassession2.class.php');
require_once(__DIR__ . '/castext2/utils.php');
require_once(__DIR__ . '/../utils.class.php');

/**
 * Class to parse user-entered data into CAS sessions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_keyval {

    /** @var Holds the raw text as entered by a question author. */
    private $raw;

    /** @var array of stack_ast_container_silent */
    private $statements;

    /** @var bool */
    private $valid;

    /** @var array of error messages that can be displayed to the user. */
    private $errors;

    /** @var stack_cas_security shared security to use for validation */
    private $security;

    // For those using keyvals as a generator for sessions.
    private $options;
    private $seed;

    // For error mapping keyvals do need a context.
    private $context;

    /**
     * @var string the name of the error-wrapper-class, tunable for use in
     * other contexts, e.g. Stateful.
     */
    public $errclass = 'stack_cas_error';

    public function __construct($raw, $options = null, $seed=null, $ctx='') {
        $this->raw          = $raw;
        $this->statements   = array();
        $this->errors       = array();
        $this->options      = $options;
        $this->seed         = $seed;
        $this->context      = $ctx;
        $this->security     = new stack_cas_security();

        if (!is_string($raw)) {
            throw new stack_exception('stack_cas_keyval: raw must be a string.');
        }

        if (!is_null($options) && !is_a($options, 'stack_options')) {
            throw new stack_exception('stack_cas_keyval: options must be null or stack_options.');
        }

        if (!is_null($seed) && !is_int($seed)) {
            throw new stack_exception('stack_cas_keyval: seed must be a null or an integer.');
        }

    }

    private function validate($inputs) {

        if (empty($this->raw) || '' == trim($this->raw) || null == $this->raw) {
            $this->valid = true;
            return true;
        }

        // Protect things inside strings before we do QMCHAR tricks, and check for @, $.
        $str = maxima_parser_utils::remove_comments($this->raw);

        // Check again whether the string is now empty.
        if (trim($str) == '') {
            $this->valid = true;
            return true;
        }

        $strings = stack_utils::all_substring_strings($str);
        foreach ($strings as $key => $string) {
            $str = str_replace('"'.$string.'"', '[STR:'.$key.']', $str);
        }

        $str = str_replace('?', 'QMCHAR', $str);

        // CAS keyval may not contain @ or $ outside strings.
        // We should certainly prevent the $ to make sure statements are separated by ;, although Maxima does allow $.
        if (strpos($str, '@') !== false || strpos($str, '$') !== false) {
            $this->errors[] = new $this->errclass(stack_string('illegalcaschars'), $this->context);
            $this->valid = false;
            return false;
        }

        foreach ($strings as $key => $string) {
            $str = str_replace('[STR:'.$key.']', '"' .$string . '"', $str);
        }

        // 6/10/18 No longer split by line change, split by statement.
        // Allow writing of loops and other long statements onto multiple lines.
        $ast = maxima_parser_utils::parse_and_insert_missing_semicolons_with_includes($str);
        if (!$ast instanceof MP_Root) {
            // If not then it is a SyntaxError. Or an include error.
            if ($ast instanceof stack_exception) {
                $this->errors[] = new $this->errclass($ast->getMessage(), $this->context);
                $this->valid = false;
                return false;
            }
            $syntaxerror = $ast;
            $error = $syntaxerror->getMessage();
            if (isset($syntaxerror->grammarLine) && isset($syntaxerror->grammarColumn)) {
                $error .= ' (' . stack_string('stackCas_errorpos',
                        ['line' => $syntaxerror->grammarLine, 'col' => $syntaxerror->grammarColumn]) . ')';
            }
            $this->errors[] = new $this->errclass($error, $this->context);
            $this->valid = false;
            return false;
        }

        $vallist = array();
        // Mark inputs as specific type.
        if (is_array($inputs)) {
            foreach ($inputs as $name) {
                if (!isset($vallist[$name])) {
                    $vallist[$name] = [];
                }
                $vallist[$name][-2] = -2;
            }
        }
        $this->security->set_context($vallist);

        $this->valid   = true;
        $this->statements   = array();
        foreach ($ast->items as $item) {
            // Include might have brought in some comments. Even after we removed them from the source.
            if ($item instanceof MP_Statement) {
                $cs = stack_ast_container::make_from_teacher_ast($item, '', $this->security);
                $op = '';
                if ($item->statement instanceof MP_Operation) {
                    $op = $item->statement->op;
                }
                if ($item->statement instanceof MP_FunctionCall) {
                    $op = $item->statement->name->value;
                }
                // Context variables should always be silent.  We might need a separate feature "silent" in future.
                if (stack_cas_security::get_feature($op, 'contextvariable') !== null) {
                    $cs = stack_ast_container_silent::make_from_teacher_ast($item, '',
                            $this->security);
                }
                $this->valid = $this->valid && $cs->get_valid();
                $this->errors = array_merge($this->errors, $cs->get_errors('objects'));
                $this->statements[] = $cs;
            }
        }

        // Allow reference to inputs in the values of the question variables (otherwise we can't use them)!
        // Prevent reference to inputs in the keys.
        if (is_array($inputs)) {
            $usage = $this->get_variable_usage();
            foreach ($usage['write'] as $key => $used) {
                if (in_array($key, $inputs)) {
                    $this->valid = false;
                    $this->errors[] = new $this->errclass(stack_string('stackCas_inputsdefined', $key), $this->context);
                }
            }
        }

        return $this->valid;
    }

    /** Specify non default security, do this before validation. */
    public function set_security(stack_cas_security $security) {
        $this->security = clone $security;
    }

    /** Extract a security object with type related context information, do this after validation. */
    public function get_security(): stack_cas_security {
        return $this->security;
    }


    /*
     * @array $inputs Holds an array of the input names which are forbidden as keys.
     * @bool $inputstrict Decides if we should forbid any reference to the inputs in the values of variables.
     */
    public function get_valid($inputs = null) {
        if (null === $this->valid || is_array($inputs)) {
            $this->validate($inputs);
        }
        return $this->valid;
    }

    public function get_errors($casdebug = false, $raw = 'strings') {
        if (null === $this->valid) {
            $this->validate(null);
        }
        $errors = [];
        if ($raw === 'objects') {
            $errors = array_merge([], $this->errors);
            if ($casdebug) {
                $errors[] = new $this->errclass($this->session->get_debuginfo(), $this->context);
            }
            return $this->errors;
        } else {
            foreach ($this->errors as $err) {
                if ($err instanceof stack_cas_error) {
                    $errors[] = $err->get_legacy_error();
                } else {
                    $errors[] = $err;
                }
            }
            $errors = array_unique($errors);
        }

        if ($casdebug) {
            $errors[] = $this->session->get_debuginfo();
        }
        return $errors;
    }

    public function instantiate() {
        if (null === $this->valid) {
            $this->validate(null);
        }
        $cs = new stack_cas_session2($this->statements, $this->options, $this->seed);
        if ($cs->get_valid()) {
            $cs->instantiate();
        }
        // Return any runtime errors.
        return $cs->get_errors(true);
    }

    public function get_session() {
        if (null === $this->valid) {
            $this->validate(null);
        }
        return new stack_cas_session2($this->statements, $this->options, $this->seed);
    }

    public function get_variable_usage(array $updatearray = array()): array {
        if (!array_key_exists('read', $updatearray)) {
            $updatearray['read'] = array();
        }
        if (!array_key_exists('write', $updatearray)) {
            $updatearray['write'] = array();
        }
        foreach ($this->statements as $statement) {
            $updatearray = $statement->get_variable_usage($updatearray);
        }
        return $updatearray;
    }

    /**
     * Compiles the keyval to a single statement with substatement
     * error tracking wrappings. The wrappings can contain a context-name
     * so that one can read the error messages with references like:
     *     "question-variables line 4".
     *
     * Returns the statement as well as a listing of referenced
     * variables and functions for other tasks to use. Also splits
     * out so called "blockexternals".
     *
     * Note that one must have done validation in advance.
     */
    public function compile(string $contextname, castext2_static_replacer $map = null): array {
        $bestatements = [];
        $statements = [];
        $contextvariables = [];

        $referenced = ['read' => [], 'write' => [], 'calls' => []];

        if (null === $this->valid) {
            throw new stack_exception('stack_cas_keyval: must validate before compiling.');
        }
        if (false === $this->valid) {
            throw new stack_exception('stack_cas_keyval: must validate true before compiling.');
        }

        if (count($this->statements) == 0) {
            // If nothing return nothing, the logic outside will deal with null.
            return ['blockexternal' => null,
                    'statement' => null,
                    'references' => $referenced,
                    'contextvariables' => null];
        }

        // Now we start from the RAW form as rebuilding the line
        // references for AST-containers is not a simple thing and as
        // we plan for the future where we might include logic dealing
        // with comments as well.
        $str = $this->raw;
        // Similar QMCHAR protection as previously.
        $strings = stack_utils::all_substring_strings($str);
        foreach ($strings as $key => $string) {
            $str = str_replace('"'.$string.'"', '[STR:'.$key.']', $str);
        }

        $str = str_replace('?', 'QMCHAR', $str);

        foreach ($strings as $key => $string) {
            $str = str_replace('[STR:'.$key.']', '"' .$string . '"', $str);
        }

        // And then the parsing.
        $ast = maxima_parser_utils::parse_and_insert_missing_semicolons_with_includes($str);

        // Then we will build the filter chain for the syntax-candy. Repeat security checks just in case.
        // Note that we add special 600-series filters.
        $errors = [];
        $answernotes = [];
        $filteroptions = ['998_security' => ['security' => 't'],
            '601_castext' => ['context' => $contextname, 'errclass' => $this->errclass, 'map' => $map],
            '610_castext_static_string_extractor' => ['static string extractor' => $map],
            '995_ev_modification' => ['flags' => true]];
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(['601_castext',
            '602_castext_simplifier', '680_gcl_sconcat', '995_ev_modification',
            '996_call_modification', '998_security', '999_strict'],
            $filteroptions, true);
        $tostringparams = ['nosemicolon' => true, 'pmchar' => 1];
        $securitymodel = $this->security;

        // Apply the filters.
        $ast = $pipeline->filter($ast, $errors, $answernotes, $securitymodel);

        $includes = [];

        // Process the AST.
        foreach ($ast->items as $item) {
            if ($item instanceof MP_Statement) {

                // Strip off the %_C protection at the top level to establish if this is a context variable.
                if ($item->statement instanceof MP_Group) {
                    $r0 = $item->statement->items[0];
                    if ($r0 instanceof MP_FunctionCall && $r0->name->value = '%_C') {
                        $item->statement = $item->statement->items[1];
                    }
                }

                // Render to statement.
                $cn = $contextname . '/';
                // If it comes from an inclusion add that into the error scoping.
                if (isset($item->position['included-from'])) {
                    $cn = $cn . 'external:' . $item->position['included-from'] . ' ';
                }
                if (isset($item->position['included-src'])) {
                    $includes[$item->position['included-src']] = true;
                }
                $scope = stack_utils::php_string_to_maxima_string($cn .
                        $item->position['start'] . '-' . $item->position['end']);
                $payload = $item->toString($tostringparams);
                if ($item->flags !== null && count($item->flags) > 0) {
                    $payload = 'ev(' . $payload . ')';
                }
                $statement = '_EC(errcatch(' . $payload . '),' . $scope . ')';

                // Check if it is one of the block externals.
                $op = '';
                if ($item->statement instanceof MP_Operation) {
                    $op = $item->statement->op;
                }
                if ($item->statement instanceof MP_FunctionCall) {
                    $op = $item->statement->name->value;
                }
                if (stack_cas_security::get_feature($op, 'blockexternal') !== null) {
                    $bestatements[] = $statement;
                } else if (stack_cas_security::get_feature($op, 'contextvariable') !== null) {
                    $contextvariables[] = $statement;
                } else {
                    $statements[] = $statement;
                }
            }
        }

        // Update references.
        $referenced = maxima_parser_utils::variable_usage_finder($ast, $referenced);

        // Might be that broken things were found during the deepper processing.
        if (count($errors) > 0) {
            $this->valid = false;
            $this->errors = array_merge($this->errors, $errors);
        }

        // Construct the return value.
        if (count($bestatements) == 0) {
            $bestatements = null;
        } else {
            // These statement groups always end with a 'true' to ensure minimal output.
            $bestatements = '(' . implode(',', $bestatements) . ',true)';
        }
        if (count($statements) == 0) {
            $statements = null;
        } else {
            // These statement groups always end with a 'true' to ensure minimal output.
            $statements = '(' . implode(',', $statements) . ',true)';
        }
        if (count($contextvariables) == 0) {
            $contextvariables = null;
        } else {
            // These statement groups always end with a 'true' to ensure minimal output.
            $contextvariables = '(' . implode(',', $contextvariables) . ',true)';
        }

        if (count($includes) > 0) {
            // Now output them for use elsewhere.
            return ['blockexternal' => $bestatements,
                'statement' => $statements,
                'contextvariables' => $contextvariables,
                'references' => $referenced,
                'includes' => array_keys($includes)];
        }

        // Now output them for use elsewhere.
        return ['blockexternal' => $bestatements,
            'statement' => $statements,
            'contextvariables' => $contextvariables,
            'references' => $referenced];
    }
}
