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

require_once(__DIR__ . '/../stack/cas/connectorhelper.class.php');
require_once(__DIR__ . '/../stack/cas/connector.dbcache.class.php');
require_once(__DIR__ . '/../stack/cas/installhelper.class.php');

/**
 * Upgrade code for the Stack question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_stack_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012030300) {

        // Define table qtype_stack_cas_cache to be created.
        $table = new xmldb_table('qtype_stack_cas_cache');

        // Adding fields to table qtype_stack_cas_cache.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('hash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('command', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('result', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_cas_cache.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table qtype_stack_cas_cache.
        $table->add_index('hash', XMLDB_INDEX_UNIQUE, array('hash'));

        // Conditionally launch create table for qtype_stack_cas_cache.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012030300, 'qtype', 'stack');
    }

    if ($oldversion < 2012030900) {

        // Define table qtype_stack to be created.
        $table = new xmldb_table('qtype_stack');

        // Adding fields to table qtype_stack.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('questionvariables', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('specificfeedback', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('specificfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('questionnote', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('questionsimplify', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('assumepositive', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('markmode', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, 'penalty');
        $table->add_field('prtcorrect', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('prtcorrectformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('prtpartiallycorrect', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('prtpartiallycorrectformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('prtincorrect', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('prtincorrectformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('multiplicationsign', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, 'dot');
        $table->add_field('sqrtsign', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('complexno', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, 'i');

        // Adding keys to table qtype_stack.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE, array('questionid'), 'question', array('id'));

        // Conditionally launch create table for qtype_stack.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012030900, 'qtype', 'stack');
    }

    if ($oldversion < 2012030901) {

        // Define table qtype_stack_inputs to be created.
        $table = new xmldb_table('qtype_stack_inputs');

        // Adding fields to table qtype_stack_inputs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tans', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('boxsize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '15');
        $table->add_field('strictsyntax', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('insertstars', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('syntaxhint', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forbidfloat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('requirelowestterms', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('checkanswertype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('showvalidation', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table qtype_stack_inputs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));

        // Adding indexes to table qtype_stack_inputs.
        $table->add_index('questionid-name', XMLDB_INDEX_UNIQUE, array('questionid', 'name'));

        // Conditionally launch create table for qtype_stack_inputs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012030901, 'qtype', 'stack');
    }

    if ($oldversion < 2012030902) {

        // Define table qtype_stack_prts to be created.
        $table = new xmldb_table('qtype_stack_prts');

        // Adding fields to table qtype_stack_prts.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('autosimplify', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('feedbackvariables', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_prts.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));
        $table->add_key('questionid-name', XMLDB_KEY_UNIQUE, array('questionid', 'name'));

        // Conditionally launch create table for qtype_stack_prts.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012030902, 'qtype', 'stack');
    }

    if ($oldversion < 2012030903) {

        // Define table qtype_stack_prt_nodes to be created.
        $table = new xmldb_table('qtype_stack_prt_nodes');

        // Adding fields to table qtype_stack_prt_nodes.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('prtname', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('nodename', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, null);
        $table->add_field('answertest', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sans', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tans', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('testoptions', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('quiet', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('truescoremode', XMLDB_TYPE_CHAR, '4', null, XMLDB_NOTNULL, null, '=');
        $table->add_field('truescore', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('truepenalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('truenextnode', XMLDB_TYPE_CHAR, '8', null, null, null, null);
        $table->add_field('trueanswernote', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('truefeedback', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('truefeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('falsescoremode', XMLDB_TYPE_CHAR, '4', null, XMLDB_NOTNULL, null, '=');
        $table->add_field('falsescore', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, '0');
        $table->add_field('falsepenalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('falsenextnode', XMLDB_TYPE_CHAR, '8', null, null, null, null);
        $table->add_field('falseanswernote', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('falsefeedback', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('falsefeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table qtype_stack_prt_nodes.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid-name', XMLDB_KEY_FOREIGN_UNIQUE, array('questionid', 'prtname'),
                'qtype_stack_prts', array('questionid', 'name'));

        // Adding indexes to table qtype_stack_prt_nodes.
        $table->add_index('questionid-prtname-nodename', XMLDB_INDEX_UNIQUE, array('questionid', 'prtname', 'nodename'));

        // Conditionally launch create table for qtype_stack_prt_nodes.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012030903, 'qtype', 'stack');
    }

    if ($oldversion < 2012031301) {
        // Define key questionid-name (foreign) to be dropped form qtype_stack_prt_nodes.
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $key = new xmldb_key('questionid-name', XMLDB_KEY_FOREIGN_UNIQUE, array('questionid', 'prtname'),
                'qtype_stack_prts', array('questionid', 'name'));

        // Launch drop key questionid-name.
        $dbman->drop_key($table, $key);

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012031301, 'qtype', 'stack');
    }

    if ($oldversion < 2012031302) {

        // Define key questionid-name (foreign) to be added to qtype_stack_prt_nodes.
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $key = new xmldb_key('questionid-name', XMLDB_KEY_FOREIGN, array('questionid', 'prtname'),
                'qtype_stack_prts', array('questionid', 'name'));

        // Launch add key questionid-name.
        $dbman->add_key($table, $key);

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012031302, 'qtype', 'stack');
    }

    if ($oldversion < 2012031600) {

        // Define field forbidwords to be added to qtype_stack_inputs.
        $table = new xmldb_table('qtype_stack_inputs');
        $field = new xmldb_field('forbidwords', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'syntaxhint');

        // Conditionally launch add field forbidwords.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012031600, 'qtype', 'stack');
    }

    if ($oldversion < 2012031601) {

        // Define field mustverify to be added to qtype_stack_inputs.
        $table = new xmldb_table('qtype_stack_inputs');
        $field = new xmldb_field('mustverify', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'checkanswertype');

        // Conditionally launch add field mustverify.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012031601, 'qtype', 'stack');
    }

    if ($oldversion < 2012032200) {

        // Define table qtype_stack_qtests to be created.
        $table = new xmldb_table('qtype_stack_qtests');

        // Adding fields to table qtype_stack_qtests.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('testcase', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_qtests.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));
        $table->add_key('questionid-testcase', XMLDB_KEY_UNIQUE, array('questionid', 'testcase'));

        // Conditionally launch create table for qtype_stack_qtests.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012032200, 'qtype', 'stack');
    }

    if ($oldversion < 2012032201) {

        // Define table qtype_stack_qtest_inputs to be created.
        $table = new xmldb_table('qtype_stack_qtest_inputs');

        // Adding fields to table qtype_stack_qtest_inputs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('testcase', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('inputname', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_qtest_inputs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid-testcase', XMLDB_KEY_FOREIGN,
                array('questionid', 'testcase'), 'qtype_stack_qtests', array('questionid', 'testcase'));

        // Adding indexes to table qtype_stack_qtest_inputs.
        $table->add_index('questionid-testcase-inputname', XMLDB_INDEX_UNIQUE,
                array('questionid', 'testcase', 'inputname'));

        // Conditionally launch create table for qtype_stack_qtest_inputs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012032201, 'qtype', 'stack');
    }

    if ($oldversion < 2012032202) {

        // Define table qtype_stack_qtest_expected to be created.
        $table = new xmldb_table('qtype_stack_qtest_expected');

        // Adding fields to table qtype_stack_qtest_expected.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('testcase', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('prtname', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expectedscore', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expectedpenalty', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expectedanswernote', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_qtest_expected.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid-testcase', XMLDB_KEY_FOREIGN,
                array('questionid', 'testcase'), 'qtype_stack_qtests', array('questionid', 'testcase'));

        // Adding indexes to table qtype_stack_qtest_expected.
        $table->add_index('questionid-testcase-prtname', XMLDB_INDEX_UNIQUE,
                array('questionid', 'testcase', 'prtname'));

        // Conditionally launch create table for qtype_stack_qtest_expected.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012032202, 'qtype', 'stack');
    }

    if ($oldversion < 2012032300) {

        // Define table qtype_stack_deployed_seeds to be created.
        $table = new xmldb_table('qtype_stack_deployed_seeds');

        // Adding fields to table qtype_stack_deployed_seeds.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('seed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_deployed_seeds.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));

        // Adding indexes to table qtype_stack_deployed_seeds.
        $table->add_index('questionid-seed', XMLDB_INDEX_UNIQUE, array('questionid', 'seed'));

        // Conditionally launch create table for qtype_stack_deployed_seeds.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012032300, 'qtype', 'stack');
    }

    if ($oldversion < 2012033000) {

        // Define field variantsselectionseed to be added to qtype_stack.
        $table = new xmldb_table('qtype_stack');
        $field = new xmldb_field('variantsselectionseed', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'complexno');

        // Conditionally launch add field variantsselectionseed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012033000, 'qtype', 'stack');
    }

    if ($oldversion < 2012043000) {

        // Changing nullability of field expectedscore on table qtype_stack_qtest_expected to null.
        $table = new xmldb_table('qtype_stack_qtest_expected');
        $field = new xmldb_field('expectedscore', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'prtname');

        // Launch change of nullability for field expectedscore.
        $dbman->change_field_notnull($table, $field);

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012043000, 'qtype', 'stack');
    }

    if ($oldversion < 2012043001) {

        // Changing nullability of field expectedpenalty on table qtype_stack_qtest_expected to null.
        $table = new xmldb_table('qtype_stack_qtest_expected');
        $field = new xmldb_field('expectedpenalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'expectedscore');

        // Launch change of nullability for field expectedpenalty.
        $dbman->change_field_notnull($table, $field);

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012043001, 'qtype', 'stack');
    }

    if ($oldversion < 2012061500) {
        // Define field markmode to be dropped from qtype_stack.
        $table = new xmldb_table('qtype_stack');
        $field = new xmldb_field('markmode');

        // Conditionally launch drop field markmode.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012061500, 'qtype', 'stack');
    }

    if ($oldversion < 2012061501) {
        // Changing type of field truepenalty on table qtype_stack_prt_nodes to number.
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $field = new xmldb_field('truepenalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'truescore');

        // Launch change of type for field truepenalty.
        $dbman->change_field_type($table, $field);

        // Changing type of field falsepenalty on table qtype_stack_prt_nodes to number.
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $field = new xmldb_field('falsepenalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'falsescore');

        // Launch change of type for field falsepenalty.
        $dbman->change_field_type($table, $field);

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012061501, 'qtype', 'stack');
    }

    // We want to change the index on hash from unique to non-unique, which seems
    // to involve dropping it and re-creating.
    if ($oldversion < 2012062100) {

        // Define index hash (not unique) to be dropped form qtype_stack_cas_cache.
        $table = new xmldb_table('qtype_stack_cas_cache');
        $index = new xmldb_index('hash', XMLDB_INDEX_UNIQUE, array('hash'));

        // Conditionally launch drop index hash.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012062100, 'qtype', 'stack');
    }

    if ($oldversion < 2012062101) {

        // Define index hash (not unique) to be added to qtype_stack_cas_cache.
        $table = new xmldb_table('qtype_stack_cas_cache');
        $index = new xmldb_index('hash', XMLDB_INDEX_NOTUNIQUE, array('hash'));

        // Conditionally launch add index hash.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2012062101, 'qtype', 'stack');
    }

    if ($oldversion < 2012062504) {

        // Changing precision of field sans on table qtype_stack_prt_nodes to (255).
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $field = new xmldb_field('sans', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'answertest');

        // Launch change of precision for field sans.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field tans on table qtype_stack_prt_nodes to (255).
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $field = new xmldb_field('tans', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'sans');

        // Launch change of precision for field tans.
        $dbman->change_field_precision($table, $field);

        // Changing precision of field testoptions on table qtype_stack_prt_nodes to (255).
        $table = new xmldb_table('qtype_stack_prt_nodes');
        $field = new xmldb_field('testoptions', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'tans');

        // Launch change of precision for field testoptions.
        $dbman->change_field_precision($table, $field);

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2012062504, 'qtype', 'stack');
    }

    if ($oldversion < 2013030100) {

        // Define table qtype_stack to be renamed to qtype_stack_options.
        $table = new xmldb_table('qtype_stack');

        // Launch rename table for qtype_stack.
        $dbman->rename_table($table, 'qtype_stack_options');

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030100, 'qtype', 'stack');
    }

    if ($oldversion < 2013030101) {

        // Define field inversetrig to be added to qtype_stack_options.
        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('inversetrig', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, 'cos-1', 'complexno');

        // Conditionally launch add field inversetrig.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030101, 'qtype', 'stack');
    }

    if ($oldversion < 2013030102) {

        // Define field options to be added to qtype_stack_inputs.
        $table = new xmldb_table('qtype_stack_inputs');
        $field = new xmldb_field('options', XMLDB_TYPE_TEXT, null, null, null, null, null, 'showvalidation');

        // Conditionally launch add field options.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030102, 'qtype', 'stack');
    }

    if ($oldversion < 2013030103) {

        // Fill qtype_stack_inputs.options column with empty strings.
        $DB->set_field('qtype_stack_inputs', 'options', '');

        // Question stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030103, 'qtype', 'stack');
    }

    if ($oldversion < 2013030104) {

        // Changing nullability of field options on table qtype_stack_inputs to not null.
        $table = new xmldb_table('qtype_stack_inputs');
        $field = new xmldb_field('options', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'showvalidation');

        // Launch change of nullability for field options.
        $dbman->change_field_notnull($table, $field);

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030104, 'qtype', 'stack');
    }

    if ($oldversion < 2013030800) {

        // Define field firstnodename to be added to qtype_stack_prts.
        $table = new xmldb_table('qtype_stack_prts');
        $field = new xmldb_field('firstnodename', XMLDB_TYPE_CHAR, '8', null, null, null, null, 'feedbackvariables');

        // Conditionally launch add field firstnodename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030800, 'qtype', 'stack');
    }

    if ($oldversion < 2013030801) {
        // Fill the qtype_stack_prts.firstnodename column.
        $DB->execute('UPDATE {qtype_stack_prts} SET firstnodename = (
                      SELECT MIN(' . $DB->sql_cast_char2int('nodename') . ')
                        FROM {qtype_stack_prt_nodes} nodes
                       WHERE nodes.questionid = {qtype_stack_prts}.questionid
                         AND nodes.prtname = {qtype_stack_prts}.name
                    )');

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030801, 'qtype', 'stack');
    }

    if ($oldversion < 2013030802) {

        // Changing nullability of field firstnodename on table qtype_stack_prts to not null.
        $table = new xmldb_table('qtype_stack_prts');
        $field = new xmldb_field('firstnodename', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, null, 'feedbackvariables');

        // Launch change of nullability for field firstnodename.
        $dbman->change_field_notnull($table, $field);

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2013030802, 'qtype', 'stack');
    }

    if ($oldversion < 2013091900) {

        // Define table qtype_stack_inputs to be created.
        $table = new xmldb_table('qtype_stack_inputs');

        $field = new xmldb_field('allowwords', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'forbidwords');

        // Conditionally launch add field forbidwords.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2013091900, 'qtype', 'stack');
    }

    if ($oldversion < 2014040501) {

        // Define field matrixparens to be added to qtype_stack_options.
        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('matrixparens', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, '[', 'inversetrig');

        // Conditionally launch add field matrixparens.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2014040501, 'qtype', 'stack');
    }

    if ($oldversion < 2016012900) {

        // Convert approximate thirds for penalties in the question table.
        $DB->set_field_select('question', 'penalty', '0.3333333',
                'qtype = ? AND penalty BETWEEN ? AND ?', array('stack', '0.33', '0.34'));
        $DB->set_field_select('question', 'penalty', '0.6666667',
                'qtype = ? AND penalty BETWEEN ? AND ?', array('stack', '0.66', '0.67'));

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2016012900, 'qtype', 'stack');
    }

    if ($oldversion < 2016012901) {

        // Convert approximate thirds for penalties in the qtype_stack_prt_nodes table.
        $DB->set_field_select('qtype_stack_prt_nodes', 'truepenalty', '0.3333333',
                'truepenalty BETWEEN ? AND ?', array('0.33', '0.34'));
        $DB->set_field_select('qtype_stack_prt_nodes', 'truepenalty', '0.6666667',
                'truepenalty BETWEEN ? AND ?', array('0.66', '0.67'));
        $DB->set_field_select('qtype_stack_prt_nodes', 'falsepenalty', '0.3333333',
                'falsepenalty BETWEEN ? AND ?', array('0.33', '0.34'));
        $DB->set_field_select('qtype_stack_prt_nodes', 'falsepenalty', '0.6666667',
                'falsepenalty BETWEEN ? AND ?', array('0.66', '0.67'));

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2016012901, 'qtype', 'stack');
    }

    if ($oldversion < 2016012902) {

        // Convert approximate thirds for penalties in the qtype_stack_qtest_expected table.
        $DB->set_field_select('qtype_stack_qtest_expected', 'expectedpenalty', '0.3333333',
                'expectedpenalty BETWEEN ? AND ?', array('0.33', '0.34'));
        $DB->set_field_select('qtype_stack_qtest_expected', 'expectedpenalty', '0.6666667',
                'expectedpenalty BETWEEN ? AND ?', array('0.66', '0.67'));

        upgrade_plugin_savepoint(true, 2016012902, 'qtype', 'stack');
    }

    if ($oldversion < 2016082000) {

        // Define table qtype_stack_inputs to be created.
        $table = new xmldb_table('qtype_stack_inputs');

        $field = new xmldb_field('syntaxattribute', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'syntaxhint');

        // Conditionally launch add field syntaxattribute.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2016082000, 'qtype', 'stack');
    }

    if ($oldversion < 2017082300) {

        // Define field assumepositive to be added to qtype_stack_options.
        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('assumereal', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'assumepositive');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2017082300, 'qtype', 'stack');
    }

    if ($oldversion < 2017082400) {
        // Changing type of field questionnote on table qtype_stack_options to text.
        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('questionnote', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);

        // Launch change of type for field questionnote.
        $dbman->change_field_type($table, $field);
        $dbman->change_field_default($table, $field);

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2017082400, 'qtype', 'stack');
    }

    if ($oldversion < 2018021900) {

        // Define field timemodified to be added to qtype_stack_qtests.
        $table = new xmldb_table('qtype_stack_qtests');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'testcase');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Stack savepoint reached.
        upgrade_plugin_savepoint(true, 2018021900, 'qtype', 'stack');
    }

    if ($oldversion < 2018021901) {

        // Define table qtype_stack_qtest_results to be created.
        $table = new xmldb_table('qtype_stack_qtest_results');

        // Adding fields to table qtype_stack_qtest_results.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('testcase', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('seed', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('result', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timerun', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_stack_qtest_results.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid-testcase', XMLDB_KEY_FOREIGN, array('questionid', 'testcase'),
                'qtype_stack_qtests', array('questionid', 'testcase'));

        // Adding indexes to table qtype_stack_qtest_results.
        $table->add_index('questionid-testcase-seed', XMLDB_INDEX_UNIQUE, array('questionid', 'testcase', 'seed'));

        // Conditionally launch create table for qtype_stack_qtest_results.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Stack savepoint reached.
        upgrade_plugin_savepoint(true, 2018021901, 'qtype', 'stack');
    }

    if ($oldversion < 2018060102) {

        // Define field stackversion to be added to qtype_stack_options.
        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('stackversion', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'questionid');

        // Conditionally launch add field stackversion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // STACK savepoint reached.
        upgrade_plugin_savepoint(true, 2018060102, 'qtype', 'stack');
    }

    if ($oldversion < 2018120500) {

        // Changing nullability of field stackversion on table qtype_stack_options to null.
        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('stackversion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questionid');

        // Conditionally launch add field stackversion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Launch change of nullability for field stackversion.
        $dbman->change_field_notnull($table, $field);

        // Stack savepoint reached.
        upgrade_plugin_savepoint(true, 2018120500, 'qtype', 'stack');
    }

    if ($oldversion < 2019041600) {
        upgrade_plugin_savepoint(true, 2019041600, 'qtype', 'stack');
    }

    if ($oldversion < 2020041100) {

        $table = new xmldb_table('qtype_stack_options');
        $field = new xmldb_field('logicsymbol', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, 'lang', 'inversetrig');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2020041100, 'qtype', 'stack');
    }

    if ($oldversion < 2020041400) {

        $table = new xmldb_table('qtype_stack_prts');
        $field = new xmldb_field('feedbackstyle', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'autosimplify');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Qtype stack savepoint reached.
        upgrade_plugin_savepoint(true, 2020041400, 'qtype', 'stack');
    }

    if ($oldversion < 2020112100) {
        if (get_config('qtype_stack', 'platform') == 'unix' || get_config('qtype_stack', 'platform') == 'unix-optimised') {
            set_config('platform', 'linux', 'qtype_stack');
            set_config('maximacommandopt', get_config('qtype_stack', 'maximacommand'), 'qtype_stack');
            set_config('maximacommand', '', 'qtype_stack');
        }
        if (get_config('qtype_stack', 'platform') == 'server') {
            set_config('maximacommandserver', get_config('qtype_stack', 'maximacommand'), 'qtype_stack');
            set_config('maximacommand', '', 'qtype_stack');
        }
        upgrade_plugin_savepoint(true, 2020112100, 'qtype', 'stack');
    }

    // Add new upgrade blocks just above here.

    // Check the version of the Maxima library code that comes with this version
    // of STACK. Compare that to the version that was previously in use. If they
    // are different, automatically clear the CAS cache.

    // This block of code is intentionally outside of an if statement. We want
    // this bit of code to run every time that qtype_stack is updated.
    if (!preg_match('~stackmaximaversion:(\d{10})~',
            file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
                throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
    }
    $latestversion = $matches[1];
    $currentlyusedversion = get_config('qtype_stack', 'stackmaximaversion');

    // Update the record of the currently used version.
    set_config('stackmaximaversion', $latestversion, 'qtype_stack');

    // If appropriate, clear the CAS cache and re-generate the image.
    if ($latestversion != $currentlyusedversion) {
        stack_cas_connection_db_cache::clear_cache($DB);
        if (get_config('qtype_stack', 'platform') !== 'server') {
            $pbar = new progress_bar('healthautomaxopt', 500, true);
            list($ok, $message) = stack_cas_configuration::create_auto_maxima_image();
            $pbar->update(500, 500, get_string('healthautomaxopt', 'qtype_stack', array()));
            if (!$ok) {
                echo html_writer::div($message, 'adminwarning');
                echo html_writer::div(get_string('healthautomaxoptintro', 'qtype_stack'), 'adminwarning');
            }
        }
    }

    return true;
}
