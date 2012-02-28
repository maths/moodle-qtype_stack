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

    /**
     * @var string Any specific feedback for this question. This is displayed
     * in the 'yellow' feedback area of the question. It can contain PRTfeedback
     * tags, but not IEfeedback.
     */
    public $specificfeedback;

    /**
     * @var int one of the FORMAT_... constants
     */
    public $specificfeedbackformat;

    /**
     * @var array STACK specific: string name as it appears in the question text => stack_input
     */
    public $inputs;

    /**
     * @var string STACK specific: variables, as authored by the teacher.
     */
    public $questionvariables;

    /**
     * @var array stack_potentialresponse_tree STACK specific: respones tree number => ...
     */
    public $prts;

    /**
     * @var stack_options STACK specific: question-level options.
     */
    public $options;

    /**
     * @var int STACK specific: seeds Maxima's random number generator.
     */
    protected $seed;

    /**
    * @var array stack_cas_session STACK specific: session of variables.
     */
    protected $session;

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

        $this->seed = time();
        $step->set_qt_var('_seed', $this->seed);

        $questionvars = new stack_cas_keyval($this->questionvariables, $this->options, $this->seed, 't');
        $qtext = new stack_cas_text($this->questiontext, $questionvars->get_session(), $this->seed, 't', false, true);

        $this->session = $qtext->get_session();
        $step->set_qt_var('_questiontext', $qtext->get_display_castext());

        if ($qtext->get_errors()) {
            throw new Exception('Error rendering the question text: ' . $qtext->get_errors());
        }

        $feedbacktext = new stack_cas_text($this->specificfeedback, $this->session, $this->seed, 't', false, true);
        $step->set_qt_var('_feedback', $feedbacktext->get_display_castext());

        if ($qtext->get_errors()) {
            throw new Exception('Error rendering the feedback text: ' . $feedbacktext->get_errors());
        }

        $this->session = $qtext->get_session();
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->seed = (int) $step->get_qt_var('_seed');
        $questionvars = new stack_cas_keyval($this->questionvariables, $this->options, $this->seed, 't');
        $qtext = new stack_cas_text($this->questiontext, $questionvars->get_session(), $this->seed, 't', false, true);
        $this->session = $qtext->get_session();
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
        $response = array();
        foreach ($this->inputs as $name => $input) {
            $response[$name] = $input->get_teacher_answer();
        }
        return $response;
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

        $this->inputstates[$name] = $this->inputs[$name]->validate_student_response(
                $response, $this->options);

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
        $allblank = true;
        foreach ($this->inputs as $name => $input) {
            $status = $this->get_input_state($name, $response)->status;
            if (stack_input::INVALID == $status) {
                return false;
            }
            $allblank = $allblank && ($status == stack_input::BLANK);
        }
        return !$allblank;
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
     * Do we have all the necssary inputs to execute one of the potential response trees?
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
                'fraction' => null,
                'feedback' => '',
            );
        }

        return $this->prtresults[$index];
    }

    public function get_num_variants() {
        // TODO We will probably need this when it comes to instantiating questions.
        return 1;
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'specificfeedback') {
            // Specific feedback files only visibile when the feedback is.
            return $options->feedback;

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }
}
