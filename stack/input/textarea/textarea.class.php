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

require_once(dirname(__FILE__) . '/../../utils.class.php');

/**
 *Input that is a text area. Each line input becomes one element of a list.
 *
 * The value is stored as a string maxima list. For example [1,hello,x + y].
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_textarea_input extends stack_input {

    public function render(stack_input_state $state, $fieldname, $readonly) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        $attributes = array(
            'name' => $fieldname,
        );

        if ('' === trim($state->contents)) {
            $current = $this->maxima_to_raw_input($this->parameters['syntaxHint']);
        } else {
            $current = $state->contents;
        }

        // Sort out size of text area.
        $rows = stack_utils::list_to_array($current, false);
        $attributes['rows'] = max($this->parameters['boxHeight'], count($rows) + 1);

        $boxwidth = $this->parameters['boxWidth'];
        foreach ($rows as $row) {
            $boxwidth = max($boxwidth, strlen($row) + 5);
        }
        $attributes['cols'] = $boxwidth;

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        return html_writer::tag('textarea', htmlspecialchars($current), $attributes);
    }

    public function add_to_moodleform(MoodleQuickForm $mform) {
        $mform->addElement('textarea', $this->name, $this->name,
                array('rows' => $this->parameters['boxHeight'], 'cols' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
    }

    /**
     * Converts the inputs passed in into a textarea into a Maxima list
     *
     * @param string $in
     * @return string
     * @access public
     */
    public function raw_input_to_maxima($in) {
        if (!trim($in)) {
             return '';
        }

        $rowsin = explode("\n", $in);
        $rowsout = array();
        foreach ($rowsin as $key => $row) {
            $cleanrow = trim($row);
            if ($cleanrow) {
                $rowsout[] = $cleanrow;
            }
        }

        return '[' . implode(',', $rowsout) . ']';
    }

    /**
    * Converts a Maxima expression (a list) into something which can be placed into the text area.
    *
    * @param string $in
    * @return string
    * @access public
    */
    public function maxima_to_raw_input($in) {
        $values = stack_utils::list_to_array($in, false);
        $out = implode("\n", $values);
        return $out;
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => true,
            'hideFeedback'   => false,
            'boxWidth'       => 20,
            'boxHeight'      => 5,
            'strictSyntax'  => true,
            'insertStars'    => false,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => true);
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid.
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value>0;
                break;

            case 'boxHeight':
                $valid = is_int($value) && $value>0;
                break;
        }
        return $valid;
    }
}
