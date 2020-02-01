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

defined('MOODLE_INTERNAL') || die();

/**
 * Class which undertakes process control to connect to Maxima.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_connection_db_cache implements stack_cas_connection {
    /** @var stack_cas_connection the un-cached connection to Maxima. */
    protected $rawconnection;

    /** @var stack_debug_log does the debugging. */
    protected $debug;

    /** @var moodle_database The database connection to use for the cache. */
    protected $db;

    /**
     * Constructor.
     * @param stack_cas_connection $rawconnection the un-cached connection.
     * @param stack_debug_log $debuglog the debug log to use.
     */
    public function __construct(stack_cas_connection $rawconnection, stack_debug_log $debuglog, moodle_database $db) {
        $this->rawconnection = $rawconnection;
        $this->debug = $debuglog;
        $this->db = $db;
    }

    public function compute($command) {
        $cached = $this->get_cached_result($command);
        if ($cached->result) {
            $this->debug->log('Maxima command', $command);
            // @codingStandardsIgnoreStart
            $this->debug->log('Unpacked result found in the DB cache', print_r($cached->result, true));
            // @codingStandardsIgnoreEnd
            if (!stack_connection_helper::check_stackmaxima_version($cached->result)) {
                stack_connection_helper::warn_about_version_mismatch($this->debug);
                // We could consider automatically purging the cache here.
            }
            return $cached->result;
        }
        $this->debug->log('Maxima command not found in the cache. Using the raw connection.');
        $result = $this->rawconnection->compute($command);
        // Only add to the cache if we didn't timeout!
        if (!stack_connection_helper::did_cas_timeout($result)) {
            $this->add_to_cache($command, $result, $cached->key);
        }
        return $result;
    }

    public function json_compute($command): array {
        $cached = $this->get_cached_result($command);
        if ($cached->result) {
            $this->debug->log('Maxima command', $command);
            // @codingStandardsIgnoreStart
            $this->debug->log('Unpacked result found in the DB cache', print_r($cached->result, true));
            // @codingStandardsIgnoreEnd
            return $cached->result;
        }
        $this->debug->log('Maxima command not found in the cache. Using the raw connection.');
        $this->debug->log('Maxima command', $command);
        $parsed = $this->rawconnection->json_compute($command);

        // Only add to the cache if we didn't timeout!
        if (!stack_connection_helper::did_cas_timeout($parsed)) {
            $this->add_to_cache($command, $parsed, $cached->key);
        }
        // @codingStandardsIgnoreStart
        $this->debug->log('Parsed result as', print_r($parsed, true));
        // @codingStandardsIgnoreEnd

        return $parsed;
    }

    public function get_debuginfo() {
        return $this->debug->get_log();
    }

    /**
     * Get the cached result, if known.
     * @param string $command Maxima code to execute.
     * @return object with two fields:
     *      ->result, the cached result, if any, otherwise null, and
     *      ->key, the hashed key used to index this result.
     */
    protected function get_cached_result($command) {
        $cached = new stdClass();
        $cached->key = $this->get_cache_key($command);

        // Are there any cached records that might match?
        $data = $this->db->get_records('qtype_stack_cas_cache',
                array('hash' => $cached->key), 'id');
        if (!$data) {
            // Nothing relevant in the cache.
            $cached->result = null;
            return $cached;
        }

        // Get the data from the first record.
        $record = reset($data);
        if ($record->command != $command) {
            throw new stack_exception('stack_cas_connection_db_cache: the command found at hash key ' .
                    $cached->key . ' did not match what was expected.');
        }
        $cached->result = json_decode($record->result, true);

        // If there was more than one record in the cache (due to a race condition)
        // drop the duplicates.
        unset($data[$record->id]);
        if ($data) {
            $this->db->delete_records_list('qtype_stack_cas_cache', 'id', array_keys($data));
        }

        return $cached;
    }

    /**
     * Add a new result to the cache.
     * @param string $command Maxima code to execute.
     * @param array $result the result from Maxima for this command.
     * @param string $key the key used to store this command, if already known.
     */
    protected function add_to_cache($command, $result, $key = null) {
        if (is_null($key)) {
            $key = $this->get_cache_key($command);
        }

        $data = new stdClass();
        $data->hash = $key;
        $data->command = $command;
        $data->result = json_encode($result);

        $this->db->insert_record('qtype_stack_cas_cache', $data);
    }

    /**
     * @param string $command Maxima code to execute.
     * @return string the key used to store this command.
     */
    protected function get_cache_key($command) {
        return sha1($command);
    }

    /**
     * Completely clear the cache.
     * @param moodle_database $db the database connection to use to access the cache.
     */
    public static function clear_cache($db) {
        // Delete the cache records from the database.
        $db->delete_records('qtype_stack_cas_cache');

        // Also take this opportunity to empty the plots folder on disc.
        $plots = glob(stack_cas_configuration::images_location() . '/*.{png,svg}', GLOB_BRACE);
        $a = ['total' => count($plots), 'done' => 0];
        $progressevery = (int) min(max(1, $a['total'] / 500), 100);
        if ($a['total'] > 0) {
            $pbar = new progress_bar('clearstackcache', 500, true);
            foreach ($plots as $plot) {
                unlink($plot);

                $a['done'] += 1;
                if ($a['done'] % $progressevery == 0 || $a['done'] == $a['total']) {
                    core_php_time_limit::raise(60);
                    $pbar->update($a['done'], $a['total'], get_string('clearingcachefiles', 'qtype_stack', $a));
                }
            }
        }
    }

    /**
     * @param moodle_database $db the database connection to use to access the cache.
     * @return int the number of entries in the cache.
     */
    public static function entries_count($db) {
        return $db->count_records('qtype_stack_cas_cache');
    }
}
