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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * This File defines various classes and functions present in moodle,
 * to reduce the amount of modifications neccessary to run stack standalone
 */

define('MOODLE_INTERNAL', true);
define('PARAM_RAW', 'raw');
define('PARAM_PLUGIN', true);

require_once('../config.php');
// Required to pass Moodle code check.
require_login();

class moodle_exception extends Exception {
    public function __construct($a1, $a2, $a3, $error) {
        parent::__construct($error);
    }
}

class question_graded_automatically_with_countback {
    public $defaultmark = 1;
}

interface question_automatically_gradable_with_multiple_parts {
}

function clean_param($in, $param) {
    return $in;
}

function get_config($component, $parameter = null) {
    global $CFG;
    if ($parameter === null) {
        return $CFG;
    }
    if (property_exists($CFG, $parameter)) {
        return $CFG->$parameter;
    }
    new stack_exception("Could not locate the following property in the global config: " . $parameter);
}

/**
 * Add quotes to HTML characters.
 *
 * Returns $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link p()}
 *
 * @param string $var the string potentially containing HTML characters
 * @return string
 */
function s($var) {

    if ($var === false) {
        return '0';
    }

    // When we move to PHP 5.4 as a minimum version, change ENT_QUOTES on the
    // next line to ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, and remove the
    // 'UTF-8' argument. Both bring a speed-increase.
    return preg_replace('/&amp;#(\d+|x[0-9a-f]+);/i', '&#$1;', htmlspecialchars($var, ENT_QUOTES, 'UTF-8'));
}

function format_text($text) {
    return $text;
}

function require_login() {
    return;
}

function get_file_storage() {
    $storage = new class {
        public function get_area_files($x, $y, $z, $a) {
            return [];
        }
    };
    return $storage;
}

// Specialized emulations.
require_once('Constants.php');
require_once('Localization.php');
require_once('HtmlWriter.php');
require_once('Renderer.php');
