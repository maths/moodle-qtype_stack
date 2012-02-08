<?php

/*
 This file is part of Stack - http://stack.bham.ac.uk//

 Stack is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Stack is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Stack.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* A single result returned by maxima
*
*/
class STACK_CAS_Maxima_Answer {

    /**
    *
    *
    * @access private
    * @var string
    */
    private $label;
    /**
    *
    *
    * @access private
    * @var string
    */
    private $display;
    /**
    *
    *
    * @access private
    * @var string
    */
    private $error;
    /**
    *
    *
    * @access private
    * @var string
    */
    private $value;

    /**
    *
    *
    * @param string $label
    * @param string $value
    * @param string $display
    * @param string $error
    *
    */
    public function __construct($label, $value, $display, $error) {
        $this->label = $label;
        $this->display = $display;
        $this->error = $error;
        $this->value = $value;
    }

    /**
    * returns the label
    *
    * @access public
    * @return string
    */
    public function getLabel() {
        return $this->label;
    }

    /**
    * returns a string ready for display
    *
    * @access public
    * @return string
    */
    public function getDisplay() {
        return $this->display;
    }

    /**
    * returns the raw value
    *
    * @access public
    * @return string
    */
    public function getValue() {
        return $this->value;
    }

    /**
    * Returns any errors
    *
    * @access public
    * @return string
    */
    public function getError() {
        return $this->error;
    }

    /**
    * Sets the display string
    *
    * @access public
    * @param string
    */
    public function setDisplay($text) {
        $this->display = $text;
    }

}
