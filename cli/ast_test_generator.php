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

// This script generates unit-tests for AST-filters using a common
// list of example inputs. The generated tests exist only to document
// and freeze the behaviour of the filters. When running this
// script it overwrites the existing tests so do check if you agree
// with the diffs.
//
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');
require_once(__DIR__ . '/../stack/cas/cassecurity.class.php');

require_once(__DIR__ . '/../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../stack/maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../stack/utils.class.php');


$devnull = []; // We really do not care about what goes here.

// Load the test-inputs.
$inputs = json_decode(file_get_contents(__DIR__ . '/../tests/fixtures/test_strings.json'), true);
$inputs = array_unique($inputs);

// Ensure that the inputs are parseable.
$okinputs = [];
foreach ($inputs as $input) {
    $test = maxima_corrective_parser::parse($input,
            $devnull, $devnull, ['startRule' => 'Root',
            'letToken' => stack_string('equiv_LET')]);
    if ($test !== null) {
        $okinputs[] = $input;
    }
}
$inputs = $okinputs;

// The filters to test.
$filters = [];
foreach (stack_parsing_rule_factory::list_filters() as $filter) {
    $filters[$filter] = stack_parsing_rule_factory::get_by_common_name($filter);
}

// Add the core set as a separate filter.
$filters['000_099_common_core'] = stack_parsing_rule_factory::get_filter_pipeline([], [], true);

// The security settings tested.
$secunits = new stack_cas_security(true);
$secnounits = new stack_cas_security(false);

cli_heading('First evaluate the behaviour, then generate the tests');

cli_writeln('Starting to parse the inputs, this will take time.');

// Lets try the memory usage and timing of the parser and parse the strings
// for each combination. input-filter-units-combination...
$total = 0;
$start = microtime(true);

// ASTs.
$asts = ['units' => [], 'no units' => []];
foreach ($filters as $key => $filter) {
    $asts['units'][$key] = [];
    $asts['no units'][$key] = [];
    foreach ($inputs as $input) {
        $ast = maxima_corrective_parser::parse($input,
            $devnull, $devnull, ['startRule' => 'Root',
            'letToken' => stack_string('equiv_LET')]);
        $asts['units'][$key][$input] = $ast;
        $ast = maxima_corrective_parser::parse($input,
            $devnull, $devnull, ['startRule' => 'Root',
            'letToken' => stack_string('equiv_LET')]);
        $asts['no units'][$key][$input] = $ast;
        $total = $total + 2;
    }
}

$parsetime = microtime(true) - $start;

cli_writeln('Parsed the test inputs ' . (2 * count($filters)) . ' ('. $total . ') times. Average parsetime was ' .
        (1000 * $parsetime / $total) . 'ms');

cli_writeln('');

cli_heading('Executing filters');

// Evaluate all.
$filtertimes = [];
$errors = ['units' => [], 'no units' => []];
$notes = ['units' => [], 'no units' => []];
foreach ($filters as $key => $filter) {
    $errors['units'][$key] = [];
    $errors['no units'][$key] = [];
    $notes['units'][$key] = [];
    $notes['no units'][$key] = [];
    $start = microtime(true);
    foreach ($inputs as $input) {
        $err = [];
        $nos = [];
        $asts['units'][$key][$input] = $filter->filter($asts['units'][$key][$input], $err, $nos, $secunits);
        $errors['units'][$key][$input] = $err;
        $notes['units'][$key][$input] = $nos;
        $err = [];
        $nos = [];
        $asts['no units'][$key][$input] = $filter->filter($asts['no units'][$key][$input], $err, $nos, $secnounits);
        $errors['no units'][$key][$input] = $err;
        $notes['no units'][$key][$input] = $nos;
    }
    $filtertime = microtime(true) - $start;
    $filtertimes[$key] = $filtertime;
}


cli_writeln('By average time usage the filters are:');
asort($filtertimes);
foreach ($filtertimes as $name => $time) {
    cli_writeln(sprintf(' %01.4fms %s', 1000.0 * $time / (2.0 * count($inputs)), $name));
}

cli_writeln('');

cli_heading('Now generating the tests');

// Some common things.
$nl = "\n";
$indent = '    ';
$indent2 = $indent . $indent;

$hasinvalid = false;
$findinvalid = function($node) use(&$hasinvalid) {
    if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
        $hasinvalid = true;
        return false;
    }
    return true;
};

function escp(string $string): string {
    // TODO: for coding style we should not use double quotes unless necessary.
    $a = addslashes($string);
    if (strpos($a, "'") === false) {
        $a = str_replace('\"', '"', $a);
        return "'$a'";
    }

    $a = str_replace("\'", "'", $a);
    return '"' . $a . '"';
}

// Place to store all the code.
$generatedcode = [];
foreach ($filters as $key => $filter) {
    $code = '<?' . 'php' . $nl;
    $code .= <<<ESCAPE
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

namespace qtype_stack;

use qtype_stack_ast_testcase;
use stack_cas_security;
use stack_parsing_rule_factory;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../tests/fixtures/ast_filter_test_base.php');

// Auto-generated unit tests for AST-filter DO NOT EDIT!
/**
 * @group qtype_stack
 * @group qtype_stack_ast_filters
ESCAPE;

    $code .= "\n * @covers \ast_filter_{$key}\n */\n\n";

    $code .= "class ast_filter_{$key}_auto_generated_test extends qtype_stack_ast_testcase {" . $nl;

    $testactiveunits = $indent . 'public function test_affected_units() {' . $nl;
    $testactiveunits .= $indent2 . '$this->security = new stack_cas_security(true);' . $nl;
    $testpassiveunits = $indent . 'public function test_non_affected_units() {' . $nl;
    $testpassiveunits .= $indent2 . '$this->security = new stack_cas_security(true);' . $nl;
    $testactivenounits = $nl . $indent . 'public function test_affected_no_units() {' . $nl;
    $testactivenounits .= $indent2 . '$this->security = new stack_cas_security(false);' . $nl;
    $testpassivenounits = $indent . 'public function test_non_affected_no_units() {' . $nl;
    $testpassivenounits .= $indent2 . '$this->security = new stack_cas_security(false);' . $nl;

    if (substr($key, 0, 3) !== '000') {
        $testactiveunits .= $indent2 . '$this->filter = stack_parsing_rule_factory::get_by_common_name(\'' . addslashes($key) .
            '\');' . $nl . $nl;
        $testpassiveunits .= $indent2 . '$this->filter = stack_parsing_rule_factory::get_by_common_name(\'' . addslashes($key) .
            '\');' . $nl . $nl;
        $testactivenounits .= $indent2 . '$this->filter = stack_parsing_rule_factory::get_by_common_name(\'' . addslashes($key) .
            '\');' . $nl . $nl;
        $testpassivenounits .= $indent2 . '$this->filter = stack_parsing_rule_factory::get_by_common_name(\'' . addslashes($key) .
            '\');' . $nl . $nl;
    } else {
        $testactiveunits .= $indent2 .
            '$this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);' . $nl . $nl;
        $testpassiveunits .= $indent2 .
            '$this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);' . $nl . $nl;
        $testactivenounits .= $indent2 .
            '$this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);' . $nl . $nl;
        $testpassivenounits .= $indent2 .
            '$this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);' . $nl . $nl;
    }


    foreach ($inputs as $input) {
        // What does it look if nothing changes.
        $base = maxima_corrective_parser::parse($input,
            $devnull, $devnull, ['startRule' => 'Root',
            'letToken' => stack_string('equiv_LET')]);
        $basestring = $base->toString(['nosemicolon' => true]);

        // Check with units.
        $affects = false;
        $args = [$input];
        $args[] = $asts['units'][$key][$input]->toString(['nosemicolon' => true]);
        if ($args[1] !== $basestring) {
            $affects = true;
        }
        $args[] = $notes['units'][$key][$input];
        if (!empty($notes['units'][$key][$input])) {
            $affects = true;
        }
        $hasinvalid = false;
        $asts['units'][$key][$input]->callbackRecurse($findinvalid);
        $args[] = !$hasinvalid;
        if ($hasinvalid === true) {
            $affects = true;
        }
        $args[] = !empty($errors['units'][$key][$input]);
        if (!empty($errors['units'][$key][$input])) {
            $affects = true;
        }

        $item = $indent2 . '$this->expect(' . escp($args[0]) .',' . $nl;
        $item .= $indent2 . '              ' . escp($args[1]) . ',' . $nl;
        $item .= $indent2 . '              array(';
        $nos = array_map('trim', $args[2]);
        $nos = array_map('escp', $nos);
        if (!empty($nos)) {
            $item .= implode(', ', $nos);
        }
        $item .= '),' . $nl;
        $item .= $indent2 . '              ' . ($args[3] === true ? 'true' : 'false') .
            ', ' . ($args[4] === true ? 'true' : 'false') . ');' . $nl;
        if ($affects) {
            $testactiveunits .= $item . $nl;
        } else {
            $testpassiveunits .= $item . $nl;
        }

        // No units.
        $affects = false;
        $args = [$input];
        $args[] = $asts['no units'][$key][$input]->toString(['nosemicolon' => true]);
        if ($args[1] !== $basestring) {
            $affects = true;
        }
        $args[] = $notes['no units'][$key][$input];
        if (!empty($notes['no units'][$key][$input])) {
            $affects = true;
        }
        $hasinvalid = false;
        $asts['no units'][$key][$input]->callbackRecurse($findinvalid);
        $args[] = !$hasinvalid;
        if ($hasinvalid === true) {
            $affects = true;
        }
        $args[] = !empty($errors['no units'][$key][$input]);
        if (!empty($errors['no units'][$key][$input])) {
            $affects = true;
        }

        $item = $indent2 . '$this->expect(' . escp($args[0]) .',' . $nl;
        $item .= $indent2 . '              ' . escp($args[1]) . ',' . $nl;
        $item .= $indent2 . '              array(';
        $nos = array_map('trim', $args[2]);
        $nos = array_map('escp', $nos);
        if (!empty($nos)) {
            $item .= implode(', ', $nos);
        }
        $item .= '),' . $nl;
        $item .= $indent2 . '              ' . ($args[3] === true ? 'true' : 'false') .
            ', ' . ($args[4] === true ? 'true' : 'false') . ');' . $nl;
        if ($affects) {
            $testactivenounits .= $item . $nl;
        } else {
            $testpassivenounits .= $item . $nl;
        }
    }

    $testactiveunits .= $indent . '}' . $nl;
    $testpassiveunits .= $indent . '}' . $nl;
    $testactivenounits .= $indent . '}' . $nl;
    $testpassivenounits .= $indent . '}' . $nl;

    $code .= $testactivenounits . $nl;
    $code .= $testactiveunits . $nl;
    $code .= $testpassiveunits . $nl;
    $code .= $testpassivenounits;

    $code .= '}' . $nl;
    $generatedcode[$key] = $code;
}


cli_heading('Comparing to existing tests');
foreach ($generatedcode as $key => $code) {
    $name = __DIR__ . '/../tests/ast_filter_' . $key . '_auto_generated_test.php';
    $old = false;
    if (file_exists($name)) {
        $old = file_get_contents($name);
    }
    if ($old === false) {
        cli_writeln('Could not read ' . $name . '.');
        cli_writeln('Maybe it did not exist yet.');
    }
    if ($old !== $code) {
        if ($old !== false) {
            cli_writeln('Old content did not match for ' . $key);
        }
        file_put_contents($name, $code);
    }
}
