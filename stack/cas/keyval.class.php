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
 * "key=value" class to parse user-entered data into CAS sessions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_keyval { // originally extended QuestionType
    // Attributes

    private $raw;            // Holds the raw text as entered by a question author.
    private $session;        // An array of stack_cas_casstring (not a fully fledged stack_cas_session)
    private $cas_session;    // A fully fledged stack_cas_session, when instantiated.

    private $valid;          // true or false
    private $instantiated;   // has this been sent to the CAS yet?
    private $errors;        // string for the user

    private $security;
    private $insertstars;
    private $syntax;

    public function __construct($raw, $security='s', $syntax=true, $stars=false) {
        $this->raw          = $raw;
        $this->security     = $security;   // by default, student
        $this->insertstars  = $stars;      // by default don't add stars
        $this->syntax = $syntax;           // by default strict

        $this->session      = null;

        if (!is_string($raw)) {
            throw new Exception('stack_cas_keyval: raw must be a STRING.');
        }

        if (!('s'===$security || 't'===$security)) {
            throw new Exception('stack_cas_keyval: 2nd argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new Exception('stack_cas_keyval: 3 argument, stringSyntax, must be Boolean.');
        }

        if (!is_bool($stars)) {
            throw new Exception('stack_cas_keyval: 6th argument, insertStars, must be Boolean.');
        }
    }

    private function validate() {
        if (empty($this->raw)) {
            $this->valid = true;
            return true;
        }

        //$str = new STACK_StringUtil($this->raw);
        //$str = $str->removeComments();
        $str = $this->raw;
        $str = str_replace(';', "\n", $str);
        $kv_array = explode("\n", $this->raw);

        $errors  = '';
        $valid   = true;
        $session = array();
        foreach ($kv_array as $kvs) {
            $cs        = new stack_cas_casstring($kvs, $this->security, $this->insertstars, $this->syntax);
            $valid     = $valid && $cs->get_valid();
            $errors   .= $cs->get_errors();
            $session[] = $cs;
        }

        $this->session = $session;
        $this->valid   = $valid;
        if (!$valid) {
            $this->errors .= stack_string('stackCas_invalidCommand').'<br />'.$errors;
        }
    }

    public function get_valid() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->valid;
    }

    public function get_errors() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->errors;
    }

    private function instantiate($seed) {
        if (!$this->valid) {
            return false;
        }

        $new_session = new stack_cas_session($this->session, null, $seed, $this->security, $this->addStars, $this->strictSyntax);
        $new_session->instantiate();

        $this->cas_session = $new_session;
        $this->errors .= $this->session->get_errors();

        $this->instantiated = true;
    }

    public function get_session() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (null===$this->instantiated) {
            $this->instantiate();
        } else if (false===$this->instantiated) {
            return false;
        }
        return $this->cas_session;
    }

    /**
     * Generates a form element for editing this question type. If values have
     * been specified then a drop down list is generate, otherwise a text input box
     * @param name string the name of the element in the form
     * @param size int the size of the text box, defaults to 15
     * @access public
     * @return string XHTML for insertion into a form field.
     */
    public function edit_widget($name, $size=100) {

        $edit_text = str_replace(';', "\n", $this->raw);
        $widget = '<input type="text" name="'.$name.'" size="'.$size.'" value="'.$edit_text .'"/>';

        return $widget;
    }
}
