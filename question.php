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
require_once(__DIR__ . '/stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/stack/cas/cassecurity.class.php');
require_once($CFG->dirroot . '/question/behaviour/adaptivemultipart/behaviour.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/questiontype.php');
require_once(__DIR__ . '/stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/stack/prt.class.php');
require_once(__DIR__ . '/stack/prt.evaluatable.class.php');
require_once(__DIR__ . '/vle_specific.php');

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
     * @var stack_potentialresponse_tree_lite[] STACK specific: respones tree number => ...
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
     * @var castext2_evaluatable STACK specific: variant specifying castext fragment.
     */
    protected $questionnoteinstantiated = null;

    /**
     * @var castext2_evaluatable instantiated version of questiontext.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $questiontextinstantiated;

    /**
     * @var castext2_evaluatable instantiated version of specificfeedback.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $specificfeedbackinstantiated;

    /**
     * @var castext2_evaluatable instantiated version of generalfeedback.
     * Init depends of config.
     */
    private $generalfeedbackinstantiated = null;

    /**
     * @var castext2_evaluatable instantiated version of prtcorrect.
     * Initialised in start_attempt / apply_attempt_state.
     * NOTE: used in rederer.php:standard_prt_feedback() in an uncommon way.
     */
    public $prtcorrectinstantiated;

    /**
     * @var castext2_evaluatable instantiated version of prtpartiallycorrect.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $prtpartiallycorrectinstantiated;

    /**
     * @var castext2_evaluatable instantiated version of prtincorrect.
     * Initialised in start_attempt / apply_attempt_state.
     */
    public $prtincorrectinstantiated;

    /**
     * @var castext2_processor an accesspoint to the question attempt for
     * the castext2 post-processing logic for pluginfile url-writing.
     */
    public $castextprocessor = null;

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
     * @var array set of expensive to evaluate but static things.
     */
    public $compiledcache = [];

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
        // We can detect a logically faulty question by checking if the cache can
        // return anything if it can't then we can simply skip to the output of errors.
        if ($this->get_cached('units') !== null) {
            // Build up the question session out of all the bits that need to go into it.
            // 1. question variables.
            $session = new stack_cas_session2([], $this->options, $this->seed);

            // Construct the security object. But first units declaration into the session.
            $units = (boolean) $this->get_cached('units');

            // If we are using localisation we should tell the CAS side logic about it.
            // For castext rendering and other tasks.
            if (count($this->get_cached('langs')) > 0) {
                $ml = new stack_multilang();
                $selected = $ml->pick_lang($this->get_cached('langs'));
                $session->add_statement(new stack_secure_loader('%_STACK_LANG:' .
                    stack_utils::php_string_to_maxima_string($selected), 'language setting'), false);
            }

            // If we have units we might as well include the units declaration in the session.
            // To simplify authors work and remove the need to call that long function.
            // TODO: Maybe add this to the preable to save lines, but for now documented here.
            if ($units) {
                $session->add_statement(new stack_secure_loader('stack_unit_si_declare(true)',
                        'automatic unit declaration'), false);
            }

            if ($this->get_cached('preamble-qv') !== null) {
                $session->add_statement(new stack_secure_loader($this->get_cached('preamble-qv'), 'preamble'));
            }
            // Context variables should be first.
            if ($this->get_cached('contextvariables-qv') !== null) {
                $session->add_statement(new stack_secure_loader($this->get_cached('contextvariables-qv'), '/qv'));
            }
            if ($this->get_cached('statement-qv') !== null) {
                $session->add_statement(new stack_secure_loader($this->get_cached('statement-qv'), '/qv'));
            }

            // Note that at this phase the security object has no "words".
            // The student's answer may not contain any of the variable names with which
            // the teacher has defined question variables. Otherwise when it is evaluated
            // in a PRT, the student's answer will take these values.   If the teacher defines
            // 'ta' to be the answer, the student could type in 'ta'!  We forbid this.

            // TODO: shouldn't we also protect variables used in PRT logic? Feedback vars
            // and so on?
            $forbiddenkeys = array();
            if ($this->get_cached('forbiddenkeys') !== null) {
                $forbiddenkeys = $this->get_cached('forbiddenkeys');
            }
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

            // 3.0 setup common CASText2 staticreplacer.
            $static = new castext2_static_replacer($this->get_cached('static-castext-strings'));

            // 3. CAS bits inside the question text.
            $questiontext = castext2_evaluatable::make_from_compiled($this->get_cached('castext-qt'), '/qt', $static);
            if ($questiontext->requires_evaluation()) {
                $session->add_statement($questiontext);
            }

            // 4. CAS bits inside the specific feedback.
            $feedbacktext = castext2_evaluatable::make_from_compiled($this->get_cached('castext-sf'), '/sf', $static);
            if ($feedbacktext->requires_evaluation()) {
                $session->add_statement($feedbacktext);
            }

            // Add the context to the security, needs some unpacking of the cached.
            if ($this->get_cached('security-context') === null) {
                $this->security->set_context([]);
            } else {
                $this->security->set_context($this->get_cached('security-context'));
            }

            // The session to keep. Note we do not need to reinstantiate the teachers answers.
            $sessiontokeep = new stack_cas_session2($session->get_session(), $this->options, $this->seed);

            // 5. CAS bits inside the question note.
            $notetext = castext2_evaluatable::make_from_compiled($this->get_cached('castext-qn'), '/qn', $static);
            if ($notetext->requires_evaluation()) {
                $session->add_statement($notetext);
            }

            // 6. The standard PRT feedback.
            $prtcorrect          = castext2_evaluatable::make_from_compiled($this->get_cached('castext-prt-c'),
                '/pc', $static);
            $prtpartiallycorrect = castext2_evaluatable::make_from_compiled($this->get_cached('castext-prt-pc'),
                '/pp', $static);
            $prtincorrect        = castext2_evaluatable::make_from_compiled($this->get_cached('castext-prt-ic'),
                '/pi', $static);
            if ($prtcorrect->requires_evaluation()) {
                $session->add_statement($prtcorrect);
            }
            if ($prtpartiallycorrect->requires_evaluation()) {
                $session->add_statement($prtpartiallycorrect);
            }
            if ($prtincorrect->requires_evaluation()) {
                $session->add_statement($prtincorrect);
            }

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
            $this->questiontextinstantiated        = $questiontext;
            if ($questiontext->get_errors()) {
                $s = stack_string('runtimefielderr',
                    array('field' => stack_string('questiontext'), 'err' => $questiontext->get_errors()));
                $this->runtimeerrors[$s] = true;
            }
            $this->specificfeedbackinstantiated    = $feedbacktext;
            if ($feedbacktext->get_errors()) {
                $s = stack_string('runtimefielderr',
                    array('field' => stack_string('specificfeedback'), 'err' => $feedbacktext->get_errors()));
                $this->runtimeerrors[$s] = true;
            }
            $this->questionnoteinstantiated        = $notetext;
            if ($notetext->get_errors()) {
                $s = stack_string('runtimefielderr',
                    array('field' => stack_string('questionnote'), 'err' => $notetext->get_errors()));
                $this->runtimeerrors[$s] = true;
            }
            $this->prtcorrectinstantiated          = $prtcorrect;
            $this->prtpartiallycorrectinstantiated = $prtpartiallycorrect;
            $this->prtincorrectinstantiated        = $prtincorrect;
            $this->session = $sessiontokeep;
            if ($sessiontokeep->get_errors()) {
                $s = stack_string('runtimefielderr',
                    array('field' => stack_string('questionvariables'), 'err' => $sessiontokeep->get_errors(true)));
                $this->runtimeerrors[$s] = true;
            }

            // Allow inputs to update themselves based on the model answers.
            $this->adapt_inputs();
        }

        if ($this->runtimeerrors) {
            // It is quite possible that questions will, legitimately, throw some kind of error.
            // For example, if one of the question variables is 1/0.
            // This should not be a show stopper.
            // Something has gone wrong here, and the student will be shown nothing.
            $s = html_writer::tag('span', stack_string('runtimeerror'), array('class' => 'stackruntimeerrror'));
            $errmsg = '';
            foreach ($this->runtimeerrors as $key => $val) {
                $errmsg .= html_writer::tag('li', $key);
            }
            $s .= html_writer::tag('ul', $errmsg);
            // So we have this logic where a raw string needs to turn to a CASText2 object.
            // As we do not know what it contains we escape it.
            $this->questiontextinstantiated = castext2_evaluatable::make_from_source('[[escape]]' . $s . '[[/escape]]', '/qt');
            // It is a stateic string and by calling this we make it look like it was evaluated.
            $this->questiontextinstantiated->requires_evaluation();

            // Do some setup for the features that do not work.
            $this->security = new stack_cas_security();
            $this->tas = [];
            $this->session = new stack_cas_session2([]);
        }
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
            if ($this->get_cached('contextvariables-qv') !== null) {
                $input->add_contextsession(new stack_secure_loader($this->get_cached('contextvariables-qv'), '/qv'));
            }
        }
    }

    /**
     * Get the cattext for a hint, instantiated within the question's session.
     * @param question_hint $hint the hint.
     * @return stack_cas_text the castext.
     */
    public function get_hint_castext(question_hint $hint) {
        // TODO: These are not currently cached as compiled fragments, maybe they should be.

        $hinttext = castext2_evaluatable::make_from_source($hint->hint, 'hint');

        $session = null;
        if ($this->session === null) {
            $session = new stack_cas_session2([], $this->options, $this->seed);
        } else {
            $session = new stack_cas_session2($this->session->get_session(), $this->options, $this->seed);
        }
        $session->add_statement($hinttext);
        $session->instantiate();

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
        // Could be that this is instantiated already.
        if ($this->generalfeedbackinstantiated !== null) {
            return $this->generalfeedbackinstantiated;
        }
        // We can have a failed question.
        if ($this->get_cached('castext-gf') === null) {
            $ct = castext2_evaluatable::make_from_compiled('"Broken question."', '/gf',
                new castext2_static_replacer([])); // This mainly for the bulk-test script.
            $ct->requires_evaluation(); // Makes it as if it were evaluated.
            return $ct;
        }

        $this->generalfeedbackinstantiated = castext2_evaluatable::make_from_compiled($this->get_cached('castext-gf'),
            '/gf', new castext2_static_replacer($this->get_cached('static-castext-strings')));
        // Might not require any evaluation anyway.
        if (!$this->generalfeedbackinstantiated->requires_evaluation()) {
            return $this->generalfeedbackinstantiated;
        }

        // Init a session with question-variables ant the related details.
        $session = new stack_cas_session2([], $this->options, $this->seed);
        if ($this->get_cached('preamble-qv') !== null) {
            $session->add_statement(new stack_secure_loader($this->get_cached('preamble-qv'), 'preamble'));
        }
        if ($this->get_cached('contextvariables-qv') !== null) {
            $session->add_statement(new stack_secure_loader($this->get_cached('contextvariables-qv'), '/qv'));
        }
        if ($this->get_cached('statement-qv') !== null) {
            $session->add_statement(new stack_secure_loader($this->get_cached('statement-qv'), '/qv'));
        }

        // Then add the general-feedback code.
        $session->add_statement($this->generalfeedbackinstantiated);
        $session->instantiate();

        if ($this->generalfeedbackinstantiated->get_errors()) {
            $this->runtimeerrors[$this->generalfeedbackinstantiated->get_errors()] = true;
        }

        return $this->generalfeedbackinstantiated;
    }

    /**
     * We need to make sure the inputs are displayed in the order in which they
     * occur in the question text. This is not necessarily the order in which they
     * are listed in the array $this->inputs.
     */
    public function format_correct_response($qa) {
        $feedback = '';
        $inputs = stack_utils::extract_placeholders($this->questiontextinstantiated->get_rendered(), 'input');
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
        if ($this->questionnoteinstantiated !== null &&
            '' !== $this->questionnoteinstantiated->get_rendered()) {
            return $this->questionnoteinstantiated->get_rendered();
        }
        return stack_string('questionnote_missing');
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
            $note = implode(' | ', array_map('trim', $state->get_answernotes()));
            $score = '';
            if (trim($note) == '') {
                $note = '!';
            } else {
                $score = "# = " . $state->get_score();
                if ($prt->is_formative()) {
                    $score .= ' [formative]';
                }
                $score .= " | ";
            }
            if ($state->get_errors()) {
                $score = '[RUNTIME_ERROR] ' . $score . implode("|", $state->get_errors());
            }
            if ($state->get_fverrors()) {
                $score = '[RUNTIME_FV_ERROR] ' . $score . implode("|", $state->get_fverrors()) . ' | ';
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
        if ($this->runtimeerrors || $this->get_cached('units') === null) {
            return [];
        }
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
        foreach ($this->prts as $name => $prt) {
            $result = $this->get_prt_result($name, $response, false);
            if ($result->get_errors()) {
                return true;
            }
        }

        return false;
    }

    public function is_complete_response(array $response) {

        // If all PRTs are gradable, then the question is complete. Optional inputs may be blank.
        foreach ($this->prts as $prt) {
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
        foreach ($this->prts as $name => $prt) {
            if (!$prt->is_formative()) {
                $results = $this->get_prt_result($name, $response, true);
                $fraction += $results->get_fraction();
            }
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    protected function is_same_prt_input($index, $prtinput1, $prtinput2) {
        foreach ($this->get_cached('required')[$this->prts[$index]->get_name()] as $name => $ignore) {
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
            // Some optimisation now hidden behind this, it will eval all PRTs
            // of the question for this input.
            $results = $this->get_prt_result($index, $response, $finalsubmit);
            if (!$results->is_evaluated()) {
                continue;
            }

            if (!$results->get_valid()) {
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
                    $index, $results->get_score(), $results->get_penalty());
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

        // TODO: switch the nesting, now that the eval is by response and not by PRT.
        // Current CAS-cache helps but it is wasted cycles to go to it so many times.
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

            $frac = 0;
            foreach ($responses as $response) {
                $prtinput = $this->get_prt_input($index, $response, true);

                if (!$this->is_same_prt_input($index, $lastinput, $prtinput)) {
                    $penaltytoapply = $accumulatedpenalty;
                    $lastinput = $prtinput;
                }

                if ($this->can_execute_prt($this->prts[$index], $response, true)) {
                    $results = $this->get_prt_result($index, $response, true);

                    $accumulatedpenalty += $results->get_fractionalpenalty();
                    $frac = $results->get_fraction();
                }
            }

            $fraction += max($frac - $penaltytoapply, 0);
        }

        return $fraction;
    }

    /**
     * Do we have all the necessary inputs to execute one of the potential response trees?
     * @param stack_potentialresponse_tree_lite $prt the tree in question.
     * @param array $response the response.
     * @param bool $acceptvalid if this is true, then we will grade things even
     *      if the corresponding inputs are only VALID, and not SCORE.
     * @return bool can this PRT be executed for that response.
     */
    protected function has_necessary_prt_inputs(stack_potentialresponse_tree_lite $prt, $response, $acceptvalid) {

        // Some kind of time-time error in the question, so bail here.
        if ($this->get_cached('required') === null) {
            return false;
        }

        foreach ($this->get_cached('required')[$prt->get_name()] as $name => $ignore) {
            $status = $this->get_input_state($name, $response)->status;
            if (!(stack_input::SCORE == $status || ($acceptvalid && stack_input::VALID == $status))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Do we have all the necessary inputs to execute one of the potential response trees?
     * @param stack_potentialresponse_tree_lite $prt the tree in question.
     * @param array $response the response.
     * @param bool $acceptvalid if this is true, then we will grade things even
     *      if the corresponding inputs are only VALID, and not SCORE.
     * @return bool can this PRT be executed for that response.
     */
    protected function can_execute_prt(stack_potentialresponse_tree_lite $prt, $response, $acceptvalid) {

        // The only way to find out is to actually try evaluating it. This calls
        // has_necessary_prt_inputs, and then does the computation, which ensures
        // there are no CAS errors.
        $result = $this->get_prt_result($prt->get_name(), $response, $acceptvalid);

        return $result->is_evaluated() && !$result->get_errors();
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
        foreach ($this->get_cached('required')[$prt->get_name()] as $name => $ignore) {
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
     * @return prt_evaluatable the result from traversing the prt.
     */
    public function get_prt_result($index, $response, $acceptvalid) {
        $this->validate_cache($response, $acceptvalid);

        if (array_key_exists($index, $this->prtresults)) {
            return $this->prtresults[$index];
        }

        // We can end up with a null prt at this point if we have question tests for a deleted PRT.
        // Alternatively we have a question that could not be compiled.
        if (!array_key_exists($index, $this->prts) || $this->get_cached('units') === null) {
            // Bail here with an empty state to avoid a later exception which prevents question test editing.
            return new prt_evaluatable('prt_' . $index . '(???)', 1, new castext2_static_replacer([]), array());
        }

        // If we do not have inputs for this then no need to continue.
        if (!$this->has_necessary_prt_inputs($this->prts[$index], $response, $acceptvalid)) {
            $this->prtresults[$index] = new prt_evaluatable($this->get_cached('prt-signature')[$index],
                $this->prts[$index]->get_value(),
                new castext2_static_replacer($this->get_cached('static-castext-strings')),
                $this->get_cached('prt-trace')[$index]);
            return $this->prtresults[$index];
        }

        // First figure out which PRTs can be called.
        $prts = [];
        $inputs = [];
        foreach ($this->prts as $name => $prt) {
            if ($this->has_necessary_prt_inputs($prt, $response, $acceptvalid)) {
                $prts[$name] = $prt;
                $inputs += $this->get_prt_input($name, $response, $acceptvalid);
            }
        }

        // So now we build a session to evaluate all the PRTs.
        $session = new stack_cas_session2([], $this->options, $this->seed);

        // Construct the security object. But first units declaration into the session.
        $units = (boolean) $this->get_cached('units');

        // If we have units we might as well include the units declaration in the session.
        // To simplify authors work and remove the need to call that long function.
        // TODO: Maybe add this to the preable to save lines, but for now documented here.
        if ($units) {
            $session->add_statement(new stack_secure_loader('stack_unit_si_declare(true)',
                    'automatic unit declaration'), false);
        }

        if ($this->get_cached('preamble-qv') !== null) {
            $session->add_statement(new stack_secure_loader($this->get_cached('preamble-qv'), 'preamble'));
        }
        // Add preamble from PRTs as well.
        foreach ($this->get_cached('prt-preamble') as $name => $stmt) {
            if (isset($prts[$name])) {
                $session->add_statement(new stack_secure_loader($stmt, 'preamble PRT: ' . $name));
            }
        }

        // Context variables should be first.
        if ($this->get_cached('contextvariables-qv') !== null) {
            $session->add_statement(new stack_secure_loader($this->get_cached('contextvariables-qv'), '/qv'));
        }
        // Add contextvars from PRTs as well.
        foreach ($this->get_cached('prt-contextvariables') as $name => $stmt) {
            if (isset($prts[$name])) {
                $session->add_statement(new stack_secure_loader($stmt, 'contextvariables PRT: ' . $name));
            }
        }

        if ($this->get_cached('statement-qv') !== null) {
            $session->add_statement(new stack_secure_loader($this->get_cached('statement-qv'), '/qv'));
        }

        // Then the definitions of the PRT-functions. Note not just statements for a reason.
        foreach ($this->get_cached('prt-definition') as $name => $stmt) {
            if (isset($prts[$name])) {
                $session->add_statement(new stack_secure_loader($stmt, 'definition PRT: ' . $name));
            }
        }

        // Suppress simplification of raw inputs.
        $session->add_statement(new stack_secure_loader('simp:false', 'input-simplification'));

        // Now push in the input values and the new _INPUT_STRING.
        // Note these have been validated in the input system.
        $is = '_INPUT_STRING:["stack_map"';
        foreach ($inputs as $key => $value) {
            $session->add_statement(new stack_secure_loader($key . ':' . $value, 'i/' .
                array_search($key, array_keys($this->inputs)) . '/s'));
            $is .= ',[' . stack_utils::php_string_to_maxima_string($key) . ',';
            if (strpos($value, 'ev(') === 0) { // Unpack the value if we have simp...
                $is .= stack_utils::php_string_to_maxima_string(mb_substr($value, 3, -6)) . ']';
            } else {
                $is .= stack_utils::php_string_to_maxima_string($value) . ']';
            }
        }
        $is .= ']';
        $session->add_statement(new stack_secure_loader($is, 'input-strings'));

        // Generate, cache and instantiate the results.
        foreach ($this->prts as $name => $prt) {
            // Put the input string map in the trace.
            $trace = array_merge(array($is . '$', '/* ------------------- */'), $this->get_cached('prt-trace')[$name]);
            $p = new prt_evaluatable($this->get_cached('prt-signature')[$name],
                $prt->get_value(), new castext2_static_replacer($this->get_cached('static-castext-strings')),
                $trace);
            if (isset($prts[$name])) {
                // Always make sure it gets called with simp:false.
                $session->add_statement(new stack_secure_loader('simp:false', 'prt-simplification'));
                $session->add_statement($p);
            }
            $this->prtresults[$name] = $p;
        }
        $session->instantiate();
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
            $prtresult->override_feedback(stack_string('feedbackfromprtx', $name));
        }
    }

    /**
     * @return bool Whether this question uses randomisation.
     */
    public function has_random_variants() {
        return $this->random_variants_check($this->questionvariables);
    }

    /**
     * @param string Input text (raw keyvals) to check for random functions.
     * @return bool Actual test of whether text uses randomisation.
     */
    public static function random_variants_check($text) {
        return preg_match('~\brand~', $text) || preg_match('~\bmultiselqn~', $text);
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

    /* Get the values of all variables which have a key.  So, function definitions
     * and assignments are ignored by this method.  Used to display the values of
     * variables used in a question variant.  Beware that some functions have side
     * effects in Maxima, e.g. orderless.  If you use these values you may not get
     * the same results as if you recreate the whole session from $this->questionvariables.
     */
    public function get_question_session_keyval_representation() {
        // After the cached compilation update the session no longer returns these.
        // So we will build another session just for this.
        // First we replace the compiled statements with the raw keyval statements.
        $tmp = $this->session->get_session();
        $tmp = array_filter($tmp, function($v) {
            return method_exists($v, 'is_correctly_evaluated');
        });
        $kv = new stack_cas_keyval($this->questionvariables, $this->options, $this->seed);
        $kv->get_valid();
        $session = $kv->get_session();
        $session->add_statements($tmp);
        $session->get_valid();
        if ($session->get_valid()) {
            $session->instantiate();
        }

        // We always want the values when this method is called.
        return $session->get_keyval_representation(true);
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
        if (isset($this->tas[$vname]) && $this->tas[$vname]->is_correctly_evaluated()) {
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

            $results = $this->get_prt_result($index, $response, true);

            $answernotes = implode(' | ', array_map('trim', $results->get_answernotes()));

            foreach ($prt->get_nodes_summary() as $nodeid => $choices) {
                if (in_array($choices->trueanswernote, $results->get_answernotes())) {
                    $classification[$index . '-' . $nodeid] = new question_classified_response(
                            $choices->trueanswernote, $answernotes, $results->get_fraction());

                } else if (in_array($choices->falseanswernote, $results->get_answernotes())) {
                    $classification[$index . '-' . $nodeid] = new question_classified_response(
                            $choices->falseanswernote, $answernotes, $results->get_fraction());

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

        // Add in any warnings.
        $errors = array_merge($errors, $this->validate_warnings(true));

        return implode(' ', $errors);
    }

    /*
     * Unfortunately, "errors" stop a question being saved.  So, we have a parallel warning mechanism.
     * Warnings need to be addressed but should not stop a question being saved.
     */
    public function validate_warnings($errors = false) {

        $warnings = array();

        // 1. Answer tests which require raw inputs actually have SAns a calculated value.
        foreach ($this->prts as $prt) {
            foreach ($prt->get_raw_sans_used() as $key => $sans) {
                if (!array_key_exists(trim($sans), $this->inputs)) {
                    $warnings[] = stack_string_error('AT_raw_sans_needed', array('prt' => $key));
                }
            }
            foreach ($prt->get_raw_arguments_used() as $name => $ans) {
                $tvalue = trim($ans);
                $tvalue = substr($tvalue, strlen($tvalue) - 1);
                if ($tvalue === ';') {
                    $warnings[] = stack_string('nosemicolon') . ':' . $name;
                }
            }
        }

        // 2. Check alt-text exists.
        // Reminder: previous approach in Oct 2021 tried to use libxml_use_internal_errors, but this was a dead end.

        $tocheck = array();
        $text = '';
        if ($this->questiontextinstantiated !== null) {
            $text = trim($this->questiontextinstantiated->get_rendered());
        }
        if ($text !== '') {
            $tocheck[stack_string('questiontext')] = $text;
        }
        $ct = $this->get_generalfeedback_castext();
        $text = trim($ct->get_rendered($this->castextprocessor));
        if ($text !== '') {
            $tocheck[stack_string('generalfeedback')] = $text;
        }
        // This is a compromise.  We concatinate all nodes and we don't instantiate this!
        foreach ($this->prts as $prt) {
            $text = trim($prt->get_feedback_test());
            if ($text !== '') {
                $tocheck[$prt->get_name()] = $text;
            }
        }

        foreach ($tocheck as $field => $text) {
            // Replace unprotected & symbols, which happens a lot inside LaTeX equations.
            $text = preg_replace("/&(?!\S+;)/", "&amp;", $text);

            $missingalt = stack_utils::count_missing_alttext($text);
            if ($missingalt > 0) {
                $warnings[] = stack_string_error('alttextmissing', array('field' => $field, 'num' => $missingalt));
            }
        }

        // 3. Language warning checks.
        // Put language warning checks last (see guard clause below).
        // Check multi-language versions all have the same languages.
        $ml = new stack_multilang();
        $qlangs = $ml->languages_used($this->questiontext);
        asort($qlangs);
        if ($qlangs != array() && !$errors) {
            $warnings['questiontext'] = stack_string('questiontextlanguages', implode(', ', $qlangs));
        }

        // Language tags don't exist.
        if ($qlangs == array()) {
            return $warnings;
        }

        $problems = false;
        $missinglang = array();
        $extralang = array();
        $fields = array('specificfeedback', 'generalfeedback');
        foreach ($fields as $field) {
            $text = $this->$field;
            // Strip out feedback tags (to help non-trivial content check)..
            foreach ($this->prts as $prt) {
                $text = str_replace('[[feedback:' . $prt->get_name() . ']]', '', $text);
            }

            if ($ml->non_trivial_content_for_check($text)) {

                $langs = $ml->languages_used($text);
                foreach ($qlangs as $expectedlang) {
                    if (!in_array($expectedlang, $langs)) {
                        $problems = true;
                        $missinglang[$expectedlang][] = stack_string($field);
                    }
                }
                foreach ($langs as $lang) {
                    if (!in_array($lang, $qlangs)) {
                        $problems = true;
                        $extralang[stack_string($field)][] = $lang;
                    }
                }

            }
        }

        foreach ($this->prts as $prt) {
            foreach ($prt->get_feedback_languages() as $nodes) {
                // The nodekey is really the answernote from one branch of the node.
                // No actually it is not in the new PRT-system, it's just 'true' or 'false'.
                foreach ($nodes as $nodekey => $langs) {
                    foreach ($qlangs as $expectedlang) {
                        if (!in_array($expectedlang, $langs)) {
                            $problems = true;
                            $missinglang[$expectedlang][] = $nodekey;
                        }
                    }
                    foreach ($langs as $lang) {
                        if (!in_array($lang, $qlangs)) {
                            $problems = true;
                            $extralang[$nodekey][] = $lang;
                        }
                    }
                }
            }
        }

        if ($problems) {
            $warnings[] = stack_string_error('languageproblemsexist');
        }
        foreach ($missinglang as $lang => $missing) {
            $warnings[] = stack_string('languageproblemsmissing',
                array('lang' => $lang, 'missing' => implode(', ', $missing)));
        }
        foreach ($extralang as $field => $langs) {
            $warnings[] = stack_string('languageproblemsextra',
                array('field' => $field, 'langs' => implode(', ', $langs)));
        }
        return $warnings;

    }
    /**
     * Cache management.
     *
     * Returns named items from the cache and rebuilds it if the cache
     * has been cleared.
     */
    public function get_cached(string $key) {
        global $DB;
        if ($this->compiledcache !== null && isset($this->compiledcache['FAIL'])) {
            // This question failed compilation, no need to try again in this request.
            // Make sure the error is back in the error list.
            $this->runtimeerrors[$this->compiledcache['FAIL']] = true;
            return null;
        }

        // Do we have that particular thing in the cache?
        if ($this->compiledcache === null || !array_key_exists($key, $this->compiledcache)) {
            // If not do the compilation.
            try {
                $this->compiledcache = self::compile($this->id,
                    $this->questionvariables, $this->inputs, $this->prts,
                    $this->options, $this->questiontext,
                    $this->questiontextformat,
                    $this->questionnote,
                    $this->generalfeedback, $this->generalfeedbackformat,
                    $this->specificfeedback, $this->specificfeedbackformat,
                    $this->prtcorrect, $this->prtcorrectformat,
                    $this->prtpartiallycorrect, $this->prtpartiallycorrectformat,
                    $this->prtincorrect, $this->prtincorrectformat, $this->penalty);

                // Invalidate Moodle question-cache and add there.
                if (is_integer($this->id) || is_numeric($this->id)) {
                    // Save to DB. If the question is there.
                    // Could not be in some API situations.
                    $sql = 'UPDATE {qtype_stack_options} SET compiledcache = ? WHERE questionid = ?';
                    $params[] = json_encode($this->compiledcache);
                    $params[] = $this->id;
                    $DB->execute($sql, $params);

                    // Invalidate the question definition cache.
                    // First from the next sessions.
                    cache::make('core', 'questiondata')->delete($this->id);
                }
            } catch (stack_exception $e) {
                // TODO: what exactly do we use here as the key
                // and what sort of errors does the compilation generate.
                // CHRIS: The compilation generates errors that relate to the static validation of
                // the question, any such errors are fatal and will be apparent on the first opening
                // of the question in bulk tests or elsewhere, silencing them makes no sense.
                // These are not runtime errors they are validation errors for materials that should
                // not have managed to get through the editor.
                $this->runtimeerrors[$e->getMessage()] = true;
                $this->compiledcache = ['FAIL' => $e->getMessage()];
            }
        }

        // A runtime error means we don't have the $key in the cache.
        // We don't want an error here, we want to degrade gracefully.
        $ret = null;
        if (is_array($this->compiledcache) && array_key_exists($key, $this->compiledcache)) {
            $ret = $this->compiledcache[$key];
        }
        return $ret;
    }

    /**
     * Helper method for "compiling" a question, validates and finds all the things
     * that do not change unless the question changes and stores them in a dictionary.
     *
     * Note that does throw exceptions about validation details.
     *
     * Currently the cache contains the following keys:
     *  'units' for declaring the units-mode.
     *  'forbiddenkeys' for the lsit of those.
     *  'contextvariable-qv' the pre-validated question-variables which are context variables.
     *  'statement-qv' the pre-validated question-variables.
     *  'preamble-qv' the matching blockexternals.
     *  'required' the lists of inputs required by given PRTs an array by PRT-name.
     *  'castext-qt' for the question-text as compiled CASText2.
     *  'castext-qn' for the question-note as compiled CASText2.
     *  'castext-...' for the model-solution and prtpartiallycorrect etc.
     *  'castext-td-...' for downloadable generated text content.
     *  'security-context' mainly lists keys that are student inputs.
     *  'prt-*' the compiled PRT-logics in an array. Divided by usage.
     *  'langs' a list of language codes used in this question.
     *
     * In the future expect the following:
     *  'security-config' extended logic for cas-security, e.g. custom-units.
     *
     * @param int the identifier of this question fot use if we have pluginfiles
     * @param string the questionvariables
     * @param array inputs as objects, keyed by input name
     * @param array PRTs as objects
     * @param stack_options the options in use, if they would ever matter
     * @param string question-text
     * @param string question-text format
     * @param string question-note
     * @param string general-feedback
     * @param string general-feedback format...
     * @param defaultpenalty
     * @return array a dictionary of things that might be expensive to generate.
     */
    public static function compile($id, $questionvariables, $inputs, $prts, $options,
        $questiontext, $questiontextformat,
        $questionnote,
        $generalfeedback, $generalfeedbackformat,
        $specificfeedback, $specificfeedbackformat,
        $prtcorrect, $prtcorrectformat,
        $prtpartiallycorrect, $prtpartiallycorrectformat,
        $prtincorrect, $prtincorrectformat, $defaultpenalty) {
        // NOTE! We do not compile during question save as that would make
        // import actions slow. We could compile during fromform-validation
        // but we really should look at refactoring that to better interleave
        // the compilation.
        //
        // As we currently compile at the first use things start slower than they could.

        // The cache will be a dictionary with many things.
        $cc = [];
        // Some details are globals built from many sources.
        $units = false;
        $forbiddenkeys = [];
        $sec = new stack_cas_security();

        // Some counter resets to ensure that the result is the same even if
        // we for some reason would compile twice in a session.
        // Happens during first preview and can lead to cache being always out
        // of sync if textdownload is in play.
        stack_cas_castext2_textdownload::$countfiles = 1;

        // Static string extrraction now for CASText2 in top level text blobs and PRTs,
        // question varaibles and in the future probably also from input2.
        $map = new castext2_static_replacer([]);

        // First handle the question variables.
        if ($questionvariables === null || trim($questionvariables) === '') {
            $cc['statement-qv'] = null;
            $cc['preamble-qv'] = null;
            $cc['contextvariable-qv'] = null;
            $cc['security-context'] = [];
        } else {
            $kv = new stack_cas_keyval($questionvariables, $options);
            $kv->get_security($sec);
            if (!$kv->get_valid()) {
                throw new stack_exception('Error(s) in question-variables: ' . implode('; ', $kv->get_errors()));
            }
            $c = $kv->compile('/qv', $map);
            // Store the pre-validated statement representing the whole qv.
            $cc['statement-qv'] = $c['statement'];
            // Store any contextvariables, e.g. assume statements.
            $cc['contextvariables-qv'] = $c['contextvariables'];
            // Store the possible block external features.
            $cc['preamble-qv'] = $c['blockexternal'];
            // Finally extend the forbidden keys set if we saw any variables written.
            if (isset($c['references']['write'])) {
                $forbiddenkeys = array_merge($forbiddenkeys, $c['references']['write']);
            }
            if (isset($c['includes'])) {
                $cc['includes']['keyval'] = $c['includes'];
            }
        }

        // Collect the language codes in use. For our purposes the question-text
        // is all that is needed. Other places may have other values but these are
        // enough after all the validations have passed.
        $ml = new stack_multilang();
        $cc['langs'] = $ml->languages_used($questiontext);

        // Then do some basic detail collection related to the inputs and PRTs.
        foreach ($inputs as $input) {
            if (is_a($input, 'stack_units_input')) {
                $units = true;
                break;
            }
        }
        $cc['required'] = [];
        $cc['prt-preamble'] = [];
        $cc['prt-contextvariables'] = [];
        $cc['prt-signature'] = [];
        $cc['prt-definition'] = [];
        $cc['prt-trace'] = [];
        $i = 0;
        foreach ($prts as $name => $prt) {
            $path = '/p/' . $i;
            $i = $i + 1;
            $r = $prt->compile($inputs, $forbiddenkeys, $defaultpenalty, $sec, $path, $map);
            $cc['required'][$name] = $r['required'];
            if ($r['be'] !== null && $r['be'] !== '') {
                $cc['prt-preamble'][$name] = $r['be'];
            }
            if ($r['cv'] !== null && $r['cv'] !== '') {
                $cc['prt-contextvariables'][$name] = $r['cv'];
            }
            $cc['prt-signature'][$name] = $r['sig'];
            $cc['prt-definition'][$name] = $r['def'];
            $cc['prt-trace'][$name] = $r['trace'];
            $units = $units || $r['units'];
            if (isset($r['includes'])) {
                if (!isset($cc['includes'])) {
                    $cc['includes'] = $r['includes'];
                } else {
                    if (isset($r['includes']['keyval'])) {
                        if (!isset($cc['includes']['keyval'])) {
                            $cc['includes']['keyval'] = [];
                        }
                        $cc['includes']['keyval'] = array_unique(array_merge($cc['includes']['keyval'],
                            $r['includes']['keyval']));
                    }
                    if (isset($r['includes']['castext'])) {
                        if (!isset($cc['includes']['castext'])) {
                            $cc['includes']['castext'] = [];
                        }
                        $cc['includes']['castext'] = array_unique(array_merge($cc['includes']['castext'],
                            $r['includes']['castext']));
                    }
                }
            }
        }

        // Note that instead of just adding the unit loading to the 'preamble-qv'
        // and forgetting about units we do keep this bit of information stored
        // as it may be used in input configuration at some later time.
        $cc['units'] = $units;
        $cc['forbiddenkeys'] = $forbiddenkeys;

        // Do some pluginfile mapping. Note that the PRT-nodes are mapped in PRT-compiler.
        $questiontext = stack_castext_file_filter($questiontext, [
            'questionid' => $id,
            'field' => 'questiontext'
        ]);
        $generalfeedback = stack_castext_file_filter($generalfeedback, [
            'questionid' => $id,
            'field' => 'generalfeedback'
        ]);
        $specificfeedback = stack_castext_file_filter($specificfeedback, [
            'questionid' => $id,
            'field' => 'specificfeedback'
        ]);
        $prtcorrect = stack_castext_file_filter($prtcorrect, [
            'questionid' => $id,
            'field' => 'prtcorrect'
        ]);
        $prtpartiallycorrect = stack_castext_file_filter($prtpartiallycorrect, [
            'questionid' => $id,
            'field' => 'prtpartiallycorrect'
        ]);
        $prtincorrect = stack_castext_file_filter($prtincorrect, [
            'questionid' => $id,
            'field' => 'prtincorrect'
        ]);

        // Compile the castext fragments.
        $ctoptions = [
            'bound-vars' => $forbiddenkeys,
            'prt-names' => array_flip(array_keys($prts)),
            'io-blocks-as-raw' => 'pre-input2',
            'static string extractor' => $map
        ];
        $ct = castext2_evaluatable::make_from_source($questiontext, '/qt');
        if (!$ct->get_valid($questiontextformat, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in question-text: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-qt'] = $ct->get_evaluationform();
            // Note that only with "question-text" may we get inlined downloads.
            foreach ($ct->get_special_content() as $key => $values) {
                if ($key === 'text-download') {
                    foreach ($values as $k => $v) {
                        $cc['castext-td-' . $k] = $v;
                    }
                } else if ($key === 'castext-includes') {
                    if (!isset($cc['includes'])) {
                        $cc['includes'] = ['castext' => $values];
                    } else if (!isset($cc['includes']['castext'])) {
                        $cc['includes']['castext'] = $values;
                    } else {
                        foreach ($values as $url) {
                            if (array_search($url, $cc['includes']['castext']) === false) {
                                $cc['includes']['castext'][] = $url;
                            }
                        }
                    }
                }
            }
        }

        $ct = castext2_evaluatable::make_from_source($questionnote, '/qn');
        if (!$ct->get_valid(FORMAT_HTML, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in question-note: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-qn'] = $ct->get_evaluationform();
        }

        $ct = castext2_evaluatable::make_from_source($generalfeedback, '/gf');
        if (!$ct->get_valid($generalfeedbackformat, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in general-feedback: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-gf'] = $ct->get_evaluationform();
        }

        $ct = castext2_evaluatable::make_from_source($specificfeedback, '/sf');
        if (!$ct->get_valid($specificfeedbackformat, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in specific-feedback: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-sf'] = $ct->get_evaluationform();
        }

        $ct = castext2_evaluatable::make_from_source($prtcorrect, '/pc');
        if (!$ct->get_valid($prtcorrectformat, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in PRT-correct message: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-prt-c'] = $ct->get_evaluationform();
        }

        $ct = castext2_evaluatable::make_from_source($prtpartiallycorrect, '/pp');
        if (!$ct->get_valid($prtpartiallycorrectformat, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in PRT-partially correct message: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-prt-pc'] = $ct->get_evaluationform();
        }

        $ct = castext2_evaluatable::make_from_source($prtincorrect, '/pi');
        if (!$ct->get_valid($prtincorrectformat, $ctoptions, $sec)) {
            throw new stack_exception('Error(s) in PRT-incorrect message: ' . implode('; ', $ct->get_errors(false)));
        } else {
            $cc['castext-prt-ic'] = $ct->get_evaluationform();
        }

        // Remember to collect the extracted strings once all has been done.
        $cc['static-castext-strings'] = $map->get_map();

        // The time of the security context as it were during 2021 was short, now only
        // the input variables remain.
        $si = [];

        // Mark all inputs. To let us know that they have special types.
        foreach ($inputs as $key => $value) {
            if (!isset($si[$key])) {
                $si[$key] = [];
            }
            $si[$key][-2] = -2;
        }
        $cc['security-context'] = $si;

        return $cc;
    }

    /**
     * Moodle specific acessor for question capabilities.
     */
    public function has_cap(string $capname): bool {
        return $this->has_question_capability($capname);
    }
}
