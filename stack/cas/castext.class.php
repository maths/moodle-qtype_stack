<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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


require_once('cassession.class.php');
require_once('casstring.class.php');


class STACK_CAS_CasText {

    private $rawCASText;      // Exactly the CASText entered.
    private $TrimmedCASText;  // This is processed gradually.
    private $CASText;         // The end result.

    private $session;         // STACK_CAS_CasSession
                              // Context in which the CASText is evaluated.
                              // Note, this is the place to set any CAS options of STACK_CAS_Maxima_Preferences

    private $valid;           // true or false
    private $instantiated;    // Has this been sent to the CAS yet? Stops re-sending to the CAS.
    private $errors;          // String for the user.

    private $security;
    private $addStars;
    private $strictSyntax;


    function __construct($rawCASText, $session=null, $seed=null, $securityLevel='s', $syntax=true, $stars=false) {

        if (!is_string($rawCASText)) {
            throw new Exception('STACK_CAS_CASText: rawCASText must be a STRING.');
        } else {
            $this->rawCASText   = $rawCASText;
        }

        if (is_a($session, 'STACK_CAS_CasSession') || null===$session) {
            $this->session      = $session;
        } else {
            throw new Exception('STACK_CAS_CasText constructor expects $session to be a STACK_CAS_CasSession.');
        }

        if ($seed != null) {
            if (is_int($seed)) {
                $this->seed = $seed;
            } else {
                throw new Exception('STACK_CAS_CasText: $seed must be a number.');
            }
        } else {
            $this->seed = time();
        }

        if (!('s'===$securityLevel || 't'===$securityLevel)) {
            throw new Exception('STACK_CAS_CAS_String: 4th argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new Exception('STACK_CAS_CAS_String: 5th argument, stringSyntax, must be Boolean.');
        }

        if (!is_bool($stars)) {
            throw new Exception('STACK_CAS_CAS_String: 6th argument, insertStars, must be Boolean.');
        }

        $this->security     = $securityLevel; // by default, student
        $this->strictSyntax = $syntax;        // by default strict
        $this->addStars     = $stars;         // by default don't add stars
    }

    /**
     * Checks the castext syntax is valid, no missing @'s, $'s etc
     *
     * @access public
     * @return bool
     */
    private function validate() {
        if (strlen(trim($this->rawCASText)) > 64000) {
            //Limit to just less than 64kb. Maximum practical size of a post. (about 14pages).
            $this->errors = stack_string("stackCas_tooLong");
            $this->valid = false;
            return false;
        }

        // Remove any comments from the CASText
        $str = str_replace("\n", ' ', $this->rawCASText);
        $str = new STACK_StringUtil($str);
        $this->TrimmedCASText = $str->removeComments();

        if (''===trim($this->TrimmedCASText)) {
            $this->valid = true;
            return true;
        }

        // Find reasons to invalidate the text....
        $this->valid = true;

        $cs = new STACK_StringUtil($this->TrimmedCASText);

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
            if (!$this->session->Get_valid()) {
                $this->valid = false;
                $this->errors .= $this->session->Get_errors();
            }
        }

        // Now extract and perform validation on the CAS variables.
        // This does alot more than strictly "validate" the CASText, but is makes sense to do all these things at once...
        $this->ExtractCASCommands();

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
    private function ExtractCASCommands() {
        //first check contains @s
        $count = substr_count($this->TrimmedCASText, '@');

        if ($count == 0){
            //nothing to do
            return null;
        } else {
            //extract the CAS commands
            $cs = new STACK_StringUtil($this->TrimmedCASText);
            $temp = $cs->getBetweenChars('@'); //returns an array

            //create array of commands matching with their labels
            $i = 0;
            $valid = true;
            $errors = '';
            $cmdArray = array();
            $labels   = array();

            foreach ($temp as $cmd) {
                // Trim of surrounding white space and CAS commands.
                $str = new STACK_StringUtil($cmd);
                $cmd = $str->trimCommands();

                $cs = new STACK_CAS_CasString($cmd, $this->security, $this->addStars, $this->strictSyntax);

                $key = 'caschat'.$i;
                $i++;
                $labels[] = $key;
                $cs->Set_key($key,true);
                $cmdArray[] = $cs;

                $valid = $valid && $cs->Get_valid();
                $errors .= $cs->Get_errors();
            }

            if (!$valid) {
                $this->valid = false;
                $this->errors .= stack_string('stackCas_invalidCommand').'</br>'.$errors;
            }

            if (!empty($cmdArray)) {
                $new_session   = $this->session;
                if (null===$new_session) {
                    $new_session = new STACK_CAS_CasSession($cmdArray, null, $this->seed, $this->security, $this->addStars, $this->strictSyntax);
                } else {
                    $new_session->add_vars($cmdArray);
                }
                $this->session = $new_session;

                // Now replace the commannds with their labels in the text.
                $string = new STACK_StringUtil($this->TrimmedCASText);
                $this->TrimmedCASText = $string->replaceBetween('@', '@', $labels);
            }
        }
    }

    /* This function actually evaluates the CASText */
    private function instantiate() {
        // TODO: config files....
        $DisplayMethod = 'LaTeX';

        if (!$this->valid) {
            return false;
        }
        // Deal with CASText without any CAS variables.
        if (null !== $this->session) {
            $this->session->instantiate();
            $this->errors .= $this->session->Get_errors();
        }

        if ('MathML' === $DisplayMethod) {
            $this->applyFilters();
            if (null !== $this->session) {
                $this->CASText = $this->session->Get_display_castext($this->CASText);
            }
            $this->strin = str_replace('$@', '@', $this->strin); //Mathml doesn't need to be displayed in math mode
            $this->strin = str_replace('@$', '@', $this->strin); //Sending cascommands to ttm with the $'s breaks the restore values process.
        } else {
            // Assume STACK returns raw LaTeX for subsequent processing, e.g. with JSMath.

            $str = new STACK_StringUtil($this->TrimmedCASText);
            $this->CASText = $str->wrapAround();
            //$this->captureFeedbackTags();
            //$this->capturePRTFeedbackTags();
            if (null !== $this->session) {
                $this->CASText = $this->session->Get_display_castext($this->CASText);
            }
            $this->CASText = str_replace('$<html>', '', $this->CASText); //another modification. Stops <html> tags from being given $ tags and therefore breaking tth
            $this->CASText = str_replace('</html>$', '', $this->CASText); //bug occurs when maxima returns <html>tags in output, eg plots or div by 0 errors
            $this-> JSMath_LaTeXtoHTML();
            //$this->restoreHTML();
            //$this->restoreFeedback();
            //$this->restorePRTFeedback();
        }
        $this->instantiated = true;
    }

    /* Tidy up LaTeX commands used in CASText which are not interpreted by JSMath
    */
    private function JSMath_LaTeXtoHTML() {
        // Need to create line breaks in sensible places.
        //$this->strin = str_replace("\n\n",'<br />',$this->strin);
        //$this->strin = str_replace("\n\r\n",'<br />',$this->strin);

        $this->CASText = str_replace('\begin{itemize}', '<ol>', $this->CASText);
        $this->CASText = str_replace('\end{itemize}', '</ol>', $this->CASText);
        $this->CASText = str_replace('\begin{enumerate}', '<ul>', $this->CASText);
        $this->CASText = str_replace('\end{enumerate}', '<ul>', $this->CASText);
        $this->CASText = str_replace('\item', '<li>', $this->CASText);
    }

    public function Get_valid()  {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->valid;
    }

    public function Get_errors() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->errors;
    }

    public function Get_display_castext() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (null===$this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->CASText;
    }

    public function Get_session() {
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
    public function checkExternalForbiddenWords($keywords) {
        if (null===$this->valid) {
            $this->validate();
        }
        if(!is_a($this->session, 'STACK_CAS_CasSession')) {
            return false;
        }
        return $this->session->checkExternalForbiddenWords($keywords);
    }

} // end class
