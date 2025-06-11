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
class stack_cas_castext2_hint extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {

        $body = new MP_List([new MP_String('%root')]);

        $body->items[] = new MP_String('<details class="stack-hint">');
        $body->items[] = new MP_String('<summary class="btn btn-secondary" >'.$this->params['title'].'</summary>');
        $body->items[] = new MP_String('<div class="stack-hint-content">');

        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $body->items[] = $c;
            }
        }
        $body->items[] = new MP_String('</div></details>');

        return $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return false;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['title'])) {
            return $r;
        }

        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('title', $this->params)) {
            $errors[] = new $options['errclass']('Hint block requires a title parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }

    /**
     * Is this an interactive block?
     * If true, we can't generate a static version.
     * @return bool
     */
    public function is_interactive(): bool {
        return true;
    }
}
