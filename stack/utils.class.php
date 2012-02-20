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
 * Utility methods for processing strings.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_utils {

    /**
     * Static class. You cannot create instances.
     */
    private function __construct() {
        throw new Exception('stack_utils: you cannot create instances of this class.');
    }

    /**
     * Checks for matching pairs of characters in a string. Eg there are a even
     * number of '@' chars in 'blah @blah@ blah'
     *
     * @param string $string the string to test
     * @param string $char the character to test.
     * @return bool whether there are an even number of $chars in $string.
     */
    public static function check_matching_pairs($string, $char) {
        // Check the number of occurences are even.
        return (substr_count($string, $char) % 2) == 0;
    }

    /**
     * Check whether the number of left and right substrings match, for example
     * whether every <html> has a matching </html>.
     * Returns true if equal, 'left' left is missing, 'right' if right is missing.
     *
     * @param string $string the string to test.
     * @param string $left the left delimiter.
     * @param string $right the right delimiter.
     * @return bool|string true if they match; 'left' if there are left delimiters
     *      missing; or 'right' if there are right delimiters missing.
     */
    public static function check_bookends($string, $left, $right) {
        $leftcount = substr_count($string, $left);
        $rightcount = substr_count($string, $right);

        if ($leftcount == $rightcount) {
            return true;
        } else if ($leftcount > $rightcount) {
            return 'right';
        } else {
            return 'left';
        }
    }

    /**
     * Gets the the first sub-string between two specified delimiters from within
     * a larger string.
     *
     * @param string $string the string to analyse.
     * @param string $left the left delimiter character.
     * @param string $right the right delimiter character.
     * @param int $start the position of the string to start searching at. (Default 0)
     * @return array with three elements: (the extracted string including the delimiters,
     *      the start position of the substring, and the end position of the stubstring.
     *      If there is no match, ('', -1, -1) is returned. Well, acutally it is more
     *      complex than that. If you really care read the code.
     */
    public static function substring_between($string, $left, $right, $start = 0) {

        $start = strpos($string, $left, $start);
        if ($start === false) {
            return array('', -1, 0);
        }

        if ($left == $right) {
            // Left and right are the same
            $end = strpos($string, $right, $start + 1); // Just go for the next one
            if ($end === false) {
                return array('', $start, -1);
            }
            $end += 1;

        } else {
            $length = strlen($string);
            $nesting = 1;
            $end = $start + 1;

            while ($nesting > 0 && $end < $length) {
                if ($string[$end] == $left) {
                    $nesting += 1;
                } else if ($string[$end] == $right) {
                    $nesting -= 1;
                }
                $end++;
            }

        }

        return array(substr($string, $start, $end - $start), $start, $end - 1);
    }

    /**
     * Gets the characters between two chars
     * Works throughout a string returning an array of matches
     *
     * @param string $string the string to analyse.
     * @param string $left the opening delimiter.
     * @param string $right the closing delimiter. If omitted, uses $left.
     * @return array of matches without $left or $right pre/suffixes
     */
    static function all_substring_between($string, $left, $right = null) {
        if ($right == null) {
            $right = $left;
        }

        $char = str_split($string);
        $length = count($char);
        $var = array();
        $j = 0;
        $i = 0;
        $start = false;
        $found = '';
        while ($i < $length) {
            if ($start == false) {
                //find starting @
                if ($char[$i] == $left) {
                    $start = true;
                    $found .= $char[$i];
                }
            } else {
                //we have the first @ find ending @
                if ($char[$i] == $right) {
                    //end of cas command found
                    $found .= $char[$i];
                    $found = str_replace($left, '', $found);
                    $found = str_replace($right, '', $found);
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
     * Replaces the text between $left and $right with the next string from the array.
     * If the number of replacements does not match the number of strings to
     * replaces, an exception is thrown.
     *
     * @param string $string the string to analyse.
     * @param string $left the opening delimiter.
     * @param string $right the closing delimiter. If omitted, uses $left.
     * @param array $replacements array of replacement strings, must equal the
     * number of replacements.
     * @return string
     */
    static function replace_between($string, $left, $right, $replacements) {
        // Do error checking
        $leftcount = substr_count($string, $left);
        $rightcount = substr_count($string, $right);
        $replacecount = count($replacements);
        if ($left == $right) {
            if (2 * $replacecount != $leftcount) {
                throw new Exception('replace_between: wrong number of replacements');
            }
        } else {
            if (($leftcount != $rightcount) || ($replacecount != $leftcount)) {
                throw new Exception('replace_between: wrong number of replacements or delimiters don\'t match.');
            }
        }

        $result = '';
        $matches = 0;
        $char = str_split($string);
        $length = count($char);
        $i = 0;
        $searching = true;
        while ($i < $length) {
            if ($searching) {
                //trying to find startChar
                if ($char[$i] == $left) {
                    $searching = false;
                    $result .= $char[$i];
                    $result .= $replacements[$matches];
                    $matches++;
                } else {
                    $result .= $char[$i];
                }
            } else {
                //found startChar, looking for end
                if ($char[$i] == $right) {
                    $searching = true;
                    $result .= $char[$i];
                }
            }
            $i++;
        }
        return $result;
    }

    /**
     * Removes spaces, hyphens, and optionally other character from a string,
     * replacing them with an underscore characters.
     *
     * @param string the string to process.
     * @param array (Optional) additional characters to convert to underscores.
     * @return string with characters replaced.
     */
    public static function underscore($string, $toreplace = array()) {
        $toreplace[] = '-';
        $toreplace[] = ' ';
        return str_replace($toreplace, '_', $string);
    }

    /**
     * Converts windows style paths to unix style with forward slashes
     *
     * @access public
     * @return string|null
     */
    public static function convertSlashPaths($string) {
        $in = trim($string);
        $length = strlen($in);
        $lastChar = $in[($length -1)];
        $trailingSlash = false;
        if ($lastChar == '\\') {
            $trailingSlash = true;
        }
        $pathArray = self::cvsToArray($string, "\\");
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
    public static function wrapAround($string) {
        $string = preg_replace('/\\\\\$/', 'escapeddollar', $string);
        $string = self::wrap($string);
        $string = preg_replace('/escapeddollar/', '\\\\$', $string);
        return $string;
    }

    public static function CASdelimit($text) {
        return eregi_replace('|@(.*)@|U', '$\1$', $text);
    }

    /**
     * Ensures that all elements within this text that need to be in math mode, are so.
     * Specifically, CAS elements and inline input macros.
     */
    public static function delimit($text) {
        return preg_replace('/@(.*)?@/U', '$@\1@$', $text);
        //return preg_replace('/\\\\answer{.*}{.*}{.*}(.*)/U', '$@\1@$', $text);
    }

    /**
     * Returns the first position of an opening math delimiter in $text from the $offset.
     * Helper function for wrap().
     */
    public static function mathStart($text, $offset = 0) {
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
    public static function mathLength($text, $start) {
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

    public static function wrap($text) {
        $mathStart = self::mathStart($text);
        if ($mathStart !== false) { // we have some maths ahead
            $pre = substr($text, 0, $mathStart); // get previous text
            $for = self::mathLength($text, $mathStart);
            $maths = substr($text, $mathStart, $for);
            $rest = substr($text, $mathStart + $for);
            //echo '<br />wrapping '.$pre.':'.$maths.':'.$rest;
            return self::delimit($pre).$maths.self::wrap($rest);
        } else { // no math sections left
            return self::delimit($text);
        }
    }

    /**
     * Removes any whitespace, ';' ':' or '$' signs from the end of cas command.
     *
     * @return string out
     * @access public
     */
    public static function trimCommands($string) {
        $in = trim($string);
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
    public static function removeComments($string) {
        if (strstr($string, '/*')) {
            $out = $string;
            preg_match_all('|/\*(.*)\*/|U', $out,$html_match);
            foreach ($html_match[0] as $val) {
                $out = str_replace($val, '', $out);
            }
            return $out;
        } else {
            return $string;
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
    public static function removeBetween($string, $start, $end) {
        if (strstr($string,$start) && strstr($string, $end)) {
            $out = $string;
            preg_match_all('|'.$start.'(.*)'.$end.'|U', $out, $html_match);

            foreach ($html_match[0] as $val) {
                $out = str_replace($val, '', $out);
            }

            return $out;
        } else {
            return $string;
        }
    }

    /**
     * Converts a CSV string into an array, removing empty entries.
     *
     * @param string in
     * @return array out
     * @access public
     */
    public static function cvsToArray($string, $token = ',') {
        $exploded = explode($token, $string);
        // Remove any null entries
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
    public static function arrayToCSV($array) {
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
    private static function nextElement($list) {
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

    private static function listToArrayWorkhorse($list, $rec=true) {
        $array = array();
        $list = trim($list);
        $list = substr($list, 1, strlen($list) - 2);// trims outermost [] only
        $e = self::nextElement($list);
        while ($e !== null) {
            if ($e[0]=='[') {
                if ($rec) {
                    $array[] = self::listToArrayWorkhorse($e,$rec);
                } else {
                    $array[] = $e;
                }
            } else {
                $array[] = $e;
            }
            $list = substr($list, strlen($e)+1);
            $e = self::nextElement($list);
        }
        return $array;
    }

    /**
     * Converts a list structure into an array.
     * Handles nested lists, sets and functions with help from nextElement().
     */
    public static function listToArray($string, $rec = true) {
        return self::listToArrayWorkhorse($string, $rec);
    }

    /**
     * Returns a more intuitive datestamp
     * show time if this week
     * show day + month if this year
     * show month + year if not this year
     */
    public static function prettifyDate($datestamp) {

        $dayStart = strtotime("00:00");
        $monthAgo = strtotime('-1 month');
        $yearAgo = strtotime('-1 year');

        //echo "yearstart: $yearStart monthStart: $monthStart dayStart: $dayStart";

        $time = strtotime($datestamp);

        if ($time >= $dayStart) {
            return date("g:ia", $time);// today
        } else if ($time > $monthAgo) {
            return date("g:ia, j M", $time);// not today
        } else if ($time > $yearAgo) {
            return date("M Y", $time); // not this year
        } else if ($time > $yearAgo) {
            return date("j M", $time); // not this month
        }

        // failed to prettify somehow
        return $datestamp;
    }

}
