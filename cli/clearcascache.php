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

/**
 * Script to clear the CAS cache from the command-line.
 *
 * @package    qtype_stack
 * @subpackage cli
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot . '/question/type/stack/stack/cas/cassession2.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/cas/connector.dbcache.class.php');

// Get cli options.
list($options, $unrecognized) = cli_get_params(['help' => false], ['h' => 'help']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo "This script clears the STACK CAS cache. This can safely be done at any time,
but afterwards STACK questions will run a bit slower for a while.
";
    exit(0);
}

echo "Clearing the CAS cache, which contains " .
        stack_cas_connection_db_cache::entries_count($DB) .
        " entries.\n";
stack_cas_connection_db_cache::clear_cache($DB);
echo "\nCache cleared successfully.\n\n";
exit(0);
