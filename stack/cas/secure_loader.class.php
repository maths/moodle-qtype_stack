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

// Way to load in whatever CAS code one wants without having to deal
// with validation, intended to be used with caching of large logic blocks
// like PRTs as pre validated strings.
//
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/evaluatable_object.interfaces.php');


class stack_secure_loader implements cas_evaluatable {

    private $code;
    private $errors;
    private $context;

    public function __construct(string $securedcode, string $fromwhere='') {
        if ($securedcode === null) {
            throw new stack_exception('secure_loader: the code must not be null.');
        }
        if (trim($securedcode) === '') {
            throw new stack_exception('secure_loader: the code must be a non empty string.');
        }
        $this->context = $fromwhere;
        $this->code = $securedcode;
        $this->errors = array();
    }

    public function get_valid(): bool {
        // This code has been validated elsewhere.
        return true;
    }

    public function get_evaluationform(): string {
        return $this->code;
    }

    public function set_cas_status(array $errors, array $answernotes, array $feedback) {
        // Note that secure_loader content does not care about feedback or notes.
        $this->errors = $errors;
    }

    public function get_source_context(): string {
        return $this->context;
    }

    public function get_errors($raw = 'implode') {
        if ($raw === 'implode') {
            return implode(' ', array_unique($this->errors));
        }
        return $this->errors;
    }

    public function get_key(): string {
        return '';
    }
}

class stack_secure_loader_value extends stack_secure_loader implements cas_value_extractor {
    private $value;

    public function set_cas_evaluated_value(MP_Node $ast) {
        $this->value = $ast;
    }

    public function get_value() {
        return $this->value;
    }
}
