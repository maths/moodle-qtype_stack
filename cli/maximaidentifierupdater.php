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

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// This script updates stack/cas/base-identifier-map.json
// Used for keepping track of new options and functions in new Maxima versions,
// and as a base for creating security lists.
// @copyright  2018 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

list($options, $unrecognized) = cli_get_params(array('help' => false, 'version' => '5.42.0',
    'data' => '-'),
    array('h' => 'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if (!is_readable($options['data'])) {
    cli_error('The source data must be readable.');
    exit();
}

if ($options['help']) {
    $help =
        "Updates the stack/cas/base-identifier-map.json file with new identifiers.
         for a new Maxima version. To extract identifier unpack the new versions
         source code and run the following command in its doc/info folder:

           grep '^ \-\- .*: .*' -shI * | grep -v 'an error. To debug' > identifiers.txt

         Then give that result to this as well as the version of the source code:

           php maximaidentifierupdate.php --version=5.42.0 --data=identifiers.txt

         The identifiers will be automatically merged to any existing data and can
         then be used for various things.

         The command will list identifiers not already present in the existing data,
         to help identifying new features needing attenttion.
        ";
    echo $help;
    die;
}

$newdata = file($options['data']);

$olddata = file_get_contents('../stack/cas/base-identifier-map.json');
if ($olddata !== false) {
    $olddata = json_decode($olddata, true);
} else {
    $olddata = array();
}

$newversion = $options['version'];

foreach ($newdata as $line) {
    // Example: " -- Function: tokens (<string>, <test>)".
    $line = substr($line, 4); // Drop " -- ".
    $line = explode(':', $line, 2); // Split at first ":".
    $type = trim($line[0]); // Trim the type just in case.
    $identifier = explode(" ", trim($line[1]))[0]; // Pick first token from second part.

    if (isset($olddata[$identifier])) {
        if (isset($olddata[$identifier][$type])) {
            if (array_search($newversion, $olddata[$identifier][$type]) === false) {
                $olddata[$identifier][$type][] = $newversion;
            }
        } else {
            $olddata[$identifier][$type] = array($newversion);
            cli_writeln("Identifier '$identifier' used in new way as '$type'.");
        }
    } else {
        $olddata[$identifier] = array($type => array($newversion));
        cli_writeln("New identifier '$identifier' used as '$type'.");
    }
}

// Then save to that file.
// But first do some sortting. To ease reading the data manually.
ksort($olddata);

file_put_contents('../stack/cas/base-identifier-map.json', json_encode($olddata, JSON_PRETTY_PRINT));
