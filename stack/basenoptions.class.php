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
 * Represents all the options related to converting to / from a base N number.
 *
 * @property-read int $radix the number base 2 = binary etc.
 * @property-read int $mode one of the mode constants controlling the format of base-N literals.
 * @property-read int $mindigits the minimum number of digits used to display the base n number - padded with zeroes.
 * @copyright 2017 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Stephen Parry <stephen@edumake.org>
 */
class stack_basen_options {

    const BASENMODE_COMPATIBLE = 0;     // Default STACK mode - does not work for base 11+
    const BASENMODE_ZERO_PREFIX = 1;    // Maxima mode - a valid base 11+ number must be zero prefixed
                                        // The zero is not included in mindig counts.
                                        // base 10- are written as normal integers - float literals
                                        // prefixed zero may be misinterpreted - e.g. 0e0
    const BASENMODE_GREEDY = 2;         // Greedy mode - treat all literals starting with a letter or
                                        // digit in the correct base range as a numeric. Will break
                                        // most formulae because of confusion with var names and
                                        // scientific notation, but good for simple literal values
    const BASENMODE_C = 3;              // Numbers are parsed and interpreted as per C
                                        // 0bN => base 2
                                        // 0N => base 8
                                        // 0xN => base 16
    const BASENMODE_BASIC = 4;          // Numbers are parsed and interpreted as per BASIC
                                        // &B => base 2
                                        // &O => base 8
                                        // &H => base 16
    const BASENMODE_SUFFIX = 5;         // Numbers are parsed and interpreted as per traditional maths
                                        // with the base indicated as a subscript suffix
    const BASENMODE_CHOICE = 64;        // Combined with B,C or S mode, this allows the student to choose
                                        // the radix they use themselves; they must enter the number in
                                        // correct format for that base.
    const BASENMODE_RIGHT_PAD = 128;    // Combined with D,M or G mode, the number reads as if padded from the right with
                                        // zeroes, i.e. the most significant digit is fixed as maximum value. Useful
                                        // for processing fixed point numbers.

    const BASENMODE_MAP = [
        "D" => self::BASENMODE_COMPATIBLE,
        "D<" => self::BASENMODE_COMPATIBLE | self::BASENMODE_RIGHT_PAD,
        "" => self::BASENMODE_COMPATIBLE,
        null => self::BASENMODE_COMPATIBLE,
        "M" => self::BASENMODE_ZERO_PREFIX,
        "M<" => self::BASENMODE_ZERO_PREFIX | self::BASENMODE_RIGHT_PAD,
        "G" => self::BASENMODE_GREEDY,
        "G<" => self::BASENMODE_GREEDY | self::BASENMODE_RIGHT_PAD,
        "C" => self::BASENMODE_C,
        "C*" => self::BASENMODE_C | self::BASENMODE_CHOICE,
        "B" => self::BASENMODE_BASIC,
        "B*" => self::BASENMODE_BASIC | self::BASENMODE_CHOICE,
        "S" => self::BASENMODE_SUFFIX,
        "S*" => self::BASENMODE_SUFFIX | self::BASENMODE_CHOICE,
        "_" => self::BASENMODE_SUFFIX,
        "_*" => self::BASENMODE_SUFFIX | self::BASENMODE_CHOICE];

    /**
     * converts a base N mode to integer
     * @mode mixed the mode to convert - if integer then just passed; if string then converted
     */

    public static function basen_mode_to_num($mode) {
        if (is_numeric($mode)) {
            return (int) $mode;
        } else {
            return key_exists($mode, self::BASENMODE_MAP) ? self::BASENMODE_MAP[$mode] : 0;
        }
    }

    private $radix = 0;
    private $mindigits = 0;
    private $mode = 0;

    /**
     * Constructor
     *
     * @param int $radix the number base 2 = binary etc.
     * @param int $mode one of the mode constants controlling the format of base-N literals.
     * @param int $mindigits the minimum number of digits used to display the base n number
     *      - padded with zeroes.
     */
    public function __construct($radix = 0, $mode = 1, $mindigits = 0) {
        $this->radix = $radix;
        $this->mindigits = $mindigits;
        $this->mode = self::basen_mode_to_num($mode);
    }

    public function get_radix() {
        return $this->radix;
    }

    public function get_mindigits() {
        return $this->mindigits;
    }

    public function get_mode() {
        return $this->mode;
    }

    public function get_choice() {
        return ((($this->get_mode()) & self::BASENMODE_CHOICE) != 0);
    }

    public function get_rightpad() {
        return ((($this->get_mode()) & self::BASENMODE_RIGHT_PAD) != 0);
    }

    private static function digit_range_pattern($radix, $underscoresallowed) {
        $r = abs($radix) - 1;
        if ($radix <= 10) {
            $pattern = "0-$r";
        } else if ($radix == 11) {
            $pattern = "0-Aa";
        } else {
            $pattern = "0-9A-" . chr(ord("A") + $radix - 11) . "a-" . chr(ord("a") + $radix - 11);
        }
        if ($underscoresallowed) {
            $pattern .= "_";
        }
        return $pattern;
    }

    private static function digit_pattern($radix, $underscoresallowed) {
        return "[" . self::digit_range_pattern($radix, $underscoresallowed) . "]";
    }

    private static function number_pattern($mode, $radix) {
        if ($mode == self::BASENMODE_C) {
            if (abs($radix) == 2) {
                $pattern = "0[bB]" . self::digit_pattern($radix, true) . "+";
            } else if ($radix == 8) {
                $pattern = "0" . self::digit_pattern($radix, true) . "+";
            } else if ($radix == 10) {
                $pattern = "[1-9][0-9_]*";
            } else if ($radix == 16) {
                $pattern = "0[xX]" . self::digit_pattern($radix, true) . "+";
            }
        } else if ($mode == self::BASENMODE_BASIC) {
            if (abs($radix) == 2) {
                $pattern = "&[bB]" . self::digit_pattern($radix, true) . "+";
            } else if ($radix == 8) {
                $pattern = "&[oO]" . self::digit_pattern($radix, true) . "+";
            } else if ($radix == 10) {
                $pattern = "[0-9][0-9_]*";
            } else if ($radix == 16) {
                $pattern = "&[xX]" . self::digit_pattern($radix, true) . "+";
            }
        } else if ($mode == self::BASENMODE_SUFFIX) {
            $pattern = self::digit_pattern($radix, false) . "+" . "_abs($radix)";
        }
        return $pattern;
    }

    private function full_number_pattern(bool $tail) {
        $choice = $this->get_choice();
        $rightpad = $this->get_rightpad();
        $mode = ($this->get_mode()) & ~(self::BASENMODE_CHOICE | self::BASENMODE_RIGHT_PAD);
        $radix = $this->get_radix();
        if ($mode == self::BASENMODE_COMPATIBLE) {
            $pattern = self::digit_pattern(min($radix, 10), false) . self::digit_pattern($radix, true) . "*";
            $rpattern = self::digit_range_pattern($mode, min($radix, 10));
        } else if ($mode == self::BASENMODE_ZERO_PREFIX) {
            if ($radix <= 10) {
                $pattern = self::digit_pattern($radix, false) . self::digit_pattern($radix, true) . "*";
            } else {
                $pattern = "0" . self::digit_pattern($radix, true) . "*";
            }
            $rpattern = self::digit_range_pattern($mode, $radix);
        } else if ($mode == self::BASENMODE_GREEDY) {
            $pattern = self::digit_pattern($radix, false) . self::digit_pattern($radix, true) . "*";
            $rpattern = self::digit_range_pattern($mode, $radix);
        } else if ($mode == self::BASENMODE_SUFFIX) {
            if ($choice) {
                $pattern = "(?:" . self::number_pattern($mode, 2);
                for ($i = 3; $i <= 36; $i++) {
                    $pattern .= "|";
                    $pattern .= self::number_pattern($mode, $i);
                }
                $pattern .= ")";
            } else {
                $pattern = self::number_pattern($mode, $radix);
            }
            $rpattern = self::digit_range_pattern($mode, 10);
        } else {
            if ($choice) {
                $pattern = "(?:" .
                        self::number_pattern($mode, 2) . "|" .
                        self::number_pattern($mode, 8) . "|" .
                        self::number_pattern($mode, 10) . "|" .
                        self::number_pattern($mode, 16) . ")";
                $rpattern = self::digit_range_pattern($mode, 16);
            } else {
                $pattern = self::number_pattern($mode, $radix);
                $rpattern = self::digit_range_pattern($mode, 16);
            }
        }
        if ($tail) {
            $pattern = "(" . $pattern . ")([^" . $rpattern . ".EeBb]|$)";
        }
        return $pattern;
    }

    /**
     * Surround any base N literals in a supplied string with quotes and markers that can
     * easily be replaced later by calls to basen(frombasen()). The literals must be in
     * the format dictated by this options object. This function is used to protect
     * any base n literals in a students input from being trashed by the validation
     * routines.
     * @param   string  $string the string possibly containing base N literals.
     * @returns string  the string with literals surrounded with quotes and markers.
     */
    public function inject_temp_escapes($string) {
        $pattern = "/" . $this->full_number_pattern(true) . "/";
        return preg_replace($pattern, "![!\"$1\"!]!$2", $string);
    }

    /**
     * Replace any temporary base N literals escapes in a supplied string with calls to basen(frombasen()).
     * The calls will be given parameters from  this options object.
     * @param   string  $string the string possibly containing base N literals temp escapes.
     * @returns string  the string with literals wrapped in function calls.
     */
    public function upgrade_escapes($string) {
        $tail = "," . $this->get_radix() . "," . $this->get_mode() . "," . $this->get_mindigits() . ")";
        return str_replace("![!", "basen(frombasen(", str_replace("!]!", "$tail$tail", $string));
    }

    /**
     * Searches a string for any temporary base N escapes that the student may have attempted to include
     * in his/her answer.
     * @param   string  $string the string possibly containing temp escapes.
     * @returns string  NULL if no escapes present, the first escape if present.
     */
    public function check_for_escapes($string) {
        $matches = array();
        if (preg_match('![><]!', $string, $matches) != 0) {
            return $matches[0];
        } else {
            return null;
        }
    }

    public function check_residual_base10s($stringles) {
        return $this->get_choice() || preg_match("/[0-9]+/", $stringles) == 0;
    }

}

