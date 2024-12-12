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
            // We validate the node to check that it is a string that represents a Parson's state.
            // This is not strictly required as it is prevented by `$node instanceof MP_String`, but it is an additional safety
            // measure to ensure we do not dehash other strings.
            if ($node instanceof MP_String && self::validate_parsons_string($node->value)) {
                $node->value = stack_utils::unhash_parsons_string($node->value);
            }
            return true;
        };

        $ast->callbackRecurse($strings);
        return $ast;
    }

    /**
     * Takes a PHP array and validates it's structure to check whether it represents a single Parson's state.
     * In particular the PHP should be of the following format:
     * array(2) {
     *  [0]=>
     *  array(2) {
     *      ["used"]=>
     *      array(1) {
     *          [0]=>
     *          array(1) {
     *              [0]=>
     *              array(_) {
     *                  [0]=>
     *                  string(_) <str>
     *                  ...
     *                  [n]=>
     *                  string(_) <str>
     *              }
     *          }
     *      }
     *      ["available"]=>
     *          array(_) {
     *              [0]=>
     *              string(_) <str>
     *              ...
     *              [m]=>
     *              string(_) <str>
     *          }
     *      }
     *      [1]=>
     *      int(_)
     *  }
     *
     * @param array $input
     * @return bool whether $input represents a single Parson's state or not
     */
    public static function validate_parsons_state($state) {
        // Check if $state is an array.
        if (!is_array($state)) {
            return false;
        }

        // Check if it's an array with exactly two elements.
        if (count($state) !== 2) {
            return false;
        }

        // Check if the first element is an associative array with keys "used" and "available".
        $dict = $state[0];
        if (!isset($dict['used']) || !isset($dict['available']) || !is_array($dict['used'])) {
            return false;
        }

        // Validate that "used" is an array of at least two dimensions.
        if (!is_array($dict['used'][0]) || !is_array($dict['used'][0][0])) {
            return false;
        }

        // Check if "available" is an array of at least one dimension.
        if (!is_array($dict['available'])) {
            return false;
        }

        // Validate that the second element is an integer.
        if (!is_int($state[1])) {
            return false;
        }

        // If all checks pass, the string is valid.
        return true;
    }

    /**
     * Takes a string and checks whether it is a string containing a list of Parson's states.
     * In particular, it checks whether each item in the list is of the following format:
     * "[{"used": [[[<str>, ..., <str>]]], "available": [<str>, ..., <str>]}, <int>]"
     *
     * @param string $input
     * @return bool whether $input represents a list of Parson's state or not
     */
    public static function validate_parsons_string($input) {
        $data = json_decode($input, true);
        // Check if the JSON decoding was successful and the resulting structure is an array.
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return false;
        }

        // Check whether each item is a valid PHP array corresponding to a single Parson's state.
        foreach ($data as $state) {
            if (!self::validate_parsons_state($state)) {
                // If one of them fails, then the string is invalid.
                return false;
            }
        }

        // If all items pass, then the string is valid.
        return true;
    }
}
