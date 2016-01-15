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

/**
 * Unit tests for the documentation library functions.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../doc/docslib.php');


/**
 * Unit tests for the documentation library functions.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_docslib_test extends advanced_testcase {

    public function test_stack_docs_title_from_filename() {
        $this->assertEquals('About', stack_docs_title_from_filename('About'));
        $this->assertEquals('Some folder', stack_docs_title_from_filename('Some_folder'));
        $this->assertEquals('Documentation', stack_docs_title_from_filename('Documentation.md'));
        $this->assertEquals('Future plans', stack_docs_title_from_filename('Future_plans.md'));
    }

    public function test_stack_docs_index() {
        global $CFG;

        $this->assertEquals(str_replace('WWWROOT', $CFG->wwwroot, '<ul class="dir">' .
                '<li><a href="http://www.example.com/moodle/question/type/stack/doc.php/Installation/Mathjax.md">' .
                        'Mathjax</a></li>' .
                '<li><a href="http://www.example.com/moodle/question/type/stack/doc.php/Installation/Maxima.md">Maxima</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Installation/Testing_installation.md">' .
                        'Testing installation</a></li></ul>'),
                stack_docs_index($CFG->dirroot . '/question/type/stack/doc/en/Installation',
                $CFG->wwwroot . '/question/type/stack/doc.php/Installation'));

        $this->assertEquals(str_replace('WWWROOT', $CFG->wwwroot, '<ul class="dir">' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/Accessibility.md">Accessibility</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/Answer_assessment.md">Answer assessment</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/Answer_input.md">Answer input</a></li>' .
                '<li><a href="WWWROOT/question/type/stack/doc.php/Students/FAQ.md">FAQ</a></li></ul>'),
                stack_docs_index($CFG->dirroot . '/question/type/stack/doc/en/Students',
                $CFG->wwwroot . '/question/type/stack/doc.php/Students'));
    }

    public function test_stack_docs_render_markdown() {
        $this->resetAfterTest();
        filter_set_global_state('mathjaxloader', TEXTFILTER_DISABLED);

        $this->assertEquals("<p>Test</p>\n",
                stack_docs_render_markdown('Test', '.../doc/content'));

        $this->assertEquals('<p><a href=".../doc/content/readme.txt">Readme</a></p>' . "\n",
                stack_docs_render_markdown('[Readme](%CONTENT/readme.txt)', '.../doc/content'));

        $this->assertEquals("<p>Literal %CONTENT</p>\n",
                stack_docs_render_markdown('Literal \%CONTENT', '.../doc/content'));

        $this->assertEquals("<p><code>\\(x^2\\)</code> gives \\(x^2\\).</p>\n",
                stack_docs_render_markdown('`\(x^2\)` gives \(x^2\).', '.../doc/content'));
    }
}
