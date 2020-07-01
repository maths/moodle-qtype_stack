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

/**
 * Stack question definition class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/stack/input/factory.class.php');
require_once(__DIR__ . '/stack/cas/keyval.class.php');
require_once(__DIR__ . '/stack/cas/castext.class.php');
require_once(__DIR__ . '/stack/cas/cassecurity.class.php');
require_once(__DIR__ . '/stack/potentialresponsetree.class.php');
require_once($CFG->dirroot . '/question/behaviour/adaptivemultipart/behaviour.php');
require_once(__DIR__ . '/locallib.php');

/**
 * Represents a Stack question.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question extends question_graded_automatically_with_countback
        implements question_automatically_gradable_with_multiple_parts {

    /**
     * @var string STACK specific: Holds the version of the question when it was last saved.
     */
    public $stackversion;

    /**
     * @var string STACK specific: variables, as authored by the teacher.
     */
    public $questionvariables;

    /**
     * @var string STACK specific: variables, as authored by the teacher.
     */
    public $questionnote;

    /**
     * @var string Any specific feedback for this question. This is displayed
     * in the 'yellow' feedback area of the question. It can contain PRTfeedback
     * tags, but not IEfeedback.
     */
    public $specificfeedback;

    /** @var int one of the FORMAT_... constants */
    public $specificfeedbackformat;

    /** @var string Feedback that is displayed for any PRT that returns a score of 1. */
    public $prtcorrect;

    /** @var int one of the FORMAT_... constants */
    public $prtcorrectformat;

    /** @var string Feedback that is displayed for any PRT that returns a score between 0 and 1. */
    public $prtpartiallycorrect;

    /** @var int one of the FORMAT_... constants */
    public $prtpartiallycorrectformat;

    /** @var string Feedback that is displayed for any PRT that returns a score of 0. */
    public $prtincorrect;

    /** @var int one of the FORMAT_... constants */
    public $prtincorrectformat;

    /** @var string if set, this is used to control the pseudo-random generation of the seed. */
    public $variantsselectionseed;

    /**
     * @var stack_input[] STACK specific: string name as it appears in the question text => stack_input
     */
    public $inputs = array();

    /**
     * @var stack_potentialresponse_tree[] STACK specific: respones tree number => ...
     */
    public $prts = array();

    /**
     * @var stack_options STACK specific: question-level options.
     */
    public $options;

    /**
     * @var int[] of seed values that have been deployed.
     */
    public $deployedseeds;

    /**
     * @var int STACK specific: seeds Maxima's random number generator.
     */
    public $seed = null;

    /**
     * @var stack_cas_session2 STACK specific: session of variables.
     */
    protected $session;

    /**
     * @var stack_ast_container[] STACK specific: the teacher's answers for each input.
     */
    private $tas;

    /**
     * @var stack_cas_security the question level common security
     * settings, i.e. forbidden keys and wether units are in play.
     * Note that the security-object is used to enforce read-only
     * identifiers and therefore wether we are dealing with units
     * is important to it, as obviously one should not redefine units.
     */
    private $security;

    /**
     * @var stack_cas_session2 STACK specific: session of variables.
     */
    protected $questionnoteinstantiated;

    /**
     * @var string instantiated version of questiontext.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $questiontextinstantiated;

    /**
     * @var string instantiated version of specificfeedback.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $specificfeedbackinstantiated;

    /**
     * @var string instantiated version of prtcorrect.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $prtcorrectinstantiated;

    /**
     * @var string instantiated version of prtpartiallycorrect.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $prtpartiallycorrectinstantiated;

    /**
     * @var string instantiated version of prtincorrect.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $prtincorrectinstantiated;

    /**
     * @var array Errors generated at runtime.
     * Any errors are stored as the keys to prevent duplicates.  Values are ignored.
     */
    public $runtimeerrors = array();

    /**
     * The next three fields cache the results of some expensive computations.
     * The chache is only valid for a particular response, so we store the current
     * response, so that we can learn the cached information in the result changes.
     * See {@link validate_cache()}.
     * @var array
     */
    protected $lastresponse = null;

    /**
     * @var bool like $lastresponse, but for the $acceptvalid argument to {@link validate_cache()}.
     */
    protected $lastacceptvalid = null;

    /**
     * @var stack_input_state[] input name => stack_input_state.
     * This caches the results of validate_student_response for $lastresponse.
     */
    protected $inputstates = array();

    /**
     * @var array prt name => result of evaluate_response, if known.
     */
    protected $prtresults = array();

    /**
     * Make sure the cache is valid for the current response. If not, clear it.
     *
     * @param array $response the response.
     * @param bool $acceptvalid if this is true, then we will grade things even
     * if the corresponding inputs are only VALID, and not SCORE.
     */
    protected function validate_cache($response, $acceptvalid = null) {

        if (is_null($this->lastresponse)) {
            $this->lastresponse = $response;
            $this->lastacceptvalid = $acceptvalid;
            return;
        }

        // We really need the PHP === here, as "0.040" == "0.04", even as strings.
        // See https://stackoverflow.com/questions/80646/ for details.
        if ($this->lastresponse === $response && (
                $this->lastacceptvalid === null || $acceptvalid === null || $this->lastacceptvalid === $acceptvalid)) {
            if ($this->lastacceptvalid === null) {
                $this->lastacceptvalid = $acceptvalid;
            }
            return; // Cache is good.
        }

        // Clear the cache.
        $this->lastresponse = $response;
        $this->lastacceptvalid = $acceptvalid;
        $this->inputstates = array();
        $this->prtresults = array();
    }

    /**
     * @return bool do any of the inputs in this question require the student
     *      validate the input.
     */
    protected function any_inputs_require_validation() {
        foreach ($this->inputs as $name => $input) {
            if ($input->requires_validation()) {
                return true;
            }
        }
        return false;
    }

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        if (empty($this->inputs)) {
            return question_engine::make_behaviour('informationitem', $qa, $preferredbehaviour);
        }

        if (empty($this->prts)) {
            return question_engine::make_behaviour('manualgraded', $qa, $preferredbehaviour);
        }

        if (!empty($this->inputs)) {
            foreach ($this->inputs as $input) {
                if ($input->get_extra_option('manualgraded')) {
                    return question_engine::make_behaviour('manualgraded', $qa, $preferredbehaviour);
                }
            }
        }

        if ($preferredbehaviour == 'adaptive' || $preferredbehaviour == 'adaptivenopenalty') {
            return question_engine::make_behaviour('adaptivemultipart', $qa, $preferredbehaviour);
        }

        if ($preferredbehaviour == 'deferredfeedback' && $this->any_inputs_require_validation()) {
            return question_engine::make_behaviour('dfexplicitvaildate', $qa, $preferredbehaviour);
        }

        if ($preferredbehaviour == 'deferredcbm' && $this->any_inputs_require_validation()) {
            return question_engine::make_behaviour('dfcbmexplicitvaildate', $qa, $preferredbehaviour);
        }

        return parent::make_behaviour($qa, $preferredbehaviour);
    }

    public function start_attempt(question_attempt_step $step, $variant) {

        // @codingStandardsIgnoreStart
        // Work out the right seed to use.
        if (!is_null($this->seed)) {
            // This empty if statement is a hack, but if seed has already been set, then use that.
            // This is used by the questiontestrun.php script to allow non-deployed
            // variants to be browsed.
        } else if (!$this->has_random_variants()) {
            // Randomisation not used.
            $this->seed = 1;
        } else if (!empty($this->deployedseeds)) {
            // Question has a fixed number of variants.
            $this->seed = $this->deployedseeds[$variant - 1] + 0;
            // Don't know why this is coming out as a string. + 0 converts to int.
        } else {
            // This question uses completely free randomisation.
            $this->seed = $variant;
        }
        // @codingStandardsIgnoreEnd
        $step->set_qt_var('_seed', $this->seed);

        $this->initialise_question_from_seed();
    }

    /**
     * Once we know the random seed, we can initialise all the other parts of the question.
     */
    public function initialise_question_from_seed() {
        // Build up the question session out of all the bits that need to go into it.
        // 1. question variables.
        $questionvars = new stack_cas_keyval($this->questionvariables, $this->options, $this->seed);
        $session = $questionvars->get_session();
        if ($questionvars->get_errors()) {
            $s = implode(' ', $questionvars->get_errors());
            $s = stack_string('runtimefielderr',
                array('field' => stack_string('questionvariables'), 'err' => $s));
            $this->runtimeerrors[$s] = true;
        }

        // Construct the security object.
        $units = false;
        // Units are in use if there exists even one units*-test or input.
        foreach ($this->inputs as $input) {
            if (is_a($input, 'stack_units_input')) {
                $units = true;
                break;
            }
        }
        if (!$units) {
            foreach ($this->prts as $prt) {
                if ($prt->has_units()) {
                    $units = true;
                    break;
                }
            }
        }
        // If we have units we might as well include the units declaration in the session.
        // To simplify authors work and remove the need to call that long function.
        if ($units) {
            $session->add_statement(stack_ast_container_silent::make_from_teacher_source('stack_unit_si_declare(true)',
                    'automatic unit declaration'), false);
        }

        // Note that at this phase the security object has no "words".
        $usage = $session->get_variable_usage();
        // The student's answer may not contain any of the variable names with which
        // the teacher has defined question variables. Otherwise when it is evaluated
        // in a PRT, the student's answer will take these values.   If the teacher defines
        // 'ta' to be the answer, the student could type in 'ta'!  We forbid this.

        // TODO: shouldn't we also protect variables used in PRT logic? Feedback vars
        // and so on?
        $forbiddenkeys = isset($usage['write']) ? $usage['write'] : array();
        $this->security = new stack_cas_security($units, '', '', $forbiddenkeys);

        // The session to keep. Note we do not need to reinstantiate the teachers answers.
        $sessiontokeep = new stack_cas_session2($session->get_session(), $this->options, $this->seed);

        // 2. correct answer for all inputs.
        foreach ($this->inputs as $name => $input) {
            $cs = stack_ast_container::make_from_teacher_source($input->get_teacher_answer(),
                    '', $this->security);
            $this->tas[$name] = $cs;
            $session->add_statement($cs);
        }

        // 3. CAS bits inside the question text.
        $questiontext = $this->prepare_cas_text($this->questiontext, $session);

        // 4. CAS bits inside the specific feedback.
        $feedbacktext = $this->prepare_cas_text($this->specificfeedback, $session);

        // 5. CAS bits inside the question note.
        $notetext = $this->prepare_cas_text($this->questionnote, $session);

        // 6. The standard PRT feedback.
        $prtcorrect          = $this->prepare_cas_text($this->prtcorrect, $session);
        $prtpartiallycorrect = $this->prepare_cas_text($this->prtpartiallycorrect, $session);
        $prtincorrect        = $this->prepare_cas_text($this->prtincorrect, $session);

        // Now instantiate the session.
        if ($session->get_valid()) {
            $session->instantiate();
        }
        if ($session->get_errors()) {
            // In previous versions we threw an exception here.
            // Upgrade and import stops errors being caught during validation when the question was edited or deployed.
            // This breaks bulk testing in a nasty way.
            $this->runtimeerrors[$session->get_errors(true)] = true;
        }

        // Finally, store only those values really needed for later.
        $this->questiontextinstantiated        = $questiontext->get_display_castext();
        if ($questiontext->get_errors()) {
            $s = stack_string('runtimefielderr',
                array('field' => stack_string('questiontext'), 'err' => $questiontext->get_errors()));
            $this->runtimeerrors[$s] = true;
        }
        $this->specificfeedbackinstantiated    = $feedbacktext->get_display_castext();
        if ($feedbacktext->get_errors()) {
            $s = stack_string('runtimefielderr',
                array('field' => stack_string('specificfeedback'), 'err' => $feedbacktext->get_errors()));
            $this->runtimeerrors[$s] = true;
        }
        $this->questionnoteinstantiated        = $notetext->get_display_castext();
        if ($notetext->get_errors()) {
            $s = stack_string('runtimefielderr',
                array('field' => stack_string('questionnote'), 'err' => $notetext->get_errors()));
            $this->runtimeerrors[$s] = true;
        }
        $this->prtcorrectinstantiated          = $prtcorrect->get_display_castext();
        $this->prtpartiallycorrectinstantiated = $prtpartiallycorrect->get_display_castext();
        $this->prtincorrectinstantiated        = $prtincorrect->get_display_castext();
        $this->session = $sessiontokeep;
        if ($sessiontokeep->get_errors()) {
            $s = stack_string('runtimefielderr',
                array('field' => stack_string('questionvariables'), 'err' => $sessiontokeep->get_errors(true)));
            $this->runtimeerrors[$s] = true;
        }

        // Allow inputs to update themselves based on the model answers.
        $this->adapt_inputs();
        if ($this->runtimeerrors) {
            // It is quite possible that questions will, legitimately, throw some kind of error.
            // For example, if one of the question variables is 1/0.
            // This should not be a show stopper.
            if (trim($this->questiontext) !== '' && trim($this->questiontextinstantiated) === '') {
                // Something has gone wrong here, and the student will be shown nothing.
                $s = html_writer::tag('span', stack_string('runtimeerror'), array('class' => 'stackruntimeerrror'));
                $errmsg = '';
                foreach ($this->runtimeerrors as $key => $val) {
                    $errmsg .= html_writer::tag('li', $key);
                }
                $s .= html_writer::tag('ul', $errmsg);
                $this->questiontextinstantiated .= $s;
            }
        }
    }

    /**
     * Helper method used by initialise_question_from_seed.
     * @param string $text a textual part of the question that is CAS text.
     * @param stack_cas_session2 $session the question's CAS session.
     * @return stack_cas_text the CAS text version of $text.
     */
    protected function prepare_cas_text($text, $session) {
        $castext = new stack_cas_text($text, $session, $this->seed);
        if ($castext->get_errors()) {
            $this->runtimeerrors[$castext->get_errors()] = true;
        }
        return $castext;
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->seed = (int) $step->get_qt_var('_seed');
        $this->initialise_question_from_seed();
    }

    /**
     * Give all the input elements a chance to configure themselves given the
     * teacher's model answers.
     */
    protected function adapt_inputs() {
        foreach ($this->inputs as $name => $input) {
            // TODO: again should we give the whole thing to the input.
            $teacheranswer = '';
            if ($this->tas[$name]->is_correctly_evaluated()) {
                $teacheranswer = $this->tas[$name]->get_value();
            }
            $input->adapt_to_model_answer($teacheranswer);
        }
    }

    /**
     * Get the cattext for a hint, instantiated within the question's session.
     * @param question_hint $hint the hint.
     * @return stack_cas_text the castext.
     */
    public function get_hint_castext(question_hint $hint) {
        $hinttext = new stack_cas_text($hint->hint, $this->session, $this->seed);

        if ($hinttext->get_errors()) {
            $this->runtimeerrors[$hinttext->get_errors()] = true;
        }

        return $hinttext;
    }

    /**
     * Get the cattext for the general feedback, instantiated within the question's session.
     * @return stack_cas_text the castext.
     */
    public function get_generalfeedback_castext() {
        $gftext = new stack_cas_text($this->generalfeedback, $this->session, $this->seed);

        if ($gftext->get_errors()) {
            $this->runtimeerrors[$gftext->get_errors()] = true;
        }

        return $gftext;
    }

    /**
     * We need to make sure the inputs are displayed in the order in which they
     * occur in the question text. This is not necessarily the order in which they
     * are listed in the array $this->inputs.
     */
    public function format_correct_response($qa) {
        $feedback = '';
        $inputs = stack_utils::extract_placeholders($this->questiontextinstantiated, 'input');
        foreach ($inputs as $name) {
            $input = $this->inputs[$name];
            $feedback .= html_writer::tag('p', $input->get_teacher_answer_display($this->tas[$name]->get_dispvalue(),
                    $this->tas[$name]->get_latex()));
        }
        return stack_ouput_castext($feedback);
    }

    public function get_expected_data() {
        $expected = array();
        foreach ($this->inputs as $input) {
            $expected += $input->get_expected_data();
        }
        return $expected;
    }

    public function get_question_summary() {
        if ('' !== $this->questionnoteinstantiated) {
            return $this->questionnoteinstantiated;
        }
        return parent::get_question_summary();
    }

    public function summarise_response(array $response) {
        // Provide seed information on student's version via the normal moodle quiz report.
        $bits = array('Seed: ' . $this->seed);
        foreach ($this->inputs as $name => $input) {
            $state = $this->get_input_state($name, $response);
            if (stack_input::BLANK != $state->status) {
                $bits[] = $input->summarise_response($name, $state, $response);
            }
        }
        // Add in the answer note for this response.
        foreach ($this->prts as $name => $prt) {
            $state = $this->get_prt_result($name, $response, false);
            $note = implode(' | ', $state->answernotes);
            $score = '';
            if (trim($note) == '') {
                $note = '!';
            } else {
                $score = "# = " . $state->score . " | ";
            }
            $bits[] = $name . ": " . $score . $note;
        }
        return implode('; ', $bits);
    }

    // Used in reporting - needs to return an array.
    public function summarise_response_data(array $response) {
        $bits = array();
        foreach ($this->inputs as $name => $input) {
            $state = $this->get_input_state($name, $response);
            $bits[$name] = $state->status;
        }
        return $bits;
    }

    public function get_correct_response() {
        $teacheranswer = array();
        foreach ($this->inputs as $name => $input) {
            $teacheranswer = array_merge($teacheranswer,
                    $input->get_correct_response($this->tas[$name]->get_dispvalue()));
        }
        return $teacheranswer;
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->get_expected_data() as $name => $notused) {
            if (!question_utils::arrays_same_at_key_missing_is_blank(
                    $prevresponse, $newresponse, $name)) {
                return false;
            }
        }
        return true;
    }

    public function is_same_response_for_part($index, array $prevresponse, array $newresponse) {
        $previnput = $this->get_prt_input($index, $prevresponse, true);
        $newinput = $this->get_prt_input($index, $newresponse, true);

        return $this->is_same_prt_input($index, $previnput, $newinput);
    }

    /**
     * Get the results of validating one of the input elements.
     * @param string $name the name of one of the input elements.
     * @param array $response the response, in Maxima format.
     * @param bool $rawinput the response in raw form. Needs converting to Maxima format by the input.
     * @return stack_input_state|string the result of calling validate_student_response() on the input.
     */
    public function get_input_state($name, $response, $rawinput=false) {
        $this->validate_cache($response, null);
        if (array_key_exists($name, $this->inputstates)) {
            return $this->inputstates[$name];
        }

        // TODO: we should probably give the whole ast_container to the input.
        // Direct access to LaTeX and the AST might be handy.
        $teacheranswer = '';
        if (array_key_exists($name, $this->tas)) {
            if ($this->tas[$name]->is_correctly_evaluated()) {
                $teacheranswer = $this->tas[$name]->get_value();
            }
        }
        if (array_key_exists($name, $this->inputs)) {
            $this->inputstates[$name] = $this->inputs[$name]->validate_student_response(
                $response, $this->options, $teacheranswer, $this->security, $rawinput);
            return $this->inputstates[$name];
        }
        return '';
    }

    /**
     * @param array $response the current response being processed.
     * @return boolean whether any of the inputs are blank.
     */
    public function is_any_input_blank(array $response) {
        foreach ($this->inputs as $name => $input) {
            if (stack_input::BLANK == $this->get_input_state($name, $response)->status) {
                return true;
            }
        }
        return false;
    }

    public function is_any_part_invalid(array $response) {
        // Invalid if any input is invalid, ...
        foreach ($this->inputs as $name => $input) {
            if (stack_input::INVALID == $this->get_input_state($name, $response)->status) {
                return true;
            }
        }

        // ... or any PRT gives an error.
        foreach ($this->prts as $index => $prt) {
            $result = $this->get_prt_result($index, $response, false);
            if ($result->errors) {
                return true;
            }
        }

        return false;
    }

    public function is_complete_response(array $response) {

        // If all PRTs are gradable, then the question is complete. (Optional inputs may be blank.)
        foreach ($this->prts as $index => $prt) {
            // Formative PRTs do not contribute to complete responses.
            if (!$prt->is_formative() && !$this->can_execute_prt($prt, $response, false)) {
                return false;
            }
        }

        // If there are no PRTs, then check that all inputs are complete.
        if (!$this->prts) {
            foreach ($this->inputs as $name => $notused) {
                if (stack_input::SCORE != $this->get_input_state($name, $response)->status) {
                    return false;
                }
            }
        }

        return true;
    }

    public function is_gradable_response(array $response) {
        // Manually graded answers are always gradable.
        if (!empty($this->inputs)) {
            foreach ($this->inputs as $input) {
                if ($input->get_extra_option('manualgraded')) {
                    return true;
                }
            }
        }
        // If any PRT is gradable, then we can grade the question.
        $noprts = true;
        foreach ($this->prts as $index => $prt) {
            $noprts = false;
            // Whether formative PRTs can be executed is not relevant to gradability.
            if (!$prt->is_formative() && $this->can_execute_prt($prt, $response, true)) {
                return true;
            }
        }
        // In the case of no PRTs,  questions are in state "is_gradable" if we have
        // at least one input in the "score" or "valid" state.
        if ($noprts) {
            foreach ($this->inputstates as $key => $inputstate) {
                if ($inputstate->status == 'score' || $inputstate->status == 'valid') {
                    return true;
                }
            }
        }
        // Otherwise we are not "is_gradable".
        return false;
    }

    public function get_validation_error(array $response) {
        if ($this->is_any_part_invalid($response)) {
            // There will already be a more specific validation error displayed.
            return '';

        } else if ($this->is_any_input_blank($response)) {
            return stack_string('pleaseananswerallparts');

        } else {
            return stack_string('pleasecheckyourinputs');
        }
    }

    public function grade_response(array $response) {
        $fraction = 0;

        // If we have one or more notes input which needs manual grading, then mark it as needs grading.
        if (!empty($this->inputs)) {
            foreach ($this->inputs as $input) {
                if ($input->get_extra_option('manualgraded')) {
                    return question_state::$needsgrading;
                }
            }
        }
        foreach ($this->prts as $index => $prt) {
            if (!$prt->is_formative()) {
                $results = $this->get_prt_result($index, $response, true);
                $fraction += $results->fraction;
            }
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    protected function is_same_prt_input($index, $prtinput1, $prtinput2) {
        foreach ($this->prts[$index]->get_required_variables(array_keys($this->inputs)) as $name) {
            if (!question_utils::arrays_same_at_key_missing_is_blank($prtinput1, $prtinput2, $name)) {
                return false;
            }
        }
        return true;
    }

    public function get_parts_and_weights() {
        $weights = array();
        foreach ($this->prts as $index => $prt) {
            if (!$prt->is_formative()) {
                $weights[$index] = $prt->get_value();
            }
        }
        return $weights;
    }

    public function grade_parts_that_can_be_graded(array $response, array $lastgradedresponses, $finalsubmit) {
        $partresults = array();

        // At the moment, this method is not written as efficiently as it might
        // be in terms of caching. For now I will be happy it computes the right score.
        // Once we are confident enough, we can try to optimise.

        foreach ($this->prts as $index => $prt) {

            $results = $this->get_prt_result($index, $response, $finalsubmit);
            if ($results->valid === null) {
                continue;
            }

            if ($results->errors) {
                $partresults[$index] = new qbehaviour_adaptivemultipart_part_result($index, null, null, true);
                continue;
            }

            if (array_key_exists($index, $lastgradedresponses)) {
                $lastresponse = $lastgradedresponses[$index];
            } else {
                $lastresponse = array();
            }

            $lastinput = $this->get_prt_input($index, $lastresponse, $finalsubmit);
            $prtinput = $this->get_prt_input($index, $response, $finalsubmit);

            if ($this->is_same_prt_input($index, $lastinput, $prtinput)) {
                continue;
            }

            $partresults[$index] = new qbehaviour_adaptivemultipart_part_result(
                    $index, $results->score, $results->penalty);
        }

        return $partresults;
    }

    public function compute_final_grade($responses, $totaltries) {
        // This method is used by the interactive behaviour to compute the final
        // grade after all the tries are done.

        // At the moment, this method is not written as efficiently as it might
        // be in terms of caching. For now I am happy it computes the right score.
        // Once we are confident enough, we could try switching the nesting
        // of the loops to increase efficiency.

        $fraction = 0;
        foreach ($this->prts as $index => $prt) {
            if ($prt->is_formative()) {
                continue;
            }

            $accumulatedpenalty = 0;
            $lastinput = array();
            $penaltytoapply = null;
            $results = new stdClass();
            $results->fraction = 0;

            foreach ($responses as $response) {
                $prtinput = $this->get_prt_input($index, $response, true);

                if (!$this->is_same_prt_input($index, $lastinput, $prtinput)) {
                    $penaltytoapply = $accumulatedpenalty;
                    $lastinput = $prtinput;
                }

                if ($this->can_execute_prt($this->prts[$index], $response, true)) {
                    $results = $this->prts[$index]->evaluate_response($this->session,
                            $this->options, $prtinput, $this->seed);

                    $accumulatedpenalty += $results->fractionalpenalty;
                }
            }

            $fraction += max($results->fraction - $penaltytoapply, 0);
        }

        return $fraction;
    }

    /**
     * Do we have all the necessary inputs to execute one of the potential response trees?
     * @param stack_potentialresponse_tree $prt the tree in question.
     * @param array $response the response.
     * @param bool $acceptvalid if this is true, then we will grade things even
     *      if the corresponding inputs are only VALID, and not SCORE.
     * @return bool can this PRT be executed for that response.
     */
    protected function has_necessary_prt_inputs(stack_potentialresponse_tree $prt, $response, $acceptvalid) {

        foreach ($prt->get_required_variables(array_keys($this->inputs)) as $name) {
            $status = $this->get_input_state($name, $response)->status;
            if (!(stack_input::SCORE == $status || ($acceptvalid && stack_input::VALID == $status))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Do we have all the necessary inputs to execute one of the potential response trees?
     * @param stack_potentialresponse_tree $prt the tree in question.
     * @param array $response the response.
     * @param bool $acceptvalid if this is true, then we will grade things even
     *      if the corresponding inputs are only VALID, and not SCORE.
     * @return bool can this PRT be executed for that response.
     */
    protected function can_execute_prt(stack_potentialresponse_tree $prt, $response, $acceptvalid) {

        // The only way to find out is to actually try evaluating it. This calls
        // has_necessary_prt_inputs, and then does the computation, which ensures
        // there are no CAS errors.
        $result = $this->get_prt_result($prt->get_name(), $response, $acceptvalid);
        return null !== $result->valid && !$result->errors;
    }

    /**
     * Extract the input for a given PRT from a full response.
     * @param string $index the name of the PRT.
     * @param array $response the full response data.
     * @param bool $acceptvalid if this is true, then we will grade things even
     *      if the corresponding inputs are only VALID, and not SCORE.
     * @return array the input required by that PRT.
     */
    protected function get_prt_input($index, $response, $acceptvalid) {
        if (!array_key_exists($index, $this->prts)) {
            $msg = '"' . $this->name . '" (' . $this->id . ') seed = ' .
                $this->seed . ' and STACK version = ' . $this->stackversion;
            throw new stack_exception ("get_prt_input called for PRT " . $index ." which does not exist in question " . $msg);
        }
        $prt = $this->prts[$index];
        $prtinput = array();
        foreach ($prt->get_required_variables(array_keys($this->inputs)) as $name) {
            $state = $this->get_input_state($name, $response);
            if (stack_input::SCORE == $state->status || ($acceptvalid && stack_input::VALID == $state->status)) {
                $val = $state->contentsmodified;
                if ($state->simp === true) {
                    $val = 'ev(' . $val . ',simp)';
                }
                $prtinput[$name] = $val;
            }
        }

        return $prtinput;
    }

    /**
     * Evaluate a PRT for a particular response.
     * @param string $index the index of the PRT to evaluate.
     * @param array $response the response to process.
     * @param bool $acceptvalid if this is true, then we will grade things even
     *      if the corresponding inputs are only VALID, and not SCORE.
     * @return stack_potentialresponse_tree_state the result from $prt->evaluate_response(),
     *      or a fake state object if the tree cannot be executed.
     */
    public function get_prt_result($index, $response, $acceptvalid) {
        $this->validate_cache($response, $acceptvalid);

        if (array_key_exists($index, $this->prtresults)) {
            return $this->prtresults[$index];
        }

        // We can end up with a null prt at this point if we have question tests for a deleted PRT.
        if (!array_key_exists($index, $this->prts)) {
            // Bail here with an empty state to avoid a later exception which prevents question test editing.
            return new stack_potentialresponse_tree_state(null, null, null, null);
        }
        $prt = $this->prts[$index];

        if (!$this->has_necessary_prt_inputs($prt, $response, $acceptvalid)) {
            $this->prtresults[$index] = new stack_potentialresponse_tree_state(
                    $prt->get_value(), null, null, null);
            return $this->prtresults[$index];
        }

        $prtinput = $this->get_prt_input($index, $response, $acceptvalid);

        $this->prtresults[$index] = $prt->evaluate_response($this->session,
                $this->options, $prtinput, $this->seed);

        return $this->prtresults[$index];
    }

    /**
     * For a possibly nested array, replace all the values with $newvalue.
     * @param string|array $arrayorscalar input array/value.
     * @param mixed $newvalue the new value to set.
     * @return string|array array.
     */
    protected function set_value_in_nested_arrays($arrayorscalar, $newvalue) {
        if (!is_array($arrayorscalar)) {
            return $newvalue;
        }

        $newarray = array();
        foreach ($arrayorscalar as $key => $value) {
            $newarray[$key] = $this->set_value_in_nested_arrays($value, $newvalue);
        }
        return $newarray;
    }

    /**
     * Pollute the question's input state and PRT result caches so that each
     * input appears to contain the name of the input, and each PRT feedback
     * area displays "Feedback from PRT {name}". Naturally, this method should
     * only be used for special purposes, namely the tidyquestion.php script.
     */
    public function setup_fake_feedback_and_input_validation() {
        // Set the cached input stats as if the user types the input name into each box.
        foreach ($this->inputstates as $name => $inputstate) {
            $this->inputstates[$name] = new stack_input_state(
                    $inputstate->status, $this->set_value_in_nested_arrays($inputstate->contents, $name),
                    $inputstate->contentsmodified, $inputstate->contentsdisplayed, $inputstate->errors, $inputstate->note, '');
        }

        // Set the cached prt results as if the feedback for each PRT was
        // "Feedback from PRT {name}".
        foreach ($this->prtresults as $name => $prtresult) {
            $prtresult->_feedback = array();
            $prtresult->add_feedback(stack_string('feedbackfromprtx', $name));
        }
    }

    /**
     * @return bool whether this question uses randomisation.
     */
    public function has_random_variants() {
        return preg_match('~\brand~', $this->questionvariables) || preg_match('~\bmultiselqn~', $this->questionvariables);
    }

    public function get_num_variants() {
        if (!$this->has_random_variants()) {
            // This question does not use randomisation. Only declare one variant.
            return 1;
        }

        if (!empty($this->deployedseeds)) {
            // Fixed number of deployed variants, declare that.
            return count($this->deployedseeds);
        }

        // Random question without fixed variants. We will use the seed from Moodle raw.
        return 1000000;
    }

    public function get_variants_selection_seed() {
        if (!empty($this->variantsselectionseed)) {
            return $this->variantsselectionseed;
        } else {
            return parent::get_variants_selection_seed();
        }
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'qtype_stack' && $filearea == 'specificfeedback') {
            // Specific feedback files only visibile when the feedback is.
            return $options->feedback;

        } else if ($component == 'qtype_stack' && in_array($filearea,
                array('prtcorrect', 'prtpartiallycorrect', 'prtincorrect'))) {
            // This is a bit lax, but anything else is computationally very expensive.
            return $options->feedback;

        } else if ($component == 'qtype_stack' && in_array($filearea,
                array('prtnodefalsefeedback', 'prtnodetruefeedback'))) {
            // This is a bit lax, but anything else is computationally very expensive.
            return $options->feedback;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

    public function get_context() {
        return context::instance_by_id($this->contextid);
    }

    protected function has_question_capability($type) {
        global $USER;
        $context = $this->get_context();
        return has_capability("moodle/question:{$type}all", $context) ||
                ($USER->id == $this->createdby && has_capability("moodle/question:{$type}mine", $context));
    }

    public function user_can_view() {
        return $this->has_question_capability('view');
    }

    public function user_can_edit() {
        return $this->has_question_capability('edit');
    }

    /* Get the values of all variables which have a key.  So, function definitions
     * and assignments are ignored by this method.  Used to display the values of
     * variables used in a question variant.  Beware that some functions have side
     * effects in Maxima, e.g. orderless.  If you use these values you may not get
     * the same results as if you recreate the whole session from $this->questionvariables.
     */
    public function get_question_session_keyval_representation() {
        // We always want the values when this method is called.
        return $this->session->get_keyval_representation(true);
    }

    /**
     * Add all the question variables to a give CAS session. This can be used to
     * initialise that session, so expressions can be evaluated in the context of
     * the question variables.
     * @param stack_cas_session2 $session the CAS session to add the question variables to.
     */
    public function add_question_vars_to_session(stack_cas_session2 $session) {
        // Question vars will always get added to the beginning of whatever session you give.
        $this->session->prepend_to_session($session);
    }

    /**
     * Enable the renderer to access the teacher's answer in the session.
     * TODO: should we give the whole thing?
     * @param string $vname variable name.
     */
    public function get_ta_for_input(string $vname): string {
        if ($this->tas[$vname]->is_correctly_evaluated()) {
            return $this->tas[$vname]->get_value();
        }
        return '';
    }

    public function classify_response(array $response) {
        $classification = array();

        foreach ($this->prts as $index => $prt) {
            if ($prt->is_formative()) {
                continue;
            }

            if (!$this->can_execute_prt($prt, $response, true)) {
                foreach ($prt->get_nodes_summary() as $nodeid => $choices) {
                    $classification[$index . '-' . $nodeid] = question_classified_response::no_response();
                }
                continue;
            }

            $prtinput = $this->get_prt_input($index, $response, true);

            $results = $this->prts[$index]->evaluate_response($this->session,
                    $this->options, $prtinput, $this->seed);

            $answernotes = implode(' | ', $results->answernotes);

            foreach ($prt->get_nodes_summary() as $nodeid => $choices) {
                if (in_array($choices->truenote, $results->answernotes)) {
                    $classification[$index . '-' . $nodeid] = new question_classified_response(
                            $choices->truenote, $answernotes, $results->fraction);

                } else if (in_array($choices->falsenote, $results->answernotes)) {
                    $classification[$index . '-' . $nodeid] = new question_classified_response(
                            $choices->falsenote, $answernotes, $results->fraction);

                } else {
                    $classification[$index . '-' . $nodeid] = question_classified_response::no_response();
                }
            }

        }
        return $classification;
    }

    /**
     * Deploy a variant of this question.
     * @param int $seed the seed to deploy.
     */
    public function deploy_variant($seed) {
        $this->qtype->deploy_variant($this->id, $seed);
    }

    /**
     * Un-deploy a variant of this question.
     * @param int $seed the seed to un-deploy.
     */
    public function undeploy_variant($seed) {
        $this->qtype->undeploy_variant($this->id, $seed);
    }

    /**
     * This function is called by the bulk testing script on upgrade.
     * This checks if questions use features which have changed.
     */
    public function validate_against_stackversion() {
        $errors = array();
        $qfields = array('questiontext', 'questionvariables', 'questionnote', 'specificfeedback', 'generalfeedback');

        $stackversion = (int) $this->stackversion;

        // Things no longer allowed in questions.
        $patterns = array(
             array('pat' => 'addrow', 'ver' => 2018060601, 'alt' => 'rowadd'),
             array('pat' => 'texdecorate', 'ver' => 2018080600),
             array('pat' => 'logbase', 'ver' => 2019031300, 'alt' => 'lg')
        );
        foreach ($patterns as $checkpat) {
            if ($stackversion < $checkpat['ver']) {
                foreach ($qfields as $field) {
                    if (strstr($this->$field, $checkpat['pat'])) {
                        $a = array('pat' => $checkpat['pat'], 'ver' => $checkpat['ver'], 'qfield' => stack_string($field));
                        $err = stack_string('stackversionerror', $a);
                        if (array_key_exists('alt', $checkpat)) {
                            $err .= ' ' . stack_string('stackversionerroralt', $checkpat['alt']);
                        }
                        $errors[] = $err;
                    }
                }
                // Look inside the PRT feedback variables.  Should probably check the feedback as well.
                foreach ($this->prts as $name => $prt) {
                    $kv = $prt->get_feedbackvariables_keyvals();
                    if (strstr($kv, $checkpat['pat'])) {
                        $a = array('pat' => $checkpat['pat'], 'ver' => $checkpat['ver'],
                             'qfield' => stack_string('feedbackvariables') . ' (' . $name . ')');
                        $err = stack_string('stackversionerror', $a);
                        if (array_key_exists('alt', $checkpat)) {
                            $err .= ' ' . stack_string('stackversionerroralt', $checkpat['alt']);
                        }
                        $errors[] = $err;
                    }
                }
            }
        }

        // Mul is no longer supported.
        // We don't need to include a date check here because it is not a change in behaviour.
        foreach ($this->inputs as $inputname => $input) {

            if (!preg_match('/^([a-zA-Z]+|[a-zA-Z]+[0-9a-zA-Z_]*[0-9a-zA-Z]+)$/', $inputname)) {
                $errors[] = stack_string('inputnameform', $inputname);
            }

            $options = $input->get_parameter('options');
            if (trim($options) !== '') {
                $options = explode(',', $options);
                foreach ($options as $opt) {
                    $opt = strtolower(trim($opt));
                    if ($opt === 'mul') {
                        $errors[] = stack_string('stackversionmulerror');
                    }
                }
            }
        }

        // Look for RexExp answer test which is no longer supported.
        foreach ($this->prts as $name => $prt) {
            if (array_key_exists('RegExp', $prt->get_answertests())) {
                $errors[] = stack_string('stackversionregexp');
            }
        }

        return implode(' ', $errors);
    }

    /*
     * Used for unit testing of question states.
     */
    public function get_session() {
        return $this->session;
    }
}
