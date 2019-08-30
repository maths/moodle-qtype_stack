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

// Comments blocks allow you to include comments in question text.
// This could be achieved with an [[ if test='false' ]] etc. but having comments as logically
// separate means we could extend the functionality to display them in the testing form etc.
//
// @copyright  2017 University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
require_once("block.interface.php");

class stack_cas_castext_comment extends stack_cas_castext_block {

    public function extract_attributes($tobeevaluatedcassession, $conditionstack = null) {
        return;
    }

    public function content_evaluation_context($conditionstack = array()) {
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {

        // Just gets replaced by nothing.
        $this->get_node()->convert_to_text('');

        return false;
    }

    public function validate_extract_attributes() {
        return array();
    }
}
