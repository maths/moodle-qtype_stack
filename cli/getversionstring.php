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
 * Script to extract version numbers from the code.
 * Primarily for generating the version number mapping but also for
 * sanity checking and other tool integration.
 *
 * @package    qtype_stack
 * @subpackage cli
 * @copyright  2023 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/adminlib.php');

// Fake settings object to catch some values.
class fakesettings {
    public $maximaversions = [];
    public function add($some) {
        if ($some->name === 'maximaversion') {
            $this->maximaversions = $some->choices;
            unset($this->maximaversions['default']);
        }
    }
}
$settings = new fakesettings();

require(__DIR__ . '/../settings.php');

// Fake plugin object.
$plugin = new stdClass();
require(__DIR__ . '/../version.php');

// Get cli options.
list($options, $unrecognized) = cli_get_params(['help' => false, 'only' => 'row'], ['h' => 'help']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo "This script extracts version numbers from various sources in the code.
The numbers it provides are as follows:

 - 'pluginversion', the version number, the date one, of the plugin from version.php.
 - 'pluginname', the version number, the human one, of the plugin from version.php.
 - 'stackmaxima', the version number at the end of stackmaxima.mac.
 - 'maximas', the supported Maximas from settings.php.
 - 'requiredmoodle', the minimum Moodle version from version.php.

With the additional option '--only' one can query for only one of these.
e.g. '--only=pluginname'. By default outputs selected ones in the order used in that
table in the docs.
";
    exit(0);
}

// Read things in.
$matches = [];
if (!preg_match('~stackmaximaversion:(\d{10})~',
    file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
        throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
}
// Collect values.
$stackmaxima = $matches[1];
$pluginversion = $plugin->version;
$requiredmoodle = $plugin->requires;
$pluginname = explode(' ', trim($plugin->release))[0];
$maximas = implode(', ', $settings->maximaversions);

if ($stackmaxima != $pluginversion) {
    echo "$stackmaxima != $pluginversion\n";
    throw new coding_exception('Maxima libraries version number not matching plugin version number.');
}

switch($options['only']) {
    case 'row':
        echo "$pluginname | $pluginversion | $maximas\n";
        break;
    case 'stackmaxima':
        echo "$stackmaxima\n";
        break;
    case 'maximas':
        echo "$maximas\n";
        break;
    case 'pluginversion':
        echo "$pluginversion\n";
        break;
    case 'pluginname':
        echo "$pluginname\n";
        break;
    case 'requiredmoodle':
        echo "$requiredmoodle\n";
        break;
    default:
        echo "Unknown option for '--only', the options are:
 - 'pluginversion', the version number, the date one, of the plugin from version.php.
 - 'pluginname', the version number, the human one, of the plugin from version.php.
 - 'stackmaxima', the version number at the end of stackmaxima.mac.
 - 'maximas', the supported Maximas from settings.php.
 - 'requiredmoodle', the minimum Moodle version from version.php.\n\n";
}
