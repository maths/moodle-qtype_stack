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

/**
 * "key=value" class to parse user-entered data into CAS sessions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_keyval {

    /** @var Holds the raw text as entered by a question author. */
    private $raw;

    /** @var stack_cas_session */
    private $session;

    /** @var bool */
    private $valid;

    /** @var bool has this been sent to the CAS yet? */
    private $instantiated;

    /** @var string HTML error message that can be displayed to the user. */
    private $errors;

    /** @var string 's' or 't' for student or teacher security level. */
    private $security;

    /** @var bool whether to insert *s where there are implied multipliations. */
    private $insertstars;

    /** @var bool if true, apply strict syntax checks. */
    private $syntax;

    public function __construct($raw, $options = null, $seed=null, $security='s', $syntax=true, $insertstars=0) {
        $this->raw          = $raw;
        $this->security     = $security;
        $this->syntax       = $syntax;
        $this->insertstars  = $insertstars;

        $this->session      = new stack_cas_session(null, $options, $seed);

        if (!is_string($raw)) {
            throw new stack_exception('stack_cas_keyval: raw must be a string.');
        }

        if (!('s' === $security || 't' === $security)) {
            throw new stack_exception('stack_cas_keyval: 2nd argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new stack_exception('stack_cas_keyval: 5th argument, syntax, must be boolean.');
        }

        if (!is_int($insertstars)) {
            throw new stack_exception('stack_cas_keyval: 6th argument, stars, must be an integer.');
        }
    }

    private function validate($inputs) {
        if (empty($this->raw) or '' == trim($this->raw)) {
            $this->valid = true;
            return true;
        }

        // CAS keyval may not contain @ or $.
        if (strpos($this->raw, '@') !== false || strpos($this->raw, '$') !== false) {
            $this->errors = stack_string('illegalcaschars');
            $this->valid = false;
            return false;
        }

        // Subtle one: must protect things inside strings before we explode.
        $str = $this->raw;
        $strings = stack_utils::all_substring_strings($str);
        foreach ($strings as $key => $string) {
            $str = str_replace('"'.$string.'"', '[STR:'.$key.']', $str);
        }

        $str = str_replace("\n", ';', $str);
        $str = stack_utils::remove_comments($str);
        $str = str_replace(';', "\n", $str);

        $kvarray = explode("\n", $str);
        foreach ($strings as $key => $string) {
            foreach ($kvarray as $kkey => $kstr) {
                $kvarray[$kkey] = str_replace('[STR:'.$key.']', '"'.$string.'"', $kstr);
            }
        }

        // 23/4/12 - significant changes to the way keyvals are interpreted.  Use Maxima assignmentsm i.e. x:2.
        $errors  = '';
        $valid   = true;
        $vars = array();
        foreach ($kvarray as $kvs) {
            $kvs = trim($kvs);
            if ('' != $kvs) {
                $cs = new stack_cas_casstring($kvs);
                $cs->get_valid($this->security, $this->syntax, $this->insertstars);
                $vars[] = $cs;
            }
        }

        $this->session->add_vars($vars);
        $this->valid       = $this->session->get_valid();
        $this->errors      = $this->session->get_errors();
        // Prevent reference to inputs in the values of the question variables.
        if (is_array($inputs)) {
            $keys = $this->session->get_all_keys();
            foreach ($keys as $key) {
                if (in_array($key, $inputs)) {
                    $this->valid = false;
                    $this->errors .= stack_string('stackCas_inputsdefined', $key);
                }
            }
        }
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

    public function get_errors($casdebug=false) {
        if (null === $this->valid) {
            $this->validate(null);
        }
        if ($casdebug) {
            return $this->errors.$this->session->get_debuginfo();
        }
        return $this->errors;
    }

    public function instantiate() {
        if (null === $this->valid) {
            $this->validate(null);
        }
        if (!$this->valid) {
            return false;
        }
        $this->session->instantiate();
        $this->instantiated = true;
    }

    public function get_session() {
        if (null === $this->valid) {
            $this->validate(null);
        }
        return $this->session;
    }

}
