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

/**
 * A block to turn unknown block references to something readable.
 * 
 * As requested in #959.
 */
class stack_cas_castext2_unknown extends stack_cas_castext2_block {

    public function compile($format, $options): ? MP_Node {
        // There is an intentional space for that " type" there.
        // replicate something sensible. Act as an escape block..
        $r = '[[' . $this->params[' type'];
        foreach ($this->params as $key => $value) {
            if ($key !== ' type') {
                $r .= ' ' . $key . '=';
                $r .= (new MP_String($value))->toString();
            }
        }
        $r .= ']]';
        $r .= stack_string("unknown_block", ['type' => $this->params[' type']]);
        $r .= '[[/' . $this->params[' type'] . ']]';

        $r = new MP_String($r);
        return $r;
    }

    public function is_flat() : bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        if (!isset($this->params[' type'])) {
            $errors[] = 'Unknown blocks do not exist for unnamed blocks.';
            return false;
        }
        return true;
    }
}
