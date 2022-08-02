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

class stack_cas_castext2_if extends stack_cas_castext2_block {

    public function compile($format, $options): ?string {
        // If we are flat we just sconcat stuff to return but if not then we need to
        // generate the list version like the root-block.

        $flat = $this->is_flat();

        $r = '';
        if (!is_array($this->params['test'])) {
            $r = 'if (' . $this->params['test'] . ') then ';

            if (!$flat) {
                $r .= '["%root",';
            } else {
                $r .= 'sconcat(';
            }

            $items = array();
            foreach ($this->children as $item) {
                $c = $item->compile($format, $options);
                if ($c !== null) {
                    $items[] = $c;
                }
            }
            $r .= implode(',', $items);

            if (!$flat) {
                $r .= ']';
            } else {
                $r .= ')';
            }

            $r .= ' else ""';
        } else {
            $i = 0; // Total iterator.
            $j = 0; // In block iterator.
            $b = 0; // Branch iterator.
            $r = 'if (' . $this->params['test'][$b] . ') then ';

            if (!$flat) {
                $r .= '["%root",';
            } else {
                $r .= 'sconcat(';
            }

            $items = array();
            while ($j < $this->params[' branch lengths'][$b]) {
                $c = $this->children[$i]->compile($format, $options);
                if ($c !== null) {
                    $items[] = $c;
                }
                $i = $i + 1;
                $j = $j + 1;
            }
            $r .= implode(',', $items);

            if (!$flat) {
                $r .= ']';
            } else {
                $r .= ')';
            }

            $j = 0;
            $b = $b + 1;

            while ($b < count($this->params['test'])) {
                $r .= ' elseif (' . $this->params['test'][$b] . ') then ';

                if (!$flat) {
                    $r .= '["%root",';
                } else {
                    $r .= 'sconcat(';
                }

                $items = array();
                while ($j < $this->params[' branch lengths'][$b]) {
                    $c = $this->children[$i]->compile($format, $options);
                    if ($c !== null) {
                        $items[] = $c;
                    }
                    $i = $i + 1;
                    $j = $j + 1;
                }
                $j = 0;
                $b = $b + 1;
                $r .= implode(',', $items);

                if (!$flat) {
                    $r .= ']';
                } else {
                    $r .= ')';
                }
            }

            if ($b < count($this->params[' branch lengths'])) {
                $r .= ' else ';

                if (!$flat) {
                    $r .= '["%root",';
                } else {
                    $r .= 'sconcat(';
                }

                $items = array();
                while ($j < $this->params[' branch lengths'][$b]) {
                    $c = $this->children[$i]->compile($format, $options);
                    if ($c !== null) {
                        $items[] = $c;
                    }
                    $i = $i + 1;
                    $j = $j + 1;
                }
                $r .= implode(',', $items);

                if (!$flat) {
                    $r .= ']';
                } else {
                    $r .= ')';
                }
            }
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
        $r = array();
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
