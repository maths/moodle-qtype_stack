<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
 * Defined the STACK_Input_Controller class.
 */

require_once(dirname(__FILE__) . '/answer.class.php');


/**
 * Interaction element factory. Provides a convenient way to create an
 * interaction element of any type, and to get metadata about element types.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_Controller {
    protected static $types = array(
        'algebraic'  => 'STACK_Input_Algebra',
        'boolean'    => 'STACK_Input_Boolean',
        'dropDown'   => 'STACK_Input_DropDownList',
        'list'       => 'STACK_Input_List',
//        'matrix'     => 'STACK_Input_Matrix',
        'singleChar' => 'STACK_Input_SingleChar',
//        'slider'     => 'STACK_Input_Slider',
        'textArea'   => 'STACK_Input_TextArea',
    );

    /**
     * Create an element of a given type and return it.
     * @param string $type the required type. Must be one of the values retured by
     *      {@link getAvailableTypes()}.
     * @param string $name the name of the interaction element. This is the name of the
     *      POST variable that the input from this element will be submitted as.
     * @param int $width size of the input.
     * @param string $default initial contets of the input.
     * @param int $maxLength limit on the maximum input length.
     * @param int $height height of the input.
     * @param array $param some sort of options.
     * @return STACK_Input_Answer the requested interaction element.
     */
    public static function make_element($type, $name, $width = null,
            $default = null, $maxLength = null, $height = null, $param = null) {

        $class = self::class_for_type($type);
        require_once(dirname(__FILE__) . '/' . strtolower($type) . '.class.php');
        return new $class($name, $width, $default, $maxLength, $height, $param);
    }

    /**
     * The the class name corresponding to an interaction element type.
     * @param string $type interaction element type name.
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
    public static function getAvailableTypes() {
        return array_keys(self::$types);
    }

    /**
     * Return array of the options used by each type of interaction element, for
     * use in authoring interface.
     * @return array $typename => array of names of options used.
     */
    public static function getOptionsUsed() {
        $used = array();
        foreach (self::$types as $type => $class) {
            $used[$type] = $class::getOptionDefaults();
            $used[$type][] = 'inputType';
        }
        return $used;
    }

    /**
     * Return array of the default option values for each type of interaction element,
     * for use in authoring interface.
     * @return array $typename => array of option names => default.
     */
    public static function getOptionDefaults() {
        $defaults = array();
        foreach (self::$types as $type => $class) {
            $defaults[$type] = $class::getOptionDefaults();
        }
        return $defaults;
    }
}
