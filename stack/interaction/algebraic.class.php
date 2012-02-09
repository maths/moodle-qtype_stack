<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
class STACK_Input_Algebra extends STACK_Input_Answer {

    public function getXHTML($readonly) {
        $value = '';
        if ($this->default) {
            $value = ' value="' . htmlspecialchars($this->default) . '"';
        }

        $maxlength = '';
        if ($this->maxLength) {
            $maxlength = ' maxlength="' . $this->maxLength . '"';
        }

        $disabled = '';
        if ($readonly) {
            $disabled = ' readonly="readonly"';
        }

        return '<input type="text" name="' . $this->name . '" size="' . $this->boxWidth . '"' .
                 $value . $maxlength . $disabled . ' />';
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function getOptionDefaults() {
        return array('boxSize'=>'20');
    }
}
