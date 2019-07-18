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

// This script parses STACKs Maxima libraries and tries to find variables 
// that leak
//
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');


// Get the files ../stack/maxima/*.mac

foreach (glob("../stack/maxima/*.mac") as $filename) {
	if (strpos($filename, 'maximaidentifierclassification.mac') !== false) {
		continue;
	}
	if (strpos($filename, 'rtest_') !== false) {
		continue;
	}
	if (strpos($filename, 'unittests_load.mac') !== false) {
		continue;
	}
	if (strpos($filename, 'maximaidentifierdump.mac') !== false) {
		continue;
	}
	cli_heading($filename);

	$contents = file_get_contents($filename);
	// The parser does nto deal with dollars.
	$contents = str_replace('$', ';', $contents);

	// Parse.
	try {
		$ast = maxima_parser_utils::parse($contents);
		$ast = maxima_parser_utils::position_remap($ast, $contents);
		$ast = maxima_parser_utils::strip_comments($ast);
		// Null recurse to get things tagged.
		$ast->callbackRecurse();

		cli_writeln('Items at top');
		foreach ($ast->items as $top) {
			// All things are statements.
			$top = $top->statement;
			if ($top instanceof MP_FunctionCall) {
				cli_writeln($top->position['start'] . ' Call of ' . $top->toString());
			} else if ($top instanceof MP_Operation && $top->op === ':') {
				cli_writeln($top->position['start'] . ' Declare value ' . $top->lhs->toString());
			} else if ($top instanceof MP_Operation && $top->op === ':=') {
				cli_writeln($top->position['start'] . ' Declare function ' . $top->lhs->toString());

				$vars = array();
				$globalsearch = function($node) use (&$vars) {
					if ($node instanceof MP_Identifier &&
						$node->is_variable_name() &&
						$node->is_global()) {
						$vars[$node->value] = true;
					}
					return true;
				};
				$top->rhs->callbackRecurse($globalsearch);

				if (count($vars) > 0) {
					cli_writeln('  !!! global variables: ' . implode(', ', array_keys($vars)));
				}
			}
		}


	}  catch (SyntaxError $e) {
		cli_writeln('parse error @ ' . $e->grammarOffset);
	}

}