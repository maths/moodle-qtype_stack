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

// Class to hold an array of iframes created during an API call
//
// Iframes are defined by an array of arguments suitable for
// sending to stackjsvle.js->create_iframe().
//
// Honestly this is acting like a global variable. It minimises changes to
// to the iframe block to accomodate the API.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\util;

class StackIframeHolder {
    public static $iframes = [];
    /** @var bool Are we on the library page? */
    public static $islibrary = false;

    public static function add_iframe($args) {
        for ($i = 0; $i < 5; $i++) {
            $args[$i] = json_decode($args[$i]);
        }
        array_push(self::$iframes, $args);
    }

}
