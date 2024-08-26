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

require_once($CFG->libdir . '/filterlib.php');
require_once(__DIR__ . '/mathsoutputfilterbase.class.php');

/**
 * STACK maths output methods for using MathJax.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_mathjax extends stack_maths_output_filter_base {

    protected function initialise_delimiters() {
        $this->displaywrapstart = '';
        $this->displaywrapend = '';
        $this->displaystart = '\[';
        $this->displayend = '\]';
        $this->inlinestart = '\(';
        $this->inlineend = '\)';
    }

    protected function make_filter() {
        global $CFG, $PAGE;
        if (class_exists('\filter_mathjaxloader\text_filter')) {
            $filter = new \filter_mathjaxloader\text_filter($PAGE->context, []);
        } else {
            // Once Moodle 4.5 is the lowest supported version of Moodle.
            require_once($CFG->libdir . '/filterlib.php');
            require_once($CFG->dirroot . '/filter/mathjaxloader/filter.php');
            return new filter_mathjaxloader($PAGE->context, []);
        }

        $filter->setup($PAGE, $PAGE->context);
        return $filter;
    }
}
