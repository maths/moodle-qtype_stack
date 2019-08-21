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

require_once(__DIR__ . '/../options.class.php');
require_once(__DIR__ . '/inputbase.class.php');

// Input factory. Provides a convenient way to create an input of any type,
// and to get metadata about the input types.
//
// @copyright  2012 University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

class stack_input_factory {
    /**
     * @var array type name => array of parameter names used. Used to cache the
     *      results of {@link get_parameters_defaults()}.
     */
    protected static $parametersdefaults = null;

    /**
     * Create an input of a given type and return it.
     * @param string $type the required type. Must be one of the values retured by
     *      {@link getAvailableTypes()}.
     * @param string $name the name of the input. This is the name of the
     *      POST variable that the input from this element will be submitted as.
     * @param int $width size of the input.
     * @param string $default initial contets of the input.
     * @param int $maxLength limit on the maximum input length.
     * @param int $height height of the input.
     * @param array $param some sort of options.
     * @return stack_input the requested input.
     */
    public static function make($type, $name, $teacheranswer, $options = null, $parameters = null, $runtime = true) {
        $class = self::class_for_type($type);
        return new $class($name, $teacheranswer, $options, $parameters, $runtime);
    }

    /**
     * The class name corresponding to an input type.
     * @param string $type input type name.
     * @return string corresponding class name.
     */
    protected static function class_for_type($type) {
        $typelc = strtolower($type);
        $file = __DIR__ . "/{$typelc}/{$typelc}.class.php";
        $class = "stack_{$typelc}_input";

        if (!is_readable($file)) {
            throw new stack_exception('stack_input_factory: unknown input type ' . $type);
        }
        include_once($file);

        if (!class_exists($class)) {
            throw new stack_exception('stack_input_factory: input type ' . $type .
                    ' does not define the expected class ' . $class);
        }
        return $class;
    }

    /**
     * @return array of available type names.
     */
    public static function get_available_types() {
        $ignored = array('CVS', '_vti_cnf', 'tests', 'yui', 'phpunit');
        $types = array();

        $types = array();
        foreach (new DirectoryIterator(__DIR__) as $item) {
            // Skip . and .. and non-dirs.
            if ($item->isDot() or !$item->isDir()) {
                continue;
            }

            // Skip folders from the ignored array above.
            $foldername = $item->getFilename();
            if (in_array($foldername, $ignored)) {
                continue;
            }

            // Skip folders with dubious names.
            $inputname = clean_param($foldername, PARAM_PLUGIN);
            if (empty($inputname) || $inputname != $foldername) {
                continue;
            }

            // Skip folders that don't contain the right file.
            $file = __DIR__ . "/{$inputname}/{$inputname}.class.php";
            if (!is_readable($file)) {
                continue;
            }

            // Skip folders that don't define the right class.
            include_once($file);
            $class = "stack_{$inputname}_input";
            if (!class_exists($class)) {
                continue;
            }

            // Yay! finally we have confirmed we have a valid input plugin!
            $types[$inputname] = $class;
        }

        return $types;
    }

    /**
     * @return array input type internal name => display name.
     */
    public static function get_available_type_choices() {
        $types = self::get_available_types();
        $choices = array();
        foreach ($types as $type => $notused) {
            $choices[$type] = stack_string('inputtype' . $type);
        }
        stack_utils::sort_array($choices);
        return $choices;
    }

    /**
     * Return array of the options used by each type of input, for
     * use in authoring interface.
     * @return array $typename => array of names of options used.
     */
    public static function get_parameters_used() {

        $used = array();
        foreach (self::get_parameters_defaults() as $type => $defaults) {
            $used[$type] = array_keys($defaults);
            $used[$type][] = 'inputType';
        }
        return $used;
    }

    /**
     * Return array of the options used by each type of input, for
     * use in authoring interface, with the fromform mapping.
     * @return array $typename => array of names of options used.
     */
    public static function get_parameters_fromform_mapping($type) {
        $parametermapping = array(
            'sameType'           => 'checkanswertype',
            'mustVerify'         => 'mustverify',
            'showValidation'     => 'showvalidation',
            'boxWidth'           => 'boxsize',
            'strictSyntax'       => 'strictsyntax',
            'syntaxAttribute'    => 'syntaxattribute',
            'insertStars'        => 'insertstars',
            'syntaxHint'         => 'syntaxhint',
            'forbidWords'        => 'forbidwords',
            'allowWords'         => 'allowwords',
            'forbidFloats'       => 'forbidfloat',
            'lowestTerms'        => 'requirelowestterms',
            'options'            => 'options');

        $used = self::get_parameters_defaults();
        $mapping = array();
        foreach ($used[$type] as $param => $defaults) {
                $mapping[$param] = $parametermapping[$param];
        }
        return $mapping;
    }

    /**
     * Return array of the default option values for each type of input,
     * for use in authoring interface.
     * @return array $typename => array of option names => default.
     */
    public static function get_parameters_defaults() {
        if (!is_null(self::$parametersdefaults)) {
            return self::$parametersdefaults;
        }
        self::$parametersdefaults = array();
        foreach (self::get_available_types() as $type => $class) {
            self::$parametersdefaults[$type] = $class::get_parameters_defaults();
        }
        return self::$parametersdefaults;
    }

    /**
     * Convert a raw value as received from a fromform value into a correct datatype.
     */
    public static function convert_parameter_fromform($key, $value) {
        $booleanparamaters = array('strictSyntax' => true, 'mustVerify' => true, 'sameType' => true,
            'forbidFloats' => true, 'lowestTerms' => true);
        if (array_key_exists($key, $booleanparamaters)) {
            $value = (bool) $value;
        }
        return $value;
    }

    /**
     * Convert the old value of "insert stars" (version<
     */
    public static function convert_legacy_insert_stars($value) {
        $map = [
            // Don't insert stars.
            0 => 0,
            // Insert stars for implied multiplication only.
            1 => stack_input::GRAMMAR_FIX_INSERT_STARS,
            // Insert stars assuming single-character variable names.
            2 => stack_input::GRAMMAR_FIX_INSERT_STARS | stack_input::GRAMMAR_FIX_SINGLE_CHAR,
            // Insert stars for spaces only.
            3 => stack_input::GRAMMAR_FIX_SPACES,
            // Insert stars for implied multiplication and for spaces.
            4 => stack_input::GRAMMAR_FIX_INSERT_STARS | stack_input::GRAMMAR_FIX_SPACES,
            // Insert stars assuming single-character variables, implied and for spaces.
            5 => stack_input::GRAMMAR_FIX_INSERT_STARS | stack_input::GRAMMAR_FIX_SINGLE_CHAR | stack_input::GRAMMAR_FIX_SPACES
        ];
        return $map[$value];
    }
}
