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

require_once(__DIR__ . '/platform.class.php');


/**
 * Intermediary abstract class representing local platform, either optimised or
 * non-optimised, rather than a server type platform.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Stephen Parry (stephen@edumake.org)
 *
 */
abstract class stack_platform_local extends stack_platform_base {
    /*
     * Class Loading and Metadata Member Functions
     * ===========================================
     *
     * These functions manage how platforms are made available to STACK and instantiated.
     */

    /**
     * Constructor - only available from the get() factory function, as each platform is a singleton.
     * @param string $name The name of the platform.
     */
    protected function __construct($name) {
        parent::__construct($name);
    }

    /**
     * Internal function called by check_maxima_install()
     * Checks that the configured Maxima and the actual one match and are
     * available on the system.
     * provides. Adds error to $error collection if appropriate.
     * @param array $errors
     * @return boolean Returns true if ok or cannot be determined.
     */
    protected function check_maxima_version(&$errors) {
        $settings = stack_utils::get_config();
        $versionisdefault = !isset($settings->maximaversion) || !$settings->maximaversion || 'default' === $settings->maximaversion;
        $rv = true;
        if (!$versionisdefault) {
            $version = $settings->maximaversion;
            $actualversion = $this->get_actual_maxima_version();
            if(0 !== strncasecmp($version, $actualversion, strlen($version))) {
                $errors[] = stack_string('healthcheckmaximaversionnotmatch',
                        array("config" => $version, "actual" => $actualversion));
                $rv = false;
            }
            $versions = $this->get_list_of_maxima_versions();
            if ($versions && !array_key_exists($settings->maximaversion, $versions)) {
                $errors[] = stack_string('healthcheckmaximaversionnotpresent',
                        array("chosen" => $settings->maximaversion,
                            "available" => implode(', ', array_keys($versions))));
                $rv = false;
            }
        }
        return $rv;
    }

    /**
     * Gets list of available lisp flavours. If Maxima version is configured or
     * can be determined, the list is version specific. Otherwise, all installed
     * flavours are returned.
     * @param boolean $all If all is true, get from all versions.
     * @return array|null Get list of available lisps as an array of strings.
     */
    protected function get_list_of_lisps($all = false) {
        $lisps = null;
        if ($this->can_list_maxima_versions())
        {
            if (!$all) {
                $version = $this->get_actual_maxima_version();
                if (!$version) {
                    $settings = stack_utils::get_config();
                    $versionisdefault = !isset($settings->maximaversion) || !$settings->maximaversion || 'default' === $settings->maximaversion;
                    if (!$versionisdefault) {
                        $version = $settings->maximaversion;
                    }
                }
            }
            $versions = $this->get_list_of_maxima_versions();
            if($versions) {
                if (!$all && $version && array_key_exists($version, $versions)) {
                    if(isset($versions[$version]['lisps'])) {
                        $lisps = $versions[$version]['lisps'];
                    } else {
                        $lisps = array();
                    }
                } else {
                    $lisps = array();
                    foreach ($versions as $v) {
                        if (isset($v['lisps'])) {
                            $lisps = array_merge($lisps, $v['lisps']);
                        }
                    }
                }
            }
        }
        return $lisps;
    }

    /**
     * Internal function called by check_maxima_install()
     * Checks that the configured lisp and the actual one match.
     * provides. Adds error to $errors collection if appropriate.
     * @param array $errors
     * @return boolean Returns true if ok or cannot be determined.
     */
    protected function check_lisp(&$errors) {
        $settings = stack_utils::get_config();
        $lispisdefault = !isset($settings->lisp) || !$settings->lisp || 'default' === $settings->lisp;
        $rv = true;
        if (!$lispisdefault && $this->can_list_maxima_versions()) {
            $lisp = $settings->lisp;
            $actuallisp = $this->get_actual_lisp();
            if ( $actuallisp ) {
                if(0 !== strcasecmp($actuallisp, $lisp)) {
                    $errors[] = stack_string('healthchecklispnotmatch',
                            array("config" => $lisp, "actual" => $actuallisp));
                    $rv = false;
                }
            }
            $lisps = $this->get_list_of_lisps();
            if (FALSE === array_key_exists($lisp, $lisps)) {
                $errors[] = stack_string('healthcheckmaximaversionlispnotpresent',
                        array("chosen" => $lisp,
                            "available" => implode(', ', $lisps)));
                $rv = false;
            }
        }
        return $rv;
    }

    /**
     * Internal function called by check_maxima_install()
     * Checks whther the default commands have been overridden and adds to warnings.
     * @param array $warnings
     * @return boolean Returns true if ok or cannot be determined.
     */
    protected function check_overrides(&$warnings) {
        $settings = stack_utils::get_config();
        if ($settings->maximacommand || $settings->maximapreoptcommand) {
            $warnings[] = stack_string('healthcheckwarningcommandoverride');
            return false;
        }
        return true;
    }

    /*
     * Maxima Program Member Functions
     * ===============================
     *
     * These member functions manipulate configuration, location and files related to how the Maxima
     * executable actually gets executed. The connection class objects actually do the launching and
     * connecting; the installer classes generate some of the less platform specific files, such as
     * maximalocal.mac and any optimised image, but these functions manage the more platform
     * specific aspects, such as where the files are located.
     *
     */

    /**
     * Ensures the stack data directory is present, if needed for this platform,
     * and creates it if not.
     * @return boolean true if directory found / created ok or not needed
     */
    public function ensure_data_directory() {
        make_upload_directory('stack');
        make_upload_directory('stack/logs');
        make_upload_directory('stack/plots');
        make_upload_directory('stack/tmp');
        return true;
    }


}