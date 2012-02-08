<?php

/*
* Welcome to STACK.  A system for teaching and assessment using a computer algebra kernel.
* This file is licensed under the GPL License.
* A copy of the license is in your STACK distribution called license.txt.  If you are missing this file you can obtain it from: http://www.stack.bham.ac.uk/license.txt
*
* @author Chris Sangwin
*/

require_once('casstring.class.php');

require_once('connector.class.php');

/**
*  This deals with Maxima sessions.
*  This is the class which actually sends variables to the CAS itself.
*/

class STACK_CAS_CasSession {

    private $session;
    private $options;		// STACK_CAS_Maxima_Preferences
    private $seed;

    private $valid;  		// true or false
    private $instantiated;	// Has this been sent to the CAS yet?
    private $errors;		// string for the user

    private $security;
    private $addStars;
    private $strictSyntax;

    function __construct($session, $options = null, $seed=null, $securityLevel='s', $syntax=true, $stars=false)
    {
        $this->session   	= $session;		// An array of STACK_CAS_CasString
        $this->security     	= $securityLevel; 	// by default, student
        $this->addStars     	= $stars;         	// by default don't add stars
        $this->strictSyntax 	= $syntax;        	// by default strict

        if(null===$options) {
            $this->options = new STACK_options();
        } else if (is_a($options,'STACK_options')){
            $this->options = $options;
        } else {
        	throw new Exception('STACK_CAS_CasSession: $options must be STACK_options.');
        }

	if($seed != null)
        {
		if (is_int($seed))
		{		
			$this->seed = $seed;
		}
		else
		{
			throw new Exception('STACK_CAS_CasSession: $seed must be a number.');
		}
        }
        else
        {
            $this->seed = time();
        }

	if(!('s'===$securityLevel || 't'===$securityLevel))
	{
		throw new Exception('STACK_CAS_CAS_String: 4th argument, security level, must be "s" or "t" only.');
	}

	if(!is_bool($syntax))
	{
		throw new Exception('STACK_CAS_CAS_String: 5th argument, stringSyntax, must be Boolean.');
	}

	if(!is_bool($stars))
	{
		throw new Exception('STACK_CAS_CAS_String: 6th argument, insertStars, must be Boolean.');
	}

}

	/*********************************************************/
	/* Validation functions 				 */
	/*********************************************************/

    function validate()
    {

	if (null === $this->session) // Empty sessions are ok.
	{
		$this->valid	= true;
		return true;
	}
	if (false === is_array($this->session))
	{
		$this->valid=false;
		return false;
	}

	$this->valid = $this->validate_array($this->session);

	// Ensure the array is number ordered.  We use this later when getting back the values of expressions
	// so it important to be definite now.
	if ($this->valid)
	{
		$this->session = array_values($this->session);
	}
	return $this->valid;
    }

    /* A helper function which enables an array of STACK_CAS_CasString to be validated */
    function validate_array($cmd)
    {
	$valid  = true;
	foreach($cmd as $key => $val)
	{
		if(is_a($val,'STACK_CAS_CasString'))
		{
			if ( !$val->Get_valid() )
			{
				$valid = false;
				$this->errors .= $val->Get_errors();
			}
		}
		else
		{
			throw new Exception('STACK_CAS_CasSession: $session must be null or an array of STACK_CAS_CasString.');
		}	
	}
	return $valid;
   }

    /* Check each of the CASStrings for any of the keywords */
    public function checkExternalForbiddenWords($keywords)
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
       $found = false;
       foreach ($this->session as $casStr) {
		$found = $found || $casStr->checkExternalForbiddenWords($keywords);
       }
       return $found;
    }

    /* This is the function which actually sends the commands off to Maxima. */
    public function instantiate()
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	if (!$this->valid)
	{
		return false;
	}
	// Lazy instantiation - only do this once...
	// Empty session.  Nothing to do.
	if ($this->instantiated || null===$this->session) {
		return true;
	}

	// Ensure that every command has a valid key.
	$i=0;
        foreach ($this->session as $cs)
	{
		$i++;
		if('' ==  $cs->Get_key())
		{
			$key = 'dumvar'.$i;
		} else {
			$key = $cs->Get_key();
		}
		$casCommands[$key]=$cs->Get_CASString();
	}

    // TODO: add this to some configuration.
	$platform = 'win';
        if($platform == 'server')
        {
            //send to maximaXMLConnector
            $mconn = new maximaXMLConnector($this->options, $this->seed, $this->security);
            $display = $mconn->sendCASCommands($casCommands);

            if($forDisplay === false)
            {
                //Error connecting to a maxima proxy, switch to CLI mode
                $mconn = new STACK_CAS_Maxima_CLIConnector($this->options, $this->seed, $this->security);
                $display = $mconn->sendCASCommands($casCommands);
            }
        }
        else
        {
            //send to STACK_CAS_Maxima_CLIConnector
            $mconn = new STACK_CAS_Maxima_CLIConnector($this->options, $this->seed, $this->security);
            $display = $mconn->sendCASCommands($casCommands);
        }

        $errors = $mconn->returnErrors();
        $values = $mconn->returnValues();

	// Now put the information back into the correct slots.
	$session = $this->session;
	$new_session = array();
	$new_errors  = '';
	$all_fail = true;
	$i=0;

	foreach ($session as $cs)
	{
		$i++;
		if('' ==  $cs->Get_key())
		{
			$key = 'dumvar'.$i;
		} else {
			$key = $cs->Get_key();
		}

		if (array_key_exists($key,$values))
		{
			$all_fail = false; // We at least got one result back from the CAS!
			$cs->Set_value($values[$key]);
			
			$cs->Set_display($display[$key]);
			if ('' != $errors[$key])
			{
				$cs->Add_errors($errors[$key]);
				$new_errors .= ' <span class="SyntaxExample2">'.$cs->Get_rawCASString().'</span> '.STACK_Translator::translate("stackCas_CASErrorCaused").' '.$errors[$key].' ';
			}
		}
		else
		{
			$errstr = STACK_Translator::translate("stackCas_failedReturn").' <span class="SyntaxExample2">'.$cs->Get_rawCASString().'</span> ';
			$cs->Add_errors($errstr);
			$new_errors .= $errstr;
		}
		$new_session[]=$cs;
	}
	$this->session = $new_session;

	if (''!= $new_errors) {
		$this->errors .= '<span class="error">'.STACK_Translator::translate("stackCas_CASError").'</span>'.$new_errors;
	}
	if ($all_fail) {
		$this->errors = '<span class="error">'.STACK_Translator::translate("stackCas_allFailed").'</span>';
    }

	$this->instantiated = true;
    }

    /* Add extra variables to the end of the existing session */
    /* Note, this resets instantiation and validation, which will need to be done again if used. */
    public function add_vars($vars)
    {
	if (is_array($vars))
	{	foreach($vars as $var)
		{
			if(is_a($var,'STACK_CAS_CasString'))
			{
				$this->instantiated = null;
				$this->instantiated = null;
				$this->errors       = null;
				$this->session[]    = $var;
			} else {
				throw new Exception('STACK_CAS_CasSession: trying to add a non-STACK_CAS_CasString to an existing session.');
			}
		}
	}
    }
    /*********************************************************/
    /* Return and modify information			 */
    /*********************************************************/

    public function Get_valid()
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	return $this->valid;
    }

    public function Get_errors()
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	return $this->errors;
    }

    public function Get_value_key($key)
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	if ($this->valid && null===$this->instantiated)
	{
		$this->instantiate();
	}
	foreach ($this->session as $CASstr)
	{
		if ($CASstr->Get_key()===$key)
		{
			return $CASstr->Get_value();
		}
	}
	return false;
    }
	
    public function Get_display_key($key)
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	if ($this->valid && null===$this->instantiated)
	{
		$this->instantiate();
	}
	foreach ($this->session as $CASstr)
	{
		if ($CASstr->Get_key()===$key)
		{
			return $CASstr->Get_display();
		}
	}
	return false;
    }

    public function Get_errors_key($key)
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	if ($this->valid && null===$this->instantiated)
	{
		$this->instantiate();
	}
	foreach ($this->session as $CASstr)
	{
		if ($CASstr->Get_key()===$key)
		{
			return $CASstr->Get_errors();
		}
	}
	return false;
    }

    /* This returns the values of the variables with keys */
    public function Get_display_castext($strin)
    {
	if (null===$this->valid)
	{
		$this->validate();
	}
	if ($this->valid && null===$this->instantiated)
	{
		$this->instantiate();
	}

	foreach($this->session as $CASstr)
	{
		$key    = $CASstr -> Get_key();
		$errors = $CASstr -> Get_errors();
		$disp   = $CASstr -> Get_display();
		$value  = $CASstr -> Get_CASString();

		$dummy = '@'.$key.'@';

                if (''!==$errors && null!=$errors)
                {
                    //$replace = '<font = "red"><tt>'.$value.'</tt></font>';
                    $strin = str_replace($dummy,$value,$strin);
                }
                elseif (strstr($strin,$dummy))
                {
                    $strin = str_replace($dummy,$disp,$strin);
                }//if work to be done
	}
	return $strin;
    }
	
} // end class 