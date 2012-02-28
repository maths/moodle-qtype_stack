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

require_once(dirname(__FILE__) . '/../../locallib.php');
require_once(dirname(__FILE__) . '/../options.class.php');
require_once(dirname(__FILE__) . '/../cas/casstring.class.php');
require_once(dirname(__FILE__) . '/../cas/cassession.class.php');

/**
 * This class represents the current state if an input.
 *
 * @property-read string $status one of the constants stack_input::EMPTY, stack_input::INVALID, ...
 * @property-read string $contents the current contents of the input.
 * @property-read string $contentsinterpreted how Stack interpreted the current contents of the input.
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
     * @var string the current contents of this input.
     */
    protected $_contents;

    /**
     * @var string how Stack interpreted the current contents of the input.
     */
    protected $_contentsinterpreted;

    /**
     * @var string any validation errors.
     */
    protected $_errors;

    /**
     * Constructor
     *
     * @param string $contents the current contents of this input.
     * @param string $status one of the constants stack_input::EMPTY, stack_input::INVALID, ...
     * @param string $feedback the feedback for the current contents.
     */
    public function __construct($status, $contents, $contentsinterpreted, $errors) {
        $this->_status              = $status;
        $this->_contents            = $contents;
        $this->_contentsinterpreted = $contentsinterpreted;
        $this->_errors              = $errors;
    }

    public function __get($field) {
        switch ($field) {
            case 'status':
                return $this->_status;
            case 'contents':
                return $this->_contents;
            case 'contentsinterpreted':
                return $this->_contentsinterpreted;
            case 'errors':
                return $this->_errors;
            default:
                throw new Exception('stack_input_state: unrecognised property name ' . $field);
        }}
}
