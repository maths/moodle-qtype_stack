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
 * Unit tests for the MathJax maths output class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../stack/mathsoutput/mathsoutput.class.php');
require_once(__DIR__ . '/../doc/docslib.php');


/**
 * Unit tests for the MathJax maths output class.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 */
class stack_maths_mathjax_test extends advanced_testcase {

    public function test_maths_output_mathsjax() {
        filter_set_global_state('mathjaxloader', TEXTFILTER_DISABLED);

        // MathJax output is the default.
        $this->assertEquals('Your answer needs to be a single fraction of the form \( {a}\over{b} \). ',
                stack_string('ATSingleFrac_part'));

        $this->assertEquals("<p><code>\(x^2\)</code> gives \\(x^2\\).</p>\n",
                stack_docs_render_markdown('`\(x^2\)` gives \(x^2\).', ''));

        $this->assertEquals('What is \(x^2\)?', stack_maths::process_display_castext('What is \(x^2\)?'));

        $this->resetAfterTest();
        set_config('replacedollars', '1', 'qtype_stack');
        stack_utils::clear_config_cache();
        $this->assertEquals('What is \(x^2\) or \[x^2\]?', stack_maths::process_display_castext('What is $x^2$ or $$x^2$$?'));
        stack_utils::clear_config_cache();
    }
}
