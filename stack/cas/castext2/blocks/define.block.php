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
require_once(__DIR__ . '/../../ast.container.class.php');

class stack_cas_castext2_define extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        $epos = $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end'];
        $r = new MP_Group([]);
        foreach ($this->params as $param) {
            $ev = stack_ast_container::make_from_teacher_source($param['value']);
            $ast = $ev->get_commentles_primary_statement();
            $r->items[] = new MP_FunctionCall(new MP_Identifier('_EC'),
                [
                    new MP_FunctionCall(new MP_Identifier('errcatch'), [
                        new MP_Operation(':', new MP_Identifier($param['key']), $ast)
                    ]),
                    new MP_String($epos)
                ]);
        }

        // In the end we need to return something. Note that this will break all
        // sort of simplifications and you may see some wacky logic working with this.
        // The recommended simplification rule is to move this before any static seen
        // before this, and jsut ignore what happens inside this.
        $r->items[] = new MP_String('');

        // TODO: consider a define that would define something for only its contents?
        // For now however define is assumed to be an empty block.
        // block(local(foo,bar),foo:1,bar:3,contents).
        return $r;
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        $r = [];
        foreach ($this->params as $param) {
            $r[] = stack_ast_container_silent::make_from_teacher_source($param['key'] . ':' .
                $param['value'], 'ct2:define', new stack_cas_security());
        }
        return $r;
    }
}
