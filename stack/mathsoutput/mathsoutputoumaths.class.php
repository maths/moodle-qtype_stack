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
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_oumaths extends stack_maths_output_filter_base {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function initialise_delimiters() {
        $this->displaystart = '&lt;tex mode="display"&gt;';
        $this->displayend = '&lt;/tex&gt;';
        $this->inlinestart = '&lt;tex mode="inline"&gt;';
        $this->inlineend = '&lt;/tex&gt;';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function make_filter() {
        global $CFG;

        if (class_exists('\filter_oumaths\text_filter')) {
            return new \filter_oumaths\text_filter(context_system::instance(), []);
        } else if (file_exists($CFG->dirroot . '/filter/oumaths/filter.php')) {
            // Once Moodle 4.5 is the lowest supported version of Moodle.
            require_once($CFG->libdir . '/filterlib.php');
            require_once($CFG->dirroot . '/filter/oumaths/filter.php');
            return new filter_oumaths(context_system::instance(), []);
        }
        throw new coding_exception('The OU maths filter is not installed.');
    }
}
