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
 * Connection to Maxima for Windows systems.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_connection_windows extends stack_cas_connection_base {

    protected function guess_maxima_command($path) {
        if ('default' == stack_connection_helper::get_maximaversion()) {
            throw new stack_exception("stack_cas_connection: maxima cannot be set to default on Windows platform. ".
                    "Please choose an explicit version via the administration settings page.");
        }
        $cmd = $path . '/maxima.bat';
        if (!is_readable($cmd)) {
            throw new stack_exception("stack_cas_connection: maxima launch script {$cmd} does not exist.");
        }
        return $cmd;
    }

    protected function call_maxima($command) {
        set_time_limit(0); // Note, some users may not want this!
        $ret = false;

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', $this->logs . "cas_errors.txt", 'a']];

        $cmd = '"'.$this->command.'"';
        $this->debug->log('Command line', $cmd);

        $casprocess = proc_open($cmd, $descriptors, $pipes);
        if (!is_resource($casprocess)) {
            throw new stack_exception('stack_cas_connection: Could not open a CAS process.');
        }

        if (!fwrite($pipes[0], $this->initcommand)) {
            return(false);
        }
        fwrite($pipes[0], $command);
        fwrite($pipes[0], 'quit();\n\n');
        fflush($pipes[0]);

        // Read output from stdout.
        $ret = '';
        while (!feof($pipes[1])) {
            $out = fgets($pipes[1], 1024);
            if ('' == $out) {
                // Pause.
                usleep(1000);
            }
            $ret .= $out;
        }
        fclose($pipes[0]);
        fclose($pipes[1]);

        return trim($ret);
    }
}
