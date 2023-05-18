<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk//
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

// This is a dirty hack trying to remove the need to configure webservers
// to set the correct CORS headers for the few scripts in this directory that need them.

// We cannot have moodle internal in this script.
// @codingStandardsIgnoreStart

$scriptname = urldecode($_GET['name']);

if (strpos('..', $scriptname) !== false
    || strpos('/', $scriptname) !== false
    || strpos('\\', $scriptname) !== false) {
    die("No such script here.");
}

if (file_exists($scriptname)) {
    header('HTTP/1.0 200 OK');
    if (strrpos($scriptname, '.js') === strlen($scriptname) - 3) {
        header('Content-Type: text/javascript;charset=UTF-8');
    } else if (strrpos($scriptname, '.css') === strlen($scriptname) - 4) {
        header('Content-Type: text/css;charset=UTF-8');
    }
    header('Cache-Control: public, max-age=31104000, immutable');
    header('Access-Control-Allow-Origin: *');
    echo(file_get_contents($scriptname));
} else {
    // Give the same error to stop people from trying to figure out
    // whether a given file exists, even when placed in a bad place.
    die("No such script here.");
}

// @codingStandardsIgnoreEnd