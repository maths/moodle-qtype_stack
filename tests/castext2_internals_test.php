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

namespace qtype_stack;

use CTP_Block;
use CTP_IOBlock;
use CTP_Parser;
use CTP_Raw;
use CTP_Root;
use CTP_String;
use castext2_parser_utils;
use qtype_stack_testcase;

defined('MOODLE_INTERNAL') || die();

// These tests do not declare castext2 requirements they just test
// the implementation. Do not port these over to castext3.
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/utils.php');

/**
 * This set of tests tests some internal logic.
 *
 * @group qtype_stack
 * @group qtype_stack_castext_module
 */
class castext2_internals_test extends qtype_stack_testcase {

    /**
     * @covers \qtype_stack\CTP_Parser
     * @covers \qtype_stack\CTP_Parser::parse
     */
    public function test_parser() {
        $parser = new CTP_Parser();
        $code   = '[[ if test="0"]] {#1#} {@2@}[[/if]]';
        $ast    = $parser->parse($code);

        // Parsers will alway wrap the contents into a Root-object.
        // Even if we have only one thing in it.
        $this->assertTrue($ast instanceof CTP_Root);
        $this->assertEquals(1, count($ast->items));
        $this->assertTrue($ast->items[0] instanceof CTP_Block);

        $block = $ast->items[0];
        $this->assertEquals(1, count($block->parameters));

        // Parameters are often just strings as one can use either quotes
        // and the escapes are handled by the string object.
        $this->assertTrue($block->parameters['test'] instanceof CTP_String);
        $this->assertEquals('0', $block->parameters['test']->value);
        $this->assertEquals('if', $block->name);

        // Block contents is just a list of nodes.
        $this->assertEquals(4, count($block->contents));
        $this->assertTrue($block->contents[0] instanceof CTP_Raw);
        $this->assertEquals(' ', $block->contents[0]->value);

        $this->assertTrue($block->contents[1] instanceof CTP_Block);
        $this->assertEquals('raw', $block->contents[1]->name);
        $this->assertEquals('1', $block->contents[1]->contents[0]->value);

        $this->assertTrue($block->contents[2] instanceof CTP_Raw);
        $this->assertEquals(' ', $block->contents[2]->value);

        $this->assertTrue($block->contents[3] instanceof CTP_Block);
        $this->assertEquals('latex', $block->contents[3]->name);
        $this->assertEquals('2', $block->contents[3]->contents[0]->value);
    }

    /**
     * @covers \qtype_stack\CTP_Parser
     * @covers \qtype_stack\CTP_Parser::parse
     * @covers \qtype_stack\CTP_IOBlock
     */
    public function test_ioblockextensions() {
        $parser = new CTP_Parser();
        $code   = '[[list_errors:ans1,ans2]][[ whatever : ans3 ]]';
        $ast    = $parser->parse($code);

        $this->assertTrue($ast instanceof CTP_Root);
        $this->assertEquals(2, count($ast->items));
        $this->assertTrue($ast->items[0] instanceof CTP_IOBlock);
        $this->assertTrue($ast->items[1] instanceof CTP_IOBlock);

        $this->assertEquals('list_errors', $ast->items[0]->channel);
        $this->assertEquals('whatever', $ast->items[1]->channel);
        $this->assertEquals('ans1,ans2', $ast->items[0]->variable);
        $this->assertEquals('ans3', $ast->items[1]->variable);
    }

    /**
     * @covers \qtype_stack\castext2_parser_utils::math_paint
     */
    public function test_math_paint_1() {
        $parser = new CTP_Parser();
        $code   = '\({#1#}\) {@3@} \[{@5@}\] \begin{equation}{@7@} \end{equation} {#9#}';
        $ast    = $parser->parse($code);

        foreach ($ast->items as $item) {
            $this->assertFalse($item->mathmode);
        }

        $ast = castext2_parser_utils::math_paint($ast, $code, FORMAT_HTML);

        $this->assertTrue($ast->items[1]->mathmode);
        $this->assertFalse($ast->items[3]->mathmode);
        $this->assertTrue($ast->items[5]->mathmode);
        $this->assertTrue($ast->items[7]->mathmode);
        $this->assertFalse($ast->items[9]->mathmode);
    }

    /**
     * @covers \qtype_stack\castext2_parser_utils::math_paint
     */
    public function test_math_paint_2() {
        $parser = new CTP_Parser();
        $code   = '<p>[[commonstring key="your_answer_interpreted_as"/]]</p>';
        $code  .= '[[if test="stringp(ans1)"]]<p style="text-align:center">{@false@}</p>';
        $code  .= '[[else]]\[{@true@}\][[/if]]';
        $ast    = $parser->parse($code);

        $check  = function($node) {
            if ($node instanceof CTP_Block) {
                if ($node->name === 'raw' || $node->name === 'latex') {
                    if ($node->contents[0]->value === 'true') {
                        $this->assertTrue($node->mathmode);
                    } else {
                        $this->assertFalse($node->mathmode);
                    }
                }
            }
            return true;
        };

        $ast = castext2_parser_utils::math_paint($ast, $code, FORMAT_HTML);
        $ast->callbackRecurse($check);
    }

}
