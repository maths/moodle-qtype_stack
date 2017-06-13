<?php

require_once(__DIR__ . '/stack/mathsoutput/mathsoutput.class.php');

function stack_string($key, $a = null) {

    $user_language = 'en';
    switch ($user_language)
    {
        case 'en':
            static $string = array();
            if (empty($string)) {
                include 'lang/en/qtype_stack.php';
            }
            break;
        default:
            static $string = array();
            if (empty($string)) {
                include 'lang/en/qtype_stack.php';
            }
            break;
    }

    return getString($key, $string, $a);
}

function getString($identifier, $string, $a = null) {
    $string = $string[$identifier];
    if ($a !== null) {
        if (is_object($a) or is_array($a)) {
            $a = (array)$a;
            $search = array();
            $replace = array();
            foreach ($a as $key => $value) {
                if (is_int($key)) {
                    // We do not support numeric keys - sorry!
                    continue;
                }
                $search[] = '{$a->' . $key . '}';
                $replace[] = (string) $value;
            }
            if ($search) {
                $string = str_replace($search, $replace, $string);
            }
        } else {
            $string = str_replace('{$a}', (string)$a, $string);
        }
    }

    return $string;
}

function stack_maxima_format_casstring($str) {
    return $str;
}

/**
 * Translates a string taken as output from Maxima.
 *
 * This function takes a variable number of arguments, the first of which is assumed to be the identifier
 * of the string to be translated.
 */
function stack_trans() {
    $nargs = func_num_args();

    if ($nargs > 0) {
        $arg_list = func_get_args();
        $identifier = func_get_arg(0);
        $a = array();
        if ($nargs > 1) {
            for ($i = 1; $i < $nargs; $i++) {
            $index = $i - 1;
            $a["m{$index}"] = func_get_arg($i);
        }
    }
    $return = stack_string($identifier, $a);
    echo $return;
}
}

function stack_maxima_translate($rawfeedback) {

    if (strpos($rawfeedback, 'stack_trans') === false) {
        return trim($rawfeedback);
    } else {
        $rawfeedback = str_replace('[[', '', $rawfeedback);
        $rawfeedback = str_replace(']]', '', $rawfeedback);
        $rawfeedback = str_replace('\n', '', $rawfeedback);
        $rawfeedback = str_replace('\\', '\\\\', $rawfeedback);
        $rawfeedback = str_replace('!quot!', '"', $rawfeedback);

        ob_start();
        eval($rawfeedback);
        $translated = ob_get_contents();
        ob_end_clean();

        return trim($translated);
    }
}

/**
 * You need to call this method on the string you get from
 * $castext->get_display_castext() before you echo it. This ensures that equations
 * are displayed properly.
 * @param string $castext the result of calling $castext->get_display_castext().
 * @return string HTML ready to output.
 */
function stack_ouput_castext($castext) {
    return stack_maths::process_display_castext($castext);
}

/**
* Exceptions.
*/

class stack_exception extends exception {
}
