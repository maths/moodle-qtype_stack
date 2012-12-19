<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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


require_once(dirname(__FILE__) . '/connector.interface.php');
require_once(dirname(__FILE__) . '/connector.class.php');
require_once(dirname(__FILE__) . '/connector.dbcache.class.php');


/**
 * The base class for connections to Maxima.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class stack_connection_helper {
    /** @var stdClass cached copy of the STACK configuration settings. */
    protected static $config = null;

    /** @var moodle_database keeps the connection to the 'otherdb' if we are using that option. */
    protected static $otherdb = null;

    /**
     * Ensure that self::$config is set.
     */
    protected static function ensure_config_loaded() {
        if (is_null(self::$config)) {
            self::$config = stack_utils::get_config();
        }
    }

    /**
     * Create a Maxima connection.
     * @return stack_cas_connection the connection.
     */
    public static function make() {
        self::ensure_config_loaded();

        $debuglog = stack_utils::make_debug_log(self::$config->casdebugging);

        switch (self::$config->platform) {
            case 'win':
                require_once(dirname(__FILE__) . '/connector.windows.class.php');
                $connection = new stack_cas_connection_windows(self::$config, $debuglog);
                break;
            case 'unix':
            case 'unix-optimised':
                require_once(dirname(__FILE__) . '/connector.unix.class.php');
                $connection = new stack_cas_connection_unix(self::$config, $debuglog);
                break;
            case 'server':
                require_once(dirname(__FILE__) . '/connector.server.class.php');
                $connection = new stack_cas_connection_server(self::$config, $debuglog);
                break;
            case 'tomcat':
                require_once(dirname(__FILE__) . '/connector.tomcat.class.php');
                $connection = new stack_cas_connection_tomcat(self::$config, $debuglog);
                break;

            default:
                throw new stack_exception('stack_cas_connection: Unknown platform ' . self::$config->platform);
        }

        switch (self::$config->casresultscache) {
            case 'db':
                global $DB;
                $connection = new stack_cas_connection_db_cache($connection, $debuglog, $DB);
                break;

            case 'otherdb':
                $connection = new stack_cas_connection_db_cache($connection, $debuglog, self::get_other_db());
                break;

            default:
                // Just use the raw $connection.
        }

        return $connection;
    }

    /**
     * Initialises the database connection for the 'otherdb' cache type.
     * @return moodle_database the DB connection to use.
     */
    protected static function get_other_db() {
        if (!is_null(self::$otherdb)) {
            return self::$otherdb;
        }

        $dboptions = array();
        if (!empty(self::$config->cascachedbsocket)) {
            $dboptions['dbsocket'] = true;
        }

        self::$otherdb = moodle_database::get_driver_instance(
                self::$config->cascachedbtype, self::$config->cascachedblibrary);
        self::$otherdb->connect(self::$config->cascachedbhost,
                self::$config->cascachedbuser, self::$config->cascachedbpass,
                self::$config->cascachedbname, self::$config->cascachedbprefix, $dboptions);
        return self::$otherdb;
    }

    /**
     * @return the configured platform type.
     */
    public static function get_platform() {
        self::ensure_config_loaded();
        return self::$config->platform;
    }

    /**
     * @return bool whether the CAS timed out.
     */
    public static function did_cas_timeout($result) {
        foreach ($result as $res) {
            if (array_key_exists('error', $res)) {
                if (!(false===strpos($res['error'], 'The CAS timed out'))) {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * This method checks the version information returned from the STACK-Maxima
     * libraries agains the version number we expect for this version of
     * qtype_stack.
     * @param array $unpackedresult the result of the CAS call.
     * @return bool whether the CAS call used an compatible library version.
     */
    public static function check_stackmaxima_version($unpackedresult) {
        self::ensure_config_loaded();

        if (!isset(self::$config->stackmaximaversion)) {
            // STACK not fully installed/updated. Report this as an error.
            return false;
        }

        if (empty($unpackedresult)) {
            // CAS syntax errors lead to nothing at all being returned. Don't
            // report this as a version check failure.
            return true;
        }

        foreach ($unpackedresult as $result) {
            if (array_key_exists('error', $result)) {
                // If an error has happened before we output the version number,
                // then we cannot check it, so return OK to avoid false postitives.
                return true;
            }

            if ($result['key'] != '__stackmaximaversion') {
                continue;
            }

            return self::$config->stackmaximaversion === $result['value'];
        }

        return false;
    }

    /**
     * Used when check_stackmaxima_version returns false. Give an appropriate
     * warning.
     * @param stack_debug_log $debug log to write debug information to.
     */
    public static function warn_about_version_mismatch($debug) {
        $warning = "WARNING: the version of the STACK-Maxima libraries used do not match the expected version. " .
                "Please visit the STACK heathcheck page to resolve the problems.";
        $debug->log($warning);
        debugging($warning);
    }

    public static function stackmaxima_version_healthcheck() {
        self::ensure_config_loaded();

        $command = 'cab:block([],print("[TimeStamp= [ 0 ], Locals= [ 0=[ error= ["), ' .
                'cte("__stackmaximaversion",errcatch(__stackmaximaversion:stackmaximaversion)), print("] ]"), return(true));' . "\n";
        $connection = self::make();
        $results = $connection->compute($command);

        if (!isset(self::$config->stackmaximaversion)) {
            $notificationsurl = new moodle_url('/admin/index.php');
            return array('healthchecksstackmaximanotupdated', array($notificationsurl->out()));
        }

        $usedversion = stack_string('healthchecksstackmaximatooold');
        foreach ($results as $result) {
            if ($result['key'] != '__stackmaximaversion') {
                continue;
            }

            if (self::$config->stackmaximaversion == $result['value']) {
                return null;
            } else {
                $usedversion = $result['value'];
                break;
            }
        }

        switch (self::$config->platform) {
            case 'unix-optimised':
                $docsurl = new moodle_url('/question/type/stack/doc/doc.php/CAS/Optimising_Maxima.md');
                $fix = stack_string('healthchecksstackmaximaversionfixoptimised', $docsurl);
                break;

            case 'server':
                $fix = stack_string('healthchecksstackmaximaversionfixserver');
                break;

            default:
                $fix = stack_string('healthchecksstackmaximaversionfixunknown');
        }

        return array('healthchecksstackmaximaversionmismatch',
                array('fix' => $fix, 'usedversion' => $usedversion,
                    'expectedversion' => self::$config->stackmaximaversion));
    }
}
