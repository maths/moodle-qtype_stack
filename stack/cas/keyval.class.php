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

    // For those using keyvals as a generator for sessions.
    private $options;
    private $seed;

    public function __construct($raw, $options = null, $seed=null) {
        $this->raw          = $raw;
        $this->statements   = array();
        $this->errors       = array();
        $this->options      = $options;
        $this->seed         = $seed;

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

        if (empty($this->raw) or '' == trim($this->raw)) {
            $this->valid = true;
            return true;
        }

        // CAS keyval may not contain @ or $.
        if (strpos($this->raw, '@') !== false || strpos($this->raw, '$') !== false) {
            $this->errors[] = stack_string('illegalcaschars');
            $this->valid = false;
            return false;
        }

        // Subtle one: must protect things inside strings before we do QMCHAR tricks.
        $str = $this->raw;
        $strings = stack_utils::all_substring_strings($str);
        foreach ($strings as $key => $string) {
            $str = str_replace('"'.$string.'"', '[STR:'.$key.']', $str);
        }

        $str = str_replace('?', 'QMCHAR', $str);

        foreach ($strings as $key => $string) {
            $str = str_replace('[STR:'.$key.']', '"' .$string . '"', $str);
        }

        // 6/10/18 No longer split by line change, split by statement.
        // Allow writing of loops and other long statements onto multiple lines.
        $ast = maxima_parser_utils::parse_and_insert_missing_semicolons($str);
        if (!$ast instanceof MP_Root) {
            // If not then it is a SyntaxError.
            $syntaxerror = $ast;
            $error = $syntaxerror->getMessage();
            if (isset($syntaxerror->grammarLine) && isset($syntaxerror->grammarColumn)) {
                $error .= ' (' . stack_string('stackCas_errorpos',
                        ['line' => $syntaxerror->grammarLine, 'col' => $syntaxerror->grammarColumn]) . ')';
            }
            $this->errors[] = $error;
            $this->valid = false;
            return false;
        }

        $ast = maxima_parser_utils::strip_comments($ast);

        $this->valid   = true;
        $this->statements   = array();
        foreach ($ast->items as $item) {
            $cs = stack_ast_container::make_from_teacher_ast($item, '',
                    new stack_cas_security());
            $this->valid = $this->valid && $cs->get_valid();
            $this->errors = array_merge($this->errors, $cs->get_errors(true));
            $this->statements[] = $cs;
        }

        // Allow reference to inputs in the values of the question variables (otherwise we can't use them)!
        // Prevent reference to inputs in the keys.
        if (is_array($inputs)) {
            $usage = $this->get_variable_usage();
            foreach ($usage['write'] as $key => $used) {
                if (in_array($key, $inputs)) {
                    $this->valid = false;
                    $this->errors[] = stack_string('stackCas_inputsdefined', $key);
                }
            }
        }

        return $this->valid;
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

    public function get_errors($casdebug = false) {
        if (null === $this->valid) {
            $this->validate(null);
        }
        if ($casdebug) {
            $this->errors[] = $this->session->get_debuginfo();
        }
        return $this->errors;
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
     * Remove the ast, and other clutter from casstrings, so we can test equality cleanly and dump values.
     */
    public function test_clean() {
        $this->session->test_clean();
        return true;
    }
}
