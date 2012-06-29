<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Version information for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configselect('qtype_stack/platform',
        get_string('settingplatformtype', 'qtype_stack'),
        get_string('settingplatformtype_desc', 'qtype_stack'), 'linux', array(
                'unix'   => get_string('settingplatformtypeunix', 'qtype_stack'),
                'win'    => get_string('settingplatformtypewin', 'qtype_stack'),
                'tomcat' => get_string('settingplatformtypemaximapool', 'qtype_stack'),
                'server' => get_string('settingplatformtypeserver', 'qtype_stack'))));

$settings->add(new admin_setting_configselect('qtype_stack/maximaversion',
        get_string('settingcasmaximaversion', 'qtype_stack'),
        get_string('settingcasmaximaversion_desc', 'qtype_stack'), '5.27.0',
                array('5.21.1' => '5.21.1', '5.22.1' => '5.22.1', '5.23.1' => '5.23.1',
                      '5.24.0' => '5.24.0', '5.25.1' => '5.25.1', '5.26.0' => '5.26.0',
                      '5.27.0' => '5.27.0')));

$settings->add(new admin_setting_configtext('qtype_stack/castimeout',
        get_string('settingcastimeout', 'qtype_stack'),
        get_string('settingcastimeout_desc', 'qtype_stack'), 5, PARAM_INT, 3));

$settings->add(new admin_setting_configselect('qtype_stack/casresultscache',
        get_string('settingcasresultscache', 'qtype_stack'),
        get_string('settingcasresultscache_desc', 'qtype_stack'), 'db', array(
            'none' => get_string('settingcasresultscache_none', 'qtype_stack'),
            'db' => get_string('settingcasresultscache_db', 'qtype_stack'),
        )));

$settings->add(new admin_setting_configtext('qtype_stack/maximacommand',
        get_string('settingplatformmaximacommand', 'qtype_stack'),
        get_string('settingplatformmaximacommand_desc', 'qtype_stack'), ''));

$settings->add(new admin_setting_configtext('qtype_stack/plotcommand',
        get_string('settingplatformplotcommand', 'qtype_stack'),
        get_string('settingplatformplotcommand_desc', 'qtype_stack'), ''));

$settings->add(new admin_setting_configcheckbox('qtype_stack/casdebugging',
        get_string('settingcasdebugging', 'qtype_stack'),
        get_string('settingcasdebugging_desc', 'qtype_stack'), 0));

$settings->add(new admin_setting_heading('docs',
        get_string('stackDoc_docs', 'qtype_stack'),
        get_string('stackDoc_docs_desc', 'qtype_stack',
                array('link' => (string) new moodle_url('/question/type/stack/doc/doc.php/')))));

$settings->add(new admin_setting_heading('healthcheck',
        get_string('healthcheck', 'qtype_stack'),
        get_string('healthcheck_desc', 'qtype_stack',
                array('link' => (string) new moodle_url('/question/type/stack/healthcheck.php')))));

$settings->add(new admin_setting_heading('chat',
        get_string('chattitle', 'qtype_stack'),
        get_string('chat_desc', 'qtype_stack',
                array('link' => (string) new moodle_url('/question/type/stack/caschat.php')))));

$settings->add(new admin_setting_heading('answertests',
        get_string('stackInstall_testsuite_title', 'qtype_stack'),
        get_string('stackInstall_testsuite_title_desc', 'qtype_stack',
                array('link' => (string) new moodle_url('/question/type/stack/answertests.php')))));

$settings->add(new admin_setting_heading('inputtests',
        get_string('stackInstall_input_title', 'qtype_stack'),
        get_string('stackInstall_input_title_desc', 'qtype_stack',
                array('link' => (string) new moodle_url('/question/type/stack/studentinputs.php')))));
