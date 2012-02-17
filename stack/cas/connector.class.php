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
 * Class which undertakes process control to connect to Maxima.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_cas_maxima_connector {

    /** @var array Contains all system configuration, e.g. location of Maxima  */
    private $config;

    /** @var stack_options  */
    private $options;

    /** @var string This collects all debug information.  */
    private $debuginfo = '';

    public function __construct($options=null) {

        $this->config = $this->load_config();

        $this->options = $options;

        if (null===$options) {
            $this->options = new stack_options();
        }

        if (!is_a($this->options, 'stack_options')) {
            var_dump($options);
            throw new Exception('stack_cas_maxima_connector: options must be stack_options or null.');
        }
    }

    protected function load_config() {
        global $CFG;
        static $settings = null;

        if (is_null($settings)) {
            $settings = get_config('qtype_stack');
        }

        $path = $CFG->dataroot . '/stack';

        $initcommand = 'load("' . $path . '/maximalocal.mac");' . "\n";
        $initcommand = str_replace("\\", "/", $initcommand);
        $initcommand .= "\n";

        $cmd = $settings->maximacommand;
        if ('' == trim($cmd) ) {
            if ('win'==$settings->platform) {
                $cmd = $path . '/maxima.bat';
                if (!is_readable($cmd)) {
                    throw new Exception("stack_cas_maxima_connector: maxima launch script {$cmd} does not exist.");
                }
            } else {
                $cmd = 'maxima';
            }
        }

        return array(
                'platform'        => $settings->platform,
                'logs'            => $path,
                'command'         => $cmd,
                'init_command'    => $initcommand,
                'timeout'         => $settings->castimeout,
                'debug'           => $settings->casdebugging,
                'version'         => $settings->maximaversion,
        );
    }

    public function get_debuginfo() {
        return $this->debuginfo;
    }

    private function debug($heading, $message) {
        if (!$this->config['debug']) {
            return;
        }
        if ($heading) {
            $this->debuginfo .= html_writer::tag('h3', $heading);
        }
        if ($message) {
            $this->debuginfo .= html_writer::tag('pre', $message);
        }
    }

    /**
     * Deal with platforms, and send a string to Maxima.
     *
     * @param string $strin The raw Maxima command to be processed.
     * @return array
     */
    private function send_to_maxima($command) {

        $this->debug('Maxima command', $command);

        $platform = $this->config['platform'];

        if ($platform == 'win') {
            $result = $this->send_win($command);
        } else if (($platform == 'unix') || ($platform == 'server')) {
            // TODO:server mode currently falls back to launching a Maxima process.
            $result = $this->send_unix($command);
        } else {
            throw new Exception('stack_cas_maxima_connector: Unknown platform '.$platform);
        }

        $this->debug('CAS result', $result);

        return $result;
    }

    /**
     * Starts a instance of maxima and sends the maxima command under a Windows OS
     *
     * @param string $strin
     * @return string
     * @access public
     */
    private function send_win($command) {
        $ret = false;

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('file', $this->config['logs']."cas_errors.txt", 'a'));

        $cmd = '"'.$this->config['command'].'"';
        $this->debug('Command line', $cmd);

        $casprocess = proc_open($cmd, $descriptors, $pipes);
        if (!is_resource($casprocess)) {
            throw new Exception('stack_cas_maxima_connector: Could not open a CAS process.');
        }

        if (!fwrite($pipes[0], $this->config['init_command'])) {
            //echo "<br />Could not write to the CAS process!<br/ >\n";
            //$this->logger->critical('Could not write to CAS process '.$cmd);
            return(false);
        }
        fwrite($pipes[0], $command);
        fwrite($pipes[0], 'quit();\n\n');
        fflush($pipes[0]);

        // read output from stdout
        $ret = '';
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

        return trim($ret);
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
    private function send_unix($strin) {
        // Sends the $st to maxima.

        $ret = false;
        $err = '';
        $cwd = null;
        $env = array('why'=>'itworks');

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'));
        $casprocess = proc_open($this->config['command'], $descriptors, $pipes, $cwd, $env);
        /*$proc_array = proc_get_status($CASProcess);
         echo $proc_array['command'].'<br />';
        echo $proc_array['pid'].'<br />';
        echo $proc_array['running'].'<br />';*/

        if (!is_resource($casprocess)) {
            throw new Exception('stack_cas_maxima_connector: could not open a CAS process');
        }

        if (!fwrite($pipes[0], $this->config['init_command'])) {
            echo "<br />Could not write to the CAS process!<br />\n";
            //$this->logger->critical('Could not write to the CAS process: '.$cmd);
            return(false);
        }
        fwrite($pipes[0], $strin);
        fwrite($pipes[0], 'quit();'."\n\n");

        $ret = '';
        // read output from stdout
        $start_time = microtime(true);
        $continue   = true;

        if (!stream_set_blocking($pipes[1], false)) {
            $this->debug('', 'Warning: could not stream_set_blocking to be FALSE on the CAS process.');
        }

        while ($continue and !feof($pipes[1])) {

            $now = microtime(true);

            if (($now-$start_time) > $this->config['timeout']) {
                $proc_array = proc_get_status($casprocess);
                if ($proc_array['running']) {
                    proc_terminate($casprocess);
                }
                $continue = false;
            } else {
                $out = fread($pipes[1], 1024);
                if ('' == $out) {
                    // PAUSE
                    usleep(1000);
                }
                $ret .= $out;
            }

        }

        if ($continue) {
            fclose($pipes[0]);
            fclose($pipes[1]);
            $this->debug('Timings', "Start: {$start_time}, End: {$now}, Taken = " .
                    ($now - $start_time));

        } else {
            // Add sufficient closing ]'s to allow something to be un-parsed from the CAS.
            $ret .=' The CAS timed out. ] ] ] ]';
        }

        return $ret;
    }

    public function maxima_session($command) {

        $result = $this->send_to_maxima($command);
        $unp = $this->maxima_raw_session($result);

        $this->debug('Unpacked result as', print_r($unp, true));

        return $unp;
    }

    /*
     * Top level Maxima-specific function used to parse CAS output into an array.
     *
     * @param array $strin Raw CAS output
     * @return array
     */
    private function maxima_raw_session($strin) {
        $result = '';
        $errors = false;
        //check we have a timestamp & remove everything before it.
        $ts = substr_count($strin, '[TimeStamp');
        if ($ts != 1) {
            $this->debug('', 'receive_raw_maxima: no timestamp returned.');
            return array();
        } else {
            $result = strstr($strin, '[TimeStamp'); //remove everything before the timestamp
        }

        $result = trim(str_replace('#', '', $result));
        $result = trim(str_replace("\n", '', $result));

        $unp = $this->maxima_unpack_helper($result);

        if (array_key_exists('Locals', $unp)) {
            $uplocs = $unp['Locals']; // Grab the local variables
            unset($unp['Locals']);
        } else {
            $uplocs = '';
        }

        $rawlocals = $this->maxima_unpack_helper($uplocs);
        // Now we need to turn the (error,key,value,display) tuple into an array
        foreach ($rawlocals as $var => $valdval) {
            if (is_array($valdval)) {
                $errors["CAS"] = "CAS failed to generate any useful output.";
            } else {
                if (preg_match('/.*\[.*\].*/', $valdval)) {
                    // There are some []'s in the string.
                    $loc = $this->maxima_unpack_helper($valdval);
                    if ('' == trim($loc['error'])) {
                        unset($loc['error']);
                    }
                    $locals[]=$loc;

                } else {
                    $errors["LocalVarGet$var"] = "Couldn't unpack the local variable $var from the string $valdval.";
                }
            }
        }

        // Next process and tidy up these values.
        for ($i=0; $i < count($locals); $i++) {

            if (isset($locals[$i]['error'])) {
                $locals[$i]['error'] = $this->tidy_error($locals[$i]['error']);
            } else {
                $locals[$i]['error'] = '';
            }
            $plot = isset($locals[$i]['display']) ? substr_count($locals[$i]['display'], '<img') : 0; // if theres a plot being returned
            if ($plot > 0) {
                //plots always contain errors, so remove
                $locals[$i]['error'] = '';
                //for mathml display, remove the mathml that is inserted wrongly round the plot.
                $locals[$i]['display'] = str_replace('<math xmlns=\'http://www.w3.org/1998/Math/MathML\'>', '', $locals[$i]['display']);
                $locals[$i]['display'] = str_replace('</math>', '', $locals[$i]['display']);

                // for latex mode, remove the mbox
                // handles forms: \mbox{image} and (earlier?) \mbox{{} {image} {}}
                $locals[$i]['display'] = preg_replace("|\\\mbox{({})? (<html>.+</html>) ({})?}|", "$2", $locals[$i]['display']);
            }
        }
        return $locals;
    }


    private function maxima_unpack_helper($strin) {
        // Take the raw string from the CAS, and unpack this into an array.
        $offset = 0;
        $strin_len = strlen($strin);
        $unparsed = '';
        $errors = '';

        if ($eqpos = strpos($strin, '=', $offset)) {
            // Check there are ='s
            do {
                $s = new STACK_StringUtil('');
                $gb = $s->util_grabbetween($strin, '[', ']', $eqpos);
                $val = substr($gb[0], 1, strlen($gb[0])-2);
                $val = str_replace('"', '', $val);
                $val = trim($val);

                if (preg_match('/[A-Za-z0-9].*/', substr($strin, $offset, $eqpos-$offset), $regs)) {
                    $var = trim($regs[0]);
                } else {
                    $var = 'errors';
                    $errors['LOCVARNAME'] = "Couldn't get the name of the local variable.";
                }

                $unparsed[$var] = $val;
                $offset = $gb[2];
            } while (($eqpos = strpos($strin, '=', $offset)) && ($offset < $strin_len));

        } else {
            $errors['PREPARSE'] = "There are no ='s in the raw output from the CAS!";
        }

        if ('' != $errors) {
            $unparsed['errors'] = $errors;
        }

        return($unparsed);
    }

    /**
     * Deals with Maxima errors.   Enables some translation.
     *
     * @param string $errstr a Maxima error string
     * @return string
     */
    private function tidy_error($errstr) {
        if (strpos($errstr, '0 to a negative exponent') !== false) {
            $errstr = stack_string('Maxima_DivisionZero');
        }
        return $errstr;
    }

    /**
     * Sends a answertest to Maxima
     *
     * @param string $student
     * @param string $teacher
     * @param string $anstest
     * @return array
     */
    public function maxima_answer_test($exp1, $exp2, $anstest) {

        $cas_options = $this->options->get_cas_commands();
        $csnames = $cas_options['names'];
        $csvars  = $cas_options['commands'];

        $cs  = "cab:block([ STACK_SA, STACK_TA $csnames] $csvars,";
        $cs .= " print(\"[TimeStamp = [ 123 ], Ans= [ error = [\"), STACK_SA:cte(\"STACK_SA\",errcatch($exp1)),";
        $cs .= " print(\" TAAns= [ error = [\"), STACK_TA:cte(\"STACK_TA\",errcatch(STACK_TA:$exp2)),";
        $cs .= " print(\" AnswerTestError = [  \"), str:StackReturn($anstest(STACK_SA,STACK_TA)), print(\" ], \"), print(str), return(true)); \n";

        $rawresult = $this->send_to_maxima($cs);

        $result = $this->maxima_raw_answer_test($rawresult);

        return $result;
    }

    private function maxima_raw_answer_test($instr) {

        $unp = $this->maxima_unpack_helper($instr);

        if (array_key_exists('error', $unp)) {
            $unp['error']=$this->tidy_error($unp['error']);
        } else {
            $unp['error']='';
        }

        if (array_key_exists('Ans', $unp)) {
            $unp['Ans'] = $this->maxima_unpack_helper($unp['Ans']);

            if (''==$unp['Ans']['error']) {
                unset($unp['Ans']['error']);
            } else {
                $unp['error'] .= $this->tidy_error($unp['Ans']['error']);
            }
            unset($unp['Ans']);
        }

        if (array_key_exists('TAAns', $unp)) {
            $unp['TAAns'] = $this->maxima_unpack_helper($unp['TAAns']);

            if (''!=$unp['TAAns']['error']) {
                $unp['error'].=$this->tidy_error($unp['TAAns']['error']);
            }
            unset($unp['TAAns']);
        }

        if (array_key_exists('AnswerTestError', $unp)) {
            if (''!=$unp['AnswerTestError']) {
                $unp['error'].=$unp['AnswerTestError'];
            }
        }
        unset($unp['AnswerTestError']);

        /* If the fields are not here, Maxima didn't generate them = problem! */
        if (!array_key_exists('result', $unp)) {
            $unp['result']     = 0;
            $unp['valid']      = 0;
            if (array_key_exists('feedback', $unp)) {
                $unp['feedback']   .= ' stack_trans("TEST_FAILED");';
            } else {
                $unp['feedback']    = ' stack_trans("TEST_FAILED");';
            }
            if (array_key_exists('answernote', $unp)) {
                $unp['answernote'] .= ' TEST_FAILED ';
            } else {
                $unp['answernote']  = ' TEST_FAILED';
            }
            if (array_key_exists('error', $unp)) {
                $unp['error']      .= ' TEST_FAILED';
            } else {
                $unp['error']       = ' TEST_FAILED';
            }
        } else {
            if (''==$unp['result']) {
                $unp['result']     = 0;
                $unp['valid']      = 0;
                $unp['answernote'] .= ' EMPTY_RESULT ';
            }
        }

        //Result comes back as a string.  We need to change this to integers.
        $unp['result'] = intval($unp['result']);
        $unp['valid']  = intval($unp['valid']);

        return $unp;
    }

}
