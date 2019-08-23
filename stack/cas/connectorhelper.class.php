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

require_once(__DIR__ . '/connector.interface.php');
require_once(__DIR__ . '/connector.class.php');
require_once(__DIR__ . '/connector.dbcache.class.php');
require_once(__DIR__ . '/installhelper.class.php');


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
                require_once(__DIR__ . '/connector.windows.class.php');
                $connection = new stack_cas_connection_windows(self::$config, $debuglog);
                break;
            case 'unix':
            case 'unix-optimised':
                require_once(__DIR__ . '/connector.unix.class.php');
                $connection = new stack_cas_connection_unix(self::$config, $debuglog);
                break;
            case 'server':
                require_once(__DIR__ . '/connector.server.class.php');
                $connection = new stack_cas_connection_server(self::$config, $debuglog);
                break;
            case 'tomcat':
            case 'tomcat-optimised':
                throw new stack_exception('stack_connection_helper: ' .
                    '"tomcat" and "tomcat-optimised" settings are obsolete. ' .
                    ' Please choose "server" setting instead.');
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
     * @return string the configured platform type.
     */
    public static function get_platform() {
        self::ensure_config_loaded();
        return self::$config->platform;
    }

    /**
     * @return string the configured version number.
     */
    public static function get_maximaversion() {
        self::ensure_config_loaded();
        return self::$config->maximaversion;
    }

    /**
     * Check the result of a CAS computation to see if a time-out occurred.
     * @param array $result as returned from stack_cas_connection::compute.
     * @return bool whether the CAS timed out.
     */
    public static function did_cas_timeout($result) {
        if (array_key_exists('timeout', $result)) {
            // This is the CASSession2 way.
            return $result['timeout'];
        }

        foreach ($result as $res) {
            if (array_key_exists('error', $res)) {
                if (!(false === strpos($res['error'], 'The CAS timed out'))) {
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
     * @return string the version of the STACK Maxima libraries that should be in use.
     */
    public static function get_required_stackmaxima_version() {
        self::ensure_config_loaded();
        return self::$config->stackmaximaversion;
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

    /**
     * Execute a CAS command just so we can get the version number of the
     * remote libraries being used, then check that version against what it should be.
     * @return array with two elements, a string like healthchecksstackmaximaversionok
     * or healthchecksstackmaximanotupdated which can be used as the first argument to,
     * stack_string, and possibly some extra data that can be used as the second argument.
     */
    public static function stackmaxima_version_healthcheck() {
        self::ensure_config_loaded();

        $command = 'cab:block([],print("[STACKSTART Locals= [ 0=[ error= ["), ' .
                'cte("__stackmaximaversion",errcatch(__stackmaximaversion:stackmaximaversion)), print("] ]"), return(true));' .
                "\n";
        $connection = self::make();
        $results = $connection->compute($command);

        if (empty($results)) {
            return array('stackCas_allFailed', array(), false);
        }

        if (!isset(self::$config->stackmaximaversion)) {
            $notificationsurl = new moodle_url('/admin/index.php');
            return array('healthchecksstackmaximanotupdated', array($notificationsurl->out()), false);
        }

        $usedversion = stack_string('healthchecksstackmaximatooold');
        foreach ($results as $result) {
            if ($result['key'] != '__stackmaximaversion') {
                continue;
            }

            $usedversion = $result['value'];
            if (self::$config->stackmaximaversion == $usedversion) {
                return array('healthchecksstackmaximaversionok',
                    array('usedversion' => $usedversion), true);
            } else {
                break;
            }
        }

        switch (self::$config->platform) {
            case 'unix-optimised':
                $docsurl = new moodle_url('/question/type/stack/doc/doc.php/CAS/Optimising_Maxima.md');
                $fix = stack_string('healthchecksstackmaximaversionfixoptimised', array('url' => $docsurl->out()));
                break;

            case 'server':
                $fix = stack_string('healthchecksstackmaximaversionfixserver');
                break;

            default:
                $fix = stack_string('healthchecksstackmaximaversionfixunknown');
        }

        return array('healthchecksstackmaximaversionmismatch',
                array('fix' => $fix, 'usedversion' => $usedversion,
                    'expectedversion' => self::$config->stackmaximaversion), false);
    }

    /**
     * Exectue a CAS command, without any caching.
     *
     * @param string the command to execute.
     * @return array with tho elements, the results from compute, and the CAS debug output.
     */
    private static function stackmaxima_nocache_call($command) {
        self::ensure_config_loaded();

        $configcache = self::$config->casresultscache;
        $casdebugging = self::$config->casdebugging;
        self::$config->casresultscache = 'none';
        self::$config->casdebugging = true;

        $connection = self::make();
        $results = $connection->compute($command);

        self::$config->casresultscache = $configcache;
        self::$config->casdebugging = $casdebugging;

        $debug = $connection->get_debuginfo();
        return array($results, $debug);
    }

    /**
     * Really exectue a CAS command, regardless of the cache settings.
     */
    public static function stackmaxima_genuine_connect() {
        self::ensure_config_loaded();

        $maximaversion = self::get_maximaversion();

        // Put something non-trivial in the call.
        $date = date("Y-m-d H:i:s");

        $command = 'cab:block([],print("[STACKSTART Locals= [ 0=[ error= ["), ' .
                'cte("CASresult",errcatch(diff(x^n,x))), print("1=[ error= ["), ' .
                'cte("STACKversion",errcatch(stackmaximaversion)), print("2=[ error= ["), ' .
                'cte("MAXIMAversion",errcatch(MAXIMA_VERSION_STR)), print("3=[ error= ["), ' .
                'cte("MAXIMAversionnum",errcatch(MAXIMA_VERSION_NUM)), print("4=[ error= ["), ' .
                'cte("externalformat",errcatch(adjust_external_format())), print("5=[ error= ["), ' .
                'cte("CAStime",errcatch(CAStime:"'.$date.'")), print("] ]"), return(true));' .
                "\n";

        // Really make sure there is no cache.
        list($results, $debug) = self::stackmaxima_nocache_call($command);

        $success = true;
        $message = array();
        if (empty($results)) {
            $message[] = stack_string('stackCas_allFailed');
            $success = false;
        } else {
            $maximaversionum = 'unknown number';
            foreach ($results as $result) {
                if ('MAXIMAversionnum' === $result['key']) {
                    $maximaversionum = $result['value'];
                }
            }
            foreach ($results as $result) {
                if ('CASresult' === $result['key']) {
                    if ($result['value'] != 'n*x^(n-1)') {
                        $message[] = stack_string('healthuncachedstack_CAS_calculation',
                                array('expected' => "n*x^(n-1)", 'actual' => $result['value']));
                        $success = false;
                    }
                } else if ('CAStime' === $result['key']) {
                    if ($result['value'] != '"'.$date.'"') {
                        $success = false;
                    }
                } else if ('MAXIMAversion' === $result['key']) {
                    $maximaversionstr = $result['value'] . ' ('.$maximaversionum.')';
                    if ('default' == $maximaversion) {
                        $message[] = stack_string('healthuncachedstack_CAS_versionnotchecked',
                                array('actual' => $maximaversionstr));
                    } else if ($result['value'] != '"'.$maximaversion.'"') {
                        $message[] = stack_string('healthuncachedstack_CAS_version',
                                array('expected' => $maximaversion, 'actual' => $maximaversionstr));
                        $success = false;
                    }
                }
            }
        }

        if (strpos($debug, 'failed to load') !== false) {
            $message[] = stack_string('settingmaximalibraries_failed');
            $success = false;
        }

        if ($success) {
            $message[] = stack_string('healthuncachedstack_CAS_ok');
        } else {
            $message[] = stack_string('healthuncachedstack_CAS_not');
        }

        $message = implode(" ", $message);

        return array($message, $debug, $success);
    }

    /*
     * This function is in this class, rather than installhelper.class.php, to
     * ensure the lowest level connection to the CAS, without caching.
     */
    public static function stackmaxima_auto_maxima_optimise($genuinedebug) {
        global $CFG;
        self::ensure_config_loaded();

        $imagename = stack_utils::convert_slash_paths($CFG->dataroot . '/stack/maxima_opt_auto');

        $lisp = '1';
        // Try to guess the lisp version.
        if (!(false === strpos($genuinedebug, 'GNU Common Lisp (GCL)'))) {
            $lisp = 'GCL';
        }
        if (!(false === strpos($genuinedebug, 'Lisp SBCL'))) {
            $lisp = 'SBCL';
        }
        if (!(false === strpos($genuinedebug, 'Lisp CLISP'))) {
            $lisp = 'CLISP';
        }

        switch ($lisp) {
            case 'GCL':
                $maximacommand = ':lisp (si::save-system "'.$imagename.'")' . "\n";
                $maximacommand .= 'quit();'."\n";
                $commandline = stack_utils::convert_slash_paths($imagename . ' -eval \'(cl-user::run)\'');
                break;

            case 'SBCL':
                $maximacommand = ':lisp (sb-ext:save-lisp-and-die "'.$imagename.'" :toplevel #\'run :executable t)' . "\n";
                $commandline = stack_utils::convert_slash_paths($imagename);
                break;

            case 'CLISP':
                $imagename .= '.mem';
                $maximacommand = ':lisp (ext:saveinitmem "'.$imagename.'" :init-function #\'user::run)' . "\n";
                $maximacommand .= 'quit();'."\n";
                $lisprun = shell_exec('locate lisp.run');
                if (trim($lisprun) == '') {
                    $success = false;
                    $message = stack_string('healthautomaxopt_nolisprun');
                    return array($message, '', $success, '');
                }
                $lisprun = explode("\n", $lisprun);
                $commandline = $lisprun[0].' -q -M '.stack_utils::convert_slash_paths($imagename);
                break;

            default:
                $success = false;
                $message = stack_string('healthautomaxopt_nolisp');
                return array($message, '', $success, '');
        }

        // Really make sure there is no cache.
        list($results, $debug) = self::stackmaxima_nocache_call($maximacommand);

        // Question: should we at this stage try to use the optimised image we have created?
        $success = true;

        // Add the timeout command to the message.
        $commandline = 'timeout --kill-after=10s 10s '.$commandline;
        $message = stack_string('healthautomaxopt_ok', array('command' => $commandline));
        if (!file_exists($imagename)) {
            $success = false;
            $message = stack_string('healthautomaxopt_notok');
        }

        return array($message, $debug, $success, $commandline);
    }

}
