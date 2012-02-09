<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
 * The base class for interaction elements.
 *
 * Interaction elements are the controls that the teacher can put into the question
 * text to receive the student's input.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_InteractionElement {
    const STATE_UNDEFINED     = '';
    const STATE_NEW           = 'new';
    const STATE_VALID         = 'valid';
    const STATE_INVALID       = 'invalid';
    const STATE_SCORED        = 'score';
    const STATE_SOLUTION_SEEN = 'solutionSeen';

    private static $allowedTransitions = array(
        self::STATE_UNDEFINED     => array(
                self::STATE_UNDEFINED, self::STATE_NEW, self::STATE_SOLUTION_SEEN),
        self::STATE_NEW           => array(
                self::STATE_UNDEFINED, self::STATE_NEW, self::STATE_VALID, self::STATE_INVALID),
        self::STATE_VALID         => array(
                self::STATE_UNDEFINED, self::STATE_NEW, self::STATE_INVALID,
                self::STATE_SCORED, self::STATE_SOLUTION_SEEN),
        self::STATE_INVALID       => array(
                self::STATE_UNDEFINED, self::STATE_NEW, self::STATE_SOLUTION_SEEN),
        self::STATE_SCORED        => array(
                self::STATE_UNDEFINED, self::STATE_NEW, self::STATE_SOLUTION_SEEN),
        self::STATE_SOLUTION_SEEN => array(
                self::STATE_SOLUTION_SEEN),
    );

    /**
     * @var string
     */
    public $label;

    /**
     * @var CasText
     */
    protected $rawAns;

    /**
     * @var string one of the STATE_ constants defined above.
     */
    protected $status = self::STATE_UNDEFINED;

    /**
     * @var CasString
     */
    public $casValue = null;

    /**
     * @var string
     */
    public $displayValue = null;

    /**
     * @var CasString
     */
    public $feedback;

    public function __construct($label, $rawAns, $status = self::STATE_UNDEFINED, $casAns = null) {
        $this->label = $label;
        $this->rawAns = $rawAns;
        $this->status = $status;
        $this->casValue = $casAns;
    }

    public function getRawAns() {
        // Strip $, ;, or : from answer
        $str = new STACK_StringUtil($this->rawAns);
        return $str->trimCommands();
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($newStatus) {
        if (!in_array($newStatus, self::$allowedTransitions[$this->status])) {
            return false;
        }

        $this->status = $newStatus;
        return true;
    }
}
