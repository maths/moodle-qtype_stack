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
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
/**
 * Base class for classes representing a given Maxima platform.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Stephen Parry (stephen@edumake.org)
 *
 */
abstract class stack_platform_base {

    /*
     * Member Variables
     * ================
     */

    /**
     *
     * @var array $classes List of available platform name => class
     * @var array $instances List of instantiated platforms, platform name => platform object
     */
    protected static $classes = array();
    protected static $instances = array();

    /**
     *
     * @var string $name        Full name of this platform.
     * @var string $basename   Name with optimisation suffix.
     * @var bool $optimised     true if this platform is an optimised variant.
     */
    protected $name;
    protected $basename;
    protected $optimised;
    protected $stackdatadir;

    /**
     *
     * @var array|null $errors      Error strings from last check_maxima_install() call.
     * each key is the string id, each value is the actual localised and filled in text.
     * @var array|null $warnings    Error strings from last check_maxima_install() call.
     * each key is the string id, each value is the actual localised and filled in text.
     */
    protected $errors = null;
    protected $warnings = null;

    /**
     *
     * @var array $checksettingnames Names of settings to monitor for changes when caching
     * check_maxima_install() calls
     */
    static protected $checksettingnames = array('lisp',
        'maximaversion',
        'maximacommand',
        'serveruserpass',
        'plotcommand' );

    /**
     *
     * @var array $checksettings Setting values gathered when caching check_maxima_install() calls
     */
    protected $checksettings = null;

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
        global $CFG;
        $this->name = $name;
        $n = strlen($name);
        $this->optimised = ($n > self::OPTSUFFLEN && substr($name, $n - self::OPTSUFFLEN) == self::OPTSUFF);
        if ($this->optimised) {
            $this->basename = substr($name, 0, $n - self::OPTSUFFLEN);
        } else {
            $this->basename = $name;
        }
        $this->stackdatadir = $this->pathname_concat($this->pathname_concat($CFG->dataroot, "stack"), "");
    }

    /**
     * Used statically by derived classes to register themselves as available platforms.
     * @param string $name name of the platform this class will handle - one class can handle many
     * platforms
     * @param string $class the name of the class
     */
    public static function register($name, $class) {
        self::$classes[$name] = $class;
    }

    /**
     * Get a list of all available platform names.
     *
     * @return array Returns all the known platform names.
     */
    static public function get_names_and_descs() {
        $f = function($k) {
            return self::get($k)->get_desc();
        };
        return array_combine(array_keys(self::$classes), array_map($f, array_keys(self::$classes)));
    }

    /**
     * Get a platform object by platform name.
     *
     * @param string $name The name of the platform being requested.
     * @return stack_platform_base Returns the object representing the platform called '$name'.
     */
    static public function get($name) {
        if (!array_key_exists($name, self::$instances) || self::$instances[$name] == null) {
            $class = '\\' . self::$classes[$name];
            self::$instances[$name] = new $class($name);
        }
        return self::$instances[$name];
    }

    /**
     * Get the currently selected platform, as stored in the configuration.
     *
     * @return stack_platform_base Returns the object representing the currently configured platform.
     */
    static public function get_current() {
        $name = stack_utils::get_config()->platform;
        return self::get($name);
    }

    const OPTSUFF = '-optimised';   // The suffix used for optimsied platforms.
    const OPTSUFFLEN = 10;          // The suffix length.

    /**
     * A platform has two parts to its name - the base name e.g. 'win' or 'linux' and an optional suffix
     * allowing for a non-optimised and optimised version. This method gets the whole name.
     *
     * @return string Returns the whole name, i.e. including any 'optimised' suffix.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * A platform has two parts to its name - the base name e.g. 'win' or 'linux' and an optional suffix
     * allowing for a non-optimised and optimised version. This method gets the base part of the name.
     *
     * @return string Returns the first part of the configured platform type, i.e. without any 'optimised' suffix.
     */
    public function get_basename() {
        return $this->basename;
    }

    /**
     * Get the localized descriptive name of the platform.
     *
     * @return string Returns the description. throws an exception if absent.
     *
     */
    public function get_desc() {
        return stack_string('settingplatformtype' . str_replace('-', '', $this->name) );
    }

    /*
     * Connection Class Member Functions
     * =================================
     * These functions link the platform class to the connection class the platform uses
     */

    /**
     * Get the connection class for this platform.
     *
     * @return string Returns the class name.
     *
     */
    abstract public function get_connection_class();

    /**
     * Get the source file for the connection class for this platform.
     *
     * @return string Returns the pathname relative to the folder containing this class file.
     *
     */
    abstract public function get_connection_source_file();

    /**
     * Get the a new Maxima connection object for this platform.
     *
     * @return stack_cas_connection Returns an object representing the Maxima connection.
     *
     */
    public function get_connection() {
        return stack_connection_helper::make();
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
     * @return boolean returns true if this platform has only portable pathnames.
     */
    abstract public function are_all_pathnames_portable();

    /**
     * Convert a pathname from PHP portable format form to the platform dependent format. Ideally,
     * implementations should be able to tolerate being called on a path that is already in native
     * format.
     * @param string $path The pathname in PHP portable format using directory separator of /
     * @return string Returns the pathname in native format, using the native DIRECTORY_SEPARATOR.
     */
    public function pathname_to_native($path) {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Convert a pathname from platform dependent format to PHP portable format form. Ideally,
     * implementations should be able to tolerate being called on a path that is already in PHP
     * portable format.
     * @param string $path The pathname in native format, using the native DIRECTORY_SEPARATOR.
     * @return string The pathname in PHP portable format using directory separator of /.
     */
    public function pathname_to_portable($path) {
        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }

    const PATHTYPE_EITHER   = 0;    // Could be either (no seps or on this platform seps are the same).
    const PATHTYPE_PORTABLE = 1;    // Is a path with only portable path separators.
    const PATHTYPE_NATIVE   = 2;    // Native seps only.
    const PATHTYPE_MIXED    = 3;    // Mixture (yuck).

    /**
     * Analyses a pathname and determines what format it is in:
     *  PATHTYPE_EITHER   : Could be either (no seps or on this platform seps are the same)
     *  PATHTYPE_PORTABLE : Is a path with only portable path separators
     *  PATHTYPE_NATIVE   : Native seps only
     *  PATHTYPE_MIXED    : Mixture (yuck)
     * @return int Returns PATHTYPE_ as above describing the type of pathname.
     */
    public function pathname_type_of($pathname) {
        if (DIRECTORY_SEPARATOR == '/') {
            return self::PATHTYPE_EITHER;
        } else {
            return (false !== strpos($pathname, DIRECTORY_SEPARATOR) ? self::PATHTYPE_NATIVE : 0) | (false !== strpos($pathname, '/') ? self::PATHTYPE_PORTABLE : 0);
        }
    }

    /**
     *
     * Compares two pathnames in a platform dependent way. No attempt is made to
     * resolve relative or virtual paths, existent or not.
     *
     * @param string $p1 full pathname 1
     * @param string $p2 full path name 2
     * @return boolean Returns true if the two paths are equal.
     */
    public function pathname_equals($p1, $p2) {
        return $p1 == $p2;
    }

    /**
     *
     * Takes part of a pathname and appends a second part to it. The pathnames can either be native
     * or portable. The function will preserve that where possible.
     *
     * @param string $e1 prefix part of pathname
     * @param string $e2 suffix part of pathname
     * @return string Returns the resulting concatenated pathname
     */
    public function pathname_concat($e1, $e2) {
        $sep = ($this->pathname_type_of($e1) == self::PATHTYPE_PORTABLE) ? '/' : DIRECTORY_SEPARATOR;
        $retval = $e1;
        if (substr($e1, -1) != DIRECTORY_SEPARATOR && substr($e1, -1) != '/') {
            $retval .= $sep;
        }
        $retval .= $e2;
        return $retval;
    }

    /**
     * Extracts arguments from a command line. On most platforms the default implementation will
     * suffice, which splits the command line arguments on spaces. Platforms that allow spaces in
     * their command-line pathnames (e.g. Windows) will need more involved processing.
     *
     * @param string|null $cmd The command line command, including the main program.
     * @return array The command line arguments, including the main program.
     */
    public function get_arguments_from_command($cmd) {
        return !$cmd ? array() : explode(' ', $cmd);
    }

    /**
     * Get the currently configured stack data directory.
     *
     * @return string Full pathname of the stack data directory.
     */
    public function get_stack_data_dir() {
        return $this->stackdatadir;
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
     * @return boolean true if the platform is able to provide a list.
     */
    public function can_list_maxima_versions() {
        return false;
    }

    /**
     * Get a list of versions of any Maxima installations present.
     *
     * @return array|null Returns associative array keyed by version. Each entry contains:
     *  "version" => version string
     *  "lisps" => array of lisp names.
     *  Linux/Unix/MacOSX support this so should return a valid results.
     */
    public function get_list_of_maxima_versions() {
        $connection = $this->get_connection();
        $ma = $connection->get_raw()->get_maxima_available();
        if ($ma) {
            $ma2 = explode('\n', str_replace('\r', '', $ma));
            $rv = array();
            foreach ($ma2 as $a) {
                $matches = array();
                if (preg_match("$^\s*version\s*:?\s*([0-9]+(?:\.[0-9A-Za-z]+)*)(?:\s*,\s*lisp\s*:?\s*([a-zA-Z]+))?$",
                        $a, $matches)) {
                    if (count($matches) > 1) {
                        $v = $matches[1];
                        if (!array_key_exists($v, $matches)) {
                            $rv[$v] = array("version" => $v, "lisps" => array());
                        }
                        if (count($matches) > 2) {
                            $rv[$v]['lisps'][] = $matches[2];
                        }
                    }
                }
            }
        }
        return $ma ? $rv : null;
    }

    /**
     * Validate and get the location of the Maxima install, if it makes sense for this platform.
     *
     * @param array $locations If supplied, is filled with the locations scanned for an installation.
     * @return bool|null|string Returns:
     *  - true if this platform has no single Maxima install location to find. E.g. Linux / Unix / MacOS
     *  - null if Maxima install could not be found.
     *  - The path if the Maxima install is found.
     */
    public function get_maxima_install(&$locations = array()) {
        return true;
    }

    /*
     * Note on commands:
     * The commands used for launching Maxima are determined as follows:
     * 1. If the user has specified a command, this will be the 'inner' command
     * 2. otherwise, the 'default' is determined and used as the 'inner' command
     * 3. If the platform requires a launch script and it has not been bypassed, the 'inner' command
     *    is written to launch script and the launch script becomes the executed command.
     * 4. Otherwise the 'inner' command is executed directly.
     *
     */

    /**
     * The maxima command (including parameters) to use for this platform if one is not specified in
     * the admin settings. For platforms using a launch script, this is the command written to the
     * script, not the command for running the launch script, i.e. it is like
     * get_maxima_inner_command().
     *
     * @return string Returns the default Maxima command.
     */
    public abstract function get_default_maxima_command();

    /**
     * The maxima command (including non-lisp specific parameters) to use for this platform when
     * generating an auto-optimised image, if one is not specified in the admin settings. For platforms
     * using a launch script, this is the command written to the script, not the command for running
     * the launch script, i.e. it is like get_maxima_inner_command().
     *
     * @return string Returns the default non-optimised Maxima command.
     */
    public function get_default_maxima_preopt_command() {
        return $this->get_default_maxima_command();
    }

    /**
     * Gathers and stores the current settings, for validating the error and warning caches used
     * by check_maxima_install() calls.
     */
    protected function gather_settings() {
        $settings = stack_utils::get_config();
        foreach (self::$checksettingnames as $n) {
            if (empty($settings->{$n})) {
                $this->checksettings[$n] = null;
            } else {
                $this->checksettings[$n] = $settings->{$n};
            }
        }
    }

    /**
     * Compares the stored settings against the current settings, for validating the error and
     * warning caches used by check_maxima_install() calls.
     * @return bool false if settings have changed.
     */
    protected function compare_settings() {
        $settings = stack_utils::get_config();
        if (!$this->checksettings) {
            return false;
        }
        foreach (self::$checksettingnames as $n) {
            $val = null;
            if (isset($settings->{$n})) {
                $val = $settings->{$n};
            }
            if ($this->checksettings[$n] !== $val) {
                return false;
            }
        }
        return true;
    }

    /**
     * Gets the command to be used to execute Maxima. If a launch script is required and bypass is
     * not selected, the command to execute the launch script is returned. Otherwise, the currently
     * configured maxima command will be returned. If the currently configured command is null or
     * 'default' the default command is returned. The command returned will include any necessary
     * command line parameters and will be space-escaped on platforms where this is needed.
     * If a command cannot be determined or is not relevant for this platform, null is returned.
     *
     * @return string|null Returns the command to use, or null.
     */
    public function get_maxima_command() {
        $config = stack_utils::get_config();
        if ($this->requires_launch_script() && (!isset($config->bypasslaunchscript) || !$config->bypasslaunchscript)) {
            $command = $this->get_launch_command();
        } else {
            $command = $this->get_maxima_inner_command();
        }
        return $command;
    }

    /**
     * Gets the currently configured maxima command or null if the command cannot be determined or
     * is not relevant. Will include any command line parameters. If the configured command is
     * empty, default or null, the default is returned.
     *
     * STACK uses the return value from this function by either directly executing Maxima or by
     * writing it to a launch script and executing that.
     *
     * @return string|null Returns the command to use, or null.
     */
    public function get_maxima_inner_command() {
        $config = stack_utils::get_config();
        if ($config->maximacommand && $config->maximacommand != '' && $config->maximacommand != 'default') {
            $command = $config->maximacommand;
        } else {
            $command = $this->get_default_maxima_command();
        }
        return $command;
    }

    /**
     * Gets the filename of the currently configured maxima executable or null if it cannot be
     * determined or is not relevant. The default implementation finds this from the first element
     * of the _inner_ pre-opt command, i.e. the command used to generate an auto-optimised image.
     * If the platform returns null for the pre-opt command, then the main inner command is used.
     *
     * @return string|null Returns the program file or path name.
     */
    public function get_maxima_program() {
        $cmd = $this->get_maxima_preopt_command(); // Will get default if appropriate.
        $cmd = $cmd == null ? $this->get_maxima_inner_command() : $cmd;
        $args = $this->get_arguments_from_command($cmd);
        if ($args) {
            $rv = null;
            foreach ($args as $arg) {
                if ($arg && 'timeout' !== $arg && '-' !== $arg[0] && !is_numeric($arg[0])) {
                    $rv = $arg;
                    break;
                }
            }
            return $rv;
        } else {
            return null;
        }
    }

    /**
     * Gets the currently configured maxima command or null if the command cannot be determined or
     * is not relevant. Will include any command line parameters. If the configured command is
     * empty, default or null, the default is returned.
     *
     * STACK uses the return value from this function by either directly executing Maxima or by
     * writing it to a launch script and executing that.
     *
     * @return string|null Returns the command to use, or null.
     */
    public function get_maxima_preopt_command() {
        $config = stack_utils::get_config();
        if ($config->maximapreoptcommand && $config->maximapreoptcommand != ''
                && $config->maximapreoptcommand != 'default') {
            $command = $config->maximapreoptcommand;
        } else {
            $command = $this->get_default_maxima_preopt_command();
        }
        return $command;
    }

    /**
     * Performs a rudimentary installation check on Maxima.
     *
     * @return array Returns an array of two elements, each an array:
     *  'errors' => array of error strings, 'warnings' => array of warning strings.
     *
     */
    public abstract function check_maxima_install();

    /*
     * Gnu Plot Member Functions
     * =========================
     *
     * These member functions manipulate configuration, location and files related to how the
     * gnuplot executable gets executed.
     */

    /**
     * The default gnuplot command, including any parameters, for this platform.
     *
     * @return string Returns the default command.
     */
    public abstract function get_default_plot_command();

    /**
     * Gets the currently configured gnuplot command or null if the command cannot be determined or
     * is not relevant. Will include any command line parameters. If the configured command is
     * empty, the default is returned.
     *
     * @return string|null Returns the command to use, or null.
     */
    public function get_plot_command() {
        $config = stack_utils::get_config();
        return ($config->plotcommand && $config->plotcommand != '') ? $config->plotcommand : $this->get_default_plot_command();
    }

    /**
     * The remove command for this platform, as used to delete unwanted plot files.
     *
     * @return string Returns the command.
     */
    public abstract function get_remove_command();

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
     * Determine whether this platform instance is an optimised one.
     *
     * @return bool Returns true if this is an optimised platform.
     */
    public function is_optimised() {
        return $this->optimised;
    }

    /**
     * Determine if this platform can be optimised.
     *
     * @return bool Returns true if this platform can be optimised.
     */
    public function can_be_optimised() {
        return $this->is_optimised() || array_key_exists($this->basename . '-optimised', self::$classes );
    }

    /**
     * Determine if this platform can be be auto-optimised.
     *
     * @return bool Returns true if this platform can be auto-optimised.
     */
    public function can_be_auto_optimised() {
        return $this->can_be_optimised();
    }

    /**
     * Get a reason why this platform cannot be optimised.
     *
     * @return string|null Returns explanation or null.
     */
    public function get_no_opt_reason() {
        return $this->can_be_auto_optimised() ? null : stack_string('healthautomaxopt_notsupported');
    }

    /**
     * Determine if this platform involves the Maxima image running on some kind of software server,
     * such as Apache Tomcat. This mainly affects the instructions given for image maintenance.
     *
     * @return boolean Returns true if this is a server based Maxima platform.
     */
    public function is_server_based() {
        return false;
    }

    /**
     * Get the object that represents the optimised flavour of this platform
     *
     * @return stack_platform_base Returns the optimised version of this platform or $this if it
     * is already an optimised platform.
     */
    public function optimised() {
        if ($this->is_optimised()) {
            return $this;
        } else {
            return self::get($this->basename . self::OPTSUFF);
        }
    }

    /**
     * Get the object that represents the non-optimised flavour of this platform.
     *
     * @return stack_platform_base Returns the non-optimised version of this platform or $this if it
     * is already a non-optimised platform.
     */
    public function non_optimised() {
        if ($this->is_optimised()) {
            return self::get($this->basename);
        } else {
            return $this;
        }
    }

    /**
     * Attempts to extract the name of an optimised image from a command line.
     * @param string $cmd The command line command.
     * @return array|null Returns an array [0] = the pathname [1] = argv index or null if not found
     * in $cmd.
     */
    public function get_image_from_command($cmd) {
        $args = $this->get_arguments_from_command($cmd);
        // Look for an arg that resides within the data directory.
        // This is likely to be the image file.
        $dd = $this->pathname_to_native($this->get_stack_data_dir());
        $n = strlen($dd);
        $ai = 0;
        $fallback = array(null, null);
        $dofn = $this->pathname_to_native($this->get_default_optimised_pathname());
        $daofn = $this->pathname_to_native($this->get_auto_optimised_pathname());
        $fi = new finfo(FILEINFO_MIME_ENCODING);
        $imagenext = false;
        foreach ($args as $arg) {
            $a = $this->pathname_to_native($args[0]);
            if ($this->pathname_equals($a, $dofn) || $this->pathname_equals($a, $daofn)) {
                return array($a, $ai);
            } else if (is_dir($a)) {
                $ai ++;
                continue;
            }
            // Is this some kind of source file?
            $ext = pathinfo($a, PATHINFO_EXTENSION);
            if (strcasecmp($ext, 'mac') == 0 ||
                    strcasecmp($ext, 'lisp') == 0 ||
                    strcasecmp($ext, 'lsp') == 0 ||
                    strcasecmp($ext, 'cl') == 0 ||
                    strcasecmp($ext, 'inc') == 0 ||
                    strcasecmp($ext, 'clisp') == 0 ) {
                $ai ++;
                continue;
            }

            // Is this argument a file in the stack data directory?
            if (strlen($a) > $n && $this->pathname_equals(substr($a, 0, $n), $dd)) {
                if (!$imagenext && !file_exists($a)) {
                    // File is not there; could be the image, but we will try other alternatives first.
                    $fallback = array($a, $ai);
                } else if ($imagenext || !file_exists($a) || $fi->file($a) == 'binary') {
                    return array($a, $ai);
                }
            }
            if (strlen($arg) > 0) {
                if ($arg[0] == '-') {
                    $imagenext = ($arg == '-M' || $arg == '--core');
                    $ai ++;
                    continue;
                }
            }
            $imagenext = false;
            $ai ++;
        }
        return $fallback;
    }

    /**
     * Get the filename of the currently chosen Maxima optimised image, based on the current command
     * line, whether explicitly set or default.
     *
     * @return array|null Returns an array [0] => pathname [1] => argv index or [null, null] if not
     * found in the current command line.
     */
    public function get_current_optimised_filename() {
        if ($this->is_optimised()) {
            return $this->get_image_from_command($this->get_maxima_inner_command());
        } else {
            return array(null, null);
        }
    }

    /**
     * The full default pathname of the manually optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting.
     *
     * @return string|null Returns the full pathname of the manually optimised binary file, or null
     * if not applicable to this platform.
     */
    public abstract function get_default_optimised_pathname();

    /**
     * the full default pathname of the auto-optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting. In the case of the
     * default command line, this binary takes precedence over the manually optimised one if
     * both or none are present.
     *
     * @return string|null Returns the full pathname of the auto-optimised binary file, or null
     * if not applicable to this platform.
     */
    public abstract function get_auto_optimised_pathname();

    /**
     * the full default pathname of the optimised binary file on this platform. The auto-optimised
     * binary takes precedence over the manually optimised one if both are non-null and present.
     *
     * @return string|null Returns the full pathname of the optimised binary file.
     */
    public function get_preferred_optimised_pathname() {
        $lispimageao = $this->get_auto_optimised_pathname();
        $lispimageo = $this->get_default_optimised_pathname();
        if (null === $lispimageao || !is_readable($lispimageao) && is_readable($lispimageo)) {
            $lispimage = $lispimageo;
        } else {
            $lispimage = $lispimageao;
        }
        return $lispimage;
    }

    /**
     * Some lisp implementations require support files even when optimised. . This function will
     * copy them into place.
     */
    public function copy_optimised_support_files() {
    }

    /**
     * Checks whether the optimised image (as configured on the command line) is present, executable
     * and up-to-date with the configuration settings, updating the errors and warnings members
     * appropriately. Should be called as part of the check_maxima implementation.
     *
     * @return boolean Returns true if either this platform does not support optimisation or either
     * of the optimised or auto-optimised images appears sound.
     */
    public function check_image() {
        $config = stack_utils::get_config();
        $cbao = $this->can_be_auto_optimised();
        $cbo = $this->can_be_optimised();
        if (!$cbao && !$cbo) {
            // You cannot optimise this platform, so just act as if everything is OK.
            return true;
        } else {
            list($ofn, $ind) = $this->get_current_optimised_filename();
            // If the image is also the executable.
            $needstobeexe = (0 === $ind);
            if (!$ofn) {
                // This should only happen if the user is managing the image outside of STACK.
                return true;
            }
            $cts = $config->criticalsettingsupdated ? $config->criticalsettingsupdated : 0;
            $ots = ($cbo && is_file($ofn)) ? filemtime($ofn) : 0;
            if (!($ofn && is_file($ofn))) {
                $this->warnings['erroroptimagemissing'] = stack_string('erroroptimagemissing');
                return false;
            } else if ($needstobeexe && !is_executable($ofn)) {
                $this->warnings['erroroptimagenotexecutable'] = stack_string('erroroptimagenotexecutable', $ofn);
                return false;
            } else if ($ots <= $cts) {
                $this->warnings['erroroptimageoutofdate'] = stack_string('erroroptimageoutofdate', $ofn);
                return false;
            } else {
                return true;
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
     * conused with the main startup script that is typically part of the Maxima installation.
     *
     * Depending on the configuration, this launch script may call that main script.
     */

    /**
     * Determines whether this platform and current configuration require a custom generated
     * script to launch Maxima.
     *
     * @return bool Returns true if this platform as currently configured requires a launch script.
     */
    public function requires_launch_script() {
        return false;
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
        return null;
    }

    /**
     * Determines whether an executable launch script is present. Does not verify whether or not
     * it is up-to-date.
     *
     * @return boolean Return true if the script is present, readable and executable.
     */
    public function check_launch_script() {
        return true;
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
    }

    /**
     * Get the command for actually launching the launch script.
     *
     * @return string|null Returns the full command for running the launch script, including
     * params / switches, or null if no launch script is needed.
     */
    public function get_launch_command() {
        return $this->get_launch_script_pathame();
    }
}
