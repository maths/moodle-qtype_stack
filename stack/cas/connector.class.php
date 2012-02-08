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
* MaximaConnector
*
* A wrapper function to abstract out platform specific maxima connections
*/
//require_once('maximaXMLConnector.php');
require_once('cliconnector.class.php');

class STACK_CAS_Maxima_Connector {
    /**
    *
    *
    * @var maximaPreference
    * @access private
    **/
    private $options;
    /**
    *
    *
    * @var array
    * @access private
    **/
    private $errors;
    /**
    *
    *
    * @var array
    * @access private
    **/
    private $forDisplay;
    /**
    *
    *
    * @var string
    * @access private
    **/
    private $security;
    /**
    *
    *
    * @var array
    * @access private
    **/
    private $values;
    /**
    *
    *
    * @var int
    * @access private
    **/
    private $seed;
    /**
    *
    *
    * @var array
    * @access private
    **/
    private $casCommands;

    /**
    * Constructs a maxima connection for any platform
    *
    * @param STACK_CAS_Maxima_Preferences $maximaObject
    * @param string $security
    * @param int $seed
    */
    public function __construct($options, $security='s', $seed= null)
    {
        $this->options  = $options;
        $this->security = $security;
        if($seed != null)
        {
            $this->seed = $seed;
        }
        else
        {
            $this->seed = time();
        }
    }


    /**
    * Sends the answer test to maxima returning the result
    *
    * @param string $student
    * @param string $teacher
    * @param string $anstest
    * @access public
    * @return bool
    */
    public function sendAnsTest($student, $teacher, $anstest)
    {
        $platform = _::$General["Platform"];
        if($platform == 'server')
        {
            $mconn = new maximaXMLConnector($this->STACK_CAS_Maxima_Preferences, $this->seed, 't');
            $result = $mconn->sendAnsTest($student, $teacher, $anstest);
            if($result === false)
            {
                //Error connecting to a maxima proxy, switch to CLI mode
                $mconn = new STACK_CAS_Maxima_CLIConnector($this->STACK_CAS_Maxima_Preferences, $this->seed, $this->security);
                $result = $mconn->sendAnsTest($student, $teacher, $anstest);
            }

        }
        else
        {
            $mconn = new STACK_CAS_Maxima_CLIConnector($this->STACK_CAS_Maxima_Preferences, $this->seed, 't');
            $result = $mconn->sendAnsTest($student, $teacher, $anstest);
        }
        return $result;
    }

    /**
    * returns any error strings
    *
    * @access public
    * @return array
    */
    public function returnErrors()
    {
        return $this->errors;
    }

    /**
    * returns the raw values
    *
    * @access public
    * @return array
    */
    public function returnValues()
    {
        return $this->values;
    }
}

