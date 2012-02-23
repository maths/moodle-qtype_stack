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
 * Defined the stack_input_factory class.
 */

require_once(dirname(__FILE__) . '/inputbase.class.php');


/**
 * Input factory. Provides a convenient way to create an input of any type,
 * and to get metadata about the input types.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_input_factory {
    protected static $types = array(
        'algebraic'  => 'stack_algebra_input',
        'boolean'    => 'stack_boolean_input',
        'dropDown'   => 'stack_dropdown_input',
    //    'list'       => 'stack_list_input',
    //    'matrix'     => 'stack_matrix_input',
        'singleChar' => 'stack_singlechar_input',
    //    'slider'     => 'stack_slider_input',
        'textArea'   => 'stack_textarea_input',
    );

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
    public static function make($type, $name, $teacheranswer, $parameters = null) {

        $class = self::class_for_type($type);
        require_once(dirname(__FILE__) . '/' . strtolower($type) . '.class.php');
        return new $class($name, $teacheranswer, $parameters);
    }

    /**
     * The the class name corresponding to an input type.
     * @param string $type input type name.
     * @return string corresponding class name.
     */
    protected static function class_for_type($type) {
        if (!array_key_exists($type, self::$types)) {
            // TODO throw an appropriate error.
        }
        return self::$types[$type];
    }

    /**
     * @return array of available type names.
     */
    public static function get_available_types() {
        return array_keys(self::$types);
    }

    /**
     * Return array of the options used by each type of input, for
     * use in authoring interface.
     * @return array $typename => array of names of options used.
     */
    public static function get_parameters_used() {
        $used = array();
        foreach (self::$types as $type => $class) {
            $used[$type] = $class::get_parameters_used();
            $used[$type][] = 'inputType';
        }
        return $used;
    }

    /**
     * Return array of the default option values for each type of input,
     * for use in authoring interface.
     * @return array $typename => array of option names => default.
     */
    public static function get_parameters_defaults() {
        $defaults = array();
        foreach (self::$types as $type => $class) {
            $defaults[$type] = $class::get_parameters_defaults();
        }
        return $defaults;
    }
}
