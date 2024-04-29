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
require_once($CFG->dirroot . '/filter/tex/filter.php');
require_once(__DIR__ . '/mathsoutputfilterbase.class.php');


/**
 * STACK maths output methods for using Moodle's TeX filter.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_tex extends stack_maths_output_filter_base {

    protected function initialise_delimiters() {
        $this->displaystart = '\[\displaystyle ';
        $this->displayend = '\]';
        $this->inlinestart = '\[';
        $this->inlineend = '\]';
    }

    protected function make_filter() {
        return new filter_tex(context_system::instance(), []);
    }
}
