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

/**
 * This script is for testing the execution of the old and the new parser.
 *
 * @package    qtype_stack
 * @copyright  2025 Aalto University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');
require_once(__DIR__ . '/../stack/cas/cassecurity.class.php');

require_once(__DIR__ . '/../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../stack/maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/maximaparser/autogen/parser-root.php');
require_once(__DIR__ . '/../stack/maximaparser/parser.options.class.php');
require_once(__DIR__ . '/../stack/maximaparser/error.interpreter.class.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'time' => false,
    'string' => '1+2*x', 'ast' => false, 'only' => false,
], ['h' => 'help']);
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Test the parsers on a given string.

          --string=\"1+2x\"

Parse 10000 times and report runtime.

          --time
";
    echo $help;
    die;
}

$teststring = '1+2*x';

if (isset($options['string'])) {
    $teststring = $options['string'];
}

$parseable = true;
$astold = null;
$astnew = null;
$errors = [];
$answernotes = [];
$count = 1;
if (isset($options['time']) && $options['time']) {
    $count = 10000;
}

$parseoptions = [
    'startRule' => 'Root',
    'letToken' => stack_string('equiv_LET'),
    'allowPM' => true,
];

$olderrors = [];
$oldanswernotes = [];

$startold = microtime(true);
for ($i = 0; $i < $count; $i++) {
    try {
        $parser = new MP_Parser();
        $astold = $parser->parse($teststring, $parseoptions);
    } catch (SyntaxError $e) {
        $parseable = false;
        
        try {
            $astold = maxima_corrective_parser::parse($teststring, $olderrors, $oldanswernotes, [
                'startRule' => 'Root',
                'letToken' => stack_string('equiv_LET'),
            ]);
        } catch (SyntaxError $ee) {
            maxima_corrective_parser::handle_parse_error($ee, $teststring, $olderrors, $oldanswernotes);
            break;
        }
    }
}
$endold = microtime(true);

$newerrors = [];
$newanswernotes = [];

$startnew = microtime(true);
for ($i = 0; $i < $count; $i++) {
    try {
        $options = stack_parser_options::get_old_config();
        $parser = $options->get_parser();
        $lexer = $options->get_lexer($teststring);
        $astnew = $parser->parse($lexer, $newanswernotes);
    } catch (stack_maxima_parser_exception $e) {
        $ei = new stack_parser_error_interpreter($options);
        $ei->interprete($e, $newerrors, $newanswernotes);
        break;
    }
}
$endnew = microtime(true);

if ($parseable) {
    cli_writeln('The test-string was directly parseable by the old parser.');
} else {
    if ($astold !== null) {
        cli_writeln('The test-string was not directly parseable by the old parser, some corrections required.');
        $astold->remap_position_data();
    }
}

if ($astold !== null) {
    cli_writeln('The old AST is like this:');
    cli_writeln($astold->debugPrint($astold->toString(['nosemicolon' => true])));
} else {
    cli_writeln('The old parser did not parse, errors and notes as follows:');
    print_r($olderrors);
    print_r($oldanswernotes);
}

if ($astnew !== null) {
    cli_writeln('The new AST is like this:');
    cli_writeln($astnew->debugPrint($astnew->toString(['nosemicolon' => true])));
} else {
    cli_writeln('The new parser did not parse, errors and notes as follows:');
    print_r($newerrors);
    print_r($newanswernotes);
}

if ($count > 1) {
    cli_writeln("Time use comparison with $count repetitions:");
    cli_writeln(' The old took: ' . (($endold - $startold)));
    cli_writeln(' The new took: ' . (($endnew - $startnew)));
    cli_writeln('  Old was ' . (($endold - $startold)/($endnew - $startnew)) . ' times slower.');
}