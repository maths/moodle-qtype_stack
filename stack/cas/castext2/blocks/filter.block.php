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
//

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../../../utils.class.php');

/**
 * A block for specifying parsing options in ascii blocks.
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_cas_castext2_filter extends stack_cas_castext2_block {
    /** @var array valid filter types. */
    public static $filtertypes = [
        'calculation',
        'cas',
        'markdown',
        'markdown-math',
        'plain',
    ];

    /** @var array valid transform types. */
    public static $transtypes = [
        'asciimath',
        'aligneq',
        'boldfilter',
        'minwrap',
    ];

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        $r = new MP_List([
            new MP_String('filter'),
            new MP_String(json_encode($this->params)),
        ]);

        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        // These are never flat.
        return false;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        // No CAS arguments.
        return [];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        $valid = true;
        $err = [];

        if (!array_key_exists('type', $this->params)) {
            $valid = false;
            $err[] = stack_string('stackBlock_filter_type_required');
        } else {
            if (!in_array($this->params['type'], self::$filtertypes)) {
                    $valid = false;
                    $err[] = stack_string('stackBlock_filter_unknown', [
                        'type' => $this->params['type'],
                        'filters' => implode(', ', self::$filtertypes),
                    ]);
            }
        }

        if (array_key_exists('transforms', $this->params)) {
            $transforms = explode(',', $this->params['transforms']);
            $invalidtrans = [];
            foreach ($transforms as $transform) {
                if (!in_array($transform, self::$transtypes)) {
                    $invalidtrans[] = $transform;
                }
            }
            if (count($invalidtrans)) {
                $valid = false;
                $invalidtrans = implode(', ', $invalidtrans);
                $err[] = stack_string('stackBlock_filter_trans_unknown', [
                        'transforms' => $invalidtrans,
                    'valid' => implode(', ', self::$transtypes),
                ]);
            }
        }

        // Wrap the old string errors with the context details.
        foreach ($err as $er) {
            $errors[] = new $options['errclass']($er, $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
        }

        return $valid;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(
        array $params,
        castext2_processor $processor,
        castext2_placeholder_holder $holder
    ): string {
        return '';
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
