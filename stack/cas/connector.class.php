<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../cas/connector.interface.php');
require_once(__DIR__ . '/platforms.php');


/**
 * The base class for connections to Maxima.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class stack_cas_connection_base implements stack_cas_connection {
    /**
     *
     * @var stack_platform_base The platform this connection uses / services.
     */
    protected $platform;

    /** @var string path to write Maxiam error output to. */
    protected $logs;

    /** @var string the name of the maxima executable, to use of command-lines. */
    protected $command;

    /** @var string the username:passpassword to use when connecting to the MaximaPool server, if required. */
    protected $serveruserpass = '';

    /** @var string the opening command to send to maxima. */
    protected $initcommand;

    /** @var int the timeout to use on connections to Maxima. */
    protected $timeout;

    /** @var stack_debug_log does the debugging. */
    protected $debug;

    /**
     * @var bool whether $CFG->wwwroot contains any '_' characters. If so,
     *      unpack will need to work-around a Maxima issue that replaces _ with \_ output.
     */
    protected $wwwroothasunderscores;

    /** @var string strings to replace in relation to $wwwroothasunderscores. */
    protected $wwwrootfixupfind;

    /** @var string replacement strings in relation to $wwwroothasunderscores. */
    protected $wwwrootfixupreplace;

    // @codingStandardsIgnoreStart
    /* @see stack_cas_connection::compute() */
    // @codingStandardsIgnoreEnd
    public function compute($command) {

        $context = "Platform: ". stack_connection_helper::get_platform() . "\n";
        $context .= "Maxima shell command: ". $this->command . "\n";;
        $context .= "Maxima initial command: ". $this->initcommand . "\n";
        $context .= "Maxima timeout: ". $this->timeout;
        $this->debug->log('Context used', $context);

        $this->debug->log('Maxima command', $command);

        $rawresult = $this->call_maxima($command);
        $this->debug->log('CAS result', $rawresult);

        $unpackedresult = $this->unpack_raw_result($rawresult);
        // @codingStandardsIgnoreStart
        $this->debug->log('Unpacked result as', print_r($unpackedresult, true));
        // @codingStandardsIgnoreEnd

        if (!stack_connection_helper::check_stackmaxima_version($unpackedresult)) {
            stack_connection_helper::warn_about_version_mismatch($this->debug);
        }

        return $unpackedresult;
    }
    
    /**
     * Get raw version of the connection.
     * 
     * @return stack_cas_connection_base The default implementation - for raw connections; 
     * just returns $this.
     */
    public function get_raw() {
        return $this;
    }

    // @codingStandardsIgnoreStart
    /* @see stack_cas_connection::get_debuginfo() */
    // @codingStandardsIgnoreEnd
    public function get_debuginfo() {
        return $this->debug->get_log();
    }

    /* By default, platforms cannot list available versions. */
    public function get_maxima_available() {
        return stack_string('healthunabletolistavail', $this->platform->get_name());
    }
    
    /**
     * Connect directly to the CAS, and return the raw string result.
     *
     * @param string $command The string of CAS commands to be processed.
     * @return string|bool The raw results or FALSE if there was an error.
     */
    protected abstract function call_maxima($command);

    /**
     * Constructor.
     * @param stdClass $settings the Maxima configuration settings.
     * @param stack_debug_log $debuglog the debug log to use.
     */
    public function __construct($settings, stack_debug_log $debuglog, $platform) {
        global $CFG;

        $this->platform = $platform;
        $path = $CFG->dataroot . '/stack';

        $initcommand = 'load("' . $path . '/maximalocal.mac");' . "\n";
        $initcommand = str_replace("\\", "/", $initcommand);
        $initcommand .= "\n";

        $cmd = $this->platform->get_maxima_command();

        $this->logs           = $path;
        $this->command        = $cmd;
        $this->initcommand    = $initcommand;
        $this->timeout        = $settings->castimeout;
        $this->serveruserpass = $settings->serveruserpass;
        $this->debug          = $debuglog;
        if (strpos($CFG->wwwroot, '_') !== false) {
            $this->wwwroothasunderscores = true;
            $this->wwwrootfixupfind = str_replace('_', '\_', $CFG->wwwroot);
            $this->wwwrootfixupreplace = $CFG->wwwroot;
        } else {
            $this->wwwroothasunderscores = false;
        }
    }

    /**
     * Top level Maxima-specific function used to parse CAS output into an array.
     *
     * @param string $rawresult Raw CAS output
     * @return array
     */
    protected function unpack_raw_result($rawresult) {
        $result = '';
        $errors = false;
        // This adds sufficient closing brackets to make sure we have enough to match.
        $rawresult .= ']]]]';
        if ('' == trim($rawresult)) {
            $this->debug->log('Warning, empty result!', 'unpack_raw_result: completely empty result was returned by the CAS.');
            return array();
        }

        // Check we have a timestamp & remove everything before it.
        $ts = substr_count($rawresult, '[TimeStamp');
        if ($ts != 1) {
            $this->debug->log('', 'unpack_raw_result: no timestamp returned. Data returned was: '.$rawresult);
            return array();
        } else {
            $result = strstr($rawresult, '[TimeStamp'); // Remove everything before the timestamp.
        }

        $result = trim(str_replace("\n ", '', $result));
        $result = trim(str_replace("\n", '', $result));

        $unp = $this->unpack_helper($result);

        if (array_key_exists('Locals', $unp)) {
            $uplocs = $unp['Locals']; // Grab the local variables.
            unset($unp['Locals']);
        } else {
            $uplocs = '';
        }

        // Now we need to turn the (error,key,value,display) tuple into an array.
        $locals = array();

        foreach ($this->unpack_helper($uplocs) as $var => $valdval) {
            if (is_array($valdval)) {
                $errors["CAS"] = "unpack_raw_result: CAS failed to generate any useful output.";
            } else {
                if (preg_match('/.*\[.*\].*/', $valdval)) {
                    // There are some []'s in the string.
                    $loc = $this->unpack_helper($valdval);
                    if ('' == trim($loc['error'])) {
                        unset($loc['error']);
                    }
                    $locals[$var] = $loc;

                } else {
                    $errors["LocalVarGet$var"] = "Couldn't unpack the local variable $var from the string $valdval.";
                }
            }
        }

        // Next process and tidy up these values.
        foreach ($locals as $i => &$local) {

            if (isset($local['error'])) {
                $local['error'] = $this->tidy_error($local['error']);
            } else {
                $local['error'] = '';
            }
            // If there are plots in the output.
            $plot = isset($local['display']) ? substr_count($local['display'], '!ploturl!') : 0;
            if ($plot > 0) {
                // Plots always contain errors, so remove.
                $local['error'] = '';
                // For mathml display, remove the mathml that is inserted wrongly round the plot.
                $local['display'] = str_replace('<math xmlns=\'http://www.w3.org/1998/Math/MathML\'>',
                    '', $local['display']);
                $local['display'] = str_replace('</math>', '', $local['display']);

                // @codingStandardsIgnoreStart
                // For latex mode, remove the mbox.
                // This handles forms: \mbox{image} and (earlier?) \mbox{{} {image} {}}.
                // @codingStandardsIgnoreEnd
                $local['display'] = preg_replace("|\\\mbox{({})? (<html>.+</html>) ({})?}|", "$2", $local['display']);

                if ($this->wwwroothasunderscores) {
                    $local['display'] = str_replace($this->wwwrootfixupfind,
                            $this->wwwrootfixupreplace, $local['display']);
                }
            }
            foreach ($local as $key => $val) {
                $local[$key] = trim(str_replace('!NEWLINE!', '', $val));
            }
        }
        return $locals;
    }


    protected function unpack_helper($rawresultfragment) {
        // Take the raw string from the CAS, and unpack this into an array.
        $offset = 0;
        $rawresultfragmentlen = strlen($rawresultfragment);
        $unparsed = array();
        $errors = '';

        $eqpos = strpos($rawresultfragment, '=', $offset);
        if ($eqpos) {
            // Check there are ='s.
            do {
                $gb = stack_utils::substring_between($rawresultfragment, '[', ']', $eqpos);
                $val = substr($gb[0], 1, strlen($gb[0]) - 2);
                $val = trim($val);

                if (preg_match('/[-A-Za-z0-9].*/', substr($rawresultfragment, $offset, $eqpos - $offset), $regs)) {
                    $var = trim($regs[0]);
                } else {
                    $var = 'errors';
                    $errors['LOCVARNAME'] = "Couldn't get the name of the local variable.";
                }
                $unparsed[$var] = $val;
                $offset = $gb[2];
            } while (($offset >= 0) && ($offset < $rawresultfragmentlen) && ($eqpos = strpos($rawresultfragment, '=', $offset)));

        } else {
            $errors['PREPARSE'] = "There are no ='s in the raw output from the CAS!";
        }

        if ('' != $errors) {
            $unparsed['errors'] = $errors;
        }

        return $unparsed;
    }

    /**
     * Deals with Maxima errors. Enables some translation.
     *
     * @param string $errstr a Maxima error string
     * @return string
     */
    protected function tidy_error($errstr) {

        if ('' === trim($errstr)) {
            return '';
        }

        $error = explode("!NEWLINE!", $errstr);
        $errorclean = array();
        foreach ($error as $err) {
            // This case arises when we use a numerical text for algebraic equivalence.
            if (strpos($err, 'STACK: ignore previous error.') !== false) {
                $err = '';
            }

            if (strpos($err, '0 to a negative exponent') !== false) {
                $err = stack_string('Maxima_DivisionZero');
            }

            if (strpos($err, 'args: argument must be a non-atomic expression;') !== false) {
                $err = stack_string('Maxima_Args');
            }

            $errorclean[] = $err;
        }

        return trim(implode(" ", $errorclean));
    }

}
