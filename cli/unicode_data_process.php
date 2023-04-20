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

// This script fetches Unicode mappings from:
//  https://github.com/numbas/unicode-math-normalization
//
// And preprocesses them for use in STACK logic. 
//
// @copyright  2023 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');


$greek = file_get_contents('https://raw.githubusercontent.com/numbas/unicode-math-normalization/main/final_data/greek.json');
$letters = file_get_contents('https://raw.githubusercontent.com/numbas/unicode-math-normalization/main/final_data/letters.json');
$subscripts = file_get_contents('https://raw.githubusercontent.com/numbas/unicode-math-normalization/main/final_data/subscripts.json');
$superscripts = file_get_contents('https://raw.githubusercontent.com/numbas/unicode-math-normalization/main/final_data/superscripts.json');
$symbols = file_get_contents('https://raw.githubusercontent.com/numbas/unicode-math-normalization/main/final_data/symbols.json');

if ($greek === false || $letters === false || $subscripts === false || $superscripts === false || $symbols === false) {

	die('Problem fetching the data.');
}


$greek = json_decode($greek, true);
$letters = json_decode($letters, true);
$subscripts = json_decode($subscripts, true);
$superscripts = json_decode($superscripts, true);
$symbols = json_decode($symbols, true);

// First extract the most common things we convert.
$commonsymbolconversion = [];

foreach ($greek as $key => $value) {
	switch ($value[0]) {
		case 'inifinity':
			$commonsymbolconversion[$key] = 'inf';	
			continue;
		case 'emptyset':
			$commonsymbolconversion[$key] = '{}';	
			continue;
	}
	$commonsymbolconversion[$key] = $value[0];
}

$unmaps = [];

foreach ($letters as $key => $value) {
	// Drop specifics.
	if (array_key_exists('LOOPED', $value[1]) !== false) {
		continue;
	}
	if ($value[0] === 'inifinity') {
		continue;
	}
	if ($value[0] === 'emptyset') {
		continue;
	}
	// Check if maps to non unicode.
	if (mb_detect_encoding($value[0], 'ASCII', true) === false) {
		if (!isset($commonsymbolconversion[$value[0]])) {
			$unmaps[$key] = $value[0];
		} else {
			$commonsymbolconversion[$key] = $commonsymbolconversion[$value[0]];
		}
	} else {
		$commonsymbolconversion[$key] = $value[0];
	}
}


// Map things that have intermediary steps.
$changes = true;
while($changes) {
	$changes = false;
	foreach (array_keys($unmaps) as $key) {
		if (isset($commonsymbolconversion[$unmaps[$key]])) {
			$commonsymbolconversion[$key] = $commonsymbolconversion[$unmaps[$key]];
			unset($unmaps[$key]);
			$changes = true;
		}
	}
}


// Then look at specific symbols, ops and parenthesis.
$symbolconversion = [];

foreach ($symbols as $key => $value) {
	// Only pick certain ones
	switch($value[0]) {
		case '-':
		case '+':
		case '/':
		case '*':
		case '^':
		case '(':
		case ')':
		case '[':
		case ']':
		case '{':
		case '}':
		case '>=':
		case '=':
		case '<=':
		case '>':
		case '<':
			$symbolconversion[$key] = $value[0];

	}


}

$superscriptsconversion = [];

foreach ($superscripts as $key => $value) {
	if (mb_detect_encoding($value[0], 'ASCII', true) === false) {
		if (isset($commonsymbolconversion[$value[0]])) {
			$superscriptsconversion[$key] = $commonsymbolconversion[$value[0]];
		} else {
			// Ignore for now.
		}
	} else {
		$superscriptsconversion[$key] = $value[0];
	}
}

$subscriptsconversion = [];

foreach ($subscripts as $key => $value) {
	if (mb_detect_encoding($value[0], 'ASCII', true) === false) {
		if (isset($commonsymbolconversion[$value[0]])) {
			$subscriptsconversion[$key] = $commonsymbolconversion[$value[0]];
		} else {
			// Ignore for now.
		}
	} else {
		$subscriptsconversion[$key] = $value[0];
	}
}



file_put_contents('../unicode/symbols-stack.json', json_encode($symbolconversion, JSON_PRETTY_PRINT));
file_put_contents('../unicode/superscript-stack.json', json_encode($superscriptsconversion, JSON_PRETTY_PRINT));
file_put_contents('../unicode/subscript-stack.json', json_encode($subscriptsconversion, JSON_PRETTY_PRINT));

file_put_contents('../unicode/letters-stack.json', json_encode($commonsymbolconversion, JSON_PRETTY_PRINT));

