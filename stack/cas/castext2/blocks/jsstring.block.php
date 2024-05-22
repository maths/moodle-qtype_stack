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

/**
 * Block for turning CASText output into JavaScript string literals.
 */
class stack_cas_castext2_jsstring extends stack_cas_castext2_block {

    public function compile($format, $options):  ? MP_Node {
        $r = new MP_List([new MP_String('jsstring')]);

        $allstrings = true;
        $strings = '';
        foreach ($this->children as $item) {
            // We do not force a format here.
            $c = $item->compile($format, $options);
            if ($c !== null) {
                if (!($c instanceof MP_String)) {
                    $allstrings = false;
                } else {
                    $strings = $strings . $c->value;
                }
                $r->items[] = $c;
            }
        }

        // The special case of static content.
        if ($allstrings) {
            return new MP_String(json_encode($strings));
        }

        return $r;
    }

    public function is_flat(): bool {
        // Now then the problem here is that the flatness depends on the flatness of
        // the blocks contents. If they all generate strings then we are flat but if not...
        $flat = true;

        foreach ($this->children as $child) {
            $flat = $flat && $child->is_flat();
        }

        return $flat;
    }

    public function postprocess(array $params, castext2_processor $processor): string {
        // Combine the content and then escape it as necessary.
        $content    = '';
        for ($i = 1; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $content .= $processor->process($params[$i][0], $params[$i]);
            } else {
                $content .= $params[$i];
            }
        }
        return json_encode($content);
    }

    public function validate_extract_attributes(): array {
        return [];
    }
}
