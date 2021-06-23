<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext/autogen/castextparser.class.php');


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

    public function test_mathmode() {
        // Does it mark blocks that are inside math-mode?
        $raw = "\\[{@x@}\\] {@x@} \\({@x@}\\)";
        $parsed = $this->basic_parse_and_actions($raw);
        $this->assertEquals(true, $parsed['tree_form']->firstchild->nextsibling->get_mathmode());
        $this->assertEquals(false, $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->get_mathmode());
        $this->assertEquals(true,
                $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->nextsibling->nextsibling->get_mathmode());
    }

    public function test_mathmode_env() {
        // Does it mark blocks that are inside math-mode?
        $raw = "\\begin{blaah} \\[{@x@}\\] \\begin{equation*}{@x@}\\end{equation*} \\end{blaah}";
        $parsed = $this->basic_parse_and_actions($raw);
        $this->assertEquals($raw, $parsed['to_string']);
        $this->assertEquals(true, $parsed['tree_form']->firstchild->nextsibling->get_mathmode());
        $this->assertEquals("{@x@}", $parsed['tree_form']->firstchild->nextsibling->to_string());
        $this->assertEquals("{@x@}", $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->to_string());
        $this->assertEquals(true, $parsed['tree_form']->firstchild->nextsibling->nextsibling->nextsibling->get_mathmode());
    }

    public function test_quotes_and_attributes() {
        // Quotes does it handle them, in attributes?
        $raw = '[[ quotes a="a" '."b='b' c='\"c\"'".' ]]blaah[[/quotes]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string.
        $this->assertEquals('[[ quotes '."a=\"a\" b=\"b\" c='\"c\"'".' ]]blaah[[/ quotes ]]', $parsed['to_string']);
        // String check against node->to_string.
        $this->assertEquals('[[ quotes '."a=\"a\" b=\"b\" c='\"c\"'".' ]]blaah[[/ quotes ]]', $parsed['tree_form']->to_string());
    }

    public function test_fix_pseudoblocks_1() {
        // Special pseudoblocks 'else' and 'elif', does the tree transformation work?
        $raw = '[[ if test="a" ]]1[[ else ]][[ if test="c"]]2[[else]]3[[/if]][[/ if ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string. The missing spaces should appear in the closing 'if'.
        $this->assertEquals('[[ if test="a" ]]1[[ else ]][[ if test="c" ]]2[[else]]3[[/ if ]][[/ if ]]',
                $parsed['to_string']);
        // @codingStandardsIgnoreStart

        // String check against node->to_string. The conversion to the tree-form shoudl have rewriten the elses as new ifs.
        // Should generate about this: '[[ define stackparsecond1="a" stackparsecond2="not (stackparsecond1)" /]][[ if test="stackparsecond1" ]]1[[/ if ]][[ if test="stackparsecond2" ]][[ define stackparsecond3="c" stackparsecond4="not (stackparsecond3)" /]][[ if test="stackparsecond3" ]]2[[/ if ]][[ if test="stackparsecond4" ]]3[[/ if ]][[/ if ]]'
        // Problem is that the numbers in those stackparsecond?? variables can change depending on excecution order. So we do some
        // Logic checking. Could do a regexp but the amount of escapes...

        // @codingStandardsIgnoreEND

        $matches = array();
        preg_match_all('/stackparsecond([0-9]*)/' , $parsed['tree_form']->to_string() , $matches);
        $this->assertEquals($matches[1][0], $matches[1][2]); // The first cond needs to appear here.
        $this->assertEquals($matches[1][0], $matches[1][3]);
        $this->assertEquals($matches[1][1], $matches[1][4]);
        $this->assertEquals($matches[1][5], $matches[1][7]);
        $this->assertEquals($matches[1][6], $matches[1][9]);
        $this->assertEquals($matches[1][5], $matches[1][8]);

        // Test the same equalitys with the full text.
        $testpattern = '[[ define stackparsecond' . $matches[1][0] . '="a" stackparsecond' . $matches[1][1]
            . '="not (stackparsecond' . $matches[1][0] . ')" /]][[ if test="stackparsecond' . $matches[1][0]
            . '" ]]1[[/ if ]][[ if test="stackparsecond' . $matches[1][1] . '" ]][[ define stackparsecond'
            . $matches[1][5] . '="c" stackparsecond' . $matches[1][6] . '="not (stackparsecond'
            . $matches[1][5] . ')" /]][[ if test="stackparsecond' . $matches[1][5]
            . '" ]]2[[/ if ]][[ if test="stackparsecond' . $matches[1][6] . '" ]]3[[/ if ]][[/ if ]]';
        $this->assertEquals($testpattern, $parsed['tree_form']->to_string());
    }

    public function test_fix_pseudoblocks_2() {
        // Special pseudoblocks 'else' and 'elif', does the tree transformation work?
        $raw = '[[ if test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c"]]3[[else]]4[[/if]][[/ if ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string. The missing spaces should appear in the closing 'if'.
        $this->assertEquals('[[ if test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c" ]]3[[else]]4[[/ if ]][[/ if ]]',
                $parsed['to_string']);
        // @codingStandardsIgnoreStart

        // String check against node->to_string. The conversion to the tree-form shoudl have rewriten the elses as new ifs.
        // Should generate about this: '[[ define stackparsecond18="a" stackparsecond19="not (stackparsecond18) and (b)"
        // stackparsecond20="not (stackparsecond18 or stackparsecond19)" /]][[ if test="stackparsecond18" ]]1[[/ if ]][[ if test="stackparsecond19"
        // ]]2[[/ if ]][[ if test="stackparsecond20" ]][[ define stackparsecond21="c" stackparsecond22="not (stackparsecond21)"
        // /]][[ if test="stackparsecond21" ]]3[[/ if ]][[ if test="stackparsecond22" ]]4[[/ if ]][[/ if ]]'
        // Problem is that the numbers in those stackparsecond?? Variables can change depending on excecution order.
        // We do some logic checking. Could do a regexp but the amount of escapes...

        // @codingStandardsIgnoreEND

        $matches = array();
        preg_match_all('/stackparsecond([0-9]*)/' , $parsed['tree_form']->to_string() , $matches);
        $this->assertEquals($matches[1][0], $matches[1][2]); // The first cond needs to appear here.
        $this->assertEquals($matches[1][0], $matches[1][4]);
        $this->assertEquals($matches[1][0] + 1, $matches[1][1]); // The second needs to be stored to the next and so on.
        $this->assertEquals($matches[1][1], $matches[1][5]);
        $this->assertEquals($matches[1][1], $matches[1][7]);
        $this->assertEquals($matches[1][1] + 1, $matches[1][3]);
        $this->assertEquals($matches[1][3], $matches[1][8]);
        $this->assertEquals($matches[1][9], $matches[1][11]);
        $this->assertEquals($matches[1][9], $matches[1][12]);
        $this->assertEquals($matches[1][9] + 1, $matches[1][10]);
        $this->assertEquals($matches[1][10], $matches[1][13]);

        // Test the same equalitys with the full text.
        $testpattern = '[[ define stackparsecond' . $matches[1][0] . '="a" stackparsecond' . $matches[1][1]
                    . '="not (stackparsecond' . $matches[1][0] . ') and (b)" stackparsecond' . $matches[1][3]
                    . '="not (stackparsecond' . $matches[1][0] . ' or stackparsecond' . $matches[1][1] . ')" /]][[ if test="stackparsecond' . $matches[1][0]
                    . '" ]]1[[/ if ]][[ if test="stackparsecond' . $matches[1][1]
                    . '" ]]2[[/ if ]][[ if test="stackparsecond' . $matches[1][3]
                    . '" ]][[ define stackparsecond' . $matches[1][9] . '="c" stackparsecond' . $matches[1][10]
                    . '="not (stackparsecond' . $matches[1][9] . ')" /]][[ if test="stackparsecond' . $matches[1][9]
                    . '" ]]3[[/ if ]][[ if test="stackparsecond' . $matches[1][10] . '" ]]4[[/ if ]][[/ if ]]';
        $this->assertEquals($testpattern, $parsed['tree_form']->to_string());
    }

    public function test_fix_pseudoblocks_err() {
        // Special pseudoblocks 'else' and 'elif', does the tree transformation work? In odd case?
        $raw = '[[ fi test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c"]]3[[else]]4[[/if]][[/ fi ]]';
        $parsed = $this->basic_parse_and_actions($raw);
        // String check against parser->to_string. The missing spaces should appear in the closing 'if'.
        $this->assertEquals('[[ fi test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ if test="c" ]]3[[else]]4[[/ if ]][[/ fi ]]',
                $parsed['to_string']);
        // String check against node->to_string. The conversion to the tree-form shoudl have rewriten the elses as new ifs.
        // Similar problem with the dynamic numbering.

        $matches = array();
        preg_match_all('/stackparsecond([0-9]*)/' , $parsed['tree_form']->to_string() , $matches);
        $this->assertEquals($matches[1][0], $matches[1][2]); // The first cond needs to appear here.
        $this->assertEquals($matches[1][0], $matches[1][3]);
        $this->assertEquals($matches[1][0] + 1, $matches[1][1]); // The second needs to be stored to the next and so on.
        $this->assertEquals($matches[1][1], $matches[1][4]);

        $testpattern = '[[ fi test="a" ]]1[[ elif test="b" ]]2[[ else ]][[ define stackparsecond' . $matches[1][0]
                  . '="c" stackparsecond' . $matches[1][1]
                  . '="not (stackparsecond' . $matches[1][0] . ')" /]][[ if test="stackparsecond' . $matches[1][0]
                  . '" ]]3[[/ if ]][[ if test="stackparsecond' . $matches[1][1] . '" ]]4[[/ if ]][[/ fi ]]';
        $this->assertEquals($testpattern, $parsed['tree_form']->to_string());
    }

    public function test_line_endings() {
        // Do we break line endings? We do when exotic ones appear between parameters in block openings...
        $test_lines_pre = array('A', 'b ', ' c ');
        $block_open = '[[ jsxgraph ]]';
        $test_lines_content = array('d ', ' e', 'f ');
        $block_close = '[[/ jsxgraph ]]';
        $test_lines_post = array(' g', 'h ', ' i');

        // The simple endings where we assume PHP might do magic.
        $line_ends = array("\n", "\r\n", "\r" , "\n\r");

        // Add some raw ones and repeat previous ones just in case.
        $line_ends[] = chr(10);
        $line_ends[] = chr(13) . chr(10);
        $line_ends[] = chr(13);
        $line_ends[] = chr(10) . chr(13);
        $line_ends[] = chr(30);
        $line_ends[] = chr(21);
        $line_ends[] = chr(11);
        $line_ends[] = chr(12);

        // Some extras. But as we still support 5.something lets not break that.
        if (version_compare(phpversion(), '7.0.0', '>')) {
            $line_ends[] = "\u{0085}";
            $line_ends[] = "\u{2028}";
            $line_ends[] = "\u{2029}";
        }

        foreach ($line_ends as $ending) {
            $teststring = implode($ending, $test_lines_pre);
            $teststring .= $block_open;
            $teststring .= implode($ending, $test_lines_content);
            $teststring .= $block_close;
            $teststring .= implode($ending, $test_lines_post);
            $parsed = $this->basic_parse_and_actions($teststring);

            // Test reproduction fidelity.
            $this->assertEquals($teststring, $parsed['to_string']);

            // Test structure.
            $this->assertEquals(3, $parsed['counts']['text']);
            $this->assertEquals(1, $parsed['counts']['block']);

            $this->assertEquals('text', $parsed['tree_form']->firstchild->type);
            $this->assertEquals('block', $parsed['tree_form']->firstchild->nextsibling->type);
            $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->firstchild->type);
            $this->assertEquals('text', $parsed['tree_form']->firstchild->nextsibling->nextsibling->type);

            // Check text values.
            $this->assertEquals(implode($ending, $test_lines_pre), $parsed['tree_form']->firstchild->get_content());
            $this->assertEquals(implode($ending, $test_lines_content), $parsed['tree_form']->firstchild->nextsibling->firstchild->get_content());
            $this->assertEquals(implode($ending, $test_lines_post), $parsed['tree_form']->firstchild->nextsibling->nextsibling->get_content());
        }
    }

    public function test_spaces_12_3_parser() {

        $s = '12*3';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));
        $expected = '([Root] ([Op: *] ([Int] 12), ([Int] 3)))';

        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $s = '12 3';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([Op: *] ([Int] 12), ([Int] 3)))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));

        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array('spaces'));

        $s = '12 3.57';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));
        $expected = '([Root] ([Op: *] ([Int] 12), ([Float] 3.57)))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array('spaces'));

        $s = '1 2.3 4';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([Op: *] ([Int] 1), ([Op: *] ([Float] 2.3), ([Int] 4))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array('spaces'));

        $s = '1 2 3.4';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([Op: *] ([Int] 1), ([Op: *] ([Int] 2), ([Float] 3.4))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array('spaces'));
    }

    public function test_float_dot_float_parser() {

        $s = '0.1.0.2';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([Op: .] ([Float] 0.1), ([Float] 0.2)))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());
    }

    public function test_trig_parser() {

        $s = 'sin(x)';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([FunctionCall: ([Id] sin)] ([Id] x)))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $s = 'sin^2(x)';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([Op: *] ([Op: ^] ([Id] sin), ([Int] 2)), ([Group] ([Id] x))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($answernotes, array('missing_stars'));
        $s = 'sin^-2(x)';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Root', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Root] ([Op: *] ([Op: ^] ([Id] sin), ([PrefixOp: -] ([Int] 2))), ([Group] ([Id] x))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($answernotes, array('missing_stars'));
    }

    public function test_let() {
        $s = 'let x=1';
        $ast = null;
        $errors = array();
        $answernotes = array();
        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes,
            array('startRule' => 'Equivline', 'letToken' => stack_string('equiv_LET')));

        $expected = '([Let] ([Id] x),([Int] 1))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());
    }

    public function test_pm() {
        $s = 'a*b+c*d+-A*B';
        $ast = null;
        $errors = array();
        $answernotes = array();

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array('allowPM' => false));
        $expected = '([Root] ([Op: +] ([Op: *] ([Id] a), ([Id] b)), ([Op: +] ([Op: *] ([Id] c), ([Id] d)), ' .
            '([Op: *] ([PrefixOp: -] ([Id] A)), ([Id] B)))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals('a*b+c*d+ -A*B', $ast->toString(array('nosemicolon' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array());
        $this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([Op: +] ([Op: *] ([Id] a), ([Id] b)), ([Op: +-] ' .
                '([Op: *] ([Id] c), ([Id] d)), ([Op: *] ([Id] A), ([Id] B)))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $s = 'x = +-A*B';
        $ast = null;
        $errors = array();
        $answernotes = array();

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array('allowPM' => false));
        $this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([Op: =] ([Id] x), ([PrefixOp: +] ([Op: *] ([PrefixOp: -] ([Id] A)), ([Id] B)))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array());
        $this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([Op: =] ([Id] x), ([PrefixOp: +-] ([Op: *] ([Id] A), ([Id] B)))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());
    }

    public function test_not() {
        $s = 'not false';
        $ast = null;
        $errors = array();
        $answernotes = array();

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array());
        $this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([PrefixOp: not ] ([Bool] false)))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $s = 'not(false)';
        $ast = null;
        $errors = array();
        $answernotes = array();

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array());
        //$this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([PrefixOp: not ] ([Group] ([Bool] false))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $s = 'nounnot false';
        $ast = null;
        $errors = array();
        $answernotes = array();

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array());
        $this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([PrefixOp: nounnot ] ([Bool] false)))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());

        $s = 'nounnot(false)';
        $ast = null;
        $errors = array();
        $answernotes = array();

        $ast = maxima_corrective_parser::parse($s, $errors, $answernotes, array());
        //$this->assertEquals($s, $ast->toString(array('nosemicolon' => true)));
        $expected = '([Root] ([PrefixOp: nounnot ] ([Group] ([Bool] false))))';
        $this->assertEquals($expected, $ast->toString(array('flattree' => true)));
        $this->assertEquals($errors, array());
        $this->assertEquals($answernotes, array());
    }
}