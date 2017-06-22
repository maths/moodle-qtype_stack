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

/* CJS notes:
 * 
 * https://phpunit.de/getting-started.html
 * 
 */

use PHPUnit\Framework\TestCase;

require_once("../config.php");

final class stack_apilib_html_test extends TestCase {

    public function stack_string() {
        $this->assertEquals(stack_string('debuginfo'), 'Debug info2');
    }

    public function html_writer_1() {
        $w = new html_writer;
        echo $w->tag('p', 'This is a paragraph');
    }
}
