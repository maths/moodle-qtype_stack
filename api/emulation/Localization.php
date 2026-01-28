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
 * Localisation emulation for STACK API.
 *
 * @package    qtype_stack
 * @copyright  2023 RWTH Aachen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../lang/multilang.php');
require_once(__DIR__ . '/Language.php');

use ApiLanguage;

// phpcs:ignore moodle.Commenting.MissingDocblock.Function
function current_language() {
    $requestheader = ($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en';
    return ApiLanguage::api_current_language($requestheader);
}

// phpcs:ignore moodle.Commenting.MissingDocblock.Function
function get_string($identifier, $component, $a = null) {
    $userlanguage = current_language();

    static $string = [];
    switch ($userlanguage) {
        case 'en':
            if (empty($string)) {
                // Load en values as defaults.
                include(__DIR__ . '/../../lang/en/qtype_stack.php');
            }
            break;
        default:
            if (empty($string)) {
                $variant = $userlanguage;
                $region = ApiLanguage::get_next_parent_language($variant);
                $language = ApiLanguage::get_next_parent_language($region);
                // Load en values as defaults.
                include(__DIR__ . '/../../lang/en/qtype_stack.php');
                if ($language !== 'en' && is_file(__DIR__ . "/../../lang/{$language}/qtype_stack.php")) {
                    include(__DIR__ . "/../../lang/{$language}/qtype_stack.php");
                }
                if ($region !== $language && is_file(__DIR__ . "/../../lang/{$region}/qtype_stack.php")) {
                    include(__DIR__ . "/../../lang/{$region}/qtype_stack.php");
                }
                if ($variant !== $region && is_file(__DIR__ . "/../../lang/{$variant}/qtype_stack.php")) {
                    include(__DIR__ . "/../../lang/{$variant}/qtype_stack.php");
                }
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
// phpcs:ignore moodle.Commenting.MissingDocblock.Function
function get_string_manager() {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Class
    return new class {
        // phpcs:ignore moodle.Commenting.MissingDocblock.Function
        public function get_language_dependencies($lang) {
            return [];
        }
    };
}
