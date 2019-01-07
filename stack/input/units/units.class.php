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
 * An input to support scientific units.  Heavily based on algebraic.
 *
 * @copyright  2015 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_units_input extends stack_input {

    /**
     * From STACK 4.1 we are not going to continue to add input options as columns in the database.
     * This has numerous problems, and is difficult to maintain. Extra options will be in a JSON-like format.
     * @var array
     */
    protected $extraoptions = array(
        'negpow' => false,
        // Require min/max number of decimal places?
        'mindp' => false,
        'maxdp' => false,
        // Require min/max number of significant figures?
        'minsf' => false,
        'maxsf' => false
    );

    /**
     * Decide if the student's expression should have units.
     * @var bool.
     */
    protected $units = true;

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
            'style' => 'width: '.$size.'em',
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
        );

        if ($this->is_blank_response($state->contents)) {
            $attributes['value'] = stack_utils::logic_nouns_sort($this->parameters['syntaxHint'], 'remove');
        } else {
            $attributes['value'] = $this->contents_to_maxima($state->contents);
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        return html_writer::empty_tag('input', $attributes);
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => true,
            'showValidation' => 1,
            'boxWidth'       => 15,
            'strictSyntax'   => true,
            'insertStars'    => 0,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'allowWords'     => '',
            // The forbidFloats option is ignored by this input type.
            // The Maxima code does not check for floats.
            'forbidFloats'   => false,
            'lowestTerms'    => true,
            // The sameType option is ignored by this input type.
            // The answer is essantially required to be a number and units, other types are rejected.
            'sameType'           => false,
            // Currently this can only be "negpow", or "mul".
            'options'            => '',
        );
    }

    /**
     * Get the value of one of the parameters.
     * @param string $parameter the parameter name
     * @param mixed $default the default to return if this parameter is not set.
     */
    public function get_parameter($parameter, $default = null) {
        // We always want strict syntax for this input type.
        if ($parameter == 'strictSyntax') {
            return true;
        }
        if (array_key_exists($parameter, $this->parameters)) {
            return $this->parameters[$parameter];
        } else {
            return $default;
        }
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

    /* Allows individual input types to change the way the list of variables is tagged.
     * Used by the units input type.
     */
    protected function tag_listofvariables($vars) {
        return html_writer::tag('p', stack_string('studentValidation_listofunits', $vars));
    }

    /* Allow different input types to change the CAS method used.
     * In particular, the units test does something different here.
     */
    protected function get_validation_method() {
        $validationmethod = 'units';
        if ($this->extraoptions['negpow']) {
            $validationmethod = 'unitsnegpow';
        }
        return $validationmethod;
    }

}
