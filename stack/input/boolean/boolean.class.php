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
        return [
            self::F => stack_string('false'),
            self::T => stack_string('true'),
            self::NA => stack_string('notanswered'),
        ];
    }

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

        $attributes = [];
        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $value = $this->contents_to_maxima($state->contents);
        if ($value === 'EMPTYANSWER') {
            $value = '';
        }

        switch ($this->parameters['displayType']) {
            case 0:
                //Default settings
                $element_complete=html_writer::select(self::get_choices(), $fieldname, $value, '', $attributes);
                break;
            case 1: 
                // 'Click me'-Button
                $attributes['hidden']='hidden';
                $element_select=html_writer::select(self::get_choices(), $fieldname, $value, '', $attributes);

                array_pop($attributes);
                $element_button_id = $fieldname . "-button";
                $attributes['id'] = $element_button_id;
                $attributes['class'] = 'stack-button stack-clickme-button no-answer hovered';
                $attributes['type'] = 'button';
                $attributes['onclick'] = '         
                    var selectElem = document.getElementsByName("' . $fieldname . '")[0];

                    if (document.getElementById("' . $fieldname . '-button").classList.contains("no-answer")) {
                        document.getElementById("' . $fieldname . '-button").classList.remove("no-answer");
                    }
                    if (document.getElementsByName("' . $fieldname . '")[0].value=="true") {
                        document.getElementsByName("' . $fieldname . '")[0].value = "false";
                        document.getElementById("' . $fieldname . '-button").classList.remove("boolean-pressed");
                    } else {
                        document.getElementsByName("' . $fieldname . '")[0].value = "true";
                        document.getElementById("' . $fieldname . '-button").classList.add("boolean-pressed");
                    }';
                $button_script= '
                    if (document.getElementsByName("' . $fieldname . '")[0].value !== "") {
                        document.getElementById("' . $fieldname . '-button").classList.remove("no-answer");
                    }
                    if (document.getElementsByName("' . $fieldname . '")[0].value == "true") {
                        document.getElementById("' . $fieldname . '-button").classList.add("boolean-pressed");
                    }
                    if (document.getElementById("' . $fieldname . '-button").disabled) {
                        document.getElementById("' . $fieldname . '-button").classList.remove("hovered");
                    }
                ';
                $element_script = html_writer::tag('script',$button_script);
                $title = (empty($this->parameters['buttonTitles'])) ? 'Click me' : $this->parameters['buttonTitles'];
                $element_button = html_writer::tag('button', $title, $attributes); 
                
                $element_complete=html_writer::div($element_select . $element_button . $element_script,'stack-parent-toggle-button');
                break;
            case 2:
                //Toggle-Button
                $attributes['hidden']='hidden';
                $element_select=html_writer::select(self::get_choices(), $fieldname, $value, '', $attributes);

                array_pop($attributes);
                $element_button_id = $fieldname . "-button";
                $attributes['id'] = $element_button_id;
                $attributes['class'] = 'stack-input-toggle-button';
                $attributes['type'] = 'checkbox';
                $attributes['onclick'] = '
                    if (document.getElementById("stack-button-' . $fieldname . '").classList.contains("no-answer")) {
                        document.getElementById("stack-button-' . $fieldname . '").classList.remove("no-answer");
                    }
                    if (document.getElementsByName("' . $fieldname . '")[0].value=="true") {
                        document.getElementsByName("' . $fieldname . '")[0].value = "false";
                    } else {
                        document.getElementsByName("' . $fieldname . '")[0].value = "true";
                    };
                ';
                $button_script= '
                    if (document.getElementsByName("' . $fieldname . '")[0].value !== "") {
                        document.getElementById("stack-button-' . $fieldname . '").classList.remove("no-answer");
                    }
                    if (document.getElementsByName("' . $fieldname . '")[0].value == "true") {
                     document.getElementById("' . $fieldname . '-button").checked=true;
                    }
                ';
                $element_script = html_writer::tag('script',$button_script);
                $element_button = html_writer::tag('input', "<span class='slider'></span><span class='slider-labels' data-on='True' data-off='False'></span>", $attributes);
                
                $attributes = [];
                $attributes['id'] = 'stack-button-'.$fieldname;
                $attributes['class'] = 'stack-button stack-toggle-button no-answer';
                $element_label = html_writer::tag('label',$element_button,$attributes);
                $element_complete=html_writer::div($element_select . $element_label . $element_script,'stack-parent-toggle-button');
                break;
            default:
                echo "This type is not set."; break;
        }
        return $element_complete ;
    }
    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'boolean';

        return $data;
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
        return [
            'displayType'     => 0,
            'buttonTitles'    => '',
            'mustVerify'      => false,
            'showValidation'  => 0,
            'options'         => '',
        ];
    }

}
