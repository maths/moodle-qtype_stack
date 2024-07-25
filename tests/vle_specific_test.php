<?php
// This file is part of STACK - http://stack.maths.ed.ac.uk//
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

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../vle_specific.php');

/**
 * Test Moodle VLE specific functions.
 *
 * @copyright 2023 The University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * @group qtype_stack
 * @covers \vle_specific
 */
class vle_specific_test extends qtype_stack_testcase {

    public function test_mathjaxurl() {
        $this->resetAfterTest();

        // Parameters but no config.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?one=1&two=two', 'filter_mathjaxloader');
        $result = stack_get_mathjax_url();
        $this->assertEquals('https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=TeX-AMS-MML_HTMLorMML&one=1&two=two',
                            $result);

        // No setting at all.
        set_config('httpsurl', '', 'filter_mathjaxloader');
        $result = stack_get_mathjax_url();
        $this->assertEquals('https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=TeX-AMS-MML_HTMLorMML',
                            $result);

        // Config already set and other parameter.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=alreadyhere&one=1',
                    'filter_mathjaxloader');
        $result = stack_get_mathjax_url();
        $this->assertEquals('https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=alreadyhere&one=1', $result);

        // Config already set.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=alreadyhere',
                    'filter_mathjaxloader');
        $result = stack_get_mathjax_url();
        $this->assertEquals('https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=alreadyhere', $result);

        // Parameter with question mark.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=already?here',
                    'filter_mathjaxloader');
        $result = stack_get_mathjax_url();
        $this->assertEquals('https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=already%3Fhere', $result);

        // Nothing. NB Setting the config to the following at the start of the test will lead to cache problems
        // as it matches the default and so doesn't invalidate the cache.
        set_config('httpsurl', 'https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js', 'filter_mathjaxloader');
        $result = stack_get_mathjax_url();
        $this->assertEquals('https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=TeX-AMS-MML_HTMLorMML', $result);

    }
}


