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

/**
 * A block for outputting question usage level unique identifiers.
 * Primarily used for scripting and to ensure that identifiers stay
 * unique and do not collide when simialr logic gets copied to multiple
 * questions and those questions appear on the same page.
 */
class stack_cas_castext2_quid extends stack_cas_castext2_block {


    public function compile($format, $options): ?MP_Node {
        return new MP_List([new MP_String('quid'), new MP_String($this->params['id'])]);
    }

    public function is_flat(): bool {
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor,
        castext2_placeholder_holder $holder): string {
        $id = $params[1];
        // Use the input field naming to get the question usage level id.
        // Add some extra chars to avoid likely collisions with inputs, those cannot
        // have the `-`-char in their names.
        $content = $processor->qa->get_qt_field_name('-quid_' . $id);
        return $content;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('id', $this->params)) {
            $errors[] = new $options['errclass']('quid-blocks need an id-attribute with a value.',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}
