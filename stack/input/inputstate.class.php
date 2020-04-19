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

/**
 * This class represents the current state of an input.
 *
 * @property-read string $status one of the constants stack_input::BLANK, stack_input::INVALID, ...
 * @property-read string $contents the current contents of the input.
 * @property-read string $contentsmodified CAS string as modified by the input routines.  E.g. *s inserted.
 * @property-read string $contentsdisplayed how Stack interpreted the current contents of the input.
 * @property-read string $errors any validation errors.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_input_state {

    /**
     * @var string one of the constants stack_input::BLANK, stack_input::INVALID, ...
     */
    protected $_status;

    /**
     * @var array the current contents of this input as raw input from the student's response.
     */
    protected $_contents;

    /**
     * @var string how STACK/Maxima interpreted the current contents of the input.  E.g., we might add *s to the $_contents.
     */
    protected $_contentsmodified;

    /**
     * @var string how Stack displays the interpreted input.
     */
    protected $_contentsdisplayed;

    /**
     * @var string any validation errors.
     */
    protected $_errors;

    /**
     * @var string any validation note.
     */
    protected $_note;

    /**
     * @var string any variables found in the student's answer, in displayed form.
     */
    protected $_lvars;

    /**
     * Constructor
     *
     * @param array $contents the current contents of this input.  An array with
     *      separate fields as needed by the input type.
     * @param string $contentsmodified A Maxima representation of the current contents
     *      of this input.  This might have been modified, e.g. *s added etc.
     * @param string $contentsdisplayed The displayed form of the current contents of this input.
     * @param string $status one of the constants stack_input::EMPTY, stack_input::INVALID, ...
     * @param string $feedback the feedback for the current contents.
     * @param bool   $simp Should the student's expression be simplified?
     */
    public function __construct($status, $contents, $contentsmodified, $contentsdisplayed, $errors, $note, $lvars,
            $simp = false) {
        if (!is_array($contents)) {
            throw new stack_exception('stack_input_state: contents field of constructor must be an array.');
        }
        $this->_status              = $status;
        $this->_contents            = $contents;
        $this->_contentsmodified    = $contentsmodified;
        $this->_contentsdisplayed   = $contentsdisplayed;
        $this->_errors              = $errors;
        $this->_note                = $note;
        $this->_lvars               = $lvars;
        $this->_simp                = $simp;
    }

    public function __get($field) {
        switch ($field) {
            case 'status':
                return $this->_status;
            case 'contents':
                return $this->_contents;
            case 'contentsmodified':
                return $this->_contentsmodified;
            case 'contentsdisplayed':
                return $this->_contentsdisplayed;
            case 'errors':
                return $this->_errors;
            case 'note':
                return $this->_note;
            case 'lvars':
                return $this->_lvars;
            case 'simp':
                return $this->_simp;
            default:
                throw new stack_exception('stack_input_state: unrecognised property name ' . $field);
        }
    }
}
