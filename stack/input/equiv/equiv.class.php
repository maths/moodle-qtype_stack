<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

require_once(__DIR__ . '/../../utils.class.php');

/**
 * This is an input that allows reasoning by equivalence.
 * Each line input becomes one element of a list.
 *
 * @copyright  2015 Loughborough University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_equiv_input extends stack_input {

    public function render(stack_input_state $state, $fieldname, $readonly) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        $attributes = array(
            'name' => $fieldname,
            'id'   => $fieldname,
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
                if ($cleanrow) {
                    $contents[] = $cleanrow;
                }
            }
        }

        return $contents;
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
    private function maxima_to_raw_input($in) {
        $values = stack_utils::list_to_array($in, false);
        return implode("\n", $values);
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
     * This is the basic validation of the student's "answer".
     * This method is only called if the input is not blank.
     * @param array $contents the content array of the student's input.
     * @return array of the validity, errors strings and modified contents.
     */
    protected function validate_contents($contents, $forbiddenkeys) {

        $errors = $this->extra_validation($contents);
        $valid = !$errors;

        // Now validate the input as CAS code.
        $modifiedcontents = array();
        $caslines = array();
        $errors = array();
        $allowwords = $this->get_parameter('allowWords', '');
        foreach ($contents as $index => $val) {
            $answer = new stack_cas_casstring($val);
            $answer->get_valid('s', $this->get_parameter('strictSyntax', true),
                    $this->get_parameter('insertStars', 0), $allowwords);

            // Ensure student hasn't used a variable name used by the teacher.
            if ($forbiddenkeys) {
                $answer->check_external_forbidden_words($forbiddenkeys);
            }

            $forbiddenwords = $this->get_parameter('forbidWords', '');

            if ($forbiddenwords) {
                $answer->check_external_forbidden_words_literal($forbiddenwords);
            }
            
            $caslines[] = $answer;
            $modifiedcontents[] = $answer->get_casstring();
            $valid = $valid && $answer->get_valid();
            $errors[] = $answer->get_errors();
        }

        // Add in a CAS line which will display the complete argument.
        $completeargument = 'disp_stack_eval_arg('.$this->contents_to_maxima($contents).')';
        $carg = new stack_cas_casstring($completeargument);
        $carg->get_valid('t', $this->get_parameter('strictSyntax', true),
                 $this->get_parameter('insertStars', 0), $allowwords);
        $carg->set_key('comparg');
        $caslines[] = $carg;
        
        return array($valid, $errors, $modifiedcontents, $caslines);
    }

    /**
     * This function constructs any the display variable for validation.
     * For many input types this is simply the complete answer.
     * For text areas and equivalence reasoning this is a more complex arrangement of lines.
     *
     * @param stack_casstring $answer, the complete answer.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function validation_display($answer, $caslines, $valid, $errors) {
      
        $display = '<center><table style="vertical-align: middle;" ' .
                   'border="0" cellpadding="0" cellspacing="0"><tbody>'; 
        foreach($caslines as $index => $cs) {
            $display .= '<tr>';
            if ('' != $cs->get_errors()  || '' == $cs->get_value()) {
                $valid = false;
                $errors[$index] = ' '.stack_maxima_translate($cs->get_errors());
                $display .= '<td>'. stack_maxima_format_casstring($cs->get_raw_casstring()). '</td>';
                $display .= '<td>&nbsp'. stack_maxima_translate($errors[$index]). '</td></tr>';
            } else {
                $display .= '<td>\(\displaystyle ' . $cs->get_display() . ' \)</td>';
            }
            $display .= '</tr>';
        }
        $display .= '</tbody></table></center>';

        return array($valid, $errors, $display);
    }

    
    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => true,
            'showValidation' => 1,
            'boxWidth'       => 20,
            'strictSyntax'   => true,
            'insertStars'    => 0,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'allowWords'     => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => true);
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
        $values = array_map(function ($ex) {
                return '<code>'.$ex.'</code>';
        }, $values);
        $value = "<br/>".implode("<br/>", $values);

        return stack_string('teacheranswershow', array('value' => $value, 'display' => $display));
    }
}
