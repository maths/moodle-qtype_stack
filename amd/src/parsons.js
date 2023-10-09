// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A javascript module to handle the real-time validation of the input the student types
 * into STACK questions.
 *
 * The overall way this works is as follows:
 *
 *  - right at the end of this file are the init methods, which set things up.
 *  - The work common to all input types is done by StackInput.
 *     - Sending the Ajax request.
 *     - Updating the validation display.
 *  - The work specific to different input types (getting the content of the inputs) is done by
 *    the classes like
 *     - StackSimpleInput
 *     - StackTextareaInput
 *     - StackMatrixInput
 *    objects of these types need to implement the two methods addEventHandlers and getValue().
 *
 * @module     qtype_stack/input
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function helloWorld() {
    print("Hello, world");
}