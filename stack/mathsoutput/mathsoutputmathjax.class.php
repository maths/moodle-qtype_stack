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

require_once($CFG->dirroot .'/filter/mathjaxloader/filter.php');

/**
 * STACK maths output methods for using MathJax.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_mathjax extends stack_maths_output {

    public function process_display_castext($text, $replacedollars, qtype_stack_renderer $renderer = null, $forcesomemaths = false) {
        // When rendering with MathJax the questiontext must always contain some maths.
        // This is to ensure the javascript is active so that any ajax processing is picked up.
        if ($forcesomemaths && strpos($text, '\(') === false && strpos($text, '\[') === false) {
            $text .= '\(\)';
        }
        return parent::process_display_castext($text, $replacedollars, $renderer);
    }
}
