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

class stack_cas_castext2_foreach extends stack_cas_castext2_block {

    public function compile($format, $options): ?string {
        $flat = $this->is_flat();

        if (count($this->params) === 0 || count($this->children) === 0) {
            return null;
        }

        $r = '';

        if (count($this->params) > 1) {

            $r = 'block(local(__ct2_foreach___iter,__ct2_foreach___tmp,' .
                implode(',', array_keys($this->params)). ',__ct2_foreach___' .
                implode(',__ct2_foreach___', array_keys($this->params)). ')';

            if ($flat) {
                $r .= ',__ct2_foreach___tmp:""';
            } else {
                $r .= ',__ct2_foreach___tmp:["%root"]';
            }
            foreach ($this->params as $key => $value) {
                $ev = stack_ast_container::make_from_teacher_source($value);
                $ev = $ev->get_evaluationform();
                $r .= ',__ct2_foreach___' . $key . ':listify(' . $ev . ')';
            }
            $r .= ',for __ct2_foreach___iter:1 thru ev(min(length(__ct2_foreach___' .
                implode('),length(__ct2_foreach___', array_keys($this->params)) .')),simp) do (';

            $sets = array();
            foreach ($this->params as $key => $value) {
                $ev = stack_ast_container::make_from_teacher_source($value);
                $ev = $ev->get_evaluationform();
                $sets[] = $key . ':__ct2_foreach___' . $key . '[ev(__ct2_foreach___iter,simp)]';
            }
            $r .= implode(',', $sets) . ',';

        } else {
            // If we only iterate over one thing we can skip the min logic and assing directly.
            $r = 'block(local(__ct2_foreach___tmp,' . implode(',', array_keys($this->params)). ')';

            if ($flat) {
                $r .= ',__ct2_foreach___tmp:""';
            } else {
                $r .= ',__ct2_foreach___tmp:["%root"]';
            }
            $ev = stack_ast_container::make_from_teacher_source($this->params[array_keys($this->params)[0]]);
            $ev = $ev->get_evaluationform();
            $r .= ', for ' . array_keys($this->params)[0] . ' in listify(' . $ev . ') do (';
        }

        $internal = array();
        foreach ($this->children as $child) {
            $c = $child->compile($format, $options);
            if ($c !== null) {
                $internal[] = $c;
            }
        }
        if ($flat) {
            $r .= '__ct2_foreach___tmp:sconcat(__ct2_foreach___tmp,' . implode(',', $internal). ')';
        } else {
            $r .= '__ct2_foreach___tmp:append(__ct2_foreach___tmp,[' . implode(',', $internal). '])';
        }

        $r .= '),__ct2_foreach___tmp)';

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

    public function validate_extract_attributes(): array {
        $r = array();
        foreach ($this->params as $key => $value) {
            $r[] = stack_ast_container_silent::make_from_teacher_source($key . ':' . $value, 'ct2:foreach',
                new stack_cas_security());
        }
        return $r;
    }
}
