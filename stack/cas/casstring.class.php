<?php

/*
 This file is part of Stack - http://stack.bham.ac.uk//

 Stack is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Stack is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Stack.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(dirname(__FILE__) . '/../stringutil.class.php');

class stack_cas_casstring {

    private $rawCASString;    // As typed in by the user.
    private $CASString;       // As modified by the validation.
    private $valid;           // true or false
    private $key;
    private $errors;          // string for the user

    private $value;           // Note these two values only become activated when the CASString goes to the CAS.
    private $display;

    // Option values
    private $security;
    private $insertStars;
    private $strictSyntax;

    function __construct($rawString, $securityLevel='s', $strictSyntax=true, $insertStars=false) {
        $this->rawCASString   = $rawString;
        $this->security       = $securityLevel;   // by default, student
        $this->insertStars    = $insertStars;     // by default don't add stars
        $this->strictSyntax   = $strictSyntax;    // by default strict

        $this->valid          =  null;  // If NULL then the validate command has not yet been run....

        if (!is_string($this->rawCASString)) {
            throw new Exception('STACK_CAS_CAS_String: rawString must be a STRING.');
        }

        if (!('s'===$securityLevel || 't'===$securityLevel)) {
            throw new Exception('STACK_CAS_CAS_String: 2nd argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($strictSyntax)) {
            throw new Exception('STACK_CAS_CAS_String: 3rd argument, stringSyntax, must be Boolean.');
        }

        if (!is_bool($insertStars)) {
            throw new Exception('STACK_CAS_CAS_String: 4th argument, insertStars, must be Boolean.');
        }
    }

    /*********************************************************/
    /* Validation functions                                  */
    /*********************************************************/

    function validate() {

        $this->valid     = true;
        $cmd             = $this->rawCASString;
        $this->CASString = $this->rawCASString;

        // CASStrings must be non-empty.
        if (''==trim($this->CASString)) {
            $this->valid = false;
            return false;
        }

        //if student, check for spaces between letters or numbers in expressions
        if ($this->security != 't') {
            $pat = "|([A-Za-z0-9\(\)]+) ([A-Za-z0-9\(\)]+)|";
            if (preg_match($pat, $cmd)) {
                $this->valid = false;
                $cmds = str_replace(' ', '<font color="red">_</font>', $cmd);
                $this->errors.=stack_string("stackCas_spaces").$this->format_error_string($cmds).'. ';
            }
        }

        //Check for % signs, allow %pi %e, %i, %gamma, %phi but nothing else
        if (strstr($cmd, '%') !== false) {
            $cmdl = strtolower($cmd);
            preg_match_all("(\%.*)", $cmdl, $found);

            foreach ($found[0] as $match) {
                if((strpos($match, '%e') !== false) || (strpos($match, '%pi') !== false) || (strpos($match, '%i') !== false) || (strpos($match, '%j') !== false) || (strpos($match, '%gamma') !== false) || (strpos($match, '%phi') !== false)) {
                    //%e and %pi are allowed. Any other percentages dissallowed.
                } else {
                    //problem
                    $this->valid   = false;
                    $this->errors .= stack_string("stackCas_percent").$this->format_error_string($cmd).'. ';
                }
            }
        }

        $cs = new STACK_StringUtil($cmd);
        $inline = $cs->checkBookends('(', ')');
        if ($inline !== true) { //checkBookends does not return false
            $this->valid = false;
            if ($inline == 'left') {
                $this->errors .= stack_string('stackCas_missingLeftBracket', '(').$this->format_error_string($cmd).'. ';
            } else {
                $this->errors .= stack_string('stackCas_missingRightBracket', '(').$this->format_error_string($cmd).'. ';
            }
        }
        $inline = $cs->checkBookends('{', '}');
        if ($inline !== true) { //checkBookends does not return false
            $this->valid = false;
            if ($inline == 'left') {
                $this->errors .= stack_string('stackCas_missingLeftBracket', '{').$this->format_error_string($cmd).'. ';
            } else {
                $this->errors .= stack_string('stackCas_missingRightBracket', '}').$this->format_error_string($cmd).'. ';
            }
        }
        $inline = $cs->checkBookends('[', ']');
        if ($inline !== true) { //checkBookends does not return false
            $this->valid = false;
            if ($inline == 'left') {
                $this->errors.=stack_string('stackCas_missingLeftBracket', '[').$this->format_error_string($cmd).'. ';
            } else {
                $this->errors.=stack_string('stackCas_missingRightBracket', ']').$this->format_error_string($cmd).'. ';
            }
        }

/*
        //check for apostrophes if a student
        if ($this->security == 's') {
            if(strpos($cmd, "'") !== false) {
                $this->valid = false;
                $this->errors.=stack_string("stackCas_apostrophe").$this->format_error_string($cmd).'. ';
            }
        }
*/

        // Only permit the following characters to be sent to the CAS.
        $testSet = '0123456789,./\%&{}[]()$�@!"?`^~*_-+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM;:=><|: '."'";
        $cmd = trim($cmd);
        $length = strlen($cmd);
        $lastChar = $cmd[($length -1)];

        // Check for permitted characters
        $invalidChars=array();
        foreach (str_split($cmd,1) as $chr) {
            if (strpos($testSet, $chr) === false) {
                $invalidChars[] = $chr;
            }
        }

        if (count($invalidChars)>0) {
            $this->valid = false;
            $a = array( 0 => implode(", ", array_unique($invalidChars)));
            $this->errors.=stack_string('stackCas_forbiddenChar', $a);
        }

        // Check for disallowed final characters,  / * + - ^ £ # = & ~ | , ? : ;
        $disallowedChars = array('/', '+', '*', '/', '-', '^', '£', '#', '~', '=', '?', ',', '_', '&', '`', '¬', ';', ':', '$');
        if (in_array($lastChar, $disallowedChars)) {
            $this->valid = false;
            $a = array();
            $a[0] = $lastChar;
            $a[1] = $this->format_error_string($cmd);
            $this->errors.=stack_string('stackCas_finalChar', $a);
        }

        $this->checkStars();

        $this->checkSecurity();

        $this->key_val_split();
        return $this->valid;
    }

    /**
    * Checks that there are no *'s missing from expressions, eg 2x should be 2*x
    *
    * @return bool|string true if no missing *s, false if missing stars but automatically added
    * if stack is set to not add stars automatically, a string indicating the missing stars is returned.
    */
    function checkStars() {
        // We assume f and g are single letter functions.
        $patterns[] = "|(\))(\()|";                // Simply the pattern ")(".  Must be wrong!
        $patterns[] = "|([0-9]+)([A-DF-Za-eh-z])|";   // eg 3x
        $patterns[] = "|([0-9])([A-DF-Za-z]\()|";     // eg 3 x (
        $patterns[] = "|(\))([0-9A-DF-Za-z])|";         // eg )a

        if ($this->security == 's') {
            // Teachers have more options for functions
            $patterns[]  = "|([0-9]+)(\()|";            // eg 3212 (
            $patterns[]  = "|(^[A-DF-Za-eh-z])(\()|";      // eg a(  , that is a single letter.
            $patterns[]  = "|(\*[A-DF-Za-eh-z])(\()|";
        }

        //loop over every CAS command checking for missing stars
        $missingStar     = false;
        $missingString   = '';

        $cmd =  $this->rawCASString;

        foreach ($patterns as $pat) {
            if (preg_match($pat, $cmd)) {
                //found a missing star.
                $missingStar = true;
                if (($this->strictSyntax == false) || ($this->insertStars)) {
                    //then we automatically add stars
                    $cmd = preg_replace($pat, "\${1}*\${2}", $cmd);
                }
                if ($this->strictSyntax == true) {
                    //flag up the error
                    $missingString .= $this->format_error_string(preg_replace($pat, "\${1}<strong>*</strong>\${2}", $cmd)).'<br />';
                }
            }
        }

        if (false == $missingStar) {
            //if no missing stars return true
            return true;
        } else if (false == $this->strictSyntax) {
            //if missing stars, but strictSyntax is off, return false (stars will have been added)
            $this->CASString=$cmd;
            return false;
        } else {
            //if missing stars & strict syntax is on return errors
            $this->errors .= stack_string('stackCas_MissingStars').' '.$missingString;
            return false;
        }
    }


    /**
    * Check for forbidden CAS commands, based on security level
    *
    * @return bool|string true if passes checks if fails, returns string of forbidden commands
    */
    function checkSecurity() {
        $cmd = $this->CASString;
        $strin_keywords = array();
        $pat = "|[\?_A-Za-z0-9]+|";
        preg_match_all($pat, $cmd, $out, PREG_PATTERN_ORDER);

        // Filter out some of these matches.
        foreach ($out[0] as $key) {
            // Do we have only numbers, or only 2 characters?
            // These strings are fine.
            preg_match("|[0-9]+|", $key, $justnum);

            if (empty($justnum) and strlen($key)>2) {
                //echo "Keyword found: $key <br />";
                $upKey = strtoupper($key);
                array_push($strin_keywords, $upKey);
            }
        }
        $strin_keywords = array_unique($strin_keywords);

        //check for global forbidden words
        // TODO: this file should be eventually autogenerated at install time.
        require('keywords.php');
        foreach ($strin_keywords as $key) {
            if (in_array($key, $stack_cas['globalForbid'])) {
                //very bad!.
                $this->valid = false;
                $this->errors.= stack_string('stackCas_forbiddenWord').' '.$key.'. ';
            } else {
                if ($this->security == 't') {
                    if (in_array($key, $stack_cas['teacherNotAllow'])) {
                        //if a teacher check against forbidden commands
                        $this->valid = false;
                        $this->errors.= stack_string('stackCas_unsupportedKeyword').' '.$key.'. ';
                    }
                } else {
                    //if not teacher allow only set commands.
                    if (!in_array($key, $stack_cas['studentAllow'])) {
                        $this->valid = false;
                        $this->errors.= stack_string('stackCas_unknownFunction').' '.$key.'. ';
                    }
                    // else is valid student command.
                }
            }
        }
        return null;
    }

    /**
    * Check for CAS commands which appear in the $keywords array
    * Notes, (i)  this is case insensitive.
    *        (ii) returns true if we find the element of the array.
    * @return bool|string true if an element of array is found in the CASString.
    */
    public function checkExternalForbiddenWords($keywords) {
        $found          = false;
        $cmd            = $this->CASString;
        $strin_keywords = array();
        $pat = "|[\?_A-Za-z0-9]+|";
        preg_match_all($pat, $cmd, $out, PREG_PATTERN_ORDER);

        // Ensure all $keywords are upper case
        foreach ($keywords as $key => $val) {
            $keywords[$key] = strtoupper($val);
        }

        // Filter out some of these matches.
        foreach ($out[0] as $key) {
            // Do we have only numbers, or only 2 characters?
            // These strings are fine.
            preg_match("|[0-9]+|", $key, $justnum);

            if (empty($justnum) and strlen($key)>2) {
                //echo "Keyword found: $key <br />";
                $upKey = strtoupper($key);
                array_push($strin_keywords, $upKey);
            }
        }
        $strin_keywords = array_unique($strin_keywords);

        foreach ($strin_keywords as $key) {
            if (in_array($key, $keywords)) {
                $found = true;
            }
        }
        return $found;
    }

    /*********************************************************/
    /* Internal utility functions				 */
    /*********************************************************/

    function format_error_string($str) {
        return "<span class='SyntaxExample2'>".$str."</span>";
    }

    function key_val_split() {
        $i = strpos($this->CASString, ':');
        if (false === $i) {
            $this->key   = '';
            //$this->CASString = $this->CASString;
        } else {
            // Need to check we don't have a function definition...
            if ('='===substr($this->CASString, $i+1, 1)) {
                $this->key   = '';
                //$this->CASString = $this->CASString;
            } else {
                $this->key       = substr($this->CASString, 0, $i);
                $this->CASString = substr($this->CASString, $i+1);
            }
        }
    }

    /*********************************************************/
    /* Return and modify information                         */
    /*********************************************************/

    public function Get_valid() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->valid;
    }

    public function Get_key() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->key;
    }

    public function Get_display() {
        return $this->display;
    }

    public function Get_value() {
        return $this->value;
    }

    public function Set_key($key, $append_key=true) {
        if (null===$this->valid) {
            $this->validate();
        }
        if (''!=$this->key && $append_key) {
            $this->CASString = $this->key.':'.$this->CASString;
            $this->key=$key;
        } else {
            $this->key=$key;
        }
    }

    public function Get_rawCASString() {
        return $this->rawCASString;
    }

    public function Get_CASString() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->CASString;
    }

    public function Set_value($val) {
        $this->value=$val;
    }

    public function Set_display($val) {
        $this->display=$val;
    }

    public function Get_errors() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->errors;
    }

    public function Add_errors($err) {
        if (''==trim($err)) {
            return false;
        } else {
            return $this->errors.=$err;
        }
    }

    /* If we "CAS validate" this string, then we need to set various options. */
    /* If the teacher's answer is NULL then we use typeless validation, otherwise we check type */
    public function Set_CAS_validation_CASString($key, $forbidFloats=true, $lowestTerms=true, $tans=null) {
        if (null===$this->valid) {
            $this->validate();
        }
        if (false === $this->valid) {
            return false;
        }

        $this->key = $key;
        $starredAnswer = $this->CASString;

        //Turn PHP Booleans into Maxima true & false.
        if ($forbidFloats) {
            $forbidFloats='true';
        } else {
            $forbidFloats='false';
        }
        if ($lowestTerms) {
            $lowestTerms='true';
        } else {
            $lowestTerms='false';
        }

        if (null===$tans) {
            $this->CASString = 'stack_validate_typeless(['.$starredAnswer.'],'.$forbidFloats.','.$lowestTerms.')';
        } else {
            $this->CASString = 'stack_validate(['.$starredAnswer.'],'.$forbidFloats.','.$lowestTerms.','.$tans.')';
        }
        return true;
    }

} // end of class 