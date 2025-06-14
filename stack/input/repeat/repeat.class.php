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

    /*
     * This input type is always "typeless" because the teacher's answer is a JSON string,
     * but the eventual type will be a Maxima expression.
     */
    protected function get_validation_method() {
        return 'typeless';
    }

    protected function validate_contents($contents, $basesecurity, $localoptions) {

        $errors = [];
        $valid = true;
        $caslines = [];
        $notes = [];
        $ilines = [];

        // WIP: probably not in the correct place.
        $payload = $contents[0];

        if (strlen($payload) > $this->maxinputlength) {
            $valid = false;
            $errors[] = stack_string('studentinputtoolong');
            $notes['too_long'] = true;
            $val = '[]';
            $payload = '';
        }

        $payload = stack_utils::maxima_string_to_php_string($payload);
        // Turn into a PHP stdClass object.
        $payload = json_decode($payload);
        if ($payload === null) {
            $valid = false;
            $errors[] = stack_string('invalid_json');
            $notes['invalid_json'] = true;
            $val = '[]';
        }

        // Only pay attention to the "data" in the payload.  Everything else is the
        // responsibility of the input JS.
        if ($payload !== null && property_exists($payload, 'data')) {
            $payload = $payload->data;
        } else {
            // TODO: dig one level deeper when we have sparated information coming back by repeat_id.
            $payload = null;
            $valid = false;
            $errors[] = stack_string('invalid_json');
            $notes['invalid_json'] = true;
            $val = '[]';
        }

        // Pull out from the payload all data as an array.
        $inputs = [];
        if ($payload !== null) {
            // At this depth, full JSON gives an array.
            if (is_array($payload)) {
                foreach ($payload as $repeatid) {
                    foreach ($this->simpleinputs as $inputname => $input) {
                        if (property_exists($repeatid->inputs, $inputname)) {
                            $inputs[$inputname] = $repeatid->inputs->{$inputname};
                        }
                    }
                }
            } else {
                // We have the cut-down "data" from teacher's answers.
                foreach ($this->simpleinputs as $inputname => $input) {
                    if (property_exists($payload, $inputname)) {
                        $inputs[$inputname] = $payload->{$inputname};
                    }
                }
            }
        }

        $states = [];
        $options = new stack_options();
        // Validate each entry separately using the simple input validation.
        foreach ($inputs as $inputname => $val) {
            $input = $this->simpleinputs[$inputname];
            // Val should now be an array of values.
            $exprs = [];
            foreach ($val as $sans) {
                $state = $input->validate_student_response([$inputname => $sans],
                    $options, 'null',
                    new stack_cas_security());
                if ($state->__get('status') === 'valid' || $state->__get('status') === 'score') {
                    $exprs[] = $state->__get('contentsmodified');
                } else {
                    $valid = false;
                }
                $errors[] = $state->__get('errors');
                $notes[$state->__get('note')] = true;
            }
            // If valid, collect together the valid modified expresssions.
            // This is one Maxima list per input.
            $states[$inputname] = 'repeated' . $inputname . ':[' . implode(',', $exprs) . ']';
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
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors,
        $castextprocessor, $inertdisplayform, $ilines) {

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
            list($valid, $errors, $display) = $this->validation_display_baseclass($answer, $lvars, $caslines, $additionalvars, $valid,
                $errors, $castextprocessor, $inertdisplayform, $ilines);

            return [$valid, $errors, $pdisplay . $display];
    }
}
