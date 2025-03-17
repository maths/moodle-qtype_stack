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

class stack_cas_castext2_adapt extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {

        $body = new MP_List([new MP_String('%root')]);

        $adapt_id = "stack-adapt-" . $this->params['id'];

        $style = "";
        if (isset($this->params['hidden'])) {
            if ($this->params['hidden']=='true') {
                $style = 'style="display:none;"';
            }
        }   

        $body->items[] = new MP_String('<div id="' . $adapt_id . '" ' . $style . '>');

        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $body->items[] = $c;
            }
        }
        $body->items[] = new MP_String('</div>');

        return $body;
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['id'])) {
            return $r;
        }
        return $r;
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('id', $this->params)) {
            $errors[] = new $options['errclass']('Adapt block requires a id parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }
}
