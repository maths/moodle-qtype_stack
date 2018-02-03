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
 * Class representing a remote server platform, such as tomcat.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Stephen Parry (stephen@edumake.org)
 *
 */
class stack_platform_server extends stack_platform_base {
    /*
     * Class Loading and Metadata Member Functions
     * ===========================================
     *
     * These functions manage how platforms are made available to STACK and instantiated.
     */

    /* No specialized overrides or implementations for Server platform. */

    /*
     * Connection Class Member Functions
     * =================================
     * These functions link the platform class to the connection class the platform uses
     */

    /**
     * Get the connection class for this platform.
     *
     * @return string Returns the class name. For Server platform this is
     * 'stack_cas_connection_server'.
     *
     */
    public function get_connection_class() {
        return 'stack_cas_connection_server';
    }

    /**
     * Get the source file for the connection class for this platform.
     *
     * @return string For Server platform, returns 'connector.server.class.php'.
     *
     */
    public function get_connection_source_file() {
        return 'connector.server.class.php';
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
     * On  Server platform, all pathnames are portable (PATHTYPE_EITHER)
     *
     * @return boolean returns true for this platform
     */
    public function are_all_pathnames_portable() {
        return true;
    }

    /**
     * Convert a pathname from PHP portable format form to the platform dependent format. Ideally,
     * implementations should be able to tolerate being called on a path that is already in native
     * format.
     * On  Server platform, all pathnames are portable (PATHTYPE_EITHER)
     * @param string $path The pathname in PHP portable format using directory separator of /
     * @return string Returns the pathname in native format, using the native DIRECTORY_SEPARATOR.
     */
    public function pathname_to_native($path) {
        return $path;
    }

    /**
     * Convert a pathname from platform dependent format to PHP portable format form. Ideally,
     * implementations should be able to tolerate being called on a path that is already in PHP
     * portable format.
     * On  Server platform, all pathnames are portable (PATHTYPE_EITHER)
     * @param string $path The pathname in native format, using the native DIRECTORY_SEPARATOR.
     * @return string The pathname in PHP portable format using directory separator of /.
     */
    public function pathname_to_portable($path) {
        return $path;
    }

    /**
     * Analyses a pathname and determines what format it is in:
     *  PATHTYPE_EITHER   : Could be either (no seps or on this platform seps are the same)
     *  PATHTYPE_PORTABLE : Is a path with only portable path separators
     *  PATHTYPE_NATIVE   : Native seps only
     *  PATHTYPE_MIXED    : Mixture (yuck)
     * On  Server platform, all pathnames are portable (PATHTYPE_EITHER)
     * @return int Returns PATHTYPE_ as above describing the type of pathname.
     */
    public function pathname_type_of($e1) {
        return self::PATHTYPE_EITHER;
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
     * The filename for the maxima executable or script on this platform. Can be absolute, relative
     * or pathless.
     * For the server platform, it not an executable, but a URL to the local server.
     *
     * @return string Maxima executable filename.
     */
    public function default_maxima_filename() {
        return 'http://localhost:8080/MaximaPool/MaximaPool';
    }

    /**
     * The maxima command (including arguments) to use by default for this platform.
     *
     * @return string Maxima command.
     */
    public function get_default_maxima_command() {
        return $this->default_maxima_filename();
    }

    /**
     * Performs a rudimentary installation check on Maxima.
     *
     * @return array Returns array(null, null) on the server platform, as the Maxima installation
     * is on the server.
     *
     */
    public function check_maxima_install() {
        return array(null, null);
    }

    /*
     * Gnu Plot Member Functions
     * =========================
     *
     * These member functions manipulate configuration, location and files related to how the
     * gnuplot executable gets executed.
     */

    /**
     * The default gnuplot command for this platform.
     *
     * @return string Returns 'gnuplot' on the server platform, as the server will most likely run
     * Linux or Unix.
     */
    public function get_default_plot_command() {
        return 'gnuplot';
    }

    /**
     * The remove command for this platform, as used to delete unwanted plot files.
     *
     * @return string Returns 'rm' on the server platform, as the server will most likely run Linux
     * or Unix.
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
     * Determine if this platform involves the Maxima image running on some kind of software server,
     * such as Apache Tomcat. This mainly affects the instructions given for image maintenance.
     *
     * @return boolean Returns true for a server based Maxima platform.
     */
    public function is_server_based() {
        return true;
    }

    /**
     * the full default pathname of the manually optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting.
     *
     * @return string|null Returns null on server platform, as optimised images are managed by the
     * server.
     */
    public function get_default_optimised_pathname() {
        return null;
    }

    /**
     * the full default pathname of the auto-optimised binary file on this platform. This is
     * used if none is explicitly configured via the maximacommand admin setting. In the case of the
     * default command line, this binary takes precedence over the manually optimised one if
     * both or none are present.
     *
     * @return string|null Returns null on server platform, as optimised images are managed by the
     * server.
     */
    public function get_auto_optimised_pathname() {
        return null;
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
     * Server platform does not need a launch script, so no specialized overrides or implementations.
     */

}

// Register this platform with the list of platforms.
stack_platform_base::register('server', 'stack_platform_server');

// TODO: introduce variants for non-Linux servers.