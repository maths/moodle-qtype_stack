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
 * Interaction element to display a dropdown list of choices that the teacher
 * has specified.
 *
 * TODO add extra validation to really make sure that only allowed values are submitted.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_interaction_dropdown extends stack_interaction_element {

    public function get_xhtml($studentanswer, $readonly) {
        if (empty($this->parameters['ddl_values'])) {
            return stack_string('ddl_empty');
        }

        $su       = new STACK_StringUtil('[' . trim($this->parameters['ddl_values']) . ']');
        $values = $su->listToArray(false);

        if (empty($values)) {
            return stack_string('ddl_empty');
        }

        if (!in_array($studentanswer, $values)) {
            $studentanswer = '';
        }

        $values = array_merge(
                array('' => stack_string('notanswered')),
                array_combine($values, $values));

        $disabled = '';
        if ($readonly) {
            $disabled = ' disabled="disabled"';
        }

        $output = '<select name="' . $this->name . '"' . $disabled . '>';
        foreach ($values as $value => $choice) {
            $selected = '';
            if ($value === $studentanswer) {
                $selected = ' selected="selected"';
            }

            $output .= '<option value="' . htmlspecialchars($value) . '"' . $selected . '>' .
                    htmlspecialchars($choice) . '</option>';
        }
        $output .= '</select>';

        return $output;
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => false,
            'hideFeedback'   => true
            );
    }
}
