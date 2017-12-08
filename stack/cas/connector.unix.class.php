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

/**
 * Connection to Maxima for unix-like systems.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_connection_unix extends stack_cas_connection_base {

    protected function guess_maxima_command($path) {
        global $CFG;
        if (stack_connection_helper::get_platform() == 'unix-optimised') {
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
        $ret = false;
        $err = '';
        $cwd = null;
        // TODO: Think this through:
        //  1. Originally we gave a blank environment except for PATH.
        //  2. Now we would set certain initial values and merge those to the underlying one which is already a change probably not to worse but a change still.
        //  3. To correct that we then allow those changes to be overwritten through the configuration parameter.
        // Are the changes in 2. a problem? Should we not do them even though they might silently fix many installations? Is the risk of them breaking anything realistic?

        // First initialise env with generic guesses then overwrite them with real env.
        $env = array('LC_CTYPE' => 'en_GB.UTF-8', 'LANG' => 'en_GB.UTF-8', 'LANGUAGE' => 'en_GB:en', 'MM_CHARSET' => 'UTF-8');
        if (getenv('PATH') !== false) {
            $env['PATH'] = getenv('PATH');
        }
        if (getenv('LC_CTYPE') !== false) {
            $env['LC_CTYPE'] = getenv('LC_CTYPE');
        }
        if (getenv('LANG') !== false) {
            $env['LANG'] = getenv('LANG');
        }
        if (getenv('LANGUAGE') !== false) {
            $env['LANGUAGE'] = getenv('LANGUAGE');
        }
        if (getenv('MM_CHARSET') !== false) {
            $env['MM_CHARSET'] = getenv('MM_CHARSET');
        }
        // After that inject overrides.
        foreach($this->env as $key => $value) {
            $env[$key] = $value;
        }

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'));
        $casprocess = proc_open($this->command, $descriptors, $pipes, $cwd, $env);

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
}
