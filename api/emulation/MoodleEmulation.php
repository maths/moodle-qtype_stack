<?php

/**
 * This File defines various classes and functions present in moodle, to reduce the amount of modifications neccessary to run stack standalone
 */

define('STACK_API', true);

define('MOODLE_INTERNAL', true);
define('PARAM_RAW', 'raw');
define('PARAM_PLUGIN', true);

require_once('../config.php');

class moodle_exception extends Exception {
    public function __construct($a1, $a2, $a3, $error) {
        parent::__construct($error);
    }
}

class question_graded_automatically_with_countback {
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


//Specialized emulations
require_once('Constants.php');
require_once('Localization.php');
require_once('HtmlWriter.php');
