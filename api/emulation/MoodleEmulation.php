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
define('PARAM_URL', true);

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

/**
 * Fetches content of file from Internet.
 * Due to security concerns only downloads from http(s) sources are supported.
 * (This is extremely stripped down from the Moodle function in lib/filelib.php which
 * utilises the Moodle curl class.
 * https://github.com/totara/moodle/blob/dda862abb57f656633f0736b858f7f048efd44bb/lib/filelib.php#L1172)
 *
 * @param string $url file url starting with http(s)://
 * @return string|bool false if request failed or the file content as a string.
 */
function download_file_content($url) {
    global $CFG;

    // Only http and https links supported.
    if (!preg_match('|^https?://|i', $url)) {
        return false;
    }

    $options = [];
    $options[CURLOPT_SSL_VERIFYPEER] = 1;
    $options[CURLOPT_CONNECTTIMEOUT] = 20;
    $options[CURLOPT_FOLLOWLOCATION] = 1;
    $options[CURLOPT_MAXREDIRS] = 5;
    $options[CURLOPT_RETURNTRANSFER] = true;
    $options[CURLOPT_NOBODY] = false;
    $options[CURLOPT_TIMEOUT] = 300;
    $options[CURLOPT_HTTPGET] = 1;
    $options[CURLOPT_URL] = $url;
    $curl = curl_init();
    foreach ($options as $name => $value) {
        curl_setopt($curl, $name, $value);
    }

    $result = curl_exec($curl);
    $info  = curl_getinfo($curl);
    $error_no = curl_errno($curl);

    if ($error_no) {
        return false;
    }
    if (empty($info['http_code'])) {
        // For security reasons we support only true http connections (Location: file:// exploit prevention).
        return false;
    }
    if ($info['http_code'] != 200) {
        return false;
    }

    return $result;
}

// Specialized emulations.
require_once('Constants.php');
require_once('Localization.php');
require_once('HtmlWriter.php');
require_once('Renderer.php');
