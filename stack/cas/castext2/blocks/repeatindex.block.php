<?php
// This file is part of STACK
//
// STACK is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// STACK is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The number of the copy of a repeat block, [[repeatindex/]].
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @copyright  2025 Ruhr University Bochum.
 * @copyright  2025 ETH Zürich.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/repeat.block.php');

/**
 * The number of the copy of a repeat block, replaced client side.
 */
class stack_cas_castext2_repeatindex extends stack_cas_castext2_block {

    /**
     * Set by the enclosing repeat block, which is validated before its children.
     * @var bool
     */
    public $insiderepeat = false;

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        return new MP_String(stack_cas_castext2_repeat::REPEATINDEX);
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
    public function validate(&$errors = [], $options = []): bool {
        if (!$this->insiderepeat) {
            $errors[] = new $options['errclass']('The repeatindex block may only be used inside a repeat block.',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (count($this->children) > 0) {
            $errors[] = new $options['errclass']('The repeatindex block may not have any content, use [[repeatindex/]].',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }
}
