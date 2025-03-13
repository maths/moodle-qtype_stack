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

// Input that is a checkbox/multiple choice.
//
// @copyright  2015 University of Edinburgh.
// @author     Chris Sangwin.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../dropdown/dropdown.class.php');

class stack_checkbox_input extends stack_dropdown_input {

    /*
     * ddltype must be one of 'select', 'checkbox' or 'radio'.
     */
    protected $ddltype = 'checkbox';

    /*
     * Default ddldisplay for checkboxes is 'LaTeX'.
     */
    protected $ddldisplay = 'LaTeX';

    /**
     * Transforms the contents array into a maxima list.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        $vals = [];
        foreach ($contents as $key) {
            // ISS1211 - Moodle App returns value of 0 if box not checked but
            // always safe to ignore 0 thanks to stack_dropdown_input->key_order().
            if ($key !== 0) {
                $vals[] = $this->get_input_ddl_value($key);
            }
        }
        if ($vals == [0 => '']) {
            return '';
        }
        return '['.implode(',', $vals).']';
    }

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        if ($this->errors) {
            return $this->render_error($this->errors);
        }
        // Create html.
        $result = '';
        $values = $this->get_choices();
        $selected = $state->contents;
        $selected = array_flip($state->contents);
        $radiobuttons = [];
        $classes = [];
        foreach ($values as $key => $ansid) {
            $inputattributes = [
                'type' => 'checkbox',
                'name' => $fieldname.'_'.$key,
                'value' => $key,
                'id' => $fieldname.'_'.$key,
            ];

            // Metadata for JS users.
            $inputattributes['data-stack-input-type'] = 'checkbox';

            $labelattributes = [
                'for' => $fieldname.'_'.$key,
            ];
            if (array_key_exists($key, $selected)) {
                $inputattributes['checked'] = 'checked';
            }
            if ($readonly) {
                $inputattributes['disabled'] = 'disabled';
            }
            $radiobuttons[] = html_writer::empty_tag('input', $inputattributes) .
                html_writer::tag('label', $ansid, $labelattributes);
        }

        $result = '';

        $result .= html_writer::start_tag('div', ['class' => 'answer']);
        foreach ($radiobuttons as $key => $radio) {
            $result .= html_writer::tag('div', stack_maths::process_lang_string($radio), ['class' => 'option']);
        }
        $result .= html_writer::end_tag('div');

        return $result;
    }

    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'checkbox';
        $data['options'] = $this->get_choices();

        return $data;
    }

    /**
     * Get the input variable that this input expects to process.
     * All the variable names should start with $this->name.
     * @return array string input name => PARAM_... type constant.
     */
    public function get_expected_data() {
        $expected = [];
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
     * @return array
     */
    public function maxima_to_response_array($in) {
        if ('' === $in || '[]' === $in) {
            return [];
        }

        $tc = stack_utils::list_to_array($in, false);
        $response = [];
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

    protected function ajax_to_response_array($in) {
        if (((string) $in) === '') {
            return [];
        }
        $selected = explode(',', $in);
        $result = [];
        foreach ($selected as $choice) {
            $result[$this->name . '_' . $choice] = $choice;
        }
        return $result;
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
                return [];
        }
        $contents = [];
        foreach ($this->ddlvalues as $key => $val) {
            if (array_key_exists($this->name.'_'.$key, $response)) {
                $contents[] = (int) $response[$this->name.'_'.$key];
            }
        }
        return $contents;
    }

    /**
     * @return string the teacher's answer, suitable for testcase construction.
     */
    public function get_teacher_answer_testcase() {
        return 'mcq_correct(' . $this->teacheranswer . ')';
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

    public function get_api_solution($tavalue) {
        $solution = [];
        foreach ($this->ddlvalues as $key => $value) {
            if ($value['correct']) {
                $solution['_' . $key] = strval($key);
            }
        }
        return $solution;
    }
}
