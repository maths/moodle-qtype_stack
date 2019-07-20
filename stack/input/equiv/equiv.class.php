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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../utils.class.php');

/**
 * This is an input that allows reasoning by equivalence.
 * Each line input becomes one element of a list.
 *
 * @copyright  2015 Loughborough University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_equiv_input extends stack_input {

    /**
     * From STACK 4.1 we are not going to continue to add input options as columns in the database.
     * This has numerous problems, and is difficult to maintain. Extra options will be in a JSON-like format.
     *
     * For examples see the numerical input.
     * @var array
     */
    protected $extraoptions = array(
        // Does a student see the equivalence signs at validation time?
        'hideequiv' => false,
        // Does a student see the natural domain at validation time?
        'hidedomain' => false,
        // Must a student have the same first line as the teacher's answer?
        'firstline' => false,
        // Is a student permitted to include comments in their answer?
        'comments' => false,
        // Sets the value of the assume_pos variable, which affects squareing both sides.
        'assume_pos' => false,
        // Sets the value of the assume_real variable, which affects how we deal with complex numbers.
        'assume_real' => false,
        // Sets the value of the stack_calculus variable, which affects how we deal with calulus in arguments.
        'calculus' => false
    );

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        if ($this->is_blank_response($state->contents)) {
            $current = $this->maxima_to_raw_input($this->parameters['syntaxHint']);
            $current = stack_utils::logic_nouns_sort($current, 'remove');
            // Put the first line of the value of the teacher's answer in the input.
            if (trim($this->parameters['syntaxHint']) == 'firstline') {
                $values = stack_utils::list_to_array($tavalue, false);
                $current = stack_utils::logic_nouns_sort($values[0], 'remove');
            }
            // Remove % characters, e.g. %pi should be printed just as "pi".
            $current = str_replace('%', '', $current);
            $rows = explode("\n", $current);
        } else {
            $current = implode("\n", $state->contents);
            $rows = $state->contents;
        }

        // Sort out size of text area.
        $boxwidth = $this->parameters['boxWidth'];
        foreach ($rows as $row) {
            $boxwidth = max($boxwidth, strlen($row));
        }

        $attributes = array(
            'name' => $fieldname,
            'id'   => $fieldname,
            'rows' => max(3, count($rows) + 1),
            'cols' => min($boxwidth, 50),
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
            'class'     => 'equiv',
        );

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        // This class shows the validation next to the input box in a table, and disregards to the position of the
        // [[validation:name]] tag.
        $rendervalidation = $this->render_validation($state, $fieldname);
        $class = "stackinputfeedback";
        if (!$rendervalidation) {
            $class .= ' empty';
        }
        $rendervalidation = html_writer::tag('div', $rendervalidation, array('class' => $class, 'id' => $fieldname.'_val'));

        $output = html_writer::tag('textarea', htmlspecialchars($current), $attributes);
        $output .= $rendervalidation;
        $output = html_writer::tag('div', $output, array('class' => 'equivreasoning'));

        return $output;
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
                if ($cleanrow != '') {
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
        foreach ($contents as $key => $val) {
            $contents[$key] = $this->equals_to_stackeq($val);
        }
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
        foreach ($values as $key => $val) {
            $values[$key] = $this->stackeq_to_equals($val);
        }
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
    protected function validate_contents($contents, $forbiddenkeys, $localoptions) {

        $errors = $this->extra_validation($contents);
        $valid = !$errors;

        // Now validate the input as CAS code.
        $modifiedcontents = array();
        $caslines = array();
        $errors = array();
        $allowwords = $this->get_parameter('allowWords', '');

        foreach ($contents as $index => $val) {
            if ($this->identify_comments($val)) {
                $answer = new stack_cas_casstring($val);
                // Is the student permitted to include comments in their answer?
                if (!$this->extraoptions['comments']) {
                    $valid = false;
                    $answer->add_errors(stack_string('equivnocomments'));
                }
            } else {
                // Process single character variable names in PHP.
                // This is done before we validate the casstring to split up abc->a*b*c which would otherwise be invalid.
                if (2 == $this->get_parameter('insertStars', 0) || 5 == $this->get_parameter('insertStars', 0)) {
                    $val = stack_utils::make_single_char_vars($val, $localoptions,
                        $this->get_parameter('strictSyntax', true), $this->get_parameter('insertStars', 0),
                        $this->get_parameter('allowWords', ''));
                }
                $val = stack_utils::logic_nouns_sort($val, 'add');
                $answer = new stack_cas_casstring($val);
            }

            $answer->get_valid('s', $this->get_parameter('strictSyntax', true),
                $this->get_parameter('insertStars', 0), $allowwords);

            // Ensure student hasn't used a variable name used by the teacher.
            if ($forbiddenkeys) {
                $answer->check_external_forbidden_words($forbiddenkeys);
            }

            $forbiddenwords = $this->get_parameter('forbidWords', '');

            // Forbid function definition for now.
            $forbiddenwords .= ', :=';
            if ($forbiddenwords) {
                $answer->check_external_forbidden_words_literal($forbiddenwords);
            }

            $caslines[] = $answer;

            $modifiedcontents[] = $answer->get_casstring();
            $valid = $valid && $answer->get_valid();
            $errors[] = trim($answer->get_errors());
        }

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
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors) {
        if ($this->extraoptions['firstline']) {
            $foundfirstline = false;
            foreach ($additionalvars as $index => $cs) {
                if ($cs->get_key() === 'firstline') {
                    $foundfirstline = true;
                    if ('false' === $cs->get_value()) {
                        // Then the first line of the student's response does not match that of the teacher.
                        $valid = false;
                        $caslines[0]->add_errors(stack_string('equivfirstline'));
                    }
                }
            }
            if (!$foundfirstline) {
                throw new stack_exception("ERROR: expected 'firstline' in the additional variables, but it is missing.");
            }
        }

        $display = '<center><table style="vertical-align: middle;" ' .
                'border="0" cellpadding="4" cellspacing="0"><tbody>';
        foreach ($caslines as $index => $cs) {
            $display .= '<tr>';
            if ('' != $cs->get_errors()  || '' == $cs->get_value()) {
                $valid = false;
                $errors[$index] = ' '.stack_maxima_translate($cs->get_errors());
                $cds = stack_utils::logic_nouns_sort($cs->get_raw_casstring(), 'remove');
                $display .= '<td>'. stack_maxima_format_casstring($cds). '</td>';
                $display .= '<td>'. stack_maxima_translate($errors[$index]). '</td></tr>';
            } else {
                $display .= '<td>\(\displaystyle ' . $cs->get_display() . ' \)</td>';
            }
            $display .= '</tr>';
        }
        $display .= '</tbody></table></center>';
        if ($valid) {
            $equiv = $additionalvars['equivdisplay'];
            $display = '\[ ' . $equiv->get_display() . ' \]';
        }

        return array($valid, $errors, $display);
    }


    /** This function creates additional session variables.
     */
    protected function additional_session_variables($caslines, $teacheranswer) {
        $equivdebug = 'false';
        $showlogic = 'true';
        if ($this->extraoptions['hideequiv']) {
            $showlogic = 'false';
        }
        $showdomain = 'true';
        if ($this->extraoptions['hidedomain']) {
            $showdomain = 'false';
        }
        $debuglist = 'false';
        $an = new stack_cas_casstring('disp_stack_eval_arg('.$this->name.', '.$showlogic.', '.
                $showdomain.', '.$equivdebug.', '.$debuglist.')');
        $an->get_valid('t', $this->get_parameter('strictSyntax', true),
                 $this->get_parameter('insertStars', 0));
        $an->set_key('equiv'.$this->name);

        $calculus = 'false';
        if ($this->extraoptions['calculus']) {
            $calculus = 'true';
        }
        $ca = new stack_cas_casstring('stack_calculus:'.$calculus);
        $ca->get_valid('t');

        $tresponse = $this->maxima_to_response_array($teacheranswer);
        $tcontents = $this->response_to_contents($tresponse);
        // Has the student used the correct first line?
        $fl = new stack_cas_casstring('firstline:true');
        if ($this->extraoptions['firstline']) {
            if (array_key_exists(0, $tcontents)) {
                $ta = $tcontents[0];
                if (array_key_exists(0, $caslines)) {
                    $sa = $caslines[0]->get_raw_casstring();
                    $fl = new stack_cas_casstring('firstline:second(ATEqualComAss('.$sa.','.$ta.'))');
                }
            }
        }
        // Looks odd making this true, but if there is a validity error here it will have
        // surfaced somewhere else.
        if (!($fl->get_valid('t', $this->get_parameter('strictSyntax', true),
                $this->get_parameter('insertStars', 0)))) {
            $fl = new stack_cas_casstring('firstline:true');
        }

        return array('calculus' => $ca, 'equivdisplay' => $an, 'equivfirstline' => $fl);
    }

    protected function get_validation_method() {
        return 'equiv';
    }


    /** This function decides if an expression looks like a comment in a chain of reasoning.
     */
    private function identify_comments($ex) {
        if (substr(trim($ex), 0, 1) === '"') {
            return true;
        }
        return false;
    }

    private function comment_tag($index) {
        return 'EQUIVCOMMENT'.$index;
    }

    /* Convert an expression starting with an = sign to one with stackeq. */
    private function equals_to_stackeq($val) {
        $val = trim($val);
        if (substr($val, 0, 1) === "=") {
            $trimmed = trim(substr($val, 1));
            if ( $trimmed !== '') {
                $val = 'stackeq(' . $trimmed . ')';
            }
        }
        // Safely wrap "let" statements.
        $langlet = strtolower(stack_string('equiv_LET'));
        if (strtolower(substr($val, 0, strlen($langlet))) === $langlet) {
            $nv = explode('=', substr($val, strlen($langlet) + 1));
            if (count($nv) === 2) {
                $val = 'stacklet('.trim($nv[0]).','.trim($nv[1]).')';
            }
        }
        return $val;
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
            'boxWidth'       => 25,
            'strictSyntax'   => true,
            'insertStars'    => 0,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'allowWords'     => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => false,
            'options'        => ''
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
                $val = stack_utils::logic_nouns_sort($val, 'remove');
            }
            $val = '<code>'.$this->stackeq_to_equals($val).'</code>';
            $values[$key] = $val;
        }
        $value = "<br/>".implode("<br/>", $values);

        return stack_string('teacheranswershow', array('value' => $value, 'display' => $display));
    }

    /**
     * Generate the HTML that gives the results of validating the student's input.
     * This differs from the default in that errors are now given line by line.
     *
     * @param stack_input_state $state represents the results of the validation.
     * @param string $fieldname the field name to use in the HTML for this input.
     * @return string HTML for the validation results for this input.
     */
    public function render_validation(stack_input_state $state, $fieldname) {

        if (self::BLANK == $state->status) {
            return '';
        }

        if ($this->get_parameter('showValidation', 1) == 0 && self::INVALID != $state->status) {
            return '';
        }
        $feedback = stack_maths::process_lang_string($state->contentsdisplayed);

        if ($this->requires_validation() && '' !== $state->contents) {
            $feedback .= html_writer::empty_tag('input', array('type' => 'hidden',
                    'name' => $fieldname . '_val', 'value' => $this->contents_to_maxima($state->contents)));
        }

        if (self::INVALID == $state->status) {
            $feedback .= html_writer::tag('p', stack_string('studentValidation_invalidAnswer'));
        }

        if ($this->get_parameter('showValidation', 1) == 1 && !($state->lvars === '' or $state->lvars === '[]')) {
            $feedback .= html_writer::tag('p', stack_string('studentValidation_listofvariables', $state->lvars));
        }

        return $feedback;
    }

    /**
     * This input type overrides this function to place validation feedback next to the input box.
     */
    public function replace_validation_tags($state, $fieldname, $questiontext) {

        $name = $this->name;
        $response = str_replace("[[validation:{$name}]]", '', $questiontext);

        return $response;
    }

    protected function ajax_to_response_array($in) {
        $in = explode('<br>', $in);
        $in = implode("\n", $in);
        return array($this->name => $in);
    }
}
