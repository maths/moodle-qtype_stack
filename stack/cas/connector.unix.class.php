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
require_once(__DIR__ . '/platforms.php');
/**
 * Connection to Maxima for unix-like systems.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_connection_unix extends stack_cas_connection_base {

    /**
     * Connect directly to the CAS, and return the raw string result.
     *
     * @param string $command The string of CAS commands to be processed.
     * @param boolean $bypassinit Do not execute $this->initcommand - this usually
     * avoids loading maximalocal.mac; this is useful during install and when poking
     * maxima for build_info.
     * @return string|bool The raw results or FALSE if there was an error.
     * @throws stack_exception
     */
    protected function call_maxima($command, $bypassinit=false) {
        $ret = false;
        $err = '';
        $cwd = null;
        $newpath = getenv('PATH');
        $env = array('PATH' => $newpath);

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'));
        $casprocess = proc_open($this->command, $descriptors, $pipes, $cwd, $env);

        if (!is_resource($casprocess)) {
            throw new stack_exception('stack_cas_connection: could not open a CAS process');
        }

        if(!$bypassinit) {
            if (!fwrite($pipes[0], $this->initcommand)) {
                throw new stack_exception('stack_cas_connection: could not write to the CAS process.');
            }
        }
        fwrite($pipes[0], $command);
        fwrite($pipes[0], 'quit();'."\n\n");

        $ret = '';
        // Read output from stdout.
        $starttime = microtime(true);
        $continue   = true;

        if (!stream_set_blocking($pipes[1], false)) {
            $this->debug->log('', 'Warning: could not stream_set_blocking to be FALSE on the CAS process.');
        }

        while ($continue and !feof($pipes[1])) {

            $now = microtime(true);

            if (($now - $starttime) > $this->timeout) {
                $procarray = proc_get_status($casprocess);
                if ($procarray['running']) {
                    proc_terminate($casprocess);
                }
                $continue = false;
            } else {
                $out = fread($pipes[1], 1024);
                if ('' == $out) {
                    // Pause.
                    usleep(1000);
                }
                $ret .= $out;
            }

        }

        if ($continue) {
            fclose($pipes[0]);
            fclose($pipes[1]);
            $this->debug->log('Timings', "Start: {$starttime}, End: {$now}, Taken = " .
                    ($now - $starttime));

        } else {
            // Add sufficient closing ]'s to allow something to be un-parsed from the CAS.
            // WARNING: the string 'The CAS timed out' is used by the cache to search for a timeout occurrence.
            $ret .= ' The CAS timed out. ] ] ] ]';
        }

        return $ret;
    }

    /* On a Unix system list the versions of maxima available for use. */
    public function get_maxima_available() {
        $this->command = 'maxima --list-avail';
        $rawresult = $this->call_maxima('', true);
        return $rawresult;
    }
}
