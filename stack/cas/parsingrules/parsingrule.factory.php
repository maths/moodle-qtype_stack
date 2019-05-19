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

defined('MOODLE_INTERNAL')|| die();

require_once(__DIR__ . '/filter.interface.php');
require_once(__DIR__ . '/pipeline.class.php');

require_once(__DIR__ . '/002_log_candy.php');
require_once(__DIR__ . '/010_single_char_vars.php');
require_once(__DIR__ . '/040_common_function_name_multiplier.php');
require_once(__DIR__ . '/041_no_functions.php');
require_once(__DIR__ . '/042_no_functions_at_all.php');
require_once(__DIR__ . '/043_no_calling_function_returns.php');
require_once(__DIR__ . '/050_split_floats.php');
require_once(__DIR__ . '/051_no_floats.php');
require_once(__DIR__ . '/999_strict.php');

/**
 * Unlike some other factories in STACK the parsing rule factory does not
 * try to find rules from the filesystem automatically, and rules must be
 * declared by hardcoding here. In the get_by_common_name function.
 */
class stack_parsing_rule_factory {

    private static $singletons = array();

    public static function get_by_common_name(string $name): stack_cas_astfilter {
        if (empty(self::$singletons)) {
            // If the static set has not been initialised do so.
            self::$singletons['002_log_candy'] = new stack_ast_log_candy_002();
            self::$singletons['010_single_char_vars'] = new stack_ast_filter_single_char_vars_010();
            self::$singletons['040_common_function_name_multiplier'] = new stack_ast_common_function_name_multiplier_040();
            self::$singletons['041_no_functions'] = new stack_ast_filter_no_functions_041();
            self::$singletons['042_no_functions_at_all'] = new stack_ast_filter_no_functions_at_all_042();
            self::$singletons['043_no_calling_function_returns'] = new stack_ast_filter_no_calling_function_returns_43();
            self::$singletons['050_split_floats'] = new stack_ast_filter_split_floats_050();
            self::$singletons['051_no_floats'] = new stack_ast_filter_no_floats_051();
            self::$singletons['999_strict'] = new stack_ast_filter_strict_999();
        }
        return self::$singletons[$name];
    }

    public static function get_filter_pipeline(array $activefilters, bool $includecore=true): stack_cas_astfilter {
        $tobeincluded = array();
        if ($includecore === true) {
        // This would be simpler when we rename the filters so that for example 
            // everything with number in the range 000-099 is core, then we could simply
            // include them from the keys of self::$singletons...
        $tobeincluded['002_log_candy'] = self::get_by_common_name('002_log_candy');
        }
        foreach ($activefilters as $value) {
            $tobeincluded[$value] = self::get_by_common_name($value);
        }
        // Sort them into order.
        ksort($tobeincluded);
        // And return the combination filter.
        return new stack_ast_filter_pipeline($tobeincluded);
    }
}