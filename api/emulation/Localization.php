<?php

require_once(__DIR__ . '/../../lang/multilang.php');

function current_language() {
    $supported_languages = array('en', 'de');

    return locale_lookup($supported_languages, $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', true, 'en');
}

function get_string($identifier, $component, $a = null) {
    $userlanguage = current_language();

    switch ($userlanguage)
    {
        case 'de':
            static $string = array();
            if (empty($string)) {
                //Load en values as defaults
                include(__DIR__ .'/../../lang/en/qtype_stack.php');
                include(__DIR__ .'/../../lang/de/qtype_stack.php');
            }
            break;
        default:
            static $string = array();
            if (empty($string)) {
                include(__DIR__ .'/../../lang/en/qtype_stack.php');
            }
            break;
    }


    $localization = $string[$identifier];
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
                $localization = str_replace($search, $replace, $localization);
            }
        } else {
            $localization = str_replace('{$a}', (string)$a, $localization);
        }
    }

    return $localization;
}

//Used for multilanguage questions, retrusn dependencies between languages
//We currently support only english and german, therefore this is not that relevant for us
function get_string_manager() {
    return new class {
        public function get_language_dependencies($lang) {
            return array();
        }
    };
}
