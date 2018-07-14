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

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../lang/multilang.php');

// Unit tests for stack_multilang.
//
// @copyright  2018 The University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class stack_multilang_test extends qtype_stack_testcase {

    public function test_get_languages() {
        $enfi = '<span lang="en" class="multilang"><p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \] '
            . '</p><p>Compute the sum \(C = A + B\).</p>[[input:ans1]]<p></p><div>[[validation:ans1]]</div></span>'
            . '<span lang="fi" class="multilang"><p>Olkoot \[ A = {@mat1@} \quad \textrm{ja} \quad B = {@mat2@}. \]'
            . '</p><p>Laske summa \(C = A + B\).</p><p>Vastaus: [[input:ans1]]</p><div>[[validation:ans1]]</div></span>';

        $ml = new stack_multilang();
        $this->assertEquals(array('en', 'fi'), $ml->languages_used($enfi));
    }

    public function test_get_languages_none() {
        $enfi = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \] '
                . '</p><p>Compute the sum \(C = A + B\).</p>[[input:ans1]]<p></p><div>[[validation:ans1]]</div>'
                . '[[input:ans1]]</p><div>[[validation:ans1]]</div></span>';

        $ml = new stack_multilang();
        $this->assertEquals(array(), $ml->languages_used($enfi));
    }

    public function test_filter_langs() {
        $en = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \] </p>'
            . '<p>Compute the sum \(C = A + B\).</p>[[input:ans1]]<p></p><div>[[validation:ans1]]</div>';
        $fi = '<p>Olkoot \[ A = {@mat1@} \quad \textrm{ja} \quad B = {@mat2@}. \]'
            . '</p><p>Laske summa \(C = A + B\).</p><p>Vastaus: [[input:ans1]]</p><div>[[validation:ans1]]</div>';
        $enfi = '<span lang="en" class="multilang">' . $en . '</span>'
                . '<span lang="fi" class="multilang">' . $fi . '</span>';

        $ml = new stack_multilang();
        $this->assertEquals($en, $ml->filter($enfi, 'en'));
        $this->assertEquals($fi, $ml->filter($enfi, 'fi'));
    }

    public function test_filter_langs_embedded() {
        $en = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \]</p>'
               . '<p>Compute the sum \(C = A + B\).</p>';
        $fi = '<p>Olkoot \[ A = {@mat1@} \quad \textrm{ja} \quad B = {@mat2@}. \]'
            . '</p><p>Laske summa \(C = A + B\).</p><p>Vastaus: [[input:ans1]]</p><div>[[validation:ans1]]</div>';
        $enfi = '  <span lang="en" class="multilang">' . $en . '</span>'
                . '<span lang="fi" class="multilang">' . $fi . '</span>'
                . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';
        $texten = '  ' . $en . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';
        $textfi = '  ' . $fi . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';

        $ml = new stack_multilang();
        $this->assertEquals($texten, $ml->filter($enfi, 'en'));
        $this->assertEquals($textfi, $ml->filter($enfi, 'fi'));
    }

    public function test_filter_languages_none() {
        $enfi = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \] '
                . '</p><p>Compute the sum \(C = A + B\).</p>[[input:ans1]]<p></p><div>[[validation:ans1]]</div>'
                . '[[input:ans1]]</p><div>[[validation:ans1]]</div>';

        $ml = new stack_multilang();
        $this->assertEquals($enfi, $ml->filter($enfi, 'en'));
        $this->assertEquals($enfi, $ml->filter($enfi, 'fi'));
    }

    public function test_consolidate() {
        $en = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \]</p>'
               . '<p>Compute the sum \(C = A + B\).</p>';
        $fi = '<p>Olkoot \[ A = {@mat1@} \quad \textrm{ja} \quad B = {@mat2@}. \]'
            . '</p><p>Laske summa \(C = A + B\).</p><p>Vastaus: [[input:ans1]]</p><div>[[validation:ans1]]</div>';
        $enfi = '  <span lang="en" class="multilang">' . $en . '</span>'
                . '<span lang="fi" class="multilang">' . $fi . '</span>'
                . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';

        $text = '<span lang="en" class="multilang">  ' . $en . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div></span>'
                . "\n"
                . '<span lang="fi" class="multilang">  ' . $fi . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div></span>';

        $ml = new stack_multilang();
        $this->assertEquals($text, $ml->consolidate_languages($enfi));
    }

    public function test_consolidate_none() {
        $enfi = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \] '
                . '</p><p>Compute the sum \(C = A + B\).</p>[[input:ans1]]<p></p><div>[[validation:ans1]]</div>'
                . '[[input:ans1]]</p><div>[[validation:ans1]]</div>';

        $ml = new stack_multilang();
        $this->assertEquals($enfi, $ml->consolidate_languages($enfi));
    }
}

