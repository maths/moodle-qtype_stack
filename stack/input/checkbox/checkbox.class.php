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
class stack_checkbox_input extends stack_dropdown_input {

    /*
     * ddltype must be one of 'select', 'checkbox' or 'radio'.
     */
    protected $ddltype = 'checkbox';



    /**
     * Transforms the contents array into a maxima list.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        $vals = array();
        foreach ($contents as $key) {
            $vals[] = $this->get_input_ddl_value($key);
        }
        if ($vals == array( 0 => '')) {
            return '';
        }
        return '['.implode(',', $vals).']';
    }

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
                'type' => 'checkbox',
                'name' => $fieldname.'_'.$key,
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

    /**
     * Get the input variable that this input expects to process.
     * All the variable names should start with $this->name.
     * @return array string input name => PARAM_... type constant.
     */
    public function get_expected_data() {
        $expected = array();
        $expected[$this->name] = PARAM_RAW;
        foreach ($this->ddlvalues as $key => $val) {
            $expected[$this->name.'_'.$key] = PARAM_RAW;
        }

        if ($this->requires_validation()) {
            $expected[$this->name.'_val'] = PARAM_RAW;
        }
        return $expected;
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     * @param array|string $in
     * @return string
     */
    public function maxima_to_response_array($in) {
        if ('' == $in || '[]' == $in) {
            return array($this->name = '');
        }

        $tc = stack_utils::list_to_array($in, false);
        $response = array();
        foreach ($tc as $key => $val) {
            $ddlkey = $this->get_input_ddl_key($val);
            $response[$this->name.'_'.$ddlkey] = $ddlkey;
        }
        // The name field is used by the question testing mechanism for the full answer.
        $response[$this->name] = $in;

        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;
    }

    /**
     * Converts the input passed in via many input elements into an array.
     *
     * @param string $in
     * @return string
     * @access public
     */
    public function response_to_contents($response) {
        // Did the student chose the "Not answered" response?
        if (array_key_exists($this->name.'_', $response)) {
                return array();
        }
        $contents = array();
        foreach ($this->ddlvalues as $key => $val) {
            if (array_key_exists($this->name.'_'.$key, $response)) {
                $contents[] = (int) $response[$this->name.'_'.$key];
            }
        }
        return $contents;
    }

    /**
     * Decide if the contents of this attempt is blank.
     *
     * @param array $contents a non-empty array of the student's input as a split array of raw strings.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function is_blank_response($contents) {
        $allblank = true;
        foreach ($contents as $val) {
            if (!('' == trim($val)) && !('0' == trim($val))) {
                $allblank = false;
            }
        }
        return $allblank;
    }

}
