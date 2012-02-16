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
require_once('cassession.class.php');
require_once('casstring.class.php');


class stack_cas_text {

    private $rawcastext;      // Exactly the cas_text entered.
    private $trimmedcastext;  // This is processed gradually.
    private $castext;         // The end result.

    private $session;         // stack_cas_session
                              // Context in which the castext is evaluated.
                              // Note, this is the place to set any CAS options of STACK_CAS_Maxima_Preferences

    private $valid;           // true or false
    private $instantiated;    // Has this been sent to the CAS yet? Stops re-sending to the CAS.
    private $errors;          // String for the user.

    private $security;
    private $insertstars;
    private $syntax;


    public function __construct($rawcastext, $session=null, $seed=null, $security='s', $syntax=true, $insertstars=false) {

        if (!is_string($rawcastext)) {
            throw new Exception('stack_cas_text: raw_castext must be a STRING.');
        } else {
            $this->rawcastext   = $rawcastext;
        }

        if (is_a($session, 'stack_cas_session') || null===$session) {
            $this->session      = $session;
        } else {
            throw new Exception('stack_cas_text constructor expects $session to be a stack_cas_session.');
        }

        if ($seed != null) {
            if (is_int($seed)) {
                $this->seed = $seed;
            } else {
                throw new Exception('stack_cas_text: $seed must be a number.');
            }
        } else {
            $this->seed = time();
        }

        if (!('s'===$security || 't'===$security)) {
            throw new Exception('stack_cas_text: 4th argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new Exception('stack_cas_text: 5th argument, stringSyntax, must be Boolean.');
        }

        if (!is_bool($insertstars)) {
            throw new Exception('stack_cas_text: 6th argument, insertStars, must be Boolean.');
        }

        $this->security  = $security; // by default, student
        $this->syntax    = $syntax;   // by default strict
        $this->insertstars  = $insertstars;    // by default don't add insertstars
    }

    /**
     * Checks the castext syntax is valid, no missing @'s, $'s etc
     *
     * @access public
     * @return bool
     */
    private function validate() {
        if (strlen(trim($this->rawcastext)) > 64000) {
            //Limit to just less than 64kb. Maximum practical size of a post. (about 14pages).
            $this->errors = stack_string("stackCas_tooLong");
            $this->valid = false;
            return false;
        }

        // Remove any comments from the castext
        $str = str_replace("\n", ' ', $this->rawcastext);
        $str = new STACK_StringUtil($str);
        $this->trimmedcastext = $str->removeComments();

        if (''===trim($this->trimmedcastext)) {
            $this->valid = true;
            return true;
        }

        // Find reasons to invalidate the text....
        $this->valid = true;

        $cs = new STACK_StringUtil($this->trimmedcastext);

        //check @'s match
        $amps = $cs->checkMatchingPairs('@');
        if ($amps == false) {
            $this->errors .= stack_string('stackCas_MissingAt');
            $this->valid = false;
        }

        $dollar = $cs->checkMatchingPairs('$');
        if ($dollar == false) {
            $this->errors .= stack_string('stackCas_MissingDollar');
            $this->valid = false;
        }

        $hints = $cs->checkBookends('<hint>', '</hint>');
        if ($hints !== true) {
            //checkbookends does not return false
            $this->valid = false;
            if ($hints == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenHint');
            } else {
                $this->errors .= stack_string('stackCas_MissingClosingHint');
            }
        }

        $html = $cs->checkBookends('<html>', '</html>');
        if ($html !== true) {
            //checkbookends does not return false

            $this->valid = false;
            if ($html == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenHTML');
            } else {
                $this->errors .= stack_string('stackCas_MissingCloseHTML');
            }
        }

        $inline = $cs->checkBookends('\[', '\]');
        if ($inline !== true) {
            //checkbookends does not return false

            $this->valid = false;
            if ($inline == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenDisplay');
            } else {
                $this->errors .= stack_string('stackCas_MissingCloseDisplay');
            }
        }

        $inline = $cs->checkBookends('\(', '\)');
        if ($inline !== true) {
            //checkbookends does not return false
            $this->valid = false;
            if ($inline == 'left') {
                $this->errors .= stack_string('stackCas_MissingOpenInline');
            } else {
                $this->errors .= stack_string('stackCas_MissingCloseInline');
            }
        }

        // Perform validation on the existing session
        if (null!=$this->session) {
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
        //first check contains @s
        $count = substr_count($this->trimmedcastext, '@');

        if ($count == 0) {
            //nothing to do
            return null;
        } else {
            //extract the CAS commands
            $cs = new STACK_StringUtil($this->trimmedcastext);
            $temp = $cs->getBetweenChars('@'); //returns an array

            //create array of commands matching with their labels
            $i = 0;
            $valid = true;
            $errors = '';
            $cmdarray = array();
            $labels   = array();

            foreach ($temp as $cmd) {
                // Trim of surrounding white space and CAS commands.
                $str = new STACK_StringUtil($cmd);
                $cmd = $str->trimCommands();

                $cs = new stack_cas_casstring($cmd, $this->security, $this->insertstars, $this->syntax);

                $key = 'caschat'.$i;
                $i++;
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
                $new_session   = $this->session;
                if (null===$new_session) {
                    $new_session = new stack_cas_session($cmdarray, null, $this->seed, $this->security, $this->insertstars, $this->syntax);
                } else {
                    $new_session->add_vars($cmdarray);
                }
                $this->session = $new_session;

                // Now replace the commannds with their labels in the text.
                $string = new STACK_StringUtil($this->trimmedcastext);
                $this->trimmedcastext = $string->replaceBetween('@', '@', $labels);
            }
        }
    }

    /* This function actually evaluates the castext */
    private function instantiate() {
        // TODO: config files....
        $displaymethod = 'LaTeX';

        if (!$this->valid) {
            return false;
        }
        // Deal with castext without any CAS variables.
        if (null !== $this->session) {
            $this->session->instantiate();
            $this->errors .= $this->session->get_errors();
        }

        if ('MathML' === $displaymethod) {
            $this->applyFilters();
            if (null !== $this->session) {
                $this->castext = $this->session->get_display_castext($this->castext);
            }
            $this->strin = str_replace('$@', '@', $this->strin); //Mathml doesn't need to be displayed in math mode
            $this->strin = str_replace('@$', '@', $this->strin);
        } else {
            // Assume STACK returns raw LaTeX for subsequent processing, e.g. with JSMath.

            $str = new STACK_StringUtil($this->trimmedcastext);
            $this->castext = $str->wrapAround();
            //$this->captureFeedbackTags();
            //$this->capturePRTFeedbackTags();
            if (null !== $this->session) {
                $this->castext = $this->session->get_display_castext($this->castext);
            }
            $this->castext = str_replace('$<html>', '', $this->castext); //another modification. Stops <html> tags from being given $ tags and therefore breaking tth
            $this->castext = str_replace('</html>$', '', $this->castext); //bug occurs when maxima returns <html>tags in output, eg plots or div by 0 errors
            $this->latex_tidy();
            //$this->restoreHTML();
            //$this->restoreFeedback();
            //$this->restorePRTFeedback();
        }
        $this->instantiated = true;
    }

    /* Tidy up LaTeX commands used in castext which are not interpreted by JSMath
    */
    private function latex_tidy() {
        // Need to create line breaks in sensible places.
        //$this->strin = str_replace("\n\n",'<br />',$this->strin);
        //$this->strin = str_replace("\n\r\n",'<br />',$this->strin);

        $this->castext = str_replace('\begin{itemize}', '<ol>', $this->castext);
        $this->castext = str_replace('\end{itemize}', '</ol>', $this->castext);
        $this->castext = str_replace('\begin{enumerate}', '<ul>', $this->castext);
        $this->castext = str_replace('\end{enumerate}', '<ul>', $this->castext);
        $this->castext = str_replace('\item', '<li>', $this->castext);
    }

    public function get_valid()  {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->valid;
    }

    public function get_errors() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->errors;
    }

    public function get_all_raw_casstrings() {
        if (null===$this->valid) {
            $this->validate();
        }

        if (null !== $this->session) {
            return $this->session->get_all_raw_casstrings();
        } else {
            return false;
        }
    }

    public function get_display_castext() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (null===$this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->castext;
    }

    public function get_session() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (null===$this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->session;
    }

    /* Simply passes the keywords through to session.*/
    public function check_external_forbidden_words($keywords) {
        if (null===$this->valid) {
            $this->validate();
        }
        if (!is_a($this->session, 'stack_cas_session')) {
            return false;
        }
        return $this->session->check_external_forbidden_words($keywords);
    }

    public function get_debuginfo() {
        return $this->session->get_debuginfo();
    }

} // end class
