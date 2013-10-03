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

/**
 * The file provides helper code for creating the files needed to connect to the CAS.
 */

require_once(dirname(__FILE__).'/../../../../../config.php');

require_once(dirname(__FILE__) . '/../../locallib.php');
require_once(dirname(__FILE__) . '/../utils.class.php');


class stack_cas_configuration {
    protected static $instance = null;

    protected $settings;

    /** @var string the date when these settings were worked out. */
    protected $date;

    protected $maximacodepath;

    protected $logpath;

    protected $vnum;

    protected $blocksettings;

    /**
     * Constructor, initialises all the settings.
     */
    public function __construct() {
        global $CFG;

        $this->settings = get_config('qtype_stack');
        $this->date = date("F j, Y, g:i a");

        $this->maximacodepath = stack_utils::convert_slash_paths(
                $CFG->dirroot . '/question/type/stack/stack/maxima');

        $this->logpath = stack_utils::convert_slash_paths($CFG->dataroot . '/stack/logs');

        $this->vnum = (float) substr($this->settings->maximaversion, 2);

        $this->blocksettings = array();
        $this->blocksettings['TMP_IMAGE_DIR'] = stack_utils::convert_slash_paths($CFG->dataroot . '/stack/tmp/');
        $this->blocksettings['IMAGE_DIR']     = stack_utils::convert_slash_paths($CFG->dataroot . '/stack/plots/');

        // These are used by the GNUplot "set terminal" command. Currently no user interface...
        $this->blocksettings['PLOT_TERMINAL'] = 'png';
        $this->blocksettings['PLOT_TERM_OPT'] = 'large transparent size 450,300';

        if ($this->settings->platform == 'win') {
            $this->blocksettings['DEL_CMD']     = 'del';
            $this->blocksettings['GNUPLOT_CMD'] = $this->get_plotcommand_win();
        } else {
            $this->blocksettings['DEL_CMD']     = 'rm';
            if (!empty($this->settings->plotcommand)) {
                $this->blocksettings['GNUPLOT_CMD'] = $this->settings->plotcommand;
            } else if (is_readable('/Applications/Gnuplot.app/Contents/Resources/bin/gnuplot')) {
                $this->blocksettings['GNUPLOT_CMD'] = '/Applications/Gnuplot.app/Contents/Resources/bin/gnuplot';
            } else {
                $this->blocksettings['GNUPLOT_CMD'] = 'gnuplot';
            }
        }

        // Loop over this array to format them correctly...
        if ($this->settings->platform == 'win') {
            foreach ($this->blocksettings as $var => $val) {
                $this->blocksettings[$var] = addslashes(str_replace( '/', '\\', $val));
            }
        }

        $this->blocksettings['MAXIMA_VERSION'] = $this->settings->maximaversion;
        $this->blocksettings['URL_BASE']       = '!ploturl!';
    }

    /**
     * Try to guess the gnuplot command on Windows.
     * @return string the command.
     */
    public function get_plotcommand_win() {
        if ($this->settings->plotcommand && $this->settings->plotcommand != 'gnuplot') {
            return $this->settings->plotcommand;
        }

        // This does its best to find your version of Gnuplot...
        $maxima_location = $this->maxima_win_location();

        $plotcommands = array();
        $plotcommands[] = $maxima_location. '/gnuplot/wgnuplot.exe';
        $plotcommands[] = $maxima_location. '/bin/wgnuplot.exe';
        $plotcommands[] = $maxima_location. '/gnuplot/bin/wgnuplot.exe';

        foreach ($plotcommands as $plotcommand) {
            if (file_exists($plotcommand)) {
                        return '"' . $plotcommand . '"';
            }
        }

        throw new stack_exception('Could not locate GNUPlot.');
    }

    public function maxima_win_location() {
        global $CFG;

        if ($this->settings->platform != 'win') {
            return '';
        }

        $locations = array();
        $locations[] = 'C:/Program Files/Maxima-' . $this->settings->maximaversion . '/';
        $locations[] = 'C:/Program Files (x86)/Maxima-' . $this->settings->maximaversion . '/';
        if ('5.25.1' == $this->settings->maximaversion) {
            $locations[] = 'C:/Program Files/Maxima-5.25.1-gcl/';
            $locations[] = 'C:/Program Files (x86)/Maxima-5.25.1-gcl/';
        }
        if ('5.28.0' == $this->settings->maximaversion) {
            $locations[] = 'C:/Program Files/Maxima-5.28.0-2/';
            $locations[] = 'C:/Program Files (x86)/Maxima-5.28.0-2/';
        }
        if ('5.31.1' == $this->settings->maximaversion) {
            $locations[] = 'C:/Program Files/Maxima-5.31.1-1/';
            $locations[] = 'C:/Program Files (x86)/Maxima-5.31.1-1/';
        }
        $locations[] = 'C:/Program Files/Maxima/';
        $locations[] = 'C:/Program Files (x86)/Maxima/';

        foreach ($locations as $location) {
            if (file_exists($location.'bin/maxima.bat')) {
                return $location;
            }
        }

        throw new stack_exception('Could not locate the directory into which Maxima is installed. Tried the following:' .
                implode(', ', $locations));
    }

    public function copy_maxima_bat() {
        global $CFG;

        if ($this->settings->platform != 'win') {
            return true;
        }

        $batchfilename = $this->maxima_win_location() . 'bin/maxima.bat';

        if (!copy($batchfilename, $CFG->dataroot . '/stack/maxima.bat')) {
            throw new stack_exception('Could not copy the Maxima batch file ' . $batchfilename .
                    ' to location ' . $CFG->dataroot . '/stack/maxima.bat');
        }
    }

    public function maxima_bat_is_ok() {
        global $CFG;

        if ($this->settings->platform != 'win') {
            return true;
        }

        return is_readable($CFG->dataroot . '/stack/maxima.bat');
    }

    public function get_maximalocal_contents() {
        $contents = <<<END
/* ***********************************************************************/
/* This file is automatically generated at installation time.            */
/* The purpose is to transfer configuration settings to Maxima.          */
/* Hence, you should not edit this file.  Edit your configuration.       */
/* This file is regularly overwritten, so your changes will be lost.     */
/* ***********************************************************************/

/* File generated on {$this->date} */

/* Add the location to Maxima's search path */
file_search_maxima:append( [sconcat("{$this->maximacodepath}/###.{mac,mc}")] , file_search_maxima)$
file_search_lisp:append( [sconcat("{$this->maximacodepath}/###.{lisp}")] , file_search_lisp)$
file_search_maxima:append( [sconcat("{$this->logpath}/###.{mac,mc}")] , file_search_maxima)$
file_search_lisp:append( [sconcat("{$this->logpath}/###.{lisp}")] , file_search_lisp)$

STACK_SETUP(ex):=block(
    MAXIMA_VERSION_NUM:{$this->vnum},

END;
        foreach ($this->blocksettings as $name => $value) {
            $contents .= <<<END
    {$name}:"{$value}",

END;
        }
        $contents .= <<<END
    true)$

END;

        if ($this->settings->platform == 'unix-optimised' || $this->settings->platform == 'tomcat-optimised') {
            $contents .= <<<END
/* We are using an optimised lisp image with maxima and the stack libraries
   pre-loaded. That is why you don't see the familiar load("stackmaxima.mac")$ here. */

END;

        } else {
            $contents .= <<<END
/* Load the main libraries */
load("stackmaxima.mac")$

END;
        }

        return $contents;
    }

    /**
     * @return stack_cas_configuration the singleton instance of this class.
     */
    protected static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the full path for the maximalocal.mac file.
     * @return string the full path to where the maximalocal.mac file should be stored.
     */
    public static function maximalocal_location() {
        global $CFG;
        return stack_utils::convert_slash_paths($CFG->dataroot . '/stack/maximalocal.mac');
    }

    /**
     * Get the full path to the folder where plot files are stored.
     * @return string the full path to where the maximalocal.mac file should be stored.
     */
    public static function images_location() {
        global $CFG;
        return stack_utils::convert_slash_paths($CFG->dataroot . '/stack/plots');
    }

    /**
     * Create the maximalocal.mac file, overwriting it if it already exists.
     */
    public static function create_maximalocal() {
        make_upload_directory('stack');
        make_upload_directory('stack/logs');
        make_upload_directory('stack/plots');
        make_upload_directory('stack/tmp');

        self::get_instance()->copy_maxima_bat();

        $fh = fopen(self::maximalocal_location(), 'w');
        if ($fh === false) {
            throw new stack_exception('Failed to write Maxima configuration file.');
        } else {
            fwrite($fh, self::generate_maximalocal_contents());
            fclose($fh);
        }
    }

    /**
     * Generate the contents for the maximalocal configuration file.
     * @return string the contents that the maximalocal.mac file should have.
     */
    public static function generate_maximalocal_contents() {
        return self::get_instance()->get_maximalocal_contents();
    }

    /**
     * Generate the contents for the maximalocal configuration file.
     * @return string the contents that the maximalocal.mac file should have.
     */
    public static function maxima_bat_is_missing() {
        return !self::get_instance()->maxima_bat_is_ok();
    }

    /**
     * Generate the directoryname
     * @return string the contents that the maximalocal.mac file should have.
     */
    public static function confirm_maxima_win_location() {
        return self::get_instance()->maxima_win_location();
    }
}
