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

use qtype_stack_testcase;

defined('MOODLE_INTERNAL') || die();

// Unit tests for the documentation library functions.
//
// @copyright 2012 The Open University.
// @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../doc/docslib.php');
require_once(__DIR__ . '/fixtures/test_base.php');

/**
 * @group qtype_stack
 * @covers \qtype_stack
 */
class docslib_test extends qtype_stack_testcase {

    public function test_stack_docs_title_from_filename() {
        $this->assertEquals('About', stack_docs_title_from_filename('About'));
        $this->assertEquals('Some folder', stack_docs_title_from_filename('Some_folder'));
        $this->assertEquals('Documentation', stack_docs_title_from_filename('Documentation.md'));
        $this->assertEquals('Future plans', stack_docs_title_from_filename('Future_plans.md'));
    }

    public function test_stack_docs_index() {
        global $CFG;

        $this->assertEquals(str_replace('WWWROOT', $CFG->wwwroot, '<ul class="dir">' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/API.md">API</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/LTI.md">' .
                        'LTI</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Mathjax.md">' .
                        'Mathjax</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Maxima_installation.md">Maxima installation</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Migration.md">Migration</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Optimising_Maxima.md">' .
                        'Optimising Maxima</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Release_notes_4_4_x.md">' .
                        'Release notes 4 4 x</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/STACK_versions.md">STACK versions</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Testing_installation.md">' .
                        'Testing installation</a></li></ul>'),
                stack_docs_index($CFG->dirroot . '/question/type/stack/doc/en/Installation',
                $CFG->wwwroot . '/question/type/stack/doc.php/Installation'));

        $this->assertEquals(str_replace('WWWROOT', $CFG->wwwroot, '<ul class="dir">' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/Accessibility.md">Accessibility</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/Answer_assessment.md">Answer assessment</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/Answer_input.md">Answer input</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/'.
                    'Equivalence_reasoning.md">Equivalence reasoning</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/FAQ.md">FAQ</a></li></ul>'),
                stack_docs_index($CFG->dirroot . '/question/type/stack/doc/en/Students',
                $CFG->wwwroot . '/question/type/stack/doc.php/Students'));
    }

    public function test_stack_docs_render_markdown() {
        global $CFG;
        require_once($CFG->libdir . '/environmentlib.php');
        $currentversion = normalize_version(get_config('', 'release'));

        $this->assertEquals("<p>Test</p>\n",
                stack_docs_render_markdown('Test'));

        // @codingStandardsIgnoreStart
        $this->assert_content_with_maths_equals("<p><code>\\(x^2\\)</code> gives \\(x^2\\).</p>\n",
                stack_docs_render_markdown('<code>\(x^2\)</code> gives \(x^2\).'));
        $this->assert_content_with_maths_equals("<p><code>\\(x^2\\)</code> gives \\(x^2\\).</p>\n",
            stack_docs_render_markdown('`\(x^2\)` gives \(x^2\).'));
        // @codingStandardsIgnoreEnd

        $md = "Text with maths: \\(x^3\\)\n\n    And how to type it in: \\(x^3\\)\n\nShould work!";
        $ex = "<p>Text with maths: \\(x^3\\)</p>\n\n" .
            "<pre><code>And how to type it in: \\(x^3\\)\n</code></pre>\n\n" .
            "<p>Should work!</p>\n";
        $this->assert_content_with_maths_equals($ex,
            stack_docs_render_markdown($md));

        if (version_compare($currentversion, '4.1.0') >= 0) {
            $page = 'Watch <iframe width="560" height="315" src="https://www.youtube.com/embed/cpwo-D6EUgA" ' .
                'frameborder="0" allowfullscreen></iframe> This will help you.';
            $rendered = "<p>Watch</p>\n\n" .
                '<iframe width="560" height="315" src="https://www.youtube.com/embed/cpwo-D6EUgA" ' .
                'frameborder="0" allowfullscreen></iframe>' . "\n\n<p>This will help you.</p>\n";
            $this->assert_content_with_maths_equals($rendered,
                stack_docs_render_markdown($page));
        }
    }

    public function test_stack_docs_render_markdown_with_proof() {
        $md = '<div class="proof"><p>H1. Assume that \(3 \cdot 2^{172} + 1\) is a perfect square.</p></div>';
        $ex = $md . "\n";
        $this->assert_content_with_maths_equals($ex,
            stack_docs_render_markdown($md));
    }

    public function test_stack_docs_render_markdown_with_table() {
        global $CFG;
        require_once($CFG->libdir . '/environmentlib.php');
        $currentversion = normalize_version(get_config('', 'release'));

        $md = "\\[ {\\begin{array}{c|c} x & x^3\\\\ \\hline -1 & -1 \\\\ 0 & 0 \\\\ 1 & 1 \\end{array}} \\]";
        $ex = "<p>\\[ {\begin{array}{c|c} x &amp; x^3\\\\ \hline -1 &amp; -1 \\\\ " .
            "0 &amp; 0 \\\\ 1 &amp; 1 \\end{array}} \\]</p>\n";
        if (version_compare($currentversion, '4.1.0') >= 0) {
            $this->assert_content_with_maths_equals($ex,
                stack_docs_render_markdown($md));
        }
    }
}
