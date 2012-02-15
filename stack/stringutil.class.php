<?php

//~		TODO: this needs to be made into a static class.	

class STACK_StringUtil {

    private $string;

    /**
     *
     * @access public
     * @param string $stringIn
     */
    public function __construct($str) {
        $this->string = $str;
    }

    /**
     * Checks for matching pairs of characters in a string. Eg there are a even number of '@' chars in 'blah @blah@ blah'
     *
     * @param string
     * @return bool
     */
    public function checkMatchingPairs($char) {
        $no = substr_count($this->string, $char);

        if (($no % 2) == 1) {
            return false; //odd number, so missing pair
        } else {
            return true; //even
        }
    }

    /**
     * Check number of left and right substrings match
     * eg every <html> has a </html>
     * Returns true if equal, left left is missing, right if right is missing
     *
     * @access public
     * @param string $left
     * @param string $right
     * @return bool
     */
    public function checkBookends($left, $right) {
        $noLeft = substr_count($this->string, $left);
        $noRight = substr_count($this->string, $right);

        if ($noLeft == $noRight) {
            return true;
        } else if ($noLeft > $noRight) {
            return 'right'; // missing right
        } else {
            return 'left'; //missing left
        }
    }

    /**
    * Gets the string between two specified characters.
    *
    * @param int $start The position of the string to start searching at.
    * @param char $leftc Finds the first occurence of this character
    * @param char $rightc Finds the first occurence of this character after $leftc
    * @return array Field [0] contains the string between the two characters, Field [1] contains the start position of -1 if it does not exist and Field [2] contains the end position of -1 if there was no match.
    */
    public function util_grabbetween($strin, $leftc, $rightc, $start) {
        // Starting at $start, (a number)
        // Find the first occurance of $leftc, match with an occurance of
        // $rightc and grab everything in between.
        // Returns an array:
        // ret[0] = stuff grabbed
        // ret[1] = start position, or -1 if it does not exist
        // ret[2] = end position, or -1 if there is no match
        $strin_len = strlen($strin);
        $start = strpos($strin, $leftc, $start);
        if ($start === false) {
            $ret[0]='';
            $ret[1]=-1;
            $ret[2]=0;
            return $ret;
        }
        if ($leftc == $rightc) {
            // Left and right are the same
            $end = strpos($strin, $rightc, $start+1); // Just go for the next one
            if ($end === false) {
                $ret[0]='';
                $ret[1]=$start;
                $ret[2]=-1;
                return $ret;
            }
            $end++;
        } else {
            $left_bracket=0;
            $right_bracket=0;
            $end=$start;

            do {
                if ($strin[$end] == $leftc) {
                    $left_bracket++;
                } else if ($strin[$end] == $rightc) {
                    $right_bracket++;
                }

                $end++;
            } while ($left_bracket != $right_bracket and $end<$strin_len);

        }

        $ret[0]= substr($strin, $start, $end-$start);
        $ret[1]= $start;
        $ret[2]= $end-1;

        return $ret;
    }

    /**
     * Gets the characters between two chars
     * Works throughout a string returning an array of matches
     *
     * @param string $first the starting char to grab from
     * @param string $last the char to stop at (optional, if missing searches till another $first char
     * @return array of matchs without $first or $last pre/suffixes
     */
    function getBetweenChars($first, $last=null) {
        if ($last == null) {
            $last = $first;
        }

        $char = str_split($this->string);
        $length = count($char);
        $var = array();
        $j = 0;
        $i = 0;
        $start = false;
        $found = '';
        while ($i < $length) {
            if ($start == false) {
                //find starting @
                if ($char[$i] == $first) {
                    $start = true;
                    $found .= $char[$i];
                }
            } else {
                //we have the first @ find ending @
                if ($char[$i] == $last) {
                    //end of cas command found
                    $found .= $char[$i];
                    $found = str_replace($first, '', $found);
                    $found = str_replace($last, '', $found);
                    $found = trim($found);
                    $var[$j] = $found;
                    $j++;
                    $found = '';
                    $start = false;
                } else {
                    $found .= $char[$i];
                }
            }
            $i++;
        }
        return $var;
    }

    /**
     * Replaces the text between startchar and endchar with the next string from the array
     *
     * @param char $startChar the begining char to match against
     * @param char $endChar the end character of the replacement
     * @param array $replacements array of replacement strings, must equal the number of replacements
     * if the number of replacements does not match the number of strings to replaces, nothing is replaced
     * @return string
     */
    function replaceBetween($startChar, $endChar, $replacements) {
        //do error checking
        $noSC = substr_count($this->string, $startChar);
        $noEC = substr_count($this->string, $endChar);
        $no = count($replacements);
        $valid = true;
        if ($startChar == $endChar) {
            if ($no != ($noSC /2)) {
                $valid = false;
            }
        } else {
            if (($noSC != $noEC) || ($no != $noSC)) {
                $valid = false;
            }
        }

        if ($valid) {
            $toReturn = '';
            $matches = 0;
            $char = str_split($this->string);
            $length = count($char);
            $i = 0;
            $searching = true;
            while ($i < $length) {
                if ($searching) {
                    //trying to find startChar
                    if ($char[$i] == $startChar) {
                        $searching = false;
                        $toReturn .= $char[$i];
                        $toReturn .= $replacements[$matches];
                        $matches++;
                    } else {
                        $toReturn .= $char[$i];
                    }
                } else {
                    //found startChar, looking for end
                    if ($char[$i] == $endChar) {
                        $searching = true;
                        $toReturn .= $char[$i];
                    }
                }
                $i++;
            }
        }//if
        return $toReturn;
    }


    /**
     * Removes spaces & hyphens from a string, replacing with an underscore character
     *
     * @access public
     * @param array (Optional) additional characters to convert to underscores
     */
    public function underscore($additional=null) {
        $toReturn = str_replace('-', '_', $this->string);
        $toReturn = str_replace(' ', '_', $toReturn);
        if ($additional != null) {
            $toReturn = str_replace($additional, '_', $toReturn);
        }
        return $toReturn;
    }

    /**
     * Converts windows style paths to unix style with forward slashes
     *
     * @access public
     * @return string|null
     */
    public function convertSlashPaths() {
        $in = trim($this->string);
        $length = strlen($in);
        $lastChar = $in[($length -1)];
        $trailingSlash = false;
        if ($lastChar == '\\') {
            $trailingSlash = true;
        }
        $pathArray = $this->cvsToArray("\\");
        if (!empty($pathArray)) {
            $newPath = $pathArray[0];
            for ($i = 1; $i < count($pathArray); $i++) {
                $newPath .= "/".$pathArray[$i];
            }
            if ($trailingSlash == true) {
                return $newPath.'/';
            } else {
                return $newPath;
            }
        } else {
            return null;
        }
    }


    /**
     * Replaces @blah@ with $@blah@$ if the castext is not otherwise enclosed by $'s.
     *
     *
     * @access public
     * @return string
     */
    public function wrapAround() {
        $this->string = preg_replace('/\\\\\$/', 'escapeddollar', $this->string);
        $this->string = $this->wrap($this->string);
        $this->string = preg_replace('/escapeddollar/', '\\\\$', $this->string);
        return $this->string;
    }

    public function CASdelimit($text) {
        return eregi_replace('|@(.*)@|U', '$\1$', $text);
    }

    /**
     * Ensures that all elements within this text that need to be in math mode, are so.
     * Specifically, CAS elements and inline input macros.
     */
    public function delimit($text) {
        return preg_replace('/@(.*)?@/U', '$@\1@$', $text);
        //return preg_replace('/\\\\answer{.*}{.*}{.*}(.*)/U', '$@\1@$', $text);
    }

    /**
     * Returns the first position of an opening math delimiter in $text from the $offset.
     * Helper function for wrap().
     */
    public function mathStart($text, $offset = 0) {
        $delimiters = array('$', '$$', '\(', '\[');
        $at = false; // not yet found
        foreach ($delimiters as $d) {
            $pos = strpos($text, $d, $offset);
            if ($pos !== false) { // found one
                //echo "<br />found '$d' at pos $pos";
                if (($at === false || $pos <= $at)) {// take earliest ($$ taken over $)
                    //echo " \$at is bring set to $pos";
                    $at = $pos; // take earliest
                }
            }
        }
        return $at;
    }

    /**
     * Returns the position of the character following a closing math delimiter in $text from the $offset.
     * Helper function for wrap().
     */
    public function mathLength($text, $start) {
        $delimiters = array('$', '$$', '\)', '\]');
        $at = false;
        $ender = '';
        $len = strlen($text);

        // handle case where less than 3 chars to consider
        if ($len <= $start + 2) {
            return $len - $start;
        }

        foreach ($delimiters as $d) {
            $pos = strpos($text, $d, $start + 2); // check long enough
            if ($pos !== false) { // found one
                //echo "<br />found '$d' at pos $pos";
                if ($at === false || $pos <= $at) {// take earliest ($$ taken over $)
                    $at = $pos;
                    $ender = $d;
                }
            }
        }
        if($ender=='') {
            return strlen($text - $start);
            // math mode to the end
        } else {
            return $at - $start + strlen($ender);
        }
    }

    public function wrap($text) {
        $mathStart = $this->mathStart($text);
        if ($mathStart !== false) { // we have some maths ahead
            $pre = substr($text, 0, $mathStart); // get previous text
            $for = $this->mathLength($text, $mathStart);
            $maths = substr($text, $mathStart, $for);
            $rest = substr($text, $mathStart + $for);
            //echo '<br />wrapping '.$pre.':'.$maths.':'.$rest;
            return $this->delimit($pre).$maths.$this->wrap($rest);
        } else { // no math sections left
            return $this->delimit($text);
        }
    }

    /**
     * Removes any whitespace, ';' ':' or '$' signs from the end of cas command.
     *
     * @return string out
     * @access public
     */
    public function trimCommands() {
        $in = trim($this->string);
        $length = strlen($in);
        $lastChar = $in[($length -1)];

        if (($lastChar == '$') || ($lastChar == ';') || ($lastChar == ':')) {
            $out = substr($in, 0, ($length -1));
            return $out;
        } else {
            return $in;
        }
    }


    /**
     * Removes C style block comments from a string,
     *
     * @access private
     * @return string
     */
    public function removeComments() {
        if (strstr($this->string, '/*')) {
            $out = $this->string;
            preg_match_all('|/\*(.*)\*/|U', $out,$html_match);
            foreach ($html_match[0] as $val) {
                $out = str_replace($val, '', $out);
            }
            return $out;
        } else {
            return $this->string;
        }
    }

    /**
     * Removes characters between the start and end characters inclusive.
     * All instances are removed.
     *
     * @param string $start
     * @param string $end
     * @return string
     * @access public
     */
    public function removeBetween($start, $end) {
        if (strstr($this->string,$start) && strstr($this->string, $end)) {
            $out = $this->string;
            preg_match_all('|'.$start.'(.*)'.$end.'|U', $out, $html_match);

            foreach ($html_match[0] as $val) {
                $out = str_replace($val, '', $out);
            }
            $this->string = $out;

            return $out;
        } else {
            return $this->string;
        }
    }

    /**
     * Converts a CSV string into an array, removing empty entries.
     *
     * @param string in
     * @return array out
     * @access public
     */
    public function cvsToArray($token=',') {
        $exploded = explode($token, $this->string);
        //remove any null entries
        for ($i = 0; $i < count($exploded); $i++) {
            $trim = trim($exploded[$i]);
            if (!empty($trim)) {
                $toReturn[] = $exploded[$i];
            }
        }
        return $toReturn;
    }

    /**
     * Converts an array to a CSV
     *
     * @return String
     * @param $array Object
     */
    public function arrayToCSV($array) {
        if (!empty($array)) {
        $string = "";
        $i = 0;
        foreach ($array as $element){
            if ($i > 0) {
                $string .= ', ';
            }
            if (is_bool($element)) {
                if ($element) {
                    $string .= 'TRUE';
                } else {
                    $string .= 'FALSE';
                }
            } else {
                $string .= $element;
            }
            $i++;
        }
        return $string;
        } else {
            return "";
        }
    }

    /**
     *  Handles complex (comma-containing) list elements,
     * 	i.e. sets {}, functions() and nested lists[[]]
     *	Strict checking on nesting.
     *  Helper for listToArrayWorkhorse()
     */
    private function nextElement($list) {
        if ($list == '') {
            return null;
        }
        // delimited by next comma at same degree of nesting
        $startDelimiter = "[({";
        $endDelimiter   = "])}";
        $nesting = array(0=>0, 1=>0, 2=>0); // stores nesting for delimiters above
        for ($i = 0; $i < strlen($list); $i++) {
            $startChar = strpos($startDelimiter, $list[$i]);// which start delimiter
            $endChar = strpos($endDelimiter, $list[$i]);// which end delimiter (if any)

            // change nesting for delimiter if specified
            if ($startChar !== false) {
                $nesting[$startChar]++;
            } else if ($endChar !== false) {
                $nesting[$endChar]--;
            } else if ($list[$i] == ',' && $nesting[0] == 0 && $nesting[1] == 0 &&$nesting[2] == 0) {
            // otherwise, return element if all nestings are zero
                return substr($list, 0, $i);
            }
        }

        // end of list reached
        if ($nesting[0] == 0 && $nesting[1] == 0 &&$nesting[2] == 0) {
            return $list;
        } else return null; // invalid nesting
    }

    private function listToArrayWorkhorse($list, $rec=true) {
        $array = array();
        $list = trim($list);
        $list = substr($list, 1, strlen($list) - 2);// trims outermost [] only
        $e = $this->nextElement($list);
        while ($e !== null) {
            if ($e[0]=='[') {
                if ($rec) {
                    $array[] = $this->listToArrayWorkhorse($e,$rec);
                } else {
                    $array[] = $e;
                }
            } else {
                $array[] = $e;
            }
            $list = substr($list, strlen($e)+1);
            $e = $this->nextElement($list);
        }
        return $array;
    }

    /**
     * Converts a list structure into an array.
     * Handles nested lists, sets and functions with help from nextElement().
     */
    public function listToArray($rec=true) {
        $array = $this->listToArrayWorkhorse($this->string,$rec);
    return $array;
    }

    /**
     * Returns a more intuitive datestamp
     */
    public static function prettifyDate($datestamp) {
        /*
         * Rules:
         * show time if this week
         * show day + month if this year
         * show month + year if not this year
        */

        $dayStart = strtotime("00:00");
        $monthAgo = strtotime('-1 month');
        $yearAgo = strtotime('-1 year');

        //echo "yearstart: $yearStart monthStart: $monthStart dayStart: $dayStart";

        $time = strtotime($datestamp);

        if($time >= $dayStart) return date("g:ia", $time);// today
        if($time > $monthAgo) return date("g:ia, j M", $time);// not today
        if($time > $yearAgo) return date("M Y", $time); // not this year
        if($time > $yearAgo) return date("j M", $time); // not this month

        // failed to prettify somehow
        return $datestamp;
    }
}
