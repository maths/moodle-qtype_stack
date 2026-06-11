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
require_once(__DIR__ . '/../string/string.class.php');

/**
 * A text-field input which is always interpreted as a Maxima string.
 * This is intended for students to type in a complete proof.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_freetext_input extends stack_string_input {
    /**
     * @var string Maximum input length in characters.
     */
    public $maxinputlength = '4096';
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'validator' => false,
        'align' => 'left',
        'monospace' => false,
        'manualgraded' => true,
    ];

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function get_parameters_defaults() {
        return [
            'mustVerify'      => true,
            'showValidation'  => 1,
            'boxWidth'        => 80,
            'insertStars'     => 0,
            'syntaxHint'      => '',
            'syntaxAttribute' => 0,
            'forbidWords'     => '',
            'allowWords'      => '',
            'forbidFloats'    => true,
            'lowestTerms'     => true,
            'sameType'        => true,
            'options'         => '',
        ];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.
        $attributes = [
            'class'          => 'freetextinput',
            'name'           => $fieldname,
            'id'             => $fieldname,
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
        ];
        if ($this->extraoptions['align'] === 'right') {
            $attributes['class'] .= ' algebraic-right';
        }
        if ($this->extraoptions['monospace']) {
            $attributes['class'] .= ' input-monospace';
        }

        $value = stack_utils::maxima_string_to_php_string($this->contents_to_maxima($state->contents));
        if ($this->is_blank_response($state->contents)) {
            $value = stack_utils::maxima_string_to_php_string($this->parameters['syntaxHint']);
            if ($this->parameters['syntaxAttribute'] == '1') {
                $attributes['placeholder'] = $value;
                $value = '';
            }
        }

        preg_match_all('/\R/', $value, $matches);
        $attributes['rows'] = max(5, count($matches[0])+3);
        $attributes['cols'] = $this->parameters['boxWidth'];

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        // Metadata for JS users.
        $attributes['data-stack-input-type'] = 'freetext';
        $attributes['maxlength'] = $this->maxinputlength;

        return html_writer::tag('textarea', htmlspecialchars($value, ENT_COMPAT), $attributes);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'freetext';
        $data['boxWidth'] = $this->parameters['boxWidth'];
        $data['syntaxHint'] = $this->parameters['syntaxHint'];

        return $data;
    }

    /**
     * This function constructs the display of variables during validation.
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
        // Always display something sensible.
        $display = htmlentities($this->contents_to_maxima($this->rawcontents));
        $display = substr($display, 1, strlen($display) - 2);
        if ($answer->is_correctly_evaluated()) {
            $display = stack_utils::maxima_string_to_php_string($answer->get_value());
            $display = nl2br($display);
        } else {
            $valid = false;
        }
        // TO-DO: something better here.
        $display = html_writer::tag('p', $display);
        return [$valid, $errors, $display, $notes];
    }

    /**
     * Add description here.
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        if ($this->get_extra_option('hideanswer')) {
            return '';
        }
        // By default, we don't show how to "type this in".  This is only done for some, e.g. algebraic and textarea.
        if (trim($value) == 'EMPTYANSWER' || trim($value) == '""') {
            return stack_string('teacheranswerempty');
        }
        if (substr($value, 0, 1) == '"') {
            $value = trim($value);
            $value = substr($value, 1, strlen($value) - 2);
        }
        return stack_string('teacheranswershow_disp', ['display' => '<pre>' . $value . '</pre>']);
    }
}
