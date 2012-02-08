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
* Object which handles maxima specific code.
*
* takes in an array of CAS commands, builds a string suitable for maxima
* sends this string to maxima, then extracts the results from the returned string
* Also handles errors from maxima.
*/
    require_once('answer.class.php');

    class STACK_CAS_Maxima_CLIConnector {

    /**
    *
    *
    * @param int
    * @access private
    */
        private $seed;

    /**
    *
    *
    * @param error
    * @access private
    */
        private $errorLog;

    /**
    *
    *
    * @param string
    * @access private
    */
        private $display;

    /**
    *
    *
    * @param STACK_options
    * @access private
    */
        private $options;

    /**
    *
    *
    * @param string
    * @access private
    */
        private $rawResult;

    /**
    *
    *
    * @param array
    * @access private
    */
        private $casCommands;

    /**
    *
    *
    * @param string
    * @access private
    */
        private $security;

    /**
    *
    *
    * @param array
    * @access private
    */
        private $csNames;

    /**
    *
    *
    * @param array
    * @access private
    */
        private $csVars;
    /**
    *
    *
    * @param array
    * @access private
    */
        private $csCmds;

    /**
     * The logger for this class
     *
     * @param STACK_Logger
     * @access private
     */
        private $logger;

    /**
    * @param array cas commands
    * @param string display type (mathml/latex)
    */


    private $maximaAnsArray;

        function __construct($options, $seed, $security='s')
        {
            $this->options  = $options;
            $this->seed     = $seed;
            $this->security = $security;

            $this->csNames = '';
            $this->csVars  = '';
            $this->csCmds  = '';

            $this->rawcommand = '';


            $path = 'C:\xampp\data\moodledata\stack';
            $initCommand = "load(\"".$path."\maximalocal.mac\");";
            $initCommand = str_replace("\\", "/", $initCommand);
            $initCommand .= "\n";
                        
            $this->config['platform']       = 'win';
            $this->config['logs']           = $path;
            $this->config['CASCommand']     = $path.'\maxima.bat';
            $this->config['CASInitCommand'] = $initCommand;
            $this->config['CASTimeout']     = 5;
            $this->config['CASDebug']       = false;
            $this->config['CASVersion']     = '5.21.1';
            
        }

    /**
    * wrapper function, call below functions in correct order & returns the array with the display values.
    *
    * @return array|bool return results from cas, or false if error.
    */

        function sendCASCommands($casCommands)
        {
            $this->casCommands = $casCommands;

            $cas_options=$this->options->getcascommands();
            $this->csNames .= $cas_options['names'];
            $this->csVars  .= $cas_options['commands'];
            
            $this->constructMaximaCommand($casCommands);
            if(!$this->sendToMaxima())
            {
                //$this->errorLog->addError('Could not send to Maxima. ');
                //$this->logger->error('Could not send command to maxima. ');
            }
            else
            {
                if($this->parseAns())
                {
                    //$this->errorLog->addError('Parsing answer failed. ');

                }
                if(!$this->validateAns())
                {
                    //$this->errorLog->addError('Validation failed, '.$this->validateAns());

                }
                return $this->returnResults();
            }
        }
    /**
    * Sends a answertest to the cas
    *
    * @param string $student
    * @param string $teacher
    * @param string $anstest
    * @access protected
    * @return array
    *
    */
        public function sendAnsTest($student, $teacher, $anstest)
        {
            $this->buildVariables($student, $teacher, $anstest);
            $this->constructMaximaAnstest($student, $teacher, $anstest);
            //echo $this->rawcommand;

            if(!$this->sendToMaxima())
            {
                //$this->errorLog->addError('Could not send to Maxima. ');
                return 'Could not send to maxima. ';
            }
            else
            {
                //echo 'parsedAnsTest: '.$this->parseAnsTest();
                //var_dump($this->parseAnsTest());
                return $this->parseAnsTest();
            }

        }


    /**
    * wraps up the cas commands in a string suitable for maxima
    *
    * @param array $casCommands
    * @return string
    */
        function constructMaximaCommand($casCommands)
        {
            $i = 0;
            foreach($casCommands as $label => $cmd)
            {
                $cmd = str_replace('?', 'qmchar', $cmd); // replace any ?'s that slipped through

                $this->csNames .= ", $label";
                $this->csCmds .= ", print(\"$i=[ error= [\"), cte(\"$label\",errcatch($label:$cmd)) ";
                $i++;

            }

            $cs ='cab:block([ RANDOM_SEED';
            $cs .= $this->csNames;
            $cs .='], stack_randseed(';
            $cs .= $this->seed.')'.$this->csVars;
            $cs .= ", print(\"[TimeStamp= [ $this->seed ], Locals= [ \") ";
            $cs .= $this->csCmds;
            $cs .= ", print(\"] ]\") , return(true) ); \n ";

            //echo $cs.'<br />';
            $this->rawcommand = $cs;
            return $cs;
        }

    /**
    * Constructs an answertest
    *
    * @param string $exp1 //1st ans
    * @param string $exp2 //2nd ans
    * @param string $ansTest
    * @return string the command
    */
        protected function constructMaximaAnstest($exp1, $exp2, $ansTest)
        {

            $cs  = "cab:block([ STACK_SA,STACK_TA,str{$this->csNames}] {$this->csVars}, ";
            $cs .= " print(\"[ Timestamp = [ $this->seed ], Ans= [ error = [\"), STACK_SA:cte(\"STACK_SA\",errcatch($exp1)),";
            $cs .= " print(\" TAAns= [ error = [\"), STACK_TA:cte(\"STACK_TA\",errcatch(STACK_TA:$exp2)),";
            $cs .= " print(\" AnswerTestError = [  \"), str:StackReturn($ansTest(STACK_SA,STACK_TA)), print(\" ], \"), print(str), return(true)); \n";
            $this->rawcommand = $cs;
            return $cs;
        }


    /**
    * Sends string to maxima
    *
    * @return string
    */
        function sendToMaxima()
        {

            $platform = $this->config['platform'];
            
            if ($platform == 'win')
            {
                $result = $this->sendWin($this->rawcommand);
                //echo "result: $result";
            }
            //server mode may fall back to launching a maxima process if connecting to the servers fail.
            elseif (($platform == 'unix') || ($platform == 'server'))
            {
                $result = $this->sendUnix($this->rawcommand);

            }
            else
            {
                throw new Exception('STACK_CASconnector: Unknown platform '.$platform);
            }
            //echo '<br /><br /><pre>'.$this->rawcommand.'</pre><br /><br />';
            //echo '<br /><br /><pre>'.$result.'</pre><br /><br />';

            $this->rawResult = $result;
            return $result;
        }


    /**
    * Removes the junk characters maxima adds to each variable.
    *
    * @param string $var The string to clean
    * @return string The cleaned string
    */
        protected function cleanUpVar($var)
        {
            $var = str_replace('[', '',$var);
            $var = str_replace(']', '',$var);
            $var = str_replace(',', '',$var);
            $var = trim($var);
            return $var;
        }
        //"
    /**
    * Maxima-specific function used to parse CAS output into an array.
    *
    * @param array $strin Raw CAS output
    * @return array
    */
        function CASParsePreparse($strin)
        {
            // Take the raw string from the CAS, and unpack this into an array.
            $offset = 0;
            $strin_len = strlen($strin);
            $unparsed = '';
            $errors = '';

            if ($eqpos = strpos($strin,'=',$offset)) { // Check there are ='s
                do {
                    $gb = STACK_Legacy::util_grabbetween($strin,'[',']',$eqpos);
                    $val = substr($gb[0], 1, strlen($gb[0])-2);
                    $val = str_replace('"', '', $val);
                    $val = trim($val);

                    if (preg_match('/[A-Za-z0-9].*/',substr($strin,$offset,$eqpos-$offset),$regs)) {
                        $var = trim($regs[0]);
                    } else {
                        $var = 'errors';
                        $errors['LOCVARNAME'] = "Couldn't get the name of the local variable.";
                    }

                    $unparsed[$var] = $val;
                    $offset = $gb[2];
                } while (($eqpos = strpos($strin,'=',$offset)) && ($offset < $strin_len));

            } else {
                $errors['PREPARSE'] = "There are no ='s in the raw output from the CAS!";
            }

            if ('' != $errors) {
                $unparsed['errors'] = $errors;
            }

            return($unparsed);
        }

    /**
    * Maxima-specific function used to parse CAS output
    * of instantiated local variables into an array.
    *
    * @param string $instr The raw CAS output
    * @return array Has field ['questionVarsInst'] containing the array of instantiated local variables
    */
        function CASParseCASOutput($instr)
        {
            $errors = '';
            $locals = array();

            $instr = trim(str_replace('#','',$instr));
            $instr = trim(str_replace("\n",'',$instr));
            $unp   = $this->CASParsePreparse($instr); // Unparse the main body.
            //show_array($unp);
            if (array_key_exists('Locals',$unp)) {
                $uplocs = $unp['Locals']; // Grab the local variables
                unset($unp['Locals']);
            } else {
                $uplocs = '';
            }

            $locals_raw = $this->CASParsePreparse($uplocs);
            // Now we need to turn the (error,key,value,display) tuple into an array
            foreach ($locals_raw as $var => $valdval) {
                    if (is_array($valdval)) {
                        $errors["CAS"] = "CAS failed to generate any useful output.";
                    } else {
                        if (preg_match('/.*\[.*\].*/',$valdval)) { // There are some []'s in the string.
                            $loc = $this->CASParsePreparse($valdval);
                            if ('' == trim($loc['error'])) {
                                unset($loc['error']);
                            }
                            $locals[]=$loc;
                        } else {
                            $errors["LocalVarGet$var"] = "Couldn't unpack the local variable $var from the string $valdval.";
                        }
                    }
            }
        /*echo 'OUT<pre>';
        var_dump($locals);
        echo '</pre><hr>';*/
            return($locals);
        }

    /**
    * Extracts results, validates the answer from maxima & packs in array of maximaAns objects
    *
    * @return bool whether variables are missing. An explaination is added to the error variable of each object.
    */
        function parseAns()
        {
            $trimmedResult = '';
            $foundErrors = false;
            //check we have a timestamp & remove everything before it.
            $ts = substr_count($this->rawResult, '[TimeStamp');
            if($ts != 1)
            {
                $this->errorLog->addError('CAS Error, no timestamp returned.<br />');
                $foundErrors = true;
            }
            else
            {
                $trimmedResult = strstr($this->rawResult, '[TimeStamp'); //remove everything before the timestamp
            }

            $parsedArray = $this->CASParseCASOutput($trimmedResult);

            for($i=0; $i < count($parsedArray); $i++)
            {

                if(isset($parsedArray[$i]['error']))
                {
                    $error = $this->TidyMaximaErrors($parsedArray[$i]['error']);
                }
                else
                {
                    $error = '';
                }
                $plot = isset($parsedArray[$i]['display']) ? substr_count($parsedArray[$i]['display'], '<img') : 0; // if theres a plot being returned
                if($plot > 0)
                {
                    //plots always contain errors, so remove
                    $error = '';
                    //for mathml display, remove the mathml that is inserted wrongly round the plot.
                    $parsedArray[$i]['display'] = str_replace('<math xmlns=\'http://www.w3.org/1998/Math/MathML\'>', '', $parsedArray[$i]['display']);
                    $parsedArray[$i]['display'] = str_replace('</math>', '', $parsedArray[$i]['display']);

                    //for latex mode, remove the mbox
                    // handles forms: \mbox{image} and (earlier?) \mbox{{} {image} {}}
                    $parsedArray[$i]['display'] = preg_replace("|\\\mbox{({})? (<html>.+</html>) ({})?}|", "$2", $parsedArray[$i]['display']);
                }
                if (isset($parsedArray[$i]['key'])) $this->maximaAnsArray[] = new STACK_CAS_Maxima_Answer($parsedArray[$i]['key'], $parsedArray[$i]['value'], $parsedArray[$i]['display'], $error);
            }

            return $foundErrors;
        }

    /**
    * Deals with Maxima errors.   Enables some translation.
    *
    * @param string $errstr a Maxima error string
    * @return string
    */
        private function TidyMaximaErrors($errstr) {

           if (FALSE===strpos($errstr,'0 to a negative exponent')) {
           } else {
               $errstr = STACK_Translator::translate('Maxima_DivisionZero');
           }

        return $errstr;
    }

    /**
    * Maxima-specific function used to parse the CAS output from
    * an answer test into an array.
    *
    * @param string $instr the raw CAS output
    * @return array containing fields ['AnsValue'] and ['AnsDisplay']
    */
        function CASAnsTestParse($instr) {

            //echo "<pre>";print_r($instr);echo "</pre>";
            $unp = $this->CASParsePreparse($instr);
            //echo "<pre>";print_r($unp);echo "</pre>";

            if (array_key_exists('error',$unp)) {
                    $unp['error']=$this->TidyMaximaErrors($unp['error']);
            }

            if (array_key_exists('Ans',$unp)) {
                $unp['Ans'] = $this->CASParsePreparse($unp['Ans']);

                if (''==$unp['Ans']['error']) {
                    unset($unp['Ans']['error']);
                } else {
                    $unp['Ans']['error']=$this->TidyMaximaErrors($unp['Ans']['error']);
                }
            }

            if (array_key_exists('TAAns',$unp)) {
                $unp['TAAns'] = $this->CASParsePreparse($unp['TAAns']);

                if (''!=$unp['TAAns']['error']) {
                    $unp['error'].=$this->TidyMaximaErrors($unp['TAAns']['error']);
                }
                unset($unp['TAAns']);
            }


            if (array_key_exists('AnswerTestError',$unp)) {
               if (''!=$unp['AnswerTestError']) {
                   $unp['error'].=$unp['AnswerTestError'];
               }
            }
            unset($unp['AnswerTestError']);

            //echo "<pre>";print_r($unp);echo "</pre>";

            /* If the fields are not here, Maxima didn't generate them = problem! */
            if(!array_key_exists('rawmark',$unp)) {
               $unp['rawmark']     = 0;
               if (array_key_exists('feedback',$unp)) {
                  $unp['feedback']   .= ' STACK_Legacy::trans("TEST_FAILED");';
               } else {
                  $unp['feedback']    = ' STACK_Legacy::trans("TEST_FAILED");';
               }
               if (array_key_exists('answernote',$unp)) {
                  $unp['answernote'] .= ' TEST_FAILED ';
               } else {
                  $unp['answernote']  = ' TEST_FAILED';
               }
               if (array_key_exists('error',$unp)) {
                  $unp['error']      .= ' TEST_FAILED';
               } else {
                  $unp['error']       = ' TEST_FAILED';
               }
            }

            //echo "<pre>";print_r($unp);echo "</pre>";
            return $unp;

        }


    /**
    * Parses the raw result from maxima & converts into xml.
    *
    * @return array of answers
    */
        function parseAnsTest()
        {
            if(($this->rawResult == '') ||($this->rawResult ==  NULL))
            {
                $this->returnErrors = 'Error. No CAS Output for answer test';
            }

            $trimResult =  strstr($this->rawResult, 'error');
            $trimResult = str_replace('(%o1) true','', $trimResult);

            $result = $this->CASAnsTestParse($trimResult);
            return $result;
        }

    /**
    * Checks for any errors from maxima, returns false if error encountered.
    * Attempts to return as much as possible from maxima for debugging purposes.
    *
    * @return bool
    */
        function validateAns()
        {

            $toReturn = true;
            $validAnsArray= array();

            //check through every maxima command
            $noObjects = count($this->maximaAnsArray);

            for ($i =0; $i < $noObjects; $i++)
            {

                $obj = $this->maximaAnsArray[$i];

                if ($obj->getLabel() == NULL)
                {
                    //cannot match with anything, remove from array
                    //$this->maximaAnsArray[$i] = NULL;
                }
                else
                {

                    if (($obj->getDisplay() == NULL) && ($obj->getValue() == NULL))
                    {
                        //substitute in the original command
                        $label = $obj->getLabel();
                        $obj->setDisplay($this->casCommands[$label]);
                    }

                    if (($obj->getDisplay() == NULL) && ($obj->getValue() != NULL))
                    {
                        //subsitute in the value, if possible
                        $obj->setDisplay($obj->getValue);
                    }


                    if ($obj->getError() != '')
                    {
                        $toReturn = false; //errors encounted
                    }
                    $validAnsArray[] = $obj;

                }
            }

            //var_dump($validAnsArray);
            $this->maximaAnsArray = $validAnsArray;

            return $toReturn;
        }




    /**
    * Returns an array containing the results from maxima.
    * in the form [label] => display
    *
    * @return array
    */
        function returnResults()
        {
            //of the format [label] -> display

            //let everything have an error by default
            $returnArray = $this->casCommands;

            foreach($this->casCommands as $label => $cmd)
            {
                if(strpos($cmd,"stack_validate")!==false) {
                         $returnArray[$label] = '<html><span class="errorMsg">CAS Error: No output.</span></html>';
                } else {
                         $returnArray[$label] = '<html><span class="errorMsg">CAS Error: No output for '.$cmd.'</span></html>';
                }
            }

            //then overwrite the valid answers with the correct display value

            $noObjects = count($this->maximaAnsArray);

            for($i =0; $i <$noObjects; $i++)
            {
                $obj = $this->maximaAnsArray[$i];
                $label = $obj->getLabel();
                $display = $obj->getDisplay();
                $returnArray[$label] = $display;
            }

            return $returnArray;
        }

    /**
    * Returns an array containing the values from maxima with no formatting.
    * in the form [label] => value
    *
    * @return array
    */
        function returnValues()
        {
            //of the format [label] -> display

            $noObjects = count($this->maximaAnsArray);
            $returnArray = array();

            for($i =0; $i <$noObjects; $i++)
            {
                $obj = $this->maximaAnsArray[$i];
                $label = $obj->getLabel();
                $val = $obj->getValue();
                $returnArray[$label] = $val;
            }

            return $returnArray;
        }


    /**
    * Returns an array of errors matched with their labels (if any)
    * Used in STACK_CAS_DisplayCASText to match up CAS errors to the cas command
    *
    * @return array in the form [label] => error
    */
        function returnErrors()
        {
            //of the format [label] -> error

            $returnArray = array();
            $noObjects = count($this->maximaAnsArray);

            for($i =0; $i <$noObjects; $i++)
            {
                $obj = $this->maximaAnsArray[$i];
                $label = $obj->getLabel();
                $CASerror = $obj->getError();

                if ($CASerror != '')
                {
                    //get the original command
                    $org = $this->casCommands[$label];

                    $returnArray[$label] = $CASerror;
                    //'The command <span class="warning">@ '.$org.' @</span> caused the error: <span class="errorMsg">'.$CASerror.'</span>';
                }
                else
                {
                    $returnArray[$label] = NULL;
                }
            }

            return $returnArray;
        }

    /**
    * Starts a instance of maxima and sends the maxima command under a Windows OS
    *
    * @param string $strin
    * @return string
    * @access public
    */
        private function sendWin($strin)
        {
            $ret = FALSE;

            $descriptors = array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('file', $this->config['logs']."cas_errors.txt", 'a'));
            $cmd = '"'.$this->config['CASCommand'].'"';
            //echo $cmd;
            $CASProcess = proc_open($cmd, $descriptors, $pipes);
            if(is_resource($CASProcess)) {
                if (!fwrite($pipes[0], $this->config['CASInitCommand']))
                {
                    //echo "<br />Could not write to the CAS process!<br/ >\n";
                    //$this->logger->critical('Could not write to CAS process '.$cmd);
                    return(FALSE);
                }
                fwrite($pipes[0], $strin);
                fwrite($pipes[0], 'quit();\n\n');
                fflush($pipes[0]);


                $ret = '';
                // read output from stdout


                while (!feof($pipes[1])) {
                    $out = fgets($pipes[1], 1024);
                    if ('' == $out) {
                        // PAUSE
                        usleep(1000);
                    }
                    $ret .= $out;

                }
                fclose($pipes[0]);
                fclose($pipes[1]);

            }
            else
            {
                //echo "Could not open a CAS process.";
                //$this->logger->critical('Could not open a CAS process '.$cmd);
                die();
            }
            $ret = trim($ret);
            //echo '<pre>'.$strin.'</pre>';
            //echo '<hr><pre>'.$ret.'</pre>';

            if($this->config['CASDebug'])
            {
                echo "<pre>";
                echo $strin;
                echo "</pre>";
                echo $ret;
            }
            return $ret;
        }

    /**
    * Connect directly to the CAS, and return the raw string result.
    * This does not use sockets, but calls a new CAS session each time.
    * Hence, this is not likely to be efficient.
    * Furthermore, since the system gives the webserver execute priviliges
    * to this is insecure.
    *
    * @param string $strin The string of CAS commands to be processed.
    * @return string|boolean The converted HTML string or FALSE if there was an error.
    */
        private function sendUnix($strin) {
            // Sends the $st to maxima.
            //global $stack_root,$stack_logfiles,$stack_cas;


            if($this->config['CASDebug']) {
                $debug = true;
            } else {
                $debug = false;
            }
            $ret   = FALSE;
            $err   = '';
            $cwd   = null;
            $env   = array('why'=>'itworks');

            $descriptors = array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w'));
            $CASProcess = proc_open($this->config['CASCommand'],$descriptors, $pipes, $cwd, $env);
         /*$proc_array = proc_get_status($CASProcess);
         echo $proc_array['command'].'<br />';
         echo $proc_array['pid'].'<br />';
         echo $proc_array['running'].'<br />';*/

            if(is_resource($CASProcess))
            {

                if($debug)
                {
                    echo $strin;
                    echo $this->config['CASInitCommand'];
                }

                if (!fwrite($pipes[0], $this->config['CASInitCommand']))
                {
                    echo "<br />Could not write to the CAS process!<br />\n";
                    //$this->logger->critical('Could not write to the CAS process: '.$cmd);
                    return(FALSE);
                }
                fwrite($pipes[0], $strin);
                fwrite($pipes[0], 'quit();'."\n\n");

                $ret = '';
                // read output from stdout
                $start_time = microtime(true);
                $continue   = TRUE;

                if (!stream_set_blocking($pipes[1], FALSE )) {
                    echo "<br />Warning: could not stream_set_blocking to be FALSE on the CAS process.<br />";
                    //$this->logger->warn('Warning: could not stream_set_blocking to be FALSE on the CAS process.');

                }

                if ($debug) {
                    echo "<b>Input</b> <pre>$strin</pre>";
                    echo '<pre>'; };
                while ($continue and !feof($pipes[1])) {

                    $now =  microtime(true);

                    if (($now-$start_time) > $this->config['CASTimeout']) {
                        $proc_array = proc_get_status($CASProcess);
                        if ($proc_array['running']) {
                            proc_terminate($CASProcess);
                        }
                        $continue = FALSE;
                    } else {

                        $out = fread($pipes[1], 1024);
                        if ('' == $out) {
                            // PAUSE
                            usleep(1000);
                        }
                        $ret .= $out;
                        if ($debug) { echo $out; }
                    }

                }

                if ($continue) {

                    fclose($pipes[0]);
                    fclose($pipes[1]);
                    if ($debug) {
                        $time_taken = $now-$start_time;
                        echo "</pre>";
                        echo "Start: $start_time<br >End: $now<br >Taken = $time_taken";
                    };

                } else {
                    // Add sufficient closing ]'s to allow something to be un-parsed from the CAS.
                    $ret .=' The CAS timed out. ] ] ] ]';
                }


            } else {
                echo "STACK error: could not open a CAS process<br />\n";
                $err = "NO-CAS-PROCESS";
                die();
            }
            //echo '<pre>'.$ret.'</pre>';

            return $ret;
        }


    }
