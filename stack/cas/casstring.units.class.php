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

// Functions related to dealing with scientific units in STACK.
//
// @copyright  2015 University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

class stack_cas_casstring_units {

    /*
     * Entries in this array are supported prefix mulipliers.
     * They are in the form of array(label, multiplier, TeX, fullname).
     */
    private static $supportedprefix = array(
        array('y', '10^-24', 'y', 'yocto'),
        array('z', '10^-21', 'z', 'zepto'),
        array('a', '10^-18', 'a', 'atto'),
        array('f', '10^-15', 'f', 'femto'),
        array('p', '10^-12', 'p', 'pico'),
        array('n', '10^-9', 'n', 'nano'),
        array('u', '10^-6', '\mu ', 'micro'),
        array('m', '10^-3', 'm', 'milli'),
        array('c', '10^-2', 'c', 'centi'),
        array('d', '10^-1', 'd', 'deci'),
        array('da', '10', 'da', 'deca'),
        array('h', '10^2', 'h', 'hecto'),
        array('k', '10^3', 'k', 'kilo'),
        array('M', '10^6', 'M', 'mega'),
        array('G', '10^9', 'G', 'giga'),
        array('T', '10^12', 'T', 'tera'),
        array('P', '10^15', 'P', 'peta'),
        array('E', '10^18', 'E', 'exa'),
        array('Z', '10^21', 'Z', 'zetta'),
        array('Y', '10^24', 'Y', 'yotta'),
    );

    /*
     * Entries in this array are supported units.
     * For more informatio on SI, see
     * http://www.bipm.org/utils/common/pdf/si_brochure_8_en.pdf
     * The seven base quantities are length, mass, time, electric current,
     * thermodynamic temperature, amount of substance, and luminous intensity.
     *
     * Entries below are in the form of array(label, base, TeX, fullname).
     */
    private static $supportedunits = array(
        array('m', 'm', 'm', 'meter'),
        array('l', 'm^3/1000', 'l', 'litre'),
        // People have asked for this duplication, and L is in the SI system as legitimate.
        array('L', 'm^3/1000', 'L', 'litre'),
        array('g', 'kg/1000', 'g', 'gram'),
        array('t', '1000*kg', 't', 'tonne'),
        array('s', 's', 's', 'second'),
        array('h', 's*3600', 'h', 'hour'),
        array('Hz', '1/s', 'Hz', 'Hertz'),
        array('Bq', '1/s', 'Bq', 'Becquerel'),
        array('cd', 'cd', 'cd', 'candela'),
        array('N', '(kg*m)/s^2', 'N', 'Newton'),
        array('Pa', 'kg/(m*s^2)', 'Pa', 'Pascals'),
        // Define these before the base units.
        array('cal', '4.2*J', 'cal', 'calorie'),
        array('Cal', '4200*J', 'cal', 'kilo calorie'),
        array('Btu', '1055*J', 'Btu', 'British thermal units'),
        array('eV', '1.602177e-19*J', 'eV', 'Electron volt'),
        array('J', '(kg*m^2)/s^2', 'J', 'Joules'),
        array('W', '(kg*m^2)/s^3', 'W', 'Watts'),
        array('A', 'A', 'A', 'Ampere'),
        array('ohm', '(kg*m^2)/(s^3*A^2)', '\Omega', 'ohm'),
        array('C', 's*A', 'C', 'Coulomb'),
        array('V', '(kg*m^2)/(s^3*A)', 'V', 'Volt'),
        array('F', '(s^4*A^2)/(kg*m^2)', 'F', 'Farad'),
        array('S', '(s^3*A^2)/(kg*m^2)', 'S', 'Siemens'),
        array('Wb', '(kg*m^2)/(s^2*A)', 'Wb', 'Weber'),
        array('T', 'kg/(s^2*A)', 'T', 'Tesla'),
        array('H', '(kg*m^2)/(s^2*A^2)', 'H', 'Henry'),
        array('Gy', 'm^2/s^2', 'Gy', 'gray'),
        array('rem', '0.01*Sv', 'rem', 'Roentgen equivalent'),
        array('Sv', 'm^2/s^2', 'Sv', 'sievert'),
        array('lx', 'cd/m^2', 'lx', 'lux'),
        array('lm', 'cd', 'lm', 'lumen'),
        array('mol', 'mol', 'mol', 'moles'),
        array('M', 'mol/(m^3/1000)', 'M', 'Molar'),
        array('kat', 'mol/s', 'kat', 'katal'),
        array('rad', 'rad', 'rad', 'radian'),
        array('sr', 'sr', 'sr', 'steradian'),
        array('K', 'K', 'K', 'Kelvin'),
        array('VA', '(kg*m^2)/(s^3)', 'VA', 'volt-ampere'),
        array('eV', '1.602176634E-19*J', 'eV', 'electronvolt'),
        array('Ci', 'Ci', 'Ci', 'curie'),
        // @codingStandardsIgnoreStart
        // Celsius conflicts with Coulomb.
        // Add in 'C', 'C', '{}^{o}C', 'Celsius'.
        // @codingStandardsIgnoreEnd
    );

    /*
     * Entries in this array are supported units which are used without any prefix.
     * Entries below are in the form of array(label, base, TeX, fullname).
     * Remember to add any with three or more letters to security-map.json.
     */
    private static $nonprefixunits = array(
        array('min', 's*60', 'min', 'minutes'),
        array('amu', 'amu', 'amu', 'Atomic mass units'),
        array('u', 'amu', 'u', ''),
        array('mmHg', '133.322387415*Pa', 'mmHg', 'Millimeters of mercury'),
        array('bar', '10^5*Pa', 'bar', 'bar'),
        array('ha', '10^4*m^2', 'ha', 'hectare'),
        array('cc', 'm^3*10^(-6)', 'cc', 'cubic centimetre'),
        array('gal', '3.785*l', 'gal', 'US gallon'),
        array('mbar', '10^2*Pa', 'mbar', 'millibar'),
        array('atm', '101325*Pa', 'atm', 'Standard atmosphere'),
        array('torr', '101325/760*Pa', 'torr', 'torr'),
        array('rev', '2*pi*rad', 'rev', 'revolutions'),
        array('deg', 'pi*rad/180', '{}^{o}', 'degrees'),
        array('rpm', 'pi*rad/(30*s)', 'rpm', 'revolutions per minute'),
        array('au', '149597870700*m', 'au', 'astronomical unit'),
        array('Da', '1.660539040E-27*kg', 'Da', 'Dalton'),
        // Logarithmic ratio quantities.
        array('Np', 'Np', 'Np', 'neper'),
        array('B', 'B', 'B', 'bel'),
        array('dB', 'dB', 'dB', 'decibel'),
        // We know these two are not really correct, but there we are.
        array('day', '86400*s', 'day', 'day'),
        array('year', '3.156e7*s', 'year', 'year'),
        // Some countries are only inching towards the metric system.  Added by user request.
        array('hp', '746*W', 'hp', 'horsepower'),
        array('in', 'in', 'in', 'inch'),
        array('ft', '12*in', 'ft', 'foot'),
        array('yd', '36*in', 'yd', 'yard'),
        array('mi', '5280*12*in', 'mi', 'mile'),
        array('lb', '4.4482*N', 'lb', 'pound'),
    );

    /* This array keeps a list of synoymns which students are likely to use.
     * These arrays are used for generating helpful feedback.
     */
    private static $unitsynonyms = array(
        'mol' => array('mols', 'moles', 'mole'),
        'kat' => array('kats', 'katal', 'katals'),
        'rad' => array('radian', 'radians'),
        'torr' => array('tor', 'tors', 'torrs'),
        'amu' => array('amus', 'dalton'),
        'cc' => array('ccm'),
        'Hz' => array('hz'),
        'h' => array('hr', 'hours'),
        'day' => array('days')
    );

    /**
     * Static class. You cannot create instances.
     */
    private function __construct() {
        throw new stack_exception('stack_casstring_units: you cannot create instances of this class.');
    }

    /* This function contributes to the maximalocal.mac file generated in installhelper.class.php. */
    public static function maximalocal_units() {

        $maximalocal = "    /* Define units available in STACK. */\n";

        $code = array();
        $multiplier = array();
        $tex = array();
        foreach (self::$supportedprefix as $unit) {
            $code[] = $unit[0];
            $multiplier[] = $unit[1];
            $tex[] = self::maximalocal_units_tex($unit[2]);
        }
        $maximalocal .= '    stack_unit_si_prefix_code:['. implode(', ', $code). "],\n";
        $maximalocal .= '    stack_unit_si_prefix_multiplier:['. implode(', ', $multiplier). "],\n";
        $maximalocal .= '    stack_unit_si_prefix_tex:['. implode(', ', $tex). "],\n";

        $code = array();
        $conversions = array();
        $tex = array();
        foreach (self::$supportedunits as $unit) {
            $code[] = $unit[0];
            $conversions[] = $unit[1];
            $tex[] = self::maximalocal_units_tex($unit[2]);
        }

        $maximalocal .= '    stack_unit_si_unit_code:['. implode(', ', $code). "],\n";
        $maximalocal .= '    stack_unit_si_unit_conversions:['. implode(', ', $conversions). "],\n";
        $maximalocal .= '    stack_unit_si_unit_tex:['. implode(', ', $tex). "],\n";

        $code = array();
        $conversions = array();
        $tex = array();
        foreach (self::$nonprefixunits as $unit) {
            $code[] = $unit[0];
            $conversions[] = $unit[1];
            $tex[] = self::maximalocal_units_tex($unit[2]);
        }

        $maximalocal .= '    stack_unit_other_unit_code:['. implode(', ', $code). "],\n";
        $maximalocal .= '    stack_unit_other_unit_conversions:['. implode(', ', $conversions). "],\n";
        $maximalocal .= '    stack_unit_other_unit_tex:['. implode(', ', $tex). "],\n";

        return $maximalocal;
    }

    /*
     * Sort out the TeX code for this string.
     */
    private static function maximalocal_units_tex($texstr) {
        if (substr($texstr, 0, 1) === '\\') {
            return('"\\'.$texstr.'"');
        } else {
            return('"\\\\mathrm{'.$texstr.'}"');
        }
    }

    /* This function builds a list of all permitted prefix.unit combinations as defined above.
     * @param int len This is the minimum length of string to be needed to be worth considering.
     */
    public static function get_permitted_units($len) {
        static $cache = array();
        if (array_key_exists($len, $cache)) {
            return $cache[$len];
        }
        $units = array();
        foreach (self::$nonprefixunits as $unit) {
            if (strlen($unit[0]) > $len) {
                $units[$unit[0]] = true;
            }
        }
        foreach (self::$supportedunits as $unit) {
            $units[$unit[0]] = true;
            foreach (self::$supportedprefix as $prefix) {
                $cmd = $prefix[0].$unit[0];
                // By default, the student is allowed to type in any two letter string.
                // We have an option to ignore short stings.
                if (strlen($cmd) > $len) {
                    $units[$cmd] = true;
                }
            }
        }
        $cache[$len] = $units;
        return($units);
    }


    /* This array keeps a list of substitutions which are made when we deal with units.
     */
    private static $unitsubstitutions = array(
        'Torr' => 'torr',
        'kgm/s' => 'kg*m/s'
    );

    /* Make substitutions in an expression.
     * @param MP_Identifier any identifier in the parse tree.
     */
    public static function make_units_substitutions($identifiernode) {
        if ($identifiernode->value == 'Torr') {
            $identifiernode->value = 'torr';
        } else if ($identifiernode->value == 'kgm') {
            // TODO: Do we actually care if there is that '/s' or is that some regexp thing?
            if ($identifiernode->parent instanceof MP_Operation &&
                (($identifiernode->parent->op === '/' &&
                  $identifiernode->parent->lhs === $identifiernode &&
                  $identifiernode->parent->rhs instanceof MP_Identifier &&
                  $identifiernode->parent->rhs->value === 's')) ||
                 ($identifiernode->parent->rhs === $identifiernode &&
                  $identifiernode->parent->operationOnRight() === '/' &&
                  $identifiernode->parent->operandOnRight() instanceof MP_Identifier &&
                  $identifiernode->parent->operandOnRight()->value === 's')) {
                $identifiernode->value = 'kg';
                $identifiernode->parent->replace($identifiernode, new MP_Operation('*', $identifiernode, new MP_Identifier('s')));
            }
        }
    }

    /* Check to see if the student looks like they have used a synonym instead of a correct unit.
     * @param string $key is just a single atomic key.
     */
    public static function find_units_synonyms($key) {
        static $cache = false;
        if ($cache === false) {
            $cache = array();
            foreach (self::$unitsynonyms as $ckey => $synonyms) {
                foreach ($synonyms as $possibleunit) {
                    $cache[strtolower($possibleunit)] = $ckey;
                }
            }
        }

        $fndsynonym = false;
        $answernote = '';
        $synonymerr = '';
        if (array_key_exists(strtolower($key), $cache)) {
            if ($cache[strtolower($key)] != $key) {
                $fndsynonym = true;
                $answernote = 'unitssynonym';
                $synonymerr = stack_string('stackCas_unitssynonym',
                        array('forbid' => stack_maxima_format_casstring($key),
                                'unit' => stack_maxima_format_casstring($cache[strtolower($key)])));
            }
        }

        return array($fndsynonym, $answernote, $synonymerr);
    }

    /* Check to see if the student looks like they have used units with the wrong case.
     * @param string $key is just a single atomic key.
     */
    public static function check_units_case($key) {
        static $valid = false;
        static $invalid = false;
        if ($valid === false) {
            $valid = array();
            $invalid = array();

            foreach (self::$nonprefixunits as $unit) {
                $valid[$unit[0]] = true;
            }
            foreach (self::$supportedunits as $unit) {
                $valid[$unit[0]] = true;
                foreach (self::$supportedprefix as $prefix) {
                    $valid[$prefix[0].$unit[0]] = true;
                }
            }
            foreach ($valid as $k => $noop) {
                $l = strtolower($k);
                if (!array_key_exists($l, $valid)) {
                    if (!array_key_exists($l, $invalid)) {
                        $invalid[$l] = array($k);
                    } else {
                        $invalid[$l][] = $k;
                    }
                }
            }
        }

        // Note, sometimes there is more than one option.  E.g. M and m give many options.
        $foundcmds = array();
        if (array_key_exists($key, $valid)) {
            return false;
        }
        if (!array_key_exists(strtolower($key), $invalid)) {
            return false;
        }

        return(stack_string('stackCas_unknownUnitsCase',
            array('forbid' => stack_maxima_format_casstring($key),
                'unit' => stack_maxima_format_casstring('['.implode(', ', $invalid[strtolower($key)]).']'))));
    }
}
