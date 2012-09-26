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
 * Connection to Maxima running in a tomcat-server using the MaximaPool-servlet.
 *
 * @copyright  2012 The University of Birmingham
 * @copyright  2012 Aalto University - Matti Harjula
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_connection_tomcat extends stack_cas_connection_base {

    /* @see stack_cas_connection_base::guess_maxima_command() */
    protected function guess_maxima_command($path) {
        return 'http://localhost:8080/MaximaPool/MaximaPool';
    }

    /* @see stack_cas_connection_base::call_maxima() */
    protected function call_maxima($command) {
        $err = '';

        $start_time = microtime(true);

        $request = curl_init($this->command);

        $postdata = "input=".urlencode($command)."&timeout=".($this->timeout*1000);

        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($request);

        $timedout = false;

        // The servlet will return 416 if the evaluation hits the timelimit.
        if (curl_getinfo($request, CURLINFO_HTTP_CODE)!="200") {
            if (curl_getinfo($request, CURLINFO_HTTP_CODE)!="416") {
                throw new Exception('stack_cas_connection: MaximaPool error');
            } else {
                $timedout=true;
            }
        }

        curl_close($request);

        $now = microtime(true);

        $this->debug->log('Timings', "Start: {$start_time}, End: {$now}, Taken = ".($now - $start_time));

        // Add sufficient closing ]'s to allow something to be un-parsed from the CAS.
        // WARNING: the string 'The CAS timed out' is used by the cache to serach for a timout occurance.
        if ($timedout) {
            $ret .=' The CAS timed out. ] ] ] ]';
        }

        return $ret;
    }
}
