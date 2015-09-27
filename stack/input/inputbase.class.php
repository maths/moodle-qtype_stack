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

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../options.class.php');
require_once(__DIR__ . '/../cas/casstring.class.php');
require_once(__DIR__ . '/../cas/cassession.class.php');
require_once(__DIR__ . '/inputstate.class.php');


/**
 * The base class for inputs in Stack.
 *
 * Inputs are the controls that the teacher can put into the question
 * text to receive the student's response.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class stack_input {
    const BLANK = '';
    const VALID = 'valid';
    const INVALID = 'invalid';
    const SCORE = 'score';

    protected static $allparameternames = array(
        'mustVerify',
        'showValidation',
        'boxWidth',
        'boxHeight',
        'strictSyntax',
        'insertStars',
        'syntaxHint',
        'forbidWords',
        'allowWords',
        'forbidFloats',
        'lowestTerms',
        'sameType');

    /**
     * @var string the name of the input.
     * This name has two functions
     *  (1) it is the name of thename of the POST variable that the input from this
     *  element will be submitted as.
     *  (2) it is the name of the CAS variable to which the student's answer is assigned.
     *  Note, that during authoring, the teacher simply types #name# in the question stem to
     *  create these inputs.
     */
    protected $name;

    /**
     * @var string Every input must have a non-empty "teacher's answer".
     * This is assumed to be a valid Maxima string.
     */
    protected $teacheranswer;

    /**
     * Answertest paramaters.
     * @var array paramer name => current value.
     */
    protected $parameters;

    /**
     * Constructor
     *
     * @param string $name the name of the input. This is the name of the
     *      POST variable that the input from this element will be submitted as.
     * @param int $width size of the input.
     * @param string $default initial contets of the input.
     * @param int $maxLength limit on the maximum input length.
     * @param int $height height of the input.
     * @param array $parameters The options for this input. All the opitions have default
     *      values, so you only have to give options that are different from the default.
     */
    public function __construct($name, $teacheranswer, $parameters = null) {
        $this->name = $name;
        $this->teacheranswer = $teacheranswer;
        $class = get_class($this);
        $this->parameters = $class::get_parameters_defaults();

        if (!(null === $parameters || is_array($parameters))) {
            throw new stack_exception('stack_input: __construct: 3rd argumenr, $parameters, ' .
                    'must be null or an array of parameters.');
        }

        if (is_array($parameters)) {
            foreach ($parameters as $name => $value) {
                $this->set_parameter($name, $value);
            }
        }
    }

    /**
     * This method gives the input element a chance to adapt itself given the
     * value of the teacher's model answer for this variant of the question.
     * For example, the matrix question type uses this to work out how many
     * rows and columns it should have.
     * @param string $teacheranswer the teacher's model answer for this input.
     */
    public function adapt_to_model_answer($teacheranswer) {
        // By default, do nothing.
    }

    /**
     * @param string $param a settings parameter name.
     * @return bool whether this input type uses this parameter.
     */
    public function is_parameter_used($param) {
        $class = get_class($this);
        return array_key_exists($param, $class::get_parameters_defaults());
    }

    /**
     * Sets the value of an input parameter.
     * @return array of parameters names.
     */
    public function set_parameter($parameter, $value) {
        if (!$this->is_parameter_used($parameter)) {
            throw new stack_exception('stack_input: setting parameter ' . $parameter .
                    ' which does not exist for inputs of type ' . get_class($this));
        }

        if ($parameter == 'showValidation' && $value === 0 && $this->is_parameter_used('mustVerify')) {
            $this->set_parameter('mustVerify', false);
        }

        $this->parameters[$parameter] = $value;
    }

    /**
     * Get the value of one of the parameters.
     * @param string $parameter the parameter name
     * @param mixed $default the default to return if this parameter is not set.
     */
    protected function get_parameter($parameter, $default = null) {
        if (array_key_exists($parameter, $this->parameters)) {
            return $this->parameters[$parameter];
        } else {
            return $default;
        }
    }

    /**
     * Validates the value of an input parameter.
     * @return array of parameters names.
     */
    public function validate_parameter($parameter, $value) {
        if (!$this->is_parameter_used($parameter)) {
            throw new stack_exception('stack_input: trying to validate parameter ' . $parameter .
                    ' which does not exist for inputs of type ' . get_class($this));
        }

        switch($parameter) {
            case 'mustVerify':
                $valid = is_bool($value);
                break;

            case 'showValidation':
                $valid = is_numeric($value) && $value >= 0 && $value <= 2;
                break;

            case 'strictSyntax':
                $valid = is_bool($value);
                break;

            case 'insertStars':
                $valid = is_numeric($value);
                break;

            case 'forbidFloats':
                $valid = is_bool($value);
                break;

            case 'lowestTerms':
                $valid = is_bool($value);
                break;

            case 'sameType':
                $valid = is_bool($value);
                break;

            default:
                $valid = $this->internal_validate_parameter($parameter, $value);
        }
        return $valid;
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        return true;
    }

    /**
     * Returns a list of the names of all the parameters.
     * @return array of parameters names.
     */
    public function get_parameters_available() {
        return $this->allparameternames;
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters => default value.
     */
    public static function get_parameters_defaults() {
        return array();
    }

    /**
     * Get the input variable that this input expects to process.
     * All the variable names should start with $this->name.
     * @return array string input name => PARAM_... type constant.
     */
    public function get_expected_data() {
        $expected = array();
        $expected[$this->name] = PARAM_RAW;
        if ($this->requires_validation()) {
            $expected[$this->name . '_val'] = PARAM_RAW;
        }
        return $expected;
    }

    /**
     * @return string the teacher's answer, an example of what could be typed into
     * this input as part of a correct response to the question.
     */
    public function get_teacher_answer() {
        return $this->teacheranswer;
    }

    /**
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        // By default, we don't show how to "type this in".  This is only done for some, e.g. algebraic and textarea.
        return stack_string('teacheranswershow_disp', array('display' => $display));
    }

    /**
     * Validate any attempts at this question.
     *
     * @param array $response the student response to the question.
     * @param stack_options $options CAS options to use when validating.
     * @param string $teacheranswer the teachers answer as a string representation of the evaluated expression.
     * @return stack_input_state represents the current state of the input.
     */
    public function validate_student_response($response, $options, $teacheranswer, $forbiddenkeys) {

        if (!is_a($options, 'stack_options')) {
            throw new stack_exception('stack_input: validate_student_response: options not of class stack_options');
        }
        $localoptions = clone $options;

            // The validation field should always come back through as a single RAW Maxima expression for each input.
        if (array_key_exists($this->name . '_val', $response)) {
            $validator = $response[$this->name . '_val'];
        } else {
            $validator = '';
        }

        $contents = $this->response_to_contents($response);

        if (array() == $contents or $this->is_blank_response($contents)) {
            return new stack_input_state(self::BLANK, array(), '', '', '', '', '');
        }

        // This method actually validates any CAS strings etc.
        list($valid, $errors, $modifiedcontents) = $this->validate_contents($contents, $forbiddenkeys);

        // If we can't get a "displayed value" back from the CAS, show the student their original expression.
        $display = stack_maxima_format_casstring($this->contents_to_maxima($contents));
        $interpretedanswer = $this->contents_to_maxima($modifiedcontents);
        $answer = new stack_cas_casstring($interpretedanswer);
        $lvarsdisp = '';

        // Send the string to the CAS.
        if ($valid) {
            if (!$this->get_parameter('sameType')) {
                $teacheranswer = null;
            }

            $singlevarchars = false;
            if (2 == $this->get_parameter('insertStars', 0)) {
                $singlevarchars = true;
            }

            // Generate an expression from which we extract the list of variables in the student's answer.
            $lvars = new stack_cas_casstring('ev(listofvars('.$interpretedanswer.'),simp)');
            $lvars->get_valid('t', $this->get_parameter('strictSyntax', true),
                    $this->get_parameter('insertStars', 0), $this->get_parameter('allowWords', ''));

            $answer->set_cas_validation_casstring($this->name,
                    $this->get_parameter('forbidFloats', false), $this->get_parameter('lowestTerms', false),
                    $singlevarchars,
                    $teacheranswer, $this->get_parameter('allowWords', ''));
            $localoptions->set_option('simplify', false);

            $session = new stack_cas_session(array($answer, $lvars), $localoptions, 0);
            $session->instantiate();

            $session = $session->get_session();
            $answer = $session[0];
            $lvars  = $session[1];

            $errors = stack_maxima_translate($answer->get_errors());
            if ('' != $errors) {
                $valid = false;
            }
            if ('' == $answer->get_value()) {
                $valid = false;
            } else {
                $display = '\[ ' . $answer->get_display() . ' \]';
                $interpretedanswer = $answer->get_value();
                if (!($lvars->get_value() == '[]')) {
                    $lvarsdisp = '\( ' . $lvars->get_display() . '\) ';
                }
            }
        }

        $note = $answer->get_answernote();

        // Answers may not contain the ? character.  CAS-strings may, but answers may not.
        // It is very useful for teachers to be able to add in syntax hints.
        if (!(strpos($interpretedanswer, '?') === false)) {
            $valid = false;
            $errors .= stack_string('qm_error');
        }

        if (!$valid) {
            $status = self::INVALID;
        } else if ($this->get_parameter('mustVerify', true) && $validator != $this->contents_to_maxima($contents)) {
            $status = self::VALID;
        } else {
            $status = self::SCORE;
        }

        return new stack_input_state($status, $contents, $interpretedanswer, $display, $errors, $note, $lvarsdisp);
    }

    /**
     * Decide if the contents of this attempt is blank.
     *
     * @param array $contents a non-empty array of the student's input as a split array of raw strings.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function is_blank_response($contents) {
        $allblank = true;
        foreach ($contents as $val) {
            if (!('' == trim($val))) {
                $allblank = false;
            }
        }
        return $allblank;
    }

    /**
     * This is the basic validation of the student's "answer".
     * This method is only called if the input is not blank.
     *
     * Only a few input methods need to modify this method.
     * For example, Matrix types have two dimensional contents arrays to loop over.
     *
     * @param array $contents the content array of the student's input.
     * @return array of the validity, errors strings and modified contents.
     */
    protected function validate_contents($contents, $forbiddenkeys) {

        $errors = $this->extra_validation($contents);
        $valid = !$errors;

        // Now validate the input as CAS code.
        $modifiedcontents = array();
        $allowwords = $this->get_parameter('allowWords', '');
        foreach ($contents as $val) {
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

            $modifiedcontents[] = $answer->get_casstring();
            $valid = $valid && $answer->get_valid();
            $errors .= $answer->get_errors();
        }

        return array($valid, $errors, $modifiedcontents);
    }

    /**
     * Do any additional validation on the student's input.
     * For example singlechar checks that there is only one charater, and drop
     * down tests that the value is in the list.
     *
     * This method is only called in the input is not blank, so you can assume that.
     *
     * @param unknown_type $contents the student's input as maxima code.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function extra_validation($contents) {
        return '';
    }

    public function requires_validation() {
        return $this->get_parameter('mustVerify', true);
    }

    /**
     * Returns the XHTML for embedding this input in a page.
     *
     * @param string student's current answer to insert into the xhtml.
     * @param string $fieldname the field name to use in the HTML for this input.
     * @param bool $readonly whether the contro should be displayed read-only.
     * @return string HTML for this input.
     */
    public abstract function render(stack_input_state $state, $fieldname, $readonly);

    /**
     * Add this input the MoodleForm, but only used in questiontestform.php.
     * It enables the teacher to enter the data as a CAS variable where necessary
     * when the student might get some html page formatting help.  E.g. teachers
     * will want to enter information into textareas input as a single list, or
     * variable name representing a list, and matrix elements as a single CAS
     * variable, or using Maxima's syntax matrix([...]).
     * @param MoodleQuickForm $mform the form to add elements to.
     */
    public abstract function add_to_moodleform_testinput(MoodleQuickForm $mform);

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

        if ($this->get_parameter('showValidation', 1) == 0 && self::INVALID != $state->status) {
            return '';
        }
        $feedback  = '';
        $feedback .= html_writer::tag('p', stack_string('studentValidation_yourLastAnswer', $state->contentsdisplayed));

        if ($this->requires_validation() && '' !== $state->contents) {
            $feedback .= html_writer::empty_tag('input', array('type' => 'hidden',
                    'name' => $fieldname . '_val', 'value' => $this->contents_to_maxima($state->contents)));
        }

        if (self::INVALID == $state->status) {
            $feedback .= html_writer::tag('p', stack_string('studentValidation_invalidAnswer'));
        }

        if ($state->errors) {
            $feedback .= html_writer::tag('p', $state->errors, array('class' => 'stack_errors'));
        }

        if ($this->get_parameter('showValidation', 1) == 1 && !($state->lvars === '' or $state->lvars === '[]')) {
            $feedback .= html_writer::tag('p', stack_string('studentValidation_listofvariables', $state->lvars));
        }
        return $feedback;
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
            $contents = array($response[$this->name]);
        }
        return $contents;
    }

    /**
     * Transforms the contents array into a maxima expression.
     * Most simply take the first element of the contents array raw.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        if (array_key_exists(0, $contents)) {
            return $contents[0];
        } else {
            return '';
        }
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     * This is used to take a Maxima expression, e.g. a Teacher's answer or a test case, and directly transform
     * it into expected inputs.
     *
     * @param array|string $in
     * @return string
     */
    public function maxima_to_response_array($in) {
        $response[$this->name] = $in;
        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;
    }

}
