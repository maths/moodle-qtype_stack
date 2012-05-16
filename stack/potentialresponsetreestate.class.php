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
    public $_answernote  = array();

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
     * Constructor
     *
     * @param array $feedback the current contents of this input.
     * @param string $status one of the constants stack_input::EMPTY, stack_input::INVALID, ...
     * @param string $feedback the feedback for the current contents.
     */
    public function __construct($errors = '', $feedback = array(), $answernote = array(), $valid = true, $score = null, $penalty = null) {
        $this->_errors      = $errors;
        $this->_feedback    = $feedback;
        $this->_answernote  = $answernote;
        $this->_valid       = $valid;
        $this->_score       = $score;
        $this->_penalty     = $penalty;
    }

    public function __get($field) {
        switch ($field) {
            case 'errors':
                return $this->_errors;
            case 'feedback':
                return $this->_feedback;
            case 'answernote':
                return $this->_answernote;
            case 'valid':
                return $this->_valid;
            case 'score':
                return $this->_score;
            case 'penalty':
                return $this->_penalty;
            default:
                throw new stack_exception('stack_potentialresponse_tree_state: __get().  Unrecognised property name ' . $field);
        }
    }

    public function add_answernote($note) {
        $this->_answernote[] = $note;
    }

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