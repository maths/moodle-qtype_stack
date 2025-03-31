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
 * @copyright  2017 Matti Harjula.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../utils.php');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_cas_castext2_debug extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        // So we are to print out a table of bound variable values.
        $bounds = [];
        if (is_array($options) && isset($options['bound-vars'])) {
            $bounds = $options['bound-vars'];
        }

        // We are lazy and are not going to write this logic ourselves,
        // instead fall back to CASText and let other parts do the task.
        if (count($bounds) == 0) {
            return castext2_parser_utils::compile('[[commonstring key="castext_debug_no_vars"/]]', $format, $options);
        }
        $castext = '';
        if ($format === castext2_parser_utils::MDFORMAT) {
            // Test the MD-formating by building a table.
            $castext = '| [[commonstring key="castext_debug_header_key"/]] ' .
                       '| [[commonstring key="castext_debug_header_value_simp"/]] ' .
                       '| [[commonstring key="castext_debug_header_value_no_simp"/]] ' .
                       '| [[commonstring key="castext_debug_header_disp_simp"/]] ' .
                       '| [[commonstring key="castext_debug_header_disp_no_simp"/]] |';
            $castext .= "\n| --- | --- | --- | --- | --- |";

            foreach ($bounds as $key => $ignore) {
                $castext .= "\n| `$key` | `{#$key,simp#}` | `{#$key,simp=false#}` | {@$key,simp@} | {@$key,simp=false@} |";
            }
        } else {
            $castext = '<table class="table"><thead><th>[[commonstring key="castext_debug_header_key"/]]</th>' .
                '<th>[[commonstring key="castext_debug_header_value_simp"/]]</th>' .
                '<th>[[commonstring key="castext_debug_header_value_no_simp"/]]</th>' .
                '<th>[[commonstring key="castext_debug_header_disp_simp"/]]</th>' .
                '<th>[[commonstring key="castext_debug_header_disp_no_simp"/]]</th></td></thead>';
            $castext .= '<tbody>';
            foreach ($bounds as $key => $ignore) {
                $castext .= "<tr><td><code>$key</code></td><td><code>{#$key,simp#}</code></td>" .
                    "<td><code>{#$key,simp=false#}</code></td><td>{@$key,simp@}</td><td>{@$key,simp=false@}</td></tr>";
            }
            $castext .= "</tbody></table>\n";
        }

        return castext2_parser_utils::compile($castext, $format, $options);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        // ISS1085 - Change to false. Common strings need to be evaluated.
        return false;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        return [];
    }
}
