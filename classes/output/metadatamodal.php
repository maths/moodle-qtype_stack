<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk//
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

namespace qtype_stack\output;

use renderable;
use templatable;

/**
 * Render metadata modal
 *
 * @package qtype_stack
 * @copyright 2026 The University of Edinburgh
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadatamodal implements renderable, templatable {
    /**
     * Constructor.
     *
     */
    public function __construct() {
        global $PAGE;
        $PAGE->requires->js_call_amd('qtype_stack/metadata/metadatamodal', 'init');
    }
}
