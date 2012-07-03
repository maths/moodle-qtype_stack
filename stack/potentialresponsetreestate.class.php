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
 * This class represents the current state of a potential response tree.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse_tree_state {

    /**
     * @var string This holds errors needed for the user.
     */
    public $_errors      = '';

    /**
     * @var array of feedback strings for the student.
     */
    public $_feedback    = array();

    /**
     * @var array of answernote strings for the teacher.
     */
    public $_answernotes = array();

    /**
     * @var boolean Is this attempt valid?
     */
    public $_valid       = true;

    /**
     * @var float The raw score for this attempt.  Penalties are calculated later.
     */
    public $_score       = 0;

    /**
     * @var float Penalty attracted by this attempt.
     */
    public $_penalty     = 0;

    /**
     * @var float Weight of this PRT within the question.
     */
    public $_weight;

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
     */
    public function __construct($weight, $valid = true, $score = null, $penalty = null,
            $errors = '', $answernotes = array(), $feedback = array()) {
        $this->_weight      = $weight;
        $this->_valid       = $valid;
        $this->_score       = $score;
        $this->_penalty     = $penalty;
        $this->_errors      = $errors;
        $this->_answernotes = $answernotes;
        $this->_feedback    = $feedback;
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
            default:
                throw new stack_exception('stack_potentialresponse_tree_state: __get().  Unrecognised property name ' . $field);
        }
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
     * @param string $feedback the next bit of feedback.
     */
    public function add_feedback($feedback) {
        $this->_feedback[] = $feedback;
    }

    public function display_feedback($cascontext, $seed) {
        $feedbackct = new stack_cas_text(implode(' ', $this->_feedback), $cascontext, $seed, 't', false, false);
        $ret = $feedbackct->get_display_castext();
        $this->_errors = trim($this->_errors.' '.$feedbackct->get_errors());
        return $ret;
    }
}
