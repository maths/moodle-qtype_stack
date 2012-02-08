<?php
/*
* Welcome to STACK.  A system for teaching and assessment using a computer algebra kernel.
* This file is licensed under the GPL License.
* A copy of the license is in your STACK distribution called license.txt.  If you are missing this file you can obtain it from: http://www.stack.bham.ac.uk/license.txt
*
* @author Chris Sangwin
*/

/**
 *  Higher level class for a pair of values.
 *  Mostly for editing reasons.
 */
class STACK_CAS_KeyVal { // originally extended QuestionType
    // Attributes

    private $raw; 		// Holds the raw text as entered by a question author.
    private $session;		// An array of STACK_CAS_CasString (not a fully fledged STACK_CAS_CasSession)
    private $CAS_session;	// A fully fledged STACK_CAS_CasSession, when instantiated.

    private $valid;  		// true or false
    private $instantiated;	// has this been sent to the CAS yet?
    private $errors;		// string for the user

    private $security;
    private $addStars;
    private $strictSyntax;

    public function __construct($raw, $securityLevel='s', $syntax=true, $stars=false)
     {
        $this->raw 		= $raw;

	$this->security     	= $securityLevel; 	// by default, student
        $this->addStars     	= $stars;         	// by default don't add stars
        $this->strictSyntax 	= $syntax;        	// by default strict

	$this->session 		= NULL;

	if(!is_string($raw))
	{
		throw new Exception('STACK_CAS_KeyVal: raw must be a STRING.');
	}

	if(!('s'===$securityLevel || 't'===$securityLevel))
	{
		throw new Exception('STACK_CAS_KeyVal: 2nd argument, security level, must be "s" or "t" only.');
	}

	if(!is_bool($syntax))
	{
		throw new Exception('STACK_CAS_KeyVal: 3 argument, stringSyntax, must be Boolean.');
	}

	if(!is_bool($stars))
	{
		throw new Exception('STACK_CAS_KeyVal: 6th argument, insertStars, must be Boolean.');
	}
     }

    private function validate()
    {

		if(empty($this->raw))
		{
			$this->valid = true;
			return true;
		}

		//$str = new STACK_StringUtil($this->raw);
		//$str = $str->removeComments();
		$str = $this->raw;
		$str = str_replace(';',"\n",$str);
		$kv_array = explode("\n",$this->raw);
				
		$errors  = '';
		$valid   = true;
		$session = array();
		foreach($kv_array as $kvs)
		{
			$cs        = new STACK_CAS_CasString($kvs,$this->security,$this->addStars,$this->strictSyntax);
			$valid     = $valid && $cs->Get_valid();
			$errors   .= $cs->Get_errors();
			$session[] = $cs;
		}

		$this->session = $session;
		$this->valid   = $valid;
		if (!$valid)
		{
			$this->errors .= STACK_Translator::translate('stackCas_invalidCommand').'<br />'.$errors;
		}

	}

    public function Get_valid()
    {
	if (NULL===$this->valid)
	{
		$this->validate();
	}
	return $this->valid;
    }

    public function Get_errors()
    {
	if (NULL===$this->valid)
	{
		$this->validate();
	}
	return $this->errors;
    }

    private function instantiate($seed)
    {
	if (!$this->valid)
	{
		return false;
	}

	$new_session = new STACK_CAS_CasSession($this->session,NULL,$seed,$this->security,$this->addStars,$this->strictSyntax);
	$new_session->instantiate();
	
	$this->CAS_session = $new_session;
	$this->errors .= $this->session->Get_errors();

	$this->instantiated = true;
    }

    public function Get_session()
    {
	    if (NULL===$this->valid)
	    {
		$this->validate();
	    }
	    if (NULL===$this->instantiated)
	    {
		$this->instantiate();
	    }
	    else if (false===$this->instantiated)
	    {
		return false;
	    }
	    return $this->CAS_session;
    }

    /**
    * Generates a form element for editing this question type. If values have
    * been specified then a drop down list is generate, otherwise a text input box
    * @param name string the name of the element in the form
    * @param size int the size of the text box, defaults to 15
    * @access public
    * @return string XHTML for insertion into a form field.
    */
    public function editWidget($name, $size=100)
    {

        $edit_text = str_replace(';',"\n",$this->raw);
        $widget = '<input type="text" name="'.$name.'" size="'.$size.'" value="'.$edit_text .'"/>';

        return $widget;
    }
}
