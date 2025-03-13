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

require_once(__DIR__ . '/../../utils.class.php');

/**
 * Input that is a text area. Each line input becomes one element of a list.
 *
 * The value is stored as a string maxima list. For example [1,hello,x + y].
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_textarea_input extends stack_input {

    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'nounits' => true,
        'simp' => false,
        'consolidatesubscripts' => false,
    ];

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $attributes = [
            'name' => $fieldname,
            'id'   => $fieldname,
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
            'class'     => 'maxima-list',
        ];

        if ($this->is_blank_response($state->contents)) {
            $current = $this->maxima_to_raw_input($this->parameters['syntaxHint']);
            if ($this->parameters['syntaxAttribute'] == '1') {
                $attributes['placeholder'] = $current;
                $current = '';
            }
        } else {
            $current = implode("\n", $state->contents);
        }

        // Sort out size of text area.
        $rows = preg_split('/\n+/u', $current);
        $attributes['rows'] = max(5, count($rows) + 1);

        $boxwidth = $this->parameters['boxWidth'];
        foreach ($rows as $row) {
            $boxwidth = max($boxwidth, strlen($row) + 5);
        }
        $attributes['cols'] = $boxwidth;

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        // Metadata for JS users.
        $attributes['data-stack-input-type'] = 'textarea';
        if ($this->options->get_option('decimals') === ',') {
            $attributes['data-stack-input-decimal-separator']  = ',';
            $attributes['data-stack-input-list-separator'] = ';';
        } else {
            $attributes['data-stack-input-decimal-separator']  = '.';
            $attributes['data-stack-input-list-separator'] = ',';
        }

        return html_writer::tag('textarea', htmlspecialchars($current, ENT_COMPAT), $attributes);
    }

    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'textarea';
        $data['boxWidth'] = $this->parameters['boxWidth'];
        $data['syntaxHint'] = $this->maxima_to_raw_input($this->parameters['syntaxHint']);

        return $data;
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, ['size' => $this->parameters['boxWidth']]);
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Transforms the student's response input into an array.
     * Most return the same as went in.
     *
     * @param array|string $in
     * @return string
     */
    protected function response_to_contents($response) {
        $contents = [];
        if (array_key_exists($this->name, $response)) {
            $sans = $response[$this->name];
            if (trim($sans) == '' && $this->get_extra_option('allowempty')) {
                return ['EMPTYANSWER'];
            }
            $rowsin = explode("\n", $sans);
            $rowsout = [];
            foreach ($rowsin as $key => $row) {
                $cleanrow = trim($row);
                if ($cleanrow !== '') {
                    $contents[] = $cleanrow;
                }
            }
        }
        return $contents;
    }

    protected function caslines_to_answer($caslines, $secrules = false) {
        $vals = [];
        // We don't use full "inputform" here as we need to keep stacklet and stackeq as is.
        $params = [
            'checkinggroup' => true,
            'qmchar' => false,
            'pmchar' => 1,
            'nosemicolon' => true,
            'keyless' => true,
            'dealias' => false, // This is needed to stop pi->%pi etc.
            'nounify' => 1,
            'nontuples' => false,
        ];
        foreach ($caslines as $line) {
            $str = $line->ast_to_string(null, $params);
            if ($line->get_valid() || $str === 'EMPTYANSWER') {
                $vals[] = $str;
            } else {
                // This is an empty place holder for an invalid expression.
                $vals[] = 'EMPTYCHAR';
            }
        }
        $s = '['.implode(',', $vals).']';
        return stack_ast_container::make_from_student_source($s, '', $secrules);
    }

    /**
     * Transforms the contents array into a maxima expression.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        return '['.implode(',', $contents).']';
    }

    /**
     * Transforms a Maxima list into raw input.
     *
     * @param string $in
     * @return string
     */
    protected function maxima_to_raw_input($in) {
        $values = stack_utils::list_to_array($in, false);
        foreach ($values as $key => $val) {
            if (trim($val) != '') {
                $cs = stack_ast_container::make_from_teacher_source($val);
                if ($cs->get_valid()) {
                    $val = $cs->get_inputform(false, 0);
                }
            }
            $values[$key] = $val;
        }
        return implode("\n", $values);
    }

    protected function ajax_to_response_array($in) {
        $in = explode('<br>', $in);
        $in = implode("\n", $in);
        return [$this->name => $in];
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     *
     * @param string $in
     * @return string
     */
    public function maxima_to_response_array($in) {
        $response[$this->name] = $this->maxima_to_raw_input($in);
        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;
    }

    /**
     * This function constructs the display variable for validation.
     *
     * @param stack_casstring $answer, the complete answer.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function validation_display($answer, $lvars, $caslines, $additionalvars,
                                          $valid, $errors, $castextprocessor, $inertdisplayform, $ilines) {

        $rows = [];
        foreach ($caslines as $index => $cs) {
            $row = [];
            $fb = $cs->get_feedback();
            if ($cs->is_correctly_evaluated() && $fb == '') {
                // The zero element of the array defines the display style: 0 = align center, 1 = red frame.
                $row[] = [0, '\(\displaystyle ' . $ilines[$index]->get_display() . ' \)'];
                if ($errors[$index]) {
                    $row[] = [1, stack_maxima_translate($errors[$index])];
                }
            } else {
                // Feedback here is always an error.
                if ($fb !== '') {
                    $errors[] = $fb;
                }
                $valid = false;
                $row[] = [0, stack_maxima_format_casstring($this->rawcontents[$index])];
                $row[] = [1, trim(stack_maxima_translate($cs->get_errors()) . ' ' . $fb)];
            }
            $rows[] = $row;
        }

        // Do not use tables for compact validation.
        $display = '';
        if ($this->get_parameter('showValidation', 1) == 3) {
            foreach ($rows as $row) {
                foreach ($row as $cell) {
                    $display .= $cell[1] . ' ';
                }
                $display .= '<br/>';
            }
        } else {
            $display = '<table style="vertical-align: middle;" ' .
                'border="0" cellpadding="2" cellspacing="0" align="center"><tbody>';
            foreach ($rows as $row) {
                $display .= '<tr><td>';
                foreach ($row as $cell) {
                    // Zero element of the array $cell defines the display style: 0 = align center, 1 = red frame.
                    if ($cell[0] == 0) {
                        $display .= html_writer::tag('div', $cell[1], ['align' => 'center']);
                    } else {
                        $display .= html_writer::tag('div', $cell[1], ['class' => 'alert alert-danger stackinputerror']);
                    }
                }
                $display .= '</td></tr>';
            }
            $display .= '</tbody></table>';
        }

        // Return errors = null to delete error messages from the bottom of the input.
        return [$valid, null, $display];
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function get_parameters_defaults() {
        return [
            'mustVerify'         => true,
            'showValidation'     => 1,
            'boxWidth'           => 20,
            'insertStars'        => 0,
            'syntaxHint'         => '',
            'syntaxAttribute'    => 0,
            'forbidWords'        => '',
            'allowWords'         => '',
            'forbidFloats'       => true,
            'lowestTerms'        => true,
            'sameType'           => true,
            'options'            => '',
        ];
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
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        if ($this->get_extra_option('hideanswer')) {
            return '';
        }
        $values = stack_utils::list_to_array($value, false);
        foreach ($values as $key => $val) {
            if (trim($val) !== '' ) {
                $cs = stack_ast_container::make_from_teacher_source($val);
                $cs->get_valid();
                $val = '<code>'.$cs->get_inputform(true, 0, true, $this->options->get_option('decimals')).'</code>';
            }
            $values[$key] = $val;
        }
        $value = "<br/>".implode("<br/>", $values);

        return stack_string('teacheranswershow', ['value' => $value, 'display' => $display]);
    }

    public function get_api_solution($tavalue) {
        $values = stack_utils::list_to_array($tavalue, false);
        foreach ($values as $key => $val) {
            if (trim($val) !== '' ) {
                $cs = stack_ast_container::make_from_teacher_source($val);
                $cs->get_valid();
                $val = $cs->get_inputform(true, 0, true);
            }
            $values[$key] = $val;
        }

        return ['' => implode("\n", $values)];
    }


}
