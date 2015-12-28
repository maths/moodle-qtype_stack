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
 * Functions related to dealing with scientific units in STACK.
 *
 * @copyright  2015 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

class stack_cas_casstring_units {

    private static $supportedprefix = array(
        array('y', '10^-24', 'y'),
        array('z', '10^-21', 'z'),
        array('a', '10^-18', 'a'),
        array('f', '10^-15', 'f'),
        array('p', '10^-12', 'p'),
        array('n', '10^-9', 'n'),
        array('u', '10^-6', '\mu '),
        array('m', '10^-3', 'm'),
        array('c', '10^-2', 'c'),
        array('d', '10^-1', 'd'),
        array('da', '10', 'da'),
        array('h', '10^2', 'h'),
        array('k', '10^3', 'k'),
        array('M', '10^6', 'M'),
        array('G', '10^9', 'G'),
        array('T', '10^12', 'T'),
        array('P', '10^15', 'P'),
        array('E', '10^18', 'E'),
        array('Z', '10^21', 'Z'),
        array('Y', '10^24', 'Y'),
    );

    private static $supportedunits = array(
            array('m', 'm', 'm'),
            array('g', 'kg/1000', 'g'),
            array('s', 's', 's'),
            array('A', 'A', 'A'),
            array('ohm', '(kg*m^2)/(s^3*A^2)', '\Omega'),
            array('K', 'K', 'K'),
            array('mol', 'mol', 'mol'),
            array('cd', 'cd', 'cd'),
            array('Hz', '1/s', 'Hz'),
            array('N', '(kg*m)/s^2', 'N'),
            array('Pa', 'kg/(m*s^2)', 'Pa'),
            array('J', '(kg*m^2)/s^2', 'J'),
            array('W', '(kg*m^2)/s^3', 'W'),
            array('C', 's*A', 'C'),
            array('V', '(kg*m^2)/(s^3*A)', 'V'),
            array('F', '(s^4*A^2)/(kg*m^2)', 'F'),
            array('S', '(s^3*A^2)/(kg*m^2)', 'S'),
            array('Wb', '(kg*m^2)/(s^2*A)', 'Wb'),
            array('T', 'kg/(s^2*A)', 'T'),
            array('H', '(kg*m^2)/(s^2*A^2)', 'H'),
            array('l', 'm^3/1000', 'l'),
            array('Bq', '1/s', 'Bq'),
            array('Gy', 'm^2/s^2', 'Gy'),
            array('Sv', 'm^2/s^2', 'Sv'),
            array('lm', 'cd', 'lm'),
            array('lx', 'cd/m^2', 'lx')
    );

    public function __construct() {
    }

    public static function maximalocal_units() {

        $maximalocal = "    /* Define units available in STACK. */\n";

        $code = array();
        $multiplier = array();
        $tex = array();
        foreach (self::$supportedprefix as $unit) {
            $code[] = $unit[0];
            $multiplier[] = $unit[1];
            $tex[] = '"'.$unit[2].'"';
        }
        $maximalocal .= '    stack_unit_si_prefix_code:['. implode($code, ', '). "],\n";
        $maximalocal .= '    stack_unit_si_prefix_multiplier:['. implode($multiplier, ', '). "],\n";
        $maximalocal .= '    stack_unit_si_prefix_tex:['. implode($tex, ', '). "],\n";

        $code = array();
        $conversions = array();
        $tex = array();
        foreach (self::$supportedunits as $unit) {
            $code[] = $unit[0];
            $conversions[] = $unit[1];
            $tex[] = '"'.$unit[2].'"';
        }

        $maximalocal .= '    stack_unit_si_unit_code:['. implode($code, ', '). "],\n";
        $maximalocal .= '    stack_unit_si_unit_conversions:['. implode($conversions, ', '). "],\n";
        $maximalocal .= '    stack_unit_si_unit_tex:['. implode($tex, ', '). "],\n";

        return $maximalocal;
        }

}
