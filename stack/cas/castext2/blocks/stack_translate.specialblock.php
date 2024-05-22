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
require_once(__DIR__ . '/../../../../locallib.php');

class stack_cas_castext2_special_stack_translate extends stack_cas_castext2_block {
    public function compile($format, $options): ?MP_Node {
        // These blocks do not actually exist in the normal input flow, they only appear in
        // PRT feedback generation. For now.
        return new MP_String('');
    }

    public function is_flat(): bool {
        // While flat there always require post processing.
        return false;
    }

    /**
     * If this is not a flat block this will be called with the response from CAS and
     * should execute whatever additional logic is needed. Register JavaScript and such
     * things it must then return the content that will take this blocks place.
     */
    public function postprocess(array $params, castext2_processor $processor): string {
        if (count($params) < 2) {
            // Nothing at all.
            return '';
        }

        $t = $params[1];

        return stack_maxima_translate($t);
    }

    public function validate_extract_attributes(): array {
        return [];
    }
}
