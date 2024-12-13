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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');


// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_cas_castext2_todo extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        $body = new MP_List([new MP_String('%root')]);
        $body->items[] = new MP_String('<!--- stack_todo --->');
        return $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return true;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        return [];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function extract_todo(): array {
        $tags = [];
        if (!array_key_exists('tags', $this->params)) {
            return [];
        }

        foreach (explode(',', $this->params['tags']) as $tag) {
            $tag = trim($tag);
            if (!array_key_exists($tag, $tags)) {
                $tags[$tag] = true;
            }
        }
        return array_keys($tags);
    }
}
