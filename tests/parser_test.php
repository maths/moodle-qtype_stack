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
require_once(__DIR__ . '/../stack/cas/castext/castextparser.class.php');


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
        $r = array();
        $r['raw_string'] = $string;
        $r['raw_parse_tree'] = $parser->match_castext();
        $r['usable_parse_tree'] = stack_cas_castext_castextparser::normalize($r['raw_parse_tree']);
        $r['usable_parse_tree'] = stack_cas_castext_castextparser::block_conversion($r['usable_parse_tree']);
        $r['tree_form'] = stack_cas_castext_parsetreenode::build_from_nested($r['usable_parse_tree']);
        $r['to_string'] = stack_cas_castext_castextparser::to_string($r['usable_parse_tree']);
        $r['counts'] = $this->count_nodes($r['usable_parse_tree']);
        return $r;
    }

    /**
     * Counts the nodes in the parse_tree. Groups by type.
     */
    private function count_nodes($parsetree) {
        $r = array();
        switch ($parsetree['_matchrule']) {
            case "castext":
            case "block":
                if (array_key_exists('_matchrule', $parsetree['item'])) {
                    foreach ($this->count_nodes($parsetree['item']) as $key => $value) {
                        if (array_key_exists($key, $r)) {
                            $r[$key] = $r[$key] + $value;
                        } else {
                            $r[$key] = $value;
                        }
                    }
                } else {
                    foreach ($parsetree['item'] as $subtree) {
                        foreach ($this->count_nodes($subtree) as $key => $value) {
                            if (array_key_exists($key, $r)) {
                                $r[$key] = $r[$key] + $value;
                            } else {
                                $r[$key] = $value;
                            }
                        }
                    }
                }
                break;
        }
        if (array_key_exists($parsetree['_matchrule'], $r)) {
            $r[$parsetree['_matchrule']] = $r[$parsetree['_matchrule']] + 1;
        } else {
            $r[$parsetree['_matchrule']] = 1;
        }
        return $r;
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_rawcasblock() {
        $raw = 'Test string with maxima-code block {#sin(x/y)#}';
        $parsed = $this->basic_parse_and_actions($raw);
        // As this tree has no ambiguous quotes or whitespace in blocks the to_string function should produce the same string.
        $this->assertEquals($raw, $parsed['to_string']);
        // The tree should contain these nodes.
        $this->assertEquals(1, $parsed['counts']['text']);
        $this->assertEquals(1, $parsed['counts']['rawcasblock']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The contents of the cas-block should be here.
        $this->assertEquals("sin(x/y)", $parsed['usable_parse_tree']['item'][1]['cascontent']['text']);
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_texcasblock() {
        $raw = 'Test string with tex-code block {@sin(x/y)@}';
        $parsed = $this->basic_parse_and_actions($raw);
        // As this tree has no ambiguous quotes or whitespace in blocks the to_string function should produce the same string.
        $this->assertEquals($raw, $parsed['to_string']);
        // The tree should contain these nodes.
        $this->assertEquals(1, $parsed['counts']['text']);
        $this->assertEquals(1, $parsed['counts']['texcasblock']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The contents of the cas-block should be here.
        $this->assertEquals("sin(x/y)", $parsed['usable_parse_tree']['item'][1]['cascontent']['text']);
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_multi_casblock() {
        $raw = 'Test string with casblock {@sin(x/y)@} and another {#cos(x/y)#}';
        $parsed = $this->basic_parse_and_actions($raw);
        // As this tree has no ambiguous quotes or whitespace in blocks the to_string function should produce the same string.
        $this->assertEquals($raw, $parsed['to_string']);
        // The tree should contain these nodes.
        $this->assertEquals(2, $parsed['counts']['text']);
        $this->assertEquals(1, $parsed['counts']['texcasblock']);
        $this->assertEquals(1, $parsed['counts']['rawcasblock']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The contents of the cas-block should be here.
        $this->assertEquals("sin(x/y)", $parsed['usable_parse_tree']['item'][1]['cascontent']['text']);
        $this->assertEquals("cos(x/y)", $parsed['usable_parse_tree']['item'][3]['cascontent']['text']);
        // Check the random text node.
        $this->assertEquals(" and another ", $parsed['usable_parse_tree']['item'][2]['text']);
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_block_single() {
        $raw = 'Test string with an block [[ block ]] test content [[/block]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes.
        $this->assertEquals(2, $parsed['counts']['text']);
        $this->assertEquals(1, $parsed['counts']['block']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The block has a name.
        $this->assertEquals("block", $parsed['usable_parse_tree']['item'][1]['name']);
        // And the content text is.
        $this->assertEquals(" test content ", $parsed['usable_parse_tree']['item'][1]['item'][0]['text']);
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_block_multi() {
        $raw = 'Test string with an block [[ block ]] test content [[/block]][[ if test="false" ]][[/ if ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes.
        $this->assertEquals(2, $parsed['counts']['text']);
        $this->assertEquals(2, $parsed['counts']['block']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The blocks have names, note that the indeitifiers for the blocks in the 'item'-array are a bit confusing subject.
        $this->assertEquals("block", $parsed['usable_parse_tree']['item'][1]['name']);
        $this->assertEquals("if", $parsed['usable_parse_tree']['item'][4]['name']);
        // And the content text is.
        $this->assertEquals(" test content ", $parsed['usable_parse_tree']['item'][1]['item'][0]['text']);
        // A parameter is present.
        $this->assertEquals("false", $parsed['usable_parse_tree']['item'][4]['params']['test']);
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_block_nested() {
        $raw = 'Test string with an block [[ block ]] test content [[ if test="false" ]][[/ if ]][[/block]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes.
        $this->assertEquals(2, $parsed['counts']['text']);
        $this->assertEquals(2, $parsed['counts']['block']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The blocks have names, note that the indeitifiers for the blocks in the 'item'-array are a bit confusing subject.
        $this->assertEquals("block", $parsed['usable_parse_tree']['item'][1]['name']);
        $this->assertEquals("if", $parsed['usable_parse_tree']['item'][1]['item'][1]['name']);
        // And the content text is.
        $this->assertEquals(" test content ", $parsed['usable_parse_tree']['item'][1]['item'][0]['text']);
        // A parameter is present.
        $this->assertEquals("false", $parsed['usable_parse_tree']['item'][1]['item'][1]['params']['test']);
    }

    /**
     * Internal functionality testing. Actual use through the tree-form.
     */
    public function test_block_invalid_nested() {
        // The post-processor block_conversion() should ignore those overlapping ones but spot that empty one.
        // The ignored ones will be left as nodes in the parse_tree but in the tree_form they will be joined to text-nodes.
        $raw = 'Test string with invalid blocks [[ block ]] [[ empty /]] test content [[ if test="false" ]][[/ block ]][[/ if ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // The tree should contain these nodes.
        $this->assertEquals(3, $parsed['counts']['text']);
        $this->assertEquals(1, $parsed['counts']['block']);
        $this->assertEquals(2, $parsed['counts']['blockopen']);
        $this->assertEquals(2, $parsed['counts']['blockclose']);
        $this->assertEquals(1, $parsed['counts']['castext']);
        // The blocks have names, note that the indeitifiers for the blocks in the 'item'-array are a bit confusing subject.
        $this->assertEquals("empty", $parsed['usable_parse_tree']['item'][3]['name']);
        // Check the tree form.
        $this->assertEquals('text', $parsed['tree_form']->firstchild->type);
        $this->assertEquals('block', $parsed['tree_form']->firstchild->nextsibling->type);
        $this->assertEquals('empty', $parsed['tree_form']->firstchild->nextsibling->get_content());
        $this->assertEquals('Test string with invalid blocks [[ block ]] ', $parsed['tree_form']->firstchild->get_content());
        $this->assertEquals(' test content [[ if test="false" ]][[/ block ]][[/ if ]]',
                $parsed['tree_form']->firstchild->nextsibling->nextsibling->get_content());
    }

    /**
     * Actual functionality tests. For maxima evaluation related tests check ../../tests/castext..
     */
    public function test_conversion_to_text() {
        $raw = 'Test string {#sin(x)#} is {@sin(x)@}';
        $parsed = $this->basic_parse_and_actions($raw);
        // Type check.
        $this->assertEquals('text', $parsed['tree_form']->firstchild->type);
        $this->assertEquals('rawcasblock', $parsed['tree_form']->firstchild->nextsibling->type);
        $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->nextsibling->type);
        $this->assertEquals('texcasblock', $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->type);
        // Null operation.
        $this->assertEquals($raw, $parsed['tree_form']->to_string());
        // Lets mod the tree a bit.
        $parsed['tree_form']->firstchild->nextsibling->convert_to_text("sin(x)");
        $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->convert_to_text("{\sin(x)}");
        // Check types.
        $this->assertEquals('text', $parsed['tree_form']->firstchild->type);
        $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->type);
        $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->nextsibling->type);
        $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->type);
        // There should not be more nodes.
        $this->assertEquals(null, $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->nextsibling);
        // After normalize the text nodes should join together.
        $parsed['tree_form']->normalize();
        $this->assertEquals(null, $parsed['tree_form']->firstchild->nextsibling);
        // Then check the output again.
        $this->assertEquals('Test string sin(x) is {\sin(x)}', $parsed['tree_form']->to_string());
    }

    public function test_node_destruction() {
        $raw = 'Test string [[ block ]] {@1/1@} [[ block/]] [[/ block ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // Type check.
        $this->assertEquals('text', $parsed['tree_form']->firstchild->type);
        $this->assertEquals('block', $parsed['tree_form']->firstchild->nextsibling->type);
        $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->firstchild->type);
        $this->assertEquals('texcasblock', $parsed['tree_form']->firstchild->nextsibling->firstchild->nextsibling->type);
        $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->firstchild->nextsibling->nextsibling->type);
        $this->assertEquals('block',
                $parsed['tree_form']->firstchild->nextsibling->firstchild->nextsibling->nextsibling->nextsibling->type);
        // Destroy inner block.
        $parsed['tree_form']->firstchild->nextsibling->firstchild->nextsibling->nextsibling->nextsibling->destroy_node();
        // Check text.
        $this->assertEquals('Test string [[ block ]] {@1/1@}  [[/ block ]]', $parsed['tree_form']->to_string());
        // Destroy outer block but elevate contents.
        $parsed['tree_form']->firstchild->nextsibling->destroy_node_promote_children();
        // Check text.
        $this->assertEquals('Test string  {@1/1@}  ', $parsed['tree_form']->to_string());
    }

    public function test_block_as_first_element() {
        $raw = '[[ if test="false"]]blaah[[/if]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // Type check.
        $this->assertEquals('block', $parsed['tree_form']->firstchild->type);
    }

    /**
     * Does it mark blocks that are inside math-mode?
     */
    public function test_mathmode() {
        $raw = "\\[{@x@}\\] {@x@} \\({@x@}\\)";
        $parsed = $this->basic_parse_and_actions($raw);
        $this->assertEquals(true, $parsed['tree_form']->firstchild->nextsibling->get_mathmode());
        $this->assertEquals(false, $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->get_mathmode());
        $this->assertEquals(true,
                $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->nextsibling->nextsibling->get_mathmode());
    }

    /**
     * Does it mark blocks that are inside math-mode?
     */
    public function test_mathmode_env() {
        $raw = "\\begin{blaah} \\[{@x@}\\] \\begin{equation*}{@x@}\\end{equation*} \\end{blaah}";
        $parsed = $this->basic_parse_and_actions($raw);
        $this->assertEquals($raw, $parsed['to_string']);
        $this->assertEquals(true, $parsed['tree_form']->firstchild->nextsibling->get_mathmode());
        $this->assertEquals("{@x@}", $parsed['tree_form']->firstchild->nextsibling->to_string());
        $this->assertEquals("{@x@}", $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->to_string());
        $this->assertEquals(true, $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->get_mathmode());
    }

    /**
     * Quotes does it handle them, in attributes?
     */
    public function test_quotes_and_attributes() {
        $raw = '[[ quotes a="a" '."b='b' c='\"c\"'".' ]]blaah[[/quotes]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string.
        $this->assertEquals('[[ quotes '."a=\"a\" b=\"b\" c='\"c\"'".' ]]blaah[[/ quotes ]]', $parsed['to_string']);
        // String check against node->to_string.
        $this->assertEquals('[[ quotes '."a=\"a\" b=\"b\" c='\"c\"'".' ]]blaah[[/ quotes ]]', $parsed['tree_form']->to_string());
    }

    /**
     * Special pseudoblocks 'else' and 'elif', does the tree transformation work?
     */
    public function test_fix_pseudoblocks() {
        $raw = '[[ if test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c"]]3[[else]]4[[/if]][[/ if ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string. The missing spaces should appear in the closing 'if'.
        $this->assertEquals('[[ if test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c" ]]3[[else]]4[[/ if ]][[/ if ]]',
                $parsed['to_string']);
        // String check against node->to_string. The conversion to the tree-form shoudl have rewriten the elses as new ifs.
        $this->assertEquals('[[ if test="a" ]]1[[/ if ]][[ if test="(not (a)) and (b)" ]]2[[/ if ]][[ if test="(not (a)) and '.
                '(not (b))" ]][[ if test="c" ]]3[[/ if ]][[ if test="(not (c))" ]]4[[/ if ]][[/ if ]]',
                $parsed['tree_form']->to_string());
    }

    /**
     * Special pseudoblocks 'else' and 'elif', does the tree transformation work? In odd case?
     */
    public function test_fix_pseudoblocks_err() {
        $raw = '[[ fi test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c"]]3[[else]]4[[/if]][[/ fi ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string. The missing spaces should appear in the closing 'if'.
        $this->assertEquals('[[ fi test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c" ]]3[[else]]4[[/ if ]][[/ fi ]]',
                $parsed['to_string']);
        // String check against node->to_string. The conversion to the tree-form shoudl have rewriten the elses as new ifs.
        $this->assertEquals('[[ fi test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c" ]]3[[/ if ]][[ if'.
                ' test="(not (c))" ]]4[[/ if ]][[/ fi ]]', $parsed['tree_form']->to_string());
    }



}
