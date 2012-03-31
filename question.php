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

/**
 * Stack question definition class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/stack/input/factory.class.php');
require_once(dirname(__FILE__) . '/stack/cas/keyval.class.php');
require_once(dirname(__FILE__) . '/stack/cas/castext.class.php');
require_once(dirname(__FILE__) . '/stack/potentialresponsetree.class.php');


/**
 * Represents a Stack question.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question extends question_graded_automatically {
    const MARK_MODE_PENALTY = 'penalty';
    const MARK_MODE_FIRST = 'firstanswer';
    const MARK_MODE_LAST = 'lastanswer';

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

    /** @var Feedback that is displayed for any PRT that returns a score of 1. */
    public $prtcorrect;

    /** @var int one of the FORMAT_... constants */
    public $prtcorrectformat;

    /** @var Feedback that is displayed for any PRT that returns a score between 0 and 1. */
    public $prtpartiallycorrect;

    /** @var int one of the FORMAT_... constants */
    public $prtpartiallycorrectformat;

    /** @var Feedback that is displayed for any PRT that returns a score of 0. */
    public $prtincorrect;

    /** @var int one of the FORMAT_... constants */
    public $prtincorrectformat;

    /** @var string how marks are computed by PRTs in adaptive mode. */
    public $markmode;

    /** @var string if set, this is used to control the pseudo-random generation of the seed. */
    public $variantsselectionseed;

    /**
     * @var array STACK specific: string name as it appears in the question text => stack_input
     */
    public $inputs;

    /**
     * @var array stack_potentialresponse_tree STACK specific: respones tree number => ...
     */
    public $prts;

    /**
     * @var stack_options STACK specific: question-level options.
     */
    public $options;

    /**
     * @var array of seed values that have been deployed.
     */
    public $deployedseeds;

    /**
     * @var int STACK specific: seeds Maxima's random number generator.
     */
    public $seed = null;

    /**
     * @var array stack_cas_session STACK specific: session of variables.
     */
    protected $session;

    /**
     * @var array stack_cas_session STACK specific: session of variables.
     */
    protected $questionnoteinstantiated;

    /**
     * The next three fields cache the results of some expensive computations.
     * The chache is only vaid for a particular response, so we store the current
     * response, so that we can clearn the cached information in the result changes.
     * See {@link validate_cache()}.
     * @var array = null;
     */
    protected $lastresponse = null;

    /**
     * @var array input name => stack_input_state.
     * This caches the results of validate_student_response for $lastresponse.
     */
    protected $inputstates = array();

    /**
     * @var array prt name => result of evaluate_response, if known.
     */
    protected $prtresults = array();

    /**
     * Make sure the cache is valid for the current response. If not, clear it.
     */
    protected function validate_cache($response) {
        if (is_null($this->lastresponse)) {
            // Nothing cached yet. No worries.
            $this->lastresponse = $response;
            return;
        }

        if ($this->lastresponse == $response) {
            return; // Cache is good.
        }

        // Clear the cache.
        $this->lastresponse = $response;
        $this->inputstates = array();
        $this->prtresults = array();
    }

    public function start_attempt(question_attempt_step $step, $variant) {

        // Work out the right seed to use.
        if (!is_null($this->seed)) {
            // Nasty hack, but if seed has already been set, then use that. This is
            // used by the questiontestrun.php script to allow non-deployed
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
        $step->set_qt_var('_seed', $this->seed);

        // Build up the question session out of all the bits that need to go into it.
        // 1. question variables
        $questionvars = new stack_cas_keyval($this->questionvariables, $this->options, $this->seed, 't');
        $session = $questionvars->get_session();

        // 2. correct answer for all inputs.
        $response =array();
        foreach ($this->inputs as $name => $input) {
            $cs = new stack_cas_casstring($input->get_teacher_answer());
            $cs->validate('t');
            $cs->set_key($name);
            $response[$name] = $cs;
        }
        $session->add_vars($response);
        $session_length = count($session->get_session());

        // 3. CAS bits inside the question text.
        $questiontext = new stack_cas_text($this->questiontext, $session, $this->seed, 't', false, true);
        if ($questiontext->get_errors()) {
            throw new Exception('qtype_stack_question : Error in the the question text: ' .
                    $questiontext->get_errors());
        }

        // 4. CAS bits inside the specific feedback.
        $feedbacktext = new stack_cas_text($this->specificfeedback, $session, $this->seed, 't', false, true);
        if ($questiontext->get_errors()) {
            throw new Exception('qtype_stack_question : Error in the feedback text: ' .
                    $feedbacktext->get_errors());
        }

        // 5. CAS bits inside the question note.
        $notetext = new stack_cas_text($this->questionnote,  $session, $this->seed, 't', false, true);
        if ($questiontext->get_errors()) {
            throw new Exception('qtype_stack_question : Error in the question note: ' .
                    $notetext->get_errors());
        }

        // Now instantiate the session:
        $session->instantiate();
        if ($session->get_errors()) {
            throw new Exception('qtype_stack_question : CAS error when instantiating the session: ' .
            $session->get_errors());
        }

        // Now store the values that depend on the instantiated session.
        $step->set_qt_var('_questionvars', $session->get_keyval_representation());
        $step->set_qt_var('_questiontext', $questiontext->get_display_castext());
        $step->set_qt_var('_feedback', $feedbacktext->get_display_castext());
        $this->questionnoteinstantiated = $notetext->get_display_castext();
        $step->set_qt_var('_questionnote', $this->questionnoteinstantiated);

        // Finally, store only those values really needed for later.
        $session->prune_session($session_length);
        $this->session = $session;
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->seed = (int) $step->get_qt_var('_seed');
        $questionvars = new stack_cas_keyval($step->get_qt_var('_questionvars'), $this->options, $this->seed, 't');
        $this->session = $questionvars->get_session();
        $this->questionnoteinstantiated = $step->get_qt_var('_questionnote');
    }

    public function format_generalfeedback($qa) {
        if (empty($this->generalfeedback)) {
            return '';
        }

        $gftext = new stack_cas_text($this->generalfeedback, $this->session, $this->seed, 't', false, true);

        if ($gftext->get_errors()) {
            throw new Exception('Error rendering the general feedback text: ' . $qtext->get_errors());
        }

        return $this->format_text($gftext->get_display_castext(), $this->generalfeedbackformat,
                $qa, 'question', 'generalfeedback', $this->id);
    }

    public function get_expected_data() {
        $expected = array();
        foreach ($this->inputs as $name => $input) {
            $expected[$name] = PARAM_RAW;
            if ($input->requires_validation()) {
                $expected[$name . '_val'] = PARAM_RAW;
            }
        }
        return $expected;
    }

    public function get_question_summary() {
        return $this->questionnoteinstantiated;
    }

    public function summarise_response(array $response) {
        $bits = array();
        foreach ($this->inputs as $name => $notused) {
            if (array_key_exists($name, $response)) {
                $bits[] = $name . ': ' . $response[$name];
            }
        }
        return implode('; ', $bits);
    }

    public function get_correct_response() {
        $teacheranswer = array();
        foreach ($this->inputs as $name => $input) {
            $teacheranswer[$name] = $this->session->get_casstring_key($name);
        }
        return $teacheranswer;
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->inputs as $name => $input) {
            if (!question_utils::arrays_same_at_key_missing_is_blank(
                    $prevresponse, $newresponse, $name)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the results of validating one of the input elements.
     * @param string $name the name of one of the input elements.
     * @param array $response the response.
     * @return stack_input_state the result of calling validate_student_response() on the input.
     */
    public function get_input_state($name, $response) {
        $this->validate_cache($response);

        if (array_key_exists($name, $this->inputstates)) {
            return $this->inputstates[$name];
        }

        // The student's answer may not contain any of the variable names with which
        // the teacher has defined question variables.   Otherwise when it is evaluated
        // in a PRT, the student's answer will take these values.   If the teacher defines
        // 'ta' to be the answer, the student could type in 'ta'!  We forbid this.
        $forbiddenkeys = $this->session->get_all_keys();

        $teacheranswer = $this->session->get_casstring_key($name);
        $this->inputstates[$name] = $this->inputs[$name]->validate_student_response(
                $response, $this->options, $teacheranswer, $forbiddenkeys);

        return $this->inputstates[$name];
    }

    public function is_complete_response(array $response) {
        foreach ($this->inputs as $name => $input) {
            if (stack_input::SCORE != $this->get_input_state($name, $response)->status) {
                return false;
            }
        }
        return true;
    }

    public function is_gradable_response(array $response) {
        // The following code requires all *inputs* to be non-empty and valid.
        $allblank = true;
        foreach ($this->inputs as $name => $input) {
            $status = $this->get_input_state($name, $response)->status;
            if (stack_input::INVALID == $status) {
                return false;
            }
            $allblank = $allblank && ($status == stack_input::BLANK);
        }
        return !$allblank;
        /*
        // I think this is closer to the mark....
        $anyprtgradable = false;
        foreach ($this->prts as $index => $prt) {
            $anyprtgradable = $anyprtgradable || $this->can_execute_prt($prt, $response);
        }
        return $anyprtgradable;
        */
    }

    public function get_validation_error(array $response) {
        // We don't use this method, but the interface requires us to have implemented it.
        return '';
    }

    public function grade_response(array $response) {
        $fraction = 0;

        foreach ($this->prts as $index => $prt) {
            $results = $this->get_prt_result($index, $response);
            $fraction += $results['fraction'];
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    /**
     * Do we have all the necessary inputs to execute one of the potential response trees?
     * @param stack_potentialresponse_tree $prt the tree in question.
     * @param array $response the response.
     * @return bool can this PRT be executed for that response.
     */
    protected function can_execute_prt(stack_potentialresponse_tree $prt, $response) {
        foreach ($prt->get_required_variables(array_keys($this->inputs)) as $name) {
            if (stack_input::SCORE != $this->get_input_state($name, $response)->status) {
                return false;
            }
        }
        return true;
    }

    /**
     * Evaluate a PRT for a particular response.
     * @param string $index the index of the PRT to evaluate.
     * @param array $response the response to process.
     * @return array the result from $prt->evaluate_response(), or a fake array
     *      if the tree cannot be executed.
     */
    public function get_prt_result($index, $response) {
        $this->validate_cache($response);

        if (array_key_exists($index, $this->prtresults)) {
            return $this->prtresults[$index];
        }

        $prt = $this->prts[$index];
        if ($this->can_execute_prt($prt, $response)) {
            $this->prtresults[$index] = $prt->evaluate_response(
                    $this->session, $this->options, $response, $this->seed);
        } else {
            $this->prtresults[$index] = array(
                'feedback'   => '',
                'answernote' => null,
                'errors'     => null,
                'valid'      => null,
                'score'      => null,
                'penalty'    => null,
                'fraction'   => null,
            );
        }

        return $this->prtresults[$index];
    }

    /**
     * @return bool whether this question uses randomisation.
     */
    public function has_random_variants() {
        return preg_match('~\brand~', $this->questionvariables);
    }

    public function get_num_variants() {
        if (!$this->has_random_variants()) {
            // This question does not use randomisation. Only declare one variant.
            return 1;
        }

        if (!empty($this->deployedseeds)) {
            // Fixed number of deployed versions, declare that.
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

    public function get_all_question_vars() {
        $vars = array();
        foreach ($this->session->get_all_keys() as $key) {
            $vars[$key] = $this->session->get_value_key($key);
        }
        return $vars;
    }

    /**
     * Add all the question variables to a give CAS session. This can be used to
     * initialise that session, so expressions can be evaluated in the context of
     * the question variables.
     * @param stack_cas_session $session the CAS session to add the question variables to.
     */
    public function add_question_vars_to_session(stack_cas_session $session) {
        $session->merge_session($this->session);
    }
}
