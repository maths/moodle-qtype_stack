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

require_once(__DIR__ . '/002_log_candy.filter.php');
require_once(__DIR__ . '/010_single_char_vars.php');
require_once(__DIR__ . '/040_common_function_name_multiplier.php');
require_once(__DIR__ . '/041_no_functions.php');
require_once(__DIR__ . '/042_no_functions_at_all.php');
require_once(__DIR__ . '/043_no_calling_function_returns.php');
require_once(__DIR__ . '/050_split_floats.php');
require_once(__DIR__ . '/051_no_floats.php');
require_once(__DIR__ . '/998_security.filter.php');
require_once(__DIR__ . '/999_strict.filter.php');

/**
 * Unlike some other factories in STACK the parsing rule factory does not
 * try to find rules from the filesystem automatically, and rules must be
 * declared by hardcoding here. In the build function.
 */
class stack_parsing_rule_factory {

    private static $singletons = array();

    private static function build_from_name(string $name): stack_cas_astfilter {
        // Might as well do the require once here, but better limit to 
        // vetted and require all by default to catch syntax errors.
        switch ($name) {
            case '002_log_candy':
                return new stack_ast_filter_002_log_candy();
            case '998_security':
                return new stack_ast_filter_998_security();
            case '999_strict':
                return new stack_ast_filter_999_strict();
        }


    }

    public static function get_by_common_name(string $name): stack_cas_astfilter {
        if (empty(self::$singletons)) {
            foreach (array('002_log_candy', '998_security', '999_strict') as $name) {
                self::$singletons[$name] = self::build_from_name($name);
            }


            // If the static set has not been initialised do so.
            
            self::$singletons['010_single_char_vars'] = new stack_ast_filter_single_char_vars_010();
            self::$singletons['040_common_function_name_multiplier'] = new stack_ast_common_function_name_multiplier_040();
            self::$singletons['041_no_functions'] = new stack_ast_filter_no_functions_041();
            self::$singletons['042_no_functions_at_all'] = new stack_ast_filter_no_functions_at_all_042();
            self::$singletons['043_no_calling_function_returns'] = new stack_ast_filter_no_calling_function_returns_43();
            self::$singletons['050_split_floats'] = new stack_ast_filter_split_floats_050();
            self::$singletons['051_no_floats'] = new stack_ast_filter_no_floats_051();
        }
        return self::$singletons[$name];
    }

    public static function get_filter_pipeline(array $activefilters, array $settings, bool $includecore=true): stack_cas_astfilter {
        $tobeincluded = array();
        if ($includecore === true) {
            // This would be simpler when we rename the filters so that for example 
            // everything with number in the range 000-099 is core, then we could simply
            // include them from the keys of self::$singletons...
            $tobeincluded['002_log_candy'] = self::get_by_common_name('002_log_candy');

            // The security filter is not a core filter, maybe it should be?
        }
        foreach ($activefilters as $value) {
            $filter = self::get_by_common_name($value);
            if ($filter instanceof stack_cas_astfilter_parametric) {
                // If the filter is parametric we cannot use the 'singleton' instance.
                $filter = self::build_from_name($value);
                // And we need to push in the parameters. 
                // Key example being 's'/'t' for 998_security.
                $filter->set_filter_parameters($settings[$value]);
            }
            $tobeincluded[$value] = $filter;
        }
        // Sort them into order.
        ksort($tobeincluded);
        // And return the combination filter.
        return new stack_ast_filter_pipeline($tobeincluded);
    }
}