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
require_once(__DIR__ . '/../../ast.container.silent.class.php');
require_once(__DIR__ . '/../../ast.container.class.php');

class stack_cas_castext2_castext extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        // The purpose of this block is to inject a section of CASText
        // structure into another CASText structure so this does
        // very little.
        $ev = stack_ast_container::make_from_teacher_source($this->params['evaluated']);
        $ast = $ev->get_commentles_primary_statement();
        return $ast;
    }

    public function is_flat(): bool {
        return false;
    }

    public function validate_extract_attributes(): array {
        $r = [stack_ast_container_silent::make_from_teacher_source($this->params['evaluated'],
            'ct2:castext', new stack_cas_security())];
        return $r;
    }

    public function validate(&$errors=[], $options = []): bool {
        if (!array_key_exists('evaluated', $this->params)) {
            $errors[] = new $options['errclass']('The castext block must be empty and needs to have the "evaluated" ' .
                'attribute providing the castext-fragment.', $options['context'] . '/' . $this->position['start'] .
                '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}
