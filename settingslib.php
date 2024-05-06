<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Configuration settings library code for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Admin settings class for the STACK maths rendering method choices.
 *
 * Just so we can lazy-load the choices.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_admin_setting_maths_display_method extends admin_setting_configselect {
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = ['mathjax' => get_string('settingmathsdisplay_mathjax', 'qtype_stack')];

        // Remove this if statement once we no longer need to support Moodle 2.5.x.
        if (class_exists('core_component') && method_exists('core_component', 'get_plugin_list_with_file')) {
            $filters = core_component::get_plugin_list_with_file('filter', 'filter.php');
        } else {
            $filters = get_plugin_list_with_file('filter', 'filter.php');
        }

        if (array_key_exists('tex', $filters)) {
            $this->choices['tex'] = get_string('settingmathsdisplay_tex', 'qtype_stack');
        }

        if (array_key_exists('maths', $filters)) {
            $this->choices['maths'] = get_string('settingmathsdisplay_maths', 'qtype_stack');
        }

        if (array_key_exists('oumaths', $filters)) {
            $this->choices['oumaths'] = get_string('settingmathsdisplay_oumaths', 'qtype_stack');
        }

        return true;
    }
}


/**
 * Admin settings class for the STACK input type choices.
 *
 * So we can lazy-load the choices.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_admin_setting_input_types extends admin_setting_configselect {
    public function load_choices() {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/stack/stack/input/factory.class.php');

        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = stack_input_factory::get_available_type_choices();

        return true;
    }
}
