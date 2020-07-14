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

require_once(__DIR__ . '/../../utils.class.php');

/**
 * Input that is a text area.
 * However, the purpose is to allow a student to write language (English) notes.
 * These are not passed into the CAS
 * @copyright  2017 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_notes_input extends stack_input {

    protected $extraoptions = array(
        'hideanswer' => false,
        'manualgraded' => false,
    );

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.
        $attributes = array(
            'name' => $fieldname,
            'id'   => $fieldname,
        );

        if ($this->is_blank_response($state->contents)) {
            $current = $this->parameters['syntaxHint'];
        } else {
            $current = implode("\n", $state->contents);
        }

        // Sort out size of text area.
        $rows = stack_utils::list_to_array($current, false);
        $attributes['rows'] = max(5, count($rows) + 1);

        $boxwidth = $this->parameters['boxWidth'];
        foreach ($rows as $row) {
            $boxwidth = max($boxwidth, strlen($row) + 5);
        }
        $attributes['cols'] = $boxwidth;

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        return html_writer::tag('textarea', htmlspecialchars($current), $attributes) .
            html_writer::tag('div', "", array('class' => 'clearfix'));
    }

    /**
     * This is the basic validation of the student's "answer".
     * This method is only called if the input is not blank.
     *
     * This always returns an answer of the form "true", which is a valid Maxima expression.
     *
     * @param array $contents the content array of the student's input.
     * @return array of the validity, errors strings and modified contents.
     */
    protected function validate_contents($contents, $basesecurity, $localoptions) {
        $errors   = null;
        $notes    = array();
        $caslines = array();
        $valid    = true;
        $answer   = stack_ast_container::make_from_student_source('', '', $basesecurity);;

        return array($valid, $errors, $notes, $answer, $caslines);
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Transforms the contents array into a maxima expression.
     * The notes class always returns a boolean true value.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        return 'true';
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => false,
            'showValidation' => 1,
            'boxWidth'       => 50,
            'insertStars'    => 0,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'allowWords'     => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => true,
            'options'        => '',
        );
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid.
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value > 0;
                break;

            case 'boxHeight':
                $valid = is_int($value) && $value > 0;
                break;
        }
        return $valid;
    }

    /**
     * @return string the teacher's answer, an example of what could be typed into
     * this input as part of a correct response to the question.
     * For the notes class this is always the boolean "true".
     */
    public function get_teacher_answer() {
        return 'true';
    }

    /**
     * For the notes class, there is no teacher's answer.
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        if ($this->extraoptions['hideanswer']) {
            return '';
        }
        return stack_string('teacheranswershownotes');
    }

    /**
     * Generate the HTML that gives the results of validating the student's input.
     * @param stack_input_state $state represents the results of the validation.
     * @param string $fieldname the field name to use in the HTML for this input.
     * @return string HTML for the validation results for this input.
     */
    public function render_validation(stack_input_state $state, $fieldname) {

        if (self::BLANK == $state->status) {
            return '';
        }
        if ($this->get_extra_option('allowempty') && $this->is_blank_response($state->contents)) {
            return '';
        }
        if ($this->get_parameter('showValidation', 1) == 0) {
            return '';
        }

        $contents = $state->contents;
        $render = '';
        if (array_key_exists(0, $contents)) {
            $render .= html_writer::tag('p', $contents[0]);
        }
        $render .= html_writer::tag('p', stack_string('studentValidation_notes'), array('class' => 'stackinputnotice'));
        return format_text(stack_maths::process_display_castext($render));
    }

    public function summarise_response($name, $state, $response) {
        // Output the value for reporting.
        $val = '';
        if (array_key_exists($name, $response)) {
            $val = '"' . addslashes($response[$name]) . '"';
        }
        return $name . ': ' . $val . ' [' . $state->status . ']';
    }

}
