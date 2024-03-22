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

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../lang/multilang.php');

function current_language() {
    $supportedlanguages = ['en', 'de'];

    return locale_lookup($supportedlanguages, $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', true, 'en');
}

function get_string($identifier, $component, $a = null) {
    $userlanguage = current_language();

    switch ($userlanguage)
    {
        case 'de':
            static $string = [];
            if (empty($string)) {
                // Load en values as defaults.
                include(__DIR__ .'/../../lang/en/qtype_stack.php');
                include(__DIR__ .'/../../lang/de/qtype_stack.php');
            }
            break;
        default:
            static $string = [];
            if (empty($string)) {
                include(__DIR__ .'/../../lang/en/qtype_stack.php');
            }
            break;
    }

    $localization = $string[$identifier];
    if ($a !== null) {
        if (is_object($a) || is_array($a)) {
            $a = (array)$a;
            $search = [];
            $replace = [];
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

// Used for multilanguage questions, retrusn dependencies between languages.
// We currently support only english and german, therefore this is not that relevant for us.
function get_string_manager() {
    return new class {
        public function get_language_dependencies($lang) {
            return [];
        }
    };
}
