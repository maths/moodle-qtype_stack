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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../string/string.class.php');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_parsons_input extends stack_string_input {

    /**
     * If new functionality is added to the Parson's block that require new answer functions then they should be added to
     * the following two functions.
     *
     * Each if clause should test the fully evaluated input. For example, in Parson's questions
     * for proof, this full evaluated input looks like `[proof..., [["a", "A"], ["b", "B"]]]`. So we can test it by
     * checking the existence of proof near the beginning. Each if clause should then return an array of two elements of the
     * form `[answer_function, required_args]` according to the relevant Maxima answer function. For example in proof questions,
     * the answer function `proof_answer` takes two arguments, which are already included in the evaluated input `$in`.
     *
     * NOTE: If the input does NOT represent a valid JSON,
     * then it should be added before any lines in which the if-clause `json_decode`. Similarly any clauses involving
     * `json_decode` should go at the end.
     *
     * @param string $in
     * @return array
     */
    private static function answer_function($in) {
        if (self::is_proof_question($in)) {
            return ["parsons_answer", $in];
        }
        $decode = json_decode($in);
        if (!is_array($decode)) {
            return ["error", ""];
        }
        if (count($decode) === 3) {
            // In this case input looks like `[ta, steps, 3]` and only the first two are needed for `group_answer`.
            return ["group_answer", json_encode(array_slice(json_decode($in), 0, 2))];
        } else if (count($decode) === 4) {
            // In this case input looks like `[ta, steps, 3, 2]` and only the first three are needed for `match_answer`.
            return ["match_answer", json_encode(array_slice(json_decode($in), 0, 3))];
        } else {
            return ["error", ""];
        }
    }

    /**
     * Analogous to `answer_function` above but works for unevaluated inputs. So these are never valid JSONs. On the other hand
     * since they are unevaluated we can safely assume any commas represent list delimiters, so we can mostly explode and implode.
     *
     * NOTE: These will be checked in the order they are given in the return array. If the input does NOT represent a valid JSON,
     * then it should be added before any lines in which the key contains `json_decode`. Similarly any keys involving
     * `json_decode` should go at the end of the array.
     *
     * @param string $in
     * @return array
     */
    private static function answer_function_testcase($ta) {
        if (!stack_utils::is_array_string($ta)) {
            return ["error", ""];
        }
        $ex = explode(",", $ta);
        if (count($ex) === 2) {
            return ["parsons_answer", $ta];
        } else if (count($ex) === 3) {
            return ["group_answer", implode(",", array_slice(explode(",", $ta), 0, 2)) . "]"];
        } else if (count($ex) === 4) {
            return ["match_answer", implode(",", array_slice(explode(",", $ta), 0, 3)) . "]"];
        } else {
            return ["error", ""];
        }
    }

    /**
     * Filters to apply for display in validate_contents
     * @var array
     */
    protected $protectfilters = ['909_parsons_decode_state_for_display', '910_inert_float_for_display',
        '912_inert_string_for_display', ];

    /**
     * Make sure we have a valid JSON object we can really decode.
     * @see stack_input::extra_validation()
     */
    protected function extra_validation($contents) {
        $validation = $contents[0];
        if ($validation === 'EMPTYANSWER') {
            $validation = '';
        }
        if (!self::validate_parsons_contents($contents)) {
            return stack_string('parsons_got_unrecognised_value');
        }
        return '';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // This is the same as `string` input render except we hide the input box.

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
            'style'     => 'display:none',
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
        if (trim($in) == 'EMPTYANSWER' || $in === null) {
            $value = '';
        }

        // Get the relevant Maxima function and arguments.
        [$answerfun, $args] = $this::answer_function($in);

        if ($answerfun === 'error') {
            $this->errors[] = stack_string('inputtypeparsons_incorrect_model_ans');
            return [];
        }

        // Extract actual correct answer from the steps.
        $ta = 'apply(' . $answerfun . ',' . $args . ')';
        $cs = stack_ast_container::make_from_teacher_source($ta);
        $at1 = new stack_cas_session2([$cs], null, 0);
        $at1->instantiate();
        $value = json_decode($cs->get_value());
        if ('' != $at1->get_errors()) {
            $this->errors[] = $at1->get_errors();
            return [];
        }

        /* We replace the dummy `0` timestamp coming from Maxima with the actual
        Unix time (we do this here because Maxima does not have an in-built unix time function). */
        $value = $this->replace_dummy_time($value);
        $value = $this->ensure_string(stack_utils::php_string_to_maxima_string($value));

        return $this->maxima_to_response_array($value);
    }

    /**
     * Add description here.
     * @return string the teacher's answer, suitable for testcase construction.
     */
    public function get_teacher_answer_testcase() {
        [$answerfun, $args] = self::answer_function_testcase($this->teacheranswer);
        if ($answerfun === 'error') {
            $this->errors[] = stack_string('inputtypeparsons_incorrect_model_ans');
            return [];
        }
        $ta = 'apply(' . $answerfun . ',' . $args . ')';
        return $ta;
    }

    /**
     * Provide a summary of the student's response for the Moodle reporting.
     * We unhash here to provide meaningful information in response history for authors.
     */
    public function summarise_response($name, $state, $response) {
        $display = $state->contents[0];
        if ($state->status !== 'invalid') {
            $display = stack_utils::unhash_parsons_string_maxima($state->contents[0]);
        }
        return $name . ': ' . $display . ' [' . $state->status . ']';
    }

    /**
     * Do not show the JSON containing teacher answer as feedback.
     * This avoids the need to write 'hideanswer' for Parson's questions.
     */
    public function get_teacher_answer_display($value, $display) {
        if ($this->extraoptions['hideanswer']) {
            return '';
        }

        // For matching problems just hide the answer display.
        if (!$this->is_proof_question($value)) {
            return '';
        }

        $ta = 'apply(proof_display, ' . $value . ')';
        $cs = stack_ast_container::make_from_teacher_source($ta);
        $at1 = new stack_cas_session2([$cs], null, 0);
        $at1->instantiate();

        if ('' != $at1->get_errors()) {
            $this->errors[] = $at1->get_errors();
            return;
        }

        return stack_utils::maxima_string_strip_mbox($cs->get_display());
    }

    /**
     * This is used to replace the dummy `0` timestamp coming from Maxima with Unix time.
     *
     * @param string $in
     * @return string
     */
    private static function replace_dummy_time($in) {
        $json = json_decode($in);
        $json[0][1] = time();
        return json_encode($json);
    }

    /**
     * Proof questions have model answer with two elements [ta, steps], and ta begins with `proof`.
     * In this case we cannot use `json_decode` and count the number of elements, because the list is not valid JSON.
     *
     * @param string $in
     * @return bool
     */
    private static function is_proof_question($in) {
        return substr(trim($in), 1, 5) === 'proof';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function get_api_solution_render($tadisplay, $ta) {
        $render = $this->get_teacher_answer_display($ta, null);

        return $render;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function get_api_solution($value) {
        return null;
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

    public static function validate_parsons_contents($contents) {
        $strings = function($node) use (&$answernotes, &$errors) {
            // We validate the node to check that it is a string that represents a Parson's state.
            // This is not strictly required as it is prevented by `$node instanceof MP_String`, but it is an additional safety
            // measure to ensure we do not dehash other strings.
            if ($node instanceof MP_String && self::validate_parsons_string($node->value)) {
                $node->value = stack_utils::unhash_parsons_string($node->value);
            }
            
            return true;
        };
        return $strings($contents);
    }
}
