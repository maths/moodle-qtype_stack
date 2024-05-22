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

class stack_cas_castext2_if extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        // If we are flat we just sconcat stuff to return but if not then we need to
        // generate the list version like the root-block.

        $r = null;
        if (!is_array($this->params['test'])) {
            $ev = stack_ast_container::make_from_teacher_source($this->params['test']);
            $ast = new MP_Group([$ev->get_commentles_primary_statement()]);

            $flat = $this->is_flat();
            $body = null;

            if (!$flat) {
                $body = new MP_List([new MP_String('%root')]);
            } else {
                $body = new MP_FunctionCall(new MP_Identifier('sconcat'), [new MP_String('')]);
            }

            $items = [];
            foreach ($this->children as $item) {
                $c = $item->compile($format, $options);
                if ($c !== null) {
                    if ($flat) {
                        $body->arguments[] = $c;
                    } else {
                        $body->items[] = $c;
                    }
                }
            }

            $r = new MP_If([$ast], [$body, new MP_String('')]);
        } else {
            $tests = [];
            $branches = [];

            $i = 0; // Total iterator.
            $j = 0; // In block iterator.
            $b = 0; // Branch iterator.
            $ev = stack_ast_container::make_from_teacher_source($this->params['test'][$b]);
            $ast = new MP_Group([$ev->get_commentles_primary_statement()]);

            $flat = true;
            $items = [];
            while ($j < $this->params[' branch lengths'][$b]) {
                $c = $this->children[$i]->compile($format, $options);
                if ($c !== null) {
                    $flat = $flat && $this->children[$i]->is_flat();
                    $items[] = $c;
                }
                $i = $i + 1;
                $j = $j + 1;
            }
            $body = [];
            if (!$flat) {
                $body = new MP_List([new MP_String('%root')]);
                foreach ($items as $it) {
                    $body->items[] = $it;
                }
            } else {
                $body = new MP_FunctionCall(new MP_Identifier('sconcat'), [new MP_String('')]);
                foreach ($items as $it) {
                    $body->arguments[] = $it;
                }
            }

            $tests[] = $ast;
            $branches[] = $body;

            $j = 0;
            $b = $b + 1;

            while ($b < count($this->params['test'])) {
                $ev = stack_ast_container::make_from_teacher_source($this->params['test'][$b]);
                $ast = new MP_Group([$ev->get_commentles_primary_statement()]);

                $flat = true;
                $items = [];
                while ($j < $this->params[' branch lengths'][$b]) {
                    $c = $this->children[$i]->compile($format, $options);
                    if ($c !== null) {
                        $flat = $flat && $this->children[$i]->is_flat();
                        $items[] = $c;
                    }
                    $i = $i + 1;
                    $j = $j + 1;
                }
                $j = 0;
                $b = $b + 1;
                $body = [];
                if (!$flat) {
                    $body = new MP_List([new MP_String('%root')]);
                    foreach ($items as $it) {
                        $body->items[] = $it;
                    }
                } else {
                    $body = new MP_FunctionCall(new MP_Identifier('sconcat'), [new MP_String('')]);
                    foreach ($items as $it) {
                        $body->arguments[] = $it;
                    }
                }

                $tests[] = $ast;
                $branches[] = $body;
            }

            if ($b < count($this->params[' branch lengths'])) {
                $flat = true;
                $items = [];
                while ($j < $this->params[' branch lengths'][$b]) {
                    $c = $this->children[$i]->compile($format, $options);
                    if ($c !== null) {
                        $flat = $flat && $this->children[$i]->is_flat();
                        $items[] = $c;
                    }
                    $i = $i + 1;
                    $j = $j + 1;
                }
                $body = [];
                if (!$flat) {
                    $body = new MP_List([new MP_String('%root')]);
                    foreach ($items as $i) {
                        $body->items[] = $i;
                    }
                } else {
                    $body = new MP_FunctionCall(new MP_Identifier('sconcat'), [new MP_String('')]);
                    foreach ($items as $i) {
                        $body->arguments[] = $i;
                    }
                }
                $branches[] = $body;
            }

            $r = new MP_If($tests, $branches);
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

    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['test'])) {
            return $r;
        }

        if (is_array($this->params['test'])) {
            foreach ($this->params['test'] as $item) {
                $r[] = stack_ast_container_silent::make_from_teacher_source($item, 'ct2:if', new stack_cas_security());;
            }
        } else {
            $r[] = stack_ast_container_silent::make_from_teacher_source($this->params['test'], 'ct2:if',
                new stack_cas_security());;
        }

        return $r;
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('test', $this->params)) {
            $errors[] = new $options['errclass']('If block requires a test parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}
