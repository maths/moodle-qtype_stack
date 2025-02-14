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
        } else if (count(json_decode($in)) === 3) {
            // In this case input looks like `[ta, steps, 3]` and only the first two are needed for `group_answer`.
            return ["group_answer", json_encode(array_slice(json_decode($in), 0, 2))];
        } else if (count(json_decode($in)) === 4) {
            // In this case input looks like `[ta, steps, 3, 2]` and only the first three are needed for `match_answer`.
            return ["match_answer", json_encode(array_slice(json_decode($in), 0, 3))];
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
        if (count(explode(",", $ta)) === 2) {
            return ["parsons_answer", $ta];
        } else if (count(explode(",", $ta)) === 3) {
            return ["group_answer", implode(",", array_slice(explode(",", $ta), 0, 2)) . "]"];
        } else if (count(explode(",", $ta)) === 4) {
            return ["match_answer", implode(",", array_slice(explode(",", $ta), 0, 3)) . "]"];
        }
    }

    /**
     * Filters to apply for display in validate_contents
     * @var array
     */
    protected $protectfilters = ['909_parsons_decode_state_for_display', '910_inert_float_for_display',
        '912_inert_string_for_display', ];

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

        // Extract actual correct answer from the steps.
        $ta = 'apply(' . $answerfun . ',' . $args . ')';
        $cs = stack_ast_container::make_from_teacher_source($ta);
        $at1 = new stack_cas_session2([$cs], null, 0);
        $at1->instantiate();
        $value = json_decode($cs->get_value());

        if ('' != $at1->get_errors()) {
            $this->errors[] = $at1->get_errors();
            return;
        }

        /* We replace the dummy `0` timestamp coming from Maxima with the actual
        Unix time (we do this here because Maxima does not have an in-built unix time function). */
        $value = $this->replace_dummy_time($value);
        $value = $this->ensure_string(stack_utils::php_string_to_maxima_string($value));

        return $this->maxima_to_response_array($value);
    }

    /**
     * @return string the teacher's answer, suitable for testcase construction.
     */
    public function get_teacher_answer_testcase() {
        [$answerfun, $args] = self::answer_function_testcase($this->teacheranswer);
        $ta = 'apply(' . $answerfun . ',' . $args . ')';
        return $ta;
    }

    /**
     * Provide a summary of the student's response for the Moodle reporting.
     * We unhash here to provide meaningful information in response history for authors.
     */
    public function summarise_response($name, $state, $response) {
        $display = stack_utils::unhash_parsons_string_maxima($state->contents[0]);
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
        return substr($in, 1, 5) === 'proof';
    }
}
