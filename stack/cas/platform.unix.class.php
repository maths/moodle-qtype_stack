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
 * Class representing a Unix- or Linux-like or MacOS X platform, either optimised or non-optimised.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Stephen Parry (stephen@edumake.org)
 *
 */
class stack_platform_unix extends stack_platform_base {
    /*
     * Member Variables
     * ================
     */

    /**
     *
     * @var string $default_optimised_pathname full default pathname of the manually optimised
     * maxima image
     * @var string $auto_optimised_pathname full pathname of the auto-optimised maxima image
     * generated using the healthcheck page.
     */
    protected $defaultoptimisedpathname = null;
    protected $autooptimisedpathname = null;

    /*
     * Class Loading and Metadata Member Functions
     * ===========================================
     *
     * These functions manage how platforms are made available to STACK and instantiated.
     *
     */

    /**
     * MacOSX functions in many ways as a Unix variant, so is handled by this platform. This
     * function queries the local machine to determine if it is actually MacOS X
     *
     * @return bool true if this platform is actually MacOSx
     */
    public function is_macosx() {
        return strcasecmp(PHP_OS, "Darwin") == 0;
    }

    /**
     * Constructor - only available from the stack_platform_base::get() factory function, as each
     * platform is a singleton.
     * @param string $name The name of the platform.
     */
    public function __construct($name) {
        parent::__construct($name);
        $this->defaultoptimisedpathname = $this->pathname_concat($this->get_stack_data_dir(), 'maxima-optimised');
        $this->autooptimisedpathname = $this->pathname_concat($this->get_stack_data_dir(), 'maxima_opt_auto');
    }

    /*
     * Connection Class Member Functions
     * =================================
     * These functions link the platform class to the connection class the platform uses
     */

    /**
     * Get the connection class name for this platform.
     *
     * @return string For Linux/Unix/MacOSX Returns 'stack_cas_connection_unix'.
     *
     */
    public function get_connection_class() {
        return 'stack_cas_connection_unix';
    }

    /**
     * Get the source file for the connection class for this platform.
     *
     * @return string For Linux/Unix/MacOSX Returns 'connector.unix.class.php'.
     *
     */
    public function get_connection_source_file() {
        return 'connector.unix.class.php';
    }

    /*
     * Pathname Member Functions
     * =========================
     * Pathnames in this context are filenames that include a full path. PHP supports 'portable' and
     * native pathnames; portable ones use a '/' character as directory separator, native pathnames
     * use a platform specific directory separator. These functions arer specifieed to cope with
     * both formats, and even a mixture.
     */

    /**
     * Some platforms have native and portable paths (e.g. Windows). For others, such as Unix,
     * all pathnames are portable.
     *
     * @return boolean returns true for Linux/Unix/MacOSX.
     */
    public function are_all_pathnames_portable() {
        return true;
    }

    /**
     * Convert a pathname from PHP portable format form to the platform dependent format. Ideally,
     * implementations should be able to tolerate being called on a path that is already in native
     * format.
     * @param string $path The pathname in PHP portable format using directory separator of /
     * @return string Returns the pathname in native format, using the native DIRECTORY_SEPARATOR.
     * For Linux/Unix/MacOSX it is returned unchanged.
     */
    public function pathname_to_native($path) {
        return $path;
    }

    /**
     * Convert a pathname from platform dependent format to PHP portable format form. Ideally,
     * implementations should be able to tolerate being called on a path that is already in PHP
     * portable format.
     * @param string $path The pathname in native format, using the native DIRECTORY_SEPARATOR.
     * @return string The pathname in PHP portable format using directory separator of /; in the
     * case of the Linux/Unix/MacOSX platform, this means the pathname is returned unchanged.
     */
    public function pathname_to_portable($path) {
        return $path;
    }

    /**
     * Analyses a pathname and determines what format it is in:
     *  PATHTYPE_EITHER   : Could be either (no seps or on this platform seps are the same).
     *  PATHTYPE_PORTABLE : Is a path with only portable path separators.
     *  PATHTYPE_NATIVE   : Native separators only.
     *  PATHTYPE_MIXED    : Mixture (yuck).
     * @return int Returns PATHTYPE_EITHER for Linux/Unix/MacOSX platform.
     */
    public function pathname_type_of($e1) {
        return self::PATHTYPE_EITHER;
    }

    /**
     *
     * Takes part of a pathname and appends a second part to it. The pathnames can either be native
     * or portable. The function will preserve that where possible.
     *
     * @param string $e1 prefix part of pathname.
     * @param string $e2 suffix part of pathname.
     * @return string Returns the resulting concatenated pathname.
     */
    public function pathname_concat($e1, $e2) {
        $retval = $e1;
        if (substr($e1, -1) != '/') {
            $retval .= '/';
        }
        $retval .= $e2;
        return $retval;
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
     * Can this platform provide a list of versions of any Maxima installations present?
     *
     * @return boolean Returns true for Linux/Unix/MacOSX.
     */
    public function can_list_maxima_versions() {
        return true;
    }

    /**
     * The filename for the maxima executable or script on this platform. Can be absolute, relative
     * or pathless.
     *
     * @return string Maxima executable filename.
     */
    public function default_maxima_filename() {
        return 'maxima';
    }

    /**
     * The maxima command (including parameters) to use for this platform in one is not specified in
     * the admin settings. For platforms using a launch script, this is the command written to the
     * script, not the command for running the launch script.
     *
     * @return string Returns the default Maxima command.
     */
    public function get_default_maxima_command() {
        $settings = stack_utils::get_config();
        if ($this->is_optimised()) {
            // We are trying to use a Lisp snapshot of Maxima with all the
            // STACK libraries loaded.
            $retval = $this->get_preferred_optimised_pathname();
            if (isset($settings->lisp) && 'gcl' === $settings->lisp) {
                $retval .= " -eval '(cl-user::run)'";
            }
        } else {
            $retval = $this->get_default_maxima_preopt_command();
        }
        if (isset($settings->exectimeout) && $settings->exectimeout && $settings->exectimeout > 0) {
            $lines = array(); $execrv = 0;
            exec("which timeout", $lines, $execrv);
            if (0 !== $execrv) {
                $retval = "timeout --kill-after=" . $settings->exectimeout . "s " . $settings->exectimeout . "s " . $retval;
            }
        }
        return $retval;
    }

    /**
     * The maxima command (including non-lisp specific parameters) to use for this platform when
     * generating an auto-optimised image, if one is not specified in the admin settings.
     *
     * @return string Returns the default non-optimised Maxima command.
     */
    public function get_default_maxima_preopt_command() {
        $settings = stack_utils::get_config();
        if ($this->is_macosx()) {
            // This is the path on Macs, if Maxima has been installed following
            // the instructions on Sourceforge.
            $retval = '/Applications/Maxima.app/Contents/Resources/maxima.sh';
        } else {
            // Default guess on Linux, making explicit use of the chosen version number.
            $retval = 'maxima';
            if (isset($settings->maximaversion) && $settings->maximaversion && 'default' != $settings->maximaversion) {
                $retval .= ' --use-version=' . $settings->maximaversion;
            }
        }
        if (isset($settings->lisp) && $settings->lisp && 'default' !== $settings->lisp) {
            $retval .= ' --lisp ' . $settings->lisp;
        }
        return $retval;
    }

    /**
     * Performs a rudimentary installation check on Maxima.
     *
     * @return array Returns an array of two elements, each an array:
     *  'errors' => array of error strings, 'warnings' => array of warning strings.
     *
     */
    public function check_maxima_install() {
        if (!$this->compare_settings()) {
            $this->gather_settings();
            $settings = stack_utils::get_config();
            $errors = array(); $warnings = array();
            if (is_readable('/Applications/Maxima.app/Contents/Resources/maxima.sh')) {
                // This is the path on Macs, if Maxima has been installed following
                // the instructions on Sourceforge. If instead, the install is a Unix-like one,
                // the else branch below should handle it.
                if (! is_readable('/Applications/Gnuplot.app/Contents/Resources/bin/gnuplot')) {
                    $warnings['errorgnuplotnotfound']
                        = stack_string('errorgnuplotnotfound', '/Applications/Gnuplot.app/Contents/Resources/bin/gnuplot');
                }
            } else {
                // Note: MacOS X unix-like installs should come this way.
                $maxprog = $this->get_maxima_program();
                if (false !== strpos($maxprog, '/')) {
                    if (!is_executable($maxprog)) {
                        $errors['errormaximanotfound'] = stack_string('errormaximanotfound', getenv('PATH'));
                    }
                } else {
                    $lines = array(); $execrv = 0;
                    exec("which $maxprog", $lines, $execrv);
                    if (0 !== $execrv) {
                        $errors['errormaximanotonpath'] = stack_string('errormaximanotonpath', getenv('PATH'));
                    }
                }
                $plotprog = $this->get_arguments_from_command($this->get_plot_command())[0];
                if (false !== strpos($plotprog, '/')) {
                    if (!is_executable($plotprog)) {
                        $warnings['errorgnuplotnotfound'] = stack_string('errorgnuplotnotfound', getenv('PATH'));
                    }
                } else {
                    $lines = array(); $execrv = 0;
                    exec("which $plotprog", $lines, $execrv);
                    if (0 !== $execrv) {
                        $warnings['errorgnuplotnotonpath'] = stack_string('errorgnuplotnotonpath', getenv('PATH'));
                    }
                }
            }
            if (isset($settings->maximaversion) && $settings->maximaversion && 'default' !== $settings->maximaversion) {
                $versions = $this->get_list_of_maxima_versions();
                if ($versions && !array_key_exists($versions, $settings->maximaversion)) {
                    $errors[] = stack_string('healthcheckmaximaversionnotpresent',
                            array("chosen" => $settings->maximaversion,
                                "available" => implode(', ', array_keys($versions))));
                } else if (isset($settings->lisp) && $versions && $lisp  = $versions[$settings->maximaversion]->lisps &&
                        false === array_search($settings->lisp, $lisps) ) {
                    $errors[] = stack_string('healthcheckmaximaversionlispnotpresent',
                            array("chosen" => $settings->lisp,
                                "available" => implode(', ', array_keys($lisps))));
                }
            }
            if ((isset($settings->maximacommand) && $settings->maximacommand) ||
                    (isset($settings->maximapreoptcommand) && $settings->maximapreoptcommand)) {
                $warnings[] = stack_string('healthcheckwarningcommandoverride');
            }
            if (isset($settings->castimeout) && $settings->castimeout && $settings->castimeout > 0 &&
                    isset($settings->exectimeout) && $settings->exectimeout && $settings->exectimeout > 0 &&
                    ($settings->exectimeout <= $settings->castimeout || $settings->castimeout == 0)) {
                $warnings[] = stack_string('healthcheckwarningtimeout');
            }
            $this->errors = $errors;
            $this->warnings = $warnings;
            if ($this->is_optimised()) {
                $this->check_image();
            }
        }
        return array('errors' => $this->errors, 'warnings' => $this->warnings);
    }

    /*
     * Gnu Plot Member Functions
     * =========================
     *
     * These member functions manipulate configuration, location and files related to how the
     * gnuplot executable gets executed.
     *
     */

    /**
     * The default gnuplot command, including any parameters, for this platform.
     *
     * @return string Returns the default command.
     */
    public function get_default_plot_command() {
        if ($this->is_macosx()) {
            return '/Applications/Gnuplot.app/Contents/Resources/bin/gnuplot';
        } else {
            return 'gnuplot';
        }
    }

    /**
     * The remove command for this platform, as used to delete unwanted plot files.
     *
     * @return string Returns 'rm' for Linux/Unix/MacOSX.
     */
    public function get_remove_command() {
        return "rm";
    }

    /*
     * Optimised Image Member Functions
     * ================================
     * These functions manage data needed to locate, execute regenerate an optimiised Maxima image
     * on this platform. Using images of this kind can greatly improve Maxima performance.
     * For platforms where optimisation is possible, platform objects will exist and be listed as a
     * pair, one for the non-optimised and one for optimised variant. They will usually share the
     * same implementation class.
     * Images may be manually optimised, following the documentation and using maxima commands or
     * auto-optimised using the healthcheck page. Some platforms will support both, some manual
     * only optimisation, some neither.
     */

    /**
     * the full default pathname of the manually optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting.
     *
     * @return string Returns the full pathname of the manually optimised binary file.
     */
    public function get_default_optimised_pathname() {
        return $this->defaultoptimisedpathname;
    }

    /**
     * the full default pathname of the auto-optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting. In the case of the
     * default command line, this binary takes precedence over the manually optimised one if
     * both or none are present.
     *
     * @return string Returns the full pathname of the auto-optimised binary file -
     * Linux/Unix/MacOSX allow for an optimised image, so should return a meaningful pathname.
     */
    public function get_auto_optimised_pathname() {
        return $this->autooptimisedpathname;
    }

    /*
     * Launch Script Member Functions
     * ==============================
     * The launch script is a batch file or shell script used to launch the maxima executable. Some
     * platforms require this in order to establish a working envirnment for either the main Maxima
     * program or an optimised Maxima image. For these platforms, the command-line specified in
     * the admin settings will be written to the launch script; the launch script will be what
     * actually gets executed. This script is generated specifically by STACK, and is not to be
     * confused with the main startup script that is typically part of the Maxima installation.
     *
     * Depending on the configuration, this launch script may call that main script.
     */

    /*
     * Linux/Unix/MacosX platform does not need a launch script, so no specialized overrides or
     * implementations.
     */
}

// Register this platform with the list of platforms.
stack_platform_base::register('unix', 'stack_platform_unix');
stack_platform_base::register('unix-optimised', 'stack_platform_unix');
