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

    public static function get_parameters_defaults() {
        return [
            'mustVerify'         => false,
            'showValidation'     => 0,
            'syntaxHint'         => '',
            'syntaxAttribute'    => 0,
            'options'            => 'hideanswer',
        ];
    }
    
    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $attributes = [
            'type'  => 'text',
            'name'  => $fieldname,
            'id'    => $fieldname,
            'autocapitalize' => 'none',
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

    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'string';
        $data['syntaxHint'] = $this->parameters['syntaxHint'];
        $data['syntaxHintType'] = $this->parameters['syntaxAttribute'] == '1' ? 'placeholder' : 'value';

        return $data;
    }

    protected function response_to_contents($response) {

        $contents = [];
        if (array_key_exists($this->name, $response)) {
            // Don't turn an empty string into an empty string.
            if (trim($response[$this->name]) === '' && !$this->extraoptions['allowempty']) {
                return $contents;
            }
            
            // Unhash keys
            //$unhashed = $this->unhash_json_keys($response[$this->name]);

            // Protect any other quotes etc.
            $converted = stack_utils::php_string_to_maxima_string($response[$this->name]);

            $contents = [$this->ensure_string($converted)];
        }
        return $contents;
    }

    public function maxima_to_response_array($in) {
        if ($in === '') {
            return [$this->name => ''];
        }

        $value = stack_utils::maxima_string_to_php_string($in);
        //$hashed = $this->hash_json_keys($value);
        $response[$this->name] = $value;
        if ($this->requires_validation()) {
            // Do not strip strings from the _val, to enable test inputs to work.
            $response[$this->name . '_val'] = $in;
        }
        return $response;
    }

    public function summarise_response($name, $state, $response) {
        $ans_display = stack_utils::php_string_to_maxima_string($this->unhash_json_keys(stack_utils::maxima_string_to_php_string($state->contents[0])));
        return $name . ': ' . $ans_display . ' [' . $state->status . ']';
    }

    private function unhash_json_keys($ex) {
        $decoded = json_decode($ex);
        $decoded[0][0]->used[0][0] = $this->unhash_json_array($decoded[0][0]->used[0][0]);
        $decoded[0][0]->available = $this->unhash_json_array($decoded[0][0]->available);
        return json_encode($decoded);
    }

    private function unhash_json_array($arr) {
        foreach ($arr as $key => $value) {
            $arr[$key] = base64_decode($value);
        }
        return $arr;
    }

    private function hash_json_keys($ex) {
        $decoded = json_decode($ex);
        $decoded[0][0]->used[0][0] = $this->hash_json_array($decoded[0][0]->used[0][0]);
        $decoded[0][0]->available = $this->hash_json_array($decoded[0][0]->available);
        return json_encode($decoded);
    }

    private function hash_json_array($arr) {
        foreach ($arr as $key => $value) {
            $arr[$key] = base64_encode($value);
        }
        return $arr;
    }

    private function ensure_string($ex) {
        $ex = trim($ex);
        if (substr($ex, 0, 1) !== '"') {
            $ex = '"'.$ex.'"';
        }
        return $ex;
    } 
}