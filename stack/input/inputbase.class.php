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
require_once(__DIR__ . '/../cas/cassession2.class.php');
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

    const GRAMMAR_FIX_INSERT_STARS = 1;
    const GRAMMAR_FIX_SPACES = 2;
    const GRAMMAR_FIX_SINGLE_CHAR = 4;
    const GRAMMAR_FIX_FUNCTIONS = 16;

    /**
     * @var string the name of the input.
     * This name has two functions
     *  (1) it is the name of the POST variable that the input from this
     *  element will be submitted as.
     *  (2) it is the name of the CAS variable to which the student's answer is assigned.
     *  Note, that during authoring, the teacher simply types [[input:name]] in the question stem to
     *  create these inputs.
     */
    protected $name;

    /**
     * @var int the maximum length of a permitted input.
     */
    protected $maxinputlength = 32768;

    /**
     * Special variables in the question which should be exposed to the inputs and answer tests.
     */
    protected $contextsession = [];

    /**
     * @var string Every input must have a non-empty "teacher's answer".
     * This is assumed to be a valid Maxima string in inputform.
     */
    protected $teacheranswer;

    /**
     * These are the fixed parameters, most of which have hard-wired columns in the Moodle database.
     * @var array
     */
    protected static $allparameternames = [
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
        'sameType',
    ];

    /**
     * From STACK 4.1 we are not going to continue to add input options as columns in the database.
     * This has numerous problems, and is difficult to maintain. Extra options will be in a JSON-like format.
     *
     * For examples see the numerical input.
     * @var array
     */
    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
    ];

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
    protected $errors = [];

    /**
     * Store any raw contents for use in error messages.
     * @var array.
     */
    protected $rawcontents = [];

    /**
     * Decide if the input is being used at runtime or just constructed elsewhere.
     * @var bool.
     */
    protected $runtime = true;

    /**
     * Filters to apply for display in validate_contents
     * @var array
     */
    protected $protectfilters = ['910_inert_float_for_display', '912_inert_string_for_display'];

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
     * @param bool $runtime This decides if we are at runtime (true) or in edit mode.  Can we rely on the value of
     * the teacher's answer as an instantiated variable?
     */
    public function __construct($name, $teacheranswer, $options = null, $parameters = null, $runtime = true) {
        if (trim($name) === '') {
            throw new stack_exception('stack_input: $name must be non-empty.');
        }

        $this->name = $name;
        $this->teacheranswer = $teacheranswer;
        $this->runtime = $runtime;
        $class = get_class($this);
        foreach ($class::get_parameters_defaults() as $name => $value) {
            $this->set_parameter($name, $value);
        }

        if (!(null === $options || is_a($options, 'stack_options'))) {
            throw new stack_exception('stack_input: $options must be stack_options.');
        }
        $this->options = $options;
        if ($this->options === null) {
            $this->options = new stack_options();
        }

        if (!(null === $parameters || is_array($parameters))) {
            throw new stack_exception('stack_input: __construct: 3rd argumenr, $parameters, ' .
                    'must be null or an array of parameters.');
        }

        if (is_array($parameters)) {
            foreach ($parameters as $name => $value) {
                $this->set_parameter($name, $value);
            }
        }

        $this->internal_construct();
    }

    /**
     * This allows each input type to adapt to the values of parameters.  For example, the dropdown and units
     * use this to sort out options.
     */
    protected function internal_construct() {
        $options = $this->get_parameter('options');
        $setoptions = [];
        if (trim($options ?? '') != '') {
            $options = explode(',', $options);
            foreach ($options as $option) {
                $option = strtolower(trim($option));
                list($option, $arg) = stack_utils::parse_option($option);
                // Only accept those options specified in the array for this input type.
                if (array_key_exists($option, $this->extraoptions)) {
                    if ($arg === '') {
                        // Extra options with no argument set a Boolean flag.
                        $this->extraoptions[$option] = true;
                    } else if ($arg === 'false') {
                        $this->extraoptions[$option] = false;
                    } else if ($arg === 'true') {
                        $this->extraoptions[$option] = true;
                    } else {
                        $this->extraoptions[$option] = $arg;
                    }
                    $setoptions[] = $option;
                } else {
                    $this->errors[] = stack_string('inputoptionunknown', $option);
                }
            }
        }
        $this->set_defaults($setoptions);
        $this->validate_extra_options();
    }

    /**
     * Set extra options with defaults to the default if they have not been explicitly set.
     *
     * @param [] $setoptions - array of options that have been explicity set
     * @return void
     */
    protected function set_defaults($setoptions) {
        $optionswithdefaults = ['monospace'];
        foreach ($optionswithdefaults as $currentoption) {
            if (!array_key_exists($currentoption, $this->extraoptions) || array_search($currentoption, $setoptions) !== false) {
                // Option not available for this input type or has been explicitly set.
                continue;
            }

            $functionname = "is_{$currentoption}";
            $this->extraoptions[$currentoption] = stack_options::$functionname(get_class($this));
        }
    }

    /**
     * Validate the individual extra options.
     */
    public function validate_extra_options() {

        foreach ($this->extraoptions as $option => $arg) {

            switch($option) {

                case 'manualgraded':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'novars':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'simp':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'floatnum':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'intnum':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'rationalnum':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'hideanswer':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'allowempty':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'rationalized':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'negpow':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'consolidatesubscripts':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'checkvars':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'mindp':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'maxdp':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'minsf':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'maxsf':
                    if (!($arg === false || is_numeric($arg))) {
                        $this->errors[] = stack_string('numericalinputoptinterr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'mul':
                    // Mul was depricated in version 4.2.
                    $this->errors[] = stack_string('stackversionmulerror');

                case 'hideequiv':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'hidedomain':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'comments':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'firstline':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'assume_pos':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'assume_real':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'calculus':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'align':
                    if ($arg !== 'left' && $arg !== 'right') {
                        $this->errors[] = stack_string('inputopterr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'monospace':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'nounits':
                    if (!(is_bool($arg))) {
                        $this->errors[] = stack_string('numericalinputoptboolerr', ['opt' => $option, 'val' => $arg]);
                    }
                    break;

                case 'validator' || 'feedback':
                    // Perform simple checking of function names: not fully general.
                    $good = true;
                    if ($arg === false) {
                        $good = true;
                    } else if (!preg_match('/^([a-zA-Z]+|[a-zA-Z]+[0-9a-zA-Z_]*[0-9a-zA-Z]+)$/', $arg)) {
                        $good = false;
                    }
                    if (!$good) {
                        $this->errors[] = stack_string('inputvalidatorerr', ['opt' => $option, 'val' => $arg]);
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

    /*
     * Set the contextsession values.
     */
    public function add_contextsession($contextsession) {
        if ($contextsession != null) {
            // Always make this the start of an array.
            $this->contextsession = array_merge($this->contextsession, [$contextsession]);
        }
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

        // Legacy values used up to this point.
        if ($parameter == 'insertStars') {
            $this->parameters['grammarAutofixes'] = stack_input_factory::convert_legacy_insert_stars($value);
        }
        $this->internal_construct();
    }

    /**
     * Get the value of one of the parameters.
     * @param string $parameter the parameter name
     * @param mixed $default the default to return if this parameter is not set.
     */
    public function get_parameter($parameter, $default = null) {
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
                $valid = is_numeric($value) && $value >= 0 && $value <= 3;
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
        return [];
    }

    /**
     * Get the value of one of the extra options
     * @param string $topyion the parameter name
     * @param mixed $default the default to return if this parameter is not set.
     */
    public function get_extra_option($option, $default = false) {
        if (array_key_exists($option, $this->extraoptions)) {
            return $this->extraoptions[$option];
        } else {
            return $default;
        }
    }

    /*
     * Return the value of any extra options.
     */
    public function get_extra_options() {
        return $this->extraoptions;
    }

    /**
     * Get the input variable that this input expects to process.
     * All the variable names should start with $this->name.
     * @return array string input name => PARAM_... type constant.
     */
    public function get_expected_data() {
        $expected = [];
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
     * @return string the teacher's answer, suitable for testcase construction.
     */
    public function get_teacher_answer_testcase() {
        return $this->teacheranswer;
    }

    /**
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        if ($this->get_extra_option('hideanswer')) {
            return '';
        }
        // By default, we don't show how to "type this in".  This is only done for some, e.g. algebraic and textarea.
        if (trim($value) == 'EMPTYANSWER') {
            return stack_string('teacheranswerempty');
        }
        return stack_string('teacheranswershow_disp', ['display' => '\( '.$display.' \)']);
    }

    /**
     * Decide if the contents of this attempt is blank.
     *
     * @param array $contents a non-empty array of the student's input as a split array of raw strings.
     * @return boolean
     *
     */
    protected function is_blank_response($contents) {
        $allblank = true;
        foreach ($contents as $val) {
            if (!('' === trim($val) || 'EMPTYANSWER' == $val)) {
                $allblank = false;
            }
        }
        return $allblank;
    }

    /**
     * Validate any attempts at this question.
     *
     * @param array $response the student response to the input.
     * @param stack_options $options CAS options to use when validating.
     * @param string $teacheranswer the teachers answer as a string representation of the evaluated expression.
     * @param stack_cas_security $basesecurity declares the forbidden keys used in the question
     *             as well as wether we are dealign with units.
     * @return stack_input_state represents the current state of the input.
     */
    public function validate_student_response($response, $options, $teacheranswer, stack_cas_security $basesecurity,
            $ajaxinput = false, $castextprocessor = null, $questionvariables = null, $lang = null) {
        if (!is_a($options, 'stack_options')) {
            throw new stack_exception('stack_input: validate_student_response: options not of class stack_options');
        }
        $localoptions = clone $options;
        $localoptions->set_option('simplify', false);
        if ($this->get_extra_option('simp')) {
            $localoptions->set_option('simplify', true);
        }

        if ($ajaxinput) {
            $response = $this->ajax_to_response_array($response);
        }
        $contents = $this->response_to_contents($response);
        $this->rawcontents = $contents;

        // The validation field should always come back through as a single RAW Maxima expression for each input.
        if (array_key_exists($this->name . '_val', $response)) {
            $validator = $response[$this->name . '_val'];
        } else {
            $validator = '';
        }

        if ([] == $contents || (!$this->get_extra_option('allowempty') && $this->is_blank_response($contents))) {
            // Runtime errors may make it appear as if this response is blank, so we put any errors in here.
            $errors = $this->get_errors();
            if ($errors || $errors === []) {
                $errors = implode(' ', $errors);
            }
            return new stack_input_state(self::BLANK, [], '', '', $errors, '', '');
        }

        $secrules = clone $basesecurity;
        // Are we operating in a units context we should ignore?
        if ($this->get_extra_option('nounits', false)) {
            // Logic reversed: nounits means we don't have them.
            $secrules->set_units(false);
        }

        // This method actually validates any CAS strings etc.
        // Modified contents is already an array of things which become individually validated CAS statements.
        // At this sage, $valid records the PHP validation or other non-CAS issues.
        list($valid, $errors, $notes, $answer, $caslines, $inertdisplayform, $ilines)
            = $this->validate_contents($contents, $secrules, $localoptions);
        // Match up lines from the teacher's answer to lines in the student's answer.
        // Send as much of the string to the CAS as possible.
        $validationmethod = $this->get_validation_method();
        $checktype = false;
        if ('checktype' == $validationmethod || 'units' == $validationmethod || 'unitsnegpow' == $validationmethod) {
            $checktype = true;
            $tresponse = $this->maxima_to_response_array($teacheranswer);
            $tcontents = $this->response_to_contents($tresponse);
            list($tvalid, $terrors, $tnotes, $tmodifiedcontents, $tcaslines)
                = $this->validate_contents($tcontents, $secrules, $localoptions);
        } else {
            $tcaslines = [];
        }
        $tvalidator = [];
        foreach ($caslines as $index => $cs) {
            $tvalidator[$index] = null;
            if (array_key_exists($index, $tcaslines)) {
                // We need a CAS expression so Maxima can establish if the student's answer is the "same type".
                $tvalidator[$index] = $tcaslines[$index]->get_inputform();
            }
        }
        $lvarsdisp   = '';
        $note        = '';
        $sessionvars = [];
        // We might need languages in bespoke validation functions defined by the user.
        if ($lang !== null) {
            $sessionvars[] = new stack_secure_loader('%_STACK_LANG:' .
                stack_utils::php_string_to_maxima_string($lang), 'language setting');
        }
        $sessionvars = array_merge($sessionvars, $this->contextsession);

        // Clone answer so we can get the displayed form without the set validation context function, which simplifies.
        $answerd = clone $answer;

        // Validate each line separately, where required and when there is something from the teacher to match up to.
        foreach ($caslines as $index => $cs) {
            // Check the teacher actually has an answer in this slot.
            $ta = '0';
            $trivialta = true;
            if (array_key_exists($index, $tvalidator)) {
                if (!('' == trim($tvalidator[$index] ?? ''))) {
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
                $cs->set_cas_validation_context($this->name.$index, $this->get_parameter('lowestTerms', false),
                        $ta, $ivalidationmethod,
                        $this->get_extra_option('simp', false),  $this->get_extra_option('checkvars', 0));
                $sessionvars[] = $cs;

                // The inert caslines also need to be sent over for LaTeX rendering.
                if ($ilines[$index] !== $cs) {
                    // The basic assumption is that these are valid as long as the original non inert one is.
                    // There are some inputs that will have no inert things and will use the same ones.
                    $sessionvars[] = $ilines[$index];
                }
            }
        }

        // Equiv type checking is done in caslines.
        if ($validationmethod == 'equiv') {
            $validationmethod = 'typeless';
        }
        if ($valid && $answer->get_valid()) {
            $answer->set_cas_validation_context($this->name, $this->get_parameter('lowestTerms', false),
                    $teacheranswer, $validationmethod,
                    $this->get_extra_option('simp', false),  $this->get_extra_option('checkvars', 0));
            // Units inputs change the display in very significant ways.
            if ('units' == $validationmethod || 'unitsnegpow' == $validationmethod) {
                $inertdisplayform->set_cas_validation_context($this->name, $this->get_parameter('lowestTerms', false),
                    $teacheranswer, $validationmethod,
                    $this->get_extra_option('simp', false),  $this->get_extra_option('checkvars', 0));
            }
            // Evaluate both the answer, and the validation context separately.
            // This allows us to display 1/0 type errors without actually evaluating them.
            $sessionvars[] = $answer;
            $sessionvars[] = $answerd;
            if ($answer !== $inertdisplayform) {
                $sessionvars[] = $inertdisplayform;
            }
        }

        // Generate an expression from which we extract the list of variables in the student's answer.
        $raw = 'stack_validate_listofvars('.$this->name.')';
        $lvars = stack_ast_container::make_from_teacher_source($raw, '', $secrules, []);
        if ($lvars->get_valid() && $valid && $answer->get_valid()) {
            $sessionvars[] = $lvars;
        }

        $additionalvars = array_merge($this->extra_option_variables($questionvariables),
                $this->additional_session_variables($caslines, $teacheranswer));
        $sessionvars = array_merge($sessionvars, $additionalvars);

        $session = new stack_cas_session2($sessionvars, $localoptions, 0);

        // If we are dealing with units in this question we apply units texput rules everywhere.
        if ($basesecurity->get_units()) {
            $session->add_statement(stack_ast_container_silent::make_from_teacher_source('stack_unit_si_declare(true)',
                    'automatic unit declaration'), false);
        }

        // Only add errors from the answer if they are generated at run time (e.g. division by zero).
        // Other errors will be created by the specific caslines, and we don't need to confuse with duplicates, or alternatives.
        $answercasvalidation = $answer->get_valid();

        if ($session->get_valid()) {
            $session->instantiate();
        }

        // Pick up any errors generated by Maxima here.
        if ($answercasvalidation && !$answer->get_valid()) {
            $errors = [stack_maxima_translate($answer->get_errors())];
            $valid = false;
        } else {
            if ($lvars->is_correctly_evaluated() && $lvars->get_value() !== '[]') {
                $lvarsdisp = '\( ' . $lvars->get_display() . '\) ';
            }
        }

        // Pick up any new answer notes from the CAS.
        foreach ($answer->get_answernote(false) as $note) {
            $notes[$note] = true;
        }

        // Since $lvars and $answer and the other casstrings are passed by reference, into the $session,
        // we don't need to extract updated values from the instantiated $session explicitly.
        if ('units' == $validationmethod || 'unitsnegpow' == $validationmethod) {
            // The units type changes the display, so we really need the validation method display here.
            list($valid, $errors, $display) = $this->validation_display($answer, $lvars, $caslines, $additionalvars,
                $valid, $errors, $castextprocessor, $inertdisplayform, $ilines);
        } else {
            list($valid, $errors, $display) = $this->validation_display($answerd, $lvars, $caslines, $additionalvars,
                $valid, $errors, $castextprocessor, $inertdisplayform, $ilines);
        }

        // Answers may not contain the ? character.  CAS-strings may, but answers may not.
        // It is very useful for teachers to be able to add in syntax hints.
        // We make sure +- -> #pm# here so that +- can be interpreted at +(-....).
        $params = [
            'inputform' => true,
            'qmchar' => false,
            'pmchar' => 1,
            'nosemicolon' => true,
            'keyless' => true,
            'dealias' => true, // This is needed to stop pi->%pi etc.
            'nounify' => 1,
            'nontuples' => false,
        ];
        $interpretedanswer = $answerd->ast_to_string(null, $params);
        if (!(strpos($interpretedanswer, 'QMCHAR') === false)) {
            $valid = false;
            $errors[] = stack_string('qm_error');
            $notes['qm_error'] = true;
        }

        if ($notes == []) {
            $note = $answer->get_answernote();
        } else {
            $note = implode(' | ', array_keys($notes));
        }

        // Did the CAS throw any errors?  Any feedback will be an error message.
        $feedback = $answer->get_feedback();
        if ($feedback !== '') {
            $errors[] = $feedback;
            $valid = false;
        }

        if ($errors || $errors === []) {
            $errors = trim(implode(' ', $errors));
        }

        if (!$valid) {
            $status = self::INVALID;
        } else if ($this->get_parameter('mustVerify', true) && !($validator === $this->contents_to_maxima($contents))) {
            $status = self::VALID;
        } else {
            $status = self::SCORE;
        }
        // The EMPTYANSWER is not sufficiently robust to determine if we have an empty answer, e.g. matrix inputs.
        if ($this->get_extra_option('allowempty') && $this->is_blank_response($contents)
                && (array_key_exists($this->name, $response) || array_key_exists($this->name.'_sub_0_0', $response))) {
                    return new stack_input_state(self::SCORE, $contents, $interpretedanswer, '', '', '', '');
        }
        $simp = false;
        if ($this->get_extra_option('simp', false) === true) {
            $simp = true;
        }

        $state = new stack_input_state($status, $contents, $interpretedanswer, $display, $errors, $note, $lvarsdisp, $simp);
        return $state;
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

    /*
     * Sort out which filters to apply, based on options to the input.
     * Should be mostly independent of input type.
     */
    protected function validate_contents_filters($basesecurity) {
        $secrules = clone $basesecurity;
        $secrules->set_allowedwords($this->get_parameter('allowWords', ''));
        $secrules->set_forbiddenwords($this->get_parameter('forbidWords', ''));

        $grammarautofixes = $this->get_parameter('grammarAutofixes', 0);

        $filterstoapply = [];

        if ($this->get_parameter('forbidFloats', false)) {
            $filterstoapply[] = '101_no_floats';
        }

        $filterstoapply[] = '150_replace_unicode_letters';

        if (get_class($this) === 'stack_units_input' || get_class($this) === 'stack_numerical_input') {
            $filterstoapply[] = '210_x_used_as_multiplication';
        }

        // The common insert stars rules, that will be forced
        // and if you do not allow insertion of stars then it is invalid.
        $filterstoapply[] = '402_split_prefix_from_common_function_name';
        $filterstoapply[] = '403_split_at_number_letter_boundary';
        $filterstoapply[] = '406_split_implied_variable_names';

        $filterstoapply[] = '502_replace_pm';

        // Replace evaluation groups with tuples.
        $filterstoapply[] = '504_insert_tuples_for_groups';
        // Then ban the rest.
        $filterstoapply[] = '505_no_evaluation_groups';

        // Remove scripts and other related things from string-values.
        $filterstoapply[] = '997_string_security';

        // If stars = 0 then strict, ignore the other strict syntax.
        if ($grammarautofixes === 0) {
            $filterstoapply[] = '999_strict';
        }

        // Insert stars = 1.
        if ($grammarautofixes & self::GRAMMAR_FIX_INSERT_STARS) {
            // The rules are applied anyway, we just check the use of them.
            // If code-tidy issue just negate the test and cut this one out.
            $donothing = true;
        } else if ($grammarautofixes !== 0) {
            $filterstoapply[] = '991_no_fixing_stars';
        }

        // Fix spaces = 2.
        if ($grammarautofixes & self::GRAMMAR_FIX_SPACES) {
            // The rules are applied anyway, we just check the use of them.
            // If code-tidy issue just negate the test and cut this one out.
            $donothing = true;
        } else if ($grammarautofixes !== 0) {
            $filterstoapply[] = '990_no_fixing_spaces';
        }

        // Assume single letter variable names = 4.
        if ($grammarautofixes & self::GRAMMAR_FIX_SINGLE_CHAR) {
            $filterstoapply[] = '410_single_char_vars';
        }

        // Assume single letter variable names = 16.
        if ($grammarautofixes & self::GRAMMAR_FIX_FUNCTIONS) {
            $filterstoapply[] = '441_split_unknown_functions';
        }

        // Consolidate M_1 to M1 and so on.
        if ($this->get_extra_option('consolidatesubscripts', false)) {
            $filterstoapply[] = '420_consolidate_subscripts';
        }

        return [$secrules, $filterstoapply];
    }

    /**
     * This is the basic validation of the student's "answer".
     * This method is only called if the input is not blank.
     *
     * Only a few input methods need to modify this method.
     * For example, Matrix types have two dimensional contents arrays to loop over.
     *
     * @param array $contents the content array of the student's input.
     * @param stack_cas_security $basesecurity declares the variables which must not
     *                                         appear in the student's input.
     * @return array of the validity, errors strings, modified contents, caslines and inertdisplayform.
     */
    protected function validate_contents($contents, $basesecurity, $localoptions) {

        $errors = $this->extra_validation($contents);
        $valid = !$errors;
        $caslines = [];
        $errors = [];
        $notes = [];
        $ilines = [];

        list ($secrules, $filterstoapply) = $this->validate_contents_filters($basesecurity);
        // Separate rules for inert display logic, which wraps floats with certain functions.
        $secrulesd = clone $secrules;
        $secrulesd->add_allowedwords('dispdp,displaysci');

        foreach ($contents as $index => $val) {
            if ($val === null) {
                // One of those things logic nouns hid.
                $val = '';
            }

            // Any student input which is too long is not even parsed.
            if (strlen($val) > $this->maxinputlength) {
                $valid = false;
                $errors[] = stack_string('studentinputtoolong');
                $notes['too_long'] = true;
                $val = '';
            }

            $answer = stack_ast_container::make_from_student_source($val, '', $secrules, $filterstoapply,
                [], 'Root', $this->options->get_option('decimals'));

            $caslines[] = $answer;
            $valid = $valid && $answer->get_valid();
            $errors[] = $answer->get_errors();
            $note = $answer->get_answernote(true);
            if ($note) {
                foreach ($note as $n) {
                    $notes[$n] = true;
                }
            }

            // Construct inert version of that.
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

        }

        // Construct one final "answer" as a single maxima object.
        $answer = $this->caslines_to_answer($caslines, $basesecurity);

        // Same for the inert version.
        $inertdisplayform = $this->caslines_to_answer($ilines, $basesecurity);

        return [$valid, $errors, $notes, $answer, $caslines, $inertdisplayform, $ilines];
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
        return [];
    }

    private function extra_option_variables($questionvariables) {

        $additionalvars = [];

        if ($questionvariables) {
            if ($questionvariables['preamble-qv'] !== null) {
                $additionalvars['preamble-qv'] = new stack_secure_loader($questionvariables['preamble-qv'],
                    'preamble', 'blockexternal');
            }
        }

        if (array_key_exists('floatnum', $this->extraoptions) && $this->extraoptions['floatnum']) {
            $additionalvars['floatnum'] = stack_ast_container::make_from_teacher_source('simp_floatnump('.$this->name.')',
                    '', new stack_cas_security(), []);
        }

        if (array_key_exists('rationalnum', $this->extraoptions) && $this->extraoptions['rationalnum']) {
            $additionalvars['rationalnum'] = stack_ast_container::make_from_teacher_source('rational_numberp('.$this->name.')',
                    '', new stack_cas_security(), []);
        }

        if (array_key_exists('rationalized', $this->extraoptions) && $this->extraoptions['rationalized']) {
            $additionalvars['rationalized'] = stack_ast_container::make_from_teacher_source('rationalized('.$this->name.')',
                    '', new stack_cas_security(), []);
        }

        if (array_key_exists('assume_pos', $this->extraoptions)) {
            $assumepos = 'false';
            if ($this->extraoptions['assume_pos']) {
                $assumepos = 'true';
            }
            $additionalvars['assume_pos'] = stack_ast_container::make_from_teacher_source('assume_pos:'.$assumepos,
                    '', new stack_cas_security(), []);
        }

        if (array_key_exists('assume_real', $this->extraoptions)) {
            $assumereal = 'false';
            if ($this->extraoptions['assume_real']) {
                $assumereal = 'true';
            }
            $additionalvars['assume_real'] = stack_ast_container::make_from_teacher_source('assume_real:'.$assumereal,
                    '', new stack_cas_security(), []);
        }

        if (array_key_exists('calculus', $this->extraoptions)) {
            $calculus = 'false';
            if ($this->extraoptions['calculus']) {
                $calculus = 'true';
            }
            $additionalvars['calculus'] = stack_ast_container::make_from_teacher_source('stack_calculus:'.$calculus,
                    '', new stack_cas_security(), []);;
        }

        if ((array_key_exists('validator', $this->extraoptions) && $this->extraoptions['validator']) ||
            (array_key_exists('feedback', $this->extraoptions) && $this->extraoptions['feedback'])) {
            if ($questionvariables) {
                if ($questionvariables['contextvariables-qv'] !== null) {
                    $additionalvars['contextvariables-qv'] = new stack_secure_loader($questionvariables['contextvariables-qv'],
                        'contextvariables');
                }
                if ($questionvariables['statement-qv'] !== null) {
                    $additionalvars['statement-qv'] = new stack_secure_loader($questionvariables['statement-qv'], 'statement');
                }
            }
            if ($this->extraoptions['validator']) {
                $additionalvars['validator'] = stack_ast_container::make_from_teacher_source(
                    $this->extraoptions['validator'].'('.$this->name.')', '', new stack_cas_security(), []);
            }
            if ($this->extraoptions['feedback']) {
                $additionalvars['feedback'] = stack_ast_container::make_from_teacher_source(
                    $this->extraoptions['feedback'].'('.$this->name.')', '', new stack_cas_security(), []);
            }
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
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors,
                $castextprocessor, $inertdisplayform, $ilines) {

        $display = stack_maxima_format_casstring(htmlentities($this->contents_to_maxima($this->rawcontents), ENT_COMPAT));
        if ($answer->is_correctly_evaluated()) {
            $display = '\[ ' . $inertdisplayform->get_display() . ' \]';
            if ($this->get_parameter('showValidation', 1) == 3) {
                $display = '\( ' . $inertdisplayform->get_display() . ' \)';
            }
        } else {
            $valid = false;
        }

        // The "novars" option is only used by the numerical input type.
        if (array_key_exists('novars', $this->extraoptions)) {
            if (!$valid) {
                $errors[] = stack_string('numericalinputmustnumber');
            }
        }

        // Guard clause at this point.
        if (!$valid) {
            return [$valid, $errors, $display];
        }

        // The "novars" option is only used by the numerical input type.
        if (array_key_exists('novars', $this->extraoptions)) {
            if ($lvars->is_correctly_evaluated() &&  $lvars->get_value() != '[]') {
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

        if ($this->get_extra_option('intnum') && !$answer->is_int()) {
            $valid = false;
            $errors[] = stack_string('numericalinputmustint');
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
            $fltfmt = $answer->get_decimal_digits();
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
                $errors[] = stack_string('ATLowestTerms_not_rat', ['m0' => '\[ '.$rn->get_display().' \]']);
            }
        }

        // We only call our bespoke validator when it's valid so far.
        if ($valid && array_key_exists('validator', $additionalvars)) {
            $rn = $additionalvars['validator'];
            if ($rn->is_correctly_evaluated()) {
                $rn = $rn->get_ast_clone();
                $rn = $rn->statement;
                if ($rn instanceof MP_Boolean && $rn->value == true) {
                    $valid = true;
                } else {
                    if ($rn instanceof MP_String || $rn instanceof MP_List) {
                        $holder = new castext2_placeholder_holder();
                        $msg = castext2_parser_utils::postprocess_mp_parsed($rn, $castextprocessor, $holder);
                        // No filtering here.
                        $msg = $holder->replace($msg);
                        if (trim($msg) !== '') {
                            $valid = false;
                            $errors[] = $msg;
                        }
                    } else {
                        $valid = false;
                        $errors[] = stack_string('inputvalidatorerrcouldnot');
                    }
                }
            } else {
                $valid = false;
                $errors[] = stack_string('inputvalidatorerrcouldnot');
            }
            $rnerr = $additionalvars['validator']->get_errors();
            if (trim($rnerr) != '') {
                $valid = false;
                $errors[] = stack_string('inputvalidatorerrors', ['err' => $rnerr]);
            }
        }

        if ($valid && array_key_exists('feedback', $additionalvars)) {
            $rn = $additionalvars['feedback'];
            if ($rn->is_correctly_evaluated()) {
                $rn = $rn->get_ast_clone();
                $rn = $rn->statement;
                if ($rn instanceof MP_Boolean && $rn->value == true) {
                    $valid = true;
                } else {
                    if ($rn instanceof MP_String || $rn instanceof MP_List) {
                        $holder = new castext2_placeholder_holder();
                        $tmp = castext2_parser_utils::postprocess_mp_parsed($rn, $castextprocessor, $holder);
                        $display .= $holder->replace($tmp);
                    }
                }
            } else {
                $valid = false;
                $errors[] = stack_string('inputvalidatorerrcouldnot');
            }
            $rnerr = $additionalvars['feedback']->get_errors();
            if (trim($rnerr) != '') {
                $valid = false;
                $errors[] = stack_string('inputvalidatorerrors', ['err' => $rnerr]);
            }
        }

        return [$valid, $errors, $display];
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
    abstract public function render(stack_input_state $state, $fieldname, $readonly, $tavalue);

    /*
     * Render any error messages.
     */
    protected function render_error($error) {
        $errors = $this->get_errors();
        if ($errors) {
            $errors = implode(' ', $errors);
        }
        $result = html_writer::tag('p', stack_string_error('ddl_runtime'));
        $result .= html_writer::tag('p', $errors);
        return html_writer::tag('div', $result, ['class' => 'error']);
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
    abstract public function add_to_moodleform_testinput(MoodleQuickForm $mform);

    /**
     * Generate the HTML that gives the results of validating the student's input.
     * @param stack_input_state $state represents the results of the validation.
     * @param string $fieldname the field name to use in the HTML for this input.
     * @param string $lang language of the question.
     * @return string HTML for the validation results for this input.
     */
    public function render_validation(stack_input_state $state, $fieldname, $lang) {
        if ($lang !== null && $lang !== '') {
            $prevlang = force_current_language($lang);
        }
        if (self::BLANK == $state->status) {
            return '';
        }
        if ($this->get_extra_option('allowempty') && $this->is_blank_response($state->contents)) {
            return '';
        }
        if ($this->get_parameter('showValidation', 1) == 0 && self::INVALID != $state->status) {
            return '';
        }
        $feedback  = '';

        $val = $state->contentsdisplayed;

        if (trim($val) !== '<span class="stacksyntaxexample"></span>') {
            // Compact validation.
            if ($this->get_parameter('showValidation', 1) == 3) {
                $feedback .= stack_maths::process_lang_string($val);
            } else {
                $feedback .= html_writer::tag('p', stack_string('studentValidation_yourLastAnswer', $val));
            }
        }

        if ($this->requires_validation() && '' !== $state->contents) {
            $feedback .= html_writer::empty_tag('input', [
                'type' => 'hidden',
                'name' => $fieldname . '_val', 'value' => $this->contents_to_maxima($state->contents),
            ]);
        }

        $feedbackerr = '';
        if (self::INVALID == $state->status) {
            $feedbackerr .= stack_string('studentValidation_invalidAnswer');
        }
        if ($state->errors) {
            $feedbackerr .= $state->errors;
        }
        if ($feedbackerr != '') {
            // Bespoke validation messages might contain maths, which needs to be processed.
            $feedbackerr = stack_ouput_castext($feedbackerr);
            $feedback .= html_writer::tag('div', $feedbackerr, ['class' => 'alert alert-danger stackinputerror']);
        }

        if ($this->get_parameter('showValidation', 1) == 1 && !($state->lvars === '' || $state->lvars === '[]')) {
            $feedback .= $this->tag_listofvariables($state->lvars);
        }
        if ($lang !== null && $lang !== '') {
            force_current_language($prevlang);
        }
        return $feedback;
    }

    /* Allows individual input types to change the way the list of variables is tagged.
     * Used by the units input type.
     */
    protected function tag_listofvariables($vars) {
        return stack_string('studentValidation_listofvariables', $vars);
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
            $val = $response[$this->name];
            if (trim($val) == '' && $this->get_extra_option('allowempty')) {
                $val = 'EMPTYANSWER';
            }
            $contents = [$val];
        }
        return $contents;
    }

    /**
     * Transforms the caslines array into a single casstring representing the student's answer.
     *
     * @param array|string $in
     * @return string
     */
    protected function caslines_to_answer($caslines, $secrules = false) {
        if (array_key_exists(0, $caslines)) {
            return $caslines[0];
        }
        throw new stack_exception('caslines_to_answer could not create the answer.');
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
    public function get_correct_response($value) {
        if (trim($value) == 'EMPTYANSWER' || $value === null) {
            $value = '';
        }
        // TO-DO: refactor this ast creation away.
        $cs = stack_ast_container::make_from_teacher_source($value, '', new stack_cas_security(), []);
        $cs->set_nounify(0);

        $decimal = '.';
        $listsep = ',';
        if ($this->options->get_option('decimals') === ',') {
            $decimal = ',';
            $listsep = ';';
        }
        $params = [
            'checkinggroup' => true,
            'qmchar' => false,
            'pmchar' => 1,
            'nosemicolon' => true,
            'keyless' => true,
            'dealias' => false, // This is needed to stop pi->%pi etc.
            'nounify' => 0,
            'nontuples' => false,
            'decimal' => $decimal,
            'listsep' => $listsep,
        ];
        if ($cs->get_valid()) {
            $value = $cs->ast_to_string(null, $params);
        }
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
        // ISS879 Set language override to null as we should be in the question render here. It's only
        // when we're calling the validation via the webservice that we may need to override.
        $feedback = $this->render_validation($state, $fieldname, null);

        $class = "stackinputfeedback standard";
        $divspan = 'div';
        // Equiv inputs don't have validation divs.
        if ($this->get_validation_method() == 'equiv') {
            $class = "stackinputfeedback equiv";
            $divspan = 'span';
        }
        if ($this->get_parameter('showValidation', 1) == 3) {
            $class = "stackinputfeedback compact";
            $divspan = 'span';
        }

        if (!$feedback) {
            $class .= ' empty';
        }

        $feedback = html_writer::tag($divspan, $feedback,
            ['class' => $class, 'id' => $fieldname.'_val', 'aria-live' => 'assertive']);
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
        return [$this->name => $in];
    }

    /*
     * Return the value of any errors.
     */
    public function get_errors() {
        if ($this->errors === null) {
            return null;
        }
        // Send each error only once.
        $errors = [];
        foreach ($this->errors as $err) {
            $err = trim($err);
            $errors[$err] = true;
        }
        return array_keys($errors);
    }

    /*
     * Provide a summary of the student's response for the Moodle reporting.
     * Notes do something different here.
     */
    public function summarise_response($name, $state, $response) {
        return $name . ': ' . $this->contents_to_maxima($state->contents) . ' [' . $state->status . ']';
    }

    /**
     * Returns the definition of this input as it should appear in an API response
     * @return array
     */
    abstract public function render_api_data($tavalue);

    /**
     * Returns the solution in the format used by the api
     * @param $tavalue
     * @return array
     */
    public function get_api_solution($tavalue) {
        return ['' => $tavalue];
    }

    /**
     * Returns the rendering of the solution
     * @param $tadisplay
     * @return mixed
     */
    public function get_api_solution_render($tadisplay) {
        return $tadisplay;
    }
}
