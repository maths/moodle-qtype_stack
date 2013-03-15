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

require_once(dirname(__FILE__) . '/../../../../locallib.php');
require_once(dirname(__FILE__) . '/../../../../tests/test_base.php');
require_once(dirname(__FILE__) . '/../castextparser.class.php');


/**
 * Unit tests for {@link stack_cas_castext_castextparser}.
 * @group qtype_stack
 */
class stack_cas_castext_parser_test extends qtype_stack_testcase {

    /**
     * Does some common actions related to the parser as well as counts some values that can be used to check the result.
     */
    public function basic_parse_and_actions($string) {
        $parser = new stack_cas_castext_castextparser($string);
        $R = array();
        $R['raw_string'] = $string;
        $R['raw_parse_tree'] = $parser->match_castext();
        $R['usable_parse_tree'] = stack_cas_castext_castextparser::normalize($R['raw_parse_tree']);
        $R['usable_parse_tree'] = stack_cas_castext_castextparser::block_conversion($R['usable_parse_tree']);
        $R['usable_parse_tree'] = stack_cas_castext_castextparser::tag($R['usable_parse_tree']);
        $R['to_string'] = stack_cas_castext_castextparser::to_string($R['usable_parse_tree']);
        $R['counts'] = $this->count_nodes($R['usable_parse_tree']);
        return $R;
    }

    /**
     * Counts the nodes in the parse_tree. Groups by type.
     */
    private function count_nodes($parse_tree) {
        $R = array();
        switch ($parse_tree['_matchrule']) {
            case "castext":
            case "block":
                if (array_key_exists('_matchrule',$parse_tree['item'])) {
                    foreach ($this->count_nodes($parse_tree['item']) as $key => $value) {
                        if (array_key_exists($key,$R)) {
                            $R[$key] = $R[$key] + $value;
                        } else {
                            $R[$key] = $value;
                        }
                    }
                } else {
                    foreach ($parse_tree['item'] as $sub_tree) {
                        foreach ($this->count_nodes($sub_tree) as $key => $value) {
                            if (array_key_exists($key,$R)) {
                                $R[$key] = $R[$key] + $value;
                            } else {
                                $R[$key] = $value;
                            }
                        }
                    }
                }
                break;
        }
        if (array_key_exists($parse_tree['_matchrule'],$R)) {
            $R[$parse_tree['_matchrule']] = $R[$parse_tree['_matchrule']] +1;
        } else {
            $R[$parse_tree['_matchrule']] = 1;
        }
        return $R;
    }


    public function test_rawcasblock() {
        $raw = 'Test string with maxima-code block {#sin(x/y)#}';
        $parsed = $this->basic_parse_and_actions($raw);
        // As this tree has no ambiguous quotes or whitespace in blocks the to_string function should produce the same string
        $this->assertEquals($raw, $parsed['to_string']);
        // The tree should contain these nodes
        $this->assertEquals(1,$parsed['counts']['text']);
        $this->assertEquals(1,$parsed['counts']['rawcasblock']);
        $this->assertEquals(1,$parsed['counts']['castext']);
        // The contents of the cas-block should be here
        $this->assertEquals("sin(x/y)",$parsed['usable_parse_tree']['item'][1]['cascontent']['text']);
    }

    public function test_texcasblock() {
        $raw = 'Test string with tex-code block {@sin(x/y)@}';
        $parsed = $this->basic_parse_and_actions($raw);
        // As this tree has no ambiguous quotes or whitespace in blocks the to_string function should produce the same string
        $this->assertEquals($raw, $parsed['to_string']);
        // The tree should contain these nodes
        $this->assertEquals(1,$parsed['counts']['text']);
        $this->assertEquals(1,$parsed['counts']['texcasblock']);
        $this->assertEquals(1,$parsed['counts']['castext']);
        // The contents of the cas-block should be here
        $this->assertEquals("sin(x/y)",$parsed['usable_parse_tree']['item'][1]['cascontent']['text']);
    }

    public function test_multi_casblock() {
        $raw = 'Test string with casblock {@sin(x/y)@} and another {#cos(x/y)#}';
        $parsed = $this->basic_parse_and_actions($raw);
        // As this tree has no ambiguous quotes or whitespace in blocks the to_string function should produce the same string
        $this->assertEquals($raw, $parsed['to_string']);
        // The tree should contain these nodes
        $this->assertEquals(2,$parsed['counts']['text']);
        $this->assertEquals(1,$parsed['counts']['texcasblock']);
        $this->assertEquals(1,$parsed['counts']['rawcasblock']);
        $this->assertEquals(1,$parsed['counts']['castext']);
        // The contents of the cas-block should be here
        $this->assertEquals("sin(x/y)",$parsed['usable_parse_tree']['item'][1]['cascontent']['text']);
        $this->assertEquals("cos(x/y)",$parsed['usable_parse_tree']['item'][3]['cascontent']['text']);
        // Check the random text node
        $this->assertEquals(" and another ",$parsed['usable_parse_tree']['item'][2]['text']);
    }

    public function test_block_single() {
        $raw = 'Test string with an block [[ block ]] test content [[/block]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes
        $this->assertEquals(2,$parsed['counts']['text']);
        $this->assertEquals(1,$parsed['counts']['block']);
        $this->assertEquals(1,$parsed['counts']['castext']);
        // The block has a name
        $this->assertEquals("block",$parsed['usable_parse_tree']['item'][1]['name']);
        // And the content text is
        $this->assertEquals(" test content ",$parsed['usable_parse_tree']['item'][1]['item'][0]['text']);
    }

    public function test_block_multi() {
        $raw = 'Test string with an block [[ block ]] test content [[/block]][[ if test="false" ]][[/ if ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes
        $this->assertEquals(2,$parsed['counts']['text']);
        $this->assertEquals(2,$parsed['counts']['block']);
        $this->assertEquals(1,$parsed['counts']['castext']);
        // The blocks have names, note that the indeitifiers for the blocks in the 'item'-array are a bit confusing subject
        $this->assertEquals("block",$parsed['usable_parse_tree']['item'][1]['name']);
        $this->assertEquals("if",$parsed['usable_parse_tree']['item'][4]['name']);
        // And the content text is
        $this->assertEquals(" test content ",$parsed['usable_parse_tree']['item'][1]['item'][0]['text']);
        // A parameter is present
        $this->assertEquals("false",$parsed['usable_parse_tree']['item'][4]['params']['test']);
    }

    public function test_block_nested() {
        $raw = 'Test string with an block [[ block ]] test content [[ if test="false" ]][[/ if ]][[/block]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes
        $this->assertEquals(2,$parsed['counts']['text']);
        $this->assertEquals(2,$parsed['counts']['block']);
        $this->assertEquals(1,$parsed['counts']['castext']);
        // The blocks have names, note that the indeitifiers for the blocks in the 'item'-array are a bit confusing subject
        $this->assertEquals("block",$parsed['usable_parse_tree']['item'][1]['name']);
        $this->assertEquals("if",$parsed['usable_parse_tree']['item'][1]['item'][1]['name']);
        // And the content text is
        $this->assertEquals(" test content ",$parsed['usable_parse_tree']['item'][1]['item'][0]['text']);
        // A parameter is present
        $this->assertEquals("false",$parsed['usable_parse_tree']['item'][1]['item'][1]['params']['test']);
    }




}
