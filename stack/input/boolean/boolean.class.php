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
 * Input for entering true/false using a select dropdown.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_boolean_input extends stack_input {
    const F = 'false';
    const T = 'true';
    const NA = '';

    public static function get_choices() {
        return array(
            self::F => stack_string('false'),
            self::T => stack_string('true'),
            self::NA => stack_string('notanswered'),
        );
    }

    protected $extraoptions = array(
        'hideanswer' => false,
        'allowempty' => false
    );

    protected function extra_validation($contents) {
        $validation = $contents[0];
        if ($validation === 'EMPTYANSWER') {
            $validation = '';
        }
        if (!array_key_exists($validation, $this->get_choices())) {
            return stack_string('booleangotunrecognisedvalue');
        }
        return '';
    }

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $attributes = array();
        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $value = $this->contents_to_maxima($state->contents);
        if ($value === 'EMPTYANSWER') {
            $value = '';
        }
        return html_writer::select(self::get_choices(), $fieldname,
                $value, '', $attributes);
    }


    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
                'mustVerify'      => false,
                'showValidation'  => 0,
                'options'            => ''
        );
    }
}
