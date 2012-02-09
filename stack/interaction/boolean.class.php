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
 * Interaction element for inputting true/false using a select dropdown.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_Boolean extends STACK_Input_Answer {
    const F = 'false';
    const T = 'true';
    const NA = '';

    public function __construct($name, $width = NULL, $default = NULL, $maxLength = NULL,
            $height = NULL, $param = NULL) {
        if (!in_array($default, array(self::T, self::F))) {
            $default = self::NA;
        }
        parent::__construct($name, $width, $default, $maxLength, $height, $param);
    }

    public function getXHTML($readonly) {
        $choices = array(
            self::F => stack_string('false'),
            self::T => stack_string('true'),
            self::NA => stack_string('notanswered'),
        );

        $disabled = '';
        if ($readonly) {
            $disabled = ' disabled="disabled"';
        }

        $output = '<select name="' . $this->name . '"' . $disabled . '>';
        foreach ($choices as $value => $choice) {
            $selected = '';
            if ($value === $this->default) {
                $selected = ' selected="selected"';
            }

            $output .= '<option value="' . $value . '"' . $selected . '>' . $choice . '</option>';
        }
        $output .= '</select>';

        return $output;
    }

    /**
     * Returns a list of the names of all the opitions that this type of interaction
     * element uses. (Default implementation returns all options.)
     * @return array of option names.
     */
    public static function getOptionsUsed() {
        return array('teacherAns', 'studentVerify', 'hideFeedback');
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function getOptionDefaults() {
        return array(
            'studentVerify' => 'false',
            'hideFeedback'  => 'true'
        );
    }
}
