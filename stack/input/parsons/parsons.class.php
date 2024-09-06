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

require_once(__DIR__ . '/../string/string.class.php');

class stack_parsons_input extends stack_string_input {

    /**
     * Filters to apply for display in validate_contents
     * @var array
     */
    protected $protectfilters = ['909_parsons_decode_state_for_display', '910_inert_float_for_display', 
        '912_inert_string_for_display'];

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // This is the same as `string` input render except we hide the input box

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $attributes = [
            'type'  => 'text',
            'name'  => $fieldname,
            'id'    => $fieldname,
            'autocapitalize' => 'none',
            'size'  => $this->parameters['boxWidth'] * 1.1,
            'spellcheck'     => 'false',
            'class'     => 'maxima-string',
            'style'     => 'display:none'
        ];

        if ($this->is_blank_response($state->contents)) {
            $field = 'value';
            if ($this->parameters['syntaxAttribute'] == '1') {
                $field = 'placeholder';
            }
            $attributes[$field] = $this->parameters['syntaxHint'];
        } else {
            $value = stack_utils::maxima_string_to_php_string($this->contents_to_maxima($state->contents));
            $attributes['value'] = $value;
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        // Metadata for JS users.
        $attributes['data-stack-input-type'] = 'string';

        return html_writer::empty_tag('input', $attributes);
    }

    /**
     * This is used by the question to get the teacher's correct response.
     *
     * @param array|string $in
     * @return array response to submit for this input.
     */
    public function get_correct_response($in) {
        // This is the same as the string method, except we replace the dummy `0` timestamp coming from Maxima with the actual 
        // Unix time (we do this here because Maxima does not have an in-built unix time function)
        $value_obj = json_decode(stack_utils::maxima_string_to_php_string($in));
        $value_obj[0][1] = time();
        $value = $this->ensure_string(stack_utils::php_string_to_maxima_string(json_encode($value_obj)));

        if (trim($value) == 'EMPTYANSWER' || $value === null) {
            $value = '';
        }

        return $this->maxima_to_response_array($value);
    }

    /*
     * Provide a summary of the student's response for the Moodle reporting.
     * We unhash here to provide meaningful information in response history for authors.
     */
    public function summarise_response($name, $state, $response) {
        $ans_display = $this->unhash_array_json_maxima($state->contents[0]); 
        return $name . ': ' . $ans_display . ' [' . $state->status . ']';
    }
    
    /*
     * Do not show the JSON containing teacher answer as feedback. 
     * This avoids the need to write 'hideanswer' for Parson's questions.
     */
    public function get_teacher_answer_display($value, $display) {
        return '';
    }
    
    /*
     * Unhash strings for display purposes.
     */
    public function render_display_value($val) {
        return $this->unhash_array_json_maxima($val);
    }

    /*
     * Takes a string that contains a list where each element has the format
     * [<JSON>, <int>]
     * and each JSON has the format
     * {"used" : [[[<hashed string>, ..., <hashed string>]]], "available" : [<hashed_string>, ... <hashed_string>]}
     * each `<hashed_string>` is assumed to be Base64-hashed.
     * 
     * This function will return the same format string, with each `<hashed_string>` replaced by the original string value.
     */
    private function unhash_array_json($list_of_jsons) {
        $decoded_list = json_decode($list_of_jsons);
        foreach($decoded_list as $key => $json) {
            $decoded_list[$key][0]->used[0][0] = $this->unhash_array($decoded_list[$key][0]->used[0][0]);
            $decoded_list[$key][0]->available = $this->unhash_array($decoded_list[$key][0]->available);
        }
        return json_encode($decoded_list);
    }

    /*
     * Takes a list of Base64-hashed strings and returns the corresponding list of original string values.
     */
    private function unhash_array($arr) {
        foreach ($arr as $key => $value) {
            $arr[$key] = base64_decode($value);
        }
        return $arr;
    }

    /*
     * Maxima string version of `unhash_array_json`.
     */
    private function unhash_array_json_maxima($list_of_jsons) {
        $php_list_of_jsons = stack_utils::maxima_string_to_php_string($list_of_jsons);
        return stack_utils::php_string_to_maxima_string($this->unhash_array_json($php_list_of_jsons));
    }

    /*
     * Takes a string that contains a list where each element has the format
     * [<JSON>, <int>]
     * and each JSON has the format
     * {"used" : [[[<string>, ..., <string>]]], "available" : [<string>, ... <string>]}
     * 
     * This function will return the same format string, with each `<string>` replaced by its Base64-hashed value.
     */
    private function hash_array_json($list_of_jsons) {
        $decoded_list = json_decode($list_of_jsons);
        foreach($decoded_list as $key => $json) {
            $decoded_list[$key][0]->used[0][0] = $this->hash_array($decoded_list[$key][0]->used[0][0]);
            $decoded_list[$key][0]->available = $this->hash_array($decoded_list[$key][0]->available);
        }
        return json_encode($decoded_list);
    }
    
    /*
     * Takes a list of strings and returns the corresponding list of Base64-hashed string values.
     */
    private function hash_array($arr) {
        foreach ($arr as $key => $value) {
            $arr[$key] = base64_encode($value);
        }
        return $arr;
    }

    /*
     * Maxima string version of `hash_array_json`.
     */
    private function hash_array_json_maxima($list_of_jsons) {
        $php_list_of_jsons = stack_utils::maxima_string_to_php_string($list_of_jsons);
        return stack_utils::php_string_to_maxima_string($this->hash_array_json($php_list_of_jsons));
    }


    private function ensure_string($ex) {
        $ex = trim($ex);
        if (substr($ex, 0, 1) !== '"') {
            $ex = '"'.$ex.'"';
        }
        return $ex;
    } 
}