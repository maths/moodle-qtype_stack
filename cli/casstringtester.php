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

// This file is for the testing phase...
// @copyright  2018 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/cas/casstring.class.php');

require_once(__DIR__ . '/../stack/utils.class.php');

$comparemode = true;
if ($comparemode) {
    require_once(__DIR__ . '/../stack/cas/casstring.class.old.php');
}

$CFG->debug = (E_ALL | E_STRICT);


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help' => false, 'string' => '1+2x'),
    array('h' => 'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Test the casstring validation with a given string. Goes through most options.

          --string=\"1+2x\"
        ";
    echo $help;
    die;
}

$teststring = '1+2x';

if (isset($options['string'])) {
    $teststring = $options['string'];
}

cli_heading('= testing = ' . $teststring . '=');

$results = array();

$difference = false;
$keydifference = false;

// Generate...
for ($insertstars = 0; $insertstars < 6; $insertstars++) {
    $heading1 = 'insertstars = ' . $insertstars;
    foreach (array('s', 't') as $security) {
        $heading2 = $heading1 . ' security = ' . $security;
        foreach (array(true, false) as $syntax) {
            $heading3 = $heading2 . ' syntax = ' . ($syntax ? 'true' : 'false');
            foreach (array(true, false) as $units) {
                $str = $teststring;
                if ($security === 's') {
                    $str = stack_utils::logic_nouns_sort($str, 'add');
                }
                $heading = $heading3 . ' units = ' . ($units ? 'true' : 'false');
                $output = array();
                $cs = new stack_cas_casstring($str);
                $cs->set_units($units);
                $cs2 = false;
                if ($comparemode) {
                    $cs2 = new stack_cas_casstring_old($str);
                    $cs2->set_units($units);
                }
                $valid = $cs->get_valid($security, $syntax, $insertstars);
                $output[] = 'valid      : ' . ($valid ? 'true' : 'false');
                if ($comparemode && $valid !== $cs2->get_valid($security, $syntax, $insertstars)) {
                    $output[count($output) - 1] .= ' (MISSMATCH WITH OLD)';
                    $difference = true;
                    $keydifference = true;
                }
                if ($cs->get_errors() !== '') {
                    $output[] = 'errors     : ' . trim($cs->get_errors());
                }
                if ($comparemode && (trim($cs2->get_errors()) !== trim($cs->get_errors()))) {
                    $output[] = 'errors(OLD): ' . trim($cs2->get_errors());
                    $difference = true;
                }
                if ($cs->get_answernote() !== '') {
                    $output[] = 'note       : ' . $cs->get_answernote();
                }
                if ($comparemode && (trim($cs2->get_answernote()) !== trim($cs->get_answernote()))) {
                    $output[] = 'note (OLD) : ' . trim($cs2->get_answernote());
                    $difference = true;
                }
                $output[] = 'key        : ' . $cs->get_key();
                if ($comparemode && (trim($cs2->get_key()) !== trim($cs->get_key()))) {
                    $output[] = 'key (OLD)  : ' . trim($cs2->get_key());
                    $difference = true;
                }
                $output[] = 'value      : ' . $cs->get_casstring();
                if ($comparemode && (trim($cs2->get_casstring()) !== trim($cs->get_casstring()))) {
                    $output[] = 'value (OLD): ' . trim($cs2->get_casstring());
                    $difference = true;
                }
                if ($cs->ast !== null) {
                    $output[] = $cs->ast->debugPrint($teststring);
                }

                $results[$heading] = implode("\n", $output);
            }
        }
    }
}

// Group.
$distinct = array_unique($results);
foreach ($distinct as $val) {
    foreach ($results as $heading => $result) {
        if ($result == $val) {
            cli_heading($heading);
        }
    }
    cli_writeln($val);
}
if ($comparemode) {
    if ($difference) {
        cli_writeln('NOTE! the old and new validations provided different output.');
    }
    if ($keydifference) {
        cli_writeln('NOTE! A difference in validity between old and new was seen.');
    }
}
