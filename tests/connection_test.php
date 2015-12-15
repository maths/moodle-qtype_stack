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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');


/**
 * Unit tests for {@link stack_cas_text}.
 * @group qtype_stack
 */
class stack_cas_connection_base_test extends qtype_stack_testcase {

    public function test_compute_true() {
        $connection = stack_connection_helper::make();
        $strin = 'cab:block([],print("[TimeStamp= [ 0 ], Locals= [ 0=[ error= ["), cte("p",errcatch(diff(x^n,x))), print("] ]"), return(true));';
        $return = $connection->compute($strin);
        $expected = array( 0 => array('key' => 'p', 'value' => 'n*x^(n-1)', 'display' => 'n\,x^{n-1}', 'error' => ''));
        $this->assertEquals($return, $expected);
    }

    public function test_compute_miss_formed_command() {
        $connection = stack_connection_helper::make();
        // This will induce a timeout on the CAS because we don't have a well formed CAS statement.
        $strin = 'cab:block([],print("[TimeStamp= [ 0 ];';
        $return = $connection->compute($strin);
        $expected = array();
        $this->assertEquals($return, $expected);
    }
}
