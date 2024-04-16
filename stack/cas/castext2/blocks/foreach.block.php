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

    public function compile($format, $options): ?MP_Node {
        $flat = $this->is_flat();

        if (count($this->params) === 0 || count($this->children) === 0) {
            return null;
        }

        $local = new MP_FunctionCall(new MP_Identifier('local'), []);
        // The varaible holding the result.
        $local->arguments[] = new MP_Identifier('__ct2_foreach___tmp');
        // Variables holding the values of current iteration.
        foreach ($this->params as $key => $duh) {
            $local->arguments[] = new MP_Identifier($key);
        }
        $r = new MP_FunctionCall(new MP_Identifier('block'), [$local]);

        // The body.
        $internal = [new MP_Identifier('__ct2_foreach___tmp')];
        foreach ($this->children as $child) {
            $c = $child->compile($format, $options);
            if ($c !== null) {
                $internal[] = $c;
            }
        }
        // The actual body is a singel statement updateing a total output variable.
        $body = null;
        // We try to keep things simpler if we know the result is nice and flat.
        if ($flat) {
            $body = new MP_Operation(':', new MP_Identifier('__ct2_foreach___tmp'),
                new MP_FunctionCall(new MP_Identifier('sconcat'), $internal));
        } else {
            array_shift($internal);
            $body = new MP_Operation(':', new MP_Identifier('__ct2_foreach___tmp'),
                new MP_FunctionCall(new MP_Identifier('append'), [new MP_Identifier('__ct2_foreach___tmp'),
                new MP_List($internal)]));
        }

        if (count($this->params) > 1) {
            $local->arguments[] = new MP_Identifier('__ct2_foreach___iter');

            // Variables holding the evaluated lists.
            foreach ($this->params as $key => $duh) {
                $local->arguments[] = new MP_Identifier('__ct2_foreach___' . $key);
            }

            // Init based on the type of result.
            if ($flat) {
                $r->arguments[] = new MP_Operation(':', new MP_Identifier('__ct2_foreach___tmp'),
                    new MP_String(''));
            } else {
                $r->arguments[] = new MP_Operation(':', new MP_Identifier('__ct2_foreach___tmp'),
                    new MP_List([new MP_String('%root')]));
            }
            // Evaluate the lists.
            $lengths = [];
            foreach ($this->params as $key => $value) {
                $ev = stack_ast_container::make_from_teacher_source($value);
                $ast = $ev->get_commentles_primary_statement();
                $lengths[] = new MP_FunctionCall(new MP_Identifier('length'), [new MP_Identifier('__ct2_foreach___' . $key)]);
                $r->arguments[] = new MP_Operation(':', new MP_Identifier('__ct2_foreach___' . $key),
                    new MP_FunctionCall(new MP_Identifier('listify'), [$ast])
                );
            }

            // Build the defines. Which move the value of given index from those evaluated lists to the correct identifier.
            $definedbody = new MP_Group([]);
            foreach ($this->params as $key => $value) {
                $definedbody->items[] = new MP_Operation(':', new MP_Identifier($key),
                    new MP_Indexing(new MP_Identifier('__ct2_foreach___' . $key),
                    [new MP_List([new MP_FunctionCall(new MP_Identifier('ev'),
                    [new MP_Identifier('__ct2_foreach___iter'), new MP_Identifier('simp')])])]));
            }
            $definedbody->items[] = $body;

            $r->arguments[] = new MP_Loop($definedbody, [
                new MP_LoopBit('for', new MP_Operation(':', new MP_Identifier('__ct2_foreach___iter'),
                new MP_Integer(1))),
                new MP_LoopBit('thru', new MP_FunctionCall(new MP_Identifier('ev'),
                    [
                        new MP_FunctionCall(new MP_Identifier('min'), $lengths),
                        new MP_Identifier('simp')
                    ]))
            ]);
        } else {
            // If we only iterate over one thing we can skip the min logic and assing directly.
            // Init based on the type of result.
            if ($flat) {
                $r->arguments[] = new MP_Operation(':', new MP_Identifier('__ct2_foreach___tmp'),
                    new MP_String(''));
            } else {
                $r->arguments[] = new MP_Operation(':', new MP_Identifier('__ct2_foreach___tmp'),
                    new MP_List([new MP_String('%root')]));
            }

            $ev = stack_ast_container::make_from_teacher_source($this->params[array_keys($this->params)[0]]);
            $ast = $ev->get_commentles_primary_statement();
            $r->arguments[] = new MP_Loop($body, [
                new MP_LoopBit('for', new MP_Identifier(array_keys($this->params)[0])),
                new MP_LoopBit('in', new MP_FunctionCall(new MP_Identifier('listify'), [$ast]))
            ]);
        }

        $r->arguments[]  = new MP_Identifier('__ct2_foreach___tmp');

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
        $r = [];
        foreach ($this->params as $key => $value) {
            $r[] = stack_ast_container_silent::make_from_teacher_source($key . ':' . $value, 'ct2:foreach',
                new stack_cas_security());
        }
        return $r;
    }
}
