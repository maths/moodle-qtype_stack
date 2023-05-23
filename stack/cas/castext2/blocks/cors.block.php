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
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/../../../../vle_specific.php');

/**
 * Simple block for dealing with CORS content urls.
 */
class stack_cas_castext2_cors extends stack_cas_castext2_block {

    public function compile($format, $options): ? MP_Node {
        $r = new MP_String(stack_cors_link($this->params['src']));
        return $r;
    }

    public function is_flat() : bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        if (!isset($this->params['src'])) {
            $errors[] = 'Needs src attribute.';
            return false;
        }
        return true;
    }
}
