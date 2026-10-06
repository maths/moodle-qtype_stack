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
require_once(__DIR__ . '/../json/json.class.php');

/**
 * A compound input class for questions where a student can repeat inputs.
 *
 * @package    qtype_stack
 * @copyright  2018 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_repeat_input extends stack_json_input {
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'validator' => false,
    ];

    /**
     * Announces if the input is "simple" or compound.
     */
    public function get_simplicity() {
        return stack_input::SIMPLICITY_COMPOUND;
    }

    /**
     * When evaluated, this input defines one Maxima list `repeated<name>` per
     * simple input used inside the corresponding `[[repeat]]` block. PRTs refer
     * to those lists rather than to the raw input names.
     *
     * @return string[]
     */
    public function get_generated_variable_names() {
        $names = [];
        foreach (array_keys($this->simpleinputs) as $inputname) {
            $names[] = 'repeated' . $inputname;
        }
        return $names;
    }

    /*
     * This input type is always "typeless" because the teacher's answer is a JSON string,
     * but the eventual type will be a Maxima expression.
     */
    protected function get_validation_method() {
        return 'typeless';
    }

    /**
     * Pull the list of raw values for each simple input out of the JSON state.
     *
     * The state is either the full JSON written by the repeat block, where "data" is a list of
     * {repeat_id, inputs} objects, or the cut-down form from the teacher's answer where "data"
     * maps input names to lists of values directly.
     *
     * The JSON comes from the student's browser, so anything not matching this structure exactly
     * (e.g. a value which is not a list, or a list containing anything but strings and numbers)
     * is rejected rather than guessed at.
     *
     * @param array $contents the contents of this input.
     * @return array|null input name => list of raw values, or null if the JSON is unusable.
     */
    protected function extract_inputs($contents) {
        if (!array_key_exists(0, $contents)) {
            return null;
        }
        $payload = json_decode(stack_utils::maxima_string_to_php_string($contents[0]));
        // Only pay attention to the "data" in the payload.  Everything else is the
        // responsibility of the input JS.
        if (!is_object($payload) || !property_exists($payload, 'data')) {
            return null;
        }
        if (is_array($payload->data)) {
            $groups = [];
            foreach ($payload->data as $group) {
                if (!is_object($group) || !property_exists($group, 'inputs')) {
                    return null;
                }
                $groups[] = $group->inputs;
            }
        } else {
            $groups = [$payload->data];
        }

        $inputs = [];
        foreach ($groups as $group) {
            if (!is_object($group)) {
                return null;
            }
            foreach ($this->simpleinputs as $inputname => $input) {
                if (!property_exists($group, $inputname)) {
                    continue;
                }
                $values = $group->{$inputname};
                // A JSON list decodes to a PHP list, a JSON object would decode to stdClass.
                if (!is_array($values)) {
                    return null;
                }
                foreach ($values as $value) {
                    if (!is_string($value) && !is_int($value) && !is_float($value)) {
                        return null;
                    }
                }
                $inputs[$inputname] = $values;
            }
        }
        return $inputs;
    }

    /**
     * Remove the rows in which every input is blank, e.g. because the student added more rows than needed.
     *
     * Rows are removed from all inputs together, so that values in the same row stay aligned.
     *
     * @param array $inputs input name => list of raw values.
     * @return array the same, without the empty rows.
     */
    protected function remove_empty_rows(array $inputs): array {
        $lists = array_filter($inputs, 'is_array');
        $numrows = $lists ? max(array_map('count', $lists)) : 0;
        $keep = [];
        for ($row = 0; $row < $numrows; $row++) {
            foreach ($lists as $values) {
                if (array_key_exists($row, $values) && !$this->is_blank_value($values[$row])) {
                    $keep[] = $row;
                    break;
                }
            }
        }
        // Lists may have different lengths (e.g. a teacher's answer), so only keep rows a list actually has.
        foreach ($lists as $inputname => $values) {
            $inputs[$inputname] = [];
            foreach ($keep as $row) {
                if (array_key_exists($row, $values)) {
                    $inputs[$inputname][] = $values[$row];
                }
            }
        }
        return $inputs;
    }

    /**
     * Is a single value from the JSON state blank?
     * @param mixed $value
     * @return bool
     */
    private function is_blank_value($value): bool {
        return is_scalar($value) && (trim((string) $value) === '' || $value === 'EMPTYANSWER');
    }

    /**
     * The response is blank if the JSON state contains no row with a non-empty value.
     * @param array $contents
     * @return bool
     */
    protected function is_blank_response($contents) {
        if (parent::is_blank_response($contents)) {
            return true;
        }
        $inputs = $this->extract_inputs($contents);
        if ($inputs === null) {
            // Broken JSON is not blank, the student needs to see the validation error.
            return false;
        }
        foreach ($this->remove_empty_rows($inputs) as $values) {
            if ($values !== []) {
                return false;
            }
        }
        return true;
    }

    protected function validate_contents($contents, $basesecurity, $localoptions) {

        $errors = [];
        $valid = true;
        $caslines = [];
        $notes = [];
        $ilines = [];

        if (strlen($contents[0]) > $this->maxinputlength) {
            $valid = false;
            $errors[] = stack_string('studentinputtoolong');
            $notes['too_long'] = true;
            $inputs = [];
        } else {
            $inputs = $this->extract_inputs($contents);
            if ($inputs === null) {
                $valid = false;
                $errors[] = stack_string('invalid_json');
                $notes['invalid_json'] = true;
                $inputs = [];
            }
        }
        // Rows the student added but left completely empty are ignored.
        $inputs = $this->remove_empty_rows($inputs);

        $states = [];
        $incompleterows = [];
        // Validate each entry separately using the simple input validation, with the
        // question's security settings (e.g. forbidden words, units) and options.
        foreach ($inputs as $inputname => $val) {
            $input = $this->simpleinputs[$inputname];
            // Val should now be an array of values.
            $exprs = [];
            foreach ($val as $row => $sans) {
                $state = $input->validate_student_response([$inputname => (string) $sans],
                    $localoptions, 'null', $basesecurity);
                if ($state->__get('status') === stack_input::VALID || $state->__get('status') === stack_input::SCORE) {
                    $exprs[] = $state->__get('contentsmodified');
                } else {
                    $valid = false;
                    if ($state->__get('status') === stack_input::BLANK) {
                        // Only some fields of this (non-empty) row have been filled in.
                        $incompleterows[$row + 1] = true;
                    }
                }
                $errors[] = $state->__get('errors');
                if ($state->__get('note') !== '') {
                    $notes[$state->__get('note')] = true;
                }
            }
            // If valid, collect together the valid modified expresssions.
            // This is one Maxima list per input.
            $states[$inputname] = 'repeated' . $inputname . ':[' . implode(',', $exprs) . ']';
        }
        ksort($incompleterows);
        foreach (array_keys($incompleterows) as $row) {
            $errors[] = stack_string('repeatincompleterow', $row);
        }
        if ($incompleterows) {
            $notes['repeat_incomplete_row'] = true;
        }

        // Concatinate expressions into a Maxima block which defines the variables separatel.
        $val = '[]';
        if ($valid) {
            $val = '(' . implode(',', $states) . ')';
        }
        // Teacher source is acceptable here because the $val has already been through regular validation above.
        $answer = stack_ast_container::make_from_teacher_source($val, '', new stack_cas_security());
        $caslines[] = $answer;
        $valid = $valid && $answer->get_valid();
        $errors[] = $answer->get_errors();
        $note = $answer->get_answernote(true);
        if ($note) {
            foreach ($note as $n) {
                $notes[$n] = true;
            }
        }

        list ($secrules, $filterstoapply) = $this->validate_contents_filters($basesecurity);
        // Separate rules for inert display logic, which wraps floats with certain functions.
        $secrulesd = clone $secrules;
        $secrulesd->add_allowedwords('dispdp,displaysci');
        // Construct inert version of the whole answer.
        $protectfilters = $this->protectfilters;
        if ($this->get_extra_option('simp')) {
            // A choice: we either don't include '910_inert_float_for_display' or we have a maxima
            // function to perform calculations on dispdp numbers.
            $val = 'stack_validate_simpnum(' . $val .')';
            // Add in an extra Maxima function here so we can eventaually decide how many dps to display.
        }
        $inertdisplayform = stack_ast_container::make_from_student_source($val, '', $secrulesd,
            array_merge($filterstoapply, $protectfilters),
            [], 'Root', $this->options->get_option('decimals'));
        $inertdisplayform->get_valid();
        $ilines[] = $inertdisplayform;

        return [$valid, $errors, $notes, $answer, $caslines, $inertdisplayform, $ilines];
    }

    /**
     * We have switched from receiving a JSON input to constructing a Maxima expression.
     * Take the validation_display from the baseclass, not from the JSON input.
     *
     * @param stack_casstring $answer, the complete answer.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function validation_display(
        $answer,
        $lvars,
        $caslines,
        $additionalvars,
        $valid,
        $errors,
        $castextprocessor,
        $inertdisplayform,
        $ilines,
        $notes
    ) {

            // Display the whole JSON object.
            $contents = $this->rawcontents;
            $display = stack_utils::maxima_string_to_php_string($contents[0]);
            // Turn into a PHP stdClass object.
            $json = json_decode($display);
            // If we have mal-formed JSON (exactly the situation we need to debug) then we display the original.
            if ($json !== null) {
                $display = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }
            $pdisplay = html_writer::tag('pre', $display);

            // And we want to show the actual answer as a Maxima object.
            list($valid, $errors, $display, $notes) = $this->validation_display_baseclass(
                $answer,
                $lvars,
                $caslines,
                $additionalvars,
                $valid,
                $errors,
                $castextprocessor,
                $inertdisplayform,
                $ilines,
                $notes
            );

            return [$valid, $errors, $pdisplay . $display, $notes];
    }
}
