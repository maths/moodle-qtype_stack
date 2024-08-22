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
    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'simp' => false,
        'negpow' => false,
        // Require min/max number of decimal places?
        'mindp' => false,
        'maxdp' => false,
        // Require min/max number of significant figures?
        'minsf' => false,
        'maxsf' => false,
        'align' => 'left',
        'consolidatesubscripts' => false,
        'validator' => false,
        'feedback' => false,
        'monospace' => false,
    ];


    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $size = $this->parameters['boxWidth'] * 0.9 + 0.1;
        $attributes = [
            'type'  => 'text',
            'name'  => $fieldname,
            'id'    => $fieldname,
            'size'  => $this->parameters['boxWidth'] * 1.1,
            'style' => 'width: '.$size.'em',
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
            'class'     => 'algebraic-units',
        ];
        if ($this->extraoptions['align'] === 'right') {
            $attributes['class'] = 'algebraic-units-right';
        }
        if ($this->extraoptions['monospace']) {
            $attributes['class'] .= ' input-monospace';
        }

        if ($state->contents == 'EMPTYANSWER') {
            // Active empty choices don't result in a syntax hint again (with that option set).
            $attributes['value'] = '';
        } else if ($this->is_blank_response($state->contents)) {
            $field = 'value';
            if ($this->parameters['syntaxAttribute'] == '1') {
                $field = 'placeholder';
            }
            $attributes[$field] = $this->parameters['syntaxHint'];
        } else {
            $attributes['value'] = $this->contents_to_maxima($state->contents);
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        // Metadata for JS users.
        $attributes['data-stack-input-type'] = 'units';
        if ($this->options->get_option('decimals') === ',') {
            $attributes['data-stack-input-decimal-separator']  = ',';
            $attributes['data-stack-input-list-separator'] = ';';
        } else {
            $attributes['data-stack-input-decimal-separator']  = '.';
            $attributes['data-stack-input-list-separator'] = ',';
        }

        return html_writer::empty_tag('input', $attributes);
    }

    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'units';
        $data['boxWidth'] = $this->parameters['boxWidth'];
        $data['align'] = $this->extraoptions['align'] === 'right' ? 'right' : 'left';
        $data['syntaxHint'] = $this->parameters['syntaxHint'];
        $data['syntaxHintType'] = $this->parameters['syntaxAttribute'] == '1' ? 'placeholder' : 'value';

        return $data;
    }



    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, ['size' => $this->parameters['boxWidth']]);
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return [
            'mustVerify'      => true,
            'showValidation'  => 1,
            'boxWidth'        => 15,
            'insertStars'     => 0,
            'syntaxHint'      => '',
            'syntaxAttribute' => 0,
            'forbidWords'     => '',
            'allowWords'      => '',
            // The forbidFloats option is ignored by this input type.
            // The Maxima code does not check for floats.
            'forbidFloats'    => false,
            'lowestTerms'     => true,
            // The sameType option is ignored by this input type.
            // The answer is essantially required to be a number and units, other types are rejected.
            'sameType'        => false,
            // Currently this can only be "negpow", or "mul".
            'options'            => '',
        ];
    }

    /**
     * Get the value of one of the parameters.
     * @param string $parameter the parameter name
     * @param mixed $default the default to return if this parameter is not set.
     */
    public function get_parameter($parameter, $default = null) {
        // We always allow floats in units. Repeat pre 4.3 behaviour.
        if ($parameter == 'forbidFloats') {
            return false;
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
        if ($this->get_extra_option('hideanswer')) {
            return '';
        }
        if (trim($value) == 'EMPTYANSWER') {
            return stack_string('teacheranswerempty');
        }
        $cs = stack_ast_container::make_from_teacher_source($value, '', new stack_cas_security());
        $cs->set_nounify(0);
        $value = $cs->get_inputform(true, 0, true, $this->options->get_option('decimals'));
        return stack_string('teacheranswershow', ['value' => '<code>'.$value.'</code>', 'display' => $display]);
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
