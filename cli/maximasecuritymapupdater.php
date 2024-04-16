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

// This script updates stack/cas/security-map.json to include new identifiers
// present in stack/cas/base-identifier-map.json.
// @copyright  2018 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

list($options, $unrecognized) = cli_get_params(['help' => false],
    ['h' => 'help']);
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if (!is_readable('../stack/cas/base-identifier-map.json')) {
    cli_error('Could not find the base-identifier-map.json, ensure that it is generated first.');
    exit();
}

// Load the identifier data.
$identifierdata = json_decode(file_get_contents('../stack/cas/base-identifier-map.json'), true);


// Load the security-map.
$olddata = false;
if (is_readable('../stack/cas/security-map.json')) {
    $olddata = file_get_contents('../stack/cas/security-map.json');
}
if ($olddata !== false) {
    $olddata = json_decode($olddata, true);
} else {
    $olddata = [];
}


// Type aliases, not all of these come from the base-identifier-map.
// Some are added to the security-map by hand.
$aliases = [
    // Note that values are arrays, some might have multiple targets but not now.

    // Functions are things that are called.
    'Functions' => ['function'],
    'Function' => ['function'],
    'System function' => ['function'],
    'Scene constructor' => ['function'],

    // Keywords are things that should not appear as targets of writing,
    // they should not appear as identifiers at all.
    'Special operator' => ['keyword'],
    'Keyword' => ['keyword'],

    // Operators are just that and should not be used as variable names.
    'Operator' => ['operator'],
    'Input terminator' => ['operator'], // Note that ; and $ are basically operators.

    // Properties should not be used as variable names.
    'Property' => ['property'],
    'Declaration' => ['property'],

    // Constants should not be written to.
    'Constant' => ['constant'],

    // Variables are essenttially options. But might be much more.
    // One may need to map some of these as 'constant' to allow student input
    // without allowing definition e.g. in the case of '%c'.
    'Global variable' => ['variable'],
    'Variable' => ['variable'],
    'System variable' => ['variable'], // These are mostly forbidden.
    'Optional variable' => ['variable'],
    'Option variable' => ['variable'],

    // Some types are ignored as they have no meaning.
    'Function_partitions' => null, // Existed in 5.35.1 documentation.
    'Package' => null, // Irrelevant.
    'Special symbol' => null, // These require manual intervention.
    // The remainder just have names we do not want to block for most uses.
    'Scene object' => null,
    'Object option' => null,
    'Scene option' => null,
    'Graphic option' => null,
    'Graphic object' => null, // Object constructors.
    'Plot option' => null,
    'draw_graph option' => null,
    '_comment' => null // For our own comments.
];

foreach ($identifierdata as $identifier => $types) {
    foreach ($types as $type => $blaah) {
        if (array_key_exists($type, $aliases)) {
            if ($aliases[$type] !== null) {
                foreach ($aliases[$type] as $t) {
                    if (!isset($olddata[$identifier])) {
                        $olddata[$identifier] = [$t => '?'];
                    } else if (!isset($olddata[$identifier][$t])) {
                        // Only set value if not already set.
                        // The default value is '?' other options are 'f', 't'
                        // and 's' for forbid, teacher allow and student allow.
                        // Things allowed for teachers are not allowed for
                        // students but teachers may override that and forbidden
                        // Things are forbidden from all.
                        // '?' means we don't care yet.
                        $olddata[$identifier][$t] = '?';
                    }
                }
            }
        } else {
            cli_writeln("New type '$type'.");
        }
        if (isset($olddata[$identifier])) {
            if (isset($olddata[$identifier]['aliasfunction'])) {
                $targetdata = [];
                if (isset($olddata[$olddata[$identifier]['aliasfunction']])) {
                    $targetdata = $olddata[$olddata[$identifier]['aliasfunction']];
                }
                if (!isset($targetdata['aliasfunctions'])) {
                    $targetdata['aliasfunctions'] = [$identifier];
                }
                if (array_search($identifier, $targetdata['aliasfunctions']) === false) {
                    $targetdata['aliasfunctions'][] = $identifier;
                }
                $olddata[$olddata[$identifier]['aliasfunction']] = $targetdata;
            }
            if (isset($olddata[$identifier]['aliasvariable'])) {
                $targetdata = [];
                if (isset($olddata[$olddata[$identifier]['aliasvariable']])) {
                    $targetdata = $olddata[$olddata[$identifier]['aliasvariable']];
                }
                if (!isset($targetdata['aliasvariables'])) {
                    $targetdata['aliasvariables'] = [$identifier];
                }
                if (array_search($identifier, $targetdata['aliasvariables']) === false) {
                    $targetdata['aliasvariables'][] = $identifier;
                }
                $olddata[$olddata[$identifier]['aliasvariable']] = $targetdata;
            }
        }
    }
    if (isset($olddata[$identifier])) {
        // Ensure consistent order.
        ksort($olddata[$identifier]);
    }
}


// Sort the keys also to ease manual usage.
ksort($olddata);

file_put_contents('../stack/cas/security-map.json', json_encode($olddata, JSON_PRETTY_PRINT));
