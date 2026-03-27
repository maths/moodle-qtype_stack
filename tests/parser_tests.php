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
 * Tests for parsing.
 * @package    qtype_stack
 * @copyright  2026 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use qtype_stack_testcase;
use stack_parser_options;
use MP_Root;
use MP_Comment;
use MP_Statement;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/maximaparser/parser.options.class.php');
/**
 * Defines behaviour for parsing of certain special cases.
 * @group qtype_stack
 * @covers \qtype_stack_question
 */
final class parser_tests extends qtype_stack_testcase {

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function test_empty_values(): void {
        // Use parsing settings as close to keyval usage as possible.
        $po = stack_parser_options::get_cas_config();
        $po->dropcomments = true;

        $parserdropcomments = $po->get_parser();

        // True empty should not cause issues.
        $test1 = "";
        $lex1 = $po->get_lexer($test1);
        $parsed1 = $parserdropcomments->parse($lex1);
        $this->assertTrue($parsed1 instanceof MP_Root);
        $this->assertEquals(count($parsed1->items), 0);

        // Whitespace in the empty should not cause issues.
        $test2 = "   \n\n ";
        $lex2 = $po->get_lexer($test2);
        $parsed2 = $parserdropcomments->parse($lex2);
        $this->assertTrue($parsed2 instanceof MP_Root);
        $this->assertEquals(count($parsed2->items), 0);

        // Comments should not cause issues.
        $test3 = "/* A comment */";
        $lex3 = $po->get_lexer($test3);
        $parsed3 = $parserdropcomments->parse($lex3);
        $this->assertTrue($parsed3 instanceof MP_Root);
        $this->assertEquals(count($parsed3->items), 0);

        $test4 = " /" . "* A comment */ /" . "** Another one */";
        $lex4 = $po->get_lexer($test4);
        $parsed4 = $parserdropcomments->parse($lex4);
        $this->assertTrue($parsed4 instanceof MP_Root);
        $this->assertEquals(count($parsed4->items), 0);

        // Now if we start collecting those comments we need to also be able to collect them from empty.
        $po->dropcomments = false;
        $parserdontdropcomments = $po->get_parser();

        // Comments should not cause issues.
        $test5 = "/" . "* A comment */";
        $lex5 = $po->get_lexer($test5);
        $parsed5 = $parserdontdropcomments->parse($lex5);
        $this->assertTrue($parsed5 instanceof MP_Root);
        $this->assertEquals(count($parsed5->items), 1);
        $this->assertTrue($parsed5->items[0] instanceof MP_Comment);

        $test6 = " /" . "* A comment */ /" . "** Another one */";
        $lex6 = $po->get_lexer($test6);
        $parsed6 = $parserdontdropcomments->parse($lex6);
        $this->assertTrue($parsed6 instanceof MP_Root);
        $this->assertEquals(count($parsed6->items), 2);
        $this->assertTrue($parsed6->items[0] instanceof MP_Comment);
        $this->assertTrue($parsed6->items[1] instanceof MP_Comment);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function test_comment_collection(): void {
        // Use parsing settings as close to keyval usage as possible.
        $po = stack_parser_options::get_cas_config();
        $po->dropcomments = false;
        $parser = $po->get_parser();

        // When collecting comments, we need to be able to identify their
        // positions in relation to other things.
        // First top level-comments appear in between statements.
        $test1 = "foo:1;/" . "* a comment */\nbar:2;/" . "* another comment */";
        $lex1 = $po->get_lexer($test1);
        $parsed1 = $parser->parse($lex1);
        $this->assertTrue($parsed1 instanceof MP_Root);
        $this->assertEquals(count($parsed1->items), 4);
        $this->assertTrue($parsed1->items[1] instanceof MP_Comment);
        $this->assertTrue($parsed1->items[3] instanceof MP_Comment);
        $this->assertEquals($parsed1->items[1]->value, ' a comment ');
        $this->assertEquals($parsed1->items[3]->value, ' another comment ');

        // A comment may also exists inside a statement.
        $test2 = 'foo:1/' . '* was 1 */+2;bar:3;';
        $lex2 = $po->get_lexer($test2);
        $parsed2 = $parser->parse($lex2);
        $this->assertTrue($parsed2 instanceof MP_Root);
        $this->assertEquals(count($parsed2->items), 2);
        $this->assertTrue($parsed2->items[0] instanceof MP_Statement);
        $this->assertEquals(count($parsed2->items[0]->internalcomments), 1);
        $this->assertTrue($parsed2->items[0]->internalcomments[0] instanceof MP_Comment);
        $this->assertEquals($parsed2->items[0]->internalcomments[0]->value, ' was 1 ');
    }
}
