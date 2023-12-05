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
require_once(__DIR__ . '/../utils.php');

class stack_cas_castext2_debug extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        // So we are to print out a table of bound variable values.
        $bounds = [];
        if (is_array($options) && isset($options['bound-vars'])) {
            $bounds = $options['bound-vars'];
        }

        // We are lazy and are not going to write this logic ourselves,
        // instead fall back to CASText and let other parts do the task.
        if (count($bounds) == 0) {
            return new MP_List([new MP_String('%cs'), new MP_String('castext_debug_no_vars')]);
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
            $castext = '<table><thead><th>[[commonstring key="castext_debug_header_key"/]]</th>' .
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

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        return array();
    }
}
