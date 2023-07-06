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

class stack_cas_castext2_escape extends stack_cas_castext2_block {

    public $content;

    public function __construct($params, $children=array(), $mathmode=false) {
        parent::__construct($params, $children, $mathmode);
        if (count($children) > 0) {
            $this->content = $children[0]->content; // The child is a different type of RAW.
        } else {
            $this->content = null;
        }
        $this->children = array(); // We want to modify the iteration here a bit.
    }

    public function compile($format, $options): ?MP_Node {
        if ($this->content === null && !array_key_exists('value', $this->params)) {
            return null;
        }
        if (array_key_exists('value', $this->params)) {
            return new MP_String($this->params['value']);
        }

        return new MP_String($this->content);
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        return array();
    }

    public function validate(&$errors=[], $options=[]): bool {
        // Due to escape block needing some backwards compatibility we still need to support
        // the old way of defining the value as an parameter but not both ways at the same time.

        if ($this->content !== null && array_key_exists('value', $this->params)) {
            $errors[] = new $options['errclass']('Cannot use both old value-attribute and block-content in escape-block.',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}
