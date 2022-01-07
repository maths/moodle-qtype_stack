<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/demoodle.block.php');


/**
 * Block that will simply convert anything inside it from Moodle-auto-format
 * to HTML. Allowing certain types of mixed contents. Primarily exists
 * to map the problem of Moodle auto-format back to the normal HTML-processing.
 */
class stack_cas_castext2_moodleformat extends stack_cas_castext2_demoodle {
    // This is just an alias. The original has a more logical name, but this
    // is what people use.
}
