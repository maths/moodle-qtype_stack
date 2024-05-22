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

/**
 * Connection to Maxima for linux-like systems.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_connection_linux extends stack_cas_connection_base {

    protected function guess_maxima_command($path) {
        global $CFG;
        if (stack_connection_helper::get_platform() == 'linux-optimised') {
            // We are trying to use a Lisp snapshot of Maxima with all the
            // STACK libraries loaded.
            $lispimage = $CFG->dataroot . '/stack/maxima-optimised';
            if (is_readable($lispimage)) {
                return $lispimage;
            }
        }

        if (is_readable('/Applications/Maxima.app/Contents/Resources/maxima.sh')) {
            // This is the path on Macs, if Maxima has been installed following
            // the instructions on Sourceforge.
            return '/Applications/Maxima.app/Contents/Resources/maxima.sh';
        }

        // Default guess on Linux, making explicit use of the chosen version number.
        $maximaversion = stack_connection_helper::get_maximaversion();
        $maximacommand = 'maxima';
        if ('default' != $maximaversion) {
            $maximacommand = 'maxima --use-version='.$maximaversion;
        }
        return $maximacommand;
    }

    protected function call_maxima($command) {
        // For unicode support, we need to add something like 'LANG' => 'en_GB.UTF-8' to the environment below.
        $env = ['PATH' => getenv('PATH')];

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
        ];
        $casprocess = proc_open($this->command . ' 2>&1', $descriptors, $pipes, null, $env);

        if (!is_resource($casprocess)) {
            throw new stack_exception('stack_cas_connection: could not open a CAS process');
        }

        if (!fwrite($pipes[0], $this->initcommand)) {
            throw new stack_exception('stack_cas_connection: could not write to the CAS process.');
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

        while ($continue && !feof($pipes[1])) {

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
            $returnvalue = proc_close($casprocess);

            $this->debug->log('CAS process return value: ' . $returnvalue);

            $this->debug->log('Timings', "Start: {$starttime}, End: {$now}, Taken = " .
                    ($now - $starttime));

        } else {
            // Add sufficient closing ]'s to allow something to be un-parsed from the CAS.
            // WARNING: the string 'The CAS timed out' is used by the cache to search for a timeout occurrence.
            $ret .= ' The CAS timed out. ] ] ] ]';
        }

        return $ret;
    }
}
