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

use PHPUnit\Framework\TestCase;

require_once("config.php");
require_once("api/apilib.php");


class stack_api_apilib_test extends TestCase {

    public function test_stack_string() {
        $this->assertEquals(stack_string('debuginfo'), 'Debug info');
    }

    public function test_html_writer() {
        $w = new html_writer;
        $t = $w->tag('p', 'This is a paragraph');
        $this->assertEquals($t, '<p>This is a paragraph</p>');
    }
}
