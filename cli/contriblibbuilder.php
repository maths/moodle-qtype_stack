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
 * This script executes the `genmanifest:` logic and additional documentation
 * generation for the arguments and then places the results into the normal places.
 *
 * The manifest will end up in the root of `/stack/maxima/contrib/$X.stacklib`,
 * and the docs into `/doc/en/CAS/ContribLibraries/$X.md`. $X being the first argument
 * declaring the shortname of this library.
 *
 * The arguments can be an arbirary number of `.mac` files for the logic to be included
 * and an optional `.md` file to be used as a preamble for the documentation.
 *
 * @package    qtype_stack
 * @copyright  2026 Aalto University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

require_once(__DIR__ . '/../stack/cas/contriblibrarytools.class.php');
require_once(__DIR__ . '/../stack/maximaparser/parser.options.class.php');
require_once(__DIR__ . '/../stack/maximaparser/error.interpreter.class.php');


if (count($argv) < 3) {
	echo("This command requires atleast two arguments, the first being the name of the resulting library and the remaining ones the paths to the files it consists of.");	
	exit(0);
}

$name = $argv[1];

if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) { 
	echo("The library name should match the pattern '^[a-zA-Z0-9_]+$'.");
	exit(0);
}

$files = [];
for ($i = 2; $i < count($argv); $i++) {
	// Note that the genmanifest generator restricts where one can load so we use the bypass to give the content to it.
	$shortname = '/stack/maxima/' . (explode('/stack/stack/maxima/', $argv[$i], 2)[1]);
	$content = file_get_contents($argv[$i]);
	if ($content === false) {
		echo("Cannot read " . $argv[$i] . " maybe try full path?");
		exit(0);
	}
	$files[$shortname] = $content;
}

$genaddress = 'genmanifest: ' . implode(' ', array_keys($files));

$manifest = stack_cas_contrib_library_tools::generate_manifest($genaddress, null, $files);

file_put_contents('../stack/maxima/contrib/' . $name . '.stacklib', json_encode($manifest, JSON_PRETTY_PRINT));


//TODO: gen docs...