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
 * Defines the stack_cas_connection interface.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface stack_cas_connection {

    /**
     * Send some Maxima code to Maxima, and return the unpacked results.
     * @param string $command Maxima code to execute.
     * @return array the unpacked results returned by Maxima.
     */
    public function compute($command);

    /**
     * @return string any debug info from this session. Will be blank unless
     *      debugging is enabled by the configuration.
     */
    public function get_debuginfo();


    /**
     * Send some Maxima code to Maxima, and return the parsed results.
     * @param string $command Maxima code to execute.
     * @return array the unpacked results returned by Maxima.
     */
    public function json_compute($command): array;
}
