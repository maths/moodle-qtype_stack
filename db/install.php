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
 * Stack question type installation code.
 *
 * @package    qtype_stack
 * @copyright  2012 Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/cas/installhelper.class.php');
require_once(__DIR__ . '/../stack/cas/connectorhelper.class.php');


function xmldb_qtype_stack_install() {
    global $CFG;

    // Define stackmaximaversion config parameter.
    if (!preg_match('~stackmaximaversion:(\d{10})~',
            file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
        throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
    }
    set_config('stackmaximaversion', $matches[1], 'qtype_stack');

    // The values we want to set here cannot have a default specified in settings.php, because
    // any defaults there overwrite anything set here. Since there cannot be a default in settings.php,
    // we have to set all those values here.

    // Make an reasonable guess at the OS. (It defaults to 'linux' in settings.php.
    $platform = 'linux';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // See http://stackoverflow.com/questions/1482260/how-to-get-the-os-on-which-php-is-running
        // and http://stackoverflow.com/questions/738823/possible-values-for-php-os.
        $platform = 'win';
    }
    set_config('platform', $platform, 'qtype_stack');

    // If this is a PHP unit test site, automatically create maxima_opt_auto.
    // Should probably consider doing this for real in the future.
    if ($platform != 'win' && (PHPUNIT_TEST || defined('BEHAT_UTIL'))) {
        // Set to the same defaults as in settings.php - however, that has not been done
        // yet in the Moodle install code flow, so we have to duplicate here.
        set_config('maximaversion', 'default', 'qtype_stack');
        set_config('castimeout', 10, 'qtype_stack');
        set_config('casresultscache', 'db', 'qtype_stack');
        set_config('maximacommand', '', 'qtype_stack');
        set_config('maximacommandopt', '', 'qtype_stack');
        set_config('maximacommandserver', '', 'qtype_stack');
        set_config('serveruserpass', '', 'qtype_stack');
        set_config('plotcommand', '', 'qtype_stack');
        // @codingStandardsIgnoreStart

        // Trying to load the libraries leads to a fail to load /usr/share/maxima/5.32.1/share/draw/draw.lisp error.
        // Need to sort this out.
        // set_config('maximalibraries', 'stats, distrib, descriptive, simplex', 'qtype_stack');

        // @codingStandardsIgnoreEnd
        set_config('maximalibraries', '', 'qtype_stack');
        set_config('casdebugging', 1, 'qtype_stack');
        set_config('mathsdisplay', 'mathjax', 'qtype_stack');

        if (!defined('QTYPE_STACK_TEST_CONFIG_PLATFORM') || QTYPE_STACK_TEST_CONFIG_PLATFORM !== 'server') {
            list($ok, $message) = stack_cas_configuration::create_auto_maxima_image();
            if (!$ok) {
                throw new coding_exception('maxima_opt_auto creation failed.', $message);
            }
        }
    }
}
