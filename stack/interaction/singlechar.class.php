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
 * Interaction element for inputting a single character.
 *
 * TODO add extra validation to really make sure the user can never enter more than
 * one character, or that setDefault cannot be called with a longer string.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_SingleChar extends STACK_Input_Answer {

    public function getXHTML($readonly) {
        $value = '';
        if ($this->default) {
            $value = ' value="' . htmlspecialchars($this->default) . '"';
        }

        $disabled = '';
        if ($readonly) {
            $disabled = ' readonly="readonly"';
        }

        return '<input type="text" name="' . $this->name . '" size="1" maxlength="1"' .
                $value . $disabled . ' />';
    }

    /**
     * Returns a list of the names of all the opitions that this type of interaction
     * element uses. (Default implementation returns all options.)
     * @return array of option names.
     */
    public static function getOptionsUsed() {
        return array('teacherAns', 'forbid', 'sameType', 'studentVerify', 'hideFeedback');
    }
}
