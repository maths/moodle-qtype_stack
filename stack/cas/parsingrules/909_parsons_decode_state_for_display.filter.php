<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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
require_once(__DIR__ . '/filter.interface.php');
require_once(__DIR__ . '/../../utils.class.php');

class stack_ast_filter_909_parsons_decode_state_for_display implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $strings = function($node) use (&$answernotes, &$errors) {
            if ($node instanceof MP_String) {
                $node->value = $this->unhash_array_json($node->value);
            }
            return true;
        };

        $ast->callbackRecurse($strings);
        return $ast;
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
     * Maxima string version of `unhash_array_json`.
     */
    private function unhash_array_json_maxima($list_of_jsons) {
        $php_list_of_jsons = stack_utils::maxima_string_to_php_string($list_of_jsons);
        return stack_utils::php_string_to_maxima_string($this->unhash_array_json($php_list_of_jsons));
    }
}