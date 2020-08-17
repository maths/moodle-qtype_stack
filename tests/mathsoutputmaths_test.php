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
require_once(__DIR__ . '/../stack/mathsoutput/mathsoutputmaths.class.php');
require_once(__DIR__ . '/../doc/docslib.php');

// Unit tests for the OU maths filter output class.
//
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_maths_maths_test extends advanced_testcase {

    public function test_maths_rendering() {
        if (!stack_maths_output_maths::filter_is_installed()) {
            $this->markTestSkipped('The OU maths filter is not installed.');
        }

        if (!defined('FILTER_MATHS_TEST_SERVICE_URL_BASE')) {
            $this->markTestSkipped('To run the OU maths filter output tests, ' .
                    'you must define FILTER_MATHS_TEST_SERVICE_URL_BASE in config.php.');
        }

        $this->resetAfterTest();
        set_config('mathsdisplay', 'maths', 'qtype_stack');
        set_config('texservice',     FILTER_MATHS_TEST_SERVICE_URL_BASE . 'tex',      'filter_maths');
        set_config('imageservice',   FILTER_MATHS_TEST_SERVICE_URL_BASE . 'imagetex', 'filter_maths');
        set_config('englishservice', FILTER_MATHS_TEST_SERVICE_URL_BASE . 'english',  'filter_maths');
        stack_utils::clear_config_cache();
        filter_set_global_state('mathjaxloader', TEXTFILTER_DISABLED);

        // Test language string.
        $this->assertRegExp('~^Your answer needs to be a single fraction of the form <a .*alt="a over b".*</a>\. $~',
                stack_string('ATSingleFrac_part'));

        // Test docs - make sure maths inside <code> is not rendered.
        $this->assertRegExp('~^<p><code>\\\\\(x\^2\\\\\)</code> gives <a .*alt="x squared".*</a>\.</p>\n$~',
                stack_docs_render_markdown('<code>\(x^2\)</code> gives \(x^2\).'));

        // Test docs - make sure maths inside <textarea> is not rendered.
        $this->assertRegExp('~^<p><textarea readonly="readonly" rows="3" cols="50">\n' .
                        'Differentiate \\\\\[x\^2 \+ y\^2\\\\\] with respect to \\\\\(x\\\\\).</textarea></p>\n$~',
                stack_docs_render_markdown('<textarea readonly="readonly" rows="3" cols="50">' . "\n" .
                        'Differentiate \[x^2 + y^2\] with respect to \(x\).</textarea>'));

        // Test CAS text with inline maths.
        $this->assertEquals('What is &lt;tex mode="inline"&gt;x^2&lt;/tex&gt;?',
                stack_maths::process_display_castext('What is \(x^2\)?'));

        // Test CAS text with display maths.
        $this->assertEquals('What is <span class="displayequation">&lt;tex mode="display"&gt;x^2&lt;/tex&gt;</span>?',
                stack_maths::process_display_castext('What is \[x^2\]?'));

        // Test with replacedollars.
        set_config('replacedollars', '1', 'qtype_stack');
        stack_utils::clear_config_cache();
        $this->assertEquals('What is &lt;tex mode="inline"&gt;x^2&lt;/tex&gt; or ' .
                '<span class="displayequation">&lt;tex mode="display"&gt;x^2&lt;/tex&gt;</span>?',
                stack_maths::process_display_castext('What is $x^2$ or $$x^2$$?'));

        stack_utils::clear_config_cache();
    }
}
