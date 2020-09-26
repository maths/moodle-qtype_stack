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
        'nounits' => false,
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
            $cs = stack_ast_container::make_from_teacher_source($current);
            // The syntax hint need not be valid, but we don't want nouns.
            if ($cs->get_valid()) {
                $current = $cs->get_inputform();
            }
            // Put the first line of the value of the teacher's answer in the input.
            if (trim($this->parameters['syntaxHint']) == 'firstline') {
                $values = stack_utils::list_to_array($tavalue, false);
                if (array_key_exists(0, $values) && !is_null($values[0])) {
                    $cs = stack_ast_container::make_from_teacher_source($values[0]);
                    $cs->get_valid();
                    $current = $cs->get_inputform();
                }
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
        );

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        $output = html_writer::tag('textarea', htmlspecialchars($current), $attributes);

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
    private function maxima_to_raw_input($in) {
        $values = stack_utils::list_to_array($in, false);
        foreach ($values as $key => $val) {
            if (trim($val) != '') {
                $cs = stack_ast_container::make_from_teacher_source($val);
                if ($cs->get_valid()) {
                    $val = $cs->get_inputform(true, 0);
                }
            }
            $values[$key] = $val;
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
    protected function validate_contents($contents, $basesecurity, $localoptions) {

        // This input re-defines validate_contents, and so does not make use of extra_validation methods.
        $errors = array();
        $notes = array();
        $valid = true;
        $caslines = array();

        list ($secrules, $filterstoapply) = $this->validate_contents_filters($basesecurity);

        foreach ($contents as $index => $val) {
            $answer = stack_ast_container::make_from_student_source($val, '', $secrules, $filterstoapply,
                    array(), 'Equivline');

            // Is the student permitted to include comments in their answer?
            if (!$this->extraoptions['comments'] && $answer->is_string()) {
                $valid = false;
                $answer->add_errors(stack_string('equivnocomments'));
                $notes['equivnocomments'] = true;
            }

            $note = $answer->get_answernote(true);
            if ($note) {
                foreach ($note as $n) {
                    $notes[$n] = true;
                }
            }

            $caslines[] = $answer;
            $valid = $valid && $answer->get_valid();
            $errors[] = $answer->get_errors();
        }

        // Construct one final "answer" as a single maxima object.
        $answer = $this->caslines_to_answer($caslines, $basesecurity);
        $answer->get_valid();

        return array($valid, $errors, $notes, $answer, $caslines);
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
                        $errors[0] = stack_string('equivfirstline');
                    }
                }
            }
            if (!$foundfirstline) {
                throw new stack_exception("ERROR: expected 'firstline' in the additional variables, but it is missing.");
            }
        }
        $errorfree = true;
        $rows = array();
        foreach ($caslines as $index => $cs) {
            $row = array();
            $fb = $cs->get_feedback();
            if ($cs->is_correctly_evaluated() && $fb == '') {
                $row[] = '\(\displaystyle ' . $cs->get_display() . ' \)';
                if ($errors[$index]) {
                    $errorfree = false;
                    $row[] = stack_maxima_translate($errors[$index]);
                }
            } else {
                // Feedback here is always an error.
                if ($fb !== '') {
                    $errors[] = $fb;
                    $errorfree = false;
                }
                $valid = false;
                $row[] = stack_maxima_format_casstring($this->rawcontents[$index]);
                $row[] = trim(stack_maxima_translate($cs->get_errors()) . ' ' . $fb);
            }
            $rows[] = $row;
        }

        // Do not use tables.
        $display = '';
        foreach ($rows as $row) {
            $display .= implode(' ', $row);
            $display .= '<br/>';
        }

        if (array_key_exists('equivdisplay', $additionalvars)) {
            $equiv = $additionalvars['equivdisplay'];
            if ($equiv->is_correctly_evaluated() && $errorfree) {
                $display = '\[ ' . $equiv->get_display() . ' \]';
            } else if ($valid) {
                // Invalid expressions always throw an error from equivdisplay.
                $display .= $equiv->get_errors();
            }
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
        $s = 'equiv'.$this->name.':disp_stack_eval_arg('.$this->name.', '.$showlogic.', '. $showdomain.
            ', '.$equivdebug.', '.$debuglist.')';
        $an = stack_ast_container::make_from_teacher_source($s);

        $calculus = 'false';
        if ($this->extraoptions['calculus']) {
            $calculus = 'true';
        }
        $ca = stack_ast_container::make_from_teacher_source('stack_calculus:'.$calculus);
        $ca->get_valid();

        $tresponse = $this->maxima_to_response_array($teacheranswer);
        $tcontents = $this->response_to_contents($tresponse);
        // Has the student used the correct first line?
        $fl = stack_ast_container::make_from_teacher_source('firstline:true');
        if ($this->extraoptions['firstline']) {
            if (array_key_exists(0, $tcontents)) {
                $ta = $tcontents[0];
                if (array_key_exists(0, $caslines)) {
                    $sa = $caslines[0]->get_inputform();
                    $fl = stack_ast_container::make_from_teacher_source('firstline:second(ATEqualComAss('.$sa.','.$ta.'))');
                }
            }
        }
        // Looks odd making this true, but if there is a validity error here it will have
        // surfaced somewhere else.
        if (!($fl->get_valid())) {
            $fl = new stack_cas_casstring('firstline:true');
        }

        return array('calculus' => $ca, 'equivdisplay' => $an, 'equivfirstline' => $fl);
    }

    protected function get_validation_method() {
        return 'equiv';
    }

    private function comment_tag($index) {
        return 'EQUIVCOMMENT'.$index;
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
                $cs = stack_ast_container::make_from_teacher_source($val);
                $cs->get_valid();
                $val = '<code>'.$cs->get_inputform(true, 0).'</code>';
            }
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
            $feedback .= html_writer::tag('div', stack_string('studentValidation_invalidAnswer'),
                    array('class' => 'alert alert-danger stackinputerror'));
        }

        if ($this->get_parameter('showValidation', 1) == 1 && !($state->lvars === '' or $state->lvars === '[]')) {
            $feedback .= $this->tag_listofvariables($state->lvars);
        }

        return $feedback;
    }

    protected function ajax_to_response_array($in) {
        $in = explode('<br>', $in);
        $in = implode("\n", $in);
        return array($this->name => $in);
    }
}
