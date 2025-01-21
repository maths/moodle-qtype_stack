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

// Note that is a complete rewrite of cassession, in this we generate
// no "caching" in the form of keyval representations as we do not
// necessarily return enough information from the CAS to do that, for
// that matter neither did the old one...
//
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/connectorhelper.class.php');
require_once(__DIR__ . '/../options.class.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/caserror.class.php');

class stack_cas_session2 {
    /**
     * @var string separator used between successive CAS commands inside the block.
     */
    const SEP = ",\n  ";

    /**
     * @var cas_evaluatable[]
     */
    private $statements;

    /**
     * @var bool
     */
    private $instantiated;

    /**
     * @var stack_options
     */
    private $options;

    /**
     * @var int
     */
    private $seed;

    /**
     * @var string
     *
     * In the event of a timeout (the only session level error) this is as much raw output
     * from Maxima as we can manage to reasonably get back.
     */
    private $timeoutdebug = '';

    /**
     * @var string the name of the error-wrapper-class, tuneable for use in
     * other contexts, e.g. Stateful.
     */
    public $errclass = 'stack_cas_error';

    public function __construct(array $statements, $options = null, $seed = null) {

        $this->instantiated = false;
        $this->statements = $statements;

        foreach ($statements as $statement) {
            if (!is_subclass_of($statement, 'cas_evaluatable')) {
                throw new stack_exception('stack_cas_session: items in $statements must be cas_evaluatable.');
            }
        }

        if ($options === null) {
            $this->options = new stack_options();
        } else if (is_a($options, 'stack_options')) {
            $this->options = $options;
        } else {
            throw new stack_exception('stack_cas_session: $options must be stack_options.');
        }

        if (!($seed === null)) {
            if (is_int($seed)) {
                $this->seed = $seed;
            } else {
                throw new stack_exception('stack_cas_session: $seed must be a number.  Got "'.$seed.'"');
            }
        } else {
            $this->seed = time();
        }
    }

    public function get_session(): array {
        return $this->statements;
    }

    public function get_contextvariables(): array {
        $ret = [];
        foreach ($this->statements as $statement) {
            if (method_exists($statement, 'is_toplevel_property') &&
                $statement->is_toplevel_property('contextvariable')) {
                    $ret[] = $statement;
            }
        }
        return $ret;
    }

    public function get_options(): stack_options {
        return $this->options;
    }

    public function add_statement(cas_evaluatable $statement, bool $append = true) {
        if ($append) {
            $this->statements[] = $statement;
        } else {
            array_unshift($this->statements, $statement);
        }
        $this->instantiated = false;
    }

    public function add_statements(array $statements, bool $append = true) {
        foreach ($statements as $statement) {
            if (!is_subclass_of($statement, 'cas_evaluatable')) {
                throw new stack_exception('stack_cas_session: items in $statements must be cas_evaluatable.');
            }
        }

        if ($append) {
            $this->statements = array_merge($this->statements, $statements);
        } else {
            $this->statements = array_merge($statements, $this->statements);
        }
        $this->instantiated = false;
    }

    /**
     * Add all the statements from this session at the start of the target session.
     *
     * @param stack_cas_session2 $target
     */
    public function prepend_to_session(stack_cas_session2 $target) {
        $target->statements = array_merge($this->statements, $target->statements);
        $target->instantiated = false;
    }

    /**
     * Add all the statements from this session at the end of the target session.
     *
     * @param stack_cas_session2 $target
     */
    public function append_to_session(stack_cas_session2 $target) {
        $target->statements = array_merge($target->statements, $this->statements);
        $target->instantiated = false;
    }

    public function get_variable_usage(array $updatearray = []): array {
        foreach ($this->statements as $statement) {
            $updatearray = $statement->get_variable_usage($updatearray);
        }
        return $updatearray;
    }

    public function is_instantiated(): bool {
        return $this->instantiated;
    }

    public function get_valid(): bool {
        $valid = true;
        foreach ($this->statements as $statement) {
            if ($statement->get_valid() === false) {
                $valid = false;
            }
        }
        // There is nothing wrong with an empty session.
        return $valid;
    }

    /*
     * TO-DO: set return value of : ?cas_evaluatable
     */
    public function get_by_key(string $key) {
        // Searches from the statements the last one with a given key.
        // This is a concession for backwards compatibility and should not be used.
        $found = null;
        foreach ($this->statements as $statement) {
            if ($statement->get_key() === $key) {
                $found = $statement;
            }
        }
        return $found;
    }

    /**
     * Returns all the errors related to the session.
     * This includes errors validating castrings prior to instantiation.
     * And it includes any runtime errors, specifically if we get nothing back.
     */
    public function get_errors($implode = true, $withcontext = true) {
        $errors = [];

        if ($this->timeoutdebug !== '') {
            $errors[] = [stack_string('stackCas_failedtimeout')];
        }

        foreach ($this->statements as $num => $statement) {
            $err = $statement->get_errors('implode');
            if ($err) {
                $errors[$num] = $statement->get_errors(false);
            }
        }

        /* Make sure each error is only reported once. */
        $unique = [];
        foreach ($errors as $errs) {
            foreach ($errs as $err) {
                $unique[$err] = true;
            }
        }
        $unique = array_keys($unique);
        if ($implode == true) {
            return implode(' ', $unique);
        }
        return $unique;
    }

    /**
     * Executes this session and returns the values to the statements that
     * request them.
     * Returns true if everything went well.
     */
    public function instantiate(): bool {
        if (!$this->get_valid()) {
            throw new stack_exception('stack_cas_session: cannot instantiate invalid session');
        }
        if (count($this->statements) === 0 || $this->instantiated === true) {
            $this->instantiated = true;
            return true;
        }

        // Let's simply build the expression here.
        // NOTE that we do not even bother trying to protect the global scope
        // as we have not seen anyone using the same CAS process twice, should
        // that become necessary much more would need to be done. But the parser
        // can handle that if need be.
        $collectvalues = [];
        $collectlatex = [];
        $collectdvs = [];
        $collectdvsandvalues = [];

        foreach ($this->statements as $num => $statement) {
            $dvv = false;
            if ($statement instanceof cas_value_extractor || $statement instanceof cas_raw_value_extractor) {
                if ($statement instanceof cas_display_value_extractor) {
                    $dvv = true;
                    if ($statement->get_key() === '') {
                        $collectdvsandvalues['__s' . $num] = $statement;
                    } else {
                        $collectdvsandvalues[$statement->get_key()] = $statement;
                    }
                } else {
                    if ($statement->get_key() === '') {
                        $collectvalues['__s' . $num] = $statement;
                    } else {
                        $collectvalues[$statement->get_key()] = $statement;
                    }
                }
            }
            if ($statement instanceof cas_latex_extractor) {
                if ($statement->get_key() === '') {
                    $collectlatex['__s' . $num] = $statement;
                } else {
                    $collectlatex[$statement->get_key()] = $statement;
                }
            }
            if ($dvv === false && ($statement instanceof cas_display_value_extractor)) {
                if ($statement->get_key() === '') {
                    $collectdvs['__s' . $num] = $statement;
                } else {
                    $collectdvs[$statement->get_key()] = $statement;
                }
            }
        }

        // We will build the whole command here.
        // No protection in the block.
        $preblock = '';
        $command = 'block([stack_seed]' .
                self::SEP . 'stack_randseed(' . $this->seed . ')';
        // Make the value of the seed available in the session.
        $command .= self::SEP . 'stack_seed:' . $this->seed;
        // The options.
        $command .= $this->options->get_cas_commands()['commands'];
        // Some parts of logic storage.
        $command .= self::SEP . '_RESPONSE:["stack_map"]';
        $command .= self::SEP . '_VALUES:["stack_map"]';
        if (count($collectlatex) > 0) {
            $command .= self::SEP . '_LATEX:["stack_map"]';
        }
        if ((count($collectdvs) + count($collectdvsandvalues)) > 0) {
            $command .= self::SEP . '_DVALUES:["stack_map"]';
        }

        // Set some values.
        $command .= self::SEP . '_CS2v("__stackmaximaversion",stackmaximaversion)';

        // Evaluate statements.
        foreach ($this->statements as $num => $statement) {
            $command .= self::SEP . '%stmt:' . stack_utils::php_string_to_maxima_string('s' . $num);
            $ef = $statement->get_evaluationform();
            $line = '_EC(errcatch(' . $ef . '),';
            $key = null;
            if (($statement instanceof cas_value_extractor ||
                    $statement instanceof cas_raw_value_extractor) ||
                    ($statement instanceof cas_latex_extractor) ||
                    ($statement instanceof cas_display_value_extractor)) {
                // One of those that need to be collected later.
                if (($key = $statement->get_key()) === '') {
                    $key = '__s' . $num;
                    $line = '_EC(errcatch(__s' . $num . ':' . $ef . '),';
                }
            }
            $line .= stack_utils::php_string_to_maxima_string($statement->get_source_context());
            $line .= ')';

            if (($statement instanceof stack_secure_loader && $statement->get_blockexternal()) ||
                (method_exists($statement, 'is_toplevel_property') && $statement->is_toplevel_property('blockexternal'))) {
                $preblock .= 'errcatch(' . $ef . ")$\n";
            } else {
                $command .= self::SEP . $line;
            }

            // If this is something that needs its LaTeX value collect it.
            // Now while the settings are correct. Only the last statements.
            if ($statement instanceof cas_latex_extractor) {
                if ($collectlatex[$key] === $statement) {
                    $command .= self::SEP . '_CS2l(';
                    $command .= stack_utils::php_string_to_maxima_string($key);
                    $command .= ',' . $key . ')';
                }
            }
        }
        // Collect values if required.
        foreach ($collectvalues as $key => $statement) {
            $command .= self::SEP . '_CS2v(';
            $command .= stack_utils::php_string_to_maxima_string($key);
            $command .= ',' . $key . ')';
        }
        foreach ($collectdvs as $key => $statement) {
            $command .= self::SEP . '_CS2dv(';
            $command .= stack_utils::php_string_to_maxima_string($key);
            $command .= ',' . $key . ')';
        }
        foreach ($collectdvsandvalues as $key => $statement) {
            $command .= self::SEP . '_CS2dvv(';
            $command .= stack_utils::php_string_to_maxima_string($key);
            $command .= ',' . $key . ')';
        }

        // Pack optional values to the response.
        if (count($collectlatex) > 0) {
            $command .= self::SEP . '_RESPONSE:stackmap_set(_RESPONSE,"presentation",_LATEX)';
        }
        if ((count($collectdvs) + count($collectdvsandvalues)) > 0) {
            $command .= self::SEP . '_RESPONSE:stackmap_set(_RESPONSE,"display",_DVALUES)';
        }

        // Then output the response.
        $command .= self::SEP . '_CS2out()';
        $command .= "\n)$\n";

        // Prepend those statements which should be outside the block.
        $command = $preblock . $command;
        // Send it to CAS.
        $connection = stack_connection_helper::make();
        $results = $connection->json_compute($command);
        // Let's collect what we got.
        $asts = [];
        $latex = [];
        $display = [];
        if (!isset($results['timeout']) || $results['timeout'] === true) {
            if (array_key_exists('timeoutdebug', $results)) {
                $this->timeoutdebug = $results['timeoutdebug'];
            }
            foreach ($this->statements as $num => $statement) {
                $errors = [new $this->errclass(stack_string('stackCas_failedtimeout'), '')];
                $statement->set_cas_status($errors, [], []);
            }
            return false;
        }

        if (array_key_exists('values', $results)) {
            foreach ($results['values'] as $key => $value) {
                if (is_string($value)) {
                    try {
                        if (!isset($collectvalues[$key]) || $collectvalues[$key] instanceof cas_value_extractor) {
                            $ast = maxima_parser_utils::parse($value, 'Root', false);
                            // Let's unpack the MP_Root immediately.
                            $asts[$key] = $ast->items[0];
                        } else {
                            $asts[$key] = $value;
                        }
                    } catch (Exception $e) {
                        // TODO: issue #1279 would change this exception to add in an error associated
                        // with the values collected rather than a stack_exception.
                        // We would then add something like this to allow the process to continue.
                        // $asts[$key] = maxima_parser_utils::parse('null', 'Root', false);
                        throw new stack_exception('stack_cas_session: tried to parse the value ' .
                                $value . ', but got the following exception ' . $e->getMessage());
                    }
                }
            }
        }
        if (array_key_exists('presentation', $results)) {
            foreach ($results['presentation'] as $key => $value) {
                if (is_string($value)) {
                    $latex[$key] = $value;
                }
            }
        }
        if (array_key_exists('display', $results)) {
            foreach ($results['display'] as $key => $value) {
                if (is_string($value)) {
                    $display[$key] = $value;
                }
            }
        }
        // Then push those to the objects we are handling.
        foreach ($this->statements as $num => $statement) {
            $err = [];
            if (array_key_exists('errors', $results)) {
                if (array_key_exists('s' . $num, $results['errors'])) {
                    foreach ($results['errors']['s' . $num] as $errs) {
                        // The first element is a list of errors declared
                        // at a given position in the logic.
                        // There can be multiple errors from multiple positions.
                        // The second element is the position.
                        foreach ($errs[0] as $er) {
                            $err[] = new $this->errclass(stack_utils::maxima_translate_string($er), $errs[1]);
                        }
                    }
                }
            }
            // Check for ignores.
            $last = null;
            $errb = [];
            foreach ($err as $error) {
                if (strpos($error->get_legacy_error(), 'STACK: ignore previous error.') !== false) {
                    $last = null;
                } else {
                    if ($last !== null) {
                        $errb[] = $last;
                    }
                    $last = $error;
                }
            }
            if ($last !== null) {
                $errb[] = $last;
            }
            $err = $errb;

            $answernotes = [];
            if (array_key_exists('notes', $results)) {
                if (array_key_exists('s' . $num, $results['notes'])) {
                    $answernotes = $results['notes']['s' . $num];
                }
            }
            $feedback = [];
            if (array_key_exists('feedback', $results)) {
                if (array_key_exists('s' . $num, $results['feedback'])) {
                    $feedback = $results['feedback']['s' . $num];
                }
            }
            $statement->set_cas_status($err, $answernotes, $feedback);
        }

        // Check the Maxima versions match and fail badly if not.
        if (array_key_exists('__stackmaximaversion', $results['values'])) {
            $usedversion = $results['values']['__stackmaximaversion'];
            $config = stack_utils::get_config();
            if ($usedversion !== $config->stackmaximaversion) {
                $errors = [
                    new $this->errclass(stack_string_error('healthchecksstackmaximaversionmismatch',
                    ['fix' => '', 'usedversion' => $usedversion, 'expectedversion' => $config->stackmaximaversion]), ''),
                ];
                foreach ($this->statements as $num => $statement) {
                    $statement->set_cas_status($errors, [], []);
                }
            }
        }

        foreach ($collectvalues as $key => $statement) {
            $statement->set_cas_evaluated_value($asts[$key]);
        }
        foreach ($collectlatex as $key => $statement) {
            $statement->set_cas_latex_value($latex[$key]);
        }
        foreach ($collectdvs as $key => $statement) {
            $statement->set_cas_display_value($display[$key]);
        }
        foreach ($collectdvsandvalues as $key => $statement) {
            $statement->set_cas_evaluated_value($asts[$key]);
            $statement->set_cas_display_value($display[$key]);
        }
        $this->instantiated = true;

        return $this->instantiated;
    }

    /*
     * This representation is only used in debugging questions, and for
     * offline (sandbox) testing.  We need to provide teachers with something
     * they can type back into Maxima.
     */
    public function get_keyval_representation($evaluatedvalues = false): string {
        $keyvals = '';
        $params = [
            'checkinggroup' => true,
            'qmchar' => false,
            'pmchar' => false,
            'nosemicolon' => true,
            'keyless' => false,
            'dealias' => true, // This is needed to stop pi->%pi etc.
            'nounify' => 0,
            'nontuples' => false,
        ];

        foreach ($this->statements as $statement) {
            if ($evaluatedvalues) {
                if (is_a($statement, 'stack_ast_container') && $statement->is_correctly_evaluated()) {
                    // Only print out variables with a key, to display their values.
                    $key = trim($statement->get_key());
                    if ($key !== '') {
                        $keyvals .= $key . ':' . trim($statement->get_value()) . ";\n";
                    }
                }
            } else {
                if ($statement->get_valid()) {
                    $val = trim($statement->ast_to_string(null, $params));
                    $keyvals .= $val . ";\n";
                } else {
                    $keyvals .= "/* " . stack_string('stackInstall_testsuite_errors') . " */\n";
                    $keyvals .= "/* " . trim($statement->get_errors()) . " */\n";
                }
            }
        }
        return trim($keyvals);
    }

    public function get_debuginfo() {
        if (trim($this->timeoutdebug ?? '') !== '') {
            return $this->timeoutdebug;
        }
        return '';
    }
}
