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

// This script is for executing sensible combinations of all known
// AST filters on a given input. It aims to identify filters that
// affect the given input.
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

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help' => false,
    'string' => '1+2x', 'ast' => false), array('h' => 'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Test the AST filters on a given string.

          --string=\"1+2x\"
        ";
    echo $help;
    die;
}

$teststring = '1+2x';

if (isset($options['string'])) {
    $teststring = $options['string'];
}

$astonly = false;
if (isset($options['ast'])) {
    $astonly = $options['ast'];
}

cli_heading('= testing = ' . $teststring . ' =');

$parseable = true;
$ast = null;
$errors = array();
$answernotes = array();
try {
    $ast = maxima_parser_utils::parse($teststring);
} catch (SyntaxError $e) {
    $parseable = false;

    $ast = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array('startRule' => 'Root',
                               'letToken' => stack_string('equiv_LET')));
}
if ($ast === null) {
    cli_writeln('The test-string was not parseable, even with the corrective parser.');
    foreach ($errors as $err) {
        cli_writeln($err);
    }
    exit(0);
}
if ($parseable) {
    cli_writeln('The test-string was directly parseable.');
} else {
    cli_writeln('The test-string was not directly parseable, some corrections required.');
    $ast->remap_position_data();
}

cli_writeln('The AST is like this before filters:');
cli_writeln($ast->toString(array('flattree' => true)));
cli_writeln($ast->debugPrint($ast->toString(array('nosemicolon' => true))));

if ($astonly) {
    die();
}

function check_filter($ast, $filter, $security, $filtername) {
    $errors = array();
    $answernotes = array();
    $ast->remap_position_data();
    $pre = $ast->debugPrint($ast->toString(array('nosemicolon' => true)));
    $filter->filter($ast, $errors, $answernotes, $security);
    $ast->remap_position_data();
    $post = $ast->debugPrint($ast->toString(array('nosemicolon' => true)));
    if ($pre === $post && count($errors) === 0 && count($answernotes) === 0) {
        cli_writeln($filtername . ' had no effect');
    } else {
        cli_writeln($filtername . ' did change things');
        cli_writeln(' answernotes: ' . implode(', ', $answernotes));
        cli_writeln(' errors: ' . implode(', ', $errors));
        cli_writeln(' AST after ' . $filtername . ':');
        cli_writeln($post);
    }
}

cli_heading('= Every filter on its own on the raw AST, without units =');
$filters = stack_parsing_rule_factory::list_filters();
sort($filters);
foreach ($filters as $filtername) {
    $filter = stack_parsing_rule_factory::get_by_common_name($filtername);
    // We need a fresh AST.
    $freshast = null;
    if ($parseable) {
        $freshast = maxima_parser_utils::parse($teststring);
    } else {
        $freshast = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array('startRule' => 'Root',
                               'letToken' => stack_string('equiv_LET')));
    }
    check_filter($freshast, $filter, new stack_cas_security(false), $filtername);
}

cli_heading('= Every filter on its own on the raw AST, with units =');
foreach ($filters as $filtername) {
    $filter = stack_parsing_rule_factory::get_by_common_name($filtername);
    // We need a fresh AST.
    $freshast = null;
    if ($parseable) {
        $freshast = maxima_parser_utils::parse($teststring);
    } else {
        $freshast = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array('startRule' => 'Root',
                               'letToken' => stack_string('equiv_LET')));
    }
    check_filter($freshast, $filter, new stack_cas_security(true), $filtername);
}

cli_heading('= core + security(s) =');
$freshast = null;
if ($parseable) {
    $freshast = maxima_parser_utils::parse($teststring);
} else {
    $freshast = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array('startRule' => 'Root',
                           'letToken' => stack_string('equiv_LET')));
}

$pipeline = stack_parsing_rule_factory::get_filter_pipeline(array('998_security'),
        array('998_security' => array('security' => 's')), true);
check_filter($freshast, $pipeline, new stack_cas_security(false), 'core + security(s)');

cli_heading('= core + security(t) + strict =');
$freshast = null;
if ($parseable) {
    $freshast = maxima_parser_utils::parse($teststring);
} else {
    $freshast = maxima_corrective_parser::parse($teststring, $errors, $answernotes, array('startRule' => 'Root',
                           'letToken' => stack_string('equiv_LET')));
}

$pipeline = stack_parsing_rule_factory::get_filter_pipeline(array('998_security', '999_strict'),
        array('998_security' => array('security' => 't')), true);
check_filter($freshast, $pipeline, new stack_cas_security(false), 'core + security(t) + strict');