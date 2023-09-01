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

// This script is for testing parsers with different lexers.
//
// @copyright  2023 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

require_once(__DIR__ . '/../stack/maximaparser/autogen/parser-root.php');
require_once(__DIR__ . '/../stack/maximaparser/autogen/parser-equivline.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../stack/maximaparser/lexers/cas.php');
require_once(__DIR__ . '/../stack/maximaparser/lexers/localised.php');
require_once(__DIR__ . '/../stack/maximaparser/parser.options.class.php');


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help' => false,
    'string' => '1+2x', 'only' => false, 'list' => false, 'rootonly' => false, 'exception' => false), array('h' => 'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Test parsing of a given input:

          --string=\"1+2x\"

          Limit to specific lexer-config with:

          --only=CAS

          Test with Equivline instead of Root:

          --equivline

          List lexer configs:

          --list

          Show raw parser exception if triggered:
          --exception
        ";
    echo $help;
    die;
}
$src = $options['string'];
echo "Testing with '$src'\n";

$baseoptions = stack_parser_options::get_cas_config();
$baseoptions->tryinsert = '*';
$baseoptionslisp = stack_parser_options::get_cas_config();
$baseoptionslisp->lispids = true;
$baseoptionslisp->tryinsert = '*';

$baseoptionsfi = new stack_parser_options();
$baseoptionsfi->tryinsert = '*';
$baseoptionsfi->listseparator = ';';
$baseoptionsfi->decimalseparator = ',';
$baseoptionsfi->statementendtokens = ['$'];
$baseoptionsfi->decimalgroupping = [' '];

$baseoptionsold = stack_parser_options::get_old_config();

$loptions = [];

$loptions['CAS'] = [$baseoptions, 'Normal CAS syntax. [tryinsert=*]'];
$loptions['CAS-LISP'] = [$baseoptionslisp, 'Normal CAS syntax with LISP identifiers enabled. [tryinsert=*]'];
$loptions['OLD'] = [$baseoptionsold, 'Old normal config for student input, with Unicode replacement.'];
$loptions['FI'] = [$baseoptionsfi, 'FI locale DS=",", LS=";", ET="$", DG=" "'];



if ($options['list']) {
    $help =
        "Currently known lexer configs:
        ";
    foreach ($loptions as $name => [$opts, $desc]) {
    	$help .= " $name:
           $desc
    	";
    }
    $help .= "\n";
    echo $help;
    die;
}

// Which ones are we running.
$lexerstotest = array_keys($loptions);
if ($options['only'] !== false && isset($loptions[$options['only']])) {
	$lexerstotest = [$options['only']];
}

// Do token test.
echo "\nToken output tests:\n";
foreach ($lexerstotest as $name) {
	$lexer = $loptions[$name][0]->get_lexer($src);
	echo "\n $name:\n";
	$token = $lexer->get_next_token();
	while ($token !== null) {
		echo "  $token\n";
		$token = $lexer->get_next_token();
	}
}


// Do parse test
echo "\nParser output tests:\n";
foreach ($lexerstotest as $name) {
	$opts = $loptions[$name][0];
	$lexer = $opts->get_lexer($src);
	$lexer->reset();
	$tostringopts = $opts->get_toString_options();
	echo "\n $name:\n";
	
	if (!$options['equivline']) {
		$lexer->reset();
		echo "\n  Root:\n";
		$opts->primaryrule = 'Root';
		try {
			$ast = stack_maxima_parser2_root::parse($lexer, $opts->tryinsert, !$opts->dropcomments);
			echo $ast->debugPrint($src);
			echo "\n toString():\n";
			echo $ast->toString($tostringopts) . "\n";
			echo $ast->toString();
		} catch (stack_parser_exception $e) {
			echo "\n FAILED trying exception translation: ";
			$errors = [];
			$answernotes = [];
			maxima_parser_utils::translate_exception($e, $errors, $answernotes);
			echo "\n ERRORS: \n";
			print_r($errors);
			echo "\n ANSWERNOTES: \n";
			print_r($answernotes);
			if ($options['exception']) {
				echo("\n EXCEPTION:");
				print_r($e);
			}
		} catch (Exception $e) {
			echo "\n FAILED: ";
			print_r($e);
		}
		echo "\n";
	} else {
		$lexer->reset();
		echo "\n  Equivline:\n";
		$opts->primaryrule = 'Equivline';
		try {
			$ast = stack_maxima_parser2_equivline::parse($lexer, $opts->tryinsert, !$opts->dropcomments);
			echo $ast->debugPrint($src);
			echo "\n toString():\n";
			echo $ast->toString($tostringopts) . "\n";
			echo $ast->toString();
		} catch (stack_parser_exception $e) {
			echo "\n FAILED trying exception translation: ";
			maxima_parser_utils::translate_exception($e);
			$errors = [];
			$answernotes = [];
			maxima_parser_utils::translate_exception($e, $errors, $answernotes);
			echo "\n ERRORS: \n";
			print_r($errors);
			echo "\n ANSWERNOTES: \n";
			print_r($answernotes);
			if ($options['exception']) {
				echo("\n EXCEPTION:");
				print_r($e);
			}
		} catch (Exception $e) {
			echo "\n FAILED: ";
			print_r($e);
		}
		echo "\n";
	}

}
