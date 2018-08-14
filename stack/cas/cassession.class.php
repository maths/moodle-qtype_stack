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

// A CAS session is a list of Maxima expressions, which are validated
// sent to the CAS Maxima to be evaluated, and then used.  This class
// prepares expressions for the CAS and deals with return information.
//
// @copyright  2012 The University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once('casstring.class.php');
require_once('connectorhelper.class.php');
require_once(__DIR__ . '/../options.class.php');

class stack_cas_session {
    /**
     * @var array stack_cas_casstring
     */
    private $session;

    /**
     * @var stack_options
     */
    private $options;

    /**
     * @var int Needed to seed any randomization when instantated.
     */
    private $seed;

    /**
     * @var boolean
     */
    private $valid;

    /**
     * @var boolean Has this been sent to the CAS yet?
     */
    private $instantiated;

    /**
     * @var string Error messages for the user.
     */
    private $errors;

    /**
     * @var string
     */
    private $debuginfo;

    /** @var array Global variables. */
    private static $maximaglobals = array('stackintfmt' => true, 'stackfltfmt' => true, 'ibase' => true, 'obase' => true);

    public function __construct($session, $options = null, $seed = null) {

        if (is_null($session)) {
            $session = array();
        }

        // An array of stack_cas_casstring.
        $this->session = $session;

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

    /*********************************************************/
    /* Validation functions                                  */
    /*********************************************************/

    private function validate() {
        if (null === $this->session) { // Empty sessions are ok.
            $this->valid = true;
            return true;
        }
        if (false === is_array($this->session)) {
            $this->valid = false;
            return false;
        }
        if (empty($this->session)) {
            $this->valid = true;
            $this->session = null;
            return true;
        }

        $this->valid = $this->validate_array($this->session);

        // Ensure the array is number ordered.  We use this later when getting back the values of expressions
        // so it important to be definite now.
        if ($this->valid) {
            $this->session = array_values($this->session);
        }
        return $this->valid;
    }

    /* A helper function which enables an array of stack_cas_casstring to be validated. */
    private function validate_array($cmd) {
        $valid  = true;
        foreach ($cmd as $key => $val) {
            if (is_a($val, 'stack_cas_casstring')) {
                if (!$val->get_valid()) {
                    $valid = false;
                    $this->errors .= $val->get_errors();
                }
            } else {
                throw new stack_exception('stack_cas_session: $session must be null or an array of stack_cas_casstring.');
            }
        }
        return $valid;
    }

    /* Check each of the CASStrings for any of the keywords. */
    public function check_external_forbidden_words($keywords) {
        if (null === $this->valid) {
            $this->validate();
        }
        $found = false;
        foreach ($this->session as $casstr) {
            $found = $found || $casstr->check_external_forbidden_words($keywords);
        }
        return $found;
    }

    /* This is the function which actually sends the commands off to Maxima. */
    public function instantiate() {
        if (null === $this->valid) {
            $this->validate();
        }
        if (!$this->valid) {
            return false;
        }
        // Lazy instantiation - only do this once...
        // Empty session.  Nothing to do.
        if ($this->instantiated || null === $this->session) {
            return true;
        }

        $connection = stack_connection_helper::make();
        $results = $connection->compute($this->construct_maxima_command());
        $this->debuginfo = $connection->get_debuginfo();
        // Now put the information back into the correct slots.
        $session    = $this->session;
        $newsession = array();
        $newerrors  = '';
        $allfail    = true;
        $i          = 0;

        // We loop over each entry in the session, not over the result.
        // This way we can add an error for missing values.
        foreach ($session as $cs) {
            $gotvalue = false;

            if ('' == $cs->get_key()) {
                $key = 'dumvar'.$i;
            } else {
                $key = $cs->get_key();
            }

            if (array_key_exists($i, $results)) {
                $allfail = false; // We at least got one result back from the CAS!

                $result = $results["$i"]; // GOCHA!  Results have string represenations of numbers, not int....

                if ('' != $result['error'] and false === strstr($result['error'], 'clipped')) {
                    $cs->add_errors($result['error']);
                    $cs->decode_maxima_errors($result['error']);
                    $newerrors .= stack_maxima_format_casstring($cs->get_raw_casstring());
                    $newerrors .= ' '.stack_string("stackCas_CASErrorCaused") .
                            ' ' . $result['error'] . ' ';
                }

                if (array_key_exists('value', $result)) {
                    $val = str_replace('QMCHAR', '?', $result['value']);
                    $cs->set_value($val);
                    $gotvalue = true;
                } else {
                    $cs->add_errors(stack_string("stackCas_failedReturnOne"));
                }

                if (array_key_exists('display', $result)) {
                    $disp = $result['display'];
                    $disp = $this->translate_displayed_tex($disp);
                    $cs->set_display($disp);
                }

                if (array_key_exists('dispvalue', $result)) {
                    $valfix = array('QMCHAR' => '?');
                      // Need to add this in here also because strings may contain question mark characters.
                    $val = $result['dispvalue'];
                    foreach ($valfix as $key => $fix) {
                        $val = str_replace($key, $fix, $val);
                    }
                    $val = str_replace('"!! ', '', $val);
                    $val = str_replace(' !!"', '', $val);
                    $cs->set_dispvalue(trim($val));
                }

                if (array_key_exists('valid', $result)) {
                    $cs->set_valid($result['valid']);
                }

                if (array_key_exists('answernote', $result)) {
                    $cs->set_answernote($result['answernote']);
                }

                if (array_key_exists('feedback', $result)) {
                    $feedback = $result['feedback'];
                    $feedback = $this->translate_displayed_tex($feedback);
                    $cs->set_feedback($feedback);
                }

            } else if (!$gotvalue) {
                $errstr = stack_string("stackCas_failedReturn").' '.stack_maxima_format_casstring($cs->get_raw_casstring());
                $cs->add_errors($errstr);
                $cs->set_answernote('CASFailedReturn');
                $newerrors .= $errstr;
            }

            $newsession[] = $cs;
            $i++;
        }
        $this->session = $newsession;

        if ('' != $newerrors) {
            $this->errors .= '<span class="error">'.stack_string('stackCas_CASError').'</span>'.$newerrors;
        }
        if ($allfail) {
            $this->errors = '<span class="error">'.stack_string('stackCas_allFailed').'</span>';
            $this->errors .= $this->get_debuginfo();
        }
        $this->instantiated = true;
    }

    /**
     * Some of the TeX contains language tags which we need to translate.
     * @param string $str
     */
    private function translate_displayed_tex($str) {
        $dispfix = array('QMCHAR' => '?', '!LEFTSQ!' => '\left[', '!LEFTR!' => '\left(',
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

    public function get_debuginfo() {
        return $this->debuginfo;
    }

    /**
     * Add extra variables to the end of the existing session.
     * Note that this resets instantiation and validation, which will need to be
     * done again if used.
     * @param array $vars variable name => stack_cas_casstring, the variables to add.
     */
    public function add_vars($vars) {
        if (!is_array($vars)) {
            return;
        }
        foreach ($vars as $var) {
            if (!is_a($var, 'stack_cas_casstring')) {
                throw new stack_exception('stack_cas_session: trying to add a non-stack_cas_casstring to an existing session.');
            }

            $this->instantiated = null;
            $this->errors       = null;
            $this->session[]    = clone $var; // Yes, we really need new versions of the variables.
        }
    }

    /**
     * Concatenates the variables from $incoming onto the end of $this->session.
     * Treats this as essentially a new session.
     * The settings for this session are respected (currently).
     * @param stack_cas_session $incoming
     */
    public function merge_session($incoming) {
        if (null === $incoming) {
            return true;
        }
        if (is_a($incoming, 'stack_cas_session')) {
            $this->add_vars($incoming->get_session()); // This method resets errors and instantiated fields.
            $this->valid = null;
        } else {
            throw new stack_exception('stack_cas_session: merge_session expects its argument to be a stack_cas_session');
        }
    }

    /*********************************************************/
    /* Return and modify information                         */
    /*********************************************************/

    public function get_valid() {
        if (null === $this->valid) {
            $this->validate();
        }
        return $this->valid;
    }

    public function get_errors($casdebug=false) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($casdebug) {
            return $this->errors.$this->get_debuginfo();
        }
        return $this->errors;
    }

    public function get_all_raw_casstrings() {
        $return = array();
        if (!(null === $this->session)) { // Empty sessions are ok.
            foreach ($this->session as $casstr) {
                $return[] = $casstr->get_raw_casstring();
            }
        }
        return $return;
    }

    public function get_casstring_key($key) {
        if (null === $this->valid) {
            $this->validate();
        }
        foreach (array_reverse($this->session) as $casstr) {
            if ($casstr->get_key() === $key) {
                return $casstr->get_casstring();
            }
        }
        return false;
    }

    public function get_value_key($key, $dispvalue = false) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($this->valid && null === $this->instantiated) {
            $this->instantiate();
        }
        // We need to reverse the array to get the last value with this key.
        foreach (array_reverse($this->session) as $casstr) {
            if ($casstr->get_key() === $key) {
                if ($dispvalue) {
                    return $casstr->get_dispvalue();
                }
                return $casstr->get_value();
            }
        }
        return false;
    }

    public function get_display_key($key) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($this->valid && null === $this->instantiated) {
            $this->instantiate();
        }
        foreach (array_reverse($this->session) as $casstr) {
            if ($casstr->get_key() === $key) {
                return $casstr->get_display();
            }
        }
        return false;
    }

    public function get_errors_key($key) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($this->valid && null === $this->instantiated) {
            $this->instantiate();
        }
        if ($this->session === null) {
            return false;
        }
        foreach (array_reverse($this->session) as $casstr) {
            if ($casstr->get_key() === $key) {
                return $casstr->get_errors();
            }
        }
        return false;
    }

    public function get_session() {
        return $this->session;
    }

    public function prune_session($len) {
        if (!is_int($len)) {
            throw new stack_exception('stack_cas_session: prune_session $len must be an integer.');
        }
        if ($this->session === null) {
            // Empty session. Nothing to do.
            return;
        }
        $newsession = array_slice($this->session, 0, $len);
        $this->session = $newsession;
    }

    public function get_all_keys() {
        if (null === $this->valid) {
            $this->validate();
        }

        $keys = array();
        if (empty($this->session)) {
            return array();
        }
        foreach ($this->session as $cs) {
            if ('' != $cs->get_key()) {
                $keys[$cs->get_key()] = true;
            }
        }
        $keys = array_keys($keys);
        return $keys;
    }

    /* This returns the values of the variables with keys */
    public function get_display_castext($strin) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($this->valid && null === $this->instantiated) {
            $this->instantiate();
        }
        if (null === $this->session) {
            return $strin;
        }

        foreach ($this->session as $casstr) {
            $key = $casstr->get_key();
            if ($key === '') {
                // An empty key is something like a function definition, or an equality.
                // It is not something that can be replaced in the CAS text.
                continue;
            }
            $errors = $casstr->get_errors();
            $disp   = $casstr->get_display();
            $value  = $casstr->get_value();

            $dummy = '{@'.$key.'@}';

            // When we have only a single string in the output remove the maths environment.
            if ($errors == '' and substr(trim($value), 0, 1) == '"' and strpos($strin, '\(@'.$key.'@\)') !== false) {
                $disp = preg_replace('|^\\\\mbox\{(.*)\}$|', '$1', trim($disp));
                if ($value == '""') {
                    $disp = '';
                }
                // TODO: probably check for whitespace, e.g. \( @...@ \).
                $dummy = '\({@'.$key.'@}\)';
            }

            if ('' !== $errors && null != $errors) {
                $strin = str_replace($dummy, $casstr->get_casstring(), $strin);
            } else if (strstr($strin, $dummy)) {
                $strin = str_replace($dummy, $disp, $strin);
            }
        }
        return $strin;
    }

    /**
     * Creates the string which Maxima will execute
     *
     * @return string
     */
    private function construct_maxima_command() {
        // Ensure that every command has a valid key.

        $casoptions = $this->options->get_cas_commands();

        $csnames = $casoptions['names'];
        $csvars  = $casoptions['commands'];
        $cascommands = '';
        $caspreamble = '';

        $cascommands .= ', print("-1=[ error= ["), cte("__stackmaximaversion",errcatch(__stackmaximaversion:stackmaximaversion)) ';

        $i = 0;
        foreach ($this->session as $cs) {
            if ('' == $cs->get_key()) {
                $label = 'dumvar'.$i;
            } else {
                $label = $cs->get_key();
            }

            // Replace any ?'s with a safe value.
            $cmd = str_replace('?', 'QMCHAR', $cs->get_casstring());
            // Strip off any []s at the end of a variable name.
            // These are used to assign elements of lists and matrices, but this breaks Maxima's block command.
            if (false === strpos($label, '[')) {
                $cleanlabel = $label;
            } else {
                $cleanlabel = substr($label, 0, strpos($label, '['));
            }

            // Now we do special things if we have a command to re-order expressions.
            if (false !== strpos($cmd, 'ordergreat') || false !== strpos($cmd, 'orderless')) {
                // These commands must be in a separate block, and must only appear once.
                $caspreamble = $cmd."$\n";
                $cmd = '0';
            }

            // Special handling for the conditionally evaluated strings.
            if (count($cs->get_conditions()) > 0) {
                $conditions = array();
                foreach ($cs->get_conditions() as $cond) {
                    // No need to evaluate again if it is already evaluated.
                    if (array_search($cond, $this->session) !== false
                            && array_search($cond, $this->session) < array_search($cs, $this->session)) {
                        $conditions[] = str_replace('?', 'QMCHAR', $cond->get_key());
                    } else {
                        $conditions[] = str_replace('?', 'QMCHAR', $cond->get_casstring());
                    }
                }

                $condition = implode(" and ", $conditions);

                $cascommands .= ", print(\"$i=[ error= [\"), if $condition then cte(\"$label\",errcatch($label:$cmd)) "
                        . "else cte(\"$label\",errcatch($label:false)) ";
            } else {
                $cascommands .= ", print(\"$i=[ error= [\"), cte(\"$label\",errcatch($label:$cmd)) ";
            }

            // From Maxima 5.40.0, variable names may only occur once in the local variable list in a block.
            // This makes sure they only occur once.
            $csnames = array('RANDOM_SEED' => true);
            // The session might, legitimately, attempt to redefine a Maxima global variable,
            // which would throw a spurious error when the block attempts to define them as local.
            if (!(array_key_exists($cleanlabel, self::$maximaglobals))) {
                $csnames[$cleanlabel] = true;
            }

            $i++;
        }

        $cass  = $caspreamble;
        $cass .= 'cab:block([';
        $cass .= implode(', ', array_keys($csnames));
        $cass .= '], stack_randseed(';
        $cass .= $this->seed . ')' . $csvars;
        $cass .= ", print(\"[STACKSTART Locals= [ \") ";
        $cass .= $cascommands;
        $cass .= ", print(\"] ]\"), return(true) ); \n ";

        return $cass;
    }

    /**
     * Creates a string which we can feedback into a keyval.class object.
     * This is sufficient to define the session for caching purposes.
     *
     * @return string
     */
    public function get_keyval_representation() {
        if ($this->session == null) {
            return '';
        }

        $keyvals = '';
        foreach ($this->session as $cs) {
            if (null === $this->instantiated) {
                $val = $cs->get_casstring();
            } else {
                $val = $cs->get_value();
            }

            if ('' == $cs->get_key()) {
                $keyvals .= $val.'; ';
            } else {
                $keyvals .= $cs->get_key().':'.$val.'; ';
            }
        }
        return trim($keyvals);
    }

    public function is_instantiated() {
        return $this->instantiated !== null;
    }
}
