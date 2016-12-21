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

global $CFG;
require_once($CFG->libdir . '/filterlib.php');
require_once(__DIR__ . '/mathsoutputfilterbase.class.php');


/**
 * STACK maths output methods for using The OU's maths filter.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_maths extends stack_maths_output_filter_base {

    /**
     * @return boolean is the OU maths filter installed?
     */
    public static function filter_is_installed() {
        global $CFG;
        return file_exists($CFG->dirroot . '/filter/maths/filter.php');
    }

    protected function initialise_delimiters() {
        $this->displaystart = '&lt;tex mode="display"&gt;';
        $this->displayend = '&lt;/tex&gt;';
        $this->inlinestart = '&lt;tex mode="inline"&gt;';
        $this->inlineend = '&lt;/tex&gt;';
    }

    protected function make_filter() {
        global $CFG;

        if (!self::filter_is_installed()) {
            throw new coding_exception('The OU maths filter is not installed.');
        }

        require_once($CFG->dirroot . '/filter/maths/filter.php');
        return new filter_maths(context_system::instance(), array());
    }
}
