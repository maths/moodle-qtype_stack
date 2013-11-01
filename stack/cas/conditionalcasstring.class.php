<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

/**
 * Conditionally evaluated CAS strings. i.e. eval me not when that would mean division by zero.
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('casstring.class.php');

class stack_cas_conditionalcasstring extends stack_cas_casstring{

    /** @var array of boolean valued CAS strings. */
    private $conditions;

    public function __construct($rawstring, $conditionstack) {
        parent::__construct($rawstring);
        $this->conditions = $conditionstack;
    }

    public function get_conditions() {
        return $this->conditions;
    }

}
