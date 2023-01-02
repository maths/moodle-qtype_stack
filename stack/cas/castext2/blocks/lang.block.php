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
require_once(__DIR__ . '/../../../utils.class.php');

/**
 * A block for togling content based on a the active language.
 * The active language is defined elsewhere and exists as an variable
 * in the session.
 */
class stack_cas_castext2_lang extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        $checks = null;
        foreach (explode(',', $this->params['code']) as $code) {
            // Normalise codes like the other filters...
            $c = str_replace('-', '_', strtolower(trim($code)));
            $check = new MP_FunctionCall(new MP_Identifier('is_lang'), [new MP_String($c)]);
            if ($checks === null) {
                $checks = $check;
            } else {
                $checks = new MP_Operation('or', $checks, $check);
            }
        }

        $body = null;
        $items = [];
        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $items[] = $c;
            }
        }
        // If only one thing then no need to wrap it, save space and processing...
        if (count($items) === 1) {
            $body = $items[0];
        } else {
            if ($his->is_flat()) {
                $body = new MP_FunctionCall(new MP_Identifier('sconcat'), $items);
            } else {
                array_unshift($items, new MP_String('%root'));
                $body = new MP_List($items);
            }
        }

        $r = new MP_If([new MP_Group($checks)], [$body, new MP_String('')]);

        return $r;
    }

    public function is_flat(): bool {
        $flat = true;

        foreach ($this->children as $child) {
            $flat = $flat && $child->is_flat();
        }

        return $flat;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('code', $this->params)) {
            $errors[] = new $options['errclass']('The "lang"-block needs a code atribute with a singular code or a comma ' .
                'separated list of alternatives.', $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
            return false;
        }

        return true;
    }
}
