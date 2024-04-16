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

// This script is for testing compilation of CASText2
//
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/cas/castext2/utils.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(['help' => false,
    'string' => '{@foo@}', 'ast' => false], ['h' => 'help']);
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Test the CASText2 compiler on a given string.

          --string=\"{@foo@}\"
        ";
    echo $help;
    die;
}

$teststring = '{@foo@}';

if (isset($options['string'])) {
    $teststring = $options['string'];
}

try {
    $result = castext2_parser_utils::compile($teststring, null, ['context' => 'test']);
    print("\nSuccess:\n");
    print($result->toString(['pretty' => true]) . "\n");
} catch (Exception $e) {
    print("\nFailed\n");
    var_dump($e);
}
