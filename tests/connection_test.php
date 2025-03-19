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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use qtype_stack_testcase;
use stack_connection_helper;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');


/**
 * Add description here.
 * @group qtype_stack
 * @covers \stack_connection_helper
 */
final class connection_test extends qtype_stack_testcase {

    public function test_compute_true(): void {

        $connection = stack_connection_helper::make();
        $strin = 'cab:block([],print("[STACKSTART Locals= [ 0=[ error= ["), cte("p",errcatch(diff(x^n,x))),'
                .' print("] ]"), return(true));';
        $return = $connection->compute($strin);
        $expected = [
            0 => [
                'key' => 'p', 'value' => 'n*x^(n-1)', 'dispvalue' => 'n*x^(n-1)', 'display' => 'n\,x^{n-1}',
                'error' => '',
            ],
        ];
        $this->assertEquals($expected, $return);
    }

    public function test_compute_dispvalue(): void {

        $connection = stack_connection_helper::make();
        $strin = 'cab:block([],print("[STACKSTART Locals= [ 0=[ error= ["), cte("p",errcatch(dispdp(1,3))),'
        .' print("] ]"), return(true));';
        $return = $connection->compute($strin);
        $expected = [
            0 => [
                'key' => 'p', 'value' => 'displaydp(1.0,3)', 'dispvalue' => '1.000',
                'display' => '1.000', 'error' => '',
            ],
        ];
        $this->assertEquals($expected, $return);
    }

    public function test_compute_dispvalue_units(): void {

        $connection = stack_connection_helper::make();
        $strin = 'cab:block([],print("[STACKSTART Locals= [ 0=[ error= ["), cte("p",errcatch(stackunits(dispsf(30,4),kg))),'
        .' print("] ]"), return(true));';
        $return = $connection->compute($strin);
        $expected = [
            0 => [
                'key' => 'p', 'value' => 'stackunits(displaydp(30,2),kg)',
                'dispvalue' => '30.00*kg', 'display' => '30.00\, {\it kg}', 'error' => '',
            ],
        ];
        $this->assertEquals($expected, $return);
    }

    /**
     * Note, with this test on SBCL the timeout can create a runaway process.
     */
    public function test_compute_miss_formed_command(): void {

        $connection = stack_connection_helper::make();
        // This will induce a timeout on the CAS because we don't have a well formed CAS statement.
        $strin = 'cab:block([],print("[STACKSTART ;';
        $return = $connection->compute($strin);
        $expected = [];
        $this->assertEquals($expected, $return);
    }
}
