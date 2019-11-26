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

defined('MOODLE_INTERNAL')|| die();

// Ast container and related functions, which replace "cas strings".
//
// @copyright  2019 University of Aalto.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/cassecurity.class.php');
require_once(__DIR__ . '/ast.container.silent.class.php');
require_once(__DIR__ . '/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');


class stack_ast_container_conditional extends stack_ast_container {

    private $conditions;

    public function set_conditions(array $conditions) {
        $this->conditions = $conditions;
    }

    public function get_valid(): bool {
        $valid = parent::get_valid();
        foreach ($this->conditions as $cond) {
            $valid = $valid && $cond->get_valid();
        }
        return $valid;
    }

    public function get_evaluationform(): string {
        if ($this->conditions === null || count($this->conditions) === 0) {
            return parent::get_evaluationform();
        }
        $content = parent::get_evaluationform();
        $conds = array();
        foreach ($this->conditions as $cond) {
            $conds[] = '(' . $cond->get_evaluationform() .')';
        }
        $r = 'if ' . implode(' and ', $conds) . ' then (' . $content . ') else ';
        if ($this->get_key() !== '') {
            $r .= $this->get_key() . ':false';
        } else {
            $r .= 'false';
        }
        return $r;
    }

    /**
     * Cloning is complex when we have object references.
     */
    public function __clone() {
        parent::__clone();
        if ($this->conditions !== null && count($this->conditions) > 0) {
            $i = 0;
            for ($i = 0; $i < $this->conditions; $i++) {
                $this->conditions[$i] = clone $this->conditions[$i];
            }
        }
    }
}

class stack_ast_container_conditional_value extends stack_ast_container_silent implements cas_value_extractor {

    private $conditions;
    private $evaluated;

    public function set_cas_evaluated_value(MP_Node $ast) {
        $this->evaluated = $ast;
    }

    public function set_conditions(array $conditions) {
        $this->conditions = $conditions;
    }

    public function get_valid(): bool {
        $valid = parent::get_valid();
        foreach ($this->conditions as $cond) {
            $valid = $valid && $cond->get_valid();
        }
        return $valid;
    }

    public function get_evaluationform(): string {
        if ($this->conditions === null || count($this->conditions) === 0) {
            return parent::get_evaluationform();
        }
        $content = parent::get_evaluationform();
        $conds = array();
        foreach ($this->conditions as $cond) {
            $conds[] = '(' . $cond->get_evaluationform() .')';
        }
        $r = 'if ' . implode(' and ', $conds) . ' then (' . $content . ') else ';
        if ($this->get_key() !== '') {
            $r .= $this->get_key() . ':false';
        } else {
            $r .= 'false';
        }
        return $r;
    }

    /**
     * Cloning is complex when we have object references.
     */
    public function __clone() {
        parent::__clone();
        if ($this->conditions !== null && count($this->conditions) > 0) {
            $i = 0;
            for ($i = 0; $i < $this->conditions; $i++) {
                $this->conditions[$i] = clone $this->conditions[$i];
            }
        }
    }

    public function get_evaluated(): MP_Node {
        return $this->evaluated;
    }

    public function get_value() {
        if (null === $this->evaluated) {
            throw new stack_exception('stack_ast_container: tried to get the value from of an unevaluated casstring.');
        }
        return $this->ast_to_string($this->evaluated);
    }
}

class stack_ast_container_conditional_latex_and_value extends stack_ast_container_silent
        implements cas_value_extractor, cas_latex_extractor {

    private $conditions;
    private $evaluated;
    private $latex;

    public function set_cas_evaluated_value(MP_Node $ast) {
        $this->evaluated = $ast;
    }

    public function set_conditions(array $conditions) {
        $this->conditions = $conditions;
    }

    public function get_valid(): bool {
        $valid = parent::get_valid();
        foreach ($this->conditions as $cond) {
            $valid = $valid && $cond->get_valid();
        }
        return $valid;
    }

    public function get_evaluationform(): string {
        if ($this->conditions === null || count($this->conditions) === 0) {
            return parent::get_evaluationform();
        }
        $content = parent::get_evaluationform();
        $conds = array();
        foreach ($this->conditions as $cond) {
            $conds[] = '(' . $cond->get_evaluationform() .')';
        }
        $r = 'if ' . implode(' and ', $conds) . ' then (' . $content . ') else ';
        if ($this->get_key() !== '') {
            $r .= $this->get_key() . ':false';
        } else {
            $r .= 'false';
        }
        return $r;
    }

    /**
     * Cloning is complex when we have object references.
     */
    public function __clone() {
        parent::__clone();
        if ($this->conditions !== null && count($this->conditions) > 0) {
            $i = 0;
            for ($i = 0; $i < $this->conditions; $i++) {
                $this->conditions[$i] = clone $this->conditions[$i];
            }
        }
    }

    public function get_evaluated(): MP_Node {
        return $this->evaluated;
    }

    public function get_value() {
        if (null === $this->evaluated) {
            throw new stack_exception('stack_ast_container: tried to get the value from of an unevaluated casstring.');
        }
        return $this->ast_to_string($this->evaluated);
    }

    public function set_cas_latex_value(string $latex) {
        $this->latex = stack_maxima_latex_tidy($latex);
    }

    public function get_display() {
        if (!$this->is_correctly_evaluated()) {
            throw new stack_exception('stack_ast_container: ' .
                    'tried to get the LaTeX representation from of an unevaluated or invalid casstring.');
        }
        return trim($this->latex);
    }
}


class stack_ast_container_conditional_silent extends stack_ast_container_silent {

    private $conditions;

    public function set_conditions(array $conditions) {
        $this->conditions = $conditions;
    }

    public function get_valid(): bool {
        $valid = parent::get_valid();
        foreach ($this->conditions as $cond) {
            $valid = $valid && $cond->get_valid();
        }
        return $valid;
    }

    public function get_evaluationform(): string {
        if ($this->conditions === null || count($this->conditions) === 0) {
            return parent::get_evaluationform();
        }
        $content = parent::get_evaluationform();
        $conds = array();
        foreach ($this->conditions as $cond) {
            $conds[] = '(' . $cond->get_evaluationform() .')';
        }
        $r = 'if ' . implode(' and ', $conds) . ' then (' . $content . ') else ';
        if ($this->get_key() !== '') {
            $r .= $this->get_key() . ':false';
        } else {
            $r .= 'false';
        }
        return $r;
    }

    /**
     * Cloning is complex when we have object references.
     */
    public function __clone() {
        parent::__clone();
        if ($this->conditions !== null && count($this->conditions) > 0) {
            $i = 0;
            for ($i = 0; $i < $this->conditions; $i++) {
                $this->conditions[$i] = clone $this->conditions[$i];
            }
        }
    }
}