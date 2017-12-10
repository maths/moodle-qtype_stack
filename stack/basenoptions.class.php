<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basenoptions
 *
 * @author parrysg
 */
class stack_basen_options {
    
    const BASENMODE_COMPATIBLE = 0;        // Default STACK mode - does not work for base 11+
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
    const BASENMODE_MAP = [
            "D" => self::BASENMODE_COMPATIBLE,
            "" => self::BASENMODE_COMPATIBLE,
            Null => self::BASENMODE_COMPATIBLE,
            "M" => self::BASENMODE_ZERO_PREFIX,
            "G" => self::BASENMODE_GREEDY,
            "C" => self::BASENMODE_C,
            "B" => self::BASENMODE_BASIC,
            "S" => self::BASENMODE_SUFFIX,
            "_" => self::BASENMODE_SUFFIX ];

    public static function basen_mode_to_num($mode) {
        if(is_numeric($mode)) {
            return (int)$mode;
        } else {
            return key_exists($mode, self::BASENMODE_MAP) ? self::BASENMODE_MAP[$mode] : 0;
        }
    }

    private $radix = 0;
    private $mindigits=0;
    private $mode=0;
    
    public function __construct($radix = 0, $mindigits = 0, $mode=1)
    {
        $this->radix = $radix;
        $this->mindigits = $mindigits;
        $this->mode = self::basen_mode_to_num($mode);
    }
    
    public function get_radix()
    {
        return $this->radix;
    }

    public function get_mindigits()
    {
        return $this->mindigits;
    }

    public function get_mode()
    {
        return $this->mode;
    }
}
