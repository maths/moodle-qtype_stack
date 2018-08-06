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

require_once(__DIR__ . '/../algebraic/algebraic.class.php');

/**
 * A basic text-field input which is always interpreted as a Maxima string.
 * This has been requested to support the input of things like multi-base numbers.
 *
 * @copyright  2018 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_string_input extends stack_algebraic_input {

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
            $field = 'value';
            if ($this->parameters['syntaxAttribute'] == '1') {
                $field = 'placeholder';
            }
            $attributes[$field] = $this->strip_string(stack_utils::logic_nouns_sort($this->parameters['syntaxHint'], 'remove'));
        } else {
            $value = stack_utils::maxima_string_to_php_string($this->contents_to_maxima($state->contents));
            $attributes['value'] = $in = $this->strip_string($value);
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        $input = html_writer::empty_tag('input', $attributes);
        $result = html_writer::tag('label', get_string('answer', 'qtype_stack',
                html_writer::tag('span', $input, ['class' => 'answer'])),
                ['for' => $attributes['id']]);

        return $result;
    }

    /**
     * Transforms the student's response input into an array.
     * Most return the same as went in.
     *
     * @param array|string $in
     * @return string
     */
    protected function response_to_contents($response) {

        $contents = array();
        if (array_key_exists($this->name, $response)) {
            // Protect any other quotes etc.
            $converted = stack_utils::php_string_to_maxima_string($response[$this->name]);
            // Finally make sure we actually have a Maxima string!
            $contents = array($this->ensure_string($converted));
        }
        return $contents;
    }

    /**
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        $value = stack_utils::maxima_string_to_php_string($value);
        $value = $this->strip_string($value);
        return stack_string('teacheranswershow', array('value' => '<code>'.$value.'</code>', 'display' => $display));
    }

    /**
     * This is used by the question to get the teacher's correct response.
     * The dropdown type needs to intercept this to filter the correct answers.
     * @param unknown_type $in
     */
    public function get_correct_response($in) {
        $value = stack_utils::logic_nouns_sort($in, 'remove');
        $value = $this->strip_string($value);
        return $this->maxima_to_response_array($value);
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     * This is used to take a Maxima expression, e.g. a Teacher's answer or a test case, and directly transform
     * it into expected inputs.
     *
     * @param array|string $in
     * @return string
     */
    public function maxima_to_response_array($in) {
        $response[$this->name] = $this->strip_string($in);
        if ($this->requires_validation()) {
            // Do not strip strings from the _val, to enable test inputs to work.
            $response[$this->name . '_val'] = $in;
        }
        return $response;
    }

    /**
     * Transforms the contents array into a maxima expression.
     * Most simply take the casstring from the first element of the contents array.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        if (array_key_exists(0, $contents)) {
            return $this->ensure_string($contents[0]);
        } else {
            return '';
        }
    }

    private function strip_string($ex) {
        $ex = trim($ex);
        if (substr($ex, 0, 1) === '"') {
            $ex = substr($ex, 1, -1);
        }
        return $ex;
    }

    private function ensure_string($ex) {
        $ex = trim($ex);
        if (substr($ex, 0, 1) !== '"') {
            $ex = '"'.$ex.'"';
        }
        return $ex;
    }
}
