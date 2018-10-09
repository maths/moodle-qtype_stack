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
require_once(__DIR__ . '/insertstars0.class.php');

class stack_parser_logic_insertstars1 extends stack_parser_logic_insertstars0 {

    public function __construct($insertstars = true, $fixspaces = false) {
        // Stars but not spaces.
        parent::__construct($insertstars, $fixspaces);
        $this->insertstars = $insertstars;
        $this->fixspaces = $fixspaces;
    }
}