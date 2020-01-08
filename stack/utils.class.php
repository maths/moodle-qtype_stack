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
 * Various utility classes for Stack.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Interface for a class that stores debug information (or not).
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface stack_debug_log {

    /**
     * @return string the contents of the log.
     */
    public function get_log();

    /**
     * Add to the log
     * @param string $heading a heading to precede the acutal message.
     * @param string $message the debug message.
     */
    public function log($heading = '', $message = '');
}


/**
 * Interface for a class that stores debug information (or not).
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_debug_log_base implements stack_debug_log {

    protected $debuginfo = '';

    /**
     * @return string the contents of the log.
     */
    public function get_log() {
        return $this->debuginfo;
    }

    /**
     * Add to the log
     * @param string $heading a heading to precede the acutal message.
     * @param string $message the debug message.
     */
    public function log($heading = '', $message = '') {
        if ($heading) {
            $this->debuginfo .= html_writer::tag('h3', $heading);
        }
        if ($message) {
            $this->debuginfo .= html_writer::tag('pre', s($message));
        }
    }
}


/**
 * A null stack_debug_log. Does not acutally log anything. Used when debugging is off.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_debug_log_null implements stack_debug_log {

    /**
     * @return string the contents of the log.
     */
    public function get_log() {
        return '';
    }

    /**
     * Add to the log
     * @param string $heading a heading to precede the acutal message.
     * @param string $message the debug message.
     */
    public function log($heading = '', $message = '') {
        // Do nothing.
    }
}


/**
 * Utility methods for processing strings.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_utils {
    /** @var object the STACK config data, so we only ever have to load it from the DB once. */
    protected static $config = null;

    /** @var A list of mathematics environments we search for, from AMSmath package 2.0. */
    protected static $mathdelimiters = array('equation', 'align', 'gather', 'flalign', 'multline', 'alignat', 'split');

    /**
     * @var string fragment of regular expression that matches valid PRT and
     * input names.
     */
    const VALID_NAME_REGEX = '[a-zA-Z][a-zA-Z0-9_]*';

    /**
     * Static class. You cannot create instances.
     */
    private function __construct() {
        throw new stack_exception('stack_utils: you cannot create instances of this class.');
    }

    /**
     * Create a debug log that either does, or does not, log anything.
     * @param bool $debugenabled Whether we actually want to keep a debug log.
     * @return stack_debug_log the log
     */
    public static function make_debug_log($debugenabled = true) {
        if ($debugenabled) {
            return new stack_debug_log_base();
        } else {
            return new stack_debug_log_null();
        }
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
     * Check that the opening and closing brackets match, including nesting.
     * This method only works with pairs of characters that are different, like ().
     * It cannot cope with matching "", for example.
     * @param string $string the string to test.
     * @param string $lefts opening bracket characters. By default '([{'.
     * @param string $rights the corresponding closing bracket characters. By default ')]}'.
     * @return boolean true if all brackets match and are nested properly.
     */
    public static function check_nested_bookends($string, $lefts = '([{', $rights = ')]}') {
        $openstack = array();
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            if (strpos($lefts, $char) !== false) {
                array_push($openstack, $char);

            } else if (($closerpos = strpos($rights, $char)) !== false) {
                $opener = array_pop($openstack); // NULL if array is empty, which works.
                if ($opener !== $lefts[$closerpos]) {
                    return false;
                }
            }
        }

        return empty($openstack);
    }

    /**
     * Gets the first sub-string between two specified delimiters from within
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
            // Left and right are the same.
            $end = strpos($string, $right, $start + 1); // Just go for the next one.
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

            if ($nesting > 0) {
                return array('', -1, -1);
            }
        }

        return array(substr($string, $start, $end - $start), $start, $end - 1);
    }

    /**
     * Gets the characters between two *characters*
     * Works throughout a string returning an array of matches
     *
     * @param string $string the string to analyse.
     * @param string $left the opening delimiter.
     * @param string $right the closing delimiter. If omitted, uses $left.
     * @param bool $skipempty whether to leave out any empty substrings.
     * @return array of matches without $left or $right pre/suffixes
     */
    public static function all_substring_between($string, $left, $right = null, $skipempty = false) {
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
                // Find starting @.
                if ($char[$i] == $left) {
                    $start = true;
                    $found .= $char[$i];
                }
            } else {
                // We have the first @ find ending @.
                if ($char[$i] == $right) {
                    // End of cas command found.
                    $found .= $char[$i];
                    $found = str_replace($left, '', $found);
                    $found = str_replace($right, '', $found);
                    $found = trim($found);
                    if (!$skipempty || $found) {
                        $var[$j] = $found;
                    }
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
     * @param bool $skipempty whether to leave out any empty substrings.
     * number of replacements.
     * @return string
     */
    public static function replace_between($string, $left, $right, $replacements, $skipempty = false) {
        // Do error checking.
        $leftcount = substr_count($string, $left);
        $rightcount = substr_count($string, $right);
        $replacecount = count($replacements);
        if ($left != $right && $leftcount != $rightcount) {
            throw new stack_exception('replace_between: delimiters don\'t match.');
        }

        $result = '';
        $matches = 0;
        $char = str_split($string);
        $length = count($char);
        $i = 0;
        $searching = true;
        while ($i < $length) {
            if ($searching) {
                // Trying to find startchar.
                if ($char[$i] == $left) {
                    $searching = false;
                    $empty = true;
                }
                $result .= $char[$i];
            } else {
                // Found startchar, looking for end.
                if ($char[$i] == $right) {
                    // @codingStandardsIgnoreStart
                    if ($skipempty && $empty) {
                        // Do nothing.
                    } else if (!isset($replacements[$matches])) {
                        throw new stack_exception('replace_between: not enough replacements.');
                    } else {
                        $result .= $replacements[$matches];
                        $matches++;
                    }
                    // @codingStandardsIgnoreEnd
                    $searching = true;
                    $result .= $char[$i];
                }
                $empty = false;
            }
            $i++;
        }
        if ($matches != count($replacements)) {
            throw new stack_exception('replace_between: too many replacements.');
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
    public static function convert_slash_paths($string) {
        $in = trim($string);
        $length = strlen($in);
        $lastchar = $in[($length - 1)];
        $trailingslash = false;
        if ($lastchar == '\\') {
            $trailingslash = true;
        }
        $patharray = self::cvs_to_array($string, "\\");
        if (!empty($patharray)) {
            $newpath = $patharray[0];
            for ($i = 1; $i < count($patharray); $i++) {
                $newpath .= "/".$patharray[$i];
            }
            if ($trailingslash == true) {
                return $newpath.'/';
            } else {
                return $newpath;
            }
        } else {
            return null;
        }
    }


    /**
     * Ensures that all elements within this text that need to be in math mode, are so.
     * Specifically, CAS elements and inline input macros.
     * @param string
     * @return string
     */
    public static function delimit($text) {
        return preg_replace_callback('/@([^@]*)@/', array('stack_utils', 'delimit_callback'), $text);
    }

    public static function delimit_callback($matches) {
        if (!empty($matches[1])) {
            return '\(@' . $matches[1] . '@\)';
        } else {
            return '@@';
        }
    }

    /**
     * Returns the first position of an opening math delimiter in $text from the $offset.
     * Helper function for wrap_around().
     */
    public static function math_start($text, $offset = 0) {
        $delimiters = array('$', '$$', '\(', '\[');
        foreach (self::$mathdelimiters as $delim) {
            $delimiters[] = '\begin{'.$delim.'}';
            $delimiters[] = '\begin{'.$delim.'*}';
        }
        $at = false; // Not yet found.
        foreach ($delimiters as $d) {
            $pos = strpos($text, $d, $offset);
            if ($pos !== false) { // Found one.
                if (($at === false || $pos <= $at)) {// take earliest ($$ taken over $)
                    $at = $pos; // Take earliest.
                }
            }
        }
        return $at;
    }

    /**
     * Returns the position of the character following a closing math delimiter in $text from the $offset.
     * Helper function for wrap_around().
     */
    public static function math_length($text, $start) {
        $delimiters = array('$', '$$', '\)', '\]');
        foreach (self::$mathdelimiters as $delim) {
            $delimiters[] = '\end{'.$delim.'}';
            $delimiters[] = '\end{'.$delim.'*}';
        }
        $at = false;
        $ender = '';
        $len = strlen($text);

        // Handle case where less than 3 chars to consider.
        if ($len <= $start + 2) {
            return $len - $start;
        }

        foreach ($delimiters as $d) {
            $pos = strpos($text, $d, $start + 2); // Check long enough.
            if ($pos !== false) { // Found one.
                if ($at === false || $pos <= $at) { // Take earliest ($$ taken over $).
                    $at = $pos;
                    $ender = $d;
                }
            }
        }
        if ($ender == '') {
            return strlen($text - $start);
            // Math mode to the end.
        } else {
            return $at - $start + strlen($ender);
        }
    }

    /**
     * Replaces @blah@ with \(@blah@\) if the castext is not otherwise enclosed by mathematics environments.
     * @param string
     * @return string
     */
    public static function wrap_around($text) {
        $mathstart = self::math_start($text);
        if ($mathstart !== false) { // We have some maths ahead.
            $pre = substr($text, 0, $mathstart); // Get previous text.
            $for = self::math_length($text, $mathstart);
            $maths = substr($text, $mathstart, $for);
            $rest = substr($text, $mathstart + $for);
            return self::delimit($pre).$maths.self::wrap_around($rest);
        } else { // No math sections left.
            return self::delimit($text);
        }
    }

    /**
     * Removes any whitespace, ';' ':' or '$' signs from the end of cas command.
     *
     * @return string out
     * @access public
     */
    public static function trim_commands($string) {
        $in = trim($string);
        $length = strlen($in);
        $lastchar = $in[($length - 1)];

        if (($lastchar == '$') || ($lastchar == ';') || ($lastchar == ':')) {
            $out = substr($in, 0, ($length - 1));
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
    public static function remove_comments($string) {
        if (strstr($string, '/*')) {
            $out = $string;
            preg_match_all('|/\*(.*)\*/|U', $out, $htmlmatch);
            foreach ($htmlmatch[0] as $val) {
                $out = str_replace($val, '', $out);
            }
            return $out;
        } else {
            return $string;
        }
    }

    /**
     * Extracts double quoted strings with \-escapes, extracts only the content
     * not the quotes.
     *
     * @access public
     * @return array
     */
    public static function all_substring_strings($string) {
        $strings = array();
        $i = 0;
        $lastslash = false;
        $instring = false;
        $stringentry = -1;
        while ($i < strlen($string)) {
            $c = $string[$i];
            $i++;
            if ($instring) {
                if ($c == '"' && !$lastslash) {
                    $instring = false;
                    // Last -1 to drop the quote.
                    $s = substr($string, $stringentry, ($i - $stringentry) - 1);
                    $strings[] = $s;
                } else if ($c == "\\") {
                    $lastslash = !$lastslash;
                } else if ($lastslash) {
                    $lastslash = false;
                }
            } else if ($c == '"') {
                $instring = true;
                $lastslash = false;
                $stringentry = $i;
            }
        }
        return $strings;
    }

    /**
     * Replaces all Maxima strings with zero length strings to eliminate string
     * contents for validation tasks.
     *
     * @access public
     * @return string
     */
    public static function eliminate_strings($string) {
        $cleared = '';
        $i = 0;
        $lastslash = false;
        $instring = false;
        $laststringexit = 0;
        while ($i < strlen($string)) {
            $c = $string[$i];
            $i++;
            if ($instring) {
                if ($c == '"' && !$lastslash) {
                    $instring = false;
                    $laststringexit = $i - 1;
                } else if ($c == "\\") {
                    $lastslash = !$lastslash;
                } else if ($lastslash) {
                    $lastslash = false;
                }
            } else if ($c == '"') {
                $instring = true;
                $lastslash = false;
                $cleared .= substr($string, $laststringexit, $i - $laststringexit);
            }
        }
        $cleared .= substr($string, $laststringexit);
        return $cleared;
    }

    /**
     * Converts a CSV string into an array, removing empty entries.
     *
     * @param string in
     * @return array out
     * @access public
     */
    public static function cvs_to_array($string, $token = ',') {
        $exploded = explode($token, $string);
        // Remove any null entries.
        for ($i = 0; $i < count($exploded); $i++) {
            $trim = trim($exploded[$i]);
            if (!empty($trim)) {
                $toreturn[] = $exploded[$i];
            }
        }
        return $toreturn;
    }

    /**
     * Converts an array to a CSV.
     *
     * @param $array the data to output.
     * @return string the output.
     */
    public static function array_to_cvs($array) {
        if (!empty($array)) {
            $string = '';
            $i = 0;
            foreach ($array as $element) {
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
            return '';
        }
    }

    /**
     * Handles complex (comma-containing) list elements,
     * i.e. sets {}, functions() and nested lists[[]]
     * Strict checking on nesting.
     * Helper for list_to_array_workhorse()
     */
    private static function next_element($list) {
        if ($list == '') {
            return null;
        }
        // Delimited by next comma at same degree of nesting.
        $startdelimiter = "[({";
        $enddelimiter   = "])}";
        $nesting = array(0 => 0, 1 => 0, 2 => 0); // Stores nesting for delimiters above.
        for ($i = 0; $i < strlen($list); $i++) {
            $startchar = strpos($startdelimiter, $list[$i]); // Which start delimiter.
            $endchar = strpos($enddelimiter, $list[$i]); // Which end delimiter (if any).

            // Change nesting for delimiter if specified.
            if ($startchar !== false) {
                $nesting[$startchar]++;
            } else if ($endchar !== false) {
                $nesting[$endchar]--;
            } else if ($list[$i] == ',' && $nesting[0] == 0 && $nesting[1] == 0 &&$nesting[2] == 0) {
                // Otherwise, return element if all nestings are zero.
                return substr($list, 0, $i);
            }
        }

        // End of list reached.
        if ($nesting[0] == 0 && $nesting[1] == 0 &&$nesting[2] == 0) {
            return $list;
        } else {
            return null;
        }
    }

    private static function list_to_array_workhorse($list, $rec = true) {
        $array = array();
        $list = trim($list);
        $list = substr($list, 1, strlen($list) - 2); // Trims outermost [] only.
        $e = self::next_element($list);
        while ($e !== null) {
            if ($e[0] == '[') {
                if ($rec) {
                    $array[] = self::list_to_array_workhorse($e, $rec);
                } else {
                    $array[] = $e;
                }
            } else {
                $array[] = $e;
            }
            $list = substr($list, strlen($e) + 1);
            $e = self::next_element($list);
        }
        return $array;
    }

    /**
     * Converts a list structure into an array.
     * Handles nested lists, sets and functions with help from next_element().
     */
    public static function list_to_array($string, $rec = true) {
        return self::list_to_array_workhorse($string, $rec);
    }

    /**
     * Extract the names of all the placeholders like [[{$type}:{$name}]] from
     * a bit of text. Names must start with an ASCII letter, and be comprised of
     * ASCII letters, numbers and underscores.
     *
     * @param string $text some text. E.g. '[[input:ans1]]'.
     * @param string $type the type of placeholder to extract. e.g. 'input'.
     * @return array of placeholdernames.
     */
    public static function extract_placeholders($text, $type) {
        preg_match_all('~\[\[' . $type . ':(' . self::VALID_NAME_REGEX . ')\]\]~',
                $text, $matches);
        return $matches[1];
    }

    /**
     * Extract what look like "sloppy" placeholders like [[{$type}:{$name}]] from
     * a bit of text. We forbit bits of whitespace between the various bits.
     * Modelled on public static function extract_placeholders($text, $type)
     *
     * @param string $text some text. E.g. '[[input:ans1]]'.
     * @param string $type the type of placeholder to extract. e.g. 'input'.
     * @return array of placeholdernames.
     */
    public static function extract_placeholders_sloppy($text, $type) {
        preg_match_all('~\[\[' . $type . ':(' . self::VALID_NAME_REGEX . ')\]\]~',
                $text, $matches1);
        preg_match_all('~\[\[\s*' . $type . '\s*:(\s*' . self::VALID_NAME_REGEX . ')\s*\]\]~',
                $text, $matches2);

        $ret = array();
        foreach ($matches2[1] as $key => $name) {
            if (!in_array(trim($name), $matches1[1])) {
                $ret[] = $matches2[0][$key];
            }
        }
        return($ret);
    }

    /**
     * @param string $name a potential name for part of a STACK question.
     * @return bool whether that name is allowed.
     */
    public static function is_valid_name($name) {
        return preg_match('~^' . self::VALID_NAME_REGEX . '$~', $name);
    }

    /** Get the stack configuration settings. */
    public static function get_config() {
        if (is_null(self::$config)) {
            self::$config = get_config('qtype_stack');
        }
        return self::$config;
    }

    public static function clear_config_cache() {
        self::$config = null;
    }

    /**
     * This breaks down a complex rename of the names of a set of things, so that
     * the renames may safely be performed one-at-a-time. This is easier to understand
     * with an example:
     *
     * Suppose the input is array(1 => 2, 2 => 1). Then the output will be
     * array (1 => temp1, 2 => 1, temp1 => 2).
     *
     * This function can solve this problem in the general case.
     *
     * @param array $renamemap a mapping from oldname => newname for a set of things.
     * @return array $saferenames a sequence of single rename operations,
     *      oldname => newname that when performed in order, will not cause a
     *      name clash.
     */
    public static function decompose_rename_operation(array $renamemap) {

        $nontrivialmap = array();
        $usednames = array();
        foreach ($renamemap as $from => $to) {
            $usednames[(string) $from] = 1;
            $usednames[(string) $to] = 1;
            if ((string) $from !== (string) $to) {
                $nontrivialmap[(string) $from] = (string) $to;
            }
        }

        if (empty($nontrivialmap)) {
            return array();
        }

        // First we deal with all renames that are not part of cycles.
        // This bit is O(n^2) and it ought to be possible to do better,
        // but it does not seem worth the effort.
        $saferenames = array();
        $todocount = count($nontrivialmap) + 1;
        while (count($nontrivialmap) < $todocount) {
            $todocount = count($nontrivialmap);

            foreach ($nontrivialmap as $from => $to) {
                if (array_key_exists($to, $nontrivialmap)) {
                    continue; // Cannot currenly do this rename.
                }
                // Is safe to do this rename now.
                $saferenames[$from] = $to;
                unset($nontrivialmap[$from]);
            }
        }

        // Are we done?
        if (empty($nontrivialmap)) {
            return $saferenames;
        }

        // Now, what is left in $nontrivialmap will permutation, which must be a
        // combination of distinct cycles. We need to break them.
        $tempname = self::get_next_unused_name($usednames);
        while (!empty($nontrivialmap)) {
            // Extract the first cycle.
            reset($nontrivialmap);
            $current = $cyclestart = (string) key($nontrivialmap);
            $cycle = array();
            do {
                $cycle[] = $current;
                $next = $nontrivialmap[$current];
                unset($nontrivialmap[$current]);
                $current = $next;
            } while ($current !== $cyclestart);

            // Now convert it to a sequence of safe renames by using a temp.
            $saferenames[$cyclestart] = $tempname;
            $cycle[0] = $tempname;
            $to = $cyclestart;
            while ($from = array_pop($cycle)) {
                $saferenames[$from] = $to;
                $to = $from;
            }

            $tempname = self::get_next_unused_name($usednames, ++$tempname);
        }

        return $saferenames;
    }

    /**
     * Get an name that is not used as a key in $usednames.
     * @param array $usednames where the keys are the used names.
     * @param string $suggestedname the form the name should take. Default 'temp1'.
     * @return string an unused name.
     */
    protected static function get_next_unused_name($usednames, $suggestedname = 'temp1') {
        while (array_key_exists($suggestedname, $usednames)) {
            $suggestedname++;
        }
        return $suggestedname;
    }

    /**
     * Locale-aware version of PHP's asort function.
     * @param array $array The array to sort. Sorted in place.
     */
    public static function sort_array(&$array) {
        if (class_exists('core_collator')) {
            core_collator::asort($array);
        } else {
            collatorlib::asort($array);
        }
    }

    /**
     * Locale-aware version of PHP's ksort function.
     * @param array $array The array to sort. Sorted in place.
     */
    public static function sort_array_by_key(&$array) {
        if (class_exists('core_collator')) {
            core_collator::ksort($array);
        } else {
            collatorlib::ksort($array);
        }
    }

    /**
     * Converts a PHP string object to a PHP string object containing the Maxima code that would generate a similar
     * string in Maxima.
     * @param a string
     * @return a string that contains ""-quotes around the content.
     */
    public static function php_string_to_maxima_string($string) {
        $converted = str_replace("\\", "\\\\", $string);
        $converted = str_replace("\"", "\\\"", $converted);
        return '"' . $converted . '"';
    }
    /**
     * Converts a PHP string object containing a Maxima string as presented by the grind command to a PHP string object.
     * @param a string that contains ""-quotes around the content.
     * @return a string without those quotes.
     */
    public static function maxima_string_to_php_string($string) {
        $converted = str_replace("\\\\", "\\", $string);
        $converted = str_replace("\\\"", '"', $converted);
        return substr($converted, 1, -1);
    }

    /**
     * Find a rational approximation to $n
     * @param float $n
     * @param int $accuracy Stop when we get within this many decimal places of $n
     */
    public static function rational_approximation($n, $accuracy) {
        $accuracy = pow(10, -$accuracy);

        $i = floor($n);
        if ($i == $n) { // If n is an integer, its rational representation is obvious.
            return array($n, 1);
        }

        // Take away the integer part of n.
        // From now on, we can assume 0 < n < 1.
        $nint = $i;
        $n = $n - $i;

        // We'll keep track of our working as (numx*n +numc)/(denx*n+denc).
        $numx = 0;
        $numc = 1;
        $denx = 1;
        $denc = 0;

        $frac = array(); // Continued fraction coefficients.
        $diff = $n - $i; // Difference between current approximation and n.

        $steps = 0;
        $onum = 0;
        $oden = 1;
        while (abs($diff) > $accuracy && $steps < 1000) {
            $steps = $steps + 1;

            // Evaluate current working to a fraction.
            $nume = $numx * $n + $numc;
            $dene = $denx * $n + $denc;
            $div = $nume / $dene; // Then to a float.
            $i = floor($div); // Integer part - this is the next coefficient in the continued fraction.
            if ($dene <= $nume) {
                // If i>=1.
                array_unshift($frac, $i);
            }

            // Reduce the continued fraction.
            $onum = 0;
            $oden = 1;
            foreach ($frac as $c) {
                list($oden, $onum) = array($oden * $c + $onum, $oden);
            }
            $diff = $n - $onum / $oden;

            // Subtract i from our working, and then take its reciprocal.
            list($numx, $numc, $denx, $denc) = array($denx, $denc, $numx - $denx * $i, $numc - $denc * $i);
        }
        return array($nint * $oden + $onum, $oden);
    }

    public static function fix_to_continued_fraction($n, $accuracy) {
        $frac = self::rational_approximation($n, $accuracy);
        return $frac[0] / $frac[1];
    }

    /**
     * Change fraction marks close to 1/3 or 2/3 to the values exact to 7 decimal places.
     *
     * Moodle rounds fractional marks close to 1/3 (0.33 <= x <= 0.34) or 2/3
     * (0.66 <= x <= 0.67) to exactly 0.3333333 and 0.6666667, for example whe @author tjh238
     * course is backed up and restored. Some of the fractional marks that STACK
     * uses are affected by this, and others are not. Thereofore, after a course
     * is backed up and restored, some question tests start failing.
     *
     * Therefore, this fucntion is used to match Moodle's logic.
     *
     * @param float $fraction a fractional mark between 0 and 1.
     * @return float $fraction, except that values close to 1/3 or 2/3 are returned to 7 decimal places.
     */
    public static function fix_approximate_thirds($fraction) {
        if ($fraction >= 0.33 && $fraction <= 0.34) {
            return 0.3333333;
        } else if ($fraction >= 0.66 && $fraction <= 0.67) {
            return 0.6666667;
        } else {
            return $fraction;
        }
    }

    /*
     * This function takes user input of the form "option:arg" and splits them up.
     * Used to sort out options to the inputs field.
     */
    public static function parse_option($option) {
        $arg = '';
        if (!(strpos($option, ':') === false)) {
            $ops = explode(':', $option);
            $option = $ops[0];
            $arg = trim($ops[1]);
        }
        return(array($option, $arg));
    }
}
