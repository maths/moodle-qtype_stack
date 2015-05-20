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
 * Input that is a dropdown list of choices that the teacher
 * has specified.
 *
 * TODO add extra validation to really make sure that only allowed values are submitted.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_dropdown_input extends stack_input {

    protected function get_choices() {
        if (empty($this->parameters['ddl_values'])) {
            return array();
        }

        $values = stack_utils::list_to_array('[' . trim($this->parameters['ddl_values']) . ']', false);
        if (empty($values)) {
            return array();
        }

        $choices = array('' => stack_string('notanswered'));
        foreach ($values as $value) {
            $choices[$value] = $value;
        }
        return $choices;
    }

    protected function extra_validation($contents) {
        if (!array_key_exists($contents[0], $this->get_choices())) {
            return stack_string('dropdowngotunrecognisedvalue');
        }
        return '';
    }

    public function render(stack_input_state $state, $fieldname, $readonly) {
        $values = $this->get_choices();
        if (empty($values)) {
            return stack_string('ddl_empty');
        }

        $attributes = array();
        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }

        return html_writer::select($values, $fieldname, $this->contents_to_maxima($state->contents),
                array('' => stack_string('notanswered')), $attributes);
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $values = $this->get_choices();
        if (empty($values)) {
            $mform->addElement('static', $this->name, stack_string('ddl_empty'));
        } else {
            $mform->addElement('select', $this->name, $this->name, $values);
        }
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => false,
            'showValidation' => 0,
            'ddl_values'     => '',
        );
    }
}
