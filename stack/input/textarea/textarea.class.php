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

    protected $extraoptions = array(
        'nounits' => true,
        'simp' => false
    );

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $attributes = array(
            'name' => $fieldname,
            'id'   => $fieldname,
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
            'class'     => 'maxima-list',
        );

        if ($this->is_blank_response($state->contents)) {
            $current = $this->maxima_to_raw_input($this->parameters['syntaxHint']);
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

        return html_writer::tag('textarea', htmlspecialchars($current), $attributes);
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
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
        $contents = array();
        if (array_key_exists($this->name, $response)) {
            $sans = $response[$this->name];
            $rowsin = explode("\n", $sans);
            $rowsout = array();
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
        $vals = array();
        foreach ($caslines as $line) {
            if ($line->get_valid()) {
                $vals[] = $line->get_evaluationform();
            } else {
                // This is an empty place holder for an invalid expression.
                $vals[] = 'EMPTYCHAR';
            }
        }
        $s = '['.implode(',', $vals).']';
        if (!$secrules) {
            $secrules = $caslines[0]->get_securitymodel();
        }
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
                    $val = $cs->get_inputform();
                }
            }
            $values[$key] = $val;
        }
        return implode("\n", $values);
    }

    protected function ajax_to_response_array($in) {
        $in = explode('<br>', $in);
        $in = implode("\n", $in);
        return array($this->name => $in);
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
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors) {

        $rows = array();
        foreach ($caslines as $index => $cs) {
            $row = array();
            $fb = $cs->get_feedback();
            if ($cs->is_correctly_evaluated() && $fb == '') {
                $row[] = '\(\displaystyle ' . $cs->get_display() . ' \)';
                if ($errors[$index]) {
                    $row[] = stack_maxima_translate($errors[$index]);
                }
            } else {
                // Feedback here is always an error.
                if ($fb !== '') {
                    $errors[] = $fb;
                }
                $valid = false;
                $row[] = stack_maxima_format_casstring($this->rawcontents[$index]);
                $row[] = trim(stack_maxima_translate($cs->get_errors()) . ' ' . $fb);
            }
            $rows[] = $row;
        }

        // Do not use tables for compact validation.
        $display = '';
        if ($this->get_parameter('showValidation', 1) == 3) {
            foreach ($rows as $row) {
                $display .= implode(' ', $row);
                $display .= '<br/>';
            }
        } else {
            $display = '<table style="vertical-align: middle;" ' .
                   'border="0" cellpadding="2" cellspacing="0" align="center"><tbody>';
            foreach ($rows as $row) {
                $display .= '<tr>';
                foreach ($row as $cell) {
                    $display .= '<td>' . $cell . '</td>';
                }
                $display .= '</tr>';
            }
            $display .= '</tbody></table>';
        }

        return array($valid, $errors, $display);
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function get_parameters_defaults() {
        return array(
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
            'options'            => ''
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
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        $values = stack_utils::list_to_array($value, false);
        foreach ($values as $key => $val) {
            if (trim($val) !== '' ) {
                $cs = stack_ast_container::make_from_teacher_source($val);
                $cs->get_valid();
                $val = '<code>'.$cs->get_inputform(true, 0).'</code>';
            }
            $values[$key] = $val;
        }
        $value = "<br/>".implode("<br/>", $values);

        return stack_string('teacheranswershow', array('value' => $value, 'display' => $display));
    }
}
