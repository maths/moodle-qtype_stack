<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

/**
 * CAS text and related functions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/cassession.class.php');
require_once(__DIR__ . '/casstring.class.php');


class stack_cas_text {

    /** @var string Exactly the cas_text entered. */
    private $rawcastext;

    /** @var string This is processed gradually. */
    private $trimmedcastext;

    /** @var string The end result. */
    private $castext;

    /**
     * @var stack_cas_session Context in which the castext is evaluated.
     *  Note, this is the place to set any CAS options of STACK_CAS_Maxima_Preferences.
     */
    private $session;

    /** @var bool whether the string is valid. */
    private $valid;

    /** @var bool whether this been sent to the CAS yet? Stops re-sending to the CAS. */
    private $instantiated;

    /** @var string any error messages to display to the user. */
    private $errors;

    /** @var string security level, 's' or 't'. */
    private $security;

    /** @var bool whether to insert stars. */
    private $insertstars;

    /** @var bool whether to do strict syntax checks. */
    private $syntax;

    public function __construct($rawcastext, $session=null, $seed=null, $security='s', $syntax=true, $insertstars=0) {

        if (!is_string($rawcastext)) {
            throw new stack_exception('stack_cas_text: raw_castext must be a STRING.');
        } else {
            $this->rawcastext   = $rawcastext;
        }

        if (is_a($session, 'stack_cas_session') || null === $session) {
            $this->session      = $session;
        } else {
            throw new stack_exception('stack_cas_text constructor expects $session to be a stack_cas_session.');
        }

        if (is_int($seed)) {
            $this->seed = $seed;
        } else if ($seed === null) {
            $this->seed = time();
        } else {
            throw new stack_exception('stack_cas_text: $seed must be a number (or null).');
        }

        if (!('s' === $security || 't' === $security)) {
            throw new stack_exception('stack_cas_text: 4th argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new stack_exception('stack_cas_text: 5th argument, stringSyntax, must be Boolean.');
        }

        if (!is_int($insertstars)) {
            throw new stack_exception('stack_cas_text: 6th argument, insertStars, must be an integer.');
        }

        $this->security    = $security;
        $this->syntax      = $syntax;
        $this->insertstars = $insertstars;

    }

    /**
     * Checks the castext syntax is valid, no missing @'s, $'s etc
     *
     * @access public
     * @return bool
     */
    private function validate() {
        if (strlen(trim($this->rawcastext)) > 64000) {
            // Limit to just less than 64kb. Maximum practical size of a post. (about 14pages).
            $this->errors = stack_string("stackCas_tooLong");
            $this->valid = false;
            return false;
        }

        // Remove any comments from the castext.
        $this->trimmedcastext = stack_utils::remove_comments(str_replace("\n", ' ', $this->rawcastext));

        if (trim($this->trimmedcastext) === '') {
            $this->valid = true;
            return true;
        }

        // Find reasons to invalidate the text...
        $this->valid = true;

        // Check @'s match.
        $amps = stack_utils::check_matching_pairs($this->trimmedcastext, '@');
        if ($amps == false) {
            $this->errors .= stack_string('stackCas_MissingAt');
            $this->valid = false;
        }

        // Dollars can be protected for use with currency.
        $protected = str_replace('\$', '', $this->trimmedcastext);
        $dollar = stack_utils::check_matching_pairs($protected, '$');
        if ($dollar == false) {
            $this->errors .= stack_string('stackCas_MissingDollar');
            $this->valid = false;
        }

        $html = stack_utils::check_bookends($this->trimmedcastext, '<html>', '</html>');
        if ($html !== true) {
            // The method check_bookends does not return false.

            $this->valid = false;
            if ($html == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenHTML');
            } else {
                $this->errors .= stack_string('stackCas_MissingCloseHTML');
            }
        }

        $inline = stack_utils::check_bookends($this->trimmedcastext, '\[', '\]');
        if ($inline !== true) {
            // The method check_bookends does not return false.

            $this->valid = false;
            if ($inline == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenDisplay');
            } else {
                $this->errors .= stack_string('stackCas_MissingCloseDisplay');
            }
        }

        $inline = stack_utils::check_bookends($this->trimmedcastext, '\(', '\)');
        if ($inline !== true) {
            // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenInline');
            } else {
                $this->errors .= stack_string('stackCas_MissingCloseInline');
            }
        }

        // Perform validation on the existing session.
        if (null != $this->session) {
            if (!$this->session->get_valid()) {
                $this->valid = false;
                $this->errors .= $this->session->get_errors();
            }
        }

        // Now extract and perform validation on the CAS variables.
        // This does alot more than strictly "validate" the castext, but is makes sense to do all these things at once...
        $this->extract_cas_commands();

        if (false === $this->valid) {
            $this->errors = '<span class="error">'.stack_string("stackCas_failedValidation").'</span>'.$this->errors;
        }
        return $this->valid;
    }


    /**
     * Extract the CAS commands from the string
     *
     * @access public
     * @return bool false if no commands to extract, true if succeeds.
     */
    private function extract_cas_commands() {
        // First check contains @s.
        $count = preg_match_all('~(?<!@)@(?!@)~', $this->trimmedcastext, $notused);

        if ($count == 0) {
            // Nothing to do.
            return null;
        } else {
            // Extract the CAS commands.
            $temp = stack_utils::all_substring_between($this->trimmedcastext, '@', '@', true);

            // Create array of commands matching with their labels.
            $i = 0;
            $valid = true;
            $errors = '';
            $cmdarray = array();
            $labels   = array();

            $sessionkeys = array();
            if (is_a($this->session, 'stack_cas_session')) {
                $sessionkeys = $this->session->get_all_keys();
            }
            foreach ($temp as $cmd) {
                // Trim of surrounding white space and CAS commands.
                $cmd = stack_utils::trim_commands($cmd);

                $cs = new stack_cas_casstring($cmd);
                $cs->get_valid($this->security, $this->syntax, $this->insertstars);

                do { // ... make sure names are not already in use.
                    $key = 'caschat'.$i;
                    $i++;
                } while (in_array($key, $sessionkeys));
                $sesionkeys[] = $key;
                $labels[] = $key;
                $cs->set_key($key, true);
                $cmdarray[] = $cs;

                $valid = $valid && $cs->get_valid();
                $errors .= $cs->get_errors();
            }

            if (!$valid) {
                $this->valid = false;
                $this->errors .= stack_string('stackCas_invalidCommand').'</br>'.$errors;
            }

            if (!empty($cmdarray)) {
                $newsession   = $this->session;
                if (null === $newsession) {
                    $newsession = new stack_cas_session($cmdarray, null, $this->seed);
                } else {
                    $newsession->add_vars($cmdarray);
                }
                $this->session = $newsession;

                // Now replace the commannds with their labels in the text.
                $this->trimmedcastext = stack_utils::replace_between($this->trimmedcastext, '@', '@', $labels, true);
            }
        }
    }

    /**
     * This function actually evaluates the castext.
     */
    private function instantiate() {

        if (!$this->valid) {
            return false;
        }

        // Deal with castext without any CAS variables.
        if (null !== $this->session) {
            $this->session->instantiate();
            $this->errors .= $this->session->get_errors();
        }

        $this->castext = stack_utils::wrap_around($this->trimmedcastext);
        if (null !== $this->session) {
            $this->castext = $this->session->get_display_castext($this->castext);
        }
        // Another modification. Stops <html> tags from being given $ tags.
        $this->castext = str_replace('\(<html>', '', $this->castext);
        // Bug occurs when maxima returns <html>tags in output, eg plots or div by 0 errors.
        $this->castext = str_replace('</html>\)', '', $this->castext);
        $this->latex_tidy();

        $this->instantiated = true;
    }

    /**
     * Tidy up LaTeX commands used in castext which are not interpreted by JSMath.
     */
    private function latex_tidy() {
        // Need to create line breaks in sensible places.
        $this->castext = str_replace('\begin{itemize}', '<ol>', $this->castext);
        $this->castext = str_replace('\end{itemize}', '</ol>', $this->castext);
        $this->castext = str_replace('\begin{enumerate}', '<ul>', $this->castext);
        $this->castext = str_replace('\end{enumerate}', '</ul>', $this->castext);
        $this->castext = str_replace('\item', '<li>', $this->castext);
    }

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
            return $this->errors.$this->session->get_debuginfo();
        }
        return $this->errors;
    }

    public function get_all_raw_casstrings() {
        if (null === $this->valid) {
            $this->validate();
        }

        if (null !== $this->session) {
            return $this->session->get_all_raw_casstrings();
        } else {
            return array();
        }
    }

    public function get_display_castext() {
        if (null === $this->valid) {
            $this->validate();
        }
        if (null === $this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->castext;
    }

    public function get_session() {
        if (null === $this->valid) {
            $this->validate();
        }
        if (null === $this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->session;
    }

    /* Simply passes the keywords through to session.*/
    public function check_external_forbidden_words($keywords) {
        if (null === $this->valid) {
            $this->validate();
        }
        if (!is_a($this->session, 'stack_cas_session')) {
            return false;
        }
        return $this->session->check_external_forbidden_words($keywords);
    }

    public function get_debuginfo() {
        if (null === $this->session) {
            return "Session is NULL. ";
        }
        return $this->session->get_debuginfo();
    }

}
