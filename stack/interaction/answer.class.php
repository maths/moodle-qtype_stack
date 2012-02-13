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
 * The base class for interaction elements.
 *
 * Interaction elements are the controls that the teacher can put into the question
 * text to receive the student's input.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class STACK_Input_Answer {

    /**
     * @var string the name of the interaction element. This is the name of the
     * POST variable that the input from this element will be submitted as.
     */
    protected $name;

    /**
     * @var int TODO I'm not really sure why this is in the base class.
     */
    protected $boxWidth = 15;

    /**
     * @var int TODO I'm not really sure why this is in the base class.
     */
    protected $boxHeight = 1;

    /**
     * @var string|array
     */
    protected $default;

    /**
     * @var    int
     */
    protected $maxLength = null;

    /**
     * Answertest paramaters.
     * @var array paramer name => current value.
     */
    protected $parameters;

    /**
     * Constructor
     *
     * @param string $name the name of the interaction element. This is the name of the
     *      POST variable that the input from this element will be submitted as.
     * @param int $width size of the input.
     * @param string $default initial contets of the input.
     * @param int $maxLength limit on the maximum input length.
     * @param int $height height of the input.
     * @param array $param some sort of options.
     */
    public function __construct($name, $width = null, $default = null, $maxLength = null,
            $height = null, $param = null) {
        $this->name = $name;
        if (!is_null($width)) {
            $this->boxWidth = $width;
        }
        if (!is_null($default)) {
            $this->default = $default;
        }
        if (!is_null($maxLength)) {
            $this->maxLength = $maxLength;
        }
        if (!is_null($height)) {
            $this->boxHeight = $height;
        }
        if (!is_null($param)) {
            $this->parameters = $param;
        }
    }

    /**
     * Returns the XHTML for embedding this interaction element in a page.
     *
     * @param bool $readonly whether the contro should be displayed read-only.
     * @return string HTML fragment.
     */
    public abstract function getXHTML($readonly);

    /**
     * Sets the text in the Answerinput type to a new default,
     * Used to put the students last answer back in the input type when the page is submitted.
     *
     * @param string $newDefault
     */
    public function setDefault($newDefault) {
        $this->default = $newDefault;
    }

    /**
     * Returns the default parameters for this input, optionally updating them first.
     *
     * @param array $param array of new parameter values to set.
     * @return array the parameter values for this field.
     */
    public function getDefaultParam($param = null) {
        return $this->parameters;
    }

    /**
     * Transforms the student's input into a casstring if needed. From most returns same as went in.
     *
     * @param array|string $in
     * @return string
     */
    public function transform($in) {
        return $in;
    }

    /**
     * A helper method used in testing. Given a value returned by this input element,
     * returns the POST data variables that generate that value.
     *
     * @param string $value a value for this input element.
     * @return array simulated POST data.
     */
    public function getTestPostData($value) {
        return array($this->name=>$value);
    }

    /**
     * Returns a list of the names of all the opitions that this type of interaction
     * element uses. (Default implementation returns all options.)
     * @return array of option names.
     */
    public static function getOptionsUsed() {
        return array('teacherAns', 'boxSize', 'informalSyntax', 'insertStars',
                'syntaxHint', 'forbid', 'allow', 'floats', 'lowest', 'sameType',
                'studentVerify', 'hideFeedback');
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function getOptionDefaults() {
        return array();
    }
}
