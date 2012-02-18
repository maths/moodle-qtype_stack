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

/**
 * The base class for interaction elements.
 *
 * Interaction elements are the controls that the teacher can put into the question
 * text to receive the student's input.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_interaction_element {
    protected static $perametersavailable = array(
        'mustVerify',
        'hideFeedback',
        'boxWidth',
        'boxHeight',
        'strinctSyntax',
        'insertStars',
        'syntaxHint',
        'forbidWords',
        'forbidFloats',
        'lowestTerms',
        'sameType');
    /**
     * @var string the name of the interaction element. 
     * This name has two functions
     *  (1) it is the name of thename of the POST variable that the input from this 
     *  element will be submitted as.
     *  (2) it is the name of the CAS variable to which the student's answer is assigned.
     *  Note, that during authoring, the teacher simply types #name# in the question stem to
     *  create these interaction elements.
     */
    protected $name;

    /*
     * @var string Every interaction element must have a non-empty "teacher's answer".
     */
    private $teacheranswer;

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
     * @param array $parameters some sort of options.
     */
    public function __construct($name, $teacheranswer, $parameters = null) {
        $this->name = $name;
        $this->teacheranswer = $teacheranswer;
        if (is_null($parameters)) {
            $this->parameters = $this->get_parameters_defaults();
        } else {
            // TODO validate incoming data here.
            $this->parameters = $parameters;
        }
    }

    /**
     * Returns the XHTML for embedding this interaction element in a page.
     *
     * @param string student's current answer to insert into the xhtml.
     * @param bool $readonly whether the contro should be displayed read-only.
     * @return string HTML fragment.
     */
    public function get_xhtml($studentanswer, $readonly) {
        return '';
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
     * Sets the value of an interaction element parameters. 
     * @return array of parameters names.
     */
    public function set_parameter($parameter, $value) {
        if (in_array($parameter, $this->get_parameters_used())) {
            $this->parameters[$parameter] = $value;
        } else {
            //TODO how do we know the name of the class for the error message?
            throw new Exception('stack_interaction_element: setting parameter '.$parameter.' which does not exist for interaction elements of type ?', $code, $previous);
        }
    }

    /**
     * Validates the value of an interaction element parameters. 
     * @return array of parameters names.
     */
    public function validate_parameter($parameter, $value) {
        if (!in_array($parameter, $this->get_parameters_used())) {
            //TODO how do we know the name of the class for the error message?
            throw new Exception('stack_interaction_element: trying to validate parameter '.$parameter.' which does not exist for interaction elements of type ?', $code, $previous);
        }

        switch($parameter) {
            case 'mustVerify':
                $valid = is_bool($value);
                break;

            case 'hideFeedback':
                $valid = is_bool($value);
                break;

            case 'strinctSyntax':
                $valid = is_bool($value);
                break;

            case 'insertStars':
                $valid = is_bool($value);
                break;

            case 'forbidFloats':
                $valid = is_bool($value);
                break;

            case 'lowestTerms':
                $valid = is_bool($value);
                break;

            case 'sameType':
                $valid = is_bool($value);
                break;

            default:
                $valid = $this->internal_validate_parameter($parameter, $value);
        }
        return $valid;
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid 
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        return true;
    }

    /**
     * Returns a list of the names of all the parameters. 
     * @return array of parameters names.
     */
    public function get_parameters_available() {
        return $this->$perametersavailable;
    }

    /**
     * Returns a list of the names of all the parameters that this type of interaction
     * element uses. 
     * @return array of parameters names.
     */
    public function get_parameters_used() {
        return array_keys($this->get_parameters_defaults());
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array();
    }

    /**
     * A helper method used in testing. Given a value returned by this input element,
     * returns the POST data variables that generate that value.
     *
     * @param string $value a value for this input element.
     * @return array simulated POST data.
     */
    public function get_test_post_data($value) {
        return array($this->name=>$value);
    }
}
