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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use qtype_stack_testcase;
use stack_noun_simp_test_data;
use stack_ast_container;
use stack_ast_container_silent;
use stack_cas_security;
use stack_cas_session2;
use stack_numbers_test_data;
use stack_options;
use function stack_utils\get_config;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/fixtures/noun_simpfixtures.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

/**
 * This file is for testing the "noun" operators defined in noun_simp.mac
 * @group qtype_stack
 * @covers \stack_cas_session2
 */
final class cas_noun_simp_test extends qtype_stack_testcase {

    public function test_basic_1(): void {
        $cs = stack_ast_container::make_from_teacher_source('a nounadd (b nounmul c)', 'version-check');

        $session = new stack_cas_session2([$cs]);

        $session->get_valid();
        $session->instantiate();

        // Note, we don't need brackets here.
        $this->assertEquals($cs->get_value(),
            'a nounadd b nounmul c');
        $this->assertEquals($cs->get_display(),
            'a+b\cdot c');
    }

    /**
     * Run numerous, compact, tests of the noun_simp.mac CAS functions.
     * @codingStandardsIgnoreStart
     * Provider in another class/file throws false code check error.
     * @dataProvider stack_noun_simp_test_data::get_raw_test_data
     * @codingStandardsIgnoreEnd
     */
    public function test_noun_simp(): void {

        $test = stack_noun_simp_test_data::test_from_raw(func_get_args(), 'simp:true');
        $result = stack_noun_simp_test_data::run_test($test);
        $this->assertEquals($result->errors, '');
        $this->assertTrue($result->passed);

        $test = stack_noun_simp_test_data::test_from_raw(func_get_args(), 'simp:false');
        $result = stack_noun_simp_test_data::run_test($test);
        $this->assertEquals($result->errors, '');
        $this->assertTrue($result->passed);
    }
}
