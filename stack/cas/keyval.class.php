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
 * "key=value" class to parse user-entered data into CAS sessions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_keyval { // originally extended QuestionType
    // Attributes

    private $raw;            // Holds the raw text as entered by a question author.
    private $session;        // An array of stack_cas_casstring (not a fully fledged stack_cas_session)

    private $valid;          // true or false
    private $instantiated;   // has this been sent to the CAS yet?
    private $errors;         // string for the user

    private $security;
    private $insertstars;
    private $syntax;

    public function __construct($raw, $options = null, $seed=null, $security='s', $syntax=true, $stars=false) {
        $this->raw          = $raw;
        $this->security     = $security;   // by default, student
        $this->insertstars  = $stars;      // by default don't add stars
        $this->syntax       = $syntax;     // by default strict

        $this->session      = new stack_cas_session(null, $options, $seed, $this->security, $this->syntax, $this->insertstars);;

        if (!is_string($raw)) {
            throw new Exception('stack_cas_keyval: raw must be a string.');
        }

        if (!('s'===$security || 't'===$security)) {
            throw new Exception('stack_cas_keyval: 2nd argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new Exception('stack_cas_keyval: 3 argument, syntax, must be boolean.');
        }

        if (!is_bool($stars)) {
            throw new Exception('stack_cas_keyval: 6th argument, stars, must be boolean.');
        }
    }

    private function validate() {
        if (empty($this->raw)) {
            $this->valid = true;
            return true;
        }

        //TODO remove comments from the raw string before processing.
        $str = $this->raw;
        $str = str_replace(';', "\n", $str);
        $kv_array = explode("\n", $str);

        $errors  = '';
        $valid   = true;
        $vars = array();
        foreach ($kv_array as $kvs) {
            $kvs = trim($kvs);
            if ('' != $kvs) {
                // Split over the first occurance of the equals sign, turning this into normal Maxima assignment.
                    $i = strpos($kvs, '=');
                    if (false === $i) {
                        $val = $kvs; 
                    } else {
                    // Need to check we don't have a function definition...
                        if (':'===substr($kvs, $i-1, 1)) {
                            $val = $kvs; 
                        } else {
                            $val = trim(trim(substr($kvs, 0, $i)).':'.trim(substr($kvs, $i+1)));
                        }
                    }

                $cs = new stack_cas_casstring($val, $this->security, $this->syntax, $this->insertstars);
                $vars[] = $cs;
            }
        }

        $this->session->add_vars($vars);
        $this->valid       = $this->session->get_valid();
        $this->errors      = $this->session->get_errors();
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

    public function instantiate() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (!$this->valid) {
            return false;
        }
        $this->session->instantiate();
        $this->instantiated = true;
    }

    public function get_session() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->session;
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
