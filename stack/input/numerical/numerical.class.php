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
 * A basic text-field input.
 *
 * @copyright  2017 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_numerical_input extends stack_input {

    /**
     * @var bool
     * Is a student required to type in a float?
     */
    private $optfloatnum = false;

    protected function internal_contruct() {
        $options = $this->get_parameter('options');

        if (trim($options) != '') {
            $options = explode(',', $options);
            foreach ($options as $option) {
                $option = strtolower(trim($option));

                switch($option) {

                    case 'floatnum':
                        $this->optfloatnum = true;
                        break;

                    default:
                        $this->errors[] = stack_string('inputoptionunknown', $option);
                }
            }
        }
        return true;
    }

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $size = $this->parameters['boxWidth'] * 0.9 + 0.1;
        $attributes = array(
            'type'  => 'text',
            'name'  => $fieldname,
            'id'    => $fieldname,
            'size'  => $this->parameters['boxWidth'] * 1.1,
            'style' => 'width: '.$size.'em'
        );

        if ($this->is_blank_response($state->contents)) {
            $field = 'value';
            if ($this->parameters['syntaxAttribute'] == '1') {
                $field = 'placeholder';
            }
            $attributes[$field] = stack_utils::logic_nouns_sort($this->parameters['syntaxHint'], 'remove');
        } else {
            $attributes['value'] = $this->contents_to_maxima($state->contents);
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        return html_writer::empty_tag('input', $attributes);
    }

    /** This function creates additional session variables.
     */
    protected function additional_session_variables($caslines, $teacheranswer) {
        $floatnum = new stack_cas_casstring('floatnump('.$this->name.')');
        $floatnum->get_valid('t');

        return array('floatnum' => $floatnum);
    }

    /**
     * This function constructs the display of variables during validation.
     * For many input types this is simply the complete answer.
     * For text areas and equivalence reasoning this is a more complex arrangement of lines.
     *
     * @param stack_casstring $answer, the complete answer.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors) {

        $display = stack_maxima_format_casstring($answer->get_raw_casstring());
        if ('' != $answer->get_errors()) {
            $valid = false;
            $errors = array(stack_maxima_translate($answer->get_errors()));
        }
        if (trim($answer->get_display()) == '') {
            $valid = false;
        } else {
            $display = '\[ ' . $answer->get_display() . ' \]';
        }

        if ($lvars->get_value() != '[]') {
            $valid = false;
            $errors[] = stack_string('numericalinputvarsforbidden');
            $this->set_parameter('showValidation', 1);
        }

        $fn = $additionalvars['floatnum'];
        if ($this->optfloatnum && $fn->get_value() == 'false') {
            $valid = false;
            $errors[] = stack_string('numericalinputmustfloat');
        }
        return array($valid, $errors, $display);
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * Parameters are options a teacher might set.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'         => true,
            'showValidation'     => 1,
            'boxWidth'           => 15,
            // The option strictSyntax as true means we don't insert *s into 192.3e3 etc.
            'strictSyntax'       => true,
            'insertStars'        => 0,
            'syntaxHint'         => '',
            'syntaxAttribute'    => 0,
            'forbidWords'        => '',
            'allowWords'         => '',
            'forbidFloats'       => false,
            'lowestTerms'        => true,
            'sameType'           => true,
            'options'            => '');
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value > 0;
                break;
        }
        return $valid;
    }

    /**
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        return stack_string('teacheranswershow', array('value' => '<code>'.$value.'</code>', 'display' => $display));
    }

    protected function get_validation_method() {
        return 'numerical';
    }
}
