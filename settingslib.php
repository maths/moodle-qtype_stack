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
require_once(__DIR__ . '/stack/cas/platforms.php');
require_once(__DIR__ . '/stack/cas/installhelper.class.php');


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

        $this->choices = array('mathjax' => get_string('settingmathsdisplay_mathjax', 'qtype_stack'));

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

class qtype_stack_admin_messages extends admin_setting {

    private $warnings;
    private $errors;
    /**
     * not a setting, just text
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting'
     *                     for ones in config_plugins.
     * @param string $heading heading
     * @param string $information text in box
     */
    public function __construct($name, $heading, $information, $errors, $warnings) {
        $this->nosave = true;
        $this->warnings = $warnings;
        $this->errors = $errors;
        parent::__construct($name, $heading, $information, '');
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
        $return = '';
        if ($this->errors || $this->warnings) {
            if (is_array($this->errors)) {
                foreach ($this->errors as $e) {
                    $return .= $OUTPUT->box($OUTPUT->heading(get_string('error'), 5)
                            . $e, 'alert alert-error alert-block');
                }
            } else if ($this->errors) {
                $return .= $OUTPUT->box($OUTPUT->heading(get_string('error'), 5)
                        . $this->errors, 'alert alert-error alert-block');
            }
            if (is_array($this->warnings)) {
                foreach ($this->warnings as $w) {
                    $return .= $OUTPUT->box($OUTPUT->heading(get_string('warning'), 5)
                            . $w, 'alert alert-warning alert-block');
                }
            } else if ($this->warnings) {
                $return .= $OUTPUT->box($OUTPUT->heading(get_string('warning'), 5)
                        . $this->warnings, 'alert alert-warning alert-block');
            }
        } else {
            $return .= $OUTPUT->box('None', 'generalbox formsettingheading');
        }
        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, '', '', '');
    }
}

function qtype_stack_admin_handle_updated($settingfullname) {
    qtype_stack_admin_timestamp::handle_updated($settingfullname);
}

/**
 * Hidden setting used to timestamp changes in key settings.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_admin_timestamp extends admin_setting_configtext {

    static protected $links = array();
    protected $updated = true;

    static public function handle_updated($updatedsetting) {
        global $ADMIN, $PAGE, $CFG;
        $link = self::$links[$updatedsetting];
        $link[0]->updated = true;
        stack_utils::get_config()->refresh($link[1]->name);
    }

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param array  $monitored
     */
    public function __construct($name, $visiblename, $description, $monitored) {
        parent::__construct($name, $visiblename, $description, '0', $paramtype = PARAM_INT, $size = 12);
        foreach ($monitored as $m) {
            $n = $m->get_full_name();
            self::$links[$m->get_full_name()] = array($this, $m);
            $m->set_updatedcallback('qtype_stack_admin_handle_updated');
        }
    }

    public function write_setting($data) {
        if ($this->updated) {
            $data = time();
            $this->updated = false;
        }
        return parent::write_setting($data);
    }

    /**
     * Returns an XHTML string for the hidden field
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        return '<div class="form-empty" >' .
                                    '<input type="hidden"' .
                                        ' id="'. $this->get_id() .'"' .
                                        ' name="'. $this->get_full_name() .'"' .
                                        ' value="'.s($data).'"/></div>';
        return format_admin_setting($this,
                                    '',
                                    '<div class="form-empty" >' .
                                    '<input type="hidden"' .
                                        ' id="'. $this->get_id() .'"' .
                                        ' name="'. $this->get_full_name() .'"' .
                                        ' value=".s($data)."/></div>',
                                    '',
                                    false,
                                    '',
                                    '',
                                    '');
    }
}

