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

require_once(__DIR__ . '/../algebraic/algebraic.class.php');
require_once(__DIR__ . '/../string/string.class.php');

/**
 * A basic text-field input which is always interpreted as a Maxima string.
 * This has been requested to support the input of things like multi-base numbers.
 *
 * @package    qtype_stack
 * @copyright  2018 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_json_input extends stack_string_input {
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'validator' => false,
    ];

    /**
     * This function constructs the display of variables during validation as JSON.
     *
     * @param stack_casstring $answer, the complete answer.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function validation_display($answer, $lvars, $caslines, $additionalvars, $valid, $errors,
        $castextprocessor, $inertdisplayform, $ilines, $notes) {

        // Always display something sensible.
        $display = $this->contents_to_maxima($this->rawcontents);
        $display = substr($display, 1, strlen($display) - 2);
        if ($answer->is_correctly_evaluated()) {
            $display = stack_utils::maxima_string_to_php_string($answer->get_value());
        } else {
            $valid = false;
        }
        $json = json_decode($display);
        // If we have mal-formed JSON (exactly the situation we need to debug) then we display the original.
        if ($json !== null) {
            $display = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }
        $display = html_writer::tag('pre', $display);

        return [$valid, $errors, $display, $notes];
    }
}
