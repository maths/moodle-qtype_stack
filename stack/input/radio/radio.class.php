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
 * Input that is a dropdown list/multiple choice that the teacher
 * has specified.
 *
 * @copyright  2015 University of Edinburgh
 * @author     Chris Sangwin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../dropdown/dropdown.class.php');
class stack_radio_input extends stack_dropdown_input {

    protected $ddltype = 'radio';

    public function render(stack_input_state $state, $fieldname, $readonly) {

        $result = '';
        // Display runtime errors and bail out.
        if ('' != $this->ddlerrors) {
            $result .= html_writer::tag('p', stack_string('ddl_runtime'));
            $result .= html_writer::tag('p', $this->ddlerrors);
            return html_writer::tag('div', $result, array('class' => 'error'));
        }

        // Create html.
        $values = $this->get_choices();
        $selected = $state->contents;

        $selected = array_flip($state->contents);
        $radiobuttons = array();
        $classes = array();

        foreach ($values as $key => $ansid) {
            $inputattributes = array(
                'type' => 'radio',
                'name' => $fieldname,
                'value' => $key,
                'id' => $fieldname.'_'.$key
            );
            if (array_key_exists($key, $selected)) {
                $inputattributes['checked'] = 'checked';
            }
            if ($readonly) {
                $inputattributes['disabled'] = 'disabled';
            }
            $radiobuttons[] = html_writer::empty_tag('input', $inputattributes) . html_writer::tag('label', $ansid);
        }

        $result = '';

        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        foreach ($radiobuttons as $key => $radio) {
            $result .= html_writer::tag('div', $radio);
        }
        $result .= html_writer::end_tag('div');

        return $result;
    }
}
