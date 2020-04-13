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
 * Helper class for setting up the STACK configuration for automated tests.
 *
 * @package   qtype_stack
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../stack/cas/installhelper.class.php');

/**
 * Helper class for setting up the STACK configuration for automated tests.
 *
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_test_config {
    public static function is_test_config_available() {
        // Either the platform is already non-default (e.g.
        // because auto-optimse worked during install, or
        // settings given in config.php.
        return get_config('qtype_stack', 'platform') !== 'unix' ||
                defined('QTYPE_STACK_TEST_CONFIG_PLATFORM');
    }

    /**
     * Helper that sets up the maxima configuration. This allows maxima to be used
     * from test classes that cannot subclass this one, for whatever reason.
     */
    public static function setup_test_maxima_connection() {
        global $CFG;

        if (!self::is_test_config_available()) {
            throw new coding_exception('The calling code should call setup_test_maxima_connection ' .
                    'and skip the test in an appropriate way if it returns false.');
        }

        if (!defined('QTYPE_STACK_EXPECTED_VERSION')) {
            if (!preg_match('~stackmaximaversion:(\d{10})~',
                    file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
                throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
            }
            define('QTYPE_STACK_EXPECTED_VERSION', $matches[1]);
        }

        if (!defined('QTYPE_STACK_TEST_CONFIG_PLATFORM')) {
            // Things were set up by install.php. Nothing to do here.
            return;
        }

        set_config('platform',        QTYPE_STACK_TEST_CONFIG_PLATFORM,        'qtype_stack');
        set_config('maximaversion',   QTYPE_STACK_TEST_CONFIG_MAXIMAVERSION,   'qtype_stack');
        set_config('castimeout',      QTYPE_STACK_TEST_CONFIG_CASTIMEOUT,      'qtype_stack');
        set_config('casresultscache', QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE, 'qtype_stack');
        set_config('maximacommand',   QTYPE_STACK_TEST_CONFIG_MAXIMACOMMAND,   'qtype_stack');
        set_config('plotcommand',     QTYPE_STACK_TEST_CONFIG_PLOTCOMMAND,     'qtype_stack');
        set_config('maximalibraries', QTYPE_STACK_TEST_CONFIG_MAXIMALIBRARIES, 'qtype_stack');
        set_config('casdebugging',    QTYPE_STACK_TEST_CONFIG_CASDEBUGGING,    'qtype_stack');
        set_config('mathsdisplay',    'mathjax',                               'qtype_stack');
        set_config('replacedollars',  0,                                       'qtype_stack');
        set_config('stackmaximaversion', QTYPE_STACK_EXPECTED_VERSION,         'qtype_stack');

        if (QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE == 'otherdb') {
            set_config('cascachedbtype',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBTYPE,    'qtype_stack');
            set_config('cascachedblibrary', QTYPE_STACK_TEST_CONFIG_CASCACHEDBLIBRARY, 'qtype_stack');
            set_config('cascachedbhost',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBHOST,    'qtype_stack');
            set_config('cascachedbname',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBNAME,    'qtype_stack');
            set_config('cascachedbuser',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBUSER,    'qtype_stack');
            set_config('cascachedbpass',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBPASS,    'qtype_stack');
            set_config('cascachedbprefix',  QTYPE_STACK_TEST_CONFIG_CASCACHEDBPREFIX,  'qtype_stack');
            if (defined('QTYPE_STACK_TEST_CONFIG_CASCACHEDBSOCKET')) {
                set_config('cascachedbsocket',  QTYPE_STACK_TEST_CONFIG_CASCACHEDBSOCKET,  'qtype_stack');
            }
        }

        if (defined('QTYPE_STACK_TEST_CONFIG_SERVERUSERPASS')) {
            set_config('serveruserpass',    QTYPE_STACK_TEST_CONFIG_SERVERUSERPASS,    'qtype_stack');
        }

        if (!file_exists(stack_cas_configuration::maximalocal_location())) {
            stack_cas_configuration::create_maximalocal();
        }

        // Create the required directories inside moodledata.
        make_upload_directory('stack');
        make_upload_directory('stack/logs');
        make_upload_directory('stack/plots');
        make_upload_directory('stack/tmp');
    }
}
