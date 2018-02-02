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
 * Class representing a Microsoft Windows platform, either optimised or non-optimised.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Stephen Parry (stephen@edumake.org)
 *
 */
class stack_platform_windows extends stack_platform_base {

    /*
     * Member Variables
     * ================
     */

    static protected $lastmaximalocation = null;
    static protected $locations = null;
    static protected $lastmaximaversion = null;

    protected $maximalocation = null;
    protected $maximaversion = null;
    protected $plotexecutable = null;
    protected $lastplotstart = null;
    protected $plotlocations = null;

    protected $default_optimised_pathname = null;
    protected $auto_optimised_pathname = null;

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
    public function __construct($name) {
        parent::__construct($name);
        $this->default_optimised_pathname = $this->pathname_concat($this->get_stack_data_dir(), 'maxima-optimised.exe');
        $this->auto_optimised_pathname = $this->pathname_concat($this->get_stack_data_dir(), 'maxima_opt_auto.exe');
    }

    /*
     * Connection Class Member Functions
     * =================================
     * These functions link the platform class to the connection class the platform uses.
     */

    /**
     * Get the connection class for this platform.
     *
     * @return string For Windows Returns 'stack_cas_connection_windows'.
     *
     */
    public function get_connection_class() {
        return 'stack_cas_connection_windows';
    }

    /**
     * Get the source file for the connection class for this platform.
     *
     * @return string For Windows Returns 'connector.windows.class.php'.
     *
     */
    public function get_connection_source_file() {
        return 'connector.windows.class.php';
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
     * @return boolean returns false for the Windows platform.
     */
    public function are_all_pathnames_portable() {
        return false;
    }

    /**
     * Escapes the spaces in a single filename. Does not work for full commands.
     * @param string $f filename to escape.
     * @return string Returns the escaped string.
     */
    static public function escape_spaces($f) {
        return false === strpos($f, ' ') || false !== strpos($f, '"') ? $f : '"'. $f . '"';
    }

    /**
     *
     * Compares two pathnames in a platform dependent way. No attempt is made to
     * resolve relative or virtual paths, existent or not. Under windows, pathnames are case
     * insensitive.
     *
     * @param string $p1 full pathname 1.
     * @param string $p2 full path name 2.0
     * @return boolean Returns true if the two paths are equal.
     */
    public function pathname_equals($p1, $p2) {
        return strcasecmp($this->pathname_to_native($p1), $this->pathname_to_native($p2)) == 0;
    }

    /**
     * Extracts arguments from a command line. On most platforms the default implementation will
     * suffice, which splits the command line arguments on spaces. Platforms that allow spaces in
     * their command-line pathnames (e.g. Windows) will need more involved processing.
     *
     * @param string $cmd The command line command, including the main program.
     * @return array|null The command line arguments, including the main program.
     */
    public function get_arguments_from_command($cmd) {
        $retval = array();
        if ($cmd && strpos($cmd, '"') !== false) {
            $in = false;
            $pieces = explode('"', $cmd);
            foreach ($pieces as $p) {
                if ($in) {
                    $retval[] = $p;
                } else {
                    array_merge($retval, explode(' ', trim($p)));
                }
                $in = !$in;
            }
        } else {
            $retval = explode(' ', $cmd);
        }
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
     * Validate and get the location of the Maxima install.
     * @param array $locations If supplied, is filled with the locations that were scanned for an installation.
     * @return null|string Returns:
     *  - null if Maxima install could not be found.
     *  - The path if the Maxima install is found.
     */
    public function get_maxima_install(&$locations = array()) {
        $settings = stack_utils::get_config();
        if (!isset($settings->maximaversion)) {
            return null;
        }
        if (!$this->maximalocation ||  $this->maximaversion !== $settings->maximaversion) {
            if (!self::$lastmaximalocation || self::$lastmaximaversion !== $settings->maximaversion) {
                if (!self::$locations) {
                    self::$locations = array();
                    self::$locations[] = 'C:\\maxima-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\maxima-' . $settings->maximaversion . 'a\\';
                    self::$locations[] = 'C:\\Maxima-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\bin\\Maxima-gcl-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\bin\\Maxima-sbcl-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\Program Files\\Maxima-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\Program Files (x86)\\Maxima-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\Program Files\\Maxima-sbcl-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\Program Files (x86)\\Maxima-sbcl-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\Program Files\\Maxima-gcl-' . $settings->maximaversion . '\\';
                    self::$locations[] = 'C:\\Program Files (x86)\\Maxima-gcl-' . $settings->maximaversion . '\\';
                    if ('5.25.1' === $settings->maximaversion) {
                        self::$locations[] = 'C:\\Program Files\\Maxima-5.25.1-gcl\\';
                        self::$locations[] = 'C:\\Program Files (x86)\\Maxima-5.25.1-gcl\\';
                    }
                    if ('5.28.0' === $settings->maximaversion) {
                        self::$locations[] = 'C:\\Program Files\\Maxima-5.28.0-2\\';
                        self::$locations[] = 'C:\\Program Files (x86)\\Maxima-5.28.0-2\\';
                    }
                    if ('5.31.1' === $settings->maximaversion) {
                        self::$locations[] = 'C:\\Program Files\\Maxima-5.31.1-1\\';
                        self::$locations[] = 'C:\\Program Files (x86)\\Maxima-5.31.1-1\\';
                    }
                    self::$locations[] = 'C:\\Program Files\\Maxima\\';
                    self::$locations[] = 'C:\\Program Files (x86)\\Maxima\\';
                }
                self::$lastmaximalocation = null;
                self::$lastmaximaversion = $settings->maximaversion;
                foreach (self::$locations as $location) {
                    if (file_exists($location.'bin\\maxima.bat')) {
                        self::$lastmaximalocation = $location;
                        break;
                    }
                }
            }
            $this->maximalocation = self::$lastmaximalocation;
            $this->maximaversion = self::$lastmaximaversion;
        }
        $locations = self::$locations;
        return $this->maximalocation;
    }

    /**
     * Finds the program file used to launch Maxima when we are not using an optimised executable.
     *
     * @return string|null Returns the full pathname of the program file.
     */
    public function get_maxima_non_opt_filename() {
        $location = $this->get_maxima_install();
        return $location ? $this->pathname_concat($location , 'bin\\maxima.bat') : null;
    }

    /**
     * The maxima command (including non-lisp specific parameters) to use for this platform when
     * generating an auto-optimised image, if one is not specified in the admin settings. For platforms
     * using a launch script, this is the command written to the script, not the command for running
     * the launch script, i.e. it is like get_maxima_inner_command().
     *
     * @return string Returns the default non-optimised Maxima command.
     */
    public function get_default_maxima_preopt_command() {
        $settings = stack_utils::get_config();
        $retval = $this->escape_spaces($this->pathname_to_native($this->get_maxima_non_opt_filename()));
        if (isset($settings->lisp) && $settings->lisp && 'default' !== $settings->lisp) {
            $retval .= ' --lisp ' . $settings->lisp;
        }
        return $retval;
    }

    /**
     * The maxima command (including arguments) to use by default for this platform. For platforms
     * using a launch script, this the command written to the script, not the command for
     * running the launch script.
     *
     * @return string Returns the default Maxima command.
     */
    public function get_default_maxima_command() {
        if ($this->is_optimised()) {
            // We are trying to use a Lisp snapshot of Maxima with all the
            // STACK libraries loaded.
            $lispimage = $this->get_preferred_optimised_pathname();
            return $this->escape_spaces($this->pathname_to_native($lispimage));
        } else {
            return $this->get_default_maxima_preopt_command();
        }
    }

    /**
     * Performs a rudimentary installation check on Maxima.
     *
     * @return array Returns an array of two elements, each an array:
     *  'errors' => array of error strings, 'warnings' => array of warning strings.
     */
    public function check_maxima_install() {
        if (!$this->compare_settings()) {
            $this->gather_settings();
            $settings = stack_utils::get_config();
            $errors = array();
            $warnings = array();
            if (!isset($settings->maximaversion) || !$settings->maximaversion || 'default' === $settings->maximaversion) {
                $errors['errormaximaversiondefault'] = stack_string('errormaximaversiondefault');
            } else {
                $vers = explode('.', $settings->maximaversion);
                if ((int)$vers[0] < 5 || ( (int)$vers[0] === 5 && (int)$vers[1] < 39 )) {
                    $warnings['errormaximaversionuntested']
                    = stack_string('errormaximaversionuntested', $settings->maximaversion);
                }
                $maxok = false;
                $maxprog = null;
                if ($settings->maximacommand && 'default' !== $settings->maximacommand) {
                    // Attempt to split the executable from the rest of the command.
                    $maxprog = $this->get_arguments_from_command($settings->maximacommand)[0];
                    $maxok = file_exists($maxprog);
                    if (!$maxok) {
                        // If it's the optimised image that's missing, make this a warning
                        // otherwise generating the image becomes a real pain.
                        if ($this->is_optimised() && 0 === $this->get_current_optimised_filename()[1]) {
                            $warnings['errormaximanotfound'] = stack_string('errormaximanotfound', $maxprog);
                        } else {
                            $errors['errormaximanotfound'] = stack_string('errormaximanotfound', $maxprog);
                        }
                    }
                    if (false !== strpos($maxprog, ' ')) {
                        $warnings['errormaximainstallspaces'] = stack_string('errormaximainstallspaces', $maxprog);
                    }
                } else {
                    $looked = array();
                    $loc = $this->get_maxima_install($looked);
                    $maxok = ($loc !== null);
                    if (!$maxok) {
                        $errors[] = stack_string('errormaximainstallnotfound', implode(", ", $looked));
                    }
                    if (false !== strpos($loc, ' ')) {
                        $warnings['errormaximainstallspaces'] = stack_string('errormaximainstallspaces', $loc);
                    }
                }
                $plotcmd = $settings->plotcommand;
                $plotok = false;
                if ($maxok || ($plotcmd && 'default' !== $plotcmd)) {
                    $plotprog = null;
                    $looked = array();
                    if ($plotcmd && 'default' !== $plotcmd) {
                        // Attempt to split the executable from the rest of the command.
                        $args = $this->get_arguments_from_command($plotcmd);
                        $plotprog = $args ? $args[0] : null;
                    } else {
                        $plotprog = $this->find_plot_executable(null, $looked);
                    }
                    $plotok = (null !== $plotprog && is_executable($plotprog) );
                    if (!$plotok) {
                        $warnings['errorgnuplotnotfound'] = stack_string('errorgnuplotnotfound', implode(", ", $looked));
                    }
                }
            }
            if ($settings->maximacommand || $settings->maximapreoptcommand) {
                $warnings[] = stack_string('healthcheckwarningcommandoverride');
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
     *
     * @param string|null $start The directory to look within (null means use get_maxima_install)
     * @param array $locations The pathnames relative to $start to try.
     * @return string|null Returns the location of gnuplot or null if it could not be found.
     */

    public function find_plot_executable($start = null, &$locations = array()) {
        if (!$start) {
            $start = $this->get_maxima_install();
        }
        if ($this->lastplotstart !== $start) {

            // This does its best to find your version of Gnuplot.
            $plotexecutables = array();
            $plotexecutables[] = $start. 'gnuplot\\wgnuplot.exe';
            $plotexecutables[] = $start. 'bin\\wgnuplot.exe';
            $plotexecutables[] = $start. 'gnuplot\\bin\\wgnuplot.exe';

            $this->plotexecutable = null;
            foreach ($plotexecutables as $plotexecutable) {
                if (file_exists($plotexecutable)) {
                    $this->plotexecutable = $plotexecutable;
                    break;
                }
            }
            $this->lastplotstart = $start;
            $this->plotlocations = $start;
        }
        $locations = $this->plotlocations;
        return $this->plotexecutable;
    }

    /**
     * The default gnuplot command, including any parameters, for this platform.
     *
     * @return string Returns the default command.
     */
    public function get_default_plot_command() {
        return self::escape_spaces($this->find_plot_executable());
    }

    /**
     * The remove command for this platform, as used to delete unwanted plot files.
     *
     * @return string Returns the command.
     */
    public function get_remove_command() {
        return "DEL";
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
     * Determine if this platform can be be auto-optimised. Under Windows GCL cannot be auto-optimised
     *
     * @return bool|string Returns true if this platform can be optimised, or a reason if not.
     */
    public function can_be_auto_optimised() {
        $settings = stack_utils::get_config();
        return $this->can_be_optimised() && (empty($settings->lisp) ||'gcl' !== $settings->lisp) ? true : stack_string('healthautomaxopt_gclwinmanual');
    }

    /**
     * Get a reason why this platform cannot be optimised.
     *
     * @return string|null Returns explanation or null.
     */
    public function get_no_opt_reason() {
        return $this->can_be_auto_optimised() ? null : stack_string('healthautomaxopt_gclwinmanual');
    }

    /**
     * the full default pathname of the manually optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting.
     *
     * @return string Returns the full pathname of the manually optimised binary file.
     */
    public function get_default_optimised_pathname() {
        return $this->default_optimised_pathname;
    }

    /**
     * the full default pathname of the manually optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting. In the case of the
     * default command line, this binary takes precedence over the manually optimised one if
     * both are present
     *
     * @return string Returns the full pathname of the auto-optimised binary file.
     */
    public function get_auto_optimised_pathname() {
        return $this->auto_optimised_pathname;
    }

    /**
     * Some lisp implementations require support files even when optimised. This function will
     * copy them into place.
     */
    public function copy_optimised_support_files() {
        if (stack_utils::get_config()->lisp == 'clisp') {
            $location = $this->get_maxima_install();
            $dir = dir($location);
            $src = null;
            while (false !== ($e = $dir->read())) {
                if (preg_match('/^clisp.*/', $e) !== 0 &&
                        is_dir($src = $this->pathname_concat($this->pathname_concat($location, $e), 'base'))) {
                    $dir->close();
                    break;
                }
            }
            if ($src) {
                $dest = $this->get_stack_data_dir();
                stack_utils::copy_dir_r($src, $dest, "*.dll");
            }
        }
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

    /**
     * Determines whether this platform and current configuration require a custom generated
     * script to launch Maxima. The Windows platform does normally require a launch script.
     *
     * @return bool Returns true if this platform as currently configured requires a launch script.
     */
    public function requires_launch_script() {
        return true;
    }

    /**
     * Determines location of the custom generated script used to launch Maxima, if it is needed.
     * This is a script generated specifically by STACK, and is not to be confused with the main
     * startup script that is typically part of the Maxima installation.
     *
     * Depending on the configuration, this launch script may call the main script.
     *
     * @return string|null Returns the full filename of the launch script to use or null if not needed.
     */
    public function get_launch_script_pathame() {
        global $CFG;
        return $CFG->dataroot . '\\stack\\maxima.bat';
    }

    /**
     * Determines whether an executable launch script is present. Does not verify whether or not
     * it is up-to-date.
     *
     * @return boolean Return true if the script is present, readable and executable.
     */
    public function check_launch_script() {
        $lsfn = $this->get_launch_script_pathame();
        if (is_readable($lsfn)) {
            $settings = stack_utils::get_config();
            $cts = (empty($settings->criticalsettingsupdated) || !$settings->criticalsettingsupdated) ?
                    0 : (int)$settings->criticalsettingsupdated;
            $lsts = filemtime($lsfn);
            return $lsts > $cts;
        } else {
            return false;
        }
    }

    /**
     * Generates a launch script for those configurations that need it. This should only be called
     * after check_maxima_install has returned without errors.This is the custom generated
     * script used to launch Maxima. This script is generated specifically by STACK, and is not to
     * be confused with the main startup script that is typically part of the Maxima installation.
     *
     * @thows throws an exception if the file cannot be written.
     */
    public function generate_launch_script() {
        $this->check_maxima_install();
        if ($this->errors) {
            throw new stack_exception(stack_string('errorlaunchscriptunresolvederrors'));
        }
        $settings = stack_utils::get_config();
        $maximalocation = $this->get_maxima_install();

        $mcmd = $this->get_maxima_inner_command();
        $mprog = $this->get_maxima_program();
        $ext = pathinfo($mprog, PATHINFO_EXTENSION);
        if ($ext == 'bat' || $ext == 'cmd') {
            $mcmd = "call " . $mcmd;
        }

        $stackbatchfilename = $this->get_launch_script_pathame();
        $date = date("F j, Y, g:i a");
        // Common; we set up various variables to establish the maxima home directory as = the location of
        // this script, i.e. the stack data directory.
        $contents = <<<END
@echo off
rem /* ***********************************************************************/
rem /* This file is automatically generated at installation time.            */
rem /* The purpose is to launch Maxima with the correct environment          */
rem /* variables and cxommand line.                                          */
rem /* Hence, you should not edit this file.  Edit your configuration.       */
rem /* This file is regularly overwritten, so your changes will be lost.     */
rem /* ***********************************************************************/

rem File generated on {$date}

setlocal
set "M=%~dp0"
set "M=%M:\=/%"
set "MAXIMA_USERDIR=%M%"
set "MAXIMA_TEMPDIR=%M%tmp/"

END;

        // Optimised executables cannot use the main batch file because they are located in the stack data
        // directory, so we set up a minimal maxima environment parallel to that produced by the main
        // maxima batch file and execute the optimised image from there.
        if ($this->is_optimised()) {
            $contents .= <<<END
set version={$settings->maximaversion}
set prefix=$maximalocation
set maxima_prefix=$maximalocation
set package=maxima
set verbose=false
set path=%maxima_prefix%\gnuplot;%maxima_prefix%\gnuplot\bin;%maxima_prefix%\bin;%path%

END;
        }
        $contents .= <<<END
{$mcmd}
endlocal
END;

        // Convert line-endings and write the file, if we can.
        if (!file_put_contents($stackbatchfilename, implode("\r\n", explode("\n", $contents)))) {
            throw new stack_exception('Could not create the STACK Maxima batch file: ' . $stackbatchfilename);
        }
    }

    /**
     * Get the command for actually launching the launch script. On Windows the command must be
     * space escaped.
     *
     * @return string|null Returns the full command for running the launch script, including
     * params / switches, or null if no launch script is needed.
     */
    public function get_launch_command() {
        return $this->escape_spaces($this->get_launch_script_pathame());
    }
}

// Register this platform with the list of platforms.
stack_platform_base::register('win', 'stack_platform_windows');
stack_platform_base::register('win-optimised', 'stack_platform_windows');
