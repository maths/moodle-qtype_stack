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


require_once(__DIR__ . '/../block.interface.php');


class stack_cas_castext2_todo extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        $body = new MP_List([new MP_String('%root')]);
        $body->items[] = new MP_String('<!--- stack_todo --->');
        return $body;
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        return [];
    }
}
