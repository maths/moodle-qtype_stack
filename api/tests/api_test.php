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

require_once("config.php");

use PHPUnit\Framework\TestCase;

require_once("api/api.php");

class stack_api_api_test extends TestCase {

    public function test_xml_import() {
        $questionxml = file_get_contents('samplequestions/test_1_basic_integral.xml');
        $api = new qtype_stack_api();
        $question = $api->initialise_question_from_xml($questionxml);

        $this->assertEquals($question->type, 'stack');
        $this->assertEquals($question->questionnote, '$\int @p@ d@v@ = @ta@$');
        $this->assertEquals($question->questiontext, '<p>Find \[ \int @p@ d@v@\] [[input:ans1]] [[validation:ans1]]</p>');
    }

}