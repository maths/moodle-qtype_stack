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

/**
 * This is a dirty hack trying to remove the need to configure webservers
 * to set the correct CORS headers for the few scripts in this directory that need them.
 *
 * @package    qtype_stack
 * @copyright  2023 RWTH Aachen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// This is a dirty hack trying to remove the need to configure webservers
// to set the correct CORS headers for the few scripts in this directory that need them.

// We cannot have moodle internal in this script.
// @codingStandardsIgnoreStart
require_once('../config.php');
$scriptname = urldecode($_GET['name']);
if (isset($_GET['question'])) {
    $is_question = urldecode($_GET['question']) == 'true' ? true : false;
} else {
    $is_question = false;
}

if (strpos($scriptname, '..') !== false
    || strpos($scriptname, '/') !== false
    || strpos($scriptname, '\\') !== false) {
        // Give a special exception for sample questions.
        if (!($is_question && file_exists('../../samplequestions/' . $scriptname))) {
            die("No such script here.");
        }
}

if (file_exists('../../corsscripts/' . $scriptname) || $scriptname === 'styles.css'
    || ($is_question && file_exists('../../samplequestions/' . $scriptname))) {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('HTTP/1.0 204 OK');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Max-Age: 86400');
        header('Connection: keep-alive');
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header('HTTP/1.0 200 OK');
        if (strrpos($scriptname, '.js') === strlen($scriptname) - 3) {
            header('Content-Type: text/javascript;charset=UTF-8');
        } else if (strrpos($scriptname, '.css') === strlen($scriptname) - 4) {
            header('Content-Type: text/css;charset=UTF-8');
        } else if (strrpos($scriptname, '.xml') === strlen($scriptname) - 4) {
            header('Content-Type: text/xml;charset=UTF-8');
        }
        header('Cache-Control: public, max-age=31104000, immutable');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        if ($scriptname === 'styles.css') {
            echo(file_get_contents('../../' . $scriptname));
        } else if ($is_question) {
            echo(file_get_contents('../../samplequestions/' . $scriptname));
        } else {
            echo(file_get_contents('../../corsscripts/' . $scriptname));
        }
    }
} else {
    // Give the same error to stop people from trying to figure out
    // whether a given file exists, even when placed in a bad place.
    die("No such script here.");
}

// @codingStandardsIgnoreEnd
