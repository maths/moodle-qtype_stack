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

/**
 * This class represents the current state of a potential response tree.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse_tree_state {

    /**
     * @var string This holds errors needed for the user.
     */
    public $_errors = '';

    /**
     * @var array of stack_prt_feedback_element.
     */
    public $_feedback = array();

    /**
     * @var array of answernote strings for the teacher.
     */
    public $_answernotes = array();

    /**
     * @var array of detailed tracing for the teacher.
     */
    public $_trace = array();

    /**
     * @var boolean Is this attempt valid?
     */
    public $_valid = true;

    /**
     * @var float The raw score for this attempt.  Penalties are calculated later.
     */
    public $_score = 0;

    /**
     * @var float Penalty attracted by this attempt.
     */
    public $_penalty = 0;

    /**
     * @var float Weight of this PRT within the question.
     */
    public $_weight;

    /**
     * @var array
     */
    public $_debuginfo;

    /**
     * @var stack_cas_session2
     */
    protected $cascontext;

    /**
     * @var int
     */
    protected $seed;

    /**
     * @var boolean
     */
    protected $simplify;

    /**
     * Constructor
     *
     * @param float $weight the value of this PRT within the question.
     * @param bool $valid whether evaluating the PRT completed successfully.
     * @param float $score the score computed by this PRT.
     * @param float $penalty penalty computed by this PRT.
     * @param string $errors any error messages.
     * @param array $answernotes the answer notes from the evaluation.
     * @param array $feedback the current contents of this input.
     * @param array $debuginfo debug info.
     */
    public function __construct($weight, $valid = true, $score = null, $penalty = null,
            $errors = '', $answernotes = array(), $feedback = array(), $debuginfo = null) {
        $this->_weight      = $weight;
        $this->_valid       = $valid;
        $this->_score       = $score;
        $this->_penalty     = $penalty;
        $this->_errors      = $errors;
        $this->_answernotes = $answernotes;
        $this->_feedback    = $feedback;
        $this->_debuginfo   = $debuginfo;
    }

    public function __get($field) {
        switch ($field) {
            case 'weight':
                return $this->_weight;
            case 'valid':
                return $this->_valid;
            case 'score':
                return $this->_score;
            case 'penalty':
                return $this->_penalty;
            case 'fraction':
                return $this->_score * $this->_weight;
            case 'fractionalpenalty':
                return $this->_penalty * $this->_weight;
            case 'errors':
                return $this->_errors;
            case 'feedback':
                return $this->_feedback;
            case 'answernotes':
                return $this->_answernotes;
            case 'trace':
                return $this->_trace;
            case 'debuginfo':
                return $this->_debuginfo;
            default:
                throw new stack_exception('stack_potentialresponse_tree_state: __get().  Unrecognised property name ' . $field);
        }
    }

    /**
     * Store the CAS context, so we can use it later if we want to output the
     * feedback.
     * @param stack_cas_session2 $cascontext the case context containing the
     *      feedback variables, sans and tans for each node, etc.
     * @param int $seed the random seed used.
     * @param bool $simp
     */
    public function set_cas_context(stack_cas_session2 $cascontext, $seed, $simp) {
        $this->cascontext = $cascontext;
        $this->seed = $seed;
        $this->simplify = $simp;
    }

    /**
     * Add another answer note to the list.
     * @param string $note the new answer note.
     */
    public function add_answernote($note) {
        $this->_answernotes[] = $note;
    }

    /**
     * Add more answer notes to the list.
     * @param array $notes the new answer notes.
     */
    public function add_answernotes($notes) {
        $this->_answernotes = array_merge($this->_answernotes, $notes);
    }

    /**
     * Add another bit of feedback.
     *
     * @param string $feedback the next bit of feedback.
     * @param int $format one of Moodle's FORMAT_... constants.
     * @param string $filearea feedback file area name.
     * @param int $nodeid node id (used as the file area item id).
     */
    public function add_feedback($feedback, $format = null, $filearea = null, $nodeid = null) {
        $this->_feedback[] = new stack_prt_feedback_element($feedback, $format, $filearea, $nodeid);
    }

    /**
     * Get the bits of feedback.
     * @return array of stack_prt_feedback_element.
     */
    public function get_feedback() {
        return $this->_feedback;
    }

    /**
     * Subsitute variables into the feedback text.
     * @param string $feedback the concatenated feedback text.
     * @return string the feedback with question variables substituted.
     */
    public function substitue_variables_in_feedback($feedback) {
        // In this case, we want to get as much castext as possible back to a student.
        // Some variables might have created a run time error (e.g. division by zero).
        // These errors render $this->cascontext invalid, so the castext will not evaluate.
        // However, many (most?) of the variables will exist, and we can generate decent partial castext.
        // We prune out any invalid variables at this stage.
        $sessionvars = $this->cascontext->get_session();
        $cleanvars = array();
        foreach ($sessionvars as $var) {
            if ($var->get_valid()) {
                $cleanvars[] = $var;
            }
        }

        $options = $this->cascontext->get_options();
        // We also need to respect the actual value of simplification and set it explicitly again at the end of the list.
        if ($this->simplify) {
            $simp = 'true';
        } else {
            $simp = 'false';
        }
        $cleanvars[] = stack_ast_container::make_from_teacher_source('simp:' . $simp, '', new stack_cas_security());

        $cleansession = new stack_cas_session2($cleanvars, $options, $this->seed);
        $feedbackct = new stack_cas_text($feedback, $cleansession, $this->seed);
        $result = $feedbackct->get_display_castext();
        $this->_errors = trim($this->_errors . ' ' . $feedbackct->get_errors());
        $this->_errors = trim($this->_errors . ' ' . $this->cascontext->get_errors());
        return $result;
    }

    /**
     * Add another answer trace to the list.
     * @param string $trace the line in the trace.
     */
    public function add_trace($trace) {
        $this->_trace[] = $trace;
    }
}


/**
 * Small class to encapsulate all the data for the feedback from one PRT node.
 */
class stack_prt_feedback_element {
    /** @var string the feedback text. */
    public $feedback;

    /** @var int the feedback format. One of the FORMAT_... constants. */
    public $format;

    /** @var string feedback file area name. */
    public $filearea;

    /** @var int node id (used as the file area item id). */
    public $itemid;

    public function __construct($feedback, $format, $filearea, $itemid) {
        $this->feedback = $feedback;
        $this->format   = $format;
        $this->filearea = $filearea;
        $this->itemid   = $itemid;
    }
}
