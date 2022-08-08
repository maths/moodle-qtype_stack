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

    public function compile($format, $options): ?string {
        $checks = [];
        foreach (explode(',', $this->params['code']) as $code) {
            // Normalise codes like the other filters...
            $c = str_replace('-', '_', strtolower(trim($code)));
            $checks[] = 'is_lang(' . stack_utils::php_string_to_maxima_string($c) . ')';
        }

        $r = 'if (';
        $r .= implode(' or ', $checks);
        $r .= ') then (';
        $items = [];
        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $items[] = $c;
            }
        }
        // If only one thing then no need to wrap it, save space and processing...
        if (count($items) === 1) {
            $r .= $items[0];
        } else {
            $r .= '["%root", ' . implode(',', $items) . ']';
        }
        // Remember to return something.
        $r .= ') else ("")';
        return $r;
    }

    public function is_flat(): bool {
        return false;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(array $options, &$errors=array()): bool {
        if (!array_key_exists('code', $this->params)) {
            $errors[] = new $options['errclass']('The "lang"-block needs a code atribute with a singular code or a comma ' .
                'separated list of alternatives.', $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
            return false;
        }

        return true;
    }
}
