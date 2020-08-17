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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/mathsoutput/mathsoutput.class.php');
require_once(__DIR__ . '/../doc/docslib.php');

// Unit tests for the Moodle TeX filter maths output class.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_maths_tex_test extends advanced_testcase {

    public function test_tex_rendering() {
        $this->resetAfterTest();
        set_config('mathsdisplay', 'tex', 'qtype_stack');
        stack_utils::clear_config_cache();
        filter_set_global_state('mathjaxloader', TEXTFILTER_DISABLED);

        // Test language string.
        // The <span class="MathJax_Preview"> bit is something that got added in
        // Moodle 2.8, so match it optionally.
        $this->assertRegExp('~^Your answer needs to be a single fraction of the form ' .
                '(<span class="MathJax_Preview">)?<a .*alt=" \{a\}\\\\over\{b\} ".*</(a|script)> \. ~',
                stack_string('ATSingleFrac_part'));

        // Test docs - make sure maths inside <code> is not rendered.
        $this->assertRegExp('~^<p><code>\\\\\(x\^2\\\\\)</code> gives (<span class="MathJax_Preview">)?'
                .'<a .*alt="x\^2".*</(a|script)> \.</p>\n$~',
                stack_docs_render_markdown('<code>\(x^2\)</code> gives \(x^2\).'));

        // Test docs - make sure maths inside <textarea> is not rendered, and <textarea> is retained.
        $expectation = '<p><textarea readonly="readonly" rows="3" cols="50">' . "\n" .
            'Differentiate \[x^2 + y^2\] with respect to \(x\).</textarea></p>' . "\n";
        $this->assertEquals($expectation,
                stack_docs_render_markdown('<textarea readonly="readonly" rows="3" cols="50">' . "\n" .
                        'Differentiate \[x^2 + y^2\] with respect to \(x\).</textarea>'));

        // Test CAS text with inline maths.
        $this->assertEquals('What is \[x^2\]?', stack_maths::process_display_castext('What is \(x^2\)?'));

        // Test CAS text with display maths.
        $this->assertEquals('What is <span class="displayequation">\[\displaystyle x^2\]</span>?',
                stack_maths::process_display_castext('What is \[x^2\]?'));

        // Test with replacedollars.
        set_config('replacedollars', '1', 'qtype_stack');
        stack_utils::clear_config_cache();
        $this->assertEquals('What is \[x^2\] or <span class="displayequation">\[\displaystyle x^2\]</span>?',
                stack_maths::process_display_castext('What is $x^2$ or $$x^2$$?'));

        stack_utils::clear_config_cache();
    }
}
