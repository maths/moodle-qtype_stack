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

require_once(__DIR__ . '/001_fix_call_of_a_group_or_function.filter.php');
require_once(__DIR__ . '/002_log_candy.filter.php');
require_once(__DIR__ . '/003_no_dot_dot.filter.php');
require_once(__DIR__ . '/005_i_is_never_a_function.filter.php');
require_once(__DIR__ . '/020_no_arc.filter.php');
require_once(__DIR__ . '/025_no_trig_power.filter.php');
require_once(__DIR__ . '/030_no_trig_space.filter.php');
require_once(__DIR__ . '/031_no_trig_brackets.filter.php');
require_once(__DIR__ . '/050_no_chained_inequalities.filter.php');
require_once(__DIR__ . '/090_special_forbidden_characters.filter.php');
require_once(__DIR__ . '/101_no_floats.filter.php');
require_once(__DIR__ . '/102_no_strings.filter.php');
require_once(__DIR__ . '/103_no_lists.filter.php');
require_once(__DIR__ . '/104_no_sets.filter.php');
require_once(__DIR__ . '/105_no_grouppings.filter.php');
require_once(__DIR__ . '/106_no_control_flow.filter.php');
require_once(__DIR__ . '/201_sig_figs_validation.filter.php');
require_once(__DIR__ . '/202_decimal_places_validation.filter.php');
require_once(__DIR__ . '/210_x_used_as_multiplication.filter.php');
require_once(__DIR__ . '/402_split_prefix_from_common_function_name.filter.php');
require_once(__DIR__ . '/403_split_at_number_letter_boundary.filter.php');
require_once(__DIR__ . '/404_split_at_number_letter_number_boundary.filter.php');
require_once(__DIR__ . '/406_split_implied_variable_names.filter.php');
require_once(__DIR__ . '/410_single_char_vars.filter.php');
require_once(__DIR__ . '/441_split_unknown_functions.filter.php');
require_once(__DIR__ . '/442_split_all_functions.filter.php');
require_once(__DIR__ . '/450_split_floats.filter.php');
require_once(__DIR__ . '/502_replace_pm.filter.php');
require_once(__DIR__ . '/505_no_evaluation_groups.filter.php');
require_once(__DIR__ . '/520_no_equality_with_logic.filter.php');
require_once(__DIR__ . '/541_no_unknown_functions.filter.php');
require_once(__DIR__ . '/542_no_functions_at_all.filter.php');
require_once(__DIR__ . '/801_singleton_numeric.filter.php');
require_once(__DIR__ . '/802_singleton_units.filter.php');
require_once(__DIR__ . '/910_inert_float_for_display.filter.php');
require_once(__DIR__ . '/990_no_fixing_spaces.filter.php');
require_once(__DIR__ . '/991_no_fixing_stars.filter.php');
require_once(__DIR__ . '/997_string_security.filter.php');
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
            case '001_fix_call_of_a_group_or_function':
                return new stack_ast_filter_001_fix_call_of_a_group_or_function();
            case '002_log_candy':
                return new stack_ast_filter_002_log_candy();
            case '003_no_dot_dot':
                return new stack_ast_filter_003_no_dot_dot();
            case '005_i_is_never_a_function':
                return new stack_ast_filter_005_i_is_never_a_function();
            case '020_no_arc':
                return new stack_ast_filter_020_no_arc();
            case '025_no_trig_power':
                return new stack_ast_filter_025_no_trig_power();
            case '030_no_trig_space':
                return new stack_ast_filter_030_no_trig_space();
            case '031_no_trig_brackets':
                return new stack_ast_filter_031_no_trig_brackets();
            case '050_no_chained_inequalities':
                return new stack_ast_filter_050_no_chained_inequalities();
            case '090_special_forbidden_characters':
                return new stack_ast_filter_090_special_forbidden_characters();
            case '101_no_floats':
                return new stack_ast_filter_101_no_floats();
            case '102_no_strings':
                return new stack_ast_filter_102_no_strings();
            case '103_no_lists':
                return new stack_ast_filter_103_no_lists();
            case '104_no_sets':
                return new stack_ast_filter_104_no_sets();
            case '105_no_grouppings':
                return new stack_ast_filter_105_no_grouppings();
            case '106_no_control_flow':
                return new stack_ast_filter_106_no_control_flow();
            case '201_sig_figs_validation':
                return new stack_ast_filter_201_sig_figs_validation();
            case '202_decimal_places_validation':
                return new stack_ast_filter_202_decimal_places_validation();
            case '210_x_used_as_multiplication':
                return new stack_ast_filter_210_x_used_as_multiplication();
            case '402_split_prefix_from_common_function_name':
                return new stack_ast_filter_402_split_prefix_from_common_function_name();
            case '403_split_at_number_letter_boundary':
                return new stack_ast_filter_403_split_at_number_letter_boundary();
            case '404_split_at_number_letter_number_boundary':
                return new stack_ast_filter_404_split_at_number_letter_number_boundary();
            case '406_split_implied_variable_names':
                return new stack_ast_filter_406_split_implied_variable_names();
            case '410_single_char_vars':
                return new stack_ast_filter_410_single_char_vars();
            case '441_split_unknown_functions':
                return new stack_ast_filter_441_split_unknown_functions();
            case '442_split_all_functions':
                return new stack_ast_filter_442_split_all_functions();
            case '450_split_floats':
                return new stack_ast_filter_450_split_floats();
            case '502_replace_pm':
                return new stack_ast_filter_502_replace_pm();
            case '505_no_evaluation_groups':
                return new stack_ast_filter_505_no_evaluation_groups();
            case '520_no_equality_with_logic':
                return new stack_ast_filter_520_no_equality_with_logic();
            case '541_no_unknown_functions':
                return new stack_ast_filter_541_no_unknown_functions();
            case '542_no_functions_at_all':
                return new stack_ast_filter_542_no_functions_at_all();
            case '801_singleton_numeric':
                return new stack_ast_filter_801_singleton_numeric();
            case '802_singleton_units':
                return new stack_ast_filter_802_singleton_units();
            case '910_inert_float_for_display':
                return new stack_ast_filter_910_inert_float_for_display();
            case '990_no_fixing_spaces':
                return new stack_ast_filter_990_no_fixing_spaces();
            case '991_no_fixing_stars':
                return new stack_ast_filter_991_no_fixing_stars();
            case '997_string_security':
                return new stack_ast_filter_997_string_security();
            case '998_security':
                return new stack_ast_filter_998_security();
            case '999_strict':
                return new stack_ast_filter_999_strict();
        }
    }

    public static function get_by_common_name(string $name): stack_cas_astfilter {
        if (empty(self::$singletons)) {
            // If the static set has not been initialised do so.
            foreach (array('001_fix_call_of_a_group_or_function', '002_log_candy',
                           '003_no_dot_dot', '005_i_is_never_a_function',
                           '020_no_arc', '025_no_trig_power',
                           '030_no_trig_space', '031_no_trig_brackets',
                           '050_no_chained_inequalities',
                           '090_special_forbidden_characters',
                           '101_no_floats', '102_no_strings',
                           '103_no_lists', '104_no_sets',
                           '105_no_grouppings', '106_no_control_flow',
                           '201_sig_figs_validation',
                           '202_decimal_places_validation',
                           '210_x_used_as_multiplication',
                           '402_split_prefix_from_common_function_name',
                           '403_split_at_number_letter_boundary',
                           '404_split_at_number_letter_number_boundary',
                           '406_split_implied_variable_names',
                           '410_single_char_vars', '441_split_unknown_functions',
                           '442_split_all_functions', '450_split_floats',
                           '502_replace_pm',
                           '505_no_evaluation_groups',
                           '520_no_equality_with_logic',
                           '541_no_unknown_functions', '542_no_functions_at_all',
                           '801_singleton_numeric', '802_singleton_units',
                           '910_inert_float_for_display',
                           '990_no_fixing_spaces', '991_no_fixing_stars',
                           '997_string_security',
                           '998_security', '999_strict') as $name) {
                self::$singletons[$name] = self::build_from_name($name);
            }
        }
        return self::$singletons[$name];
    }

    public static function get_filter_pipeline(array $activefilters, array $settings, bool $includecore=true): stack_cas_astfilter {
        $tobeincluded = array();
        if ($includecore === true) {
            if (empty(self::$singletons)) {
                // If not generated generate the list.
                $tobeincluded['001_fix_call_of_a_group_or_function']
                    = self::get_by_common_name('001_fix_call_of_a_group_or_function');
            }

            // All core filters begin with 0.
            foreach (self::$singletons as $key => $filter) {
                if (strpos($key, '0') === 0) {
                    $tobeincluded[$key] = $filter;
                }
            }

            // 999_strict and 998_security can also be considered as core but
            // are not included by default as they are selective features.
        }
        foreach ($activefilters as $value) {
            $filter = self::get_by_common_name($value);
            if ($filter === null) {
                throw new stack_exception('stack_ast_filter: unknown filter ' . $value);
            }
            if ($filter instanceof stack_cas_astfilter_parametric) {
                // If the filter is parametric we cannot use the 'singleton' instance.
                $filter = self::build_from_name($value);
                // And we need to push in the parameters.
                // Key example being 's'/'t' for 998_security.
                $filter->set_filter_parameters($settings[$value]);
            }
            $tobeincluded[$value] = $filter;
        }

        // Check for exclusions, just in case.
        foreach ($tobeincluded as $key => $filter) {
            if ($filter instanceof stack_cas_astfilter_exclusion) {
                foreach ($tobeincluded as $name => $duh) {
                    if ($name !== $key && $filter->conflicts_with($name)) {
                        throw new stack_exception('stack_ast_filter: conflicting filters present in pipeline ' .
                                $key . ' and ' . $name);
                    }
                }
            }
        }

        // Sort them into order.
        ksort($tobeincluded);
        // And return the combination filter.
        return new stack_ast_filter_pipeline($tobeincluded);
    }

    public static function list_filters(): array {
        if (empty(self::$singletons)) {
            self::get_by_common_name('001_fix_call_of_a_group_or_function');
        }
        return array_keys(self::$singletons);
    }
}