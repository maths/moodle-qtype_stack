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
 * A basic text-field input.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_matrix_input extends stack_input {

    public function render(stack_input_state $state, $fieldname, $readonly) {
        $attributes = array(
            'type' => 'text',
            'name' => $fieldname,
        );

        // Work out how big the matrix should be from the teacher's answer.
        $cs =  new stack_cas_casstring('ta:matrix_size('.$this->teacheranswer.')');
        $cs->validate('t');
        $at1 = new stack_cas_session(array($cs), null, 0);
        $at1->instantiate();
        $ret = '';
        if ('' != $at1->get_errors()) {
            $ret .= html_writer::tag('div', $at1->get_errors(), array('id' => 'error', 'class' => 'error'));
        }
        $size = $at1->get_value_key('ta');
        $dimensions = explode(',', $size);

        if ('' === trim($state->contents)) {
            $attributes['value'] = $this->parameters['syntaxHint'];
        } else {
            $attributes['value'] = $state->contents;
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        $ret .= html_writer::empty_tag('input', $attributes);
        return $ret;
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => true,
            'hideFeedback'   => false,
            'strictSyntax'   => false,
            'insertStars'    => false,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => true);
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value>0;
                break;
        }
        return $valid;
    }
}
