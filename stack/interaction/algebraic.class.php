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
class stack_interaction_algebra extends stack_interaction_element {

    public function get_xhtml($studentanswer, $readonly) {
        $value = '';
        if ($studentanswer) {
            $value = ' value="' . htmlspecialchars($studentanswer) . '"';
        } else {
            $value = ' value="' . htmlspecialchars($this->parameters['syntaxHint']) . '"';
        }

        $disabled = '';
        if ($readonly) {
            $disabled = ' readonly="readonly"';
        }

        $boxwidth = $this->parameters['boxWidth'];
        return '<input type="text" name="' . $this->name . '" size="' . $boxwidth . '"' .
                 $value . $disabled . ' />';
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => true,
            'hideFeedback'   => false,
            'boxWidth'       => 15,
            'strinctSyntax'  => true,
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
    // TODO: I don't understand why this can't be a private function.... CJS
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
