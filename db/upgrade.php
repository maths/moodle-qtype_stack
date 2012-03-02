<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Stack question type upgrade code.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the Stack question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_stack_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012030300) {

        // Define table qtype_stack_cas_cache to be created
        $table = new xmldb_table('qtype_stack_cas_cache');

        // Adding fields to table qtype_stack_cas_cache
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('hash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('command', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('result', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_cas_cache
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table qtype_stack_cas_cache
        $table->add_index('hash', XMLDB_INDEX_UNIQUE, array('hash'));

        // Conditionally launch create table for qtype_stack_cas_cache
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // stack savepoint reached
        upgrade_plugin_savepoint(true, 2012030300, 'qtype', 'stack');
    }

    return true;
}
