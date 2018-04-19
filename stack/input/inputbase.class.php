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

    /**
     * @var string the name of the input.
     * This name has two functions
     *  (1) it is the name of thename of the POST variable that the input from this
     *  element will be submitted as.
     *  (2) it is the name of the CAS variable to which the student's answer is assigned.
     *  Note, that during authoring, the teacher simply types [[input:name]] in the question stem to
     *  create these inputs.
     */
    protected $name;

    /**
     * @var string Every input must have a non-empty "teacher's answer".
     * This is assumed to be a valid Maxima string.
     */
    protected $teacheranswer;

    /**
     * These are the fixed parameters, most of which have hard-wired columns in the Moodle database.
     * @var array
     */
    protected static $allparameternames = array(
        'mustVerify',
        'showValidation',
        'boxWidth',
        'boxHeight',
        'strictSyntax',
        'insertStars',
        'syntaxHint',
        'syntaxAttribute',
        'forbidWords',
        'allowWords',
        'forbidFloats',
        'lowestTerms',
        'sameType');

    /**
     * From STACK 4.1 we are not going to continue to add input options as columns in the database.
     * This has numerous problems, and is difficult to maintain. Extra options will be in a JSON-like format.
     *
     * For examples see the numerical input.
     * @var array
     */
    protected $extraoptions = array();

    /**
     * The question level options for CAS sessions.
     */
    protected $options;

    /**
     * Inputtype paramaters.
     * @var array paramer name => current value.
     */
    protected $parameters;

    /**
     * Catch and report runtime errors.
     * @var array.
     */
    protected $errors = null;

    /**
     * Decide if the student's expression should have units.
     * @var bool.
     */
    protected $units = false;

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
    public function __construct($name, $teacheranswer, $options = null, $parameters = null) {
        if (trim($name) === '') {
            throw new stack_exception('stack_input: $name must be non-empty.');
        }

        $this->name = $name;
        $this->teacheranswer = $teacheranswer;
        $class = get_class($this);
        $this->parameters = $class::get_parameters_defaults();

        if (!(null === $options || is_a($options, 'stack_options'))) {
            throw new stack_exception('stack_input: $options must be stack_options.');
        }
        $this->options = $options;

        if (!(null === $parameters || is_array($parameters))) {
            throw new stack_exception('stack_input: __construct: 3rd argumenr, $parameters, ' .
                    'must be null or an array of parameters.');
        }

        if (is_array($parameters)) {
            foreach ($parameters as $name => $value) {
                $this->set_parameter($name, $value);
            }
        }

        $this->internal_contruct();
    }

    /* This allows each input type to adapt to the values of parameters.  For example, the dropdown and units
     * use this to sort out options.
     */
    protected function internal_contruct() {
        $options = $this->get_parameter('options');

        if (trim($options) != '') {
            $options = explode(',', $options);
            foreach ($options as $option) {
                $option = strtolower(trim($option));
                list($option, $arg) = stack_utils::parse_option($option);

                // Only accept those options specified in the array for this input type.
                if (array_key_exists($option, $this->extraoptions)) {
                    if ($arg === '') {
                        // Extra options with no argument set a Boolean flag.
                        $this->extraoptions[$option] = true;
                    } else {
                        $this->extraoptions[$option] = $arg;
                    }
                } else {
                    $this->errors[] = stack_string('inputoptionunknown', $option);
                }
            }
        }
        $this->validate_extra_options();
    }

    /**
     * Validate the individual extra options.
     */
    protected function validate_extra_options() {

        foreach ($this->extraoptions as $option => $arg) {

            switch($option) {

                case 'novars':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptbooplerr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'floatnum':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptbooplerr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'rationalnum':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptbooplerr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'rationalized':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptbooplerr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'negpow':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptbooplerr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'mindp':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'maxdp':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'minsf':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                case 'maxsf':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', array('opt' => $option, 'val' => $arg));
                    }
                    break;

                default:
                    $this->errors[] = stack_string('inputoptionunknown', $option);
            }
        }

        $mindp = false;
        $maxdp = false;
        $minsf = false;
        $maxsf = false;
        if (array_key_exists('mindp', $this->extraoptions)) {
            $mindp = $this->extraoptions['mindp'];
        }
        if (array_key_exists('maxdp', $this->extraoptions)) {
            $maxdp = $this->extraoptions['maxdp'];
        }
        if (array_key_exists('minsf', $this->extraoptions)) {
            $minsf = $this->extraoptions['minsf'];
        }
        if (array_key_exists('maxsf', $this->extraoptions)) {
            $maxsf = $this->extraoptions['maxsf'];
        }
        if (is_numeric($mindp) && is_numeric($maxdp) && $mindp > $maxdp) {
                    $this->errors[] = stack_string('numericalinputminmaxerr');
        }
        if (is_numeric($minsf) && is_numeric($maxsf) && $minsf > $maxsf) {
                    $this->errors[] = stack_string('numericalinputminmaxerr');
        }
        if ((is_numeric($mindp) || is_numeric($maxdp)) && (is_numeric($minsf) || is_numeric($maxsf))) {
                    $this->errors[] = stack_string('numericalinputminsfmaxdperr');
        }

        return true;
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
        // Often setting a paramter needs to update internal flags, so we call this again.
        // Mostly used by testing.
        $this->internal_contruct();
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
        return stack_string('teacheranswershow_disp', array('display' => '\( '.$display.' \)'));
    }

    /* Convert an expression starting with a stackeq to an equals sign. */
    protected function stackeq_to_equals($val) {
        if (substr(trim($val), 0, 8) == 'stackeq(') {
            $val = '= ' . substr(trim($val), 8, -1);
        }
        return $val;
    }

    /**
     * Validate any attempts at this question.
     *
     * @param array $response the student response to the question.
     * @param stack_options $options CAS options to use when validating.
     * @param string $teacheranswer the teachers answer as a string representation of the evaluated expression.
     * @param array $forbiddenkeys is an array of casstring keys which appears in the question variables.
     * @return stack_input_state represents the current state of the input.
     */
    public function validate_student_response($response, $options, $teacheranswer, $forbiddenkeys, $ajaxinput = false) {
        if (!is_a($options, 'stack_options')) {
            throw new stack_exception('stack_input: validate_student_response: options not of class stack_options');
        }
        $localoptions = clone $options;
        $localoptions->set_option('simplify', false);

        if ($ajaxinput) {
            $response = $this->ajax_to_response_array($response);
        }
        $contents = $this->response_to_contents($response);

        // The validation field should always come back through as a single RAW Maxima expression for each input.
        if (array_key_exists($this->name . '_val', $response)) {
            $validator = $response[$this->name . '_val'];
        } else {
            $validator = '';
        }

        if (array() == $contents or $this->is_blank_response($contents)) {
            // Runtime errors may make it appear as if this response is blank, so we put any errors in here.
            $errors = $this->get_errors();
            if ($errors) {
                $errors = implode(' ', $errors);
            }
            return new stack_input_state(self::BLANK, array(), '', '', $errors, '', '');
        }

        $singlevarchars = false;
        if (2 == $this->get_parameter('insertStars', 0) || 5 == $this->get_parameter('insertStars', 0)) {
            $singlevarchars = true;
        }

        // This method actually validates any CAS strings etc.
        // Modified contents is already an array of things which become individually validated CAS statements.
        // At this sage, $valid records the PHP validation or other non-CAS issues.
        list($valid, $errors, $modifiedcontents, $caslines) = $this->validate_contents($contents, $forbiddenkeys, $localoptions);

        // Match up lines from the teacher's answer to lines in the student's answer.
        // Send as much of the string to the CAS as possible.
        $validationmethod = $this->get_validation_method();
        $checktype = false;
        if ('checktype' == $validationmethod || 'units' == $validationmethod || 'unitsnegpow' == $validationmethod) {
            $checktype = true;
            $tresponse = $this->maxima_to_response_array($teacheranswer);
            $tcontents = $this->response_to_contents($tresponse);
            list($tvalid, $terrors, $tmodifiedcontents, $tcaslines)
                = $this->validate_contents($tcontents, $forbiddenkeys, $localoptions);
        } else {
            $tcaslines = array();
        }
        $tvalidator = array();
        foreach ($caslines as $index => $cs) {
            $tvalidator[$index] = null;
            if (array_key_exists($index, $tcaslines)) {
                $ta = $tcaslines[$index];
                $tvalidator[$index] = $ta->get_casstring();
            }
        }
        $interpretedanswer = $this->contents_to_maxima($modifiedcontents);
        $lvarsdisp   = '';
        $note        = '';
        $sessionvars = array();

        // Validate each line separately, where required and when there is something from the teacher to match up to.
        foreach ($caslines as $index => $cs) {
            // Check the teacher actually has an answer in this slot.
            $ta = '0';
            $trivialta = true;
            if (array_key_exists($index, $tvalidator)) {
                if (!('' == trim($tvalidator[$index]))) {
                    $ta = $tvalidator[$index];
                    $trivialta = false;
                }
            }
            // If we expect to check types, but don't have a teacher's answer just set to typeless.
            // This can happen legitimately, e.g. where a student has too many elements in the textarea.
            // Mostly it does not happen as other input types require a non-trivial teacher's answer at authoring time.
            $ivalidationmethod = $validationmethod;
            if ($checktype && $trivialta) {
                $ivalidationmethod = 'typeless';
            }
            if (array_key_exists($index, $errors) && '' == $errors[$index]) {
                $cs->set_cas_validation_casstring($this->name.$index,
                    $this->get_parameter('forbidFloats', false), $this->get_parameter('lowestTerms', false),
                    $ta, $ivalidationmethod, $this->get_parameter('allowWords', ''));
                $sessionvars[] = $cs;
            }
        }

        // Ensure we have an element in the session which is the whole answer.
        // This results in a duplication for many, but textareas create a single list here representing the whole answer.
        $answer = new stack_cas_casstring($interpretedanswer);
        if ($this->units) {
            $answer->set_units(true);
        }
        $answer->set_cas_validation_casstring($this->name,
            $this->get_parameter('forbidFloats', false), $this->get_parameter('lowestTerms', false),
            $teacheranswer, $validationmethod, $this->get_parameter('allowWords', ''));
        if ($valid && $answer->get_valid()) {
            $sessionvars[] = $answer;
        }

        // Generate an expression from which we extract the list of variables in the student's answer.
        // We do this from the *answer* once interprted, so stars are inserted if insertStars=2.
        $lvars = new stack_cas_casstring('ev(sort(listofvars('.$this->name.')),simp)');
        $lvars->get_valid('t', $this->get_parameter('strictSyntax', true),
                $this->get_parameter('insertStars', 0), $this->get_parameter('allowWords', ''));
        if ($lvars->get_valid() && $valid && $answer->get_valid()) {
            $sessionvars[] = $lvars;
        }
        $additionalvars = $this->additional_session_variables($caslines, $teacheranswer);
        $sessionvars = array_merge($sessionvars, $additionalvars);

        $localoptions->set_option('simplify', false);
        $session = new stack_cas_session($sessionvars, $localoptions, 0);
        $session->instantiate();

        // Since $lvars and $answer and the other casstrings are passed by reference, into the $session,
        // we don't need to extract updated values from the instantiated $session explicitly.
        list($valid, $errors, $display) = $this->validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors);

        if ('' == $answer->get_value()) {
            $valid = false;
        } else {
            if (!($lvars->get_value() == '[]' || trim($lvars->get_dispvalue()) == '')) {
                $lvarsdisp = '\( ' . $lvars->get_display() . '\) ';
            }
        }

        // Answers may not contain the ? character.  CAS-strings may, but answers may not.
        // It is very useful for teachers to be able to add in syntax hints.
        if (!(strpos($interpretedanswer, '?') === false)) {
            $valid = false;
            $errors[] = stack_string('qm_error');
        }

        $note = $answer->get_answernote();
        if ($errors) {
            $errors = implode(' ', $errors);
        }

        if (!$valid) {
            $status = self::INVALID;
        } else if ($this->get_parameter('mustVerify', true) && !($validator === $this->contents_to_maxima($contents))) {
            $status = self::VALID;
        } else {
            $status = self::SCORE;
        }

        return new stack_input_state($status, $contents, $interpretedanswer, $display, $errors, $note, $lvarsdisp);
    }

    /* Allow different input types to change the CAS method used.
     * In particular, the units and equiv inputs do something different here.
     */
    protected function get_validation_method() {
        $validationmethod = 'checktype';
        if (!$this->get_parameter('sameType')) {
            $validationmethod = 'typeless';
        }
        return $validationmethod;
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
            if (!('' === trim($val))) {
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
     * @param array $forbiddenkeys is an array of keys of casstings from the question
     *                             variables which must not appear in the student's input.
     * @return array of the validity, errors strings, modified contents and caslines.
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
            // Process single character variable names in PHP.
            // This is done before we validate the casstring to split up abc->a*b*c which would otherwise be invalid.
            if (2 == $this->get_parameter('insertStars', 0) || 5 == $this->get_parameter('insertStars', 0)) {
                $val = stack_utils::make_single_char_vars($val, $localoptions,
                        $this->get_parameter('strictSyntax', true), $this->get_parameter('insertStars', 0),
                        $this->get_parameter('allowWords', ''));
            }

            $val = stack_utils::logic_nouns_sort($val, 'add');
            $answer = new stack_cas_casstring($val);
            if ($this->units) {
                $answer->set_units(true);
            }
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

        return array($valid, $errors, $modifiedcontents, $caslines);
    }

    /**
     * Do any additional validation on the student's input.
     * For example singlechar checks that there is only one charater, and drop
     * down tests that the value is in the list.
     *
     * This method is only called if the input is not blank, so you can assume that.
     *
     * @param unknown_type $contents the student's input as maxima code.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function extra_validation($contents) {
        return '';
    }

    /**
     * This function creates additional session variables.
     * If needed, these will be used by the extra options.
     */
    protected function additional_session_variables($caslines, $teacheranswer) {

        $additionalvars = array();

        if (array_key_exists('floatnum', $this->extraoptions) && $this->extraoptions['floatnum']) {
            $floatnum = new stack_cas_casstring('floatnump('.$this->name.')');
            $floatnum->get_valid('t');
            $additionalvars['floatnum'] = $floatnum;
        }

        if (array_key_exists('rationalnum', $this->extraoptions) && $this->extraoptions['rationalnum']) {
            $rationalnum = new stack_cas_casstring('rationalnum('.$this->name.')');
            $rationalnum->get_valid('t');
            $additionalvars['rationalnum'] = $rationalnum;
        }

        if (array_key_exists('rationalized', $this->extraoptions) && $this->extraoptions['rationalized']) {
            $rationalized = new stack_cas_casstring('rationalized('.$this->name.')');
            $rationalized->get_valid('t');
            $additionalvars['rationalized'] = $rationalized;
        }

        return $additionalvars;
    }

    /**
     * This function constructs the display of variables during validation, and undertakes additional validation.
     * For many input types this is simply the complete answer.
     * For text areas and equivalence reasoning this is a more complex arrangement of lines.
     *
     * @param stack_casstring $answer, the complete answer.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors) {

        $display = stack_maxima_format_casstring($answer->get_raw_casstring());
        if ('' != $answer->get_errors()) {
            $valid = false;
            $errors = array(stack_maxima_translate($answer->get_errors()));
        }
        if (trim($answer->get_display()) == '') {
            $valid = false;
        } else {
            $display = '\[ ' . $answer->get_display() . ' \]';
        }

        // Guard clause at this point.
        if (!$valid) {
            return array($valid, $errors, $display);
        }

        // The "novars" option is only used by the numerical input type.
        if (array_key_exists('novars', $this->extraoptions)) {
            if ($lvars->get_value() != '[]') {
                $valid = false;
                $errors[] = stack_string('numericalinputvarsforbidden');
                $this->set_parameter('showValidation', 1);
            }
        }

        if (array_key_exists('floatnum', $additionalvars)) {
            $fn = $additionalvars['floatnum'];
            if ($this->extraoptions['floatnum'] && $fn->get_value() == 'false') {
                $valid = false;
                $errors[] = stack_string('numericalinputmustfloat');
            }
        }

        $mindp = false;
        $maxdp = false;
        $minsf = false;
        $maxsf = false;
        if (array_key_exists('mindp', $this->extraoptions)) {
            $mindp = $this->extraoptions['mindp'];
        }
        if (array_key_exists('maxdp', $this->extraoptions)) {
            $maxdp = $this->extraoptions['maxdp'];
        }
        if (array_key_exists('minsf', $this->extraoptions)) {
            $minsf = $this->extraoptions['minsf'];
        }
        if (array_key_exists('maxsf', $this->extraoptions)) {
            $maxsf = $this->extraoptions['maxsf'];
        }
        // Do we need to check any numerical accuracy at validation stage?
        if ($mindp || $maxdp || $minsf || $maxsf) {
            $fltfmt = stack_utils::decimal_digits($answer->get_raw_casstring());
            $accuracychecked = false;
            if (!is_bool($mindp) && !is_bool($maxdp) && $mindp == $maxdp) {
                $accuracychecked = true;
                if ($fltfmt['decimalplaces'] < $mindp || $fltfmt['decimalplaces'] > $maxdp) {
                    $valid = false;
                    $errors[] = stack_string('numericalinputdp', $mindp);
                }
            }
            if (!is_bool($minsf) && !is_bool($minsf) && $minsf == $maxsf) {
                $accuracychecked = true;
                if ($fltfmt['upperbound'] < $minsf || $fltfmt['lowerbound'] > $maxsf) {
                    $valid = false;
                    $errors[] = stack_string('numericalinputsf', $minsf);
                }
            }
            if (!$accuracychecked && !is_bool($mindp) && $fltfmt['decimalplaces'] < $mindp) {
                $valid = false;
                $errors[] = stack_string('numericalinputmindp', $mindp);
            }
            if (!$accuracychecked && !is_bool($maxdp) && $fltfmt['decimalplaces'] > $maxdp) {
                $valid = false;
                $errors[] = stack_string('numericalinputmaxdp', $maxdp);
            }
            if (!$accuracychecked && !is_bool($minsf) && $fltfmt['upperbound'] < $minsf) {
                $valid = false;
                $errors[] = stack_string('numericalinputminsf', $minsf);
            }
            if (!$accuracychecked && !is_bool($maxsf) && $fltfmt['lowerbound'] > $maxsf) {
                $valid = false;
                $errors[] = stack_string('numericalinputmaxsf', $maxsf);
            }
        }

        if (array_key_exists('rationalnum', $additionalvars)) {
            $rn = $additionalvars['rationalnum'];
            if ($this->extraoptions['rationalnum'] && $rn->get_value() == 'false') {
                $valid = false;
                $errors[] = stack_string('numericalinputmustrational');
            }
        }

        if (array_key_exists('rationalized', $additionalvars)) {
            $rn = $additionalvars['rationalized'];
            if ($this->extraoptions['rationalized'] && $rn->get_value() !== 'true') {
                $valid = false;
                $errors[] = stack_string('ATLowestTerms_not_rat', array('m0' => '\[ '.$rn->get_display().' \]'));
            }
        }

        return array($valid, $errors, $display);
    }

    public function requires_validation() {
        return $this->get_parameter('mustVerify', true);
    }

    /**
     * Returns the XHTML for embedding this input in a page.
     *
     * @param string student's current answer to insert into the xhtml.
     * @param string $fieldname the field name to use in the HTML for this input.
     * @param bool $readonly whether the control should be displayed read-only.
     * @param array $tavalue the value of the teacher's answer for this input.
     * @return string HTML for this input.
     */
    public abstract function render(stack_input_state $state, $fieldname, $readonly, $tavalue);

    /*
     * Render any error messages.
     */
    protected function render_error($error) {
        $errors = $this->get_errors();
        if ($errors) {
            $errors = implode(' ', $errors);
        }
        $result = html_writer::tag('p', stack_string('ddl_runtime'));
        $result .= html_writer::tag('p', $errors);
        return html_writer::tag('div', $result, array('class' => 'error'));
    }

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
            $feedback .= $this->tag_listofvariables($state->lvars);
        }
        return $feedback;
    }

    /* Allows individual input types to change the way the list of variables is tagged.
     * Used by the units input type.
     */
    protected function tag_listofvariables($vars) {
        return html_writer::tag('p', stack_string('studentValidation_listofvariables', $vars));
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
     * Most simply take the casstring from the first element of the contents array.
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
     * This is used by the question to get the teacher's correct response.
     * The dropdown type needs to intercept this to filter the correct answers.
     * @param unknown_type $in
     */
    public function get_correct_response($in) {
        $value = stack_utils::logic_nouns_sort($in, 'remove');
        return $this->maxima_to_response_array($value);
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

    /**
     * This function is responsible for removing the validation tags from the question stem and replacing
     * them with the validation feedback.  Only the equiv input type currently does anything different here.
     */
    public function replace_validation_tags($state, $fieldname, $questiontext) {

        $name = $this->name;
        $feedback = $this->render_validation($state, $fieldname);

        $class = "stackinputfeedback";
        if (!$feedback) {
            $class .= ' empty';
        }
        $feedback = html_writer::tag('div', $feedback, array('class' => $class, 'id' => $fieldname.'_val'));
        $response = str_replace("[[validation:{$name}]]", $feedback, $questiontext);

        return $response;
    }

    /**
     * The AJAX instant validation method mostly returns a Maxima expression.
     * Mostly, we need an array, labelled with the input name.
     *
     * The text areas and equiv input types are not Maxima expressions yet,
     * as they have newline characters in.
     *
     * The matrix type is different.  The javascript creates a single Maxima expression,
     * and we need to split this up into an array of individual elements.
     *
     * @param string $in
     * @return array
     */
    protected function ajax_to_response_array($in) {
        return array($this->name => $in);
    }

    /*
     * Return the value of any errors.
     */
    public function get_errors() {
        if ($this->errors === null) {
            return null;
        }
        // Send each error only once.
        $errors = array();
        foreach ($this->errors as $err) {
            $err = trim($err);
            $errors[$err] = true;
        }
        return array_keys($errors);
    }
}
